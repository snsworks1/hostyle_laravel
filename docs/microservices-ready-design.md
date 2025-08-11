# 🏗️ 마이크로서비스 준비 SaaS 웹 빌더 시스템 설계

## 📋 목차
1. [마이크로서비스 아키텍처 개요](#1-마이크로서비스-아키텍처-개요)
2. [서비스 분리 전략](#2-서비스-분리-전략)
3. [모듈화된 데이터베이스 설계](#3-모듈화된-데이터베이스-설계)
4. [API 게이트웨이 설계](#4-api-게이트웨이-설계)
5. [서비스별 상세 설계](#5-서비스별-상세-설계)
6. [분산 시스템 구현 가이드](#6-분산-시스템-구현-가이드)
7. [확장 로드맵](#7-확장-로드맵)

---

## 1. 마이크로서비스 아키텍처 개요

### 1.1. 전체 시스템 구조 (분산 환경)
```
┌─────────────────────────────────────────────────────────────────┐
│                        클라이언트 레이어                        │
├─────────────────┬─────────────────┬─────────────────────────────┤
│   사용자 브라우저  │   관리자 브라우저  │         모바일 앱         │
└─────────┬───────┴─────────┬───────┴─────────┬─────────────────┘
          │                 │                 │
          └─────────────────┼─────────────────┘
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
              │  (라우팅/인증/제한)        │
              └─────────────┬─────────────┘
                           │
    ┌──────────────────────┼──────────────────────┐
    │                      │                      │
┌───▼────┐  ┌─────────────▼─────────────┐  ┌────▼────┐
│ 인증    │  │      웹 빌더 서비스        │  │  결제    │
│ 서비스  │  │  (대시보드/빌더/템플릿)     │  │  서비스  │
└───┬────┘  └─────────────┬─────────────┘  └────┬────┘
    │                     │                     │
    └─────────────────────┼─────────────────────┘
                          │
              ┌───────────▼───────────┐
              │    콘텐츠 관리 서비스   │
              │  (게시판/회원/파일)    │
              └───────────┬───────────┘
                          │
              ┌───────────▼───────────┐
              │    배포 관리 서비스    │
              │  (CyberPanel 연동)    │
              └───────────┬───────────┘
                          │
    ┌─────────────────────┼─────────────────────┐
    │                     │                     │
┌───▼────┐  ┌────────────▼────────────┐  ┌────▼────┐
│ 사용자  │  │      통계 분석 서비스    │  │  알림    │
│ DB     │  │   (트래픽/성능/사용량)   │  │  서비스  │
└───┬────┘  └────────────┬────────────┘  └────┬────┘
    │                    │                    │
    └────────────────────┼────────────────────┘
                         │
              ┌──────────▼───────────┐
              │    Redis 클러스터     │
              │   (캐싱/세션/큐)     │
              └──────────────────────┘
```

### 1.2. 서비스 분리 원칙
- **단일 책임 원칙**: 각 서비스는 하나의 명확한 비즈니스 기능 담당
- **독립성**: 서비스 간 최소한의 의존성
- **확장성**: 개별 서비스의 독립적 확장 가능
- **데이터 격리**: 서비스별 독립적인 데이터베이스

---

## 2. 서비스 분리 전략

### 2.1. 서비스별 분리 계획

#### Phase 1: 모놀리식 → 모듈화 (초기)
```
┌─────────────────────────────────────┐
│         Laravel 모놀리식            │
├─────────────────────────────────────┤
│ - 인증/권한 관리                   │
│ - 웹 빌더 (대시보드/빌더/템플릿)    │
│ - 콘텐츠 관리 (게시판/회원/파일)    │
│ - 배포 관리 (CyberPanel 연동)       │
│ - 결제/구독 관리                   │
│ - 통계/분석                        │
└─────────────────────────────────────┘
```

#### Phase 2: 핵심 서비스 분리
```
┌─────────────┐  ┌─────────────┐  ┌─────────────┐
│   인증      │  │   웹 빌더   │  │   콘텐츠    │
│   서비스    │  │   서비스    │  │   서비스    │
└─────────────┘  └─────────────┘  └─────────────┘
       │                │                │
       └────────────────┼────────────────┘
                        │
              ┌─────────▼─────────┐
              │   API Gateway     │
              └─────────┬─────────┘
                        │
              ┌─────────▼─────────┐
              │   공유 데이터베이스 │
              └────────────────────┘
```

#### Phase 3: 완전 분리 (최종)
```
┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐
│   인증      │  │   웹 빌더   │  │   콘텐츠    │  │   배포      │
│   서비스    │  │   서비스    │  │   서비스    │  │   서비스    │
│   (Auth)    │  │  (Builder)  │  │ (Content)  │  │ (Deploy)   │
└─────────────┘  └─────────────┘  └─────────────┘  └─────────────┘
       │                │                │                │
       └────────────────┼────────────────┼────────────────┘
                        │                │
              ┌─────────▼─────────┐  ┌───▼────┐
              │   API Gateway     │  │ 결제   │
              │   (Kong)          │  │ 서비스 │
              └─────────┬─────────┘  └───┬────┘
                        │                │
    ┌───────────────────┼────────────────┼───────────────────┐
    │                   │                │                   │
┌───▼────┐  ┌──────────▼──────────┐  ┌──▼────┐  ┌─────────▼─────────┐
│ 인증    │  │      콘텐츠 DB      │  │ 결제   │  │    통계 분석      │
│ DB     │  │   (게시판/회원/파일) │  │ DB    │  │     서비스        │
└───┬────┘  └──────────┬──────────┘  └──┬────┘  └─────────┬─────────┘
    │                  │                 │                 │
    └──────────────────┼─────────────────┼─────────────────┘
                       │                 │
              ┌────────▼────────┐  ┌────▼────┐
              │   Redis 클러스터 │  │  알림    │
              │  (캐싱/세션/큐) │  │  서비스  │
              └─────────────────┘  └─────────┘
```

### 2.2. 서비스별 책임 분담

#### 🔐 인증 서비스 (Auth Service)
**책임:**
- 사용자 인증/인가
- JWT 토큰 관리
- 세션 관리
- 권한 관리

**API 엔드포인트:**
```
POST /auth/login
POST /auth/register
POST /auth/logout
POST /auth/refresh
GET  /auth/profile
PUT  /auth/profile
```

#### 🎨 웹 빌더 서비스 (Builder Service)
**책임:**
- GrapesJS 빌더 관리
- 템플릿 관리
- 페이지 데이터 관리
- 실시간 저장

**API 엔드포인트:**
```
GET    /builder/templates
GET    /builder/templates/{id}
POST   /builder/pages
PUT    /builder/pages/{id}
DELETE /builder/pages/{id}
GET    /builder/pages/{id}/preview
```

#### 📝 콘텐츠 관리 서비스 (Content Service)
**책임:**
- 게시판 관리
- 게시글 관리
- 회원 관리
- 파일 업로드

**API 엔드포인트:**
```
GET    /content/boards
POST   /content/boards
GET    /content/boards/{id}/posts
POST   /content/boards/{id}/posts
GET    /content/members
POST   /content/upload
```

#### 🚀 배포 관리 서비스 (Deploy Service)
**책임:**
- CyberPanel 연동
- 파일 배포
- 도메인 관리
- SSL 인증서 관리

**API 엔드포인트:**
```
POST /deploy/site/{serviceId}
GET  /deploy/status/{serviceId}
POST /deploy/domain
POST /deploy/ssl
```

#### 💳 결제 서비스 (Payment Service)
**책임:**
- 구독 관리
- 결제 처리
- 요금제 관리
- 사용량 추적

**API 엔드포인트:**
```
GET    /payment/subscriptions
POST   /payment/subscribe
PUT    /payment/subscriptions/{id}
GET    /payment/usage/{serviceId}
```

#### 📊 통계 분석 서비스 (Analytics Service)
**책임:**
- 트래픽 분석
- 성능 모니터링
- 사용량 통계
- 리포트 생성

**API 엔드포인트:**
```
GET /analytics/traffic/{serviceId}
GET /analytics/performance/{serviceId}
GET /analytics/usage/{serviceId}
GET /analytics/reports
```

---

## 3. 모듈화된 데이터베이스 설계

### 3.1. 서비스별 데이터베이스 분리

#### 🔐 인증 서비스 DB (auth_db)
```sql
-- users 테이블
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

-- user_sessions 테이블
CREATE TABLE user_sessions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- permissions 테이블
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- user_permissions 테이블
CREATE TABLE user_permissions (
    user_id BIGINT UNSIGNED NOT NULL,
    permission_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, permission_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id)
);
```

#### 🎨 웹 빌더 서비스 DB (builder_db)
```sql
-- templates 테이블
CREATE TABLE templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    html LONGTEXT,
    css LONGTEXT,
    components JSON,
    thumb_url VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- pages 테이블
CREATE TABLE pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content JSON,
    is_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- site_settings 테이블
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 📝 콘텐츠 관리 서비스 DB (content_db)
```sql
-- boards 테이블
CREATE TABLE boards (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    type VARCHAR(50) DEFAULT 'general',
    settings JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- posts 테이블
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
    FOREIGN KEY (board_id) REFERENCES boards(id)
);

-- members 테이블
CREATE TABLE members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- file_uploads 테이블
CREATE TABLE file_uploads (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT NOT NULL,
    file_type VARCHAR(100),
    original_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 💳 결제 서비스 DB (payment_db)
```sql
-- subscriptions 테이블
CREATE TABLE subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    plan_type ENUM('basic', 'premium', 'enterprise') NOT NULL,
    status ENUM('active', 'cancelled', 'expired') DEFAULT 'active',
    start_date TIMESTAMP NOT NULL,
    end_date TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- payments 테이블
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subscription_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'KRW',
    payment_method VARCHAR(50),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id)
);

-- usage_logs 테이블
CREATE TABLE usage_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    disk_usage BIGINT DEFAULT 0,
    bandwidth_usage BIGINT DEFAULT 0,
    page_views INT DEFAULT 0,
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 3.2. 서비스 간 데이터 동기화

#### 이벤트 기반 동기화
```php
// app/Events/UserCreated.php
class UserCreated
{
    public $user;
    
    public function __construct($user)
    {
        $this->user = $user;
    }
}

// app/Listeners/SyncUserToOtherServices.php
class SyncUserToOtherServices
{
    public function handle(UserCreated $event)
    {
        // 다른 서비스에 사용자 정보 동기화
        $this->syncToContentService($event->user);
        $this->syncToPaymentService($event->user);
    }
    
    private function syncToContentService($user)
    {
        Http::post(config('services.content.url') . '/api/sync/user', [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]);
    }
}
```

---

## 4. API 게이트웨이 설계

### 4.1. Kong API Gateway 설정

#### docker-compose.yml
```yaml
version: '3.8'

services:
  kong:
    image: kong:latest
    environment:
      KONG_DATABASE: postgres
      KONG_PG_HOST: kong-database
      KONG_PG_USER: kong
      KONG_PG_PASSWORD: kong
      KONG_PROXY_ACCESS_LOG: /dev/stdout
      KONG_ADMIN_ACCESS_LOG: /dev/stdout
      KONG_PROXY_ERROR_LOG: /dev/stderr
      KONG_ADMIN_ERROR_LOG: /dev/stderr
      KONG_ADMIN_LISTEN: 0.0.0.0:8001
      KONG_ADMIN_GUI_URL: http://localhost:8002
    ports:
      - "8000:8000"
      - "8443:8443"
      - "8001:8001"
      - "8444:8444"
    depends_on:
      - kong-database
    networks:
      - kong-net

  kong-database:
    image: postgres:13
    environment:
      POSTGRES_USER: kong
      POSTGRES_DB: kong
      POSTGRES_PASSWORD: kong
    volumes:
      - kong-data:/var/lib/postgresql/data
    networks:
      - kong-net

volumes:
  kong-data:

networks:
  kong-net:
    driver: bridge
```

#### 서비스 등록 스크립트
```bash
#!/bin/bash
# register-services.sh

# 인증 서비스 등록
curl -X POST http://localhost:8001/services \
  -d name=auth-service \
  -d url=http://auth-service:8000

curl -X POST http://localhost:8001/services/auth-service/routes \
  -d name=auth-routes \
  -d paths[]=/auth

# 웹 빌더 서비스 등록
curl -X POST http://localhost:8001/services \
  -d name=builder-service \
  -d url=http://builder-service:8000

curl -X POST http://localhost:8001/services/builder-service/routes \
  -d name=builder-routes \
  -d paths[]=/builder

# 콘텐츠 서비스 등록
curl -X POST http://localhost:8001/services \
  -d name=content-service \
  -d url=http://content-service:8000

curl -X POST http://localhost:8001/services/content-service/routes \
  -d name=content-routes \
  -d paths[]=/content

# 배포 서비스 등록
curl -X POST http://localhost:8001/services \
  -d name=deploy-service \
  -d url=http://deploy-service:8000

curl -X POST http://localhost:8001/services/deploy-service/routes \
  -d name=deploy-routes \
  -d paths[]=/deploy

# 결제 서비스 등록
curl -X POST http://localhost:8001/services \
  -d name=payment-service \
  -d url=http://payment-service:8000

curl -X POST http://localhost:8001/services/payment-service/routes \
  -d name=payment-routes \
  -d paths[]=/payment

# 통계 서비스 등록
curl -X POST http://localhost:8001/services \
  -d name=analytics-service \
  -d url=http://analytics-service:8000

curl -X POST http://localhost:8001/services/analytics-service/routes \
  -d name=analytics-routes \
  -d paths[]=/analytics
```

### 4.2. 인증 플러그인 설정
```bash
# JWT 인증 플러그인 활성화
curl -X POST http://localhost:8001/plugins \
  -d name=jwt \
  -d config.secret=your-secret-key

# Rate Limiting 플러그인
curl -X POST http://localhost:8001/plugins \
  -d name=rate-limiting \
  -d config.minute=100 \
  -d config.hour=1000
```

---

## 5. 서비스별 상세 설계

### 5.1. 인증 서비스 (Auth Service)

#### 서비스 구조
```
auth-service/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   └── UserController.php
│   │   └── Middleware/
│   │       └── JwtMiddleware.php
│   ├── Services/
│   │   ├── JwtService.php
│   │   └── UserService.php
│   └── Events/
│       └── UserCreated.php
├── config/
│   └── auth.php
└── routes/
    └── api.php
```

#### JWT 서비스
```php
// app/Services/JwtService.php
class JwtService
{
    public function generateToken($user)
    {
        $payload = [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'exp' => time() + (60 * 60 * 24), // 24시간
            'iat' => time()
        ];
        
        return JWT::encode($payload, config('auth.jwt_secret'), 'HS256');
    }
    
    public function validateToken($token)
    {
        try {
            $payload = JWT::decode($token, config('auth.jwt_secret'), ['HS256']);
            return $payload;
        } catch (Exception $e) {
            return false;
        }
    }
}
```

### 5.2. 웹 빌더 서비스 (Builder Service)

#### 서비스 구조
```
builder-service/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── TemplateController.php
│   │   │   ├── PageController.php
│   │   │   └── BuilderController.php
│   │   └── Middleware/
│   │       └── AuthMiddleware.php
│   ├── Services/
│   │   ├── TemplateService.php
│   │   ├── PageService.php
│   │   └── GrapesJSService.php
│   └── Events/
│       └── PageSaved.php
├── config/
│   └── builder.php
└── routes/
    └── api.php
```

#### 템플릿 서비스
```php
// app/Services/TemplateService.php
class TemplateService
{
    public function getTemplates($type = null)
    {
        $query = Template::where('is_active', true);
        
        if ($type) {
            $query->where('type', $type);
        }
        
        return $query->get();
    }
    
    public function createTemplate($data)
    {
        return Template::create([
            'name' => $data['name'],
            'type' => $data['type'],
            'html' => $data['html'],
            'css' => $data['css'],
            'components' => json_encode($data['components']),
            'thumb_url' => $data['thumb_url'] ?? null
        ]);
    }
}
```

### 5.3. 콘텐츠 관리 서비스 (Content Service)

#### 서비스 구조
```
content-service/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── BoardController.php
│   │   │   ├── PostController.php
│   │   │   ├── MemberController.php
│   │   │   └── FileController.php
│   │   └── Middleware/
│   │       └── AuthMiddleware.php
│   ├── Services/
│   │   ├── BoardService.php
│   │   ├── PostService.php
│   │   ├── MemberService.php
│   │   └── FileUploadService.php
│   └── Events/
│       └── PostCreated.php
├── config/
│   └── content.php
└── routes/
    └── api.php
```

#### 파일 업로드 서비스
```php
// app/Services/FileUploadService.php
class FileUploadService
{
    public function uploadToUserSite($file, $serviceId, $path = 'assets')
    {
        // 1. 서비스 정보 조회 (API 호출)
        $service = $this->getServiceInfo($serviceId);
        
        // 2. 파일명 생성
        $filename = $this->generateFilename($file);
        
        // 3. CyberPanel 계정으로 업로드
        $uploadPath = "{$path}/{$filename}";
        
        $this->uploadViaFTP($service, $uploadPath, $file);
        
        // 4. 파일 정보 DB 저장
        $this->saveFileInfo($serviceId, $uploadPath, $file);
        
        return $uploadPath;
    }
    
    private function getServiceInfo($serviceId)
    {
        // 다른 서비스에서 서비스 정보 조회
        $response = Http::get(config('services.payment.url') . "/api/services/{$serviceId}");
        return $response->json();
    }
}
```

---

## 6. 분산 시스템 구현 가이드

### 6.1. 서비스 간 통신

#### HTTP 클라이언트 설정
```php
// config/services.php
return [
    'auth' => [
        'url' => env('AUTH_SERVICE_URL', 'http://auth-service:8000'),
        'timeout' => 30,
    ],
    'builder' => [
        'url' => env('BUILDER_SERVICE_URL', 'http://builder-service:8000'),
        'timeout' => 30,
    ],
    'content' => [
        'url' => env('CONTENT_SERVICE_URL', 'http://content-service:8000'),
        'timeout' => 30,
    ],
    'deploy' => [
        'url' => env('DEPLOY_SERVICE_URL', 'http://deploy-service:8000'),
        'timeout' => 30,
    ],
    'payment' => [
        'url' => env('PAYMENT_SERVICE_URL', 'http://payment-service:8000'),
        'timeout' => 30,
    ],
    'analytics' => [
        'url' => env('ANALYTICS_SERVICE_URL', 'http://analytics-service:8000'),
        'timeout' => 30,
    ],
];
```

#### 서비스 간 API 호출
```php
// app/Services/ServiceCommunication.php
class ServiceCommunication
{
    public function getUserInfo($userId)
    {
        $response = Http::timeout(10)
            ->get(config('services.auth.url') . "/api/users/{$userId}");
        
        if ($response->successful()) {
            return $response->json();
        }
        
        throw new Exception('Failed to get user info');
    }
    
    public function getServiceInfo($serviceId)
    {
        $response = Http::timeout(10)
            ->get(config('services.payment.url') . "/api/services/{$serviceId}");
        
        if ($response->successful()) {
            return $response->json();
        }
        
        throw new Exception('Failed to get service info');
    }
}
```

### 6.2. 이벤트 기반 통신

#### 이벤트 발행
```php
// app/Events/UserCreated.php
class UserCreated
{
    public $user;
    
    public function __construct($user)
    {
        $this->user = $user;
    }
}

// app/Listeners/SyncUserToOtherServices.php
class SyncUserToOtherServices
{
    public function handle(UserCreated $event)
    {
        // 다른 서비스에 사용자 정보 동기화
        $this->syncToContentService($event->user);
        $this->syncToPaymentService($event->user);
    }
    
    private function syncToContentService($user)
    {
        Http::post(config('services.content.url') . '/api/sync/user', [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]);
    }
}
```

### 6.3. 분산 트랜잭션 관리

#### Saga 패턴 구현
```php
// app/Services/SagaOrchestrator.php
class SagaOrchestrator
{
    public function createService($userData, $serviceData)
    {
        try {
            // 1. 사용자 생성
            $user = $this->createUser($userData);
            
            // 2. 서비스 생성
            $service = $this->createService($serviceData, $user->id);
            
            // 3. 초기 설정 생성
            $this->createInitialSettings($service->id);
            
            // 4. 결제 정보 생성
            $this->createPaymentInfo($service->id);
            
            return $service;
            
        } catch (Exception $e) {
            // 롤백 처리
            $this->rollback($user->id ?? null, $service->id ?? null);
            throw $e;
        }
    }
    
    private function rollback($userId, $serviceId)
    {
        if ($serviceId) {
            Http::delete(config('services.payment.url') . "/api/services/{$serviceId}");
        }
        
        if ($userId) {
            Http::delete(config('services.auth.url') . "/api/users/{$userId}");
        }
    }
}
```

---

## 7. 확장 로드맵

### 7.1. Phase 1: 모놀리식 → 모듈화 (4-6주)
- [ ] 기존 Laravel 앱을 모듈별로 분리
- [ ] 공통 라이브러리 추출
- [ ] API 게이트웨이 도입
- [ ] 서비스 간 통신 구현

### 7.2. Phase 2: 핵심 서비스 분리 (6-8주)
- [ ] 인증 서비스 분리
- [ ] 웹 빌더 서비스 분리
- [ ] 콘텐츠 관리 서비스 분리
- [ ] 데이터베이스 분리

### 7.3. Phase 3: 완전 분리 (8-10주)
- [ ] 배포 서비스 분리
- [ ] 결제 서비스 분리
- [ ] 통계 분석 서비스 분리
- [ ] 알림 서비스 분리

### 7.4. Phase 4: 최적화 (4-6주)
- [ ] 서비스별 독립적 확장
- [ ] 캐싱 전략 최적화
- [ ] 모니터링 시스템 구축
- [ ] 자동 복구 시스템

### 7.5. Phase 5: 고급 기능 (6-8주)
- [ ] 이벤트 소싱 도입
- [ ] CQRS 패턴 적용
- [ ] 분산 트랜잭션 최적화
- [ ] 보안 강화

---

## 🎯 핵심 성공 요소

### 1. 모듈화 설계
- ✅ 서비스별 독립적인 데이터베이스
- ✅ 명확한 서비스 경계
- ✅ 이벤트 기반 통신

### 2. 확장성
- ✅ 개별 서비스 독립적 확장
- ✅ 로드밸런서를 통한 트래픽 분산
- ✅ 자동 확장 시스템

### 3. 안정성
- ✅ 서비스 간 격리
- ✅ 장애 전파 방지
- ✅ 자동 복구 시스템

### 4. 개발 효율성
- ✅ 팀별 독립적 개발
- ✅ 기술 스택 자유도
- ✅ 배포 자동화

이 설계는 마이크로서비스 분리를 고려한 모듈화된 구조로, 서버 분산 시 편리하게 분리할 수 있도록 설계되었습니다. 각 단계별로 점진적으로 분리하여 안정적인 확장이 가능합니다!
