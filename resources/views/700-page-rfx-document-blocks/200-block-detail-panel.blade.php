<div class="bg-white p-4 rounded-lg shadow sticky top-4">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">📌 블록 상세</h3>

    @if($selectedBlock)
        <div class="space-y-4">
            <!-- 블록 정보 -->
            <div>
                <p class="text-sm font-medium text-gray-700">블록 번호</p>
                <p class="text-lg font-semibold text-gray-900">#{{ $selectedBlock['block_id'] }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-700">블록 타입</p>
                <span class="inline-block px-3 py-1 rounded {{ $selectedBlock['block_type'] === 'title' ? 'bg-green-100 text-green-800' : ($selectedBlock['block_type'] === 'paragraph' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                    {{ $selectedBlock['block_type'] }}
                </span>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-700">신뢰도</p>
                <p class="text-lg text-gray-900">{{ number_format($selectedBlock['confidence'] ?? 0, 2) }}</p>
            </div>

            <!-- 텍스트 -->
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">텍스트</p>
                <div class="p-3 bg-gray-50 rounded border border-gray-200 max-h-64 overflow-y-auto">
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $selectedBlock['text'] ?? '' }}</p>
                </div>
            </div>

            <!-- 위치 정보 -->
            @if(isset($selectedBlock['position']))
            <div>
                <p class="text-sm font-medium text-gray-700 mb-2">위치</p>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-gray-600">X:</span>
                        <span class="font-semibold">{{ $selectedBlock['position']['x'] ?? 0 }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Y:</span>
                        <span class="font-semibold">{{ $selectedBlock['position']['y'] ?? 0 }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">너비:</span>
                        <span class="font-semibold">{{ $selectedBlock['position']['width'] ?? 0 }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">높이:</span>
                        <span class="font-semibold">{{ $selectedBlock['position']['height'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- 액션 버튼 -->
            <div class="space-y-2">
                <button wire:click="showBlockImage({{ $selectedBlock['block_id'] }})"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    이미지 보기
                </button>

                <button wire:click="openEditModal({{ $selectedBlock['block_id'] }})"
                        class="w-full px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                    블록 편집
                </button>
            </div>
        </div>
    @else
        <p class="text-center text-gray-500 py-8">블록을 선택해주세요</p>
    @endif
</div>
