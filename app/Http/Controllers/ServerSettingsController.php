<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\CyberpanelServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ServerSettingsController extends Controller
{
    public function changePhpVersion(Request $request, Server $server)
    {
        $request->validate([
            'php_version' => 'required|in:8.0,8.1,8.2,8.3'
        ]);

        // 현재 PHP 버전과 동일한지 확인
        if ($server->php_version === $request->php_version) {
            return response()->json([
                'success' => false,
                'message' => '이미 설치된 PHP 버전입니다.'
            ]);
        }

        try {
            // CyberPanel 서버 정보 가져오기
            $cyberpanelServer = CyberpanelServer::find($server->cyberpanel_server_id);
            if (!$cyberpanelServer) {
                return response()->json([
                    'success' => false,
                    'message' => 'CyberPanel 서버 정보를 찾을 수 없습니다.'
                ]);
            }

            // CyberPanel API 설정
            $cpHost = $cyberpanelServer->host;
            $cpPort = $cyberpanelServer->api_port;
            $cpUser = $cyberpanelServer->api_user;
            $cpPass = $cyberpanelServer->api_password;
            $cpToken = $cyberpanelServer->api_token;
            $cpSSL = $cyberpanelServer->ssl;
            $protocol = $cpSSL ? 'https' : 'http';
            $cpUrl = "$protocol://$cpHost:$cpPort/cloudAPI/";
            $cpAuth = $cpToken ?: base64_encode($cpUser . ':' . $cpPass);

            // PHP 버전 변경 API 호출
            $payload = [
                'serverUserName' => $cpUser,
                'controller' => 'changePHP',
                'childDomain' => $server->fqdn,
                'phpSelection' => 'PHP ' . $request->php_version
            ];

            Log::info('PHP 버전 변경 요청', $payload);

            $response = Http::timeout(20)
                ->withHeaders([
                    'Authorization' => $cpAuth,
                    'Content-Type' => 'application/json'
                ])
                ->withOptions(['verify' => false])
                ->post($cpUrl, $payload);

            $json = $response->json();
            Log::info('PHP 버전 변경 응답', $json);

            if ($json['status'] ?? 0) {
                // 성공 시 서버 테이블 업데이트
                $server->update(['php_version' => $request->php_version]);

                return response()->json([
                    'success' => true,
                    'message' => 'PHP 버전이 성공적으로 변경되었습니다.',
                    'new_version' => $request->php_version
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'PHP 버전 변경 실패: ' . ($json['error_message'] ?? '알 수 없는 오류')
                ]);
            }

        } catch (\Exception $e) {
            Log::error('PHP 버전 변경 중 오류', [
                'server_id' => $server->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'PHP 버전 변경 중 오류가 발생했습니다: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 사용자 검증 (MySQL 패스워드 변경 전)
     */
    public function verifyUserForMysqlPassword(Request $request, Server $server)
    {
        $request->validate([
            'dashboard_password' => 'required|string'
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '로그인이 필요합니다.'
                ]);
            }

            // 1. 대시보드 로그인 패스워드 검증
            if (!Hash::check($request->dashboard_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => '대시보드 로그인 패스워드가 올바르지 않습니다.'
                ]);
            }

            // 2. 사용자가 소유한 서버 중 현재 서버와 도메인이 일치하는지 확인
            $userOwnedServers = Server::where('user_id', $user->id)->get();
            $domainMatches = false;

            foreach ($userOwnedServers as $ownedServer) {
                if ($ownedServer->domain === $server->domain) {
                    $domainMatches = true;
                    break;
                }
            }

            if (!$domainMatches) {
                return response()->json([
                    'success' => false,
                    'message' => '소유한 서버 도메인이 일치하지 않습니다. 고객센터 문의 부탁드립니다.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => '사용자 검증이 완료되었습니다.',
                'user_email' => $user->email,
                'server_domain' => $server->domain,
                'initial_password_info' => '초기 MySQL 패스워드는 대시보드 로그인 시 사용한 패스워드와 동일합니다. 데이터베이스 접속 시 사용자명은 서버 도메인명을 사용하세요.'
            ]);

        } catch (\Exception $e) {
            Log::error('사용자 검증 예외', [
                'server_id' => $server->id,
                'user_id' => Auth::id(),
                'exception' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '사용자 검증 중 오류가 발생했습니다: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * MySQL 패스워드 변경 (검증 후)
     */
    public function changeMysqlPassword(Request $request, Server $server)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
            'dashboard_password' => 'required|string'
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => '로그인이 필요합니다.'
                ]);
            }

            // 1. 대시보드 로그인 패스워드 재검증
            if (!Hash::check($request->dashboard_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => '대시보드 로그인 패스워드가 올바르지 않습니다.'
                ]);
            }

            // 2. 사용자가 소유한 서버 중 현재 서버와 도메인이 일치하는지 재확인
            $userOwnedServers = Server::where('user_id', $user->id)->get();
            $domainMatches = false;

            foreach ($userOwnedServers as $ownedServer) {
                if ($ownedServer->domain === $server->domain) {
                    $domainMatches = true;
                    break;
                }
            }

            if (!$domainMatches) {
                return response()->json([
                    'success' => false,
                    'message' => '소유한 서버 도메인이 일치하지 않습니다. 고객센터 문의 부탁드립니다.'
                ]);
            }

            // 3. CyberPanel 서버 정보 가져오기
            $cyberpanelServer = CyberpanelServer::find($server->cyberpanel_server_id);
            if (!$cyberpanelServer) {
                return response()->json([
                    'success' => false,
                    'message' => 'CyberPanel 서버 정보를 찾을 수 없습니다.'
                ]);
            }

            // 4. SSH를 통해 MySQL 패스워드 변경
            $dbUsername = $server->domain;
            $newPassword = $request->new_password;
            
            // SSH 접속 정보 확인
            if (!$cyberpanelServer->ssh_user) {
                return response()->json([
                    'success' => false,
                    'message' => '접속 정보가 설정되지 않았습니다. 관리자에게 문의하세요.'
                ]);
            }
            
            // SSH 키 경로 설정 (웹 서버에서 접근 가능한 경로 사용)
            $sshKeyPath = $cyberpanelServer->ssh_key_path ?: '/var/www/hostylecms/storage/ssh_key';
            
            // SSH 키 파일 존재 및 권한 확인
            if (!file_exists($sshKeyPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'SSH 키 파일이 존재하지 않습니다: ' . $sshKeyPath
                ]);
            }
            
            if (!is_readable($sshKeyPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'SSH 키 파일에 읽기 권한이 없습니다: ' . $sshKeyPath
                ]);
            }
            
            // CyberPanel MySQL 패스워드 가져오기
            $mysqlPassword = $cyberpanelServer->mysql_password;
            
            // SSH 명령어로 MySQL 패스워드 변경 (SSH 키 인증)
            $sshCommand = "ssh -i {$sshKeyPath} -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR {$cyberpanelServer->ssh_user}@{$cyberpanelServer->host} \"mysql -u root -p'{$mysqlPassword}' -e \\\"ALTER USER '{$dbUsername}'@'localhost' IDENTIFIED BY '{$newPassword}'; FLUSH PRIVILEGES;\\\"\"";
            
            // SSH 패스워드가 설정되어 있다면 패스워드 인증 사용
            if ($cyberpanelServer->ssh_password) {
                $sshCommand = "sshpass -p '{$cyberpanelServer->ssh_password}' ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR {$cyberpanelServer->ssh_user}@{$cyberpanelServer->host} \"mysql -u root -p'{$mysqlPassword}' -e \\\"ALTER USER '{$dbUsername}'@'localhost' IDENTIFIED BY '{$newPassword}'; FLUSH PRIVILEGES;\\\"\"";
            }
            
            Log::info('SSH MySQL 패스워드 변경 요청', [
                'server_id' => $server->id,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'domain' => $server->domain,
                'db_username' => $dbUsername,
                'ssh_host' => $cyberpanelServer->host,
                'ssh_user' => $cyberpanelServer->ssh_user
            ]);

            // SSH 명령어 실행
            $output = [];
            $returnCode = 0;
            exec($sshCommand . ' 2>&1', $output, $returnCode);

            Log::info('SSH MySQL 패스워드 변경 결과', [
                'output' => $output,
                'return_code' => $returnCode
            ]);

            if ($returnCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'MySQL 패스워드가 성공적으로 변경되었습니다.'
                ]);
            } else {
                $errorMessage = implode("\n", $output);
                return response()->json([
                    'success' => false,
                    'message' => 'MySQL 패스워드 변경에 실패했습니다: ' . $errorMessage
                ]);
            }
        } catch (\Exception $e) {
            Log::error('MySQL 패스워드 변경 예외', [
                'server_id' => $server->id,
                'user_id' => Auth::id(),
                'exception' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'MySQL 패스워드 변경 중 오류가 발생했습니다: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * 초기 MySQL 패스워드 정보 조회
     */
    public function getInitialMysqlPassword(Request $request, Server $server)
    {
        try {
            // 현재 로그인한 사용자의 패스워드 정보 반환
            $user = Auth::user();
            
            return response()->json([
                'success' => true,
                'message' => '초기 MySQL 패스워드는 대시보드 로그인 시 사용한 패스워드와 동일합니다. 데이터베이스 접속 시 사용자명은 서버 도메인명을 사용하세요.',
                'initial_password_info' => '초기 MySQL 패스워드는 대시보드 로그인 시 사용한 패스워드와 동일합니다. 데이터베이스 접속 시 사용자명은 서버 도메인명을 사용하세요.'
            ]);

        } catch (\Exception $e) {
            Log::error('초기 패스워드 정보 조회 예외', [
                'server_id' => $server->id,
                'exception' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => '초기 패스워드 정보 조회 중 오류가 발생했습니다: ' . $e->getMessage()
            ]);
        }
    }
}
