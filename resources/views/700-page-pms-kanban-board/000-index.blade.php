<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-full mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">칸반 보드</h1>
                    <p class="text-gray-600 mt-2">드래그 앤 드롭으로 프로젝트 상태를 관리하세요</p>
                </div>
                <button wire:click="openTaskForm" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    새 작업 추가
                </button>
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="flex space-x-6 overflow-x-auto pb-6">
            @foreach($columns as $column)
            <div class="flex-shrink-0 w-80">
                <!-- Column Header -->
                <div class="{{ $column['color'] }} {{ $column['borderColor'] }} border rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-gray-900">{{ $column['title'] }}</h3>
                        <span class="bg-white px-2 py-1 rounded-full text-sm font-medium text-gray-600">
                            {{ count(array_filter($projects, fn($p) => $p['status'] === $column['id'])) }}
                        </span>
                    </div>
                </div>

                <!-- Cards Container -->
                <div class="space-y-3" style="min-height: 500px;">
                    @foreach($projects as $project)
                        @if($project['status'] === $column['id'])
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-move">
                            <!-- Card Header -->
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-medium text-gray-900 line-clamp-2">{{ $project['title'] }}</h4>
                                <span class="px-2 py-1 text-xs rounded-full font-medium ml-2 flex-shrink-0
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

                            <!-- Description -->
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project['description'] }}</p>

                            <!-- Progress Bar -->
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>진행률</span>
                                    <span>{{ $project['progress'] }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full transition-all duration-300
                                        @if($project['progress'] < 30) bg-red-500
                                        @elseif($project['progress'] < 70) bg-yellow-500
                                        @else bg-green-500
                                        @endif" 
                                        style="width: {{ $project['progress'] }}%"></div>
                                </div>
                            </div>

                            <!-- Tags -->
                            @if(!empty($project['tags']))
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach($project['tags'] as $tag)
                                <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $tag }}</span>
                                @endforeach
                            </div>
                            @endif

                            <!-- Card Footer -->
                            <div class="flex justify-between items-center text-sm">
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-xs">{{ $project['assignee'] }}</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-xs">{{ $project['dueDate'] }}</span>
                                </div>
                            </div>

                            <!-- Card Actions -->
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="flex space-x-2">
                                    <button wire:click="openTaskForm('{{ $column['id'] }}', {{ $project['id'] }})" 
                                            class="flex-1 text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200 transition-colors">
                                        편집
                                    </button>
                                    <button class="text-xs text-blue-600 hover:text-blue-800 px-2 py-1">
                                        상세
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach

                    <!-- Add Card Button -->
                    <button wire:click="openTaskForm('{{ $column['id'] }}')" 
                            class="w-full p-4 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="text-sm">새 작업 추가</span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Task Form Modal -->
        @if($showTaskForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-screen overflow-y-auto">
                <h3 class="text-lg font-semibold mb-4">
                    {{ $editingTask ? '작업 편집' : '새 작업 추가' }}
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">작업 제목</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="작업 제목을 입력하세요">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">설명</label>
                        <textarea 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            rows="3"
                            placeholder="작업 설명을 입력하세요"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach($columns as $col)
                                <option value="{{ $col['id'] }}" {{ $selectedColumn === $col['id'] ? 'selected' : '' }}>
                                    {{ $col['title'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="low">낮음</option>
                                <option value="medium">보통</option>
                                <option value="high">높음</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">담당자</label>
                            <input type="text" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="담당자명">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">마감일</label>
                            <input type="date" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">진행률</label>
                        <div class="flex items-center space-x-3">
                            <input type="range" 
                                   min="0" 
                                   max="100" 
                                   value="0"
                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                            <span class="text-sm text-gray-600 w-12">0%</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">태그</label>
                        <input type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="태그를 쉼표로 구분하여 입력하세요">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button wire:click="closeTaskForm" 
                            class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        취소
                    </button>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        {{ $editingTask ? '수정' : '생성' }}
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
// 간단한 드래그 앤 드롭 기능 (Alpine.js 없이)
document.addEventListener('DOMContentLoaded', function() {
    // 드래그 앤 드롭 기능은 향후 JavaScript 라이브러리 추가 시 구현
    console.log('칸반 보드 로드 완료');
});
</script>