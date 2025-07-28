<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Server;
use App\Models\CyberpanelServer;

class PhpSettingsController extends Controller
{
    /**
     * PHP 설정 정보 조회
     */
    public function getPhpSettings($serverId)
    {
        try {
            $server = Server::where('id', $serverId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $cyberpanelServer = CyberpanelServer::find($server->cyberpanel_server_id);
            if (!$cyberpanelServer) {
                return response()->json([
                    'success' => false,
                    'message' => 'CyberPanel 서버 정보를 찾을 수 없습니다.'
                ]);
            }

            // SSH를 통해 vhost.conf 파일 읽기
            Log::info('서버 도메인 정보', [
                'domain' => $server->domain,
                'fqdn' => $server->fqdn ?? 'N/A'
            ]);
            $vhostConfig = $this->readVhostConfig($cyberpanelServer, $server->domain);
            
            // PHP 설정 파싱
            $phpSettings = $this->parsePhpSettings($vhostConfig);
            
            // 플랜별 권장값 가져오기
            $plan = DB::table('plans')->where('name', $server->plan)->first();
            $recommendedSettings = [
                'memory_limit' => $plan->memory_limit ?? '128M',
                'upload_max_filesize' => $plan->upload_max_filesize ?? '16M',
                'post_max_size' => $plan->post_max_size ?? '20M',
                'max_execution_time' => $plan->max_execution_time ?? 30,
                'max_input_time' => $plan->max_input_time ?? 60,
            ];

            // 기본값인 경우 권장값으로 자동 설정
            $isDefaultSettings = $this->isDefaultSettings($phpSettings);
            if ($isDefaultSettings) {
                $phpSettings = $recommendedSettings;
            }

            return response()->json([
                'success' => true,
                'current_settings' => $phpSettings,
                'recommended_settings' => $recommendedSettings,
                'plan' => $server->plan,
                'is_default_settings' => $isDefaultSettings
            ]);

        } catch (\Exception $e) {
            Log::error('PHP 설정 조회 실패', [
                'server_id' => $serverId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'PHP 설정 조회 중 오류가 발생했습니다: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * PHP 설정 업데이트
     */
    public function updatePhpSettings(Request $request, $serverId)
    {
        try {
            // 디버깅을 위한 로그 추가
            Log::info('PHP 설정 업데이트 요청', [
                'server_id' => $serverId,
                'request_data' => $request->all(),
                'content_type' => $request->header('Content-Type')
            ]);

            $request->validate([
                'memory_limit' => 'required|string',
                'upload_max_filesize' => 'required|string',
                'post_max_size' => 'required|string',
                'max_execution_time' => 'required|integer|min:1',
                'max_input_time' => 'required|integer|min:1',
            ]);

            $server = Server::where('id', $serverId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            // 플랜 기준값 체크
            $plan = \DB::table('plans')->where('name', $server->plan)->first();
            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => '플랜 정보를 찾을 수 없습니다.'
                ]);
            }

            // 플랜 기준값 초과 체크
            $exceededSettings = [];
            
            // Memory Limit 체크
            $requestMemory = $this->parseMemoryValue($request->memory_limit);
            $planMemory = $this->parseMemoryValue($plan->memory_limit);
            if ($requestMemory > $planMemory) {
                $exceededSettings[] = 'Memory Limit';
            }
            
            // Upload Max Filesize 체크
            $requestUpload = $this->parseMemoryValue($request->upload_max_filesize);
            $planUpload = $this->parseMemoryValue($plan->upload_max_filesize);
            if ($requestUpload > $planUpload) {
                $exceededSettings[] = 'Upload Max Filesize';
            }
            
            // Post Max Size 체크
            $requestPost = $this->parseMemoryValue($request->post_max_size);
            $planPost = $this->parseMemoryValue($plan->post_max_size);
            if ($requestPost > $planPost) {
                $exceededSettings[] = 'Post Max Size';
            }
            
            // Max Execution Time 체크
            if ($request->max_execution_time > $plan->max_execution_time) {
                $exceededSettings[] = 'Max Execution Time';
            }
            
            // Max Input Time 체크
            if ($request->max_input_time > $plan->max_input_time) {
                $exceededSettings[] = 'Max Input Time';
            }
            
            // 플랜 기준값 초과 시 차단
            if (!empty($exceededSettings)) {
                return response()->json([
                    'success' => false,
                    'message' => '플랜 기준값을 초과하는 설정이 있습니다: ' . implode(', ', $exceededSettings) . '. 플랜을 업그레이드하세요.'
                ]);
            }

            $cyberpanelServer = CyberpanelServer::find($server->cyberpanel_server_id);
            if (!$cyberpanelServer) {
                return response()->json([
                    'success' => false,
                    'message' => 'CyberPanel 서버 정보를 찾을 수 없습니다.'
                ]);
            }

            // 현재 vhost.conf 읽기
            $vhostConfig = $this->readVhostConfig($cyberpanelServer, $server->domain);
            Log::info('현재 vhost.conf 내용', ['config_length' => strlen($vhostConfig)]);
            
            // PHP 설정 업데이트
            $updatedConfig = $this->updatePhpSettingsInConfig($vhostConfig, $request->all());
            Log::info('업데이트된 설정', ['updated_config_length' => strlen($updatedConfig)]);
            
            // 업데이트된 설정 저장
            $this->writeVhostConfig($cyberpanelServer, $server->domain, $updatedConfig);
            Log::info('vhost.conf 파일 저장 완료');
            
            // 서버 reload
            $this->reloadServer($cyberpanelServer);
            Log::info('서버 reload 완료');

            return response()->json([
                'success' => true,
                'message' => 'PHP 설정이 성공적으로 업데이트되었습니다.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('PHP 설정 validation 실패', [
                'server_id' => $serverId,
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => '입력값이 올바르지 않습니다: ' . implode(', ', array_map(function($field, $errors) {
                    return $field . ': ' . implode(', ', $errors);
                }, array_keys($e->errors()), $e->errors()))
            ]);
        } catch (\Exception $e) {
            Log::error('PHP 설정 업데이트 실패', [
                'server_id' => $serverId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'PHP 설정 업데이트 중 오류가 발생했습니다: ' . $e->getMessage()
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
        Log::info('임시 파일 생성', ['temp_file' => $tempFile, 'file_size' => filesize($tempFile)]);

        // SSH를 통해 파일 업로드
        $scpCommand = "scp -i /var/www/hostylecms/storage/ssh_key -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR {$tempFile} {$cyberpanelServer->ssh_user}@{$cyberpanelServer->host}:/usr/local/lsws/conf/vhosts/{$targetFolder}/vhost.conf";
        Log::info('SCP 명령어', ['command' => $scpCommand]);
        
        $output = [];
        $returnCode = 0;
        exec($scpCommand . ' 2>&1', $output, $returnCode);

        Log::info('SCP 실행 결과', ['return_code' => $returnCode, 'output' => $output]);

        // 임시 파일 삭제
        unlink($tempFile);

        if ($returnCode !== 0) {
            throw new \Exception('vhost.conf 파일을 업로드할 수 없습니다: ' . implode("\n", $output));
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

    /**
     * PHP 설정 파싱
     */
    private function parsePhpSettings($config)
    {
        $settings = [
            'memory_limit' => '128M',
            'upload_max_filesize' => '16M',
            'post_max_size' => '20M',
            'max_execution_time' => 30,
            'max_input_time' => 60,
        ];

        // phpIniOverride 블록에서 설정 추출
        if (preg_match('/phpIniOverride\s*{(.*?)}/s', $config, $matches)) {
            $phpIniOverride = $matches[1];
            
            // 각 설정 파싱
            if (preg_match('/memory_limit\s+(\S+)/', $phpIniOverride, $matches)) {
                $settings['memory_limit'] = $matches[1];
            }
            if (preg_match('/upload_max_filesize\s+(\S+)/', $phpIniOverride, $matches)) {
                $settings['upload_max_filesize'] = $matches[1];
            }
            if (preg_match('/post_max_size\s+(\S+)/', $phpIniOverride, $matches)) {
                $settings['post_max_size'] = $matches[1];
            }
            if (preg_match('/max_execution_time\s+(\d+)/', $phpIniOverride, $matches)) {
                $settings['max_execution_time'] = (int)$matches[1];
            }
            if (preg_match('/max_input_time\s+(\d+)/', $phpIniOverride, $matches)) {
                $settings['max_input_time'] = (int)$matches[1];
            }
        }

        return $settings;
    }

    /**
     * PHP 설정이 기본값인지 확인
     */
    private function isDefaultSettings($settings)
    {
        $defaults = [
            'memory_limit' => '128M',
            'upload_max_filesize' => '16M',
            'post_max_size' => '20M',
            'max_execution_time' => 30,
            'max_input_time' => 60,
        ];

        foreach ($defaults as $key => $defaultValue) {
            if ($settings[$key] !== $defaultValue) {
                return false;
            }
        }

        return true;
    }

    /**
     * 메모리 값 파싱 (예: "256M" -> 256, "1G" -> 1024)
     */
    private function parseMemoryValue($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }
        
        if (is_string($value)) {
            $value = strtoupper(trim($value));
            if (preg_match('/^(\d+)([MG])$/', $value, $matches)) {
                $number = (int) $matches[1];
                $unit = $matches[2];
                
                if ($unit === 'G') {
                    return $number * 1024; // 1G = 1024M
                } else {
                    return $number; // M 단위
                }
            }
        }
        
        return 0;
    }
} 