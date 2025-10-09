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
                    <!-- View Mode -->
                    <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                        <button wire:click="changeViewMode('week')" 
                                class="px-4 py-2 text-sm {{ $viewMode === 'week' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            주별
                        </button>
                        <button wire:click="changeViewMode('month')" 
                                class="px-4 py-2 text-sm {{ $viewMode === 'month' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            월별
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Controls -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex justify-between items-center">
                <button wire:click="previousPeriod" 
                        class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($currentDate)->format($viewMode === 'month' ? 'Y년 M월' : 'Y년 M월 W주') }}
                </h2>
                
                <button wire:click="nextPeriod" 
                        class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
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
                <div class="border-b border-r border-gray-200 h-32 p-2 {{ $startDate->month !== $currentMonth ? 'bg-gray-50' : 'bg-white' }} hover:bg-gray-50"
                     wire:click="selectDate('{{ $startDate->format('Y-m-d') }}')">
                    <!-- Date Number -->
                    <div class="text-sm font-medium text-gray-900 mb-1">
                        {{ $startDate->day }}
                    </div>

                    <!-- Events -->
                    <div class="space-y-1">
                        @foreach($projects as $project)
                            @if(\Carbon\Carbon::parse($project['startDate'])->lte($startDate) && \Carbon\Carbon::parse($project['endDate'])->gte($startDate))
                            <div class="text-xs p-1 rounded truncate cursor-pointer
                                @if($project['priority'] === 'high') bg-red-100 text-red-800
                                @elseif($project['priority'] === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ $project['name'] }}
                            </div>
                            @endif
                        @endforeach
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
                <div class="border-b border-r border-gray-200 h-16 p-1 hover:bg-gray-50"
                     wire:click="selectDate('{{ $weekStart->copy()->addDays($day)->format('Y-m-d') }}')">
                    <!-- Events for this time slot -->
                    @foreach($projects as $project)
                        @php
                            $currentDay = $weekStart->copy()->addDays($day);
                            $projectStart = \Carbon\Carbon::parse($project['startDate']);
                            $projectEnd = \Carbon\Carbon::parse($project['endDate']);
                        @endphp
                        @if($projectStart->lte($currentDay) && $projectEnd->gte($currentDay) && $hour === 10)
                        <div class="text-xs p-1 rounded truncate
                            @if($project['priority'] === 'high') bg-red-100 text-red-800
                            @elseif($project['priority'] === 'medium') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ $project['name'] }}
                        </div>
                        @endif
                    @endforeach
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
                $selectedProjects = collect($projects)->filter(function($project) {
                    $selectedCarbon = \Carbon\Carbon::parse($this->selectedDate);
                    $startDate = \Carbon\Carbon::parse($project['startDate']);
                    $endDate = \Carbon\Carbon::parse($project['endDate']);
                    return $startDate->lte($selectedCarbon) && $endDate->gte($selectedCarbon);
                });
            @endphp

            @if($selectedProjects->count() > 0)
            <div class="space-y-3">
                @foreach($selectedProjects as $project)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-medium text-gray-900">{{ $project['name'] }}</h4>
                        <span class="px-2 py-1 text-xs rounded-full font-medium
                            @if($project['status'] === 'in_progress') bg-blue-100 text-blue-800
                            @elseif($project['status'] === 'planning') bg-yellow-100 text-yellow-800
                            @elseif($project['status'] === 'completed') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            @if($project['status'] === 'in_progress') 진행중
                            @elseif($project['status'] === 'planning') 계획중
                            @elseif($project['status'] === 'completed') 완료
                            @else 대기중
                            @endif
                        </span>
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-2">{{ $project['description'] }}</p>
                    
                    <div class="flex justify-between items-center text-sm">
                        <div class="text-gray-600">
                            {{ $project['startDate'] }} ~ {{ $project['endDate'] }}
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                @if($project['priority'] === 'high') bg-red-100 text-red-800
                                @elseif($project['priority'] === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                @if($project['priority'] === 'high') 높음
                                @elseif($project['priority'] === 'medium') 보통
                                @else 낮음
                                @endif
                            </span>
                            <span class="text-gray-600">{{ $project['progress'] }}%</span>
                        </div>
                    </div>
                    
                    <div class="mt-2">
                        <div class="flex flex-wrap gap-1">
                            @foreach($project['team'] as $member)
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $member }}</span>
                            @endforeach
                        </div>
                    </div>
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

        <!-- Legend -->
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h4 class="font-medium text-gray-900 mb-3">범례</h4>
            <div class="flex flex-wrap gap-4 text-sm">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-red-100 border border-red-200 rounded mr-2"></div>
                    <span>높은 우선순위</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-100 border border-yellow-200 rounded mr-2"></div>
                    <span>보통 우선순위</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-100 border border-green-200 rounded mr-2"></div>
                    <span>낮은 우선순위</span>
                </div>
            </div>
        </div>
    </div>
</div>