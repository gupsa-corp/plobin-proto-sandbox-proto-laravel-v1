<div class="bg-white p-4 rounded-lg shadow">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">🔍 필터</h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- 블록 타입 필터 -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">블록 타입</label>
            <select wire:model.live="blockTypeFilter"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">전체</option>
                <option value="title">제목</option>
                <option value="paragraph">단락</option>
                <option value="table">표</option>
                <option value="list">목록</option>
                <option value="other">기타</option>
            </select>
        </div>

        <!-- 최소 신뢰도 필터 -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                최소 신뢰도: {{ number_format($confidenceMin, 2) }}
            </label>
            <input type="range"
                   wire:model.live="confidenceMin"
                   min="0"
                   max="1"
                   step="0.05"
                   class="w-full">
        </div>

        <!-- 페이지당 항목 수 -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">페이지당 항목 수</label>
            <select wire:model.live="limit"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="10">10개</option>
                <option value="20">20개</option>
                <option value="50">50개</option>
                <option value="100">100개</option>
            </select>
        </div>
    </div>
</div>
