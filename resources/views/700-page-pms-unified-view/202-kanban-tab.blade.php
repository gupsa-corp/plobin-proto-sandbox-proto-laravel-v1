<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">칸반 보드</h2>

    @php
        $statuses = [
            'planning' => ['title' => '계획중', 'color' => 'bg-gray-100'],
            'in_progress' => ['title' => '진행중', 'color' => 'bg-blue-100'],
            'review' => ['title' => '검토중', 'color' => 'bg-yellow-100'],
            'completed' => ['title' => '완료', 'color' => 'bg-green-100'],
        ];

        $groupedProjects = [];
        foreach($kanbanProjects as $project) {
            $status = $project['status'];
            if (!isset($groupedProjects[$status])) {
                $groupedProjects[$status] = [];
            }
            $groupedProjects[$status][] = $project;
        }
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($statuses as $statusKey => $statusInfo)
            <div class="flex flex-col">
                <!-- 컬럼 헤더 -->
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">{{ $statusInfo['title'] }}</h3>
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-700">
                        {{ count($groupedProjects[$statusKey] ?? []) }}
                    </span>
                </div>

                <!-- 카드 컨테이너 -->
                <div class="flex-1 space-y-2 {{ $statusInfo['color'] }} p-3 rounded-lg min-h-32">
                    @foreach($groupedProjects[$statusKey] ?? [] as $project)
                        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                            <h4 class="font-medium text-sm text-gray-900 mb-1">{{ $project['title'] }}</h4>

                            @if($project['description'])
                                <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ $project['description'] }}</p>
                            @endif

                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500">{{ $project['assignee'] ?? '미할당' }}</span>
                                <span class="px-2 py-1 rounded-full
                                    @if($project['priority'] === 'high') bg-red-100 text-red-800
                                    @elseif($project['priority'] === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800
                                    @endif">
                                    {{ strtoupper($project['priority']) }}
                                </span>
                            </div>

                            @if(isset($project['progress']) && $project['progress'] > 0)
                                <div class="mt-2">
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $project['progress'] }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    @if(empty($groupedProjects[$statusKey]))
                        <div class="text-center py-8 text-gray-400 text-sm">
                            프로젝트 없음
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
