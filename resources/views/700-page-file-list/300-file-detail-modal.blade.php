{{-- 파일 상세 정보 모달 --}}
<div x-show="showFileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">파일 상세 정보</h3>
            <button @click="showFileModal = false" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div x-show="selectedFile">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">파일명</label>
                    <p class="text-gray-900" x-text="selectedFile?.original_name"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">파일 크기</label>
                    <p class="text-gray-900" x-text="formatFileSize(selectedFile?.file_size)"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">파일 타입</label>
                    <p class="text-gray-900" x-text="getFileTypeName(selectedFile?.mime_type)"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">분석 상태</label>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                          :class="getStatusClass(selectedFile?.analysis_status)"
                          x-text="getStatusText(selectedFile?.analysis_status)"></span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">업로드일</label>
                    <p class="text-gray-900" x-text="formatDate(selectedFile?.created_at)"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">저장된 파일명</label>
                    <p class="text-gray-900 text-xs font-mono" x-text="selectedFile?.file_name"></p>
                </div>
            </div>
            <div x-show="selectedFile?.analysis_completed_at" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">분석 완료 시간</label>
                <p class="text-gray-900" x-text="formatDate(selectedFile?.analysis_completed_at)"></p>
            </div>
            <div x-show="selectedFile?.is_analysis_completed && selectedFile?.document_assets?.length > 0" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">분석 결과</label>
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-700">
                        <span x-text="selectedFile?.document_assets?.length || 0"></span>개의 분석 결과가 생성되었습니다.
                    </p>
                </div>
            </div>
        </div>
        <div class="flex space-x-3">
            <button @click="downloadFile(selectedFile)" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700">
                다운로드
            </button>
            <button @click="requestAnalysis(selectedFile)"
                    :disabled="selectedFile?.analysis_status === 'processing'"
                    :class="selectedFile?.analysis_status === 'processing' ? 'bg-gray-400 cursor-not-allowed' : 'bg-purple-600 hover:bg-purple-700'"
                    class="text-white py-2 px-4 rounded-lg">
                🤖 AI 분석 요청
            </button>
            <button @click="showFileModal = false" class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400">
                닫기
            </button>
        </div>
    </div>
</div>