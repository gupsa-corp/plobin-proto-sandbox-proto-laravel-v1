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

                            <!-- Card Header -->
                            <div class="flex items-start justify-between mb-2 p-4 pb-0 drag-handle cursor-move">
                                <h4 class="font-medium text-gray-900 text-sm">{{ $project['title'] }}</h4>
                                <span class="px-2 py-1 text-xs rounded-full font-medium flex-shrink-0 ml-2
                                    {{ $project['priority'] === 'high' ? 'bg-red-100 text-red-800' :
                                       ($project['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                                       'bg-green-100 text-green-800') }}">
                                    {{ $project['priority'] === 'high' ? '높음' :
                                       ($project['priority'] === 'medium' ? '보통' : '낮음') }}
                                </span>
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
    <script>
        document.addEventListener('livewire:init', function() {
            // 모든 칸반 컬럼에 SortableJS 적용
            document.querySelectorAll('.kanban-column').forEach(column => {
                new Sortable(column, {
                    group: 'kanban',
                    animation: 150,
                    ghostClass: 'opacity-50',
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

                        // Livewire 메서드 호출
                        @this.call('moveTask', parseInt(taskId), fromColumn, toColumn);
                    }
                });
            });

            console.log('SortableJS initialized');
        });
    </script>
    @endpush
</div>
