<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">프로젝트 목록</h1>
                    <p class="text-gray-600 mt-2">진행 중인 모든 프로젝트를 관리하세요</p>
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    새 프로젝트 추가
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">검색</label>
                    <input type="text" 
                           wire:model.live="search" 
                           placeholder="프로젝트명 또는 설명 검색..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                    <select wire:model.live="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">모든 상태</option>
                        <option value="planning">계획중</option>
                        <option value="in_progress">진행중</option>
                        <option value="completed">완료</option>
                        <option value="pending">대기중</option>
                    </select>
                </div>

                <!-- Priority Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
                    <select wire:model.live="priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">모든 우선순위</option>
                        <option value="high">높음</option>
                        <option value="medium">보통</option>
                        <option value="low">낮음</option>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">정렬</label>
                    <select wire:model.live="sortBy" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="createdAt">생성일</option>
                        <option value="name">프로젝트명</option>
                        <option value="priority">우선순위</option>
                        <option value="progress">진행률</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                <!-- Project Header -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $project['name'] }}</h3>
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
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $project['description'] }}</p>
                </div>

                <!-- Project Details -->
                <div class="p-6">
                    <!-- Progress -->
                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>진행률</span>
                            <span>{{ $project['progress'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300
                                @if($project['progress'] < 30) bg-red-500
                                @elseif($project['progress'] < 70) bg-yellow-500
                                @else bg-green-500
                                @endif" 
                                style="width: {{ $project['progress'] }}%"></div>
                        </div>
                    </div>

                    <!-- Priority -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-600">우선순위</span>
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
                    </div>

                    <!-- Dates -->
                    <div class="text-sm text-gray-600 mb-3">
                        <div class="flex justify-between">
                            <span>시작일</span>
                            <span>{{ $project['startDate'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>마감일</span>
                            <span>{{ $project['endDate'] }}</span>
                        </div>
                    </div>

                    <!-- Team -->
                    <div class="mb-4">
                        <span class="text-sm text-gray-600 block mb-2">팀원</span>
                        <div class="flex flex-wrap gap-1">
                            @foreach($project['team'] as $member)
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $member }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <button class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                            상세보기
                        </button>
                        <button class="px-3 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50 transition-colors">
                            편집
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if(empty($projects))
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">프로젝트 없음</h3>
            <p class="mt-1 text-sm text-gray-500">검색 조건에 맞는 프로젝트가 없습니다.</p>
        </div>
        @endif
    </div>
</div>