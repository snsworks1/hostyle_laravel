# 🚀 SaaS 웹 빌더 시스템: 완전 설계 가이드

## 📋 목차
1. [시스템 개요](#1-시스템-개요)
2. [아키텍처 설계](#2-아키텍처-설계)
3. [데이터베이스 구조](#3-데이터베이스-구조)
4. [핵심 기능 설계](#4-핵심-기능-설계)
5. [구현 가이드](#5-구현-가이드)
6. [성능 최적화](#6-성능-최적화)
7. [배포 및 확장](#7-배포-및-확장)
8. [개발 로드맵](#8-개발-로드맵)

---

## 1. 시스템 개요

### 1.1. 프로젝트 목표
**중앙 집중식 웹 빌더 SaaS 플랫폼**
- Laravel + Vue.js/React.js 기반 대시보드
- GrapesJS 페이지 빌더 + Editor.js/Tiptap 에디터
- CyberPanel 사용자 계정에 배포
- 중앙 DB에서 모든 데이터 관리

### 1.2. 핵심 특징
- ✅ 중앙 집중식 템플릿 관리
- ✅ 사용자별 디스크 용량 정확한 관리
- ✅ 실시간 빌더 및 미리보기
- ✅ 자동 배포 시스템
- ✅ 확장 가능한 마이크로서비스 구조

### 1.3. 기술 스택
**백엔드:**
- Laravel (API, 대시보드, 빌더 로직)
- MySQL (중앙 데이터베이스)
- Redis (캐싱, 세션)
- CyberPanel API (사용자 계정 관리)

**프론트엔드:**
- Vue.js/React.js (대시보드 UI)
- GrapesJS (페이지 빌더)
- Editor.js/Tiptap (리치 텍스트 에디터)

**인프라:**
- Vultr (클라우드 호스팅)
- HAProxy (로드 밸런서)
- Kong (API 게이트웨이)
- Cloudflare (CDN)

---

## 2. 아키텍처 설계

### 2.1. 전체 시스템 구조
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   사용자 브라우저  │    │   관리자 브라우저  │    │   모바일 앱     │
└─────────┬───────┘    └─────────┬───────┘    └─────────┬───────┘
          │                      │                      │
          └──────────────────────┼──────────────────────┘
                                 │
                    ┌─────────────▼─────────────┐
                    │      Cloudflare CDN       │
                    └─────────────┬─────────────┘
                                  │
                    ┌─────────────▼─────────────┐
                    │    HAProxy 로드밸런서     │
                    └─────────────┬─────────────┘
                                  │
                    ┌─────────────▼─────────────┐
                    │   Kong API Gateway        │
                    └─────────────┬─────────────┘
                                  │
          ┌───────────────────────┼───────────────────────┐
          │                       │                       │
┌─────────▼─────────┐  ┌─────────▼─────────┐  ┌─────────▼─────────┐
│   Laravel 서버 1   │  │   Laravel 서버 2   │  │   Laravel 서버 3   │
│  (대시보드/API)    │  │  (빌더/에디터)     │  │   (인증/결제)      │
└─────────┬─────────┘  └─────────┬─────────┘  └─────────┬─────────┘
          │                      │                      │
          └──────────────────────┼──────────────────────┘
                                 │
                    ┌─────────────▼─────────────┐
                    │      MySQL 클러스터       │
                    │   (마스터-슬레이브)       │
                    └─────────────┬─────────────┘
                                  │
                    ┌─────────────▼─────────────┐
                    │      Redis 클러스터       │
                    │   (캐싱/세션)            │
                    └─────────────┬─────────────┘
                                  │
                    ┌─────────────▼─────────────┐
                    │    CyberPanel 서버        │
                    │  (사용자 웹사이트 호스팅)  │
                    └────────────────────────────┘
```

### 2.2. 데이터 흐름
```
1. 사용자 빌더 접속
   ↓
2. GrapesJS에서 템플릿 선택
   ↓
3. Laravel API에서 템플릿 데이터 로드
   ↓
4. 사용자 커스터마이징
   ↓
5. 자동 저장 (30초마다)
   ↓
6. 배포 시 CyberPanel 사용자 계정으로 파일 전송
   ↓
7. 사용자 웹사이트에서 Laravel API로 실시간 데이터 로드
```

### 2.3. 서버 구성 (Vultr)
**초기 구성 (단일 서버):**
- 8코어 16GB RAM
- 160GB SSD
- Ubuntu 20.04 LTS

**확장 후 구성:**
- **로드밸런서 서버**: 2코어 4GB (HAProxy + Kong)
- **애플리케이션 서버**: 4코어 8GB × 3대
- **데이터베이스 서버**: 4코어 8GB × 2대
- **캐시 서버**: 2코어 4GB (Redis)

---

## 3. 데이터베이스 구조

### 3.1. 핵심 테이블 설계

#### users 테이블 (사용자 관리)
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    email_verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### services 테이블 (사용자 서비스/사이트)
```sql
CREATE TABLE services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    domain VARCHAR(255) UNIQUE,
    plan_type ENUM('basic', 'premium', 'enterprise') DEFAULT 'basic',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    disk_usage BIGINT DEFAULT 0,
    disk_limit BIGINT DEFAULT 1073741824, -- 1GB
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

#### templates 테이블 (템플릿 관리)
```sql
CREATE TABLE templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL, -- slider, board, gallery, faq 등
    html LONGTEXT,
    css LONGTEXT,
    components JSON,
    thumb_url VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### site_settings 테이블 (사이트 설정)
```sql
CREATE TABLE site_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    seo_title VARCHAR(255),
    seo_description TEXT,
    meta_tags JSON,
    header_script TEXT,
    footer_script TEXT,
    favicon_url VARCHAR(500),
    custom_css TEXT,
    custom_js TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id)
);
```

#### pages 테이블 (페이지 데이터)
```sql
CREATE TABLE pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content JSON, -- GrapesJS 데이터
    is_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id)
);
```

#### boards 테이블 (게시판)
```sql
CREATE TABLE boards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    type VARCHAR(50) DEFAULT 'general', -- general, notice, qna, gallery
    settings JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id)
);
```

#### posts 테이블 (게시글)
```sql
CREATE TABLE posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    board_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT,
    author_id BIGINT UNSIGNED,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (board_id) REFERENCES boards(id),
    FOREIGN KEY (author_id) REFERENCES users(id)
);
```

#### file_uploads 테이블 (파일 관리)
```sql
CREATE TABLE file_uploads (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT NOT NULL,
    file_type VARCHAR(100),
    original_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id)
);
```

### 3.2. 인덱스 최적화
```sql
-- 성능 최적화를 위한 인덱스
CREATE INDEX idx_services_user_id ON services(user_id);
CREATE INDEX idx_services_domain ON services(domain);
CREATE INDEX idx_pages_service_id ON pages(service_id);
CREATE INDEX idx_pages_slug ON pages(slug);
CREATE INDEX idx_boards_service_id ON boards(service_id);
CREATE INDEX idx_posts_board_id ON posts(board_id);
CREATE INDEX idx_file_uploads_service_id ON file_uploads(service_id);
```

---

## 4. 핵심 기능 설계

### 4.1. 템플릿 관리 시스템

#### 템플릿 타입별 구조
```json
{
  "slider": {
    "name": "미니 슬라이더 A형",
    "type": "slider",
    "html": "<div class='slider-container'>...</div>",
    "css": ".slider-container { ... }",
    "components": {
      "type": "slider",
      "settings": {
        "autoplay": true,
        "interval": 3000,
        "show_indicators": true
      },
      "slides": [
        {
          "image": "{{image_placeholder}}",
          "title": "{{title_placeholder}}",
          "description": "{{description_placeholder}}"
        }
      ]
    }
  },
  "board": {
    "name": "기본 게시판",
    "type": "board",
    "html": "<div class='board-container'>...</div>",
    "css": ".board-container { ... }",
    "components": {
      "type": "board",
      "board_id": "{{board_id_placeholder}}",
      "settings": {
        "posts_per_page": 10,
        "show_author": true,
        "show_date": true
      }
    }
  }
}
```

### 4.2. 파일 저장 전략

#### 저장 위치 비교
| 항목 | Laravel 서버 저장 | CyberPanel 사용자 저장 (권장) |
|------|------------------|-----------------------------------|
| 디스크 용량 반영 | ❌ 불가능 | ✅ 정확히 반영 |
| 용량 제한 | 서버 전체 용량 | 사용자별 할당량 |
| 업그레이드 기준 | 불명확 | ✅ 사용량 기준 |
| 확장성 | 서버 부담 증가 | 수평 확장 용이 |

#### 파일 업로드 흐름
```
사용자 업로드 → Laravel 임시 저장 → CyberPanel 계정으로 전송 → 사용자 사이트에서 사용
```

### 4.3. 빌더 시스템

#### GrapesJS 커스텀 블록
```javascript
// app/Http/Controllers/BuilderController.php
const customBlocks = {
  'slider': {
    label: '슬라이더',
    category: 'Components',
    content: {
      type: 'slider',
      slides: [],
      settings: {
        autoplay: true,
        interval: 3000
      }
    },
    attributes: {
      class: 'slider-component'
    }
  },
  'board': {
    label: '게시판',
    category: 'Components',
    content: {
      type: 'board',
      board_id: null,
      settings: {
        posts_per_page: 10
      }
    },
    attributes: {
      class: 'board-component'
    }
  }
};
```

#### 실시간 저장 시스템
```javascript
// 자동 저장 (30초마다)
setInterval(() => {
  const pageData = editor.getComponents();
  savePageData(pageData);
}, 30000);

