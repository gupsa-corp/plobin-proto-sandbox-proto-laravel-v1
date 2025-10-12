@if($showEditModal)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeEditModal">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[90vh] overflow-auto" wire:click.stop>
        <!-- 헤더 -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">블록 편집</h3>
            <button wire:click="closeEditModal" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- 폼 -->
        <div class="space-y-4">
            <!-- 블록 번호 (읽기 전용) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">블록 번호</label>
                <p class="text-lg font-semibold text-gray-900">#{{ $editingBlock['block_id'] ?? '' }}</p>
            </div>

            <!-- 블록 타입 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">블록 타입</label>
                <select wire:model="editBlockType"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="title">제목</option>
                    <option value="paragraph">단락</option>
                    <option value="table">표</option>
                    <option value="list">목록</option>
                    <option value="other">기타</option>
                </select>
            </div>

            <!-- 텍스트 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">텍스트</label>
                <textarea wire:model="editText"
                          rows="10"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <!-- 신뢰도 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    신뢰도: {{ number_format($editConfidence, 2) }}
                </label>
                <input type="range"
                       wire:model.live="editConfidence"
                       min="0"
                       max="1"
                       step="0.01"
                       class="w-full">
            </div>
        </div>

        <!-- 액션 버튼 -->
        <div class="mt-6 flex justify-end space-x-3">
            <button wire:click="closeEditModal"
                    class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                취소
            </button>
            <button wire:click="saveBlock"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                저장
            </button>
        </div>
    </div>
</div>
@endif
