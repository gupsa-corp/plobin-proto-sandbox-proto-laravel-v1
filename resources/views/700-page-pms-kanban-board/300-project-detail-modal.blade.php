{{-- 프로젝트 편집 모달 --}}
<div x-show="showProjectModal"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     x-cloak
     @click.self="showProjectModal = false">
    <div class="bg-white rounded-lg w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto">
        {{-- 모달 헤더 --}}
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-900">프로젝트 편집</h3>
            <button @click="showProjectModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- 모달 본문 --}}
        <div class="p-6">
            <div class="space-y-6">
                    {{-- 프로젝트 제목 --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">프로젝트 제목</label>
                        <input type="text"
                               wire:model="editTitle"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="프로젝트 제목을 입력하세요">
                    </div>

                    {{-- 설명 --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">설명</label>
                        <textarea wire:model="editDescription"
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="프로젝트 설명을 입력하세요"></textarea>
                    </div>

                    {{-- 진행률 --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">진행률</label>
                        <div class="space-y-2">
                            <div class="flex items-center space-x-4">
                                <input type="range"
                                       wire:model.live="editProgress"
                                       min="0"
                                       max="100"
                                       class="flex-1">
                                <span class="text-gray-700 font-semibold">{{ $editProgress }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="h-3 rounded-full transition-all duration-300
                                    @if($editProgress < 30) bg-red-500
                                    @elseif($editProgress < 70) bg-yellow-500
                                    @else bg-green-500
                                    @endif"
                                     style="width: {{ $editProgress }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- 상태 --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                        <select wire:model="editStatus"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="planning">계획중</option>
                            <option value="in_progress">진행중</option>
                            <option value="review">검토중</option>
                            <option value="completed">완료</option>
                        </select>
                    </div>

                    {{-- 우선순위, 담당자, 마감일 --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
                            <select wire:model="editPriority"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="high">높음</option>
                                <option value="medium">보통</option>
                                <option value="low">낮음</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">담당자</label>
                            <input type="text"
                                   wire:model="editAssignee"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="담당자 이름">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">마감일</label>
                            <input type="date"
                                   wire:model="editDueDate"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 모달 푸터 --}}
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end space-x-3">
            <button @click="showProjectModal = false"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                취소
            </button>
            <button wire:click="saveProject"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                저장
            </button>
        </div>
    </div>
</div>
