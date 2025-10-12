<div class="overflow-x-auto">
<table class="w-full text-sm">
    <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">상태</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Job</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">상세정보</th>

            @if(config('queue-monitor.ui.show_custom_data'))
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">커스텀 데이터</th>
            @endif

            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">진행률</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">소요시간</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">시작시간</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">에러</th>

            @if(config('queue-monitor.ui.allow_deletion') || config('queue-monitor.ui.allow_retry'))
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">작업</th>
            @endif
        </tr>
    </thead>

    <tbody class="bg-white divide-y divide-gray-200">

        @forelse($jobs as $job)

            <tr class="hover:bg-gray-50">

                <td class="px-4 py-4 whitespace-nowrap">
                    @include('queue-monitor::partials.job-status', ['status' => $job->status])
                </td>

                <td class="px-4 py-4">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $job->getBaseName() }}
                    </div>
                    <div class="text-xs text-gray-500">
                        #{{ $job->job_id }}
                    </div>
                </td>

                <td class="px-4 py-4">

                    <div class="text-xs text-gray-600 mb-1">
                        <span class="font-medium">큐:</span> {{ $job->queue }}
                    </div>
                    <div class="text-xs text-gray-600">
                        <span class="font-medium">시도:</span> {{ $job->attempt }}
                    </div>
                    @if($job->retried)
                        <div class="mt-2">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">재시도됨</span>
                        </div>
                    @endif
                </td>

                @if(config('queue-monitor.ui.show_custom_data'))
                    <td class="px-4 py-4">
                        <textarea rows="4"
                                  class="w-64 text-xs p-2 border border-gray-300 rounded-lg bg-gray-50 font-mono"
                                  readonly>{{ json_encode($job->getData(), JSON_PRETTY_PRINT) }}</textarea>
                    </td>
                @endif

                <td class="px-4 py-4">

                    @if($job->progress !== null)
                        <div class="w-32">
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-600" style="width: {{ $job->progress }}%"></div>
                            </div>
                            <div class="text-center mt-1 text-xs font-semibold text-gray-700">
                                {{ $job->progress }}%
                            </div>
                        </div>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>

                <td class="px-4 py-4 text-sm text-gray-600">
                    {{ $job->getElapsedInterval()->format('%H:%I:%S') }}
                </td>

                <td class="px-4 py-4 text-sm text-gray-600">
                    {{ $job->started_at?->diffForHumans() }}
                </td>

                <td class="px-4 py-4">
                    @if($job->status != \romanzipp\QueueMonitor\Enums\MonitorStatus::SUCCEEDED && $job->exception_message !== null)
                        <textarea rows="3" class="w-64 text-xs p-2 border border-red-300 rounded-lg bg-red-50 text-red-800 font-mono" readonly>{{ $job->exception_message }}</textarea>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>

                @if(config('queue-monitor.ui.allow_deletion') || config('queue-monitor.ui.allow_retry'))
                    <td class="px-4 py-4">
                        <div class="flex gap-2">
                            @if(config('queue-monitor.ui.allow_retry') && $job->canBeRetried())
                                <form action="{{ route('queue-monitor::retry', [$job]) }}" method="post">
                                    @csrf
                                    @method('patch')
                                    <button class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-all">
                                        재시도
                                    </button>
                                </form>
                            @endif
                            @if(config('queue-monitor.ui.allow_deletion') && $job->isFinished())
                                <form action="{{ route('queue-monitor::destroy', [$job]) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-lg transition-all">
                                        삭제
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                @endif

            </tr>

        @empty
            <tr>
                <td colspan="100" class="px-4 py-12">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">작업 없음</h3>
                        <p class="mt-1 text-sm text-gray-500">큐에 등록된 작업이 없습니다.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>

<!-- 페이지네이션 -->
<div class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4">
    <div class="text-sm text-gray-600">
        @if($jobs->total() > 0)
            <span class="font-medium">{{ $jobs->firstItem() }}</span> -
            <span class="font-medium">{{ $jobs->lastItem() }}</span> /
        @endif
        전체 <span class="font-medium">{{ $jobs->total() }}</span>개
    </div>

    <div class="flex gap-2">
        <a class="px-4 py-2 text-sm font-medium rounded-lg transition-all @if(!$jobs->onFirstPage()) bg-gray-100 hover:bg-gray-200 text-gray-700 @else bg-gray-50 text-gray-400 cursor-not-allowed @endif"
           @if(!$jobs->onFirstPage()) href="{{ $jobs->previousPageUrl() }}" @endif>
            이전
        </a>
        <a class="px-4 py-2 text-sm font-medium rounded-lg transition-all @if($jobs->hasMorePages()) bg-gray-100 hover:bg-gray-200 text-gray-700 @else bg-gray-50 text-gray-400 cursor-not-allowed @endif"
           @if($jobs->hasMorePages()) href="{{ $jobs->url($jobs->currentPage() + 1) }}" @endif>
            다음
        </a>
    </div>
</div>
