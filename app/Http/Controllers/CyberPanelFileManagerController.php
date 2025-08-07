<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CyberPanelService;
use Illuminate\Support\Facades\Log;

class CyberPanelFileManagerController extends Controller
{
    protected $cyberPanelService;
    
    public function __construct(CyberPanelService $cyberPanelService)
    {
        $this->cyberPanelService = $cyberPanelService;
    }
    
    /**
     * CyberPanel 파일매니저로 리디렉션
     */
    public function redirectToFileManager(Request $request, $serverId)
    {
        try {
            Log::info('CyberPanel 파일매니저 SSO 요청 시작', [
                'server_id' => $serverId,
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);
            
            // 서버 정보 조회
            $server = \App\Models\Server::with('cyberpanelServer')->findOrFail($serverId);
            
            // 사용자 권한 확인
            if ($server->user_id !== auth()->id()) {
                return response()->json(['error' => '접근 권한이 없습니다.'], 403);
            }
            
            // CyberPanel 서버 정보 확인
            if (!$server->cyberpanelServer) {
                return response()->json(['error' => 'CyberPanel 서버 정보를 찾을 수 없습니다.'], 404);
            }
            
            Log::info('CyberPanel 파일매니저 접근', [
                'server_id' => $server->id,
                'domain' => $server->fqdn,
                'cyberpanel_server' => $server->cyberpanelServer->host
            ]);
            
            // SSO 로그인 및 리디렉션
            return $this->cyberPanelService->ssoLoginAndRedirect($server);
            
        } catch (\Exception $e) {
            Log::error('CyberPanel 파일매니저 접근 실패', [
                'server_id' => $serverId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => '파일매니저 접근에 실패했습니다.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    

} 