{{-- 일괄 AI 분석 요청 모달 --}}
<div x-show="showBulkAnalysisModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">🤖 일괄 AI 분석 요청</h3>
            <button @click="showBulkAnalysisModal = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <p class="text-sm text-gray-600">선택된 <span class="font-semibold" x-text="selectedFiles.length"></span>개 파일에 대해 🤖 AI 분석을 요청하시겠습니까?</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">분석 유형</label>
            <select x-model="bulkAnalysisType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="document_analysis">문서 분석</option>
                <option value="summary_analysis">요약 분석</option>
                <option value="detailed_analysis">상세 분석</option>
            </select>
        </div>
        <div class="flex space-x-3">
            <button @click="submitBulkAnalysis()" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">
                🤖 AI 분석 요청
            </button>
            <button @click="showBulkAnalysisModal = false" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400">
                취소
            </button>
        </div>
    </div>
</div>