// 수동 저장
function savePageData(data) {
  fetch('/api/pages/save', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
      service_id: currentServiceId,
      page_id: currentPageId,
      content: data
    })
  });
}
```

---

## 5. 구현 가이드

### 5.1. API 구조

#### 템플릿 관련 API
```
GET /api/templates - 템플릿 목록 조회
GET /api/templates/{id} - 템플릿 상세 조회
GET /api/templates?type=slider - 타입별 템플릿 조회
POST /api/templates - 템플릿 생성 (관리자)
PUT /api/templates/{id} - 템플릿 수정 (관리자)
DELETE /api/templates/{id} - 템플릿 삭제 (관리자)
```

#### 파일 업로드 API
```
POST /api/upload - 파일 업로드
GET /api/files/{serviceId} - 서비스별 파일 목록
DELETE /api/files/{id} - 파일 삭제
```

#### 게시판 관련 API
```
GET /api/services/{serviceId}/boards - 서비스별 게시판 목록
GET /api/boards/{boardId}/posts - 게시글 목록
POST /api/boards/{boardId}/posts - 게시글 작성
PUT /api/posts/{id} - 게시글 수정
DELETE /api/posts/{id} - 게시글 삭제
```

### 5.2. 파일 업로드 서비스

#### FTP 연결 설정
```php
// config/filesystems.php
'ftp-user-site' => [
    'driver' => 'ftp',
    'host' => env('FTP_HOST'),
    'username' => env('FTP_USERNAME'),
    'password' => env('FTP_PASSWORD'),
    'root' => '/public_html/',
    'passive' => true,
    'ssl' => false,
    'timeout' => 30,
],
```

#### 파일 업로드 처리
```php
// app/Services/FileUploadService.php
class FileUploadService
{
    public function uploadToUserSite($file, $serviceId, $path = 'assets')
    {
        // 1. 서비스 정보 조회
        $service = Service::findOrFail($serviceId);
        
        // 2. 파일명 생성
        $filename = $this->generateFilename($file);
        
        // 3. CyberPanel 계정으로 업로드
        $uploadPath = "{$path}/{$filename}";
        
        Storage::disk('ftp-user-site')->put($uploadPath, file_get_contents($file));
        
        // 4. 파일 정보 DB 저장
        $this->saveFileInfo($serviceId, $uploadPath, $file);
        
        return $uploadPath;
    }
    
