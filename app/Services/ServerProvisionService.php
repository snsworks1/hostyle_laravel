<?php

namespace App\Services;

use App\Models\CyberpanelServer;
use App\Models\Server;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

class ServerProvisionService
{
    public function provision(array $input, $user)
    {
        $servers = CyberpanelServer::where('region', $input['region'])
            ->where('status', 'active')->get();
        $selected = null;
        foreach ($servers as $server) {
            if ($this->isAvailable($server)) {
                $selected = $server;
                break;
            }
        }
        if (!$selected) {
            return ['success' => false, 'reason' => '가용 서버 없음'];
        }
        $cyberResult = $this->provisionOnCyberpanel($selected, $input, $user);
        if (!$cyberResult['success']) {
            Log::error('CyberPanel 생성 실패', $cyberResult);
            return ['success' => false, 'reason' => $cyberResult['reason']];
        }
        $fqdn = $input['domain'] . '.hostylefree.xyz';
        $expiresAt = Carbon::now()->addMonths($input['months']);
        $serverModel = Server::create([
            'user_id' => $user->id,
            'site_name' => $input['site_name'],
            'domain' => $input['domain'],
            'fqdn' => $fqdn,
            'region' => $input['region'],
            'platform' => $input['platform'],
            
            'php_version' => '8.0',
            'plan' => $input['plan'],
            'months' => $input['months'],
            'expires_at' => $expiresAt,
            'cyberpanel_server_id' => $selected->id,
            'status' => 'active',
        ]);
        $cfResult = $this->createCloudflareDNS($fqdn, $selected->ip_address);
        if (!$cfResult['success']) {
            Log::error('Cloudflare DNS 생성 실패', $cfResult);
        }

        // 플랜별 PHP 설정 자동 적용
        $this->applyPlanPhpSettings($serverModel, $selected);

        // FTP 계정 생성
        $ftpResult = $this->createFTPAccount($selected, $serverModel, $input['user_password']);
        if (!$ftpResult['success']) {
            Log::error('FTP 계정 생성 실패', $ftpResult);
        }



        return ['success' => true, 'server_id' => $serverModel->id];
    }

    public function isAvailable($server)
    {
        try {
            $ssh = new SSH2($server->ip_address, 22);
            $key = PublicKeyLoader::load(file_get_contents('/var/www/hostylecms/storage/ssh_key'));
            if (!$ssh->login($server->ssh_user ?? 'root', $key)) {
                Log::warning('SSH 로그인 실패', ['server_id' => $server->id]);
                return false;
            }
            $disk = $ssh->exec('df -h /');
            $memory = $ssh->exec('free -m');
            $diskLines = explode("\n", trim($disk));
            $diskData = preg_split('/\s+/', $diskLines[1] ?? '');
            $diskAvail = $diskData[3] ?? '0G';
            $diskUsePercent = $diskData[4] ?? '100%';
            $diskAvailGB = (int)filter_var($diskAvail, FILTER_SANITIZE_NUMBER_INT);
            $diskUsePercentInt = (int)str_replace('%', '', $diskUsePercent);
            $memLines = explode("\n", trim($memory));
            $memData = preg_split('/\s+/', $memLines[1] ?? '');
            $memAvailable = (int)($memData[6] ?? 0);
            return ($diskAvailGB >= 20 && $diskUsePercentInt <= 80 && $memAvailable >= 2048);
        } catch (\Exception $e) {
            Log::warning('가용성 체크 중 예외', ['server_id' => $server->id, 'msg' => $e->getMessage()]);
            return false;
        }
    }

