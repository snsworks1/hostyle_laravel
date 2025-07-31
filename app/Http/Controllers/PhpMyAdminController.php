<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Server;

class PhpMyAdminController extends Controller
{

    /**
     * 대시보드 패스워드로 phpMyAdmin 자동 로그인
     */
    public function autoLoginToPhpMyAdmin(Request $request, $serverId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => '인증되지 않은 사용자입니다.'], 401);
            }

            $server = Server::where('id', $serverId)
                ->where('user_id', $user->id)
                ->first();

            if (!$server) {
                return response()->json(['error' => '서버를 찾을 수 없습니다.'], 404);
            }

            // 대시보드 패스워드 검증
            $dashboardPassword = $request->input('dashboard_password');
            if (!$dashboardPassword) {
                return response()->json(['error' => '대시보드 패스워드를 입력해주세요.'], 400);
            }

            // CyberPanel 서버 정보 가져오기
            $cyberpanelServer = $server->cyberpanelServer;
            if (!$cyberpanelServer) {
                return response()->json(['error' => 'CyberPanel 서버 정보를 찾을 수 없습니다.'], 404);
            }

            Log::info('CyberPanel phpMyAdmin 자동 로그인 시작', [
                'server_id' => $server->id,
                'domain' => $server->domain,
                'user_id' => $user->id,
                'cyberpanel_host' => $cyberpanelServer->host,
                'request_data' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // CyberPanel phpMyAdmin URL
            $cyberpanelPhpMyAdminUrl = "https://{$cyberpanelServer->host}:8090/phpmyadmin/phpmyadminsignin.php";
            
            // 자동 로그인을 위한 HTML 폼 생성
            $html = "
            <!DOCTYPE html>
            <html>
            <head>
                <title>phpMyAdmin 자동 로그인</title>
            </head>
            <body>
                <form id='autoLoginForm' method='POST' action='{$cyberpanelPhpMyAdminUrl}'>
                    <input type='hidden' name='username' value='{$server->domain}'>
                    <input type='hidden' name='password' value='{$dashboardPassword}'>
                </form>
                <script>
                    document.getElementById('autoLoginForm').submit();
                </script>
                <p>phpMyAdmin으로 자동 로그인 중입니다...</p>
            </body>
            </html>";
            
            Log::info('CyberPanel phpMyAdmin 자동 로그인 완료', [
                'server_id' => $server->id,
                'domain' => $server->domain,
                'cyberpanel_url' => $cyberpanelPhpMyAdminUrl
            ]);

            return response($html, 200, ['Content-Type' => 'text/html']);

        } catch (\Exception $e) {
            Log::error('CyberPanel phpMyAdmin 자동 로그인 실패', [
                'server_id' => $serverId ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'phpMyAdmin 자동 로그인 중 오류가 발생했습니다: ' . $e->getMessage()], 500);
        }
    }


} 