    private function generateFilename($file)
    {
        return time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
    }
    
    private function saveFileInfo($serviceId, $path, $file)
    {
        FileUpload::create([
            'service_id' => $serviceId,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'file_type' => $file->getMimeType(),
            'original_name' => $file->getClientOriginalName()
        ]);
    }
}
```

### 5.3. 배포 시스템

#### 배포 서비스
```php
// app/Services/DeploymentService.php
class DeploymentService
{
    public function deploySite($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $pages = Page::where('service_id', $serviceId)->get();
        
        foreach ($pages as $page) {
            $this->generatePageFiles($page);
            $this->uploadToUserServer($service, $page);
        }
        
        $this->updateSiteSettings($service);
    }
    
    private function generatePageFiles($page)
    {
        $html = $this->renderPage($page);
        $css = $this->generateCSS($page);
        $js = $this->generateJS($page);
        
        return [
            'html' => $html,
            'css' => $css,
            'js' => $js
        ];
    }
    
    private function uploadToUserServer($service, $page)
    {
        $ftpConfig = $this->getFTPConfig($service);
        
        // FTP를 통해 파일 업로드
        $this->uploadViaFTP($ftpConfig, $page);
    }
}
```

---

## 6. 성능 최적화

### 6.1. 캐싱 전략

#### Redis 캐싱
```php
// app/Services/CacheService.php
class CacheService
{
    public function getSiteData($serviceId)
    {
        $cacheKey = "site:{$serviceId}:data";
        return Cache::remember($cacheKey, 3600, function() use ($serviceId) {
            return Service::with(['pages', 'boards', 'settings'])->find($serviceId);
        });
    }
    
