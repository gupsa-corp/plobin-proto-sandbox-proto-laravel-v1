<div class="bg-white p-4 rounded-lg shadow">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 블록 목록</h3>

    @if(empty($blocks))
        <p class="text-center text-gray-500 py-8">블록이 없습니다</p>
    @else
        <div class="space-y-3">
            @foreach($blocks as $block)
                <div wire:click="selectBlock({{ $block['block_id'] }})"
                     class="p-4 border rounded-lg cursor-pointer hover:bg-blue-50 transition {{ isset($selectedBlock) && $selectedBlock['block_id'] === $block['block_id'] ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                    <div class="flex items-start justify-between">
                        <!-- 블록 정보 -->
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <!-- 블록 타입 아이콘 -->
                                <span class="text-xl">
                                    @if($block['block_type'] === 'title')
                                        📌
                                    @elseif($block['block_type'] === 'paragraph')
                                        📄
                                    @elseif($block['block_type'] === 'table')
                                        📋
                                    @elseif($block['block_type'] === 'list')
                                        📝
                                    @else
                                        📦
                                    @endif
                                </span>

                                <!-- 블록 번호 및 타입 -->
                                <span class="font-semibold text-gray-900">블록 #{{ $block['block_id'] }}</span>
                                <span class="px-2 py-1 text-xs rounded {{ $block['block_type'] === 'title' ? 'bg-green-100 text-green-800' : ($block['block_type'] === 'paragraph' ? 'bg-blue-100 text-blue-800' : ($block['block_type'] === 'table' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ $block['block_type'] }}
                                </span>

                                <!-- 신뢰도 -->
                                <span class="text-sm text-gray-600">
                                    {{ number_format($block['confidence'] ?? 0, 2) }}
                                </span>
                            </div>

                            <!-- 텍스트 미리보기 -->
                            <p class="text-sm text-gray-700 line-clamp-2">
                                {{ mb_substr($block['text'] ?? '', 0, 100) }}{{ mb_strlen($block['text'] ?? '') > 100 ? '...' : '' }}
                            </p>
                        </div>

                        <!-- 액션 버튼 -->
                        <div class="flex space-x-2 ml-4">
                            <button wire:click.stop="openEditModal({{ $block['block_id'] }})"
                                    class="px-3 py-1 text-sm bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                편집
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- 페이지네이션 -->
        @if(isset($pagination) && $pagination['total_pages'] > 1)
            <div class="mt-4 flex items-center justify-center space-x-2">
                <p class="text-sm text-gray-600">
                    {{ $pagination['current_page'] }} / {{ $pagination['total_pages'] }} 페이지
                    (총 {{ $pagination['total_items'] }}개)
                </p>
            </div>
        @endif
    @endif
</div>
