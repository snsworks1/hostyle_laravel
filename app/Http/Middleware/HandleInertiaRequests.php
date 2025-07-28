<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'servers' => function () use ($request) {
                if ($request->user()) {
                    $userId = $request->user()->id;
                    $servers = \DB::table('servers')->where('user_id', $userId)->get();
                    \Log::info('inertia servers', ['user_id' => $userId, 'count' => $servers->count()]);
                    return $servers
                        ->map(function($server) {
                            $createdAt = \Carbon\Carbon::parse($server->created_at);
                            $expiresAt = $createdAt->copy()->addMonths($server->months);
                            return [
                                'id' => $server->id,
                                'site_name' => $server->site_name,
                                'domain' => $server->domain,
                                'region' => $server->region,
                                'platform' => $server->platform,
                                'plan' => $server->plan,
                                'months' => $server->months,
                                'status' => $server->status,
                                'created_at' => $server->created_at,
                                'expires_at' => $expiresAt->format('Y-m-d'),
                                'days_remaining' => now()->diffInDays($expiresAt, false),
                            ];
                        })
                        ->values()
                        ->all();
                }
                \Log::info('inertia servers', ['user_id' => null]);
                return [];
            },
        ]);
    }
}
