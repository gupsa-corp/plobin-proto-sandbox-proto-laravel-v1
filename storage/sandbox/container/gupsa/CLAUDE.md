# Gupsa Sandbox 구조 가이드

## 기본 정보
- **컨테이너명**: gupsa
- **표시명**: Gupsa Sandbox
- **기본 경로**: `/sandbox/container/gupsa`
- **메타데이터**: `containers.json`에서 동적 관리

## 현재 도메인 구조

### 100-domain-pms (PMS Management)
현재 구현된 스크린:
- **101-screen-dashboard** (Dashboard)
- **106-screen-calendar-view** (Calendar View)

### 101-domain-rfx (RFX Management)
현재 구현된 스크린:
- **110-screen-form-demo** (Form Demo)

## containers.json 연동

이 샌드박스는 `containers.json` 파일을 통해 동적으로 관리됩니다:
- 도메인 목록: API를 통해 동적 로드
- 스크린 목록: 메타데이터에서 자동 생성
- 하드코딩 제거: 모든 구조 정보는 containers.json 기반

## 공통 폴더 구조 (100-common)

각 도메인의 `100-common` 폴더는 다음과 같은 표준 구조를 따릅니다:

### 000-Config (설정 및 공통 연결 중심)
- `000-common.php` - 모든 연결의 중심점, 도메인별 공통 설정
- `002-common.php` - 추가 공통 설정 파일
- 기타 설정 파일들

### 100번대: Controller 계층
- **100-Controllers** - API 엔드포인트 및 컨트롤러 로직
  - 구조: `{도메인폴더명}/Controller.php` (예: DocumentAnalysis/Controller.php)
  - 요청 검증: `{도메인폴더명}/Request.php` (예: DocumentAnalysis/Request.php)
- **102-Services** - 비즈니스 로직 서비스 클래스
  - 구조: `{도메인폴더명}/Service.php` (예: DocumentAnalysis/Service.php)
- **103-Routes** - 라우팅 설정 파일 (api.php 등)

### 200번대: Database 계층
- **200-Database**
  - `release.sqlite` - 도메인별 SQLite 데이터베이스 파일
  - `Models/` - 데이터 모델 클래스
  - `Migration/` - 스키마 마이그레이션 파일

### 400번대: Storage 계층
- **400-Storage**
  - `uploads/` - 업로드된 파일 저장소
  - `versions/` - 버전 관리 파일
  - `downloads/` - 다운로드 파일 저장소

## 경로 구조 규칙

### 프론트엔드 파일 경로
- **기본 구조**: `{숫자}-domain-{도메인명}/{숫자}-screen-{화면명}/파일명.blade.php`
- **예시**: `100-domain-pms/103-screen-table-view/000-content.blade.php`
- **중요**: `/frontend/` 중간 경로는 사용하지 않음

### 백엔드 데이터베이스 경로
- **구조**: `{도메인폴더}/100-common/200-Database/release.sqlite`
- **예시**: `100-domain-pms/100-common/200-Database/release.sqlite`
- **특징**: 각 도메인별로 독립적인 SQLite 데이터베이스 사용

### Include 경로 패턴
- **올바른 예시**: `storage_path('sandbox/{$sandbox}/100-domain-pms/101-screen-dashboard/파일명.blade.php')`
- **동적 경로 생성**: containers.json 기반으로 경로 자동 생성
- **금지된 패턴**:
  - `/frontend/` 경로 사용
  - 하드코딩된 도메인/스크린명 직접 사용

## 폴더명 규칙
- 모든 폴더는 `{3자리숫자}-{설명}` 형식 사용
- 관련 기능들은 같은 숫자 그룹으로 묶음 (예: 100번대는 Controller 관련)
- Laravel 표준 구조를 기반으로 하되 숫자 프리픽스로 정렬 관리

## SandboxRegistry API 연동

새로운 SandboxRegistry 서비스를 통해 메타데이터 접근:
- **GetContainers**: 모든 컨테이너 목록 조회
- **GetDomains**: 특정 컨테이너의 도메인 목록 조회
- **GetPages**: 특정 도메인의 페이지 목록 조회

**API 엔드포인트**:
```
GET /api/sandbox/containers
GET /api/sandbox/domains?sandbox={container}
GET /api/sandbox/screens?sandbox={container}&domain={domain}
```

**오류 처리**: containers.json 파일이 없거나 빈 경우 `SandboxMetadataNotFoundException` 발생
