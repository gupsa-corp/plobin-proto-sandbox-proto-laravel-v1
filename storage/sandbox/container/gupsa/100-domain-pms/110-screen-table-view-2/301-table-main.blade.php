<!-- 인라인 편집 스타일 -->
<style>
.editable-field {
    transition: background-color 0.2s ease;
    padding: 2px 4px;
    border-radius: 4px;
    min-height: 1.2em;
    position: relative;
}

.editable-field:hover {
    background-color: #f3f4f6;
    outline: 1px solid #d1d5db;
}

.editable-field.editing {
    background-color: #ffffff;
    outline: none;
}

.editable-select {
    transition: opacity 0.2s ease;
}

.editable-checkbox {
    transition: transform 0.1s ease;
}

.editable-checkbox:hover {
    transform: scale(1.1);
}

/* 편집 모드 시각적 표시 */
.editing::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    animation: editing-pulse 1.5s infinite;
}

@keyframes editing-pulse {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 0.4; }
}

/* 호버 시 편집 가능 표시 */
.editable-field::after {
    content: '✎';
    position: absolute;
    right: -16px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    font-size: 12px;
    color: #6b7280;
    transition: opacity 0.2s ease;
}

.editable-field:hover::after {
    opacity: 0.6;
}

.editable-field.editing::after {
    display: none;
}
</style>

<!-- 메인 테이블 컨테이너 -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden"
     x-data="tableMainData()" x-init="initTable()">

    <?php include __DIR__ . '/302-table-header.blade.php'; ?>

    <?php include __DIR__ . '/303-table-body.blade.php'; ?>

    <?php include __DIR__ . '/304-table-pagination.blade.php'; ?>

    <?php include __DIR__ . '/305-sidebar-project-edit.blade.php'; ?>
</div>

<?php include __DIR__ . '/306-table-scripts.blade.php'; ?>
