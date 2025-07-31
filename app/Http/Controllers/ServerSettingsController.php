<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\CyberpanelServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
