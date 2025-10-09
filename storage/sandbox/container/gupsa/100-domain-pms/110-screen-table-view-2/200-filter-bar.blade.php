<!-- 필터 바 -->
<div class="bg-white rounded-lg shadow-sm p-4 mb-6" x-data="filterBarData()" x-init="init()">
    <div class="flex flex-wrap gap-4 items-center">
        <div class="flex-1 min-w-64">
            <input type="text"
                   x-model="filters.search"
                   @input="debounceSearch()"
                   placeholder="프로젝트명, 클라이언트 검색..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
        </div>
        <select x-model="filters.status"
                @change="applyFilters()"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            <option value="">모든 상태</option>
            <option value="planned">계획</option>
            <option value="in_progress">진행 중</option>
            <option value="completed">완료</option>
            <option value="on_hold">보류</option>
        </select>
        <select x-model="filters.priority"
                @change="applyFilters()"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            <option value="">모든 우선순위</option>
            <option value="high">높음</option>
            <option value="medium">보통</option>
            <option value="low">낮음</option>
        </select>
        <button @click="applyFilters()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">검색</button>
        <button x-show="hasActiveFilters()"
                @click="clearFilters()"
                class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">초기화</button>
    </div>

    <!-- 컬럼 선택 드롭다운 -->
    <div class="flex-shrink-0 mt-4">
        <?php include __DIR__ . '/201-column-selector.blade.php'; ?>
    </div>
</div>

<script>
function filterBarData() {
    return {
        filters: {
            search: '<?= htmlspecialchars($search) ?>',
            status: '<?= htmlspecialchars($status) ?>',
            priority: '<?= htmlspecialchars($priority) ?>'
        },
        searchTimeout: null,

        init() {
            // 필터 초기화 완료
        },

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.applyFilters();
            }, 500);
        },

        applyFilters() {
            if (window.projectTable && window.projectTable.loadProjects) {
                window.projectTable.filters = this.filters;
                window.projectTable.pagination.offset = 0;
                window.projectTable.loadProjects();
            } else {
                // Fallback: URL 기반 필터링
                const params = new URLSearchParams();
                if (this.filters.search) params.set('search', this.filters.search);
                if (this.filters.status) params.set('status', this.filters.status);
                if (this.filters.priority) params.set('priority', this.filters.priority);

                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                window.location.href = newUrl;
            }
        },

        clearFilters() {
            this.filters = {
                search: '',
                status: '',
                priority: ''
            };
            this.applyFilters();
        },

        hasActiveFilters() {
            return this.filters.search || this.filters.status || this.filters.priority;
        }
    }
}
</script>