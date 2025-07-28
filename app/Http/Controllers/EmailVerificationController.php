<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailVerificationController extends Controller
{
    /**
     * 이메일 인증 안내 페이지를 표시합니다.
     */
    public function notice()
    {
        return Inertia::render('Auth/VerifyEmail');
    }
}
