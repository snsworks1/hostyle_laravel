# 🚀 SaaS 웹 빌더 시스템: 완전 설계 가이드

## 📋 목차
1. [시스템 개요](#1-시스템-개요)
2. [아키텍처 설계](#2-아키텍처-설계)
   - 2.1. [모놀리식 아키텍처](#21-모놀리식-아키텍처)
   - 2.2. [마이크로서비스 아키텍처](#22-마이크로서비스-아키텍처)
3. [데이터베이스 구조](#3-데이터베이스-구조)
4. [핵심 기능 설계](#4-핵심-기능-설계)
5. [빌더 시스템 상세 설계](#5-빌더-시스템-상세-설계)
6. [구현 가이드](#6-구현-가이드)
7. [성능 최적화](#7-성능-최적화)
8. [배포 및 확장](#8-배포-및-확장)
9. [개발 로드맵](#9-개발-로드맵)

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

### 2.1. 모놀리식 아키텍처

#### 전체 시스템 구조
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

### 2.2. 마이크로서비스 아키텍처

#### 전체 시스템 구조 (분산 환경)
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

---

## 3. 핵심 기능 설계

### 3.1. 템플릿 관리 시스템

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

### 3.2. 파일 저장 전략

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

---

## 4. 빌더 시스템 상세 설계

### 4.1. 커스텀 UI 구조 (권장 방식)

#### 🔧 Trait 패널 or Modal 방식으로 설정창 구성
GrapesJS는 trait 또는 custom property panel, modal을 사용해 설정 UI 구성 가능

**Modal 방식 예시:**
```javascript
editor.Modal.open({
  title: '슬라이드 설정',
  content: `
    <div>
      <label>슬라이드 이미지</label>
      <input type="file" multiple />
      <label>슬라이드 개수</label>
      <input type="range" min="1" max="10" />
      <label>페이드 효과</label>
      <input type="checkbox" />
    </div>
  `,
  buttons: [
    { text: '적용', action: () => applySliderConfig() }
  ]
});
```

#### 🎛 설정값 저장 구조
슬라이드, 게시판, 텍스트박스 등 컴포넌트별 설정값은 component.set('traits') 또는 attributes 또는 script 내에 JSON 형식으로 저장

```json
{
  "type": "slider",
  "images": ["img1.jpg", "img2.jpg"],
  "count": 5,
  "height": 680,
  "fade": true,
  "autoplay": false
}
```

#### 💾 저장 시 Laravel API로 구조 저장
```javascript
const html = editor.getHtml();
const css = editor.getCss();
const components = editor.getComponents();

axios.post('/api/page/save', {
  service_id: currentServiceId,
  html,
  css,
  components: JSON.stringify(components),
});
```

### 4.2. 게시판 컴포넌트 구조 제안

#### 사용자 선택으로 게시판 컴포넌트 추가
- 게시판 타입: 리스트형 / FAQ형 / 댓글형 / 간편형
- 게시판 설정값은 trait으로 관리
- 게시판 데이터는 Laravel API를 통해 자동 로딩

**HTML 구조:**
```html
<div data-type="board" data-board-id="5" data-style="faq"></div>
```

**렌더링 시:**
```php
// 게시판 ID = 5인 게시글 목록 불러오기
$posts = Post::where('board_id', 5)->latest()->get();
```

### 4.3. 전체 컴포넌트 설계 구조 예시

| 컴포넌트 | 설명 | 저장형식 |
|----------|------|----------|
| 슬라이더 | 이미지 목록, 옵션, 버튼 스타일 | JSON (component.set('slider-options', {})) |
| 텍스트 | 폰트, 정렬, 효과 | HTML + trait 속성 |
| 버튼 | 링크, 색상, 둥글기 | trait |
| 게시판 | 게시판 ID + 옵션 | data-* 속성 또는 JSON |

### 4.4. 커스텀 블록 정의

#### 기본 커스텀 블록 구조
```javascript
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

#### 고급 커스텀 블록 (Modal + Trait 조합)
```javascript
// 슬라이더 컴포넌트 정의
editor.BlockManager.add('slider', {
  label: '슬라이더',
  category: 'Components',
  content: {
    type: 'slider',
    slides: [],
    settings: {
      autoplay: true,
      interval: 3000,
      show_indicators: true
    }
  },
  attributes: {
    class: 'slider-component'
  },
  // 컴포넌트 선택 시 Modal 열기
  onSelect: function(component) {
    openSliderModal(component);
  }
});

// 슬라이더 설정 Modal
function openSliderModal(component) {
  editor.Modal.open({
    title: '슬라이더 설정',
    content: `
      <div class="slider-settings">
        <div class="setting-group">
          <label>슬라이드 이미지</label>
          <input type="file" multiple id="slider-images" />
        </div>
        <div class="setting-group">
          <label>슬라이드 개수</label>
          <input type="range" min="1" max="10" id="slide-count" />
        </div>
        <div class="setting-group">
          <label>자동 재생</label>
          <input type="checkbox" id="autoplay" />
        </div>
        <div class="setting-group">
          <label>페이드 효과</label>
          <input type="checkbox" id="fade-effect" />
        </div>
      </div>
    `,
    buttons: [
      { text: '취소', action: 'close' },
      { text: '적용', action: () => applySliderSettings(component) }
    ]
  });
}

// 설정 적용 함수
function applySliderSettings(component) {
  const images = document.getElementById('slider-images').files;
  const count = document.getElementById('slide-count').value;
  const autoplay = document.getElementById('autoplay').checked;
  const fade = document.getElementById('fade-effect').checked;
  
  // 컴포넌트에 설정 저장
  component.set('slider-options', {
    images: Array.from(images).map(f => f.name),
    count: parseInt(count),
    autoplay: autoplay,
    fade: fade
  });
  
  editor.Modal.close();
}
```

### 4.5. 실시간 저장 시스템

#### 자동 저장 (30초마다)
```javascript
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

### 4.6. ✨ 커스터마이징 팁

- **GrapesJS 기본 블록 외에 커스텀 블록 템플릿 등록 가능**
- **컴포넌트마다 trait + modal을 조합해 복잡한 UI도 구성 가능**
- **builder 내에서 사용자 편의를 위한 설정 UI는 대부분 editor.Modal 또는 traitManager 커스터마이징으로 가능**

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

## 9. 🎯 핵심 성공 요소

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

### 5. 🔚 결론
✅ 지금 구성 중인 빌더 시스템은 충분히 상용 웹빌더 수준의 커스터마이징 UI 구현이 가능합니다.
GrapesJS + Laravel API 조합으로 "블록 기반 UI + 설정 모달 + 동적 콘텐츠 연동 + 사용자 저장"을 완벽하게 구현할 수 있습니다.

이 설계 문서는 완전한 SaaS 웹 빌더 시스템을 구축하기 위한 모든 핵심 요소를 포함하고 있습니다. 각 단계별로 체계적으로 구현하시면 성공적인 플랫폼을 구축할 수 있습니다!
