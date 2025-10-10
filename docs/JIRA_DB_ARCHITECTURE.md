# 지라 티켓 시스템 - 데이터베이스 아키텍처

## 📊 ERD 다이어그램

```
┌─────────────────────────────────────────────────────────────────────┐
│                          Core Entities                              │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────────────┐
│  plobin_users        │
├──────────────────────┤
│ id (PK)              │
│ name                 │
│ email (UNIQUE)       │
│ role                 │◄────────┐
│ department           │         │
│ is_active            │         │
│ last_login_at        │         │
└──────────────────────┘         │
                                 │
                                 │ uploaded_by
                                 │
┌──────────────────────────────┐ │
│  plobin_uploaded_files       │ │
├──────────────────────────────┤ │
│ id (PK)                      │ │
│ original_name                │ │
│ stored_name                  │ │
│ file_path                    │ │
│ mime_type                    │ │
│ file_size                    │ │
│ status                       │ │
│ uploaded_by (FK) ────────────┘
│ tags (JSON)                  │
│ description                  │
│ download_count               │
│ analyzed_at                  │
└──────────────────────────────┘
        │                   │
        │                   │
        │                   └────────────────────────┐
        │ file_id                                    │ uploaded_file_id
        │                                            │
        ▼                                            ▼
┌──────────────────────────────┐     ┌──────────────────────────────────────┐
│  plobin_document_analyses    │     │ plobin_analysis_request_files        │
├──────────────────────────────┤     ├──────────────────────────────────────┤
│ id (PK)                      │     │ id (PK)                              │
│ file_id (FK)                 │     │ analysis_request_id (FK)             │
│ request_id (FK)              │◄────│ uploaded_file_id (FK)                │
│ status                       │     │                                      │
│ summary                      │     │ UNIQUE(analysis_request_id,          │
│ keywords (JSON)              │     │        uploaded_file_id)             │
│ categories (JSON)            │     └──────────────────────────────────────┘
│ confidence_score             │                  │
│ extracted_data (JSON)        │                  │
│ recommendations (JSON)       │                  │
│ document_type                │                  │
│ keyword_count                │                  │ analysis_request_id
│ page_count                   │                  │
│ error_message                │                  │
│ analyzed_at                  │                  ▼
└──────────────────────────────┘     ┌──────────────────────────────┐
                                     │ plobin_analysis_requests     │
                                     ├──────────────────────────────┤
                                     │ id (PK)                      │
                   ┌─────────────────┤ requester_id (FK)            │
                   │                 │ assignee_id (FK)             │
                   │                 │ title                        │
            requester_id/assignee_id │ description                  │
                   │                 │ status                       │
                   │                 │ priority                     │
                   │                 │ required_by                  │
                   └────────────────►│ estimated_hours              │
                    (plobin_users)   │ completed_percentage         │
                                     │ completed_at                 │
                                     │ cancelled_at                 │
                                     │ cancel_reason                │
                                     └──────────────────────────────┘
```

## 🗂️ 테이블 상세 설명

### 1. plobin_users (사용자 관리)
**목적**: 시스템 사용자 정보 저장

| 컬럼명 | 타입 | 제약조건 | 설명 |
|--------|------|----------|------|
| id | BIGINT | PK | 사용자 고유 ID |
| name | VARCHAR | NOT NULL | 사용자 이름 |
| email | VARCHAR | UNIQUE, NOT NULL | 이메일 (로그인 ID) |
| role | VARCHAR | DEFAULT 'analyst' | 역할: analyst, reviewer, manager, admin |
| department | VARCHAR | NULLABLE | 부서 |
| is_active | BOOLEAN | DEFAULT true | 활성 상태 |
| last_login_at | TIMESTAMP | NULLABLE | 마지막 로그인 시각 |
| created_at | TIMESTAMP | NOT NULL | 생성일 |
| updated_at | TIMESTAMP | NOT NULL | 수정일 |

