<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>공개 페이지 - Hostyle CMS</title>
    @vite('resources/css/app.css')
</head>
<body>
    <header>
        <h1>Hostyle CMS 공개 페이지</h1>
    </header>
    <main>
        @yield('content')
    </main>
    <footer>
        &copy; 2024 Hostyle CMS
    </footer>
</body>
</html> 