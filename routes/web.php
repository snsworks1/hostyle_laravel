<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ServerController;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Admin\AdminCyberpanelController;
use App\Http\Controllers\ServerSettingsController;
use App\Http\Controllers\PhpSettingsController;
use App\Http\Controllers\CyberPanelFileManagerController;

use Illuminate\Support\Facades\Response;
use phpseclib3\Net\SSH2;
use phpseclib3\Crypt\PublicKeyLoader;

// inertia 미들웨어 그룹 (GET 라우트만)
Route::middleware([HandleInertiaRequests::class])->group(function () {
    // 메인 페이지
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    });

    // 로그인 페이지
    Route::get('/login', function () {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
        ]);
    })->name('login');

    // 비밀번호 찾기(이메일 입력 폼)
    Route::get('/password/reset', function () {
        return Inertia::render('Auth/ForgotPassword');
    })->name('password.request');

    // 비밀번호 재설정 폼
    Route::get('/password/reset/{token}', function (string $token) {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => request()->query('email'),
        ]);
    })->name('password.reset');

    // 회원가입 폼
    Route::get('/register', function () {
        return Inertia::render('Auth/Register');
    })->name('register');

    // 이메일 인증 안내
    Route::get('/email/verify', function () {
        return Inertia::render('Auth/VerifyEmail');
    })->name('verification.notice');

    // 정책 및 약관 라우트
    Route::get('/policy', function () {
        return Inertia::render('Policy');
    })->name('policy.show');

    Route::get('/terms', function () {
        return Inertia::render('Terms');
    })->name('terms.show');

    // 이메일 인증 메일 재발송 라우트 (POST, 인증 필요)
    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()) {
            $request->user()->sendEmailVerificationNotification();
        }
        return back()->with('success', '인증 메일을 다시 발송했습니다.');
    })->middleware(['auth:sanctum', config('jetstream.auth_session')])->name('verification.send');
});

// 비밀번호 찾기(이메일 전송) - inertia 미들웨어 그룹 밖에서 처리
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware(['guest'])
    ->name('password.email');

// 비밀번호 재설정 처리 - inertia 미들웨어 그룹 밖에서 처리
Route::post('/reset-password', [ResetPasswordController::class, 'store'])->name('password.update');