**인덱스**:
- PRIMARY KEY: `id`
- UNIQUE KEY: `email`

**역할 설명**:
- `analyst`: 분석 요청 생성, 파일 업로드
- `reviewer`: 분석 결과 검토
- `manager`: 요청 할당, 우선순위 관리
- `admin`: 시스템 전체 관리

---

### 2. plobin_uploaded_files (파일 업로드 관리)
**목적**: 업로드된 파일의 메타데이터 및 상태 관리

| 컬럼명 | 타입 | 제약조건 | 설명 |
|--------|------|----------|------|
| id | BIGINT | PK | 파일 고유 ID |
| original_name | VARCHAR | NOT NULL | 원본 파일명 |
| stored_name | VARCHAR | NOT NULL | 저장된 파일명 (UUID 기반) |
| file_path | VARCHAR | NOT NULL | 파일 저장 경로 |
| mime_type | VARCHAR | NOT NULL | MIME 타입 (application/pdf 등) |
| file_size | BIGINT UNSIGNED | NOT NULL | 파일 크기 (bytes) |
| status | VARCHAR | DEFAULT 'uploaded' | 상태: uploaded, analyzing, completed, error |
| uploaded_by | BIGINT | FK → plobin_users.id | 업로드한 사용자 |
| tags | JSON | NULLABLE | 파일 태그 (검색용) |
| description | TEXT | NULLABLE | 파일 설명 |
| download_count | INT UNSIGNED | DEFAULT 0 | 다운로드 횟수 |
| analyzed_at | TIMESTAMP | NULLABLE | 분석 완료 시각 |
| created_at | TIMESTAMP | NOT NULL | 업로드일 |
| updated_at | TIMESTAMP | NOT NULL | 수정일 |

**인덱스**:
- PRIMARY KEY: `id`
- INDEX: `(status, created_at)` - 상태별 최신 파일 조회
- INDEX: `uploaded_by` - 사용자별 업로드 파일 조회

**상태 흐름**:
```
uploaded → analyzing → completed
                    ↘ error
```

---

### 3. plobin_analysis_requests (분석 요청 티켓)
**목적**: 지라와 유사한 티켓 시스템, 분석 작업 요청 관리

| 컬럼명 | 타입 | 제약조건 | 설명 |
|--------|------|----------|------|
| id | BIGINT | PK | 요청 고유 ID (티켓 번호) |
| title | VARCHAR | NOT NULL | 티켓 제목 |
| description | TEXT | NOT NULL | 티켓 상세 설명 |
| status | VARCHAR | DEFAULT 'pending' | 상태: pending, in_progress, completed, cancelled |
| priority | VARCHAR | DEFAULT 'medium' | 우선순위: low, medium, high, urgent |
| requester_id | BIGINT | FK → plobin_users.id | 요청자 |
| assignee_id | BIGINT | FK → plobin_users.id | 담당자 |
| required_by | DATE | NULLABLE | 요청 완료 기한 |
| estimated_hours | INT UNSIGNED | NULLABLE | 예상 소요 시간 |
| completed_percentage | INT UNSIGNED | DEFAULT 0 | 완료율 (0-100) |
| completed_at | TIMESTAMP | NULLABLE | 완료 시각 |
| cancelled_at | TIMESTAMP | NULLABLE | 취소 시각 |
| cancel_reason | TEXT | NULLABLE | 취소 사유 |
| created_at | TIMESTAMP | NOT NULL | 생성일 |
| updated_at | TIMESTAMP | NOT NULL | 수정일 |

**인덱스**:
- PRIMARY KEY: `id`
- INDEX: `(status, priority)` - 상태/우선순위별 조회
- INDEX: `requester_id` - 요청자별 티켓 조회
- INDEX: `assignee_id` - 담당자별 티켓 조회

**상태 흐름**:
```
pending → in_progress → completed
   ↓
cancelled
```

