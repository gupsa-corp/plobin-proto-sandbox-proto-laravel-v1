<!-- FullCalendar CDN 추가 -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />

@push('scripts-head')
<!-- FullCalendar JavaScript -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/ko.global.min.js'></script>
@endpush

<style>
/* FullCalendar 커스텀 스타일 */
.fc-event {
    border-left-width: 3px !important;
    cursor: pointer;
    transition: all 0.2s;
}

.fc-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.fc-daygrid-day.fc-day-today {
    background-color: #eff6ff !important;
}

.fc-daygrid-day-number {
    font-size: 0.875rem;
    font-weight: 500;
}

.fc-col-header-cell-cushion {
    padding: 8px 4px;
    font-weight: 600;
}

.fc-event-title {
    font-size: 0.75rem;
}

.fc-loading {
    display: none;
}

[x-cloak] {
    display: none !important;
}
</style>

<div class="p-6 bg-gray-50 min-h-screen"
     x-data="{
         showModal: @entangle('showEventDetailModal').live
     }"
     @show-modal.window="showModal = true"
     @close-modal.window="showModal = false">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">캘린더 뷰</h1>
                    <p class="text-gray-600 mt-2">프로젝트 일정을 캘린더로 확인하세요</p>
                </div>
                <div class="flex space-x-3">
                    <!-- Filters Toggle -->
                    <button wire:click="$toggle('showFilters')"
                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z" />
                        </svg>
                        필터
                    </button>

                    <!-- View Mode -->
                    <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                        <button wire:click="changeViewMode('week')"
                                onclick="window.dispatchEvent(new CustomEvent('view-mode-changed', { detail: { mode: 'week' } }))"
                                class="px-4 py-2 text-sm transition-all duration-200 {{ $viewMode === 'week' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            주별
                        </button>
                        <button wire:click="changeViewMode('month')"
                                onclick="window.dispatchEvent(new CustomEvent('view-mode-changed', { detail: { mode: 'month' } }))"
                                class="px-4 py-2 text-sm transition-all duration-200 {{ $viewMode === 'month' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            월별
                        </button>
                    </div>

                    <!-- Add Event Button -->
                    <button wire:click="openCreateModal"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        일정 추가
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters Panel -->
        @if($showFilters)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6 transform transition-all duration-300 ease-in-out">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
                    <select wire:model.live="filterPriority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">모든 우선순위</option>
                        <option value="high">높음</option>
                        <option value="medium">보통</option>
                        <option value="low">낮음</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                    <select wire:model.live="filterStatus" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">모든 상태</option>
                        <option value="planning">계획중</option>
                        <option value="in_progress">진행중</option>
                        <option value="completed">완료</option>
                        <option value="pending">대기중</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button wire:click="clearFilters"
                            class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">
                        필터 초기화
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Calendar Controls -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <button wire:click="previousPeriod"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200 hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>

                    <button wire:click="goToToday"
                            class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200 transition-colors">
                        오늘
                    </button>
                </div>

                <h2 class="text-xl font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($currentDate)->format($viewMode === 'month' ? 'Y년 M월' : 'Y년 M월 W주') }}
                </h2>

                <button wire:click="nextPeriod"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200 hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- FullCalendar Container -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden p-4">
            <div id="fullcalendar" wire:ignore></div>
        </div>

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
             @click="showModal = false; $wire.call('closeEventDetailModal')">
            @php
                $selectedEvent = $this->getSelectedEvent();
            @endphp
            @if($selectedEvent)
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-screen overflow-y-auto"
                 @click.stop>
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-gray-900">
                        @if($editMode)
                        일정 수정
                        @else
                        {{ $selectedEvent['title'] }}
                        @endif
                    </h3>
                    <button @click="showModal = false; $wire.call('closeEventDetailModal')"
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
                            @if($selectedEvent['priority'] === 'urgent') bg-red-100 text-red-800
                            @elseif($selectedEvent['priority'] === 'high') bg-orange-100 text-orange-800
                            @elseif($selectedEvent['priority'] === 'medium') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            우선순위: {{ strtoupper($selectedEvent['priority']) }}
                        </span>
                        <span class="px-3 py-1 text-sm rounded-full font-medium
                            @if($selectedEvent['status'] === 'in_progress') bg-blue-100 text-blue-800
                            @elseif($selectedEvent['status'] === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($selectedEvent['status'] === 'completed') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            상태:
                            @if($selectedEvent['status'] === 'in_progress') 진행중
                            @elseif($selectedEvent['status'] === 'pending') 대기중
                            @elseif($selectedEvent['status'] === 'completed') 완료
                            @else {{ $selectedEvent['status'] }}
                            @endif
                        </span>
                    </div>

                    <!-- Description -->
                    @if($selectedEvent['description'])
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">상세 설명</h4>
                        <p class="text-gray-600 whitespace-pre-wrap">{{ $selectedEvent['description'] }}</p>
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
                            <p class="font-medium text-gray-900">{{ $selectedEvent['estimated_hours'] ? $selectedEvent['estimated_hours'] . '시간' : '-' }}</p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    @if($selectedEvent['completed_percentage'] > 0)
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-sm font-medium text-gray-700">진행률</p>
                            <p class="text-sm font-bold text-blue-600">{{ $selectedEvent['completed_percentage'] }}%</p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: {{ $selectedEvent['completed_percentage'] }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-between">
                    <button wire:click="enableEditMode"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        수정
                    </button>
                    <button @click="showModal = false; $wire.call('closeEventDetailModal')"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        닫기
                    </button>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Flash Message -->
        @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
             class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                {{ session('message') }}
            </div>
        </div>
        @endif

        <!-- Legend -->
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h4 class="font-medium text-gray-900 mb-3">범례 및 사용법</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-2">우선순위</h5>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-100 border-l-2 border-red-600 rounded mr-2"></div>
                            <span>긴급 (빨강)</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-100 border-l-2 border-orange-600 rounded mr-2"></div>
                            <span>높음 (주황)</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-100 border-l-2 border-blue-600 rounded mr-2"></div>
                            <span>보통 (파랑)</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-100 border-l-2 border-gray-500 rounded mr-2"></div>
                            <span>낮음 (회색)</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-2">사용법</h5>
                    <div class="space-y-1 text-sm text-gray-600">
                        <div>• 이벤트 클릭: 상세 정보 보기</div>
                        <div>• 날짜 더블클릭: 새 일정 추가</div>
                        <div>• 여러 날 이벤트는 자동으로 연결됨</div>
                        <div>• 오늘 날짜는 파란색 배경</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Styles for Animations -->
        <style>
            @keyframes fade-in {
                from { opacity: 0; transform: scale(0.95); }
                to { opacity: 1; transform: scale(1); }
            }

            .animate-fade-in {
                animation: fade-in 0.3s ease-out;
            }
        </style>
    </div>
</div>

@push('scripts')
<script>
// Livewire 이벤트 리스너 등록 - Using window event listeners for Livewire dispatched events
window.addEventListener('view-mode-changed', (event) => {
    console.log('View mode changed event received:', event.detail);
    const mode = event.detail.mode || event.detail[0]?.mode || 'month';
    console.log('Changing calendar view to:', mode === 'week' ? 'timeGridWeek' : 'dayGridMonth');

    if (window.calendarInstance) {
        window.calendarInstance.changeView(mode === 'week' ? 'timeGridWeek' : 'dayGridMonth');
    }
});

window.addEventListener('calendar-updated', () => {
    console.log('Calendar updated event received');
    if (window.calendarInstance) {
        window.calendarInstance.refetchEvents();
        const livewireComponent = Livewire.first();
        livewireComponent.get('currentDate').then(currentDate => {
            if (currentDate && window.calendarInstance) {
                window.calendarInstance.gotoDate(currentDate);
            }
        });
    }
});

document.addEventListener('livewire:navigated', function() {
    initCalendar();
});

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('fullcalendar')) {
        initCalendar();
    }
});

