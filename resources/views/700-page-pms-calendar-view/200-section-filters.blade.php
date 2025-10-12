<!-- Filters Panel -->
@if($showFilters)
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6 transform transition-all duration-300 ease-in-out">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
            <select wire:model.live="filterPriority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">모든 우선순위</option>
                <option value="high">높음</option>
                <option value="medium">보통</option>
                <option value="low">낮음</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
            <select wire:model.live="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">모든 상태</option>
                <option value="planning">계획중</option>
                <option value="in_progress">진행중</option>
                <option value="completed">완료</option>
                <option value="pending">대기중</option>
            </select>
        </div>

        <div class="flex items-end">
            <button wire:click="clearFilters"
                    class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">
                필터 초기화
            </button>
        </div>
    </div>
</div>
@endif
