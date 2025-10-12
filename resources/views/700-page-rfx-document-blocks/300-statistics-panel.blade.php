<div class="bg-white p-4 rounded-lg shadow">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 통계</h3>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <!-- 총 블록 수 -->
        <div class="text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $statistics['total_blocks'] ?? 0 }}</p>
            <p class="text-sm text-gray-600">총 블록</p>
        </div>

        <!-- 타입별 개수 -->
        @if(isset($statistics['by_type']))
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600">{{ $statistics['by_type']['title'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">제목</p>
            </div>

            <div class="text-center">
                <p class="text-2xl font-bold text-purple-600">{{ $statistics['by_type']['paragraph'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">단락</p>
            </div>

            <div class="text-center">
                <p class="text-2xl font-bold text-orange-600">{{ $statistics['by_type']['table'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">표</p>
            </div>

            <div class="text-center">
                <p class="text-2xl font-bold text-pink-600">{{ $statistics['by_type']['list'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">목록</p>
            </div>
        @endif
    </div>

    <!-- 평균 신뢰도 -->
    <div class="mt-4 text-center">
        <p class="text-sm text-gray-600">
            평균 신뢰도:
            <span class="font-semibold text-gray-900">
                {{ isset($statistics['average_confidence']) ? number_format($statistics['average_confidence'], 2) : 'N/A' }}
            </span>
        </p>
    </div>
</div>
