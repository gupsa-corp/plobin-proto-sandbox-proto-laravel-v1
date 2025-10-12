<div>
    <h3 class="text-lg font-semibold text-gray-900 mb-3">포함된 블록</h3>

    @if(!empty($sectionBlocks))
        <div class="space-y-3">
            @foreach($sectionBlocks as $block)
                <div class="p-4 border border-gray-200 rounded hover:bg-gray-50 transition">
                    <div class="flex items-start justify-between">
                        <!-- 블록 정보 -->
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <!-- 블록 타입 아이콘 -->
                                <span class="text-xl">
                                    @if(($block['type'] ?? '') === 'title')
                                        📌
                                    @elseif(($block['type'] ?? '') === 'paragraph')
                                        📄
                                    @elseif(($block['type'] ?? '') === 'table')
                                        📋
                                    @elseif(($block['type'] ?? '') === 'list')
                                        📝
                                    @else
                                        📦
                                    @endif
                                </span>

                                <!-- 블록 번호 및 타입 -->
                                <span class="font-semibold text-gray-900">블록 #{{ $block['block_id'] }}</span>
                                <span class="px-2 py-1 text-xs rounded {{ ($block['type'] ?? '') === 'title' ? 'bg-green-100 text-green-800' : (($block['type'] ?? '') === 'paragraph' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ $block['type'] ?? 'text' }}
                                </span>

                                <!-- 신뢰도 -->
                                <span class="text-sm text-gray-600">
                                    {{ number_format($block['confidence'] ?? 0, 2) }}
                                </span>
                            </div>

                            <!-- 텍스트 -->
                            <p class="text-sm text-gray-700">
                                {{ $block['text'] ?? '' }}
                            </p>
                        </div>

                        <!-- 블록 뷰로 이동 버튼 -->
                        <a href="{{ route('rfx.documents.blocks', ['documentId' => $documentId]) }}#block-{{ $block['block_id'] }}"
                           class="ml-4 px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                            상세
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-center text-gray-500 py-8">이 섹션에는 블록이 없습니다</p>
    @endif
</div>
