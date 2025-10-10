<div class="mb-8">
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center">
                    <span class="text-white text-xl">🧠</span>
                </div>
                <div>
                    <div class="flex items-center space-x-4">
                        <h1 class="text-2xl font-bold text-gray-900">AI 문서 분석 결과</h1>
                        <div class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-full">
                            v1.0
                        </div>
                    </div>
                    <p class="text-gray-600">팔란티어 온톨로지 기반 에셋 분류 및 분석</p>
                    <div class="flex items-center space-x-3 mt-2">
                        <p class="text-sm text-indigo-600">{{ $requestInfo['file_name'] ?? 'N/A' }}</p>
                        <span class="text-gray-400">|</span>
                        <p class="text-sm text-gray-500">{{ strtoupper($requestInfo['file_type'] ?? 'N/A') }}</p>
                        <span class="text-gray-400">|</span>
                        <p class="text-sm text-gray-500">{{ $requestInfo['created_at'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="text-right space-y-2">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('rfx.ai-analysis.detail', $analysisRequestId) }}"
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        OCR 통계 보기
                    </a>
                    <a href="{{ route('rfx.ai-analysis') }}"
                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                        목록으로
                    </a>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">섹션 표시</div>
                    <div class="flex items-center justify-end space-x-2">
                        <span class="text-sm font-medium text-indigo-600">{{ count($assets) }}개</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
