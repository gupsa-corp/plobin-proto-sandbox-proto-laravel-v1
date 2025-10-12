<form action="" method="get">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label for="filter_name" class="block text-sm font-medium text-gray-700 mb-2">
                Job 이름
            </label>
            <input type="text"
                   id="filter_name"
                   name="name"
                   value="{{ $filters['name'] ?? null }}"
                   placeholder="ExampleJob"
                   class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        @if(config('queue-monitor.ui.show_custom_data'))
            <div>
                <label for="filter_custom_data" class="block text-sm font-medium text-gray-700 mb-2">
                    커스텀 데이터
                </label>
                <input type="text"
                       id="filter_custom_data"
                       name="custom_data"
                       value="{{ $filters['custom_data'] ?? null }}"
                       placeholder="커스텀 데이터"
                       class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        @endif

        <div>
            <label for="filter_status" class="block text-sm font-medium text-gray-700 mb-2">
                상태
            </label>
            <select name="status"
                    id="filter_status"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option @if($filters['status'] === null) selected @endif value="">전체</option>
                @foreach($statuses as $status => $statusName)
                    <option @if($filters['status'] === $status) selected @endif value="{{ $status }}">
                        {{ $statusName }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="filter_queues" class="block text-sm font-medium text-gray-700 mb-2">
                큐
            </label>
            <select name="queue"
                    id="filter_queues"
                    class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="all">전체</option>
                @foreach($queues as $queue)
                    <option @if($filters['queue'] === $queue) selected @endif value="{{ $queue }}">
                        {{ e($queue) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all">
            필터 적용
        </button>

        <a href="{{ route('queue-monitor::index') }}"
           class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-all">
            초기화
        </a>
    </div>
</form>
