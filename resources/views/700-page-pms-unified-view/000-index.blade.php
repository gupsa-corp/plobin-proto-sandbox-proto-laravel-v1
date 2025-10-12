<div class="p-6 bg-gray-50 min-h-screen">
    <!-- 헤더 -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">PMS 통합 관리</h1>
        <p class="text-gray-600 mt-2">프로젝트를 다양한 방식으로 관리하고 시각화하세요</p>
    </div>

    <!-- 탭 네비게이션 -->
    @include('700-page-pms-unified-view.100-tab-navigation')

    <!-- 탭 컨텐츠 -->
    <div class="mt-6">
        @if($currentTab === 'projects')
            @include('700-page-pms-unified-view.200-projects-tab')
        @elseif($currentTab === 'calendar')
            @include('700-page-pms-unified-view.201-calendar-tab')
        @elseif($currentTab === 'kanban')
            @include('700-page-pms-unified-view.202-kanban-tab')
        @elseif($currentTab === 'gantt')
            @include('700-page-pms-unified-view.203-gantt-tab')
        @elseif($currentTab === 'table')
            @include('700-page-pms-unified-view.204-table-tab')
        @endif
    </div>
</div>
