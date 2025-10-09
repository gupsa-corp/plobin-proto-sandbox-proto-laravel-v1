<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">PMS 대시보드</h1>
            <p class="text-gray-600 mt-2">프로젝트 관리 시스템 현황을 한눈에 확인하세요</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">전체 프로젝트</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['totalProjects'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">진행중인 프로젝트</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['activeProjects'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">완료된 작업</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['completedTasks'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">대기중인 작업</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['pendingTasks'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Projects -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">최근 프로젝트</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recentProjects as $project)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-medium text-gray-900">{{ $project['name'] }}</h3>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($project['status'] === 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($project['status'] === 'planning') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    @if($project['status'] === 'in_progress') 진행중
                                    @elseif($project['status'] === 'planning') 계획중
                                    @else 완료
                                    @endif
                                </span>
                            </div>
                            <div class="mb-2">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>진행률</span>
                                    <span>{{ $project['progress'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project['progress'] }}%"></div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">마감일: {{ $project['dueDate'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tasks & Notifications -->
            <div class="space-y-6">
                <!-- Recent Tasks -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">내 작업</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($tasks as $task)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $task['title'] }}</h4>
                                    <p class="text-sm text-gray-600">마감: {{ $task['dueDate'] }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($task['priority'] === 'high') bg-red-100 text-red-800
                                    @elseif($task['priority'] === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    @if($task['priority'] === 'high') 높음
                                    @elseif($task['priority'] === 'medium') 보통
                                    @else 낮음
                                    @endif
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">알림</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($notifications as $notification)
                            <div class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">{{ $notification['message'] }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $notification['time'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>