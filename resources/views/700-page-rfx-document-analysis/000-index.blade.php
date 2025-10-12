<div>
    <!-- RFX 탭 네비게이션 -->
    @include('100-rfx-tab-navigation')

    <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-full mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">문서 분석</h1>
            <p class="text-gray-600">AI를 활용한 문서 분석 결과를 확인하세요</p>
        </div>

        @if($selectedDocument && $analysisResult)
        <!-- Full Width Layout when Document is Selected -->
        <div class="flex gap-6">
            <!-- Left Sidebar - Document List (Collapsed) -->
            <div class="hidden lg:block w-80 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-base font-semibold text-gray-900 mb-3">분석된 문서</h2>

                        <!-- Filters -->
                        <div class="space-y-2">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="문서명 검색..."
                                class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            >

                            <select wire:model.live="statusFilter" class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">모든 상태</option>
                                <option value="completed">완료</option>
                                <option value="processing">분석중</option>
                                <option value="pending">대기</option>
                                <option value="failed">오류</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 max-h-[600px] overflow-y-auto">
                        <div class="space-y-2">
                            @foreach($documents as $document)
                            <div
                                wire:click="selectDocument('{{ $document['id'] }}')"
                                class="p-2 border border-gray-200 rounded cursor-pointer hover:bg-gray-50 {{ $selectedDocument && $selectedDocument['id'] === $document['id'] ? 'ring-2 ring-blue-500 bg-blue-50' : '' }}"
                            >
                                <div class="text-xs font-medium text-gray-900 truncate">{{ $document['fileName'] }}</div>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-xs text-gray-500">{{ $document['documentType'] }}</span>
                                    <span class="px-1.5 py-0.5 text-xs rounded-full font-medium
                                        @if($document['status'] === '완료') bg-green-100 text-green-800
                                        @elseif($document['status'] === '분석중') bg-yellow-100 text-yellow-800
                                        @elseif($document['status'] === '오류') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $document['status'] }}
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content - Full Width -->
            <div class="flex-1 min-w-0">
                <div class="space-y-6">
                    <!-- Document Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $selectedDocument['fileName'] }}</h3>
                            <div class="flex items-center space-x-2">
                                <button wire:click="regenerateAnalysis('{{ $selectedDocument['id'] }}')" class="text-sm bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700">
                                    재분석
                                </button>

                                <!-- 다운로드 메뉴 -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="text-sm bg-green-600 text-white px-3 py-1.5 rounded hover:bg-green-700">
                                        다운로드
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                                        <button wire:click="downloadOriginal('{{ $selectedDocument['id'] }}', 1)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">원본 이미지</button>
                                        <button wire:click="downloadVisualization('{{ $selectedDocument['id'] }}', 1)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">시각화 이미지</button>
                                    </div>
                                </div>

                                <!-- 내보내기 메뉴 -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="text-sm bg-gray-600 text-white px-3 py-1.5 rounded hover:bg-gray-700">
                                        내보내기
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                                        <button wire:click="exportAnalysis('{{ $selectedDocument['id'] }}', 'pdf')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">PDF</button>
                                        <button wire:click="exportAnalysis('{{ $selectedDocument['id'] }}', 'excel')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Excel</button>
                                        <button wire:click="exportAnalysis('{{ $selectedDocument['id'] }}', 'json')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">JSON</button>
                                    </div>
                                </div>

                                <!-- Back Button -->
                                <a href="/rfx/analysis" class="text-sm bg-gray-100 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-200">
                                    목록으로
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">상태:</span>
                                <span class="ml-1 px-2 py-1 text-xs rounded-full font-medium
                                    @if($selectedDocument['status'] === '완료') bg-green-100 text-green-800
                                    @elseif($selectedDocument['status'] === '분석중') bg-yellow-100 text-yellow-800
                                    @elseif($selectedDocument['status'] === '오류') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $selectedDocument['status'] }}
                                </span>
                            </div>
                            @if($selectedDocument['confidence'])
                            <div>
                                <span class="text-gray-500">신뢰도:</span>
                                <span class="ml-1 font-medium">{{ $selectedDocument['confidence'] }}%</span>
                            </div>
                            @endif
                            @if($selectedDocument['keywordCount'])
                            <div>
                                <span class="text-gray-500">키워드:</span>
                                <span class="ml-1 font-medium">{{ $selectedDocument['keywordCount'] }}개</span>
                            </div>
                            @endif
                            <div>
                                <span class="text-gray-500">페이지:</span>
                                <span class="ml-1 font-medium">{{ $selectedDocument['pageCount'] }}페이지</span>
                            </div>
                        </div>
                    </div>

                    <!-- Document Preview - Side by Side -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">문서 미리보기</h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Original Image -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-medium text-gray-700">원본 이미지</h4>
                                    <button wire:click="downloadOriginal('{{ $selectedDocument['id'] }}', 1)" class="text-xs text-blue-600 hover:text-blue-800">
                                        다운로드
                                    </button>
                                </div>
                                <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                                    <img
                                        src="{{ config('services.ocr.base_url') }}/requests/{{ $selectedDocument['id'] }}/pages/1/original"
                                        alt="원본 이미지"
                                        class="w-full h-auto"
                                        onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center text-gray-500\'>이미지를 불러올 수 없습니다</div>'"
                                    >
                                </div>
                            </div>

                            <!-- Visualization Image -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-medium text-gray-700">OCR 분석 결과 (바운딩 박스 표시)</h4>
                                    <button wire:click="downloadVisualization('{{ $selectedDocument['id'] }}', 1)" class="text-xs text-blue-600 hover:text-blue-800">
                                        다운로드
                                    </button>
                                </div>
                                <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                                    <img
                                        src="{{ config('services.ocr.base_url') }}/requests/{{ $selectedDocument['id'] }}/pages/1/visualization"
                                        alt="시각화 이미지"
                                        class="w-full h-auto"
                                        onerror="this.parentElement.innerHTML='<div class=\'p-8 text-center text-gray-500\'>시각화 이미지를 불러올 수 없습니다</div>'"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">문서 요약</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $analysisResult['summary'] }}</p>
                    </div>

                    <!-- Keywords -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">주요 키워드</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($analysisResult['keywords'] as $keyword)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">{{ $keyword }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">문서 분류</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($analysisResult['categories'] as $category)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">{{ $category }}</span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Extracted Data -->
                    @if(count($analysisResult['extractedData']) > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">추출된 데이터</h3>
                        <div class="space-y-2">
                            @foreach($analysisResult['extractedData'] as $key => $value)
                            <div class="flex justify-between py-2 border-b border-gray-100 last:border-b-0">
                                <span class="font-medium text-gray-700">{{ $key }}</span>
                                <span class="text-gray-900">{{ $value }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Recommendations -->
                    @if(count($analysisResult['recommendations']) > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">개선 권장사항</h3>
                        <ul class="space-y-2">
                            @foreach($analysisResult['recommendations'] as $recommendation)
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-yellow-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="text-gray-700">{{ $recommendation }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @else
        <!-- Default 3-Column Layout when No Document Selected -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Documents List -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">분석된 문서</h2>

                        <!-- Filters -->
                        <div class="mt-4 space-y-3">
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="search"
                                placeholder="문서명 검색..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >

                            <select wire:model.live="statusFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">모든 상태</option>
                                <option value="completed">완료</option>
                                <option value="processing">분석중</option>
                                <option value="pending">대기</option>
                                <option value="failed">오류</option>
                            </select>

                            <input
                                type="date"
                                wire:model.live="dateFilter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                    </div>

                    <div class="p-6 max-h-[600px] overflow-y-auto">
                        <div class="space-y-3">
                            @foreach($documents as $document)
                            <div
                                wire:click="selectDocument('{{ $document['id'] }}')"
                                class="p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $document['fileName'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $document['documentType'] }}</div>
                                        @if($document['analyzedAt'])
                                        <div class="text-xs text-gray-400">{{ $document['analyzedAt'] }}</div>
                                        @endif
                                    </div>
                                    <div class="ml-2">
                                        <span class="px-2 py-1 text-xs rounded-full font-medium
                                            @if($document['status'] === '완료') bg-green-100 text-green-800
                                            @elseif($document['status'] === '분석중') bg-yellow-100 text-yellow-800
                                            @elseif($document['status'] === '오류') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $document['status'] }}
                                        </span>
                                    </div>
                                </div>

                                @if($document['confidence'])
                                <div class="mt-2">
                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span>신뢰도</span>
                                        <span>{{ $document['confidence'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                        <div class="bg-blue-600 h-1 rounded-full" style="width: {{ $document['confidence'] }}%"></div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">문서를 선택하세요</h3>
                    <p class="text-gray-500">왼쪽 목록에서 분석 결과를 확인할 문서를 선택하세요.</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Success Message -->
        @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <div class="flex">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-2 text-green-700">{{ session('message') }}</p>
                <button @click="show = false" class="ml-4 text-green-500 hover:text-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        <!-- Error Message -->
        @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg z-50" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="flex">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-2 text-red-700">{{ session('error') }}</p>
                <button @click="show = false" class="ml-4 text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif
    </div>
    </div>
</div>