    public function getTemplateData($templateId)
    {
        $cacheKey = "template:{$templateId}";
        return Cache::remember($cacheKey, 7200, function() use ($templateId) {
            return Template::find($templateId);
        });
    }
}
```

#### CDN 캐싱
```php
// 정적 파일 캐싱 헤더
public function setCacheHeaders($response)
{
    return $response->header('Cache-Control', 'public, max-age=31536000')
                   ->header('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 31536000));
}
```

### 6.2. 데이터베이스 최적화

#### 읽기/쓰기 분리
```php
// config/database.php
'mysql' => [
    'read' => [
        'host' => [
            '192.168.1.20',
            '192.168.1.21',
        ],
    ],
    'write' => [
        'host' => '192.168.1.22',
    ],
],
```

#### 쿼리 최적화
```php
// N+1 문제 해결
$services = Service::with(['pages', 'boards', 'settings'])->get();

// 인덱스 활용
$posts = Post::where('board_id', $boardId)
             ->where('status', 'published')
             ->orderBy('created_at', 'desc')
             ->paginate(10);
```

### 6.3. 로드밸런서 설정

#### HAProxy 설정
```haproxy
# /etc/haproxy/haproxy.cfg
global
    log /dev/log local0
    log /dev/log local1 notice
    chroot /var/lib/haproxy
    stats socket /run/haproxy/admin.sock mode 660 level admin expose-fd listeners
    stats timeout 30s
    user haproxy
    group haproxy
    daemon

defaults
    log     global
    mode    http
    option  httplog
    option  dontlognull
    timeout connect 5000
    timeout client  50000
    timeout server  50000

frontend http_front
    bind *:80
    bind *:443 ssl crt /etc/ssl/certs/your-domain.pem
    redirect scheme https if !{ ssl_fc }
    
    # 헬스체크
    option httpchk GET /health
    
    # 라우팅 규칙
    acl is_builder path_beg /builder
    acl is_api path_beg /api
    acl is_static path_end .css .js .png .jpg .gif .ico
    
    use_backend builder_servers if is_builder
    use_backend api_servers if is_api
    use_backend static_servers if is_static
    default_backend web_servers

backend web_servers
    balance roundrobin
    server web1 192.168.1.10:8000 check
    server web2 192.168.1.11:8000 check
    server web3 192.168.1.12:8000 check

backend api_servers
    balance roundrobin
    server api1 192.168.1.20:8000 check
    server api2 192.168.1.21:8000 check

backend builder_servers
    balance roundrobin
    server builder1 192.168.1.30:8000 check
    server builder2 192.168.1.31:8000 check

backend static_servers
    balance roundrobin
    server static1 192.168.1.40:80 check
    server static2 192.168.1.41:80 check
```

---

## 7. 배포 및 확장

### 7.1. Vultr 서버 구성

#### 초기 단일 서버 구성
```bash
# 서버 사양
- OS: Ubuntu 20.04 LTS
- CPU: 8코어
- RAM: 16GB
- Storage: 160GB SSD
- Network: 1Gbps

# 설치 패키지
- Nginx
- PHP 8.1 + FPM
- MySQL 8.0
- Redis
- Node.js 18.x
- Git
```

#### 확장 시 서버 구성
```bash
# 로드밸런서 서버 (2코어 4GB)
- HAProxy
- Kong API Gateway
- SSL 인증서 관리

# 애플리케이션 서버 (4코어 8GB × 3대)
- Laravel 애플리케이션
- Nginx
- PHP-FPM

# 데이터베이스 서버 (4코어 8GB × 2대)
- MySQL 마스터-슬레이브
- 백업 시스템

# 캐시 서버 (2코어 4GB)
- Redis 클러스터
- 세션 관리
```

### 7.2. 자동 확장 스크립트

#### Vultr Auto-scaling
```bash
#!/bin/bash
# auto-scale.sh

# CPU 사용률 체크
CPU_USAGE=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | cut -d'%' -f1)

