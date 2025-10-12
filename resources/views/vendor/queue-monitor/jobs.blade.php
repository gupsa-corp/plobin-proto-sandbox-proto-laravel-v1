<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if(config('queue-monitor.ui.refresh_interval'))
        <meta http-equiv="refresh" content="{{ config('queue-monitor.ui.refresh_interval') }}">
    @endif
    <title>큐 모니터 - Plobin Proto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 min-h-screen">

    <div class="max-w-7xl mx-auto p-6">
        <!-- 헤더 -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">큐 모니터</h1>
                <p class="text-gray-600">작업 큐 관리 및 모니터링</p>
            </div>
            <a href="/" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                대시보드로 돌아가기
            </a>
        </div>

        <!-- 통계 카드 -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            @foreach($metrics->all() as $metric)
                @include('queue-monitor::partials.metrics-card', [
                    'metric' => $metric,
                ])
            @endforeach
        </div>

        <!-- 필터 섹션 -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">필터</h2>
            @include('queue-monitor::partials.filter', [
                'filters' => $filters,
            ])
        </div>

        <!-- 작업 목록 -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">작업 목록</h2>
                @if(config('queue-monitor.ui.allow_purge'))
                    <form action="{{ route('queue-monitor::purge') }}" method="post">
                        @csrf
                        @method('delete')
                        <button class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium rounded-lg transition-all">
                            전체 삭제
                        </button>
                    </form>
                @endif
            </div>

            @include('queue-monitor::partials.table', [
                'jobs' => $jobs,
            ])
        </div>
    </div>

</body>

</html>
