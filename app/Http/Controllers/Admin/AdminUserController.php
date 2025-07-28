<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminUserController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $users = User::all();
        return Inertia::render('Admin/Users', [
            'users' => $users,
        ]);
    }
}
