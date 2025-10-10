# Livewire ADJUST.md - 반복 실수 방지

## 🚨 드래그 앤 드롭 구현 시 절대 원칙

### ❌ 절대 금지: 직접 구현하지 마세요

**실수 사례 (2025-10-10)**:
- Alpine.js + HTML5 Drag API로 직접 구현 시도
- 200줄 이상의 복잡한 코드 작성
- 다음과 같은 오류 반복 발생:
  - `kanbancolumn is not defined`
  - `isover is not defined`
  - `Uncaught TypeError: Cannot read properties of undefined (reading 'call')`
  - Alpine.js 로딩 타이밍 이슈
  - `@this` 스코프 이슈
  - `Livewire.find()` 초기화 타이밍 이슈
- 7번 이상의 수정 시도 끝에 실패

**올바른 방법**:
```bash
# 1. 검증된 라이브러리 사용 (SortableJS 권장)
# - 35K+ GitHub stars
# - 활발한 유지보수
# - Livewire 호환 확인됨
```

### ✅ SortableJS 표준 구현 패턴

**1. Layout에 CDN 추가** (`resources/views/300-layout-common/000-app.blade.php`):
```php
<head>
    <!-- 다른 스크립트보다 먼저 로드 -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
</head>
```

**2. Blade 템플릿 구조** (`resources/views/700-page-xxx/000-index.blade.php`):
```php
<!-- 드래그 가능한 컨테이너 -->
<div class="kanban-column" data-column-id="{{ $columnId }}">
    @foreach ($items as $item)
        <!-- 드래그 가능한 아이템 -->
        <div class="kanban-card"
             draggable="true"
             data-task-id="{{ $item['id'] }}">
            {{ $item['title'] }}
        </div>
    @endforeach
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', function() {
        document.querySelectorAll('.kanban-column').forEach(column => {
            new Sortable(column, {
                group: 'kanban',
                animation: 150,
                ghostClass: 'opacity-50',
                onEnd: function(evt) {
                    const taskId = evt.item.dataset.taskId;
                    const fromColumn = evt.from.dataset.columnId;
                    const toColumn = evt.to.dataset.columnId;

                    if (fromColumn === toColumn) return;

                    @this.call('moveTask', parseInt(taskId), fromColumn, toColumn);
                }
            });
        });
        console.log('SortableJS initialized');
    });
</script>
@endpush
```

**3. Livewire 컴포넌트** (`app/Livewire/Xxx/Livewire.php`):
```php
public function moveTask($taskId, $fromColumn, $toColumn)
{
    // Service 호출만 허용 - DB 직접 접근 금지!
    $service = new \App\Services\Xxx\Service();
    $service->moveTask($taskId, $fromColumn, $toColumn);

    // 데이터 다시 로드
    $this->loadData();
}
```

### 🔍 디버깅 체크리스트

SortableJS가 작동하지 않을 때:

1. **CDN 로드 확인**
```bash
curl -s http://127.0.0.1:2100/xxx | grep "Sortable.min.js"
```

2. **초기화 로그 확인**
```bash
curl -s http://127.0.0.1:2100/xxx | grep "SortableJS initialized"
```

3. **데이터 속성 확인**
```bash
curl -s http://127.0.0.1:2100/xxx | grep -E "(data-task-id|data-column-id)"
```

4. **Livewire 이벤트 확인**
```bash
curl -s http://127.0.0.1:2100/xxx | grep "livewire:init"
```

### ⚠️ 흔한 실수 패턴

#### 실수 1: Alpine.js와 혼용
```php
<!-- ❌ 금지 -->
<div x-data="kanbancolumn()" class="kanban-column">

<!-- ✅ 허용 -->
<div class="kanban-column" data-column-id="planning">
```

#### 실수 2: HTML5 Drag API 직접 사용
```javascript
// ❌ 금지 - 타이밍 이슈, 복잡한 상태 관리
element.addEventListener('dragstart', ...)
element.addEventListener('dragover', ...)
element.addEventListener('drop', ...)

// ✅ 허용 - 라이브러리에 위임
new Sortable(element, { ... })
```

#### 실수 3: @this 스코프 문제
```javascript
// ❌ 금지 - 함수 스코프에서 @this 접근 불가
function handleDrop() {
    @this.call('moveTask', ...) // ❌ 작동 안함
}

// ✅ 허용 - SortableJS 콜백에서 직접 호출
onEnd: function(evt) {
    @this.call('moveTask', ...) // ✅ 정상 작동
}
```

### 📊 성능 비교

| 구현 방식 | 코드 라인 수 | 오류 발생 | 유지보수성 |
|-----------|--------------|-----------|------------|
| 직접 구현 (Alpine.js + HTML5 Drag API) | 200+ | 매우 높음 | 낮음 |
| SortableJS | 30 | 없음 | 높음 |

### 🎯 핵심 교훈

1. **검증된 라이브러리 우선**: 바퀴를 재발명하지 마세요
2. **Livewire 호환성 확인**: `livewire:init` 이벤트 사용 필수
3. **단순함 유지**: 복잡한 상태 관리는 라이브러리에 위임
4. **테스트 주도**: E2E 테스트로 검증 (`php artisan test`)

### 🔗 참고 자료

- SortableJS 공식 문서: https://github.com/SortableJS/Sortable
- Livewire 이벤트 문서: https://livewire.laravel.com/docs/events
- 이 이슈 해결 과정: 2025-10-10 칸반 보드 구현 (7번의 실패 후 성공)
