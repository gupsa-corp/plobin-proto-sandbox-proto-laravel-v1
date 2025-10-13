<!-- Header -->
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">캘린더 뷰</h1>
            <p class="text-gray-600 mt-2">프로젝트 일정을 캘린더로 확인하세요</p>
        </div>
        <div class="flex space-x-3">
            <!-- Filters Toggle -->
            <button @click="showFilters = !showFilters"
                    :class="showFilters ? 'bg-blue-600' : 'bg-gray-600'"
                    class="text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z" />
                </svg>
                필터
            </button>

            <!-- View Mode -->
            <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                <button wire:click="changeViewMode('week')"
                        onclick="window.dispatchEvent(new CustomEvent('view-mode-changed', { detail: { mode: 'week' } }))"
                        class="px-4 py-2 text-sm transition-all duration-200 {{ $viewMode === 'week' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    주별
                </button>
                <button wire:click="changeViewMode('month')"
                        onclick="window.dispatchEvent(new CustomEvent('view-mode-changed', { detail: { mode: 'month' } }))"
                        class="px-4 py-2 text-sm transition-all duration-200 {{ $viewMode === 'month' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                    월별
                </button>
            </div>

            <!-- Add Event Button -->
            <button wire:click="openCreateModal"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                일정 추가
            </button>
        </div>
    </div>
</div>
