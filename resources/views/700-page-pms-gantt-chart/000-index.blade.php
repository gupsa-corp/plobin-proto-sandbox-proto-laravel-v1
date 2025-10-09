<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-full mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">간트 차트</h1>
                    <p class="text-gray-600 mt-2">프로젝트 일정을 시각적으로 관리하세요</p>
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
                        <button wire:click="changeViewMode('quarter')" 
                                class="px-4 py-2 text-sm {{ $viewMode === 'quarter' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            분기별
                        </button>
                    </div>
                    
                    <!-- Time Range -->
                    <select wire:model.live="timeRange" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1month">1개월</option>
                        <option value="3months">3개월</option>
                        <option value="6months">6개월</option>
                        <option value="1year">1년</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Gantt Chart Container -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <!-- Chart Header -->
            <div class="border-b border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">프로젝트 일정표</h3>
                    <div class="text-sm text-gray-600">
                        {{ count($projects) }}개 프로젝트
                    </div>
                </div>
            </div>

            <!-- Chart Body -->
            <div class="overflow-x-auto">
                <div class="min-w-full">
                    <!-- Timeline Header -->
                    <div class="flex bg-gray-50 border-b border-gray-200">
                        <!-- Project Names Column -->
                        <div class="w-80 p-4 border-r border-gray-200">
                            <h4 class="font-medium text-gray-900">프로젝트</h4>
                        </div>
                        
                        <!-- Timeline Columns -->
                        <div class="flex-1 flex">
                            @php
                                $months = ['2024-10', '2024-11', '2024-12', '2025-01'];
                            @endphp
                            @foreach($months as $month)
                            <div class="flex-1 p-2 text-center border-r border-gray-200 last:border-r-0">
                                <div class="text-sm font-medium text-gray-900">{{ $month }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Project Rows -->
                    @foreach($projects as $project)
                    <div class="flex border-b border-gray-100 hover:bg-gray-50">
                        <!-- Project Info -->
                        <div class="w-80 p-4 border-r border-gray-200">
                            <div>
                                <h5 class="font-medium text-gray-900 mb-1">{{ $project['name'] }}</h5>
                                <div class="flex items-center space-x-2">
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
                                    <span class="text-xs text-gray-600">{{ $project['progress'] }}%</span>
                                </div>
                                <div class="mt-2 text-xs text-gray-500">
                                    {{ $project['startDate'] }} ~ {{ $project['endDate'] }}
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <div class="flex-1 p-2 relative" style="height: 80px;">
                            @php
                                // 간단한 바 표시 계산 (실제로는 날짜 계산 필요)
                                $startPos = 10; // 시작 위치 (%)
                                $width = 40; // 바 길이 (%)
                                
                                if($project['status'] === 'completed') {
                                    $barColor = 'bg-green-500';
                                } elseif($project['status'] === 'in_progress') {
                                    $barColor = 'bg-blue-500';
                                } elseif($project['status'] === 'planning') {
                                    $barColor = 'bg-yellow-500';
                                } else {
                                    $barColor = 'bg-gray-400';
                                }
                            @endphp
                            
                            <!-- Gantt Bar -->
                            <div class="absolute top-1/2 transform -translate-y-1/2 h-6 {{ $barColor }} rounded cursor-pointer hover:shadow-md transition-shadow"
                                 style="left: {{ $startPos }}%; width: {{ $width }}%;"
                                 wire:click="selectProject({{ $project['id'] }})">
                                <!-- Progress Overlay -->
                                <div class="h-full bg-black bg-opacity-20 rounded" 
                                     style="width: {{ $project['progress'] }}%"></div>
                                
                                <!-- Project Name on Bar -->
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-xs text-white font-medium truncate px-2">
                                        {{ $project['name'] }}
                                    </span>
                                </div>
                            </div>

                            <!-- Milestone Markers -->
                            <div class="absolute top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 bg-red-500 rounded-full"
                                 style="left: {{ $startPos }}%;" 
                                 title="시작일: {{ $project['startDate'] }}"></div>
                            <div class="absolute top-1/2 transform -translate-y-1/2 translate-x-1 w-2 h-2 bg-red-500 rounded-full"
                                 style="left: {{ $startPos + $width }}%;" 
                                 title="마감일: {{ $project['endDate'] }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Chart Footer -->
            <div class="bg-gray-50 p-4 border-t border-gray-200">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded mr-2"></div>
                            <span>완료</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded mr-2"></div>
                            <span>진행중</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded mr-2"></div>
                            <span>계획중</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-400 rounded mr-2"></div>
                            <span>대기중</span>
                        </div>
                    </div>
                    <div>
                        클릭하여 프로젝트 상세 정보 확인
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Detail Panel -->
        @if($selectedProject)
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @php
                $selected = collect($projects)->firstWhere('id', $selectedProject);
            @endphp
            @if($selected)
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $selected['name'] }} 상세 정보</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">기본 정보</h4>
                    <dl class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">상태:</dt>
                            <dd class="font-medium">
                                @if($selected['status'] === 'in_progress') 진행중
                                @elseif($selected['status'] === 'planning') 계획중
                                @elseif($selected['status'] === 'completed') 완료
                                @else 대기중
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">우선순위:</dt>
                            <dd class="font-medium">
                                @if($selected['priority'] === 'high') 높음
                                @elseif($selected['priority'] === 'medium') 보통
                                @else 낮음
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">진행률:</dt>
                            <dd class="font-medium">{{ $selected['progress'] }}%</dd>
                        </div>
                    </dl>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">일정</h4>
                    <dl class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">시작일:</dt>
                            <dd class="font-medium">{{ $selected['startDate'] }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">마감일:</dt>
                            <dd class="font-medium">{{ $selected['endDate'] }}</dd>
                        </div>
                    </dl>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">팀원</h4>
                    <div class="flex flex-wrap gap-1">
                        @foreach($selected['team'] as $member)
                        <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $member }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <h4 class="font-medium text-gray-900 mb-2">설명</h4>
                <p class="text-sm text-gray-600">{{ $selected['description'] }}</p>
            </div>
            
            <div class="mt-4 flex justify-end">
                <button wire:click="$set('selectedProject', null)" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    닫기
                </button>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>