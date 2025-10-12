<!-- RFX 네비게이션 -->
<div class="bg-white border-b">
    <div class="px-6 py-4">
        <nav class="flex space-x-8" aria-label="RFX Navigation">
            <a href="/rfx/dashboard" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                대시보드
            </a>

            <a href="/rfx/upload" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                업로드
            </a>

            <a href="/rfx/files" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                파일 관리
            </a>

            <a href="/rfx/analysis" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                분석 결과
            </a>

            <a href="/rfx/ai-analysis" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                AI 분석
            </a>
        </nav>
    </div>
</div>

<div class="bg-white shadow">
    <div class="max-w-[1920px] mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- 문서 정보 -->
            <div>
                <h1 class="text-2xl font-bold text-gray-900">📄 {{ $documentName }}</h1>
                <p class="text-sm text-gray-500 mt-1">섹션 뷰 - 페이지 {{ $pageNumber }}</p>
            </div>

            <!-- 네비게이션 버튼 -->
            <div class="flex items-center space-x-4">
                <!-- 블록 뷰로 전환 -->
                <a href="{{ route('rfx.documents.blocks', ['documentId' => $documentId]) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                    블록 뷰로 전환
                </a>

                <!-- 뒤로 가기 -->
                <a href="{{ route('rfx.analysis.detail', ['documentId' => $documentId]) }}"
                   class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition">
                    분석 페이지로
                </a>
            </div>
        </div>

        <!-- 문서 작업 탭 -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center space-x-2 flex-wrap">
                <!-- 블록 뷰 -->
                <a href="{{ route('rfx.documents.blocks', ['documentId' => $documentId]) }}"
                   class="text-sm bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span>블록 뷰</span>
                </a>

                <!-- 섹션 뷰 (현재 활성) -->
                <a href="{{ route('rfx.documents.sections', ['documentId' => $documentId]) }}"
                   class="text-sm bg-teal-600 text-white px-3 py-1.5 rounded hover:bg-teal-700 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>섹션 뷰</span>
                </a>

                <!-- 분석 페이지로 -->
                <a href="{{ route('rfx.analysis.detail', ['documentId' => $documentId]) }}"
                   class="text-sm bg-gray-600 text-white px-3 py-1.5 rounded hover:bg-gray-700 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <span>분석 상세</span>
                </a>

                <!-- 목록으로 -->
                <a href="/rfx/analysis"
                   class="text-sm bg-gray-100 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-200">
                    목록으로
                </a>
            </div>
        </div>

        <!-- 페이지 네비게이션 (TODO: 다중 페이지 지원 시) -->
    </div>
</div>
