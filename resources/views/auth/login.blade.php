<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostyle - 로그인</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Noto Sans KR"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans">
    <div class="min-h-screen flex">
        <!-- 좌측: 로그인 폼 -->
        <div class="flex-1 flex items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div>
                    <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                        로그인
                    </h2>
                </div>
                <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="rounded-md shadow-sm -space-y-px">
                        <div>
                            <label for="email" class="sr-only">이메일</label>
                            <input id="email" name="email" type="email" required 
                                   class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                                   placeholder="이메일">
                        </div>
                        <div>
                            <label for="password" class="sr-only">비밀번호</label>
                            <input id="password" name="password" type="password" required 
                                   class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                                   placeholder="비밀번호">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-gray-500 hover:text-gray-400">
                                비밀번호를 잊으셨나요?
                            </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-blue-400 to-green-400 hover:from-blue-500 hover:to-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            로그인
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-600">
                            계정이 없으신가요? 
                            <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                                회원가입
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- 우측: 브랜딩 영역 -->
        <div class="hidden lg:block lg:w-1/2 bg-gradient-to-b from-purple-600 to-purple-800 flex items-center justify-center">
            <div class="text-center text-white px-8">
                <div class="mb-8">
                    <h1 class="text-2xl font-bold mb-4">WELCOME TO</h1>
                    
                    <!-- 호스타일 아이콘/로고 -->
                    <div class="w-24 h-24 mx-auto mb-6 border-2 border-white rounded-lg flex items-center justify-center">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    
                    <h2 class="text-4xl font-bold mb-4">HOSTYLE</h2>
                </div>
                
                <p class="text-lg mb-8 text-purple-100">
                    카페, 병원, 소상공인을 위한<br>
                    쉽고 빠른 홈페이지 생성 플랫폼
                </p>
                
                <div class="flex justify-center space-x-6 text-sm text-purple-200">
                    <a href="#" class="hover:text-white transition-colors">개인정보 처리방침</a>
                    <a href="#" class="hover:text-white transition-colors">서비스 이용약관</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 