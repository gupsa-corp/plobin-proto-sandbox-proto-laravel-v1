<div class="p-6 bg-gray-50 min-h-screen">
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
                                class="px-4 py-2 text-sm transition-all duration-200 {{ $viewMode === 'week' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            주별
                        </button>
                        <button wire:click="changeViewMode('month')" 
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
                
                <h2 class="text-xl font-semibold text-gray-900 animate-pulse">
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

        <!-- Calendar Grid -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($viewMode === 'month')
            <!-- Month View -->
            <div class="grid grid-cols-7">
                <!-- Week Headers -->
                @foreach(['일', '월', '화', '수', '목', '금', '토'] as $day)
                <div class="p-4 text-center font-medium text-gray-900 bg-gray-50 border-b border-gray-200">
                    {{ $day }}
                </div>
                @endforeach

                <!-- Calendar Days -->
                @php
                    $startDate = \Carbon\Carbon::parse($currentDate)->startOfMonth()->startOfWeek();
                    $endDate = \Carbon\Carbon::parse($currentDate)->endOfMonth()->endOfWeek();
                    $currentMonth = \Carbon\Carbon::parse($currentDate)->month;
                @endphp

                @while($startDate <= $endDate)
                @php
                    $isToday = $startDate->isToday();
                    $isCurrentMonth = $startDate->month === $currentMonth;
                    $dateKey = $startDate->format('Y-m-d');
                    $dayEvents = $calendarEvents[$dateKey] ?? [];
                @endphp
                <div class="border-b border-r border-gray-200 h-32 p-2 cursor-pointer transition-all duration-200 hover:scale-105 hover:shadow-md group
                    {{ !$isCurrentMonth ? 'bg-gray-50' : 'bg-white' }}
                    {{ $isToday ? 'ring-2 ring-blue-500 bg-blue-50' : '' }}"
                     wire:click="selectDate('{{ $startDate->format('Y-m-d') }}')"
                     ondblclick="@this.call('openCreateModal', '{{ $startDate->format('Y-m-d') }}')">
                    
                    <!-- Date Number -->
                    <div class="flex justify-between items-center mb-1">
                        <div class="text-sm font-medium {{ $isToday ? 'text-blue-600 font-bold' : 'text-gray-900' }}
                                  {{ !$isCurrentMonth ? 'text-gray-400' : '' }}">
                            {{ $startDate->day }}
                        </div>
                        @if(count($dayEvents) > 0)
                        <div class="text-xs bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center animate-pulse">
                            {{ count($dayEvents) }}
                        </div>
                        @endif
                    </div>

                    <!-- Events -->
                    <div class="space-y-1">
                        @foreach(array_slice($dayEvents, 0, 3) as $event)
                        <div class="text-xs p-1 rounded truncate cursor-pointer transition-all duration-200 hover:scale-105
                            @if($event['priority'] === 'urgent') bg-red-100 text-red-800 border-l-2 border-red-600
                            @elseif($event['priority'] === 'high') bg-orange-100 text-orange-800 border-l-2 border-orange-500
                            @elseif($event['priority'] === 'medium') bg-blue-100 text-blue-800 border-l-2 border-blue-500
                            @else bg-gray-100 text-gray-800 border-l-2 border-gray-500
                            @endif"
                             wire:click="showEventDetail({{ $event['id'] }})"
                             onclick="event.stopPropagation()"
                             title="[{{ strtoupper($event['priority']) }}] {{ $event['title'] }} - {{ $event['assignee'] ?? '미할당' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1 min-w-0">
                                    @if($event['status'] === 'completed')
                                        <svg class="w-3 h-3 mr-1 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    @elseif($event['status'] === 'in_progress')
                                        <div class="w-2 h-2 mr-1 bg-blue-500 rounded-full animate-pulse flex-shrink-0"></div>
                                    @else
                                        <div class="w-2 h-2 mr-1 bg-gray-400 rounded-full flex-shrink-0"></div>
                                    @endif
                                    <span class="truncate">{{ $event['title'] }}</span>
                                </div>
                                @if($event['estimated_hours'])
                                <span class="text-[10px] ml-1 flex-shrink-0">{{ $event['estimated_hours'] }}h</span>
                                @endif
                            </div>
                        </div>
                        @endforeach

                        @if(count($dayEvents) > 3)
                        <div class="text-xs text-gray-500 font-medium">
                            +{{ count($dayEvents) - 3 }}개 더
                        </div>
                        @endif
                    </div>

                    <!-- Today Indicator -->
                    @if($isToday)
                    <div class="absolute inset-0 pointer-events-none">
                        <div class="absolute top-1 right-1 w-2 h-2 bg-blue-500 rounded-full animate-ping"></div>
                        <div class="absolute top-1 right-1 w-2 h-2 bg-blue-500 rounded-full"></div>
                    </div>
                    @endif

                    <!-- Add Event Hint -->
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                        <div class="absolute bottom-1 right-1 text-xs text-gray-400">
                            더블클릭하여 일정 추가
                        </div>
                    </div>
                </div>
                @php $startDate->addDay(); @endphp
                @endwhile
            </div>
            @else
            <!-- Week View -->
            <div class="grid grid-cols-8">
                <!-- Time Column Header -->
                <div class="p-4 text-center font-medium text-gray-900 bg-gray-50 border-b border-gray-200">
                    시간
                </div>
                
                <!-- Week Headers -->
                @php
                    $weekStart = \Carbon\Carbon::parse($currentDate)->startOfWeek();
                @endphp
                @for($i = 0; $i < 7; $i++)
                <div class="p-4 text-center font-medium text-gray-900 bg-gray-50 border-b border-gray-200">
                    <div>{{ $weekStart->copy()->addDays($i)->format('n/j') }}</div>
                    <div class="text-xs text-gray-600">{{ $weekStart->copy()->addDays($i)->format('D') }}</div>
                </div>
                @endfor

                <!-- Time Slots -->
                @for($hour = 9; $hour <= 18; $hour++)
                <div class="p-2 text-xs text-gray-600 bg-gray-50 border-b border-gray-200 text-center">
                    {{ $hour }}:00
                </div>
                @for($day = 0; $day < 7; $day++)
                @php
                    $currentDay = $weekStart->copy()->addDays($day);
                    $dateKey = $currentDay->format('Y-m-d');
                    $dayEvents = $calendarEvents[$dateKey] ?? [];
                @endphp
                <div class="border-b border-r border-gray-200 h-16 p-1 hover:bg-gray-50"
                     wire:click="selectDate('{{ $dateKey }}')">
                    <!-- Events for this time slot -->
                    @if($hour === 10 && count($dayEvents) > 0)
                        @foreach(array_slice($dayEvents, 0, 2) as $event)
                        <div class="text-xs p-1 rounded truncate mb-1 cursor-pointer hover:scale-105 transition-all duration-200
                            @if($event['priority'] === 'urgent') bg-red-100 text-red-800 border-l-2 border-red-600
                            @elseif($event['priority'] === 'high') bg-orange-100 text-orange-800 border-l-2 border-orange-500
                            @elseif($event['priority'] === 'medium') bg-blue-100 text-blue-800 border-l-2 border-blue-500
                            @else bg-gray-100 text-gray-800 border-l-2 border-gray-500
                            @endif"
                             wire:click="showEventDetail({{ $event['id'] }})"
                             onclick="event.stopPropagation()">
                            {{ $event['title'] }}
                        </div>
                        @endforeach
                    @endif
                </div>
                @endfor
                @endfor
            </div>
            @endif
        </div>

        <!-- Selected Date Panel -->
        @if($selectedDate)
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                {{ \Carbon\Carbon::parse($selectedDate)->format('Y년 n월 j일') }} 일정
            </h3>
            
            @php
                $selectedEvents = $calendarEvents[$selectedDate] ?? [];
            @endphp

            @if(count($selectedEvents) > 0)
            <div class="space-y-3">
                @foreach($selectedEvents as $event)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-medium text-gray-900">{{ $event['title'] }}</h4>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                @if($event['priority'] === 'urgent') bg-red-100 text-red-800
                                @elseif($event['priority'] === 'high') bg-orange-100 text-orange-800
                                @elseif($event['priority'] === 'medium') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ strtoupper($event['priority']) }}
                            </span>
                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                @if($event['status'] === 'in_progress') bg-blue-100 text-blue-800
                                @elseif($event['status'] === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($event['status'] === 'completed') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($event['status'] === 'in_progress') 진행중
                                @elseif($event['status'] === 'pending') 대기중
                                @elseif($event['status'] === 'completed') 완료
                                @else {{ $event['status'] }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 mb-2">요청자: {{ $event['requester'] ?? '미정' }}</p>
                    @if($event['assignee'])
                    <p class="text-sm text-gray-600 mb-2">담당자: {{ $event['assignee'] }}</p>
                    @endif

                    <div class="flex justify-between items-center text-sm mt-3">
                        <div class="text-gray-600">
                            완료 요청일: {{ $event['date'] }}
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-gray-600">예상: {{ $event['estimated_hours'] }}시간</span>
                            <span class="text-gray-600">진행률: {{ $event['completed_percentage'] }}%</span>
                        </div>
                    </div>

                    @if($event['completed_percentage'] > 0)
                    <div class="mt-2">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $event['completed_percentage'] }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-600">선택한 날짜에 진행 중인 프로젝트가 없습니다.</p>
            @endif
            
            <div class="mt-4 flex justify-end">
                <button wire:click="$set('selectedDate', null)" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    닫기
                </button>
            </div>
        </div>
        @endif

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
        @if($showEventDetailModal)
        @php
            $selectedEvent = $this->getSelectedEvent();
        @endphp
        @if($selectedEvent)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 animate-fade-in">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-screen overflow-y-auto transform transition-all duration-300 scale-100">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-bold text-gray-900">
                        @if($editMode)
                        일정 수정
                        @else
                        {{ $selectedEvent['title'] }}
                        @endif
                    </h3>
                    <button wire:click="closeEventDetailModal" class="text-gray-400 hover:text-gray-600">
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
                    <button wire:click="closeEventDetailModal"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        닫기
                    </button>
                </div>
                @endif
            </div>
        </div>
        @endif
        @endif

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
                            <div class="w-3 h-3 bg-red-100 border-l-2 border-red-500 rounded mr-2"></div>
                            <span>높은 우선순위</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-100 border-l-2 border-yellow-500 rounded mr-2"></div>
                            <span>보통 우선순위</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-100 border-l-2 border-green-500 rounded mr-2"></div>
                            <span>낮은 우선순위</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h5 class="text-sm font-medium text-gray-700 mb-2">사용법</h5>
                    <div class="space-y-1 text-sm text-gray-600">
                        <div>• 날짜 클릭: 해당 날짜 일정 보기</div>
                        <div>• 날짜 더블클릭: 새 일정 추가</div>
                        <div>• 오늘 날짜는 파란색 테두리</div>
                        <div>• 일정 수는 우상단 숫자로 표시</div>
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
            
            .animate-pulse {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
            
            @keyframes pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: .5; }
            }
            
            .animate-ping {
                animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
            }
            
            @keyframes ping {
                75%, 100% {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        </style>

        <!-- Debug JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Calendar View Loaded');
                console.log('Livewire:', window.Livewire);
                
                // 디버그용 함수들
                window.testCreateModal = function() {
                    console.log('Testing Create Modal...');
                    const component = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
                    console.log('Component:', component);
                    component.call('openCreateModal');
                };
                
                window.checkModalState = function() {
                    const component = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
                    console.log('showCreateModal:', component.get('showCreateModal'));
                };
                
                // 버튼 클릭 이벤트 확인
                const createButton = document.querySelector('[wire\\:click="openCreateModal"]');
                if (createButton) {
                    console.log('Create button found:', createButton);
                    createButton.addEventListener('click', function() {
                        console.log('Create button clicked!');
                    });
                } else {
                    console.log('Create button not found');
                }
                
                // Livewire 이벤트 리스너
                document.addEventListener('livewire:morph.updated', function() {
                    console.log('Livewire updated');
                });
                
                console.log('Debug functions available: testCreateModal(), checkModalState()');
            });
        </script>
    </div>
</div>