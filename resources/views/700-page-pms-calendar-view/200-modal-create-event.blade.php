<!-- Create Event Modal -->
@if($showCreateModal)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fade-in">
    <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-screen overflow-y-auto transform transition-all duration-300 scale-100">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            새 일정 추가
        </h3>

        <form wire:submit.prevent="createEvent">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">제목 *</label>
                    <input type="text"
                           wire:model="eventForm.title"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200"
                           placeholder="분석 요청 제목을 입력하세요">
                    @error('eventForm.title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">상세 설명</label>
                    <textarea wire:model="eventForm.description"
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200"
                              placeholder="요청하실 분석 내용을 상세히 작성해주세요"></textarea>
                    @error('eventForm.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">시작일</label>
                        <input type="date"
                               wire:model="eventForm.start_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200">
                        @error('eventForm.start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">종료일</label>
                        <input type="date"
                               wire:model="eventForm.end_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200">
                        @error('eventForm.end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">예상 소요시간 (시간)</label>
                    <input type="number"
                           wire:model="eventForm.estimated_hours"
                           min="1"
                           max="1000"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200">
                    @error('eventForm.estimated_hours') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">우선순위 *</label>
                    <select wire:model="eventForm.priority"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200">
                        <option value="low">낮음 (14일 이내)</option>
                        <option value="medium">보통 (7일 이내)</option>
                        <option value="high">높음 (3일 이내)</option>
                        <option value="urgent">긴급 (1일 이내)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button"
                        wire:click="closeCreateModal"
                        class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-all duration-200">
                    취소
                </button>
                <button type="submit"
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-all duration-200 transform hover:scale-105">
                    생성
                </button>
            </div>
        </form>
    </div>
</div>
@endif
