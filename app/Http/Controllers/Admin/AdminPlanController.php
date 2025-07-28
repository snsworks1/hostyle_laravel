<?php

namespace App\Http\Controllers\Admin;

use App\Models\Plan;
use App\Models\Feature;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Models\CyberpanelServer;
use Illuminate\Support\Facades\Http;

class AdminPlanController extends Controller
{
    public function index()
    {
        $plans = Plan::with('features')->get();
        $features = Feature::all();
        $servers = \App\Models\CyberpanelServer::where('status', 'active')->get();
        return Inertia::render('Admin/Plans', [
            'plans' => $plans,
            'features' => $features,
            'servers' => $servers
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'label' => 'required',
            'price' => 'required|numeric',
            'trial_days' => 'nullable|integer',
            'disk' => 'nullable|string',
            'traffic' => 'nullable|string',
            'domains' => 'nullable|integer',
            'subdomains' => 'nullable|integer',
            'databases' => 'nullable|integer',
            'emails' => 'nullable|integer',
            'features' => 'array',
        ]);
        $plan = Plan::create($data);
        if (!empty($data['features'])) {
            $plan->features()->sync($data['features']);
        }
        return redirect()->route('admin.plans.index')->with('success', '플랜이 추가되었습니다.');
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);
        $data = $request->validate([
            'name' => 'required',
            'label' => 'required',
            'price' => 'required|numeric',
            'trial_days' => 'nullable|integer',
            'disk' => 'nullable|string',
            'traffic' => 'nullable|string',
            'domains' => 'nullable|integer',
            'subdomains' => 'nullable|integer',
            'databases' => 'nullable|integer',
            'emails' => 'nullable|integer',
            'features' => 'array',
        ]);
        $plan->update($data);
        if (isset($data['features'])) {
            $plan->features()->sync($data['features']);
        }
        return redirect()->route('admin.plans.index')->with('success', '플랜이 수정되었습니다.');
    }

    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->features()->detach();
        $plan->delete();
        return redirect()->route('admin.plans.index')->with('success', '플랜이 삭제되었습니다.');
    }

    /**
     * 플랜과 사이버패널 패키지 동기화
     */
    public function syncCyberpanelPackages(Request $request)
    {
        \Log::info('syncCyberpanelPackages called', ['user' => auth()->user()]);
        $plans = Plan::all();
        $serverId = $request->input('server_id');
        if ($serverId) {
            $servers = CyberpanelServer::where('id', $serverId)->where('status', 'active')->get();
        } else {
            $servers = CyberpanelServer::where('status', 'active')->get();
        }
        $results = [];
        foreach ($servers as $server) {
            $protocol = $server->ssl ? 'https' : 'http';
            $url = "{$protocol}://{$server->host}:{$server->api_port}/cloudAPI/";
            $auth = $server->api_token ?: base64_encode($server->api_user . ':' . $server->api_password);
            // 1. listPackages 호출
            $listRes = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => $auth,
                    'Content-Type' => 'application/json'
                ])
                ->withOptions(['verify' => false])
                ->post($url, [
                    'serverUserName' => $server->api_user,
                    'controller' => 'listPackages'
                ]);
            $remotePackages = $listRes->json()['data'] ?? [];
            foreach ($plans as $plan) {
                $matched = collect($remotePackages)->first(fn($p) => $p['packageName'] === $plan->label);
                $action = '동일';
                $error = null;
                // GB → MB 변환 함수
                $gbToMb = function($value) {
                    $num = (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    return $num > 0 ? $num * 1024 : 0;
                };
                // 플랜별 이메일 계정 수: DB 값 사용, 없으면 0
                $emails = $plan->emails ?? 0;
                // 무료플랜 도메인 1로 고정, 나머지는 DB값
                $allowedDomains = strtolower($plan->name) === 'free' ? 1 : ($plan->domains ?? 1);
                // 플랜별 사양 매핑 (디스크/트래픽 MB 변환)
                $spec = [
                    'serverUserName' => $server->api_user,
                    'packageName' => $plan->label,
                    'diskSpace' => $gbToMb($plan->disk),
                    'bandwidth' => $gbToMb($plan->traffic),
                    'dataBases' => $plan->databases ?? 0,
                    'ftpAccounts' => 0,
                    'emails' => $emails,
                    'allowedDomains' => $allowedDomains,
                    'api' => 1,
                    'allowFullDomain' => 1
                ];
                if (!$matched) {
                    // submitPackage 호출
                    $createPayload = array_merge($spec, [
                        'controller' => 'submitPackage',
                    ]);
                    \Log::info('CyberPanel submitPackage 요청', ['url' => $url, 'payload' => $createPayload]);
                    $res = Http::timeout(15)
                        ->withHeaders([
                            'Authorization' => $auth,
                            'Content-Type' => 'application/json'
                        ])
                        ->withOptions(['verify' => false])
                        ->post($url, $createPayload);
                    $json = $res->json();
                    \Log::info('CyberPanel submitPackage 응답', ['response' => $json]);
                    if (isset($json['status']) && $json['status'] == 1) {
                        $action = '생성(성공)';
                    } else if (isset($json['error_message']) && str_contains($json['error_message'], 'Duplicate entry')) {
                        // 패키지 중복 에러 발생 시 즉시 수정 API 호출
                        $editPayload = array_merge($spec, [
                            'controller' => 'submitPackageModify',
                        ]);
                        \Log::info('CyberPanel submitPackageModify(중복) 요청', ['url' => $url, 'payload' => $editPayload]);
                        $editRes = Http::timeout(15)
                            ->withHeaders([
                                'Authorization' => $auth,
                                'Content-Type' => 'application/json'
                            ])
                            ->withOptions(['verify' => false])
                            ->post($url, $editPayload);
                        $editJson = $editRes->json();
                        \Log::info('CyberPanel submitPackageModify(중복) 응답', ['response' => $editJson]);
                        if (isset($editJson['status']) && $editJson['status'] == 1) {
                            $action = '수정(성공)';
                        } else {
                            $action = '수정(실패)';
                            $error = $editJson['error_message'] ?? 'Unknown error';
                        }
                    } else {
                        $action = '생성(실패)';
                        $error = $json['error_message'] ?? 'Unknown error';
                    }
                } else {
                    // 주요 항목이 다르면 submitPackageModify 호출
                    $diff = false;
                    if (
                        (int)filter_var($plan->disk, FILTER_SANITIZE_NUMBER_INT) != $matched['diskSpace'] ||
                        (int)filter_var($plan->traffic, FILTER_SANITIZE_NUMBER_INT) != $matched['bandwidth'] ||
                        ($plan->domains ?? 0) != $matched['allowedDomains'] ||
                        ($plan->databases ?? 0) != $matched['dataBases']
                    ) {
                        $diff = true;
                    }
                    if ($diff) {
                        $editPayload = array_merge($spec, [
                            'controller' => 'submitPackageModify',
                        ]);
                        \Log::info('CyberPanel submitPackageModify 요청', ['url' => $url, 'payload' => $editPayload]);
                        $res = Http::timeout(15)
                            ->withHeaders([
                                'Authorization' => $auth,
                                'Content-Type' => 'application/json'
                            ])
                            ->withOptions(['verify' => false])
                            ->post($url, $editPayload);
                        $json = $res->json();
                        \Log::info('CyberPanel submitPackageModify 응답', ['response' => $json]);
                        if (isset($json['status']) && $json['status'] == 1) {
                            $action = '수정(성공)';
                        } else {
                            $action = '수정(실패)';
                            $error = $json['error_message'] ?? 'Unknown error';
                        }
                    }
                }
                $results[] = [
                    'server' => $server->name,
                    'plan' => $plan->label,
                    'action' => $action,
                    'error' => $error
                ];
            }
        }
        return response()->json(['results' => $results]);
    }
} 