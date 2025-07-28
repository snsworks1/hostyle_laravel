# 🚀 Hostyle CMS (CyberPanel + Central DB/API)

## 프로젝트 개요

**Hostyle CMS**는 오픈소스 기반의 CyberPanel과 중앙 API 서버(Laravel + Vue.js)를 결합해  
누구나 손쉽게 웹사이트, CMS, 게시판, 페이지, 회원 등 주요 웹기능을  
드래그앤드롭 빌더로 제작·관리하고,  
실제 서비스는 각 유저의 CyberPanel 계정(public_html)으로 배포하는  
차세대 멀티사이트 통합관리 플랫폼입니다.

---

## 🏗️ 시스템 구조

- **중앙 서버**:  
  - Laravel + Vue.js 대시보드/빌더/에디터  
  - GrapesJS(페이지), Tiptap/Editor.js(게시글)  
  - 모든 데이터/설정/파일 중앙DB(MySQL/MariaDB)에 저장  
  - RESTful API 제공 (JWT 인증)

- **사용자 서버 (CyberPanel)**:  
  - 계정별 public_html (`/home/{username}/public_html/`)  
  - SFTP/SSH/API로 배포  
  - `index.php`, `board_view.php`, `uploads/`, `assets/` 등 자동 push

- **API 기반 연동**:  
  - 사용자 서버의 PHP파일이 중앙 API로 데이터 실시간 조회  
  - 또는 정적 HTML/PHP만 배포해 API 없는 사이트도 지원

---

## 📦 주요 기능/흐름

1. **중앙 관리**  
   - 모든 사이트/페이지/게시판/회원 등 통합 관리 및 통계/백업
2. **빌더/에디터**  
   - 페이지/게시글/레이아웃을 GrapesJS, Tiptap 등으로 시각적 제작
3. **배포/게시**  
   - “배포” 클릭 시 중앙 서버→SFTP/SSH로 사용자 CyberPanel public_html로 업로드
4. **실서비스**  
   - 방문자 요청시 index.php 등에서 중앙 API로 실시간 데이터 로드 또는 정적 파일 서비스

---

## 🧩 주요 메뉴/관리기능

- **사이트 설정**: 기본정보, 이용약관, 도메인, 파비콘, 언어 등
- **SEO/소셜**: robot, sitemap, 메타태그, 구글/네이버/카카오 연동
- **회원 관리**: 리스트, 가입템플릿, 포인트
- **게시판/게시글/폼/일정**: 생성·관리, 답변, 폼, 일정
- **통계/모니터링**: 방문기록, 접속통계, 유입분석
- **서버 관리**: PHP버전, DB비번, 백업/복원, SSL, 파일관리, DB관리자
- **플랜/결제**: 플랜변경, 결제내역, 쿠폰
- **테마/에셋 관리**: (WordPress 모드 지원)

---

## 🌐 전체 데이터/서비스 흐름



## 🛠️ API 구조 및 서비스 흐름 (CyberPanel 기반)

### 개요
Hostyle CMS는 중앙 API 서버(Laravel)와 사용자별 CyberPanel public_html을 연동하여  
모든 데이터/컨텐츠/설정/배포를 RESTful API로 통합 관리합니다.

---

### API 아키텍처

- 모든 데이터/서비스/컨텐츠는 중앙 API(`/api/v1/*`)를 통해 접근·관리
- 관리자 대시보드, 빌더, 사용자 public_html의 PHP 파일, 외부앱 모두 API 기반으로 통신
- 인증: JWT 또는 API Key, 권한별 분리

---

### 주요 API 엔드포인트

| METHOD | ENDPOINT | 설명 |
| ------ | -------- | ---- |
| POST   | /api/v1/auth/login | 로그인 |
| GET    | /api/v1/services | 내 사이트 리스트 |
| POST   | /api/v1/services | 신규 사이트 생성 |
| POST   | /api/v1/services/{id}/deploy | 빌더 결과물 public_html 배포(SFTP/SSH) |
| GET    | /api/v1/services/{id}/pages | 페이지 리스트 조회 |
| POST   | /api/v1/services/{id}/pages | 새 페이지 생성 (빌더 저장) |
| GET    | /api/v1/pages/{id} | 페이지 상세(실시간 컨텐츠 API) |
| GET    | /api/v1/services/{id}/boards | 게시판 리스트 |
| GET    | /api/v1/boards/{board_id}/posts | 게시글 리스트 |
| POST   | /api/v1/boards/{board_id}/posts | 게시글 작성 |
| POST   | /api/v1/cyberpanel/create-account | CyberPanel 계정 자동 생성 |
| POST   | /api/v1/cyberpanel/backup | 계정/DB 백업 요청 |
| GET    | /api/v1/services/{id}/stats/visits | 방문/트래픽 통계 |
| GET    | /api/v1/services/{id}/settings | 사이트 설정 조회/수정 |

> 전체 엔드포인트는 `/docs` 또는 Notion API 문서 참고

---

### 서비스 흐름

1. **중앙 대시보드/빌더/에디터**  
   → 모든 데이터 생성/수정/관리를 **API**로 처리
2. **배포/게시**  
   → 중앙 API가 빌더 결과물을 SFTP/SSH로 사용자의 CyberPanel `public_html`에 자동 업로드
3. **사용자 웹사이트**  
   → 방문자 접속시 `index.php` 등에서 **중앙 API로 실시간 데이터 로딩**  
   → 또는 정적 HTML/PHP 결과물만 서비스
4. **CyberPanel 계정/DB/SSL 등 관리**  
   → 중앙 API에서 자동 생성/백업/설정 연동
