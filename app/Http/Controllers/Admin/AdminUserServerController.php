<?php

namespace App\Http\Controllers\Admin;

use App\Models\Server;
use App\Models\User;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class AdminUserServerController extends Controller
{
    public function index()
    {
        $servers = \DB::table('servers')
            ->leftJoin('users', 'servers.user_id', '=', 'users.id')
            ->select('servers.*', 'users.name as user_name', 'users.email as user_email')
            ->orderByDesc('servers.created_at')
            ->get();
        return Inertia::render('Admin/UserServers', [
            'servers' => $servers
        ]);
    }
} 