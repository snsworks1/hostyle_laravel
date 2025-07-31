<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Server;
use App\Models\CyberpanelServer;
use phpseclib3\Net\SSH2;

class DatabaseController extends Controller
{
    /**
     * 데이터베이스 목록 조회
     */
    public function fetchDatabases(Request $request, $serverId)
    {
        $server = Server::where('id', $serverId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$server) {
            return response()->json(['error' => '서버를 찾을 수 없습니다.'], 404);
        }

        $cyber = CyberpanelServer::find($server->cyberpanel_server_id);
        if (!$cyber) {
            return response()->json(['error' => 'CyberPanel 서버 정보를 찾을 수 없습니다.'], 404);
        }

        $cpUrl = ($cyber->ssl ? 'https' : 'http') . "://{$cyber->host}:{$cyber->api_port}/cloudAPI/";
        $cpAuth = $cyber->api_token ?: base64_encode($cyber->api_user . ':' . $cyber->api_password);
        
        $payload = [
            'serverUserName' => $cyber->api_user,
            'controller' => 'fetchDatabases',
            'databaseWebsite' => $server->fqdn
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => $cpAuth,
                'Content-Type' => 'application/json'
            ])->withOptions(['verify' => false])->post($cpUrl, $payload);

            if ($response->ok()) {
                $data = $response->json();
                
                // 디버깅을 위한 로그 추가
                \Log::info('CyberPanel API Response', [
                    'payload' => $payload,
                    'response' => $data
                ]);
                
                // API 응답 구조 확인 및 처리
                $databases = [];
                if (isset($data['status']) && $data['status']) {
                    // 성공적인 응답인 경우
                    if (isset($data['data'])) {
                        // data가 JSON 문자열인 경우 파싱
                        if (is_string($data['data'])) {
                            $parsedData = json_decode($data['data'], true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($parsedData)) {
                                $databases = $parsedData;
                            }
                        } elseif (is_array($data['data'])) {
                            $databases = $data['data'];
                        }
                    } elseif (isset($data['databases']) && is_array($data['databases'])) {
                        $databases = $data['databases'];
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'databases' => $databases,
                    'raw_response' => $data // 디버깅용
                ]);
            } else {
                \Log::error('CyberPanel API 호출 실패', [
                    'payload' => $payload,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                return response()->json([
                    'success' => false,
                    'error' => 'CyberPanel API 호출 실패',
                    'status' => $response->status(),
                    'body' => $response->body()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'API 호출 중 오류가 발생했습니다.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 데이터베이스 생성
     */
    public function createDatabase(Request $request, $serverId)
    {
        $request->validate([
            'dbName' => 'required|string|max:64',
            'dbUsername' => 'required|string|max:64',
            'dbPassword' => 'required|string|min:8'
        ]);

        $server = Server::where('id', $serverId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$server) {
            return response()->json(['error' => '서버를 찾을 수 없습니다.'], 404);
        }

        $cyber = CyberpanelServer::find($server->cyberpanel_server_id);
        if (!$cyber) {
            return response()->json(['error' => 'CyberPanel 서버 정보를 찾을 수 없습니다.'], 404);
        }

        $cpUrl = ($cyber->ssl ? 'https' : 'http') . "://{$cyber->host}:{$cyber->api_port}/cloudAPI/";
        $cpAuth = $cyber->api_token ?: base64_encode($cyber->api_user . ':' . $cyber->api_password);
        
        $payload = [
            'serverUserName' => $cyber->api_user,
            'controller' => 'submitDBCreation',
            'databaseWebsite' => $server->fqdn,
            'dbName' => $request->dbName,
            'dbUsername' => $request->dbUsername,
            'dbPassword' => $request->dbPassword,
            'webUserName' => $server->domain
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => $cpAuth,
                'Content-Type' => 'application/json'
            ])->withOptions(['verify' => false])->post($cpUrl, $payload);

            if ($response->ok()) {
                $data = $response->json();
                if ($data['status'] ?? false) {
                    return response()->json([
                        'success' => true,
                        'message' => '데이터베이스가 성공적으로 생성되었습니다.',
                        'data' => $data
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => $data['error_msg'] ?? '데이터베이스 생성에 실패했습니다.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'CyberPanel API 호출 실패',
                    'status' => $response->status(),
                    'body' => $response->body()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'API 호출 중 오류가 발생했습니다.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 데이터베이스 삭제 전 사용자 검증
     */
    public function verifyUserForDatabaseDeletion(Request $request, $serverId)
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

            // 2. 서버 소유권 확인
            $server = Server::where('id', $serverId)
                ->where('user_id', $user->id)
                ->first();

            if (!$server) {
                return response()->json([
                    'success' => false,
                    'message' => '서버를 찾을 수 없거나 접근 권한이 없습니다.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => '사용자 검증이 완료되었습니다.',
                'user_email' => $user->email,
                'server_domain' => $server->domain
            ]);

        } catch (\Exception $e) {
            Log::error('데이터베이스 삭제 사용자 검증 예외', [
                'server_id' => $serverId,
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
     * 데이터베이스 패스워드 변경 전 사용자 검증
     */
    public function verifyUserForDatabasePasswordChange(Request $request, $serverId)
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

            // 2. 서버 소유권 확인
            $server = Server::where('id', $serverId)
                ->where('user_id', $user->id)
                ->first();

            if (!$server) {
                return response()->json([
                    'success' => false,
                    'message' => '서버를 찾을 수 없거나 접근 권한이 없습니다.'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => '사용자 검증이 완료되었습니다.',
                'user_email' => $user->email,
                'server_domain' => $server->domain
            ]);

        } catch (\Exception $e) {
            Log::error('데이터베이스 패스워드 변경 사용자 검증 예외', [
                'server_id' => $serverId,
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
     * 데이터베이스 패스워드 변경 (SSH 방식)
     */
    public function changeDatabasePassword(Request $request, $serverId)
    {
        $request->validate([
            'dbName' => 'required|string',
            'dbUsername' => 'required|string',
            'new_password' => 'required|string|min:8',
            'dashboard_password' => 'required|string'
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => '로그인이 필요합니다.'
                ], 401);
            }

            // 1. 대시보드 로그인 패스워드 재검증
            if (!Hash::check($request->dashboard_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'error' => '대시보드 로그인 패스워드가 올바르지 않습니다.'
                ], 400);
            }

            // 2. 서버 소유권 재확인
            $server = Server::where('id', $serverId)
                ->where('user_id', $user->id)
                ->first();

            if (!$server) {
                return response()->json([
                    'success' => false,
                    'error' => '서버를 찾을 수 없거나 접근 권한이 없습니다.'
                ], 404);
            }

            $cyber = CyberpanelServer::find($server->cyberpanel_server_id);
            if (!$cyber) {
                return response()->json([
                    'success' => false,
                    'error' => 'CyberPanel 서버 정보를 찾을 수 없습니다.'
                ], 404);
            }

            // SSH를 통해 MySQL 패스워드 변경
            $dbUsername = $request->dbUsername;
            $newPassword = $request->new_password;
            
            // SSH 접속 정보 확인
            if (!$cyber->ssh_user) {
                return response()->json([
                    'success' => false,
                    'error' => '접속 정보가 설정되지 않았습니다. 관리자에게 문의하세요.'
                ], 500);
            }
            
            // SSH 키 경로 설정 (웹 서버에서 접근 가능한 경로 사용)
            $sshKeyPath = $cyber->ssh_key_path ?: '/var/www/hostylecms/storage/ssh_key';
            
            // SSH 키 파일 존재 및 권한 확인
            if (!file_exists($sshKeyPath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'SSH 키 파일이 존재하지 않습니다: ' . $sshKeyPath
                ], 500);
            }
            
            if (!is_readable($sshKeyPath)) {
                return response()->json([
                    'success' => false,
                    'error' => 'SSH 키 파일에 읽기 권한이 없습니다: ' . $sshKeyPath
                ], 500);
            }
            
            // CyberPanel MySQL 패스워드 가져오기
            $mysqlPassword = $cyber->mysql_password;
            
            // SSH 명령어로 MySQL 패스워드 변경 (SSH 키 인증)
            $sshCommand = "ssh -i {$sshKeyPath} -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR {$cyber->ssh_user}@{$cyber->host} \"mysql -u root -p'{$mysqlPassword}' -e \\\"ALTER USER '{$dbUsername}'@'localhost' IDENTIFIED BY '{$newPassword}'; FLUSH PRIVILEGES;\\\"\"";
            
            // SSH 패스워드가 설정되어 있다면 패스워드 인증 사용
            if ($cyber->ssh_password) {
                $sshCommand = "sshpass -p '{$cyber->ssh_password}' ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR {$cyber->ssh_user}@{$cyber->host} \"mysql -u root -p'{$mysqlPassword}' -e \\\"ALTER USER '{$dbUsername}'@'localhost' IDENTIFIED BY '{$newPassword}'; FLUSH PRIVILEGES;\\\"\"";
            }
            
            Log::info('SSH MySQL 패스워드 변경 요청', [
                'server_id' => $serverId,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'domain' => $server->domain,
                'db_username' => $dbUsername,
                'ssh_host' => $cyber->host,
                'ssh_user' => $cyber->ssh_user
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
                    'message' => '데이터베이스 패스워드가 성공적으로 변경되었습니다.'
                ]);
            } else {
                $errorMessage = implode("\n", $output);
                return response()->json([
                    'success' => false,
                    'error' => '패스워드 변경에 실패했습니다: ' . $errorMessage
                ], 400);
            }
            
        } catch (\Exception $e) {
            Log::error('데이터베이스 패스워드 변경 SSH 오류', [
                'server_id' => $serverId,
                'user_id' => Auth::id(),
                'exception' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => '패스워드 변경 중 오류가 발생했습니다: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 데이터베이스 삭제 (검증 후)
     */
    public function deleteDatabase(Request $request, $serverId)
    {
        $request->validate([
            'dbName' => 'required|string',
            'dashboard_password' => 'required|string'
        ]);

        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => '로그인이 필요합니다.'
                ], 401);
            }

            // 1. 대시보드 로그인 패스워드 재검증
            if (!Hash::check($request->dashboard_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'error' => '대시보드 로그인 패스워드가 올바르지 않습니다.'
                ], 400);
            }

            // 2. 서버 소유권 재확인
            $server = Server::where('id', $serverId)
                ->where('user_id', $user->id)
                ->first();

            if (!$server) {
                return response()->json([
                    'success' => false,
                    'error' => '서버를 찾을 수 없거나 접근 권한이 없습니다.'
                ], 404);
            }

            $cyber = CyberpanelServer::find($server->cyberpanel_server_id);
            if (!$cyber) {
                return response()->json([
                    'success' => false,
                    'error' => 'CyberPanel 서버 정보를 찾을 수 없습니다.'
                ], 404);
            }

            $cpUrl = ($cyber->ssl ? 'https' : 'http') . "://{$cyber->host}:{$cyber->api_port}/cloudAPI/";
            $cpAuth = $cyber->api_token ?: base64_encode($cyber->api_user . ':' . $cyber->api_password);
            
            $payload = [
                'serverUserName' => $cyber->api_user,
                'controller' => 'submitDatabaseDeletion',
                'dbName' => $request->dbName
            ];

            $response = Http::withHeaders([
                'Authorization' => $cpAuth,
                'Content-Type' => 'application/json'
            ])->withOptions(['verify' => false])->post($cpUrl, $payload);

            if ($response->ok()) {
                $data = $response->json();
                if ($data['status'] ?? false) {
                    return response()->json([
                        'success' => true,
                        'message' => '데이터베이스가 성공적으로 삭제되었습니다.',
                        'data' => $data
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => $data['error_msg'] ?? '데이터베이스 삭제에 실패했습니다.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'CyberPanel API 호출 실패',
                    'status' => $response->status(),
                    'body' => $response->body()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'API 호출 중 오류가 발생했습니다.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 