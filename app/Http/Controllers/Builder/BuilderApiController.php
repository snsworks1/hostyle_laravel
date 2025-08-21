<?php

namespace App\Http\Controllers\Builder;

use App\Http\Controllers\Controller;
use App\Models\BuilderPage;
use App\Models\Server;
use Illuminate\Http\Request;
use App\Services\Builder\Renderer;

class BuilderApiController extends Controller
{
    public function index($serverId)
    {
        try {
            \Log::info('BuilderApiController::index called', ['serverId' => $serverId, 'user_id' => auth()->id()]);
            
            $server = Server::where('id', $serverId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            \Log::info('Server found', ['server' => $server->toArray()]);

            $pages = $server->builderPages()->orderBy('created_at', 'desc')->get();

            \Log::info('Pages loaded', ['count' => $pages->count(), 'pages' => $pages->toArray()]);

            return response()->json($pages);
        } catch (\Exception $e) {
            \Log::error('BuilderApiController::index error', [
                'serverId' => $serverId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function show($serverId, $pageId)
    {
        try {
            \Log::info('BuilderApiController::show called', ['serverId' => $serverId, 'pageId' => $pageId, 'user_id' => auth()->id()]);
            
            $server = Server::where('id', $serverId)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            \Log::info('Server found in show', ['server' => $server->toArray()]);

            $page = BuilderPage::where('server_id', $serverId)
                ->where('id', $pageId)
                ->firstOrFail();

            \Log::info('Page found', ['page' => $page->toArray()]);

            return response()->json($page);
        } catch (\Exception $e) {
            \Log::error('BuilderApiController::show error', [
                'serverId' => $serverId,
                'pageId' => $pageId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function save(Request $request, $serverId, $pageId)
    {
        $server = Server::where('id', $serverId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $page = BuilderPage::where('server_id', $serverId)
            ->where('id', $pageId)
            ->firstOrFail();

        $data = $request->validate([
            'page_schema' => 'required|array',
            'site_tokens' => 'nullable|array',
        ]);

        $page->update($data);

        // 미리보기 HTML/CSS도 즉시 생성 (Renderer 클래스가 구현될 때까지 주석 처리)
        // $render = app(Renderer::class)->preview($page->page_schema, $page->site_tokens ?? []);
        // $page->update([
        //     'preview_html' => $render['html'],
        //     'preview_css' => $render['css']
        // ]);

        return response()->json(['ok' => true]);
    }

    public function publish(Request $request, $serverId, $pageId)
    {
        $server = Server::where('id', $serverId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $page = BuilderPage::where('server_id', $serverId)
            ->where('id', $pageId)
            ->firstOrFail();

        // $render = app(Renderer::class)->publish($page->page_schema, $page->site_tokens ?? []);
        $page->update([
            // 'published_html' => $render['html'],
            // 'published_css' => $render['css'],
            'is_published' => true,
            'published_at' => now(),
        ]);

        // TODO: CyberPanel 연동 (SFTP/API 업로드)
        // app(RenderDeployer::class)->upload($page);

        return response()->json(['ok' => true, 'published_at' => $page->published_at]);
    }

    public function create(Request $request, $serverId)
    {
        $server = Server::where('id', $serverId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:builder_pages,slug',
        ]);

        $page = BuilderPage::create([
            'server_id' => $server->id,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'type' => 'page',
            'is_main' => false,
            'page_schema' => ['sections' => []],
            'site_tokens' => [
                'brandColor' => '#111111',
                'contentWidth' => '1080px',
                'fontBase' => 'Noto Sans KR, sans-serif',
                'radius' => '12px'
            ],
        ]);

        return response()->json($page);
    }
}