**우선순위 정의**:
- `urgent`: 즉시 처리 필요 (1일 이내)
- `high`: 높은 우선순위 (3일 이내)
- `medium`: 보통 우선순위 (7일 이내)
- `low`: 낮은 우선순위 (14일 이내)

---

### 4. plobin_analysis_request_files (요청-파일 연결)
**목적**: 다대다 관계 - 하나의 요청에 여러 파일 첨부

| 컬럼명 | 타입 | 제약조건 | 설명 |
|--------|------|----------|------|
| id | BIGINT | PK | 연결 고유 ID |
| analysis_request_id | BIGINT | FK → plobin_analysis_requests.id | 분석 요청 ID |
| uploaded_file_id | BIGINT | FK → plobin_uploaded_files.id | 파일 ID |
| created_at | TIMESTAMP | NOT NULL | 연결 생성일 |
| updated_at | TIMESTAMP | NOT NULL | 수정일 |

**인덱스**:
- PRIMARY KEY: `id`
- UNIQUE KEY: `(analysis_request_id, uploaded_file_id)` - 중복 방지

**제약조건**:
- ON DELETE CASCADE: 요청 삭제 시 연결 자동 삭제
- ON DELETE CASCADE: 파일 삭제 시 연결 자동 삭제

---

### 5. plobin_document_analyses (AI 분석 결과)
**목적**: AI 기반 문서 분석 결과 저장

| 컬럼명 | 타입 | 제약조건 | 설명 |
|--------|------|----------|------|
| id | BIGINT | PK | 분석 결과 고유 ID |
| file_id | BIGINT | FK → plobin_uploaded_files.id | 분석 대상 파일 |
| request_id | BIGINT | FK → plobin_analysis_requests.id | 연결된 요청 (선택) |
| status | VARCHAR | DEFAULT 'pending' | 상태: pending, analyzing, completed, error |
| summary | TEXT | NULLABLE | AI 생성 요약 |
| keywords | JSON | NULLABLE | 추출된 키워드 배열 |
| categories | JSON | NULLABLE | 문서 분류 카테고리 |
| confidence_score | DECIMAL(5,2) | NULLABLE | 신뢰도 점수 (0.00-100.00) |
| extracted_data | JSON | NULLABLE | 구조화된 데이터 추출 |
| recommendations | JSON | NULLABLE | AI 추천 사항 |
| document_type | VARCHAR | NULLABLE | 문서 유형 (계약서, 보고서 등) |
| keyword_count | INT UNSIGNED | NULLABLE | 키워드 개수 |
| page_count | INT UNSIGNED | NULLABLE | 페이지 수 |
| error_message | TEXT | NULLABLE | 오류 메시지 |
| analyzed_at | TIMESTAMP | NULLABLE | 분석 완료 시각 |
| created_at | TIMESTAMP | NOT NULL | 생성일 |
| updated_at | TIMESTAMP | NOT NULL | 수정일 |

**인덱스**:
- PRIMARY KEY: `id`
- INDEX: `file_id` - 파일별 분석 조회
- INDEX: `request_id` - 요청별 분석 조회
- INDEX: `(status, analyzed_at)` - 상태별 분석 이력 조회

**JSON 필드 구조 예시**:
```json
{
  "keywords": ["계약", "금액", "기한", "당사자"],
  "categories": ["법률", "계약서", "매매계약"],
  "extracted_data": {
    "contract_amount": "1,000,000원",
    "parties": ["갑", "을"],
    "effective_date": "2025-01-01"
  },
  "recommendations": [
    "법무팀 검토 필요",
    "계약 금액 재확인 권장"
  ]
}
```

---

## 🔗 관계 다이어그램 요약

### 1:N 관계
- `plobin_users` 1 → N `plobin_uploaded_files` (uploaded_by)
- `plobin_users` 1 → N `plobin_analysis_requests` (requester_id)
- `plobin_users` 1 → N `plobin_analysis_requests` (assignee_id)
- `plobin_uploaded_files` 1 → N `plobin_document_analyses` (file_id)
- `plobin_analysis_requests` 1 → N `plobin_document_analyses` (request_id)