function initCalendar() {
    const calendarEl = document.getElementById('fullcalendar');
    if (!calendarEl) {
        console.log('Calendar element not found');
        return;
    }

    // Check if calendar already initialized
    if (window.calendarInstance) {
        console.log('Calendar already initialized, destroying old instance');
        window.calendarInstance.destroy();
    }

    // Livewire 컴포넌트 찾기 - first() 사용 (전체 페이지가 하나의 Livewire 컴포넌트)
    const livewireComponent = Livewire.first();
    console.log('Initializing FullCalendar...');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'ko',
        initialView: @js($viewMode === 'week' ? 'timeGridWeek' : 'dayGridMonth'),
        initialDate: @js($currentDate),
        headerToolbar: false,
        height: 'auto',

        events: function(fetchInfo, successCallback, failureCallback) {
            @this.call('getFullCalendarEvents').then(events => {
                console.log('Events loaded:', events.length);
                successCallback(events);
            });
        },

        eventClick: function(info) {
            console.log('Event clicked, ID:', info.event.id);
            const eventId = parseInt(info.event.id);

            // Call Livewire method via $wire
            Livewire.first().$wire.call('showEventDetail', eventId);
        },

        dateClick: function(info) {
            if (info.jsEvent.detail === 2) {
                @this.call('openCreateModal', info.dateStr);
            } else {
                @this.call('selectDate', info.dateStr);
            }
        },

        eventContent: function(arg) {
            let props = arg.event.extendedProps;
            let icon = '';
            if (props.status === 'completed') {
                icon = '<svg class="w-3 h-3 inline mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
            } else if (props.status === 'in_progress') {
                icon = '<div class="w-2 h-2 inline-block mr-1 bg-blue-500 rounded-full animate-pulse"></div>';
            }

            let hours = props.estimated_hours ?
                `<span class="text-[10px] ml-1">${props.estimated_hours}h</span>` : '';

            return {
                html: `
                    <div class="fc-event-main-frame px-1">
                        <div class="fc-event-title-container flex items-center justify-between">
                            <div class="flex items-center flex-1 min-w-0">
                                ${icon}
                                <span class="fc-event-title truncate">${arg.event.title}</span>
                            </div>
                            ${hours}
                        </div>
                    </div>
                `
            };
        },

        viewDidMount: () => {
            console.log('FullCalendar rendered');
        },

        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5],
            startTime: '09:00',
            endTime: '18:00',
        },

        weekends: true,

        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        }
    });

    calendar.render();

    // Store calendar instance globally for refresh
    window.calendarInstance = calendar;
}
</script>
@endpush
