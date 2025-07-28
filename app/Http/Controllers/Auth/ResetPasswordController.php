<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            // 성공 시 로그인 페이지로 리다이렉트 + flash 메시지 전달
            return redirect()->route('login')->with('success', __($status));
        } else {
            return back()->withErrors(['email' => __($status)]);
        }
    }
} 