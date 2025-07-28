<?php

namespace App\Http\Controllers\Admin;

use App\Models\CyberpanelServer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class AdminCyberpanelController extends Controller
{
    public function index()
    {
        $servers = CyberpanelServer::all();
        return Inertia::render('Admin/Cyberpanel', [
            'servers' => $servers,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'host' => 'required',
            'api_port' => 'required|integer',
            'api_user' => 'required',
            'api_password' => 'required',
            'ssl' => 'boolean',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable',
        ]);
        CyberpanelServer::create($data);
        return redirect()->route('admin.cyberpanel.index')->with('success', '등록 완료');
    }

    public function destroy($id)
    {
        CyberpanelServer::findOrFail($id)->delete();
        return redirect()->route('admin.cyberpanel.index')->with('success', '삭제 완료');
    }

    public function testConnection($id)
{
    $server = \App\Models\CyberpanelServer::findOrFail($id);
    $protocol = $server->ssl ? 'https' : 'http';
    $url = "{$protocol}://{$server->host}:{$server->api_port}/cloudAPI/";

    // api_token이 있으면 그 값 사용, 없으면 base64 인코딩
    if ($server->api_token) {
        $auth = $server->api_token;
    } else {
        $auth = base64_encode($server->api_user . ':' . $server->api_password);
    }

    $body = [
        'serverUserName' => $server->api_user,
        'controller' => 'verifyLogin'
    ];

    // 요청 정보 로그
    \Log::info('CyberPanel API 요청', [
        'url' => $url,
        'Authorization' => $auth,
        'body' => $body
    ]);

    $response = \Http::timeout(10)
        ->withHeaders([
            'Authorization' => $auth,
            'Content-Type' => 'application/json'
        ])
        ->withOptions(['verify' => false])
        ->post($url, $body);

    $json = $response->json();
    // 응답 정보 로그
    \Log::info('CyberPanel API 응답', [
        'status' => $response->status(),
        'body' => $json
    ]);

    if (
        $response->ok() &&
        (
            (isset($json['loginStatus']) && $json['loginStatus'] == 1) ||
            (isset($json['status']) && $json['status'] == 1)
        )
    ) {
        return response()->json(['success' => true, 'message' => '연동 성공', 'id' => $id]);
    } else {
        return response()->json(['success' => false, 'message' => '연동 실패: ' . ($json['error_message'] ?? 'Unknown error'), 'id' => $id]);
    }
}

    /**
     * CyberPanel fetchUsers API를 호출하여 사용자 서버 목록을 반환
     */
    public function fetchUsers($id)
    {
        $server = \App\Models\CyberpanelServer::findOrFail($id);
        $protocol = $server->ssl ? 'https' : 'http';
        $url = "{$protocol}://{$server->host}:{$server->api_port}/cloudAPI/";

        if ($server->api_token) {
            $auth = $server->api_token;
        } else {
            $auth = base64_encode($server->api_user . ':' . $server->api_password);
        }

        $body = '{"serverUserName": "' . $server->api_user . '", "controller": "fetchUsers"}';

        // 요청 정보 로그
        \Log::info('CyberPanel fetchUsers 요청', [
            'url' => $url,
            'Authorization' => $auth,
            'body' => $body
        ]);

        $response = \Http::timeout(15)
            ->withHeaders([
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/json'
            ])
            ->withOptions(['verify' => false])
            ->send('POST', $url, [
                'body' => $body
            ]);

        $json = $response->json();
        \Log::info('CyberPanel fetchUsers 응답', [
            'status' => $response->status(),
            'body' => $json
        ]);
        return response()->json($json);
    }
}