// 인증 필요(verified) 라우트
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    HandleInertiaRequests::class,
])->group(function () {
    // 서버 선택 페이지 (서버가 있을 때)
    Route::get('/select-server', [ServerController::class, 'select'])->name('server.select');

    // 서버 상세보기 페이지
    Route::get('/server/{id}', [ServerController::class, 'show'])->name('server.show');

    // 서버 생성/구매 페이지
    Route::get('/create-server', [ServerController::class, 'create'])->name('server.create');

    // 서버 생성 폼 제출 시 결제 더미 페이지로 이동
    Route::post('/create-server/payment-dummy', [ServerController::class, 'paymentDummy'])->name('server.payment.dummy');

    // 서버 생성 처리
    Route::post('/create-server', [ServerController::class, 'store'])->name('server.store');
    // 서버 생성 실제 실행 (로딩 페이지에서 호출)
    Route::post('/create-server/provision', [ServerController::class, 'provision'])->name('server.provision');

    // 환불 관련
    Route::get('/server/{server}/refund/calculate', [ServerController::class, 'calculateRefundDetails'])->name('server.refund.calculate');
    Route::post('/server/{server}/refund', [ServerController::class, 'processRefund'])->name('server.refund.process');

    // 대시보드
    Route::get('/dashboard', [ServerController::class, 'dashboard'])->name('dashboard');

    // 설정/관리 페이지 라우트 추가
    Route::get('/server/{id}/settings/basic', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Settings/Basic');
    })->name('server.settings.basic');
    Route::get('/server/{id}/settings/seo', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Settings/Seo');
    })->name('server.settings.seo');
    Route::get('/server/{id}/settings/social', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Settings/Social');
    })->name('server.settings.social');
    Route::get('/server/{id}/settings/extra', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Settings/Extra');
    })->name('server.settings.extra');
    Route::get('/server/{id}/settings/domain-guide', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Settings/DomainGuide');
    })->name('server.settings.domain-guide');

    // 회원관리
    Route::get('/server/{id}/members/list', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Members/List');
    })->name('server.members.list');
    Route::get('/server/{id}/members/template', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Members/Template');
    })->name('server.members.template');
    Route::get('/server/{id}/members/settings', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Members/Settings');
    })->name('server.members.settings');
    Route::get('/server/{id}/members/points', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Members/Points');
    })->name('server.members.points');

    // 기능
    Route::get('/server/{id}/board/manage', fn($id) => Inertia::render('Server/Board/Manage'))->name('server.board.manage');
    Route::get('/server/{id}/forms/manage', fn($id) => Inertia::render('Server/Forms/Manage'))->name('server.forms.manage');
    Route::get('/server/{id}/calendar/manage', fn($id) => Inertia::render('Server/Calendar/Manage'))->name('server.calendar.manage');

    // 통계
    Route::get('/server/{id}/stats/visits', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Stats/Visits');
    })->name('server.stats.visits');
    Route::get('/server/{id}/stats/access', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Stats/Access');
    })->name('server.stats.access');

    // 서버 실시간 통계 API (fetchWebsiteData)
    Route::get('/server/{id}/fetch-website-data', [ServerController::class, 'fetchWebsiteData'])->name('server.fetchWebsiteData');

    // 서버관리
    Route::get('/server/{id}/admin/php-version', fn($id) => Inertia::render('Server/Admin/PhpVersion'))->name('server.admin.php-version');
    Route::get('/server/{id}/admin/mysql-password', fn($id) => Inertia::render('Server/Admin/MysqlPassword'))->name('server.admin.mysql-password');
    Route::get('/server/{id}/admin/mysql-users', fn($id) => Inertia::render('Server/Admin/MysqlUsers'))->name('server.admin.mysql-users');
    Route::get('/server/{id}/admin/backup', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Admin/Backup');
    })->name('server.admin.backup');
    Route::get('/server/{id}/admin/ssl', function ($id) {
        $controller = app(\App\Http\Controllers\ServerController::class);
        return $controller->renderWithServerProps($id, 'Server/Admin/Ssl');
    })->name('server.admin.ssl');
    
    Route::get('/server/{id}/admin/filemanager', fn($id) => Inertia::render('Server/Admin/Filemanager'))->name('server.admin.filemanager');

    Route::get('/server/{id}/admin/mysql', fn($id) => Inertia::render('Server/Admin/Mysql'))->name('server.admin.mysql');
    Route::get('/server/{id}/wordpress/themes', fn($id) => Inertia::render('Server/Wordpress/Themes'))->name('server.wordpress.themes');
    Route::get('/server/{id}/admin/php-settings', [ServerController::class, 'phpSettings'])->name('server.admin.php-settings');
    Route::get('/server/{id}/admin/mysql-settings', [ServerController::class, 'mysqlSettings'])->name('server.admin.mysql-settings');
    
    // PHP 설정 API
    Route::get('/server/{server}/php-settings', [PhpSettingsController::class, 'getPhpSettings'])->name('server.php-settings.get');
    Route::post('/server/{server}/php-settings', [PhpSettingsController::class, 'updatePhpSettings'])->name('server.php-settings.update');
    

    
    // PHP 버전 변경 API
    Route::post('/server/{server}/change-php-version', [ServerSettingsController::class, 'changePhpVersion'])->name('server.change-php-version');
    
    // MySQL 패스워드 변경 API
    Route::post('/server/{server}/verify-user-mysql-password', [ServerSettingsController::class, 'verifyUserForMysqlPassword'])->name('server.verify-user-mysql-password');
    Route::post('/server/{server}/change-mysql-password', [ServerSettingsController::class, 'changeMysqlPassword'])->name('server.change-mysql-password');
    Route::get('/server/{server}/initial-mysql-password', [ServerSettingsController::class, 'getInitialMysqlPassword'])->name('server.initial-mysql-password');
    
    // 데이터베이스 관리 API
    Route::get('/server/{server}/databases', [\App\Http\Controllers\DatabaseController::class, 'fetchDatabases'])->name('server.databases.fetch');
Route::post('/server/{server}/databases', [\App\Http\Controllers\DatabaseController::class, 'createDatabase'])->name('server.databases.create');
Route::post('/server/{server}/databases/verify-deletion', [\App\Http\Controllers\DatabaseController::class, 'verifyUserForDatabaseDeletion'])->name('server.databases.verify-deletion');
Route::delete('/server/{server}/databases', [\App\Http\Controllers\DatabaseController::class, 'deleteDatabase'])->name('server.databases.delete');
Route::post('/server/{server}/databases/verify-password-change', [\App\Http\Controllers\DatabaseController::class, 'verifyUserForDatabasePasswordChange'])->name('server.databases.verify-password-change');
Route::post('/server/{server}/databases/change-password', [\App\Http\Controllers\DatabaseController::class, 'changeDatabasePassword'])->name('server.databases.change-password');

