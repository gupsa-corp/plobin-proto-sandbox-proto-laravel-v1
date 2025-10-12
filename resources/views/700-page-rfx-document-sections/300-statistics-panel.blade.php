<div class="bg-white p-4 rounded-lg shadow">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 통계</h3>

    <div class="space-y-3">
        <!-- 총 섹션 수 -->
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">총 섹션</span>
            <span class="text-xl font-bold text-blue-600">{{ $statistics['total_sections'] ?? 0 }}</span>
        </div>

        <!-- 총 하위 섹션 수 -->
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">하위 섹션</span>
            <span class="text-xl font-bold text-green-600">{{ $statistics['total_subsections'] ?? 0 }}</span>
        </div>

        <!-- 총 블록 수 -->
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-600">총 블록</span>
            <span class="text-xl font-bold text-purple-600">{{ $statistics['total_blocks'] ?? 0 }}</span>
        </div>
    </div>
</div>
