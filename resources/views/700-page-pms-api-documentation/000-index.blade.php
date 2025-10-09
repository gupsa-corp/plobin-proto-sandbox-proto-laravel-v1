<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">API 문서</h1>
            <p class="text-gray-600 mt-2">PMS 시스템의 API 엔드포인트를 확인하세요</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Search -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
                    <h3 class="font-medium text-gray-900 mb-3">검색</h3>
                    <input type="text" 
                           wire:model.live="searchTerm" 
                           placeholder="API 검색..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Categories -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <h3 class="font-medium text-gray-900 mb-3">카테고리</h3>
                    <nav class="space-y-2">
                        <button wire:click="selectCategory('all')" 
                                class="w-full text-left px-3 py-2 rounded-md transition-colors {{ $selectedCategory === 'all' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            전체
                        </button>
                        <button wire:click="selectCategory('dashboard')" 
                                class="w-full text-left px-3 py-2 rounded-md transition-colors {{ $selectedCategory === 'dashboard' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            대시보드
                        </button>
                        <button wire:click="selectCategory('projects')" 
                                class="w-full text-left px-3 py-2 rounded-md transition-colors {{ $selectedCategory === 'projects' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            프로젝트
                        </button>
                        <button wire:click="selectCategory('users')" 
                                class="w-full text-left px-3 py-2 rounded-md transition-colors {{ $selectedCategory === 'users' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            사용자
                        </button>
                        <button wire:click="selectCategory('reports')" 
                                class="w-full text-left px-3 py-2 rounded-md transition-colors {{ $selectedCategory === 'reports' ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            보고서
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                @if($selectedApi)
                <!-- API Detail -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <!-- Header -->
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">{{ $selectedApi['name'] }}</h2>
                                <p class="text-gray-600 mt-1">{{ $selectedApi['description'] }}</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="px-3 py-1 text-sm font-medium rounded-full
                                    @if($selectedApi['method'] === 'GET') bg-green-100 text-green-800
                                    @elseif($selectedApi['method'] === 'POST') bg-blue-100 text-blue-800
                                    @elseif($selectedApi['method'] === 'PUT') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $selectedApi['method'] }}
                                </span>
                                <button wire:click="$set('selectedApi', null)" 
                                        class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Endpoint -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">엔드포인트</h3>
                            <div class="bg-gray-900 rounded-lg p-4">
                                <code class="text-green-400">{{ $selectedApi['method'] }} {{ $selectedApi['endpoint'] }}</code>
                            </div>
                        </div>

                        <!-- Parameters -->
                        @if(!empty($selectedApi['parameters']))
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">파라미터</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">이름</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">타입</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">필수</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">설명</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($selectedApi['parameters'] as $param)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <code class="text-sm text-blue-600">{{ $param['name'] }}</code>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $param['type'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                                    {{ $param['required'] ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $param['required'] ? '필수' : '선택' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $param['description'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <!-- Response -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">응답 예시</h3>
                            <div class="bg-gray-900 rounded-lg p-4">
                                <pre class="text-green-400 text-sm overflow-x-auto"><code>{{ json_encode($selectedApi['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                            </div>
                        </div>

                        <!-- Try It Out -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-3">테스트</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">요청 URL</label>
                                    <input type="text" 
                                           value="{{ config('app.url') }}{{ $selectedApi['endpoint'] }}"
                                           readonly
                                           class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md text-sm">
                                </div>
                                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    요청 보내기
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <!-- API List -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">API 목록</h2>
                        <p class="text-gray-600 mt-1">{{ count($apis) }}개의 API가 있습니다</p>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach($apis as $api)
                        <div class="p-6 hover:bg-gray-50 cursor-pointer transition-colors" 
                             wire:click="selectApi({{ $api['id'] }})">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <span class="px-2 py-1 text-xs font-medium rounded
                                            @if($api['method'] === 'GET') bg-green-100 text-green-800
                                            @elseif($api['method'] === 'POST') bg-blue-100 text-blue-800
                                            @elseif($api['method'] === 'PUT') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ $api['method'] }}
                                        </span>
                                        <code class="text-sm text-gray-900">{{ $api['endpoint'] }}</code>
                                    </div>
                                    <h3 class="font-medium text-gray-900 mt-2">{{ $api['name'] }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ $api['description'] }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if(empty($apis))
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">API 없음</h3>
                        <p class="mt-1 text-sm text-gray-500">검색 조건에 맞는 API가 없습니다.</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>