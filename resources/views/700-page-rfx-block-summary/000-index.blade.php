<div>
    <!-- RFX 탭 네비게이션 -->
    @include('100-rfx-tab-navigation')

    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="max-w-full mx-auto px-4">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">블록 요약</h1>
                <p class="text-gray-600">AI를 활용한 문서 블록 요약 기능</p>
            </div>

            @if($selectedDocument)
            <!-- Full Width Layout when Document is Selected -->
            <div class="flex gap-6">
                <!-- Left Sidebar - Document List (Collapsed) -->
                <div class="hidden lg:block w-80 flex-shrink-0">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-4 border-b border-gray-200">
                            <h2 class="text-base font-semibold text-gray-900 mb-3">문서 목록</h2>

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
                                    <!-- AI 분석 버튼 -->
                                    <button
                                        wire:click="generateSummary()"
                                        wire:loading.attr="disabled"
                                        wire:target="generateSummary"
                                        class="text-sm bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg wire:loading.remove wire:target="generateSummary" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                        <svg wire:loading wire:target="generateSummary" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span wire:loading.remove wire:target="generateSummary">AI 분석</span>
                                        <span wire:loading wire:target="generateSummary">분석 중...</span>
                                    </button>

                                    <!-- 목록으로 버튼 -->
                                    <a href="/rfx/block-summary" class="text-sm bg-gray-100 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-200">
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
                                <div>
                                    <span class="text-gray-500">페이지:</span>
                                    <span class="ml-1 font-medium">{{ $selectedDocument['pageCount'] }}페이지</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">블록 수:</span>
                                    <span class="ml-1 font-medium">{{ count($blocks) }}개</span>
                                </div>
                            </div>
                        </div>

                        <!-- AI 요약 결과 -->
                        @if($summary)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <span>AI 분석 결과</span>
                                </h3>
                            </div>

                            <div class="prose max-w-none">
                                <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                                    <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $summary['summary'] ?? '' }}</p>
                                </div>

                                @if(isset($summary['key_points']) && count($summary['key_points']) > 0)
                                <div class="mt-4">
                                    <h4 class="text-md font-semibold text-gray-900 mb-2">주요 포인트</h4>
                                    <ul class="space-y-2">
                                        @foreach($summary['key_points'] as $point)
                                        <li class="flex items-start">
                                            <svg class="w-5 h-5 text-purple-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                            </svg>
                                            <span class="text-gray-700">{{ $point }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- 페이지별 블록 목록 -->
                        @if($savedSummary)
                            <!-- 저장된 데이터가 있는 경우: DB에서 로드한 페이지별 섹션 표시 -->
                            @foreach($savedSummary->pageSummaries as $pageSummary)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>페이지 {{ $pageSummary->page_number }} ({{ $pageSummary->block_count }}개 블록)</span>
                                    </h3>
                                </div>

                                <!-- 페이지 AI 요약 -->
                                <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-2">페이지 AI 요약</h4>
                                    <p class="text-sm text-gray-800 leading-relaxed">{{ $pageSummary->ai_summary }}</p>
                                </div>

                                <!-- 섹션 목록 -->
                                <div class="space-y-3">
                                    @foreach($pageSummary->sectionAnalyses as $section)
                                    <div class="p-4 bg-gray-50 rounded-lg border-l-4 border-purple-400">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0">
                                                <span class="text-lg">{{ $section->asset_type_icon }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-gray-900 mb-2">{{ $section->section_title }}</div>

                                                <!-- 2단 그리드: 원문 (이미지 포함) | 교정 (편집 가능) -->
                                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                                    <!-- 좌측: 버전 선택 시 교정 내용 표시 -->
                                                    <div>
                                                        @if($section->versions->count() > 1)
                                                        <!-- 버전이 2개 이상이면 드롭다운으로 표시 -->
                                                        <h5 class="text-xs font-semibold text-gray-700 mb-1 flex items-center justify-between">
                                                            <div class="flex items-center">
                                                                <svg class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                버전 선택
                                                            </div>
                                                            <span class="text-xs text-gray-500">{{ $section->versions->count() }}개 버전</span>
                                                        </h5>
                                                        <div class="space-y-2">
                                                            <!-- 버전 드롭다운 -->
                                                            @php
                                                                $currentVersionId = $section->currentVersion ? $section->currentVersion->id : null;
                                                            @endphp
                                                            <select
                                                                wire:model.live="selectedVersions.{{ $section->id }}"
                                                                class="w-full p-2 bg-blue-50 rounded text-xs text-gray-700 border border-blue-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                            >
                                                                @foreach($section->versions->sortByDesc('created_at') as $version)
                                                                <option value="{{ $version->id }}" {{ (!isset($selectedVersions[$section->id]) && $version->is_current) ? 'selected' : '' }}>
                                                                    {{ $version->version_display_name }}
                                                                    @if($version->is_current) (기본) @endif
                                                                </option>
                                                                @endforeach
                                                            </select>

                                                            <!-- 원문 내용 표시 (좌측) - 버전과 무관하게 항상 original_content -->
                                                            <div class="p-2 bg-blue-50 rounded border border-blue-200">
                                                                <!-- 블록 이미지 -->
                                                                @if(isset($section->block_id) && $section->block_id !== 'unknown')
                                                                <div class="mb-2">
                                                                    <img
                                                                        src="{{ config('services.ocr.base_url') }}/requests/{{ $selectedDocument['id'] }}/pages/{{ $pageSummary->page_number }}/blocks/{{ $section->block_id }}/image"
                                                                        alt="블록 이미지"
                                                                        class="max-w-full h-auto rounded border border-blue-200"
                                                                        onerror="this.style.display='none'"
                                                                    >
                                                                </div>
                                                                @endif
                                                                <!-- 원문 텍스트 (버전 선택과 무관하게 항상 동일) -->
                                                                <div class="text-xs text-gray-700 whitespace-pre-wrap">
                                                                    {{ $section->original_content }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @else
                                                        <!-- 버전이 1개만 있으면 원문 표시 -->
                                                        <h5 class="text-xs font-semibold text-gray-700 mb-1 flex items-center">
                                                            <svg class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            원문
                                                        </h5>
                                                        <div class="p-2 bg-blue-50 rounded">
                                                            <!-- 블록 이미지 -->
                                                            @if(isset($section->block_id) && $section->block_id !== 'unknown')
                                                            <div class="mb-2">
                                                                <img
                                                                    src="{{ config('services.ocr.base_url') }}/requests/{{ $selectedDocument['id'] }}/pages/{{ $pageSummary->page_number }}/blocks/{{ $section->block_id }}/image"
                                                                    alt="블록 이미지"
                                                                    class="max-w-full h-auto rounded border border-blue-200"
                                                                    onerror="this.style.display='none'"
                                                                >
                                                            </div>
                                                            @endif
                                                            <!-- 원문 텍스트 -->
                                                            <div class="text-xs text-gray-700">
                                                                {{ $section->original_content }}
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>

                                                    <!-- 우측: 교정 입력 (편집 가능) -->
                                                    <div>
                                                        <h5 class="text-xs font-semibold text-gray-700 mb-1 flex items-center justify-between">
                                                            <div class="flex items-center">
                                                                <svg class="w-4 h-4 text-green-500 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                                교정
                                                            </div>
                                                            @if($section->versions->count() > 1)
                                                            <span class="text-xs text-gray-500">{{ $section->versions->count() }}개 버전</span>
                                                            @endif
                                                        </h5>
                                                        <div class="space-y-2">
                                                            <!-- 교정 내용 입력 (편집 가능) -->
                                                            <textarea
                                                                wire:model.defer="editingContent.{{ $section->id }}"
                                                                placeholder="교정 내용을 입력하세요..."
                                                                class="w-full p-2 bg-green-50 rounded border border-green-200 text-xs text-gray-700 min-h-[100px] focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                                rows="5"
                                                            ></textarea>

                                                            <!-- 저장 버튼 + 버전 정보 -->
                                                            <div class="flex items-center justify-between">
                                                                <button
                                                                    wire:click="saveCorrection('{{ $section->id }}')"
                                                                    class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors flex items-center space-x-1">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                    </svg>
                                                                    <span>저장 (기본)</span>
                                                                </button>

                                                                @if($section->currentVersion)
                                                                <div class="text-xs text-green-600">
                                                                    {{ \App\Helpers\VersionHelper::formatVersion($section->current_version_number) }}
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach

                        @elseif(count($pageGroups) > 0)
                            <!-- 저장되지 않은 데이터: 페이지별로 그룹화된 블록만 표시 -->
                            @foreach($pageGroups as $pageGroup)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center space-x-2 mb-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>페이지 {{ $pageGroup['page_number'] }} ({{ $pageGroup['block_count'] }}개 블록)</span>
                                </h3>

                                <div class="space-y-3">
                                    @foreach($pageGroup['blocks'] as $index => $block)
                                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex items-start gap-3">
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">
                                                #{{ $index + 1 }}
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm text-gray-900 font-medium mb-1">
                                                    {{ $block['text'] ?? '(텍스트 없음)' }}
                                                </div>
                                                <div class="flex items-center space-x-3 text-xs text-gray-600">
                                                    @if(isset($block['block_type']))
                                                    <span class="px-2 py-0.5 bg-gray-200 rounded">{{ $block['block_type'] }}</span>
                                                    @endif
                                                    @if(isset($block['confidence']))
                                                    <span class="px-2 py-0.5 rounded {{ $block['confidence'] >= 0.9 ? 'bg-green-100 text-green-800' : ($block['confidence'] >= 0.7 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                        신뢰도 {{ round($block['confidence'] * 100, 1) }}%
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach

                        @else
                            <!-- 블록 데이터가 없는 경우 -->
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                <div class="text-center text-gray-500 py-8">
                                    블록 데이터가 없습니다
                                </div>
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
                            <h2 class="text-lg font-semibold text-gray-900">문서 목록</h2>

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
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">문서를 선택하세요</h3>
                        <p class="text-gray-500">왼쪽 목록에서 블록 요약을 생성할 문서를 선택하세요.</p>
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
