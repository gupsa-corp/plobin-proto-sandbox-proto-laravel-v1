<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">프로젝트 리스트</h2>

    @if(count($projects) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($projects as $project)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-semibold text-gray-900">{{ $project['name'] }}</h3>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($project['priority'] === 'high') bg-red-100 text-red-800
                            @elseif($project['priority'] === 'medium') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ strtoupper($project['priority']) }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $project['description'] }}</p>

                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>{{ $project['assignee'] ?? '미할당' }}</span>
                        <span class="px-2 py-1 rounded-full
                            @if($project['status'] === 'completed') bg-green-100 text-green-800
                            @elseif($project['status'] === 'in_progress') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            @if($project['status'] === 'planning') 계획중
                            @elseif($project['status'] === 'in_progress') 진행중
                            @elseif($project['status'] === 'review') 검토중
                            @elseif($project['status'] === 'completed') 완료
                            @else {{ $project['status'] }}
                            @endif
                        </span>
                    </div>

                    @if($project['progress'] > 0)
                        <div class="mt-3">
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>진행률</span>
                                <span>{{ $project['progress'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project['progress'] }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 text-gray-500">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p>등록된 프로젝트가 없습니다.</p>
        </div>
    @endif
</div>
