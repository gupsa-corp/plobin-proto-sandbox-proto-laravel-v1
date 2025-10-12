<div class="min-h-screen bg-gray-50 p-6"
     x-data="{
         showProjectModal: false
     }">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">칸반 보드</h1>
            <p class="text-gray-600 mt-2">드래그하여 프로젝트 상태를 변경하세요</p>
        </div>

        <!-- Kanban Board -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($columns as $column)
            <div class="bg-white rounded-lg shadow-sm">
                <!-- Column Header -->
                <div class="p-4 border-b {{ $column['borderColor'] }} {{ $column['color'] }}">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900">{{ $column['title'] }}</h3>
                        <span class="bg-gray-100 px-2 py-1 rounded-full text-sm font-medium text-gray-600">
                            {{ count(array_filter($projects, fn($p) => $p['status'] === $column['id'])) }}
                        </span>
                    </div>
                </div>

                <!-- Cards Container -->
                <div class="kanban-column p-4 space-y-3 min-h-[600px]"
                     data-column-id="{{ $column['id'] }}">

                    @foreach($projects as $project)
                        @if($project['status'] === $column['id'])
                        <div class="kanban-card bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow"
                             data-task-id="{{ $project['id'] }}"
                             data-project-id="{{ $project['id'] }}"
                             data-column-id="{{ $column['id'] }}"
                             wire:key="project-{{ $project['id'] }}">

                            <!-- Card Header with Drag Handle -->
                            <div class="flex items-start gap-2 mb-2 p-4 pb-0">
                                <!-- 6-Dot Drag Handle Icon -->
                                <div class="drag-handle cursor-grab active:cursor-grabbing flex-shrink-0 pt-1 group">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-colors pointer-events-none" viewBox="0 0 16 16" fill="currentColor">
                                        <circle cx="4" cy="4" r="1.5"/>
                                        <circle cx="4" cy="8" r="1.5"/>
                                        <circle cx="4" cy="12" r="1.5"/>
                                        <circle cx="8" cy="4" r="1.5"/>
                                        <circle cx="8" cy="8" r="1.5"/>
                                        <circle cx="8" cy="12" r="1.5"/>
                                    </svg>
                                </div>

                                <!-- Card Content -->
                                <div class="flex items-start justify-between flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900 text-sm">{{ $project['title'] }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full font-medium flex-shrink-0 ml-2
                                        {{ $project['priority'] === 'high' ? 'bg-red-100 text-red-800' :
                                           ($project['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                                           'bg-green-100 text-green-800') }}">
                                        {{ $project['priority'] === 'high' ? '높음' :
                                           ($project['priority'] === 'medium' ? '보통' : '낮음') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Clickable Content Area -->
                            <div class="p-4 pt-0 cursor-pointer" wire:click="selectProject({{ $project['id'] }})">
                                <!-- Description -->
                                <p class="text-sm text-gray-600 mb-3">{{ $project['description'] }}</p>

                                <!-- Progress -->
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                                        <span>진행률</span>
                                        <span>{{ $project['progress'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all
                                            {{ $project['progress'] < 30 ? 'bg-red-500' :
                                               ($project['progress'] < 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                            style="width: {{ $project['progress'] }}%"></div>
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ $project['assignee'] }}</span>
                                    <span>{{ $project['dueDate'] }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- 프로젝트 상세 모달 --}}
    @include('700-page-pms-kanban-board.300-project-detail-modal')

    @push('scripts')
    <style>
        /* SortableJS 드래그 상태 스타일 */
        .sortable-ghost {
            opacity: 0.5;
            background-color: #eff6ff;
        }
        .sortable-chosen {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            outline: 2px solid #60a5fa;
            outline-offset: 2px;
        }
        .sortable-drag {
            opacity: 0.75;
            transform: rotate(2deg);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
    </style>
    <script>
        document.addEventListener('livewire:init', function() {
            // 모든 칸반 컬럼에 SortableJS 적용
            document.querySelectorAll('.kanban-column').forEach(column => {
                new Sortable(column, {
                    group: 'kanban',
                    animation: 150,
                    ghostClass: 'sortable-ghost',      // 드롭 위치 표시
                    chosenClass: 'sortable-chosen',    // 선택된 카드
                    dragClass: 'sortable-drag',        // 드래그 중인 카드
                    handle: '.drag-handle',  // 드래그 핸들로만 드래그 가능
                    onEnd: function(evt) {
                        const taskId = evt.item.dataset.taskId;
                        const fromColumn = evt.from.dataset.columnId;
                        const toColumn = evt.to.dataset.columnId;

                        console.log('Task moved:', { taskId, fromColumn, toColumn });

                        // 같은 컬럼 내 이동은 무시
                        if (fromColumn === toColumn) {
                            return;
                        }

                        // Livewire 메서드 호출 후 페이지 새로고침
                        @this.call('moveTask', parseInt(taskId), fromColumn, toColumn)
                            .then(() => {
                                // 서버 업데이트 후 페이지 새로고침
                                window.location.reload();
                            });
                    }
                });
            });

            console.log('SortableJS initialized');
        });
    </script>
    @endpush
</div>