// phpMyAdmin 자동 로그인 라우트
Route::post('/server/{server}/phpmyadmin/auto-login-form', [\App\Http\Controllers\PhpMyAdminController::class, 'autoLoginToPhpMyAdmin'])->name('server.phpmyadmin.auto-login-form.post');

// CyberPanel 파일매니저 SSO 라우트
Route::get('/server/{server}/cyberpanel/filemanager', [CyberPanelFileManagerController::class, 'redirectToFileManager'])
    ->name('cyberpanel.filemanager')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);




});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    \App\Http\Middleware\HandleInertiaRequests::class,
])->get('/user/profile', function () {
    return Inertia::render('Profile/Show');
})->name('profile.show');

Route::post('/user/password-check', function (Request $request) {
    $user = auth()->user();
    $isValid = $user && Hash::check($request->input('password'), $user->password);
    return response()->json(['valid' => $isValid]);
})->middleware('auth');

Route::get('/ssh-test', function () {
    try {
        $ssh = new \phpseclib3\Net\SSH2('27.102.138.143', 22);
        $key = \phpseclib3\Crypt\PublicKeyLoader::load(file_get_contents('/var/www/.ssh/id_ed25519'));
        if (!$ssh->login('root', $key)) {
            return Response::make('SSH 로그인 실패', 500);
        }
        $disk = $ssh->exec('df -h /');
        $memory = $ssh->exec('free -m');

        // 디스크 파싱
        $diskLines = explode("\n", trim($disk));
        $diskData = preg_split('/\s+/', $diskLines[1] ?? '');
        $diskAvail = $diskData[3] ?? '';
        $diskUsePercent = $diskData[4] ?? '';

        // 메모리 파싱
        $memLines = explode("\n", trim($memory));
        $memData = preg_split('/\s+/', $memLines[1] ?? '');
        $memAvailable = $memData[6] ?? '';

        $html = "<h2>서버 리소스 현황</h2>";
        $html .= "<ul>";
        $html .= "<li>현재 남은 디스크 용량: <b>{$diskAvail}</b></li>";
        $html .= "<li>현재 디스크 사용률: <b>{$diskUsePercent}</b></li>";
        $html .= "<li>메모리 available(남은량): <b>{$memAvailable} MB</b></li>";
        $html .= "</ul>";
        $html .= "<hr><pre>[원본 디스크 정보]\n$disk\n[원본 메모리 정보]\n$memory</pre>";
        return Response::make($html);
    } catch (\Exception $e) {
        return Response::make('에러: ' . $e->getMessage(), 500);
    }
});

Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/cyberpanel', [AdminCyberpanelController::class, 'index'])->name('admin.cyberpanel.index');
    Route::post('/admin/cyberpanel', [AdminCyberpanelController::class, 'store'])->name('admin.cyberpanel.store');
    Route::delete('/admin/cyberpanel/{id}', [AdminCyberpanelController::class, 'destroy'])->name('admin.cyberpanel.destroy');
    Route::post('/admin/cyberpanel/{id}/test', [AdminCyberpanelController::class, 'testConnection'])->name('admin.cyberpanel.test');
    Route::post('/admin/cyberpanel/{id}/fetch-users', [AdminCyberpanelController::class, 'fetchUsers'])->name('admin.cyberpanel.fetch-users');
    Route::get('/admin/user-servers', [\App\Http\Controllers\Admin\AdminUserServerController::class, 'index'])->name('admin.user-servers.index');
    Route::get('/admin/plans', [\App\Http\Controllers\Admin\AdminPlanController::class, 'index'])->name('admin.plans.index');
    Route::post('/admin/plans', [\App\Http\Controllers\Admin\AdminPlanController::class, 'store'])->name('admin.plans.store');
    // sync-cyberpanel 고정 경로를 {id} 라우트보다 위에 둠
    Route::post('/admin/plans/sync-cyberpanel', [\App\Http\Controllers\Admin\AdminPlanController::class, 'syncCyberpanelPackages'])->name('admin.plans.sync-cyberpanel');
    Route::put('/admin/plans/{id}', [\App\Http\Controllers\Admin\AdminPlanController::class, 'update'])->name('admin.plans.update');
    Route::post('/admin/plans/{id}', [\App\Http\Controllers\Admin\AdminPlanController::class, 'update']);
    Route::delete('/admin/plans/{id}', [\App\Http\Controllers\Admin\AdminPlanController::class, 'destroy'])->name('admin.plans.destroy');
});