    public function provisionOnCyberpanel($server, $input, $user)
    {
        try {
            $cpHost = $server->host;
            $cpPort = $server->api_port;
            $cpUser = $server->api_user;
            $cpPass = $server->api_password;
            $cpToken = $server->api_token;
            $cpSSL = $server->ssl;
            $protocol = $cpSSL ? 'https' : 'http';
            $cpUrl = "$protocol://$cpHost:$cpPort/cloudAPI/";
            $cpAuth = $cpToken ?: base64_encode($cpUser . ':' . $cpPass);
            $userName = $input['domain'];
            $dbName = $input['domain'];
            $dbUsername = $input['domain'];
            $dbPassword = $input['user_password'];
            // 1. 사용자 생성
            $userPayload = [
                'serverUserName' => $cpUser,
                'controller' => 'submitUserCreation',
                'firstName' => $user->name,
                'lastName' => $user->name,
                'email' => $user->email,
                'userName' => $userName,
                'password' => $dbPassword,
                'websitesLimit' => 1,
                'selectedACL' => 'user'
            ];
            $res1 = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => $cpAuth,
                    'Content-Type' => 'application/json'
                ])
                ->withOptions(['verify' => false])
                ->post($cpUrl, $userPayload);
            $json1 = $res1->json();
            if (!($json1['status'] ?? 0)) {
                return ['success' => false, 'reason' => 'CyberPanel 사용자 생성 실패: ' . ($json1['error_message'] ?? '')];
            }
            // 2. 웹사이트 생성
            $sitePayload = [
                'serverUserName' => $cpUser,
                'controller' => 'submitWebsiteCreation',
                'domainName' => $input['domain'] . '.hostylefree.xyz',
                'package' => $input['plan'],
                'adminEmail' => $user->email,
                'phpSelection' => 'PHP 8.0',
                'websiteOwner' => $userName,
                'ssl' => 1,
                'dkimCheck' => 1,
                'openBasedir' => 1
            ];
            Log::info('CyberPanel 웹사이트 생성 요청', $sitePayload);
            $res2 = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => $cpAuth,
                    'Content-Type' => 'application/json'
                ])
                ->withOptions(['verify' => false])
                ->post($cpUrl, $sitePayload);
            $json2 = $res2->json();
            Log::info('CyberPanel 웹사이트 생성 응답', $json2);
            if (!($json2['status'] ?? 0)) {
                return ['success' => false, 'reason' => 'CyberPanel 웹사이트 생성 실패: ' . ($json2['error_message'] ?? '')];
            }
            sleep(3); // 또는 sleep(5);

            // 3. DB 생성
            $dbPayload = [
                'serverUserName' => $cpUser,
                'controller' => 'submitDBCreation',
                'databaseWebsite' => $input['domain'] . '.hostylefree.xyz',
                'dbName' => $dbName,
                'dbUsername' => $dbUsername,
                'dbPassword' => $dbPassword,
                'webUserName' => $userName
            ];
            Log::info('CyberPanel DB 생성 요청', $dbPayload);
            $maxTries = 20; // 최대 20초까지 시도
            $success = false;
            for ($i = 0; $i < $maxTries; $i++) {
                $res3 = Http::timeout(20)
                    ->withHeaders([
                        'Authorization' => $cpAuth,
                        'Content-Type' => 'application/json'
                    ])
                    ->withOptions(['verify' => false])
                    ->post($cpUrl, $dbPayload);
                $json3 = $res3->json();
                Log::info('CyberPanel DB 생성 재시도', ['try' => $i+1, 'response' => $json3]);
                if ($json3['status'] ?? 0) {
                    $success = true;
                    break;
                }
                sleep(1);
            }
            if (!$success) {
                return ['success' => false, 'reason' => 'CyberPanel DB 생성 실패: Websites matching query does not exist.'];
            }
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'reason' => 'CyberPanel API 예외: ' . $e->getMessage()];
        }
    }

    public function createCloudflareDNS($fqdn, $ip)
    {
        try {
            $cfToken = env('CLOUDFLARE_API_TOKEN');
            $cfZone = env('CLOUDFLARE_ZONE_ID');
            $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . $cfToken,
                'Content-Type' => 'application/json'
            ])->post("https://api.cloudflare.com/client/v4/zones/$cfZone/dns_records", [
                'type' => 'A',
                'name' => $fqdn,
                'content' => $ip,
                'ttl' => 3600,
                'proxied' => true
            ]);
            $cfJson = $res->json();
            if (!($cfJson['success'] ?? false)) {
                return ['success' => false, 'reason' => 'Cloudflare DNS 생성 실패: ' . json_encode($cfJson['errors'] ?? [])];
            }
            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'reason' => 'Cloudflare API 예외: ' . $e->getMessage()];
        }
    }

    /**
     * 플랜별 PHP 설정 자동 적용
     */
    private function applyPlanPhpSettings($server, $cyberpanelServer)
    {
        try {
            // 플랜별 권장 설정 가져오기
            $plan = \DB::table('plans')->where('name', $server->plan)->first();
            if (!$plan) {
                Log::warning('플랜 정보를 찾을 수 없습니다', ['plan' => $server->plan]);
                return;
            }

            $phpSettings = [
                'memory_limit' => $plan->memory_limit ?? '128M',
                'upload_max_filesize' => $plan->upload_max_filesize ?? '16M',
                'post_max_size' => $plan->post_max_size ?? '20M',
                'max_execution_time' => $plan->max_execution_time ?? 30,
                'max_input_time' => $plan->max_input_time ?? 60,
            ];

            // vhost.conf 파일 읽기
            $vhostConfig = $this->readVhostConfig($cyberpanelServer, $server->fqdn);
            
            // PHP 설정 업데이트
            $updatedConfig = $this->updatePhpSettingsInConfig($vhostConfig, $phpSettings);
            
            // 업데이트된 설정 저장
            $this->writeVhostConfig($cyberpanelServer, $server->fqdn, $updatedConfig);
            
            // 서버 reload
            $this->reloadServer($cyberpanelServer);

            Log::info('플랜별 PHP 설정 자동 적용 완료', [
                'server_id' => $server->id,
                'plan' => $server->plan,
                'settings' => $phpSettings
            ]);

        } catch (\Exception $e) {
            Log::error('플랜별 PHP 설정 자동 적용 실패', [
                'server_id' => $server->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * vhost.conf 파일 읽기
     */
    private function readVhostConfig($cyberpanelServer, $domain)
    {
        // 먼저 vhosts 디렉토리의 내용을 확인하여 정확한 도메인 폴더를 찾기
        $listCommand = "ssh -i /var/www/hostylecms/storage/ssh_key -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR {$cyberpanelServer->ssh_user}@{$cyberpanelServer->host} \"ls -la /usr/local/lsws/conf/vhosts/\"";
        
        $listOutput = [];
        $listReturnCode = 0;
        exec($listCommand . ' 2>&1', $listOutput, $listReturnCode);

        if ($listReturnCode !== 0) {
            throw new \Exception('vhosts 디렉토리를 읽을 수 없습니다: ' . implode("\n", $listOutput));
        }

        // 도메인 폴더 찾기 (부분 일치도 허용)
        $vhostFolders = [];
        foreach ($listOutput as $line) {
            if (preg_match('/^d.*\s+(\S+)$/', $line, $matches)) {
                $folderName = $matches[1];
                if ($folderName !== '.' && $folderName !== '..' && $folderName !== 'Example') {
                    $vhostFolders[] = $folderName;
                }
            }
        }

        // 정확한 도메인 폴더 찾기
        $targetFolder = null;
        
        // 정확한 일치를 우선 확인
        foreach ($vhostFolders as $folder) {
            if ($folder === $domain) {
                $targetFolder = $folder;
                break;
            }
        }
        
        // 정확한 일치가 없으면 부분 일치 확인 (더 엄격한 매칭)
        if (!$targetFolder) {
            foreach ($vhostFolders as $folder) {
                // 도메인이 폴더명의 시작 부분과 일치하는지 확인
                if (strpos($folder, $domain) === 0 || $folder === $domain) {
                    $targetFolder = $folder;
                    break;
                }
            }
        }

        if (!$targetFolder) {
            throw new \Exception("도메인 '{$domain}'에 해당하는 vhost 폴더를 찾을 수 없습니다. 사용 가능한 폴더: " . implode(', ', $vhostFolders));
        }

        // vhost.conf 파일 읽기
        $sshCommand = "ssh -i /var/www/hostylecms/storage/ssh_key -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR {$cyberpanelServer->ssh_user}@{$cyberpanelServer->host} \"cat /usr/local/lsws/conf/vhosts/{$targetFolder}/vhost.conf\"";
        
        $output = [];
        $returnCode = 0;
        exec($sshCommand . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('vhost.conf 파일을 읽을 수 없습니다: ' . implode("\n", $output));
        }

        return implode("\n", $output);
    }

    /**
     * vhost.conf 파일에 PHP 설정 업데이트
     */
    private function updatePhpSettingsInConfig($config, $phpSettings)
    {
        // phpIniOverride 블록 찾기 또는 생성
        $phpIniOverrideBlock = $this->generatePhpIniOverrideBlock($phpSettings);
        
        // 기존 phpIniOverride 블록이 있는지 확인
        if (preg_match('/phpIniOverride\s*{.*?}/s', $config)) {
            // 기존 블록 교체
            $config = preg_replace('/phpIniOverride\s*{.*?}/s', $phpIniOverrideBlock, $config);
        } else {
            // 새로운 블록 추가 (context 블록 안에)
            if (preg_match('/context\s*{/s', $config)) {
                $config = preg_replace('/context\s*{/s', "context {\n  " . $phpIniOverrideBlock, $config);
            } else {
                // context 블록이 없으면 추가
                $config .= "\ncontext {\n  " . $phpIniOverrideBlock . "\n}";
            }
        }

        return $config;
    }

    /**
     * PHP 설정 블록 생성
     */
    private function generatePhpIniOverrideBlock($phpSettings)
    {
        return "phpIniOverride {\n" .
               "  memory_limit {$phpSettings['memory_limit']}\n" .
               "  upload_max_filesize {$phpSettings['upload_max_filesize']}\n" .
               "  post_max_size {$phpSettings['post_max_size']}\n" .
               "  max_execution_time {$phpSettings['max_execution_time']}\n" .
               "  max_input_time {$phpSettings['max_input_time']}\n" .
               "}";
    }

    /**
     * vhost.conf 파일에 설정 저장
     */
    private function writeVhostConfig($cyberpanelServer, $domain, $config)
    {
        // 먼저 vhosts 디렉토리의 내용을 확인하여 정확한 도메인 폴더를 찾기
        $listCommand = "ssh -i /var/www/hostylecms/storage/ssh_key -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR {$cyberpanelServer->ssh_user}@{$cyberpanelServer->host} \"ls -la /usr/local/lsws/conf/vhosts/\"";
        
        $listOutput = [];
        $listReturnCode = 0;
        exec($listCommand . ' 2>&1', $listOutput, $listReturnCode);

        if ($listReturnCode !== 0) {
            throw new \Exception('vhosts 디렉토리를 읽을 수 없습니다: ' . implode("\n", $listOutput));
        }

        // 도메인 폴더 찾기 (부분 일치도 허용)
        $vhostFolders = [];
        foreach ($listOutput as $line) {
            if (preg_match('/^d.*\s+(\S+)$/', $line, $matches)) {
                $folderName = $matches[1];
                if ($folderName !== '.' && $folderName !== '..' && $folderName !== 'Example') {
                    $vhostFolders[] = $folderName;
                }
            }
        }

        // 정확한 도메인 폴더 찾기
        $targetFolder = null;
        
        // 정확한 일치를 우선 확인
        foreach ($vhostFolders as $folder) {
            if ($folder === $domain) {
                $targetFolder = $folder;
                break;
            }
        }
        
        // 정확한 일치가 없으면 부분 일치 확인 (더 엄격한 매칭)
        if (!$targetFolder) {
            foreach ($vhostFolders as $folder) {
                // 도메인이 폴더명의 시작 부분과 일치하는지 확인
                if (strpos($folder, $domain) === 0 || $folder === $domain) {
                    $targetFolder = $folder;
                    break;
                }
            }
        }

        if (!$targetFolder) {
            throw new \Exception("도메인 '{$domain}'에 해당하는 vhost 폴더를 찾을 수 없습니다. 사용 가능한 폴더: " . implode(', ', $vhostFolders));
        }

        // 임시 파일에 설정 저장
        $tempFile = "/tmp/vhost_{$domain}.conf";
        file_put_contents($tempFile, $config);

        // SSH를 통해 파일 업로드
        $scpCommand = "scp -i /var/www/hostylecms/storage/ssh_key -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR {$tempFile} {$cyberpanelServer->ssh_user}@{$cyberpanelServer->host}:/usr/local/lsws/conf/vhosts/{$targetFolder}/vhost.conf";
        
        $output = [];
        $returnCode = 0;
        exec($scpCommand . ' 2>&1', $output, $returnCode);

        // 임시 파일 삭제
        unlink($tempFile);

        if ($returnCode !== 0) {
            throw new \Exception('vhost.conf 파일을 업로드할 수 없습니다: ' . implode("\n", $output));
        }
    }

    /**
     * CyberPanel에 FTP 계정 생성
     */
    private function createFTPAccount($cyberpanelServer, $server, $password)
    {
        try {
            $cpHost = $cyberpanelServer->host;
            $cpPort = $cyberpanelServer->api_port;
            $cpUser = $cyberpanelServer->api_user;
            $cpPass = $cyberpanelServer->api_password;
            $cpToken = $cyberpanelServer->api_token;
            $cpSSL = $cyberpanelServer->ssl;
            $protocol = $cpSSL ? 'https' : 'http';
            $cpUrl = "$protocol://$cpHost:$cpPort/cloudAPI/";
            $cpAuth = $cpToken ?: base64_encode($cpUser . ':' . $cpPass);

            $ftpPayload = [
                'serverUserName' => $cpUser,
                'controller' => 'submitFTPCreation',
                'ftpDomain' => $server->fqdn,
                'ftpUserName' => $server->domain,
                'passwordByPass' => $password,
                'path' => ''
            ];

            Log::info('CyberPanel FTP 계정 생성 요청', $ftpPayload);

            $response = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => $cpAuth,
                    'Content-Type' => 'application/json'
                ])
                ->withOptions(['verify' => false])
                ->post($cpUrl, $ftpPayload);

            $json = $response->json();
            Log::info('CyberPanel FTP 계정 생성 응답', $json);

            if (!($json['status'] ?? 0)) {
                return ['success' => false, 'reason' => 'CyberPanel FTP 계정 생성 실패: ' . ($json['error_message'] ?? '')];
            }

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'reason' => 'CyberPanel FTP API 예외: ' . $e->getMessage()];
        }
    }



    /**
     * 서버 reload
     */
    private function reloadServer($cyberpanelServer)
    {
        $sshCommand = "ssh -i /var/www/hostylecms/storage/ssh_key -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR {$cyberpanelServer->ssh_user}@{$cyberpanelServer->host} \"/usr/local/lsws/bin/lswsctrl reload\"";
        
        $output = [];
        $returnCode = 0;
        exec($sshCommand . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            Log::warning('서버 reload 실패', [
                'output' => $output,
                'return_code' => $returnCode
            ]);
        }
    }
} 