### N:M 관계 (중간 테이블 사용)
- `plobin_analysis_requests` N ↔ M `plobin_uploaded_files`
  - 중간 테이블: `plobin_analysis_request_files`
  - 하나의 요청에 여러 파일, 하나의 파일이 여러 요청에 속할 수 있음

---

## 🎯 주요 비즈니스 로직

### 티켓 생성 워크플로우
```
1. 사용자 (requester)가 분석 요청 생성
   → plobin_analysis_requests 레코드 생성

2. 파일 업로드
   → plobin_uploaded_files 레코드 생성

3. 요청-파일 연결
   → plobin_analysis_request_files 레코드 생성

4. 담당자 (assignee) 할당
   → plobin_analysis_requests.assignee_id 업데이트

5. 상태를 'in_progress'로 변경
   → plobin_analysis_requests.status 업데이트
```

### AI 분석 워크플로우
```
1. 파일 업로드 완료
   → plobin_uploaded_files.status = 'uploaded'

2. AI 분석 큐 등록
   → plobin_document_analyses 레코드 생성 (status = 'pending')

3. AI 분석 실행
   → plobin_uploaded_files.status = 'analyzing'
   → plobin_document_analyses.status = 'analyzing'

4. 분석 완료
   → plobin_uploaded_files.status = 'completed'
   → plobin_document_analyses.status = 'completed'
   → 분석 결과 JSON 저장
   → analyzed_at 타임스탬프 기록
```

### 요청 완료 워크플로우
```
1. 담당자가 분석 완료 처리
   → plobin_analysis_requests.status = 'completed'
   → plobin_analysis_requests.completed_at = NOW()
   → plobin_analysis_requests.completed_percentage = 100

2. 관련 파일 분석 상태 확인
   → plobin_document_analyses 전체 완료 여부 검증
```

---

## 📈 주요 쿼리 패턴

### 1. 내 담당 티켓 조회 (Assignee Dashboard)
```sql
SELECT ar.*, u.name as requester_name, COUNT(arf.id) as file_count
FROM plobin_analysis_requests ar
JOIN plobin_users u ON ar.requester_id = u.id
LEFT JOIN plobin_analysis_request_files arf ON ar.id = arf.analysis_request_id
WHERE ar.assignee_id = :user_id
  AND ar.status IN ('pending', 'in_progress')
GROUP BY ar.id
ORDER BY
  FIELD(ar.priority, 'urgent', 'high', 'medium', 'low'),
  ar.required_by ASC NULLS LAST;
```

### 2. 요청별 파일 및 분석 결과 조회
```sql
SELECT
  uf.original_name,
  uf.file_size,
  uf.mime_type,
  da.summary,
  da.keywords,
  da.confidence_score,
  da.analyzed_at
FROM plobin_analysis_request_files arf
JOIN plobin_uploaded_files uf ON arf.uploaded_file_id = uf.id
LEFT JOIN plobin_document_analyses da ON uf.id = da.file_id
WHERE arf.analysis_request_id = :request_id;
```

### 3. 사용자별 업로드 현황 통계
```sql
SELECT
  u.name,
  u.department,
  COUNT(uf.id) as total_files,
  SUM(uf.file_size) as total_size,
  COUNT(CASE WHEN uf.status = 'completed' THEN 1 END) as analyzed_files
FROM plobin_users u
LEFT JOIN plobin_uploaded_files uf ON u.id = uf.uploaded_by
GROUP BY u.id;
```

### 4. 우선순위별 티켓 대시보드
```sql
SELECT
  priority,
  status,
  COUNT(*) as count,
  AVG(completed_percentage) as avg_progress
FROM plobin_analysis_requests
WHERE status != 'cancelled'
GROUP BY priority, status
ORDER BY FIELD(priority, 'urgent', 'high', 'medium', 'low'), status;
```

