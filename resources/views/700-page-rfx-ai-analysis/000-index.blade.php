<div>
    <!-- RFX 탭 네비게이션 -->
    @include('100-rfx-tab-navigation')

    <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">AI 분석 요청 관리</h1>
            <p class="text-gray-600">AI 분석 요청 상태를 모니터링하고 결과를 확인하세요</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Total Requests -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">전체 요청</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">대기중</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Processing -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">처리중</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['processing'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">완료</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                        <select wire:model="statusFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">전체</option>
                            <option value="pending">대기중</option>
                            <option value="processing">처리중</option>
                            <option value="completed">완료</option>
                            <option value="failed">실패</option>
                        </select>
                    </div>

                    <!-- Analysis Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">분석 유형</label>
                        <select wire:model="analysisTypeFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">전체</option>
                            <option value="ocr">OCR 텍스트 추출</option>
                            <option value="classification">문서 분류</option>
                            <option value="summary">요약 생성</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">기간</label>
                        <select wire:model="dateRangeFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">전체</option>
                            <option value="today">오늘</option>
                            <option value="week">최근 7일</option>
                            <option value="month">최근 30일</option>
                        </select>
                    </div>

                    <!-- Auto Refresh -->
                    <div class="flex items-end">
                        <button
                            wire:click="toggleAutoRefresh"
                            class="w-full px-4 py-2 rounded-lg transition-colors {{ $autoRefresh ? 'bg-purple-600 text-white hover:bg-purple-700' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                        >
                            {{ $autoRefresh ? '자동 갱신 ON' : '자동 갱신 OFF' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requests List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <!-- Table Header -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">분석 요청 목록 ({{ count($requests) }}개)</h2>
                    <div class="flex items-center space-x-2">
                        <button wire:click="loadRequests" class="text-sm text-gray-600 hover:text-gray-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Requests Table -->
                @if(count($requests) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">파일명</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">분석 유형</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">상태</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">진행률</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">요청일시</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">작업</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($requests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ $request['id'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request['fileName'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $request['fileType'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">
                                        {{ $request['analysisType'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        @if($request['status'] === 'completed') bg-green-100 text-green-800
                                        @elseif($request['status'] === 'processing') bg-purple-100 text-purple-800
                                        @elseif($request['status'] === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        @if($request['status'] === 'completed') 완료
                                        @elseif($request['status'] === 'processing') 처리중
                                        @elseif($request['status'] === 'failed') 실패
                                        @else 대기중
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $request['progress'] }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $request['progress'] }}%</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request['requestedAt'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('rfx.ai-analysis.detail', $request['id']) }}" class="text-blue-600 hover:text-blue-900">
                                            OCR 통계
                                        </a>
                                        @if($request['status'] === 'completed')
                                        <a href="{{ route('rfx.ai-analysis.assets', ['requestId' => $request['id']]) }}" class="text-indigo-600 hover:text-indigo-900">
                                            섹션 관리
                                        </a>
                                        <button wire:click="viewSummary({{ $request['id'] }})" class="text-purple-600 hover:text-purple-900">
                                            요약
                                        </button>
                                        <button wire:click="downloadResult({{ $request['id'] }})" class="text-green-600 hover:text-green-900">
                                            다운로드
                                        </button>
                                        @endif
                                        @if($request['status'] === 'failed')
                                        <button wire:click="retryAnalysis({{ $request['id'] }})" class="text-orange-600 hover:text-orange-900">
                                            재시도
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">분석 요청이 없습니다</h3>
                    <p class="mt-1 text-sm text-gray-500">파일 목록에서 AI 분석을 시작하세요.</p>
                    <div class="mt-6">
                        <a href="/rfx/files" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                            파일 목록으로 이동
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Detail Modal -->
        @if($selectedRequest)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="viewDetails(null)">
            <div class="relative top-20 mx-auto p-5 border w-3/4 max-w-4xl shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">분석 요청 상세 정보</h3>
                        <button wire:click="viewDetails(null)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Request Info -->
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">요청 ID</label>
                            <p class="text-sm text-gray-900">#{{ $selectedRequest['id'] }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">파일명</label>
                            <p class="text-sm text-gray-900">{{ $selectedRequest['fileName'] }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">분석 유형</label>
                            <p class="text-sm text-gray-900">{{ $selectedRequest['analysisType'] }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">상태</label>
                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                @if($selectedRequest['status'] === 'completed') bg-green-100 text-green-800
                                @elseif($selectedRequest['status'] === 'processing') bg-purple-100 text-purple-800
                                @elseif($selectedRequest['status'] === 'failed') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ $selectedRequest['status'] }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">요청일시</label>
                            <p class="text-sm text-gray-900">{{ $selectedRequest['requestedAt'] }}</p>
                        </div>
                        @if($selectedRequest['completedAt'])
                        <div>
                            <label class="block text-sm font-medium text-gray-700">완료일시</label>
                            <p class="text-sm text-gray-900">{{ $selectedRequest['completedAt'] }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Analysis Result -->
                    @if($selectedRequest['status'] === 'completed' && $selectedRequest['result'])
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">분석 결과</label>
                        <div class="bg-gray-50 rounded-lg p-4 max-h-96 overflow-y-auto">
                            <pre class="text-sm text-gray-900 whitespace-pre-wrap">{{ json_encode($selectedRequest['result'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif

                    <!-- Error Message -->
                    @if($selectedRequest['status'] === 'failed' && $selectedRequest['errorMessage'])
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">오류 메시지</label>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <p class="text-sm text-red-800">{{ $selectedRequest['errorMessage'] }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex justify-end space-x-2">
                        @if($selectedRequest['status'] === 'failed')
                        <button wire:click="retryAnalysis({{ $selectedRequest['id'] }})" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            재시도
                        </button>
                        @endif
                        @if($selectedRequest['status'] === 'completed')
                        <button wire:click="downloadResult({{ $selectedRequest['id'] }})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            결과 다운로드
                        </button>
                        @endif
                        <button wire:click="viewDetails(null)" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            닫기
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Summary Modal -->
        @if($showSummary && $summary)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="closeSummary">
            <div class="relative top-20 mx-auto p-5 border w-3/4 max-w-4xl shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">📊 AI 분석 요약</h3>
                        <button wire:click="closeSummary" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Document Info -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">📄 문서 정보</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">파일명</p>
                                <p class="text-base font-medium text-gray-900">{{ $summary['document_info']['filename'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">파일 형식</p>
                                <p class="text-base font-medium text-gray-900">{{ $summary['document_info']['file_type'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">파일 크기</p>
                                <p class="text-base font-medium text-gray-900">{{ $summary['document_info']['file_size'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">전체 페이지</p>
                                <p class="text-base font-medium text-gray-900">{{ $summary['document_info']['total_pages'] }}페이지</p>
                            </div>
                        </div>
                    </div>

                    <!-- Processing Info -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">⏱️ 처리 정보</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">상태</p>
                                <p class="text-base font-medium text-green-700">{{ $summary['processing_info']['status'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">처리 시간</p>
                                <p class="text-base font-medium text-gray-900">{{ $summary['processing_info']['processing_time'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">시작 시간</p>
                                <p class="text-base font-medium text-gray-900">{{ $summary['processing_info']['created_at'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">완료 시간</p>
                                <p class="text-base font-medium text-gray-900">{{ $summary['processing_info']['completed_at'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quality Metrics -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">📈 품질 지표</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-2">평균 신뢰도</p>
                                <div class="text-3xl font-bold text-purple-600">{{ $summary['quality_metrics']['average_confidence'] }}</div>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-2">텍스트 블록 수</p>
                                <div class="text-3xl font-bold text-indigo-600">{{ $summary['quality_metrics']['total_blocks'] }}</div>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-2">처리된 페이지</p>
                                <div class="text-3xl font-bold text-blue-600">{{ $summary['quality_metrics']['pages_processed'] }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Page Details -->
                    @if(count($summary['page_details']) > 0)
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">📑 페이지별 상세 정보</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">페이지</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">블록 수</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">신뢰도</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">처리 시간</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">품질</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($summary['page_details'] as $page)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $page['page_number'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $page['blocks'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $page['confidence'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $page['processing_time'] }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                                @if($page['quality_grade']['color'] === 'green') bg-green-100 text-green-800
                                                @elseif($page['quality_grade']['color'] === 'blue') bg-blue-100 text-blue-800
                                                @elseif($page['quality_grade']['color'] === 'yellow') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800
                                                @endif">
                                                {{ $page['quality_grade']['grade'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex justify-end space-x-2 mt-6">
                        <button wire:click="closeSummary" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            닫기
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Success/Error Messages -->
        @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-2 text-green-700">{{ session('message') }}</p>
            </div>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg z-50">
            <div class="flex">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-2 text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif
    </div>
    </div>

    <!-- AI 분석 상세 모달 -->
    @include('700-page-rfx-ai-analysis.200-detail-modal')

    <!-- File Download Script -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('download-file', (event) => {
                const content = event[0].content;
                const filename = event[0].filename;

                // Blob 생성
                const blob = new Blob([content], { type: 'application/json' });

                // 다운로드 링크 생성
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();

                // 정리
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            });
        });
    </script>
</div>
