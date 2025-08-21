# 웹 빌더 설계 구조 문서

## 📋 목차
1. [프로젝트 개요](#프로젝트-개요)
2. [파일 구조](#파일-구조)
3. [백엔드 구조](#백엔드-구조)
4. [데이터베이스 설계](#데이터베이스-설계)
5. [데이터 흐름](#데이터-흐름)
6. [핵심 기능](#핵심-기능)
7. [기술 스택](#기술-스택)
8. [API 엔드포인트](#api-엔드포인트)
9. [컴포넌트 상세](#컴포넌트-상세)
10. [설치 및 설정](#설치-및-설정)

## 🎯 프로젝트 개요

**웹 빌더**는 Laravel + Inertia.js + Vue 3 기반의 드래그 앤 드롭 웹사이트 빌더입니다. 사용자는 직관적인 인터페이스를 통해 섹션을 추가하고 편집하여 완전한 웹사이트를 구축할 수 있습니다.

### 주요 특징
- 🎨 **직관적인 UI**: 3열 레이아웃으로 편집 효율성 극대화
- 🧩 **모듈형 섹션**: Hero, Features, CTA 등 재사용 가능한 섹션
- 📱 **반응형 디자인**: 모든 디바이스에서 최적화된 레이아웃
- 🔄 **실시간 미리보기**: 변경사항 즉시 반영
- 📚 **템플릿 시스템**: 다양한 시작 템플릿 제공

## 📁 파일 구조

```
resources/js/Pages/Builder/
├── App.vue                    # 메인 빌더 레이아웃 (3열 구조)
├── Pages.vue                  # 페이지 목록 화면
├── components/                # 빌더 컴포넌트들
│   ├── Sidebar.vue           # 좌측 사이드바 (페이지 관리)
│   ├── Stage.vue             # 중앙 편집 스테이지
│   ├── InspectorPanel.vue    # 우측 속성 편집 패널
│   ├── PageTree.vue          # 페이지 트리 컴포넌트
│   ├── PageNode.vue          # 페이지 노드 컴포넌트
│   └── modals/               # 모달 컴포넌트들
│       ├── PageAddModal.vue  # 새 페이지 추가
│       ├── PopupAddModal.vue # 새 팝업 추가
│       └── HelpModal.vue     # 도움말
├── ui/                       # UI 컴포넌트들
│   ├── forms/                # 폼 컴포넌트들
│   │   ├── HeroForm.vue      # 히어로 섹션 폼
│   │   ├── FeaturesForm.vue  # 특징 섹션 폼
│   │   └── CtaForm.vue       # CTA 섹션 폼
│   └── LibraryPanel.vue      # 라이브러리 패널
└── sections/                 # 섹션 컴포넌트들
    ├── Hero.vue              # 히어로 섹션
    ├── Features.vue          # 특징 섹션
    └── Cta.vue               # CTA 섹션

resources/js/builder/
├── store.ts                  # Pinia 상태 관리
├── registry.ts               # 섹션 레지스트리
└── schema/
    └── types.ts              # TypeScript 타입 정의
```

## 🏗️ 백엔드 구조

```
app/
├── Http/Controllers/Builder/
│   ├── BuilderController.php      # 웹 라우트 처리
│   │   ├── app()                 # 빌더 앱 화면
│   │   └── pages()               # 페이지 목록 화면
│   └── BuilderApiController.php  # API 요청 처리
│       ├── index()               # 페이지 목록 조회
│       ├── show()                # 단일 페이지 조회
│       ├── save()                # 페이지 저장
│       ├── publish()             # 페이지 게시
│       └── create()              # 새 페이지 생성
├── Models/
│   ├── BuilderPage.php           # 페이지 모델
│   ├── BuilderTemplate.php       # 템플릿 모델
│   └── Server.php                # 서버 모델 (확장)
├── Services/Builder/
│   └── Renderer.php              # HTML/CSS 렌더링
│       ├── preview()             # 미리보기 HTML 생성
│       └── publish()             # 게시용 HTML 생성
└── database/migrations/
    └── create_builder_pages_table.php
```

## 🗄️ 데이터베이스 설계

### `builder_pages` 테이블
```sql
CREATE TABLE builder_pages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    server_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL DEFAULT 0,
    type VARCHAR(255) DEFAULT 'page',
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    seq INT DEFAULT 0,
    read_level VARCHAR(255) DEFAULT 'all',
    menu_hide BOOLEAN DEFAULT FALSE,
    hide_header BOOLEAN DEFAULT FALSE,
    hide_footer BOOLEAN DEFAULT FALSE,
    is_main BOOLEAN DEFAULT FALSE,
    page_schema JSON NULL,
    site_tokens JSON NULL,
    preview_html LONGTEXT NULL,
    preview_css LONGTEXT NULL,
    published_html LONGTEXT NULL,
    published_css LONGTEXT NULL,
    is_published BOOLEAN DEFAULT FALSE,
    published_at TIMESTAMP NULL,
    seo_title VARCHAR(255) NULL,
    seo_keywords TEXT NULL,
    seo_description TEXT NULL,
    seo_search_ban BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (server_id) REFERENCES servers(id) ON DELETE CASCADE
);
```

### `builder_templates` 테이블
```sql
CREATE TABLE builder_templates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    thumbnail VARCHAR(255) NULL,
    description TEXT NULL,
    page_schema JSON NOT NULL,
    site_tokens JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

## 🔄 데이터 흐름

### 1. 사용자 인증 및 접근
```
사용자 로그인 → 서버 선택 → 사이트설정 버튼 클릭 → 빌더 진입
```

### 2. 페이지 로드 프로세스
```
BuilderController@app → BuilderApiController@index → 페이지 목록 반환 → Vue 컴포넌트 렌더링
```

### 3. 섹션 편집 프로세스
```
섹션 선택 → InspectorPanel 표시 → 폼 편집 → store 업데이트 → Stage 실시간 반영
```

### 4. 저장 및 게시 프로세스
```
저장 버튼 → BuilderApiController@save → 미리보기 HTML 생성 → 데이터베이스 저장
게시 버튼 → BuilderApiController@publish → 최종 HTML 생성 → 게시 상태 변경
```

## ⚡ 핵심 기능

### 🎨 **3열 레이아웃 시스템**
- **좌측 사이드바**: 페이지 관리, 팝업, 데이터 컨텐츠
- **중앙 스테이지**: 섹션 편집 및 미리보기
- **우측 인스펙터**: 선택된 요소의 속성 편집

### 🌳 **계층적 페이지 관리**
- **페이지 트리**: 드래그 앤 드롭으로 순서 변경
- **페이지 타입**: 일반 페이지, 링크, 카테고리, 팝업, 데이터
- **SEO 관리**: 제목, 키워드, 설명, 검색 제외 설정

### 🧩 **모듈형 섹션 시스템**
- **Hero 섹션**: 메인 배너, 제목, 부제목, CTA 버튼
- **Features 섹션**: 특징 목록, 그리드 레이아웃
- **CTA 섹션**: 행동 유도, 버튼, 링크

### 🔄 **실시간 편집**
- **즉시 미리보기**: 변경사항 실시간 반영
- **드래그 앤 드롭**: 섹션 순서 변경
- **속성 편집**: 색상, 폰트, 간격 등 실시간 조정

## 🛠️ 기술 스택

### **프론트엔드**
- **Vue 3**: Composition API 기반 컴포넌트
- **TypeScript**: 타입 안전성 및 개발 효율성
- **Pinia**: 상태 관리 (페이지, 스키마, 토큰, 히스토리)
- **Tailwind CSS**: 유틸리티 퍼스트 CSS 프레임워크
- **vue-draggable-next**: 드래그 앤 드롭 기능

### **백엔드**
- **Laravel 10**: PHP 웹 프레임워크
- **Inertia.js**: SPA 경험을 위한 서버 사이드 렌더링
- **MySQL**: 관계형 데이터베이스
- **Eloquent ORM**: 데이터베이스 모델 관리

### **개발 도구**
- **Vite**: 프론트엔드 빌드 도구
- **Laravel Mix**: 에셋 컴파일
- **PHP Artisan**: 명령줄 도구

## 🌐 API 엔드포인트

### **웹 라우트**
```php
// 페이지 관리
GET  /server/{id}/builder              # 페이지 목록
GET  /server/{id}/builder/app          # 빌더 앱

// API (웹 라우트로)
GET  /server/{id}/builder/api/pages           # 페이지 목록
GET  /server/{id}/builder/api/pages/{pageId} # 단일 페이지
POST /server/{id}/builder/api/pages/{pageId} # 페이지 저장
POST /server/{id}/builder/api/pages/{pageId}/publish # 페이지 게시
POST /server/{id}/builder/api/pages          # 새 페이지 생성
```

### **인증 및 권한**
- **미들웨어**: `auth:sanctum`, `jetstream.auth_session`, `verified`
- **권한 확인**: 서버 소유자만 접근 가능
- **세션 기반**: 웹 라우트에서 세션 인증 처리

## 🧩 컴포넌트 상세

### **App.vue (메인 레이아웃)**
```vue
<template>
  <div class="builder-layout">
    <!-- 상단 헤더 -->
    <div class="builder-header">
      <div class="header-left">
        <h1>웹 빌더</h1>
        <select v-model="selectedPageId">페이지 선택</select>
      </div>
      <div class="header-right">
        <button @click="savePage">저장</button>
        <button @click="publishPage">게시</button>
      </div>
    </div>
    
    <!-- 메인 컨텐츠 -->
    <div class="builder-main">
      <Sidebar />      <!-- 좌측 -->
      <Stage />        <!-- 중앙 -->
      <InspectorPanel /> <!-- 우측 -->
    </div>
  </div>
</template>
```

### **Sidebar.vue (좌측 패널)**
```vue
<template>
  <div class="sidebar">
    <div class="sidebar-header">
      <h3>페이지 관리</h3>
    </div>
    
    <div class="sidebar-content">
      <!-- 페이지 트리 -->
      <PageTree :pages="rootPages" />
      
      <!-- 팝업 목록 -->
      <div class="popup-section">
        <h4>팝업</h4>
        <div class="popup-list">...</div>
      </div>
      
      <!-- 데이터 컨텐츠 -->
      <div class="data-section">
        <h4>데이터 컨텐츠</h4>
        <div class="data-list">...</div>
      </div>
    </div>
    
    <div class="sidebar-footer">
      <button @click="showPageModal = true">페이지 추가</button>
    </div>
  </div>
</template>
```

### **Stage.vue (중앙 편집 영역)**
```vue
<template>
  <div class="stage-container">
    <div class="stage-toolbar">
      <button @click="store.undo">실행취소</button>
      <button @click="store.redo">다시실행</button>
    </div>
    
    <div class="stage-content">
      <draggable v-model="sections" @end="onDragEnd">
        <template #item="{ element, index }">
          <div class="section-wrapper">
            <div class="section-header">
              <span class="drag-handle">⋮⋮</span>
              <span>{{ element.type }}</span>
            </div>
            <component :is="sectionMap[element.type]" v-bind="element.props" />
          </div>
        </template>
      </draggable>
    </div>
  </div>
</template>
```

### **InspectorPanel.vue (우측 속성 패널)**
```vue
<template>
  <div class="inspector-panel">
    <div class="panel-header">
      <h3>속성 편집</h3>
    </div>
    
    <div class="panel-content">
      <!-- 선택된 섹션 속성 편집 -->
      <component :is="formComponent" v-model="selectedSection.props" />
      
      <!-- 섹션 추가 버튼 -->
      <div class="add-section">
        <button v-for="section in availableSections" 
                @click="addSection(section.type)">
          {{ section.name }}
        </button>
      </div>
    </div>
  </div>
</template>
```

## 📦 설치 및 설정

### **1. 의존성 설치**
```bash
# 프론트엔드 의존성
npm install pinia vue-draggable-next @vueuse/core

# 백엔드 의존성 (이미 Laravel에 포함)
composer require laravel/sanctum
```

### **2. 마이그레이션 실행**
```bash
php artisan migrate
```

### **3. 기본 페이지 생성**
```php
// Server 모델에 자동으로 메인 페이지 생성
$server->createDefaultMainPage();
```

### **4. 프론트엔드 빌드**
```bash
npm run build
```

## 🔧 설정 파일

### **config/builder.php**
```php
<?php
return [
    'default_tokens' => [
        'brandColor' => '#111111',
        'contentWidth' => '1080px',
        'fontBase' => 'Noto Sans KR, sans-serif',
        'radius' => '12px'
    ],
    
    'available_sections' => [
        'hero' => [
            'name' => '히어로 섹션',
            'icon' => 'fas fa-star',
            'component' => 'Hero'
        ],
        'features' => [
            'name' => '특징 섹션',
            'icon' => 'fas fa-th-large',
            'component' => 'Features'
        ],
        'cta' => [
            'name' => 'CTA 섹션',
            'icon' => 'fas fa-bullhorn',
            'component' => 'Cta'
        ]
    ]
];
```

## 🚀 배포 및 운영

### **프로덕션 환경**
- **웹서버**: Nginx/Apache
- **PHP**: 8.1+ 
- **데이터베이스**: MySQL 8.0+
- **캐싱**: Redis (선택사항)

### **성능 최적화**
- **프론트엔드**: Vite 빌드 최적화
- **백엔드**: Laravel 캐싱, 쿼리 최적화
- **데이터베이스**: 인덱스 설정, 쿼리 튜닝

### **보안 고려사항**
- **CSRF 보호**: 모든 폼에 CSRF 토큰 포함
- **XSS 방지**: 사용자 입력 검증 및 이스케이프
- **권한 관리**: 서버 소유자만 접근 가능
- **SQL 인젝션 방지**: Eloquent ORM 사용

## 📚 개발 가이드

### **새 섹션 추가**
1. `resources/js/Pages/Builder/sections/`에 Vue 컴포넌트 생성
2. `resources/js/Pages/Builder/ui/forms/`에 폼 컴포넌트 생성
3. `resources/js/builder/registry.ts`에 등록
4. `config/builder.php`에 설정 추가

### **커스텀 템플릿**
1. `builder_templates` 테이블에 데이터 추가
2. `BuilderTemplate` 모델에서 템플릿 관리
3. 프론트엔드에서 템플릿 선택 UI 구현

### **확장 기능**
- **플러그인 시스템**: 섹션 플러그인 개발
- **테마 시스템**: 다크모드, 커스텀 테마
- **협업 기능**: 팀 멤버와 공유 편집
- **버전 관리**: 페이지 변경 이력 추적

---

**문서 버전**: 1.0  
**최종 업데이트**: 2025-08-21  
**작성자**: AI Assistant  
**프로젝트**: Laravel Web Builder

