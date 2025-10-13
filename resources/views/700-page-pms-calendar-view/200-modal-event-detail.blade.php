<!-- Event Detail Modal -->
<div x-show="showModal"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
     @click="showModal = false" wire:click="closeEventDetailModal">
    @php
        $selectedEvent = $this->getSelectedEvent();
    @endphp
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-screen overflow-y-auto"
         @click.stop>
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-xl font-bold text-gray-900">
                @if($editMode)
                일정 수정
                @else
                {{ $selectedEvent['title'] ?? '' }}
                @endif
            </h3>
            <button @click="showModal = false" wire:click="closeEventDetailModal"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        @if($editMode)
        <!-- Edit Form -->
        <form wire:submit.prevent="updateEvent">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">제목 *</label>
                    <input type="text"
                           wire:model="editForm.title"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('editForm.title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">상세 설명</label>
                    <textarea wire:model="editForm.description"
                              rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    @error('editForm.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">시작일</label>
                        <input type="date"
                               wire:model="editForm.start_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('editForm.start_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">종료일</label>
                        <input type="date"
                               wire:model="editForm.end_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('editForm.end_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">우선순위 *</label>
                        <select wire:model="editForm.priority"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="low">낮음</option>
                            <option value="medium">보통</option>
                            <option value="high">높음</option>
                            <option value="urgent">긴급</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">상태 *</label>
                        <select wire:model="editForm.status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending">대기중</option>
                            <option value="in_progress">진행중</option>
                            <option value="completed">완료</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">예상 소요시간 (시간)</label>
                        <input type="number"
                               wire:model="editForm.estimated_hours"
                               min="1"
                               max="1000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('editForm.estimated_hours') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">진행률 (%)</label>
                        <input type="number"
                               wire:model="editForm.completed_percentage"
                               min="0"
                               max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('editForm.completed_percentage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button"
                        wire:click="cancelEdit"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    취소
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    저장
                </button>
            </div>
        </form>
        @else
        <!-- View Mode -->
        <div class="space-y-4">
            <!-- Status and Priority -->
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-sm rounded-full font-medium
                    @if(($selectedEvent['priority'] ?? '') === 'urgent') bg-red-100 text-red-800
                    @elseif(($selectedEvent['priority'] ?? '') === 'high') bg-orange-100 text-orange-800
                    @elseif(($selectedEvent['priority'] ?? '') === 'medium') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    우선순위: {{ strtoupper($selectedEvent['priority'] ?? 'medium') }}
                </span>
                <span class="px-3 py-1 text-sm rounded-full font-medium
                    @if(($selectedEvent['status'] ?? '') === 'in_progress') bg-blue-100 text-blue-800
                    @elseif(($selectedEvent['status'] ?? '') === 'pending') bg-yellow-100 text-yellow-800
                    @elseif(($selectedEvent['status'] ?? '') === 'completed') bg-green-100 text-green-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    상태:
                    @if(($selectedEvent['status'] ?? '') === 'in_progress') 진행중
                    @elseif(($selectedEvent['status'] ?? '') === 'pending') 대기중
                    @elseif(($selectedEvent['status'] ?? '') === 'completed') 완료
                    @else {{ $selectedEvent['status'] ?? '' }}
                    @endif
                </span>
            </div>

            <!-- Description -->
            @if(($selectedEvent['description'] ?? '') !== '')
            <div>
                <h4 class="font-medium text-gray-900 mb-2">상세 설명</h4>
                <p class="text-gray-600 whitespace-pre-wrap">{{ $selectedEvent['description'] ?? '' }}</p>
            </div>
            @endif

            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">요청자</p>
                    <p class="font-medium text-gray-900">{{ $selectedEvent['requester'] ?? '미정' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">담당자</p>
                    <p class="font-medium text-gray-900">{{ $selectedEvent['assignee'] ?? '미할당' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">시작일</p>
                    <p class="font-medium text-gray-900">{{ $selectedEvent['start_date'] ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">종료일</p>
                    <p class="font-medium text-gray-900">{{ $selectedEvent['end_date'] ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">예상 소요시간</p>
                    <p class="font-medium text-gray-900">{{ ($selectedEvent['estimated_hours'] ?? null) ? $selectedEvent['estimated_hours'] . '시간' : '-' }}</p>
                </div>
            </div>

            <!-- Progress Bar -->
            @if(($selectedEvent['completed_percentage'] ?? 0) > 0)
            <div>
                <div class="flex justify-between items-center mb-2">
                    <p class="text-sm font-medium text-gray-700">진행률</p>
                    <p class="text-sm font-bold text-blue-600">{{ $selectedEvent['completed_percentage'] ?? 0 }}%</p>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ $selectedEvent['completed_percentage'] ?? 0 }}%"></div>
                </div>
            </div>
            @endif
        </div>

        <div class="mt-6 flex justify-between">
            <button wire:click="enableEditMode"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                수정
            </button>
            <button @click="showModal = false" wire:click="closeEventDetailModal"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                닫기
            </button>
        </div>
        @endif
    </div>
</div>