if [ $CPU_USAGE -gt 80 ]; then
    echo "CPU usage is high: $CPU_USAGE%"
    
    # 새 서버 생성
    curl -X POST "https://api.vultr.com/v2/instances" \
        -H "Authorization: Bearer $VULTR_API_KEY" \
        -H "Content-Type: application/json" \
        -d '{
            "region": "nrt",
            "plan": "vc2-2c-2gb",
            "os_id": 1743,
            "label": "auto-scaled-server"
        }'
    
    # 로드밸런서에 새 서버 추가
    # (Vultr API 사용)
fi
```

### 7.3. 모니터링 시스템

#### 성능 모니터링 도구
```bash
# 시스템 모니터링
htop          # CPU, 메모리 사용률
iotop         # 디스크 I/O
nethogs       # 네트워크 사용량
df -h         # 디스크 사용량

# 로그 모니터링
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
tail -f /var/log/mysql/error.log
```

---

## 8. 개발 로드맵

### 8.1. 1단계: 핵심 구조 구축 (4-6주)
- [ ] 데이터베이스 스키마 설계 및 구현
- [ ] 기본 Laravel API 구조 구축
- [ ] 사용자 인증 시스템 구현
- [ ] CyberPanel API 연동
- [ ] 기본 템플릿 관리 시스템

### 8.2. 2단계: 빌더 시스템 개발 (6-8주)
- [ ] GrapesJS 통합 및 커스터마이징
- [ ] 템플릿 시스템 구현
- [ ] 실시간 저장 기능
- [ ] 파일 업로드 시스템
- [ ] 기본 UI/UX 구현

### 8.3. 3단계: 고급 기능 구현 (8-10주)
- [ ] 게시판 시스템
- [ ] 회원 관리 시스템
- [ ] SEO 관리 기능
- [ ] 배포 시스템
- [ ] 통계 및 분석 기능

### 8.4. 4단계: 성능 최적화 (4-6주)
- [ ] 캐싱 시스템 구현
- [ ] CDN 연동
- [ ] 데이터베이스 최적화
- [ ] 로드밸런서 설정
- [ ] 보안 강화

### 8.5. 5단계: 확장 및 개선 (6-8주)
- [ ] 마이크로서비스 분리
- [ ] 자동 확장 시스템
- [ ] 고급 모니터링
- [ ] 백업 및 복구 시스템
- [ ] 문서화 및 테스트

---

## 🎯 핵심 성공 요소

### 1. 아키텍처 설계
- ✅ 중앙 집중식 데이터 관리
- ✅ 사용자별 디스크 용량 정확한 관리
- ✅ 확장 가능한 마이크로서비스 구조

### 2. 성능 최적화
- ✅ 다층 캐싱 시스템 (Redis + CDN)
- ✅ 로드밸런싱 및 자동 확장
- ✅ 데이터베이스 최적화

### 3. 사용자 경험
- ✅ 실시간 빌더 및 미리보기
- ✅ 직관적인 템플릿 시스템
- ✅ 원클릭 배포

### 4. 운영 관리
- ✅ 포괄적인 모니터링 시스템
- ✅ 자동 백업 및 복구
- ✅ 보안 강화

이 설계 문서는 완전한 SaaS 웹 빌더 시스템을 구축하기 위한 모든 핵심 요소를 포함하고 있습니다. 각 단계별로 체계적으로 구현하시면 성공적인 플랫폼을 구축할 수 있습니다!
