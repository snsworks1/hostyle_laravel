<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOSTYLE - 이메일 디자인 미리보기</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .preview-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .email-preview {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .preview-header {
            background: #667eea;
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .email-content {
            padding: 0;
        }
        
        .email-frame {
            width: 100%;
            height: 600px;
            border: none;
            background: white;
        }
        
        .controls {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .control-group {
            margin-bottom: 20px;
        }
        
        .control-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }
        
        .control-group select,
        .control-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        
        .btn:hover {
            background: #5a67d8;
        }
        
        .btn-secondary {
            background: #718096;
        }
        
        .btn-secondary:hover {
            background: #4a5568;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .info-box {
            background: #ebf8ff;
            border: 1px solid #bee3f8;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .info-box h3 {
            color: #2b6cb0;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .info-box p {
            color: #4a5568;
            font-size: 14px;
            line-height: 1.5;
        }
        
        @media (max-width: 768px) {
            .preview-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <!-- 이메일 미리보기 -->
        <div class="email-preview">
            <div class="preview-header">
                📧 이메일 미리보기
            </div>
            <div class="email-content">
                <iframe 
                    id="emailFrame" 
                    class="email-frame" 
                    src="{{ route('email.preview.content', ['type' => 'verification']) }}"
                ></iframe>
            </div>
        </div>
        
        <!-- 컨트롤 패널 -->
        <div class="controls">
            <div class="info-box">
                <h3>🎨 이메일 디자인 미리보기</h3>
                <p>
                    HOSTYLE에서 발송되는 이메일들의 디자인을 미리 확인할 수 있습니다.<br>
                    다양한 이메일 유형을 선택하여 실제 발송될 모습을 확인해보세요.
                </p>
            </div>
            
            <div class="control-group">
                <label for="emailType">이메일 유형 선택</label>
                <select id="emailType" onchange="changeEmailType()">
                    <option value="verification">이메일 인증</option>
                    <option value="reset">비밀번호 재설정</option>
                    <option value="welcome">환영 이메일</option>
                </select>
            </div>
            
            <div class="control-group">
                <label for="emailSize">이메일 크기</label>
                <select id="emailSize" onchange="changeEmailSize()">
                    <option value="600">데스크톱 (600px)</option>
                    <option value="400">태블릿 (400px)</option>
                    <option value="320">모바일 (320px)</option>
                </select>
            </div>
            
            <div class="control-group">
                <label for="emailClient">이메일 클라이언트</label>
                <select id="emailClient" onchange="changeEmailClient()">
                    <option value="gmail">Gmail</option>
                    <option value="outlook">Outlook</option>
                    <option value="apple">Apple Mail</option>
                    <option value="yahoo">Yahoo Mail</option>
                </select>
            </div>
            
            <div class="btn-group">
                <button class="btn" onclick="refreshPreview()">🔄 새로고침</button>
                <button class="btn btn-secondary" onclick="downloadHTML()">💾 HTML 다운로드</button>
            </div>
            
            <div class="info-box">
                <h3>📱 반응형 디자인</h3>
                <p>
                    모든 이메일은 모바일, 태블릿, 데스크톱에서 최적화되어 표시됩니다.<br>
                    실제 이메일 클라이언트에서도 동일한 모습으로 보입니다.
                </p>
            </div>
        </div>
    </div>

    <script>
        function changeEmailType() {
            const type = document.getElementById('emailType').value;
            const frame = document.getElementById('emailFrame');
            frame.src = `{{ route('email.preview.content') }}?type=${type}`;
        }
        
        function changeEmailSize() {
            const size = document.getElementById('emailSize').value;
            const frame = document.getElementById('emailFrame');
            frame.style.width = size + 'px';
            frame.style.margin = '0 auto';
            frame.style.display = 'block';
        }
        
        function changeEmailClient() {
            const client = document.getElementById('emailClient').value;
            // 이메일 클라이언트별 스타일 적용 (향후 확장 가능)
            console.log('Selected email client:', client);
        }
        
        function refreshPreview() {
            const frame = document.getElementById('emailFrame');
            frame.src = frame.src;
        }
        
        function downloadHTML() {
            const frame = document.getElementById('emailFrame');
            const iframeDoc = frame.contentDocument || frame.contentWindow.document;
            const htmlContent = iframeDoc.documentElement.outerHTML;
            
            const blob = new Blob([htmlContent], { type: 'text/html' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'hostyle-email.html';
            a.click();
            window.URL.revokeObjectURL(url);
        }
        
        // 초기 설정
        document.addEventListener('DOMContentLoaded', function() {
            changeEmailSize();
        });
    </script>
</body>
</html> 