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
                        <button wire:click="changeViewMode('Day')"
                                class="px-4 py-2 text-sm {{ $viewMode === 'Day' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            일별
                        </button>
                        <button wire:click="changeViewMode('Week')"
                                class="px-4 py-2 text-sm {{ $viewMode === 'Week' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            주별
                        </button>
                        <button wire:click="changeViewMode('Month')"
                                class="px-4 py-2 text-sm {{ $viewMode === 'Month' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            월별
                        </button>
                    </div>
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

            <!-- Frappe Gantt Chart -->
            <div class="p-4">
                <svg id="gantt-chart" class="w-full"></svg>
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
                        @if(isset($selected['team']) && is_array($selected['team']))
                            @foreach($selected['team'] as $member)
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $member }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h4 class="font-medium text-gray-900 mb-2">설명</h4>
                <p class="text-sm text-gray-600">{{ $selected['description'] ?? '' }}</p>
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

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.css">
<style>
    .gantt .bar-wrapper {
        cursor: pointer;
    }
    .gantt .bar {
        fill: #3b82f6 !important;
    }
    .gantt .bar-progress {
        fill: #1d4ed8 !important;
    }
    .gantt .bar-label {
        fill: #ffffff !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js" defer></script>
<script>
    // Frappe Gantt가 로드될 때까지 대기
    function waitForFrappe(callback) {
        if (typeof Frappe !== 'undefined' && Frappe.Gantt) {
            callback();
        } else {
            setTimeout(() => waitForFrappe(callback), 50);
        }
    }

    document.addEventListener('livewire:init', function() {
        let gantt = null;

        function initGanttChart() {
            const projects = @json($projects);

            // Frappe Gantt 형식으로 데이터 변환
            const tasks = projects.map(project => {
                // 상태에 따른 색상
                let customClass = '';
                if (project.status === 'completed') {
                    customClass = 'bar-milestone';
                } else if (project.status === 'in_progress') {
                    customClass = 'bar-task';
                }

                return {
                    id: 'task-' + project.id,
                    name: project.name,
                    start: project.startDate || '2024-10-01',
                    end: project.endDate || '2024-12-31',
                    progress: project.progress || 0,
                    custom_class: customClass
                };
            });

            // Gantt 차트 초기화
            if (gantt) {
                gantt.refresh(tasks);
            } else {
                gantt = new Frappe.Gantt('#gantt-chart', tasks, {
                    view_mode: '{{ $viewMode }}',
                    language: 'ko',
                    on_click: function(task) {
                        const projectId = parseInt(task.id.replace('task-', ''));
                        @this.call('selectProject', projectId);
                    },
                    on_date_change: function(task, start, end) {
                        const projectId = parseInt(task.id.replace('task-', ''));
                        const startDate = start.toISOString().split('T')[0];
                        const endDate = end.toISOString().split('T')[0];
                        @this.call('updateProjectDates', projectId, startDate, endDate);
                    },
                    on_progress_change: function(task, progress) {
                        // 진행률 변경 시 처리 (선택적)
                        console.log('Progress changed:', task.name, progress);
                    }
                });
            }

            console.log('Frappe Gantt initialized with', tasks.length, 'projects');
        }

        // Frappe Gantt 라이브러리 로드 대기 후 초기화
        waitForFrappe(function() {
            initGanttChart();

            // Livewire 업데이트 시 재초기화
            Livewire.hook('morph.updated', () => {
                setTimeout(initGanttChart, 100);
            });

            // 뷰 모드 변경 시 차트 업데이트
            Livewire.on('viewModeChanged', (viewMode) => {
                if (gantt) {
                    gantt.change_view_mode(viewMode);
                }
            });
        });
    });
</script>
@endpush
