<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CyberPanel 파일매니저</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            padding: 50px; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container { 
            background: rgba(255,255,255,0.1); 
            padding: 30px; 
            border-radius: 10px; 
            backdrop-filter: blur(10px);
            max-width: 600px;
        }
        .success { color: #4ade80; }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
        }
        .btn:hover { background: #2563eb; }
        .server-info {
            background: rgba(59, 130, 246, 0.1);
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #3b82f6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CyberPanel 파일매니저</h1>
        <p class="success">SSO 로그인 성공!</p>
        
        <div class="server-info">
            <strong>서버 정보:</strong><br>
            도메인: {{ $domain }}<br>
            서버: {{ $server->domain }}<br>
            CyberPanel 호스트: {{ $server->cyberpanelServer->host }}
        </div>
        
        <br>
        <div class="loading"></div>
        <p>파일매니저로 자동 이동 중...</p>
        <br>
        <button class="btn" onclick="openFileManager()">
            파일매니저 열기
        </button>
    </div>
    
    <script>
        function openFileManager() {
            // 새 창에서 파일매니저 열기
            var fileManagerWindow = window.open('{{ $fileManagerUrl }}', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
            
            if (fileManagerWindow) {
                // 창이 열리면 현재 창 닫기
                setTimeout(() => {
                    window.close();
                }, 2000);
            } else {
                // 팝업이 차단된 경우 현재 창에서 이동
                window.location.href = '{{ $fileManagerUrl }}';
            }
        }
        
        // 자동으로 파일매니저 열기
        setTimeout(() => {
            openFileManager();
        }, 1000);
    </script>
</body>
</html> 