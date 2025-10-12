<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">캘린더 뷰</h2>

    @php
        $currentDate = now();
        $startDate = $currentDate->copy()->startOfMonth()->startOfWeek();
        $endDate = $currentDate->copy()->endOfMonth()->endOfWeek();
        $currentMonth = $currentDate->month;
    @endphp

    <!-- 캘린더 그리드 -->
    <div class="grid grid-cols-7 gap-px bg-gray-200 border border-gray-200 rounded-lg overflow-hidden">
        <!-- 요일 헤더 -->
        @foreach(['일', '월', '화', '수', '목', '금', '토'] as $day)
            <div class="bg-gray-50 p-2 text-center font-medium text-sm text-gray-900">
                {{ $day }}
            </div>
        @endforeach

        <!-- 날짜 셀 -->
        @while($startDate <= $endDate)
            @php
                $isToday = $startDate->isToday();
                $isCurrentMonth = $startDate->month === $currentMonth;
                $dateKey = $startDate->format('Y-m-d');
                $dayEvents = $calendarEvents[$dateKey] ?? [];
            @endphp

            <div class="bg-white p-2 min-h-24 {{ !$isCurrentMonth ? 'bg-gray-50' : '' }} {{ $isToday ? 'ring-2 ring-blue-500' : '' }}">
                <div class="text-sm font-medium mb-1 {{ $isToday ? 'text-blue-600' : ($isCurrentMonth ? 'text-gray-900' : 'text-gray-400') }}">
                    {{ $startDate->day }}
                </div>

                <!-- 이벤트 표시 -->
                <div class="space-y-1">
                    @foreach(array_slice($dayEvents, 0, 2) as $event)
                        <div class="text-xs p-1 rounded truncate
                            @if($event['priority'] === 'high') bg-red-100 text-red-800
                            @elseif($event['priority'] === 'medium') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $event['title'] }}
                        </div>
                    @endforeach

                    @if(count($dayEvents) > 2)
                        <div class="text-xs text-gray-500 font-medium">
                            +{{ count($dayEvents) - 2 }}개 더
                        </div>
                    @endif
                </div>
            </div>

            @php $startDate->addDay(); @endphp
        @endwhile
    </div>
</div>
