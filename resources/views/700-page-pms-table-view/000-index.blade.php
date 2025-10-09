<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-full mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">프로젝트 테이블 뷰</h1>
                    <p class="text-gray-600 mt-2">테이블 형태로 프로젝트를 관리하세요</p>
                </div>
                <div class="flex space-x-3">
                    <button wire:click="$toggle('showColumnSelector')" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        컬럼 설정
                    </button>
                    <button wire:click="openProjectForm" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        새 프로젝트
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">검색</label>
                    <input type="text" 
                           wire:model.live="search" 
                           placeholder="프로젝트명 검색..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                    <select wire:model.live="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">모든 상태</option>
                        <option value="planning">계획중</option>
                        <option value="in_progress">진행중</option>
                        <option value="completed">완료</option>
                        <option value="pending">대기중</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
                    <select wire:model.live="priorityFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">모든 우선순위</option>
                        <option value="high">높음</option>
                        <option value="medium">보통</option>
                        <option value="low">낮음</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">
                        필터 초기화
                    </button>
                </div>
            </div>
        </div>

        <!-- Column Selector Modal -->
        @if($showColumnSelector)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-96">
                <h3 class="text-lg font-semibold mb-4">표시할 컬럼 선택</h3>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="selectedColumns" value="name" class="mr-2">
                        프로젝트명
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="selectedColumns" value="status" class="mr-2">
                        상태
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="selectedColumns" value="priority" class="mr-2">
                        우선순위
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="selectedColumns" value="progress" class="mr-2">
                        진행률
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="selectedColumns" value="team" class="mr-2">
                        팀원
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model="selectedColumns" value="dates" class="mr-2">
                        일정
                    </label>
                </div>
                <div class="flex justify-end mt-6">
                    <button wire:click="$toggle('showColumnSelector')" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        확인
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @if(in_array('name', $selectedColumns))
                            <th wire:click="sortBy('name')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                <div class="flex items-center">
                                    프로젝트명
                                    @if($sortBy === 'name')
                                        @if($sortDirection === 'asc')
                                            <svg class="ml-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/>
                                            </svg>
                                        @else
                                            <svg class="ml-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"/>
                                            </svg>
                                        @endif
                                    @endif
                                </div>
                            </th>
                            @endif

                            @if(in_array('status', $selectedColumns))
                            <th wire:click="sortBy('status')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                상태
                            </th>
                            @endif

                            @if(in_array('priority', $selectedColumns))
                            <th wire:click="sortBy('priority')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                우선순위
                            </th>
                            @endif

                            @if(in_array('progress', $selectedColumns))
                            <th wire:click="sortBy('progress')" 
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                진행률
                            </th>
                            @endif

                            @if(in_array('team', $selectedColumns))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                팀원
                            </th>
                            @endif

                            @if(in_array('dates', $selectedColumns))
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                일정
                            </th>
                            @endif

                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                작업
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($projects as $project)
                        <tr class="hover:bg-gray-50">
                            @if(in_array('name', $selectedColumns))
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $project['name'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $project['description'] }}</div>
                                </div>
                            </td>
                            @endif

                            @if(in_array('status', $selectedColumns))
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            @endif

                            @if(in_array('priority', $selectedColumns))
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            @endif

                            @if(in_array('progress', $selectedColumns))
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="h-2 rounded-full
                                            @if($project['progress'] < 30) bg-red-500
                                            @elseif($project['progress'] < 70) bg-yellow-500
                                            @else bg-green-500
                                            @endif" 
                                            style="width: {{ $project['progress'] }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $project['progress'] }}%</span>
                                </div>
                            </td>
                            @endif

                            @if(in_array('team', $selectedColumns))
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($project['team'] as $member)
                                    <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $member }}</span>
                                    @endforeach
                                </div>
                            </td>
                            @endif

                            @if(in_array('dates', $selectedColumns))
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>시작: {{ $project['startDate'] }}</div>
                                <div>마감: {{ $project['endDate'] }}</div>
                            </td>
                            @endif

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-900">보기</button>
                                    <button wire:click="openProjectForm({{ $project['id'] }})" 
                                            class="text-green-600 hover:text-green-900">편집</button>
                                    <button class="text-red-600 hover:text-red-900">삭제</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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

        <!-- Project Form Modal -->
        @if($showProjectForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-screen overflow-y-auto">
                <h3 class="text-lg font-semibold mb-4">
                    {{ $editingProject ? '프로젝트 편집' : '새 프로젝트 추가' }}
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">프로젝트명</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">설명</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="3"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="planning">계획중</option>
                                <option value="in_progress">진행중</option>
                                <option value="completed">완료</option>
                                <option value="pending">대기중</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="low">낮음</option>
                                <option value="medium">보통</option>
                                <option value="high">높음</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">시작일</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">마감일</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button wire:click="closeProjectForm" 
                            class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                        취소
                    </button>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                        {{ $editingProject ? '수정' : '생성' }}
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>