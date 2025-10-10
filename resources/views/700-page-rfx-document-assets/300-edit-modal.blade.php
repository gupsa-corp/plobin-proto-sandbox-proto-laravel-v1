<!-- 편집 모달 -->
@if($showEditModal)
<div class="fixed inset-0 z-50 overflow-y-auto" x-data>
    <!-- 오버레이 -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
         wire:click="closeEditModal"></div>

    <!-- 모달 컨텐츠 -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full"
             @click.stop>
            <!-- 헤더 -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">
                        @if($editingField === 'content')
                            📄 원문 편집
                        @elseif($editingField === 'summary')
                            🤖 AI 요약 편집
                        @elseif($editingField === 'helpful')
                            💡 도움되는 내용 편집
                        @endif
                    </h3>
                    <button wire:click="closeEditModal"
                            class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- 본문 -->
            <div class="px-6 py-4">
                <textarea wire:model="editingValue"
                          rows="12"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="내용을 입력하세요..."></textarea>
            </div>

            <!-- 푸터 -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3 rounded-b-lg">
                <button wire:click="closeEditModal"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    취소
                </button>
                <button wire:click="saveEdit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    저장
                </button>
            </div>
        </div>
    </div>
</div>
@endif
