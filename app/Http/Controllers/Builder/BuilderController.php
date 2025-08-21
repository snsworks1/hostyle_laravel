<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use App\Models\BuilderPage;
use App\Models\Server;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BuilderController extends Controller
{
    public function app(Request $request, $serverId)
    {
        try {
            // 세션 및 인증 상태 확인
            if (!auth()->check()) {
                abort(401, 'Unauthenticated');
            }
            
            // 세션 ID 확인
            if (!$request->session()->getId()) {
                abort(401, 'Invalid session');
            }
            
            // 세션 데이터 정리 (불필요한 데이터 제거)
            $request->session()->forget(['_previous', 'url']);
            
            $server = Server::where('id', $serverId)
                ->where('user_id', auth()->id())
                ->firstOrFail();
                
        } catch (\Exception $e) {
            \Log::error('Builder app error: ' . $e->getMessage());
            abort(500, 'Internal server error: ' . $e->getMessage());
        }

        // pageId가 있으면 해당 페이지, 없으면 메인 페이지를 찾음
        $pageId = $request->query('page');
        if ($pageId) {
            $page = BuilderPage::where('server_id', $serverId)
                ->where('id', $pageId)
                ->firstOrFail();
        } else {
            // 메인 페이지를 찾거나, 없으면 첫 번째 페이지를 사용
            $page = BuilderPage::where('server_id', $serverId)
                ->where('is_main', true)
                ->first();
            
            if (!$page) {
                $page = BuilderPage::where('server_id', $serverId)
                    ->orderBy('created_at', 'asc')
                    ->first();
                    
                // 페이지가 없으면 기본 메인 페이지 생성
                if (!$page) {
                    $page = $server->createDefaultMainPage();
                }
            }
        }

        return Inertia::render('Builder/App', [
            'server' => $server,
            'serverId' => $server->id,
            'pageId' => $page->id,
            'origin' => config('app.url'),
        ]);
    }

    public function pages($serverId)
    {
        $server = Server::where('id', $serverId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pages = $server->builderPages()->orderBy('created_at', 'desc')->get();

        return Inertia::render('Builder/Pages', [
            'serverId' => $server->id,
            'pages' => $pages,
        ]);
    }
}
