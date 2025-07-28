<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>관리자 페이지 - Hostyle CMS</title>
    @vite('resources/css/app.css')
</head>
<body>
    <nav>
        <ul>
            <li><a href="/admin">대시보드</a></li>
            <li><a href="/admin/users">사용자 관리</a></li>
            <li><a href="/admin/settings">설정</a></li>
        </ul>
    </nav>
    <section class="admin-content">
        @yield('content')
    </section>
    <footer>
        관리자 전용 &copy; 2024 Hostyle CMS
    </footer>
</body>
</html> 