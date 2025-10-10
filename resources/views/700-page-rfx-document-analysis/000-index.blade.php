<div>
    <!-- RFX 탭 네비게이션 -->
    @include('100-rfx-tab-navigation')

    <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">문서 분석</h1>
            <p class="text-gray-600">AI를 활용한 문서 분석 결과를 확인하세요</p>
        </div>

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
                                wire:model.debounce.300ms="search"
                                placeholder="문서명 검색..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            
                            <select wire:model="statusFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">모든 상태</option>
                                <option value="completed">완료</option>
                                <option value="analyzing">분석중</option>
                                <option value="pending">대기</option>
                                <option value="error">오류</option>
                            </select>
                            
                            <input 
                                type="date" 
                                wire:model="dateFilter"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($documents as $document)
                            <div 
                                wire:click="selectDocument({{ $document['id'] }})"
                                class="p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 {{ $selectedDocument && $selectedDocument['id'] === $document['id'] ? 'ring-2 ring-blue-500 bg-blue-50' : '' }}"
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
                                            @if($document['status'] === 'completed') bg-green-100 text-green-800
                                            @elseif($document['status'] === 'analyzing') bg-yellow-100 text-yellow-800
                                            @elseif($document['status'] === 'error') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            @if($document['status'] === 'completed') 완료
                                            @elseif($document['status'] === 'analyzing') 분석중
                                            @elseif($document['status'] === 'error') 오류
                                            @else 대기
                                            @endif
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

            <!-- Analysis Results -->
            <div class="lg:col-span-2">
                @if($selectedDocument && $analysisResult)
                <div class="space-y-6">
                    <!-- Document Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $selectedDocument['fileName'] }}</h3>
                            <div class="flex items-center space-x-2">
                                <button wire:click="regenerateAnalysis({{ $selectedDocument['id'] }})" class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                    재분석
                                </button>
                                <div class="relative">
                                    <button class="text-sm bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700" onclick="document.getElementById('export-menu-{{ $selectedDocument['id'] }}').classList.toggle('hidden')">
                                        내보내기
                                    </button>
                                    <div id="export-menu-{{ $selectedDocument['id'] }}" class="hidden absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                                        <button wire:click="exportAnalysis({{ $selectedDocument['id'] }}, 'pdf')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">PDF</button>
                                        <button wire:click="exportAnalysis({{ $selectedDocument['id'] }}, 'excel')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Excel</button>
                                        <button wire:click="exportAnalysis({{ $selectedDocument['id'] }}, 'json')" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">JSON</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">상태:</span>
                                <span class="ml-1 px-2 py-1 text-xs rounded-full font-medium
                                    @if($selectedDocument['status'] === 'completed') bg-green-100 text-green-800
                                    @elseif($selectedDocument['status'] === 'analyzing') bg-yellow-100 text-yellow-800
                                    @elseif($selectedDocument['status'] === 'error') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    @if($selectedDocument['status'] === 'completed') 완료
                                    @elseif($selectedDocument['status'] === 'analyzing') 분석중
                                    @elseif($selectedDocument['status'] === 'error') 오류
                                    @else 대기
                                    @endif
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
                @else
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">문서를 선택하세요</h3>
                    <p class="text-gray-500">왼쪽 목록에서 분석 결과를 확인할 문서를 선택하세요.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Success Message -->
        @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-2 text-green-700">{{ session('message') }}</p>
            </div>
        </div>
        @endif
    </div>
    </div>
</div>