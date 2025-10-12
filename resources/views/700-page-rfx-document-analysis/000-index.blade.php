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
                    <!-- 스냅샷 조회 중 알림 배너 -->
                    @if($viewingSnapshot)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                            </svg>
                            <span class="text-sm text-yellow-800 font-medium">
                                과거 버전을 조회 중입니다
                                @if($selectedSnapshotId)
                                    @foreach($snapshots as $s)
                                        @if($s['id'] === $selectedSnapshotId)
                                            ({{ \Carbon\Carbon::createFromFormat('YmdHis', $s['version_timestamp'])->format('Y-m-d H:i:s') }})
                                        @endif
                                    @endforeach
                                @endif
                            </span>
                        </div>
                        <button wire:click="viewLatest()" class="text-sm text-yellow-800 underline hover:text-yellow-900 font-medium">
                            최신 버전으로 돌아가기
                        </button>
                    </div>
                    @endif

                    <!-- Document Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $selectedDocument['fileName'] }}</h3>
                            <div class="flex items-center space-x-2">
                                <!-- 버전 히스토리 드롭다운 -->
                                @if($snapshots && count($snapshots) > 0)
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            class="text-sm bg-purple-600 text-white px-3 py-1.5 rounded hover:bg-purple-700 flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>버전 ({{ count($snapshots) }})</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="open"
                                         x-cloak
                                         @click.away="open = false"
                                         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-10 max-h-96 overflow-y-auto">

                                        <!-- 최신 버전 (OCR API) -->
                                        <div class="p-3 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                                            <button
                                                wire:click="viewLatest()"
                                                @click="open = false"
                                                class="w-full text-left {{ !$viewingSnapshot ? 'ring-2 ring-green-500' : '' }} p-3 rounded-lg hover:bg-green-100 transition">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                                        </svg>
                                                        <span class="font-semibold text-green-800">현재 버전</span>
                                                    </div>
                                                    <span class="px-2 py-1 text-xs bg-green-600 text-white rounded-full font-medium">최신</span>
                                                </div>
                                                <div class="text-xs text-green-700 mt-1 ml-7">OCR API에서 실시간 조회</div>
                                            </button>
                                        </div>

                                        <!-- 과거 스냅샷들 -->
                                        <div class="divide-y divide-gray-100">
                                            @foreach($snapshots as $snapshot)
                                            <button
                                                wire:click="selectSnapshot('{{ $snapshot['id'] }}')"
                                                @click="open = false"
                                                class="w-full text-left p-4 hover:bg-gray-50 transition
                                                       {{ $selectedSnapshotId === $snapshot['id'] ? 'bg-blue-50 border-l-4 border-l-blue-600' : '' }}">

                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center space-x-2">
                                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                            </svg>
                                                            <span class="font-medium text-gray-900">
                                                                {{ \Carbon\Carbon::createFromFormat('YmdHis', $snapshot['version_timestamp'])->format('Y년 m월 d일 H:i:s') }}
                                                            </span>
                                                        </div>

                                                        @if($snapshot['snapshot_reason'])
                                                        <div class="text-xs text-gray-600 mt-1 ml-6 italic">
                                                            "{{ $snapshot['snapshot_reason'] }}"
                                                        </div>
                                                        @endif

                                                        <div class="flex items-center space-x-3 mt-2 ml-6">
                                                            <span class="text-xs text-gray-500">
                                                                {{ \Carbon\Carbon::parse($snapshot['created_at'])->diffForHumans() }}
                                                            </span>
                                                            @if($snapshot['created_by_user_id'])
                                                            <span class="text-xs text-gray-500">
                                                                · 사용자 #{{ $snapshot['created_by_user_id'] }}
                                                            </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="ml-3">
                                                        <span class="px-2 py-1 text-xs rounded-full
                                                            {{ $snapshot['version_type'] === 'original' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                            {{ $snapshot['version_type'] === 'original' ? '원본' : '재분석' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </button>
                                            @endforeach
                                        </div>

                                        @if(count($snapshots) === 0)
                                        <div class="p-8 text-center text-gray-500">
                                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <p class="text-sm">이전 버전이 없습니다</p>
                                            <p class="text-xs mt-1">재분석 시 자동으로 버전이 저장됩니다</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                <!-- AI 분석 생성 버튼 -->
                                <button wire:click="generateAiAnalysis('{{ $selectedDocument['id'] }}')"
                                        class="text-sm bg-purple-600 text-white px-3 py-1.5 rounded hover:bg-purple-700 flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <span>AI 분석 생성</span>
                                </button>

                                <!-- 재분석 버튼 -->
                                <button wire:click="regenerateAnalysis('{{ $selectedDocument['id'] }}')"
                                        class="text-sm bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700 flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span>재분석</span>
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

                    <!-- OCR 상세 정보 (Raw Data) -->
                    @if(isset($analysisResult['ocrRawData']) && $analysisResult['ocrRawData'])
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">OCR 상세 정보</h3>
                            <button
                                x-data="{ copied: false }"
                                @click="navigator.clipboard.writeText(JSON.stringify(@js($analysisResult['ocrRawData']), null, 2)); copied = true; setTimeout(() => copied = false, 2000)"
                                class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="copied ? 'Copied!' : 'JSON 복사'"></span>
                            </button>
                        </div>

                        <div x-data="{ expanded: false }" class="space-y-4">
                            <!-- 문서 메타데이터 -->
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                                @if(isset($analysisResult['ocrRawData']['request_id']))
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">Request ID</div>
                                    <div class="text-sm font-mono text-gray-900 truncate">{{ $analysisResult['ocrRawData']['request_id'] }}</div>
                                </div>
                                @endif
                                @if(isset($analysisResult['ocrRawData']['original_filename']))
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">파일명</div>
                                    <div class="text-sm font-medium text-gray-900">{{ $analysisResult['ocrRawData']['original_filename'] }}</div>
                                </div>
                                @endif
                                @if(isset($analysisResult['ocrRawData']['file_type']))
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">파일 타입</div>
                                    <div class="text-sm font-medium text-gray-900 uppercase">{{ $analysisResult['ocrRawData']['file_type'] }}</div>
                                </div>
                                @endif
                                @if(isset($analysisResult['ocrRawData']['total_pages']))
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">총 페이지</div>
                                    <div class="text-sm font-medium text-gray-900">{{ $analysisResult['ocrRawData']['total_pages'] }}페이지</div>
                                </div>
                                @endif
                                @if(isset($analysisResult['ocrRawData']['total_blocks']))
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">총 블록 수</div>
                                    <div class="text-sm font-medium text-gray-900">{{ $analysisResult['ocrRawData']['total_blocks'] }}개</div>
                                </div>
                                @endif
                                @if(isset($analysisResult['ocrRawData']['completed_at']))
                                <div>
                                    <div class="text-xs text-gray-500 mb-1">완료 시간</div>
                                    <div class="text-sm font-medium text-gray-900">{{ date('Y-m-d H:i:s', strtotime($analysisResult['ocrRawData']['completed_at'])) }}</div>
                                </div>
                                @endif
                            </div>

                            <!-- 페이지별 블록 정보 -->
                            @if(isset($analysisResult['ocrRawData']['pages']) && count($analysisResult['ocrRawData']['pages']) > 0)
                            <div class="space-y-3">
                                <h4 class="text-sm font-semibold text-gray-900">페이지별 텍스트 블록</h4>
                                @foreach($analysisResult['ocrRawData']['pages'] as $page)
                                <div x-data="{ open: false }" class="border border-gray-200 rounded-lg">
                                    <button
                                        @click="open = !open"
                                        class="w-full px-4 py-3 flex items-center justify-between hover:bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-full text-sm font-semibold">
                                                {{ $page['page_number'] ?? 'N/A' }}
                                            </div>
                                            <div class="text-left">
                                                <div class="text-sm font-medium text-gray-900">페이지 {{ $page['page_number'] ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500">
                                                    블록 {{ count($page['blocks'] ?? []) }}개
                                                    @if(isset($page['average_confidence']))
                                                    · 평균 신뢰도 {{ round($page['average_confidence'] * 100, 1) }}%
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="open" x-collapse class="px-4 pb-4 space-y-2">
                                        @if(isset($page['blocks']) && count($page['blocks']) > 0)
                                            @foreach($page['blocks'] as $index => $block)
                                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 transition">
                                                <div class="flex items-start gap-3">
                                                    <!-- 블록 이미지 -->
                                                    @if(isset($block['image_url']))
                                                    <div class="flex-shrink-0">
                                                        <img
                                                            src="{{ $block['image_url'] }}"
                                                            alt="Block #{{ $index + 1 }}"
                                                            class="w-24 h-24 object-contain bg-white border border-gray-300 rounded"
                                                            onerror="this.parentElement.innerHTML='<div class=\'w-24 h-24 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-500\'>이미지 없음</div>'"
                                                        >
                                                    </div>
                                                    @endif

                                                    <!-- 블록 정보 -->
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-start justify-between mb-2">
                                                            <div class="flex items-center space-x-2">
                                                                <span class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-xs font-mono">
                                                                    #{{ $index + 1 }}
                                                                </span>
                                                                @if(isset($block['confidence']))
                                                                <span class="px-2 py-0.5 rounded text-xs font-medium
                                                                    {{ $block['confidence'] >= 0.9 ? 'bg-green-100 text-green-800' : ($block['confidence'] >= 0.7 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                                    신뢰도 {{ round($block['confidence'] * 100, 1) }}%
                                                                </span>
                                                                @endif
                                                            </div>
                                                            @if(isset($block['block_id']))
                                                            <span class="text-xs text-gray-400 font-mono">{{ $block['block_id'] }}</span>
                                                            @endif
                                                        </div>

                                                        <div class="text-sm text-gray-900 mb-2 font-medium">
                                                            {{ $block['text'] ?? '(텍스트 없음)' }}
                                                        </div>

                                                        @if(isset($block['bbox']) || isset($block['font_size']) || isset($block['font_family']))
                                                        <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                                                            @if(isset($block['bbox']))
                                                            <div>
                                                                <span class="text-gray-500">위치:</span>
                                                                <span class="font-mono">x:{{ $block['bbox']['x'] ?? 'N/A' }}, y:{{ $block['bbox']['y'] ?? 'N/A' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="text-gray-500">크기:</span>
                                                                <span class="font-mono">{{ $block['bbox']['width'] ?? 'N/A' }}×{{ $block['bbox']['height'] ?? 'N/A' }}</span>
                                                            </div>
                                                            @endif
                                                            @if(isset($block['font_size']))
                                                            <div>
                                                                <span class="text-gray-500">글꼴 크기:</span>
                                                                <span>{{ $block['font_size'] }}px</span>
                                                            </div>
                                                            @endif
                                                            @if(isset($block['font_family']))
                                                            <div>
                                                                <span class="text-gray-500">글꼴:</span>
                                                                <span>{{ $block['font_family'] }}</span>
                                                            </div>
                                                            @endif
                                                            @if(isset($block['is_bold']) && $block['is_bold'])
                                                            <div>
                                                                <span class="px-1.5 py-0.5 bg-gray-200 rounded text-xs">굵게</span>
                                                            </div>
                                                            @endif
                                                            @if(isset($block['is_italic']) && $block['is_italic'])
                                                            <div>
                                                                <span class="px-1.5 py-0.5 bg-gray-200 rounded text-xs">기울임</span>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="text-center text-gray-500 text-sm py-4">블록 데이터가 없습니다</div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- 펼치기/접기 버튼 -->
                            <button
                                @click="expanded = !expanded"
                                class="w-full py-2 px-4 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 flex items-center justify-center space-x-2">
                                <span x-text="expanded ? '전체 JSON 데이터 접기' : '전체 JSON 데이터 보기'"></span>
                                <svg class="w-4 h-4 transition-transform" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- JSON 데이터 표시 -->
                            <div x-show="expanded" x-collapse class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                                <pre class="text-xs text-green-400 font-mono">{{ json_encode($analysisResult['ocrRawData'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
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
