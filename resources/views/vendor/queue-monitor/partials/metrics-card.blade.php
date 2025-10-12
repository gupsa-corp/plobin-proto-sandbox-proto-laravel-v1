<div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-all">
    <div class="flex items-center justify-between mb-4">
        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>
        <span class="text-xs text-gray-500" title="최근 {{ config('queue-monitor.ui.metrics_time_frame') ?? 14 }}일">
            최근 {{ config('queue-monitor.ui.metrics_time_frame') ?? 14 }}일
        </span>
    </div>

    <div class="text-sm text-gray-600 font-medium mb-2">
        {{ __($metric->title) }}
    </div>

    <div class="text-3xl font-bold text-gray-900 mb-2">
        {{ $metric->format($metric->value) }}
    </div>

    @if($metric->previousValue !== null)
        <div class="text-sm font-medium {{ $metric->hasChanged() ? ($metric->hasIncreased() ? 'text-green-600' : 'text-red-600') : 'text-gray-600' }}">
            @if($metric->hasChanged())
                @if($metric->hasIncreased())
                    ↑ 이전보다 증가
                @else
                    ↓ 이전보다 감소
                @endif
            @else
                → 변화 없음
            @endif
            ({{ $metric->format($metric->previousValue) }})
        </div>
    @endif
</div>