---

## 🔐 권한 및 접근 제어

### 역할별 권한 매트릭스

| 작업 | analyst | reviewer | manager | admin |
|------|---------|----------|---------|-------|
| 티켓 생성 | ✅ | ✅ | ✅ | ✅ |
| 파일 업로드 | ✅ (자신) | ✅ (자신) | ✅ | ✅ |
| 티켓 할당 | ❌ | ❌ | ✅ | ✅ |
| 상태 변경 | ❌ | ✅ (담당 티켓) | ✅ | ✅ |
| 티켓 취소 | ✅ (자신) | ❌ | ✅ | ✅ |
| 사용자 관리 | ❌ | ❌ | ❌ | ✅ |
| 전체 통계 조회 | ❌ | ❌ | ✅ | ✅ |

---

## 📊 성능 최적화 전략

### 인덱스 전략
1. **복합 인덱스**: `(status, priority)`, `(status, created_at)` - 필터링 + 정렬
2. **외래 키 인덱스**: 모든 FK에 인덱스 자동 생성 - 조인 성능
3. **유니크 인덱스**: 중복 방지 + 조회 속도 향상

### 파티셔닝 고려사항 (향후)
- `plobin_uploaded_files`: `created_at` 기준 월별 파티셔닝
- `plobin_document_analyses`: `analyzed_at` 기준 월별 파티셔닝
- 1년 이상 된 데이터는 아카이브 테이블로 이동

### 캐싱 전략
- 사용자별 대시보드: Redis 캐싱 (TTL 5분)
- 통계 데이터: 일별 집계 테이블 생성 (배치 작업)
- 파일 메타데이터: CDN 헤더 캐싱

---

## 🚨 데이터 무결성 규칙

### 1. Cascade 규칙
- `plobin_analysis_request_files`: 요청/파일 삭제 시 연결 자동 삭제
- `plobin_document_analyses`: 파일 삭제 시 분석 결과는 **유지** (RESTRICT)

### 2. 상태 전환 제약
```php
// Service 레이어에서 구현
$allowed_transitions = [
    'pending' => ['in_progress', 'cancelled'],
    'in_progress' => ['completed', 'cancelled'],
    'completed' => [], // 완료 후 변경 불가
    'cancelled' => [], // 취소 후 변경 불가
];
```

### 3. 논리적 삭제 (Soft Delete) 고려
- 티켓 취소: `deleted_at` 컬럼 추가 대신 `cancelled_at` 사용
- 사용자 비활성화: `is_active` 플래그 사용

---

## 📝 마이그레이션 순서

```bash
# 순서 엄수 필요 (외래 키 의존성)
1. 2025_10_09_173905_create_plobin_users_table.php
2. 2025_10_09_173847_create_plobin_uploaded_files_table.php
3. 2025_10_09_173853_create_plobin_analysis_requests_table.php
4. 2025_10_09_174006_create_plobin_analysis_request_files_table.php
5. 2025_10_09_173900_create_plobin_document_analyses_table.php
```

**실행 명령어**:
```bash
php artisan migrate
php artisan db:seed --class=PlobinUserSeeder
```

---

## 🔄 향후 확장 계획

### Phase 2: 협업 기능
- `plobin_request_comments`: 티켓 댓글 시스템
- `plobin_request_history`: 상태 변경 이력 추적
- `plobin_notifications`: 실시간 알림

### Phase 3: 고급 분석
- `plobin_analysis_templates`: 분석 템플릿 관리
- `plobin_custom_fields`: 커스텀 필드 정의
- `plobin_workflows`: 자동화 워크플로우

### Phase 4: 통계 및 리포팅
- `plobin_daily_stats`: 일별 집계 데이터
- `plobin_performance_metrics`: 성능 지표
- `plobin_audit_logs`: 감사 로그

---

**문서 버전**: 1.0
**작성일**: 2025-10-10
**최종 수정일**: 2025-10-10
