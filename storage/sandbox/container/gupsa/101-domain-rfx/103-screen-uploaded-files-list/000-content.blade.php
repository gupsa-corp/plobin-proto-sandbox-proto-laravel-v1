<div>
    <div x-data="uploadedFilesList()" x-init="init()" class="p-6">
        <!-- 헤더 -->
        <div class="mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">업로드 파일 목록</h1>
                    <p class="text-gray-600 mt-1">업로드된 모든 문서를 관리하고 AI 분석을 요청하세요</p>
                </div>
                <div class="flex space-x-3">
                    <button @click="openUploadModal()"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        파일 업로드
                    </button>
                </div>
            </div>
        </div>

        <!-- 필터 및 검색 -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="파일명 검색..."
                               x-model="searchQuery"
                               @input="handleSearch"
                               class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg">
                        <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
                    </div>
                    <select x-model="statusFilter" @change="loadFiles()" class="px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">모든 상태</option>
                        <option value="uploaded">업로드됨</option>
                        <option value="processing">분석 중</option>
                        <option value="analyzed">분석 완료</option>
                        <option value="error">오류</option>
                    </select>
                    <select x-model="typeFilter" @change="loadFiles()" class="px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">모든 파일 타입</option>
                        <option value="pdf">PDF</option>
                        <option value="doc">DOC/DOCX</option>
                        <option value="xls">XLS/XLSX</option>
                        <option value="md">Markdown</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500" x-text="pagination.total + '개 파일'">로딩 중...</span>
                    <button @click="openUploadModal()"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        📤 파일 업로드
                    </button>
                    <button @click="bulkAnalyze()" :disabled="selectedFiles.length === 0"
                            :class="selectedFiles.length > 0 ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-400 cursor-not-allowed'"
                            class="px-4 py-2 text-white rounded-lg">
                        🤖 AI 분석 (<span x-text="selectedFiles.length"></span>)
                    </button>
                </div>
            </div>
        </div>

        <!-- 파일 목록 테이블 -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" @change="toggleSelectAll($event)" class="rounded">
                            </th>
                            <th @click="sortBy('original_name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                파일명 <span x-show="sortField === 'original_name'" x-text="sortDirection === 'asc' ? '↑' : '↓'"></span>
                            </th>
                            <th @click="sortBy('file_size')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                크기 <span x-show="sortField === 'file_size'" x-text="sortDirection === 'asc' ? '↑' : '↓'"></span>
                            </th>
                            <th @click="sortBy('mime_type')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                타입 <span x-show="sortField === 'mime_type'" x-text="sortDirection === 'asc' ? '↑' : '↓'"></span>
                            </th>
                            <th @click="sortBy('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                상태 <span x-show="sortField === 'status'" x-text="sortDirection === 'asc' ? '↑' : '↓'"></span>
                            </th>
                            <th @click="sortBy('uploaded_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                                업로드일 <span x-show="sortField === 'uploaded_at'" x-text="sortDirection === 'asc' ? '↑' : '↓'"></span>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                액션
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- 로딩 상태 -->
                        <tr x-show="loading">
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                파일을 로딩 중...
                            </td>
                        </tr>

                        <!-- 데이터 없음 -->
                        <tr x-show="!loading && files.length === 0">
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                업로드된 파일이 없습니다.
                            </td>
                        </tr>

                        <!-- 파일 데이터 -->
                        <template x-for="file in files" :key="file.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" :value="file.id" x-model="selectedFiles" class="rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-lg flex items-center justify-center"
                                                 :class="getFileTypeClass(file.mime_type)">
                                                <span class="text-white text-sm font-medium" x-text="getFileIcon(file.mime_type)"></span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900" x-text="file.original_name"></div>
                                            <div class="text-sm text-gray-500" x-text="file.session_id"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" x-text="formatFileSize(file.file_size)"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                          :class="getFileTypeClass(file.mime_type)"
                                          x-text="getFileTypeName(file.mime_type)">
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                          :class="getStatusClass(file.status)"
                                          x-text="getStatusText(file.status)">
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(file.uploaded_at)"></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button @click="viewFile(file)" class="text-blue-600 hover:text-blue-900">보기</button>
                                        <button @click="downloadFile(file)" class="text-green-600 hover:text-green-900">다운로드</button>
                                        <button @click="requestAnalysis(file)"
                                                :disabled="file.status === 'processing'"
                                                :class="file.status === 'processing' ? 'text-gray-400 cursor-not-allowed' : 'text-purple-600 hover:text-purple-900'">
                                            🤖 AI 분석
                                        </button>
                                        <button @click="deleteFile(file)" class="text-red-600 hover:text-red-900">삭제</button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- 페이지네이션 -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button @click="loadPreviousPage()" :disabled="!pagination.hasPrev"
                            :class="pagination.hasPrev ? 'hover:bg-gray-50' : 'opacity-50 cursor-not-allowed'"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white">
                        이전
                    </button>
                    <button @click="loadNextPage()" :disabled="!pagination.hasNext"
                            :class="pagination.hasNext ? 'hover:bg-gray-50' : 'opacity-50 cursor-not-allowed'"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white">
                        다음
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            총 <span class="font-medium" x-text="pagination.total"></span>개 중
                            <span class="font-medium" x-text="pagination.offset + 1"></span>-<span class="font-medium" x-text="Math.min(pagination.offset + pagination.limit, pagination.total)"></span>번째
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <button @click="loadPreviousPage()" :disabled="!pagination.hasPrev"
                                    :class="pagination.hasPrev ? 'hover:bg-gray-50' : 'opacity-50 cursor-not-allowed'"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                                이전
                            </button>
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                <span x-text="currentPage"></span>
                            </span>
                            <button @click="loadNextPage()" :disabled="!pagination.hasNext"
                                    :class="pagination.hasNext ? 'hover:bg-gray-50' : 'opacity-50 cursor-not-allowed'"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                                다음
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- 파일 업로드 모달 -->
        <div x-show="showUploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" x-cloak>
            <div class="bg-white rounded-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <span class="mr-2">📤</span>
                            파일 업로드
                        </h3>
                        <button @click="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- 업로드 드롭존 -->
                    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                        <div
                            id="upload-drop-zone"
                            class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-500 hover:bg-green-50 transition-colors cursor-pointer"
                            @dragover.prevent="dragOver = true"
                            @dragleave.prevent="dragOver = false"
                            @drop.prevent="handleDrop($event)"
                            :class="dragOver ? 'border-green-500 bg-green-50' : ''"
                        >
                            <div class="mb-4">
                                <span class="text-6xl">📁</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                파일을 드래그 앤 드롭하거나 클릭하여 선택하세요
                            </h3>
                            <p class="text-gray-600 mb-4">
                                JPG, PNG, PDF, DOC, XLS, ZIP 등 다양한 형식을 지원합니다
                            </p>

                            <input
                                type="file"
                                id="modal-file-input"
                                multiple
                                class="hidden"
                                accept="*/*"
                                @change="handleFileSelect($event)"
                            >

                            <button
                                type="button"
                                @click="document.getElementById('modal-file-input').click()"
                                class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition-colors"
                            >
                                파일 선택
                            </button>
                        </div>

                        <!-- 에러 메시지 -->
                        <div x-show="uploadError" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg" x-cloak>
                            <div class="flex items-center">
                                <span class="text-red-500 mr-2">⚠️</span>
                                <span x-text="uploadError" class="text-red-700"></span>
                            </div>
                        </div>
                    </div>

                    <!-- 선택된 파일 목록 -->
                    <div x-show="selectedFiles.length > 0" class="bg-white rounded-xl shadow-sm p-6 mb-6" x-cloak>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <span class="mr-2">📋</span>
                            선택된 파일 (<span class="text-green-600" x-text="selectedFiles.length"></span>개)
                        </h3>

                        <div class="space-y-3 mb-4">
                            <template x-for="(file, index) in selectedFiles" :key="index">
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <span class="text-2xl" x-text="getFileIcon(file.name)"></span>
                                        <div>
                                            <p class="font-medium text-gray-900" x-text="file.name"></p>
                                            <p class="text-sm text-gray-500" x-text="formatFileSize(file.size)"></p>
                                        </div>
                                    </div>
                                    <button
                                        type="button"
                                        @click="removeFile(index)"
                                        class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-colors"
                                        title="파일 삭제"
                                    >
                                        <span class="text-xl">🗑️</span>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <div class="text-sm text-gray-600">
                                총 용량: <span class="font-semibold text-gray-900" x-text="getTotalSize()"></span>
                            </div>
                            <div class="flex space-x-2">
                                <button
                                    type="button"
                                    @click="clearFiles()"
                                    class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2"
                                >
                                    <span>🗑️</span>
                                    <span>전체 삭제</span>
                                </button>
                                <button
                                    type="button"
                                    @click="startUpload()"
                                    :disabled="selectedFiles.length === 0 || isUploading"
                                    :class="selectedFiles.length > 0 && !isUploading ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 cursor-not-allowed'"
                                    class="px-6 py-2 text-white rounded-lg transition-colors flex items-center space-x-2"
                                >
                                    <span x-show="!isUploading">🚀</span>
                                    <span x-show="isUploading" class="animate-spin">⏳</span>
                                    <span x-text="isUploading ? '업로드 중...' : '업로드 시작'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 파일 제한 안내 -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start space-x-2">
                            <span class="text-blue-500 mt-0.5">ℹ️</span>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">업로드 제한사항</p>
                                <ul class="space-y-1 text-blue-600">
                                    <li>• 최대 파일 크기: 50MB</li>
                                    <li>• 최대 파일 개수: 20개</li>
                                    <li>• 총 업로드 크기: 500MB</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 파일 상세 모달 -->
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">상태</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                  :class="getStatusClass(selectedFile?.status)"
                                  x-text="getStatusText(selectedFile?.status)"></span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">업로드일</label>
                            <p class="text-gray-900" x-text="formatDate(selectedFile?.uploaded_at)"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">세션 ID</label>
                            <p class="text-gray-900 text-xs font-mono" x-text="selectedFile?.session_id"></p>
                        </div>
                    </div>
                    <div x-show="selectedFile?.analysis_result" class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">분석 결과</label>
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <p class="text-sm text-gray-700" x-text="selectedFile?.analysis_result || '분석 결과가 없습니다.'"></p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button @click="downloadFile(selectedFile)" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700">
                        다운로드
                    </button>
                    <button @click="requestAnalysis(selectedFile)"
                            :disabled="selectedFile?.status === 'processing'"
                            :class="selectedFile?.status === 'processing' ? 'bg-gray-400 cursor-not-allowed' : 'bg-purple-600 hover:bg-purple-700'"
                            class="text-white py-2 px-4 rounded-lg">
                        🤖 AI 분석 요청
                    </button>
                    <button @click="showFileModal = false" class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400">
                        닫기
                    </button>
                </div>
            </div>
        </div>

        <!-- 일괄 분석 모달 -->
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
                        <option value="pms_summary">PMS 요약</option>
                        <option value="custom">커스텀 분석</option>
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
    </div>

    <script>
        function uploadedFilesList() {
            return {
                files: [],
                selectedFiles: [],
                searchQuery: '',
                statusFilter: '',
                typeFilter: '',
                sortField: 'uploaded_at',
                sortDirection: 'desc',
                loading: false,
                searchTimeout: null,
                showFileModal: false,
                showBulkAnalysisModal: false,
                showUploadModal: false,
                selectedFile: null,
                bulkAnalysisType: 'document_analysis',
                // 파일 업로드 관련 변수들
                selectedFiles: [],
                dragOver: false,
                uploadError: '',
                isUploading: false,

                pagination: {
                    total: 0,
                    limit: 20,
                    offset: 0,
                    hasNext: false,
                    hasPrev: false
                },

                get currentPage() {
                    return Math.floor(this.pagination.offset / this.pagination.limit) + 1;
                },

                async init() {
                    await this.loadFiles();
                },

                async loadFiles() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams({
                            limit: this.pagination.limit,
                            offset: this.pagination.offset,
                            sort: this.sortField,
                            direction: this.sortDirection
                        });

                        if (this.searchQuery.trim()) {
                            params.append('search', this.searchQuery.trim());
                        }

                        if (this.statusFilter) {
                            params.append('status', this.statusFilter);
                        }

                        if (this.typeFilter) {
                            params.append('type', this.typeFilter);
                        }

                        const response = await fetch(`/api/sandbox/gupsa/rfx/files?${params}`);
                        const result = await response.json();

                        if (result.success && result.data) {
                            this.files = result.data.files || result.data;
                            this.pagination = result.data.pagination || this.pagination;
                        } else {
                            console.error('API 응답 오류:', result.message);
                            this.files = [];
                        }
                    } catch (error) {
                        console.error('파일 목록 로딩 실패:', error);
                        this.files = [];
                    } finally {
                        this.loading = false;
                    }
                },

                handleSearch() {
                    if (this.searchTimeout) {
                        clearTimeout(this.searchTimeout);
                    }
                    this.searchTimeout = setTimeout(() => {
                        this.pagination.offset = 0;
                        this.loadFiles();
                    }, 500);
                },

                sortBy(field) {
                    if (this.sortField === field) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortField = field;
                        this.sortDirection = 'asc';
                    }
                    this.loadFiles();
                },

                loadNextPage() {
                    if (this.pagination.hasNext) {
                        this.pagination.offset += this.pagination.limit;
                        this.loadFiles();
                    }
                },

                loadPreviousPage() {
                    if (this.pagination.hasPrev) {
                        this.pagination.offset = Math.max(0, this.pagination.offset - this.pagination.limit);
                        this.loadFiles();
                    }
                },

                toggleSelectAll(event) {
                    if (event.target.checked) {
                        this.selectedFiles = this.files.map(file => file.id);
                    } else {
                        this.selectedFiles = [];
                    }
                },

                viewFile(file) {
                    this.selectedFile = file;
                    this.showFileModal = true;
                },

                downloadFile(file) {
                    console.log('파일 다운로드:', file);

                    // public 폴더의 심볼릭 링크를 통한 다운로드
                    // 저장된 파일 이름을 사용 (file_name이 없으면 original_name 사용)
                    const fileName = file.file_name || file.original_name;
                    const downloadUrl = `/sandbox-files/downloads/${fileName}`;

                    // 파일 다운로드 링크 생성
                    const link = document.createElement('a');
                    link.href = downloadUrl;
                    link.download = file.original_name; // 다운로드 시 원래 파일 이름 사용
                    link.style.display = 'none';

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },

                async requestAnalysis(file) {
                    try {
                        const response = await fetch(`/api/sandbox/gupsa/rfx/files/${file.id}/analyze`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                request_type: 'document_analysis'
                            })
                        });

                        if (response.ok) {
                            file.status = 'processing';
                            alert('🤖 AI 분석 요청이 접수되었습니다.');
                        }
                    } catch (error) {
                        console.error('AI 분석 요청 실패:', error);
                        alert('🤖 AI 분석 요청에 실패했습니다.');
                    }
                },

                async deleteFile(file) {
                    if (!confirm('정말로 이 파일을 삭제하시겠습니까?')) return;

                    try {
                        const response = await fetch(`/api/sandbox/gupsa/rfx/files/${file.id}`, {
                            method: 'DELETE'
                        });

                        if (response.ok) {
                            this.files = this.files.filter(f => f.id !== file.id);
                            this.selectedFiles = this.selectedFiles.filter(id => id !== file.id);
                        }
                    } catch (error) {
                        console.error('파일 삭제 실패:', error);
                    }
                },

                bulkAnalyze() {
                    if (this.selectedFiles.length === 0) return;
                    this.showBulkAnalysisModal = true;
                },

                async submitBulkAnalysis() {
                    try {
                        for (const fileId of this.selectedFiles) {
                            const response = await fetch(`/api/sandbox/gupsa/rfx/files/${fileId}/analyze`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    request_type: this.bulkAnalysisType
                                })
                            });

                            if (response.ok) {
                                const file = this.files.find(f => f.id == fileId);
                                if (file) file.status = 'processing';
                            }
                        }

                        this.selectedFiles = [];
                        this.showBulkAnalysisModal = false;
                        alert('🤖 일괄 AI 분석 요청이 접수되었습니다.');
                    } catch (error) {
                        console.error('일괄 AI 분석 요청 실패:', error);
                        alert('🤖 일괄 AI 분석 요청에 실패했습니다.');
                    }
                },

                exportList() {
                    console.log('파일 목록 내보내기');
                },

                // 파일 업로드 모달 관련 함수들
                openUploadModal() {
                    this.showUploadModal = true;
                    this.selectedFiles = [];
                    this.uploadError = '';
                    this.isUploading = false;
                },

                closeUploadModal() {
                    this.showUploadModal = false;
                    this.selectedFiles = [];
                    this.uploadError = '';
                    this.isUploading = false;
                    this.dragOver = false;
                },

                handleFileSelect(event) {
                    this.addFiles(event.target.files);
                    event.target.value = ''; // 같은 파일 재선택 가능하도록
                },

                handleDrop(event) {
                    this.dragOver = false;
                    this.addFiles(event.dataTransfer.files);
                },

                addFiles(fileList) {
                    this.uploadError = '';
                    const maxFiles = 20;
                    const maxFileSize = 50 * 1024 * 1024; // 50MB
                    const maxTotalSize = 500 * 1024 * 1024; // 500MB

                    // 파일 개수 제한 검사
                    if (this.selectedFiles.length + fileList.length > maxFiles) {
                        this.uploadError = `최대 ${maxFiles}개의 파일만 업로드할 수 있습니다.`;
                        return;
                    }

                    // 각 파일 검사 및 추가
                    for (let file of fileList) {
                        // 파일 크기 검사
                        if (file.size > maxFileSize) {
                            this.uploadError = `파일 "${file.name}"의 크기가 50MB를 초과합니다.`;
                            return;
                        }

                        // 중복 파일 검사
                        if (this.selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                            this.uploadError = `파일 "${file.name}"이(가) 이미 선택되었습니다.`;
                            return;
                        }

                        this.selectedFiles.push(file);
                    }

                    // 총 크기 제한 검사
                    const totalSize = this.selectedFiles.reduce((sum, file) => sum + file.size, 0);
                    if (totalSize > maxTotalSize) {
                        this.uploadError = '총 업로드 크기가 500MB를 초과합니다.';
                        this.selectedFiles = [];
                        return;
                    }
                },

                removeFile(index) {
                    this.selectedFiles.splice(index, 1);
                    this.uploadError = '';
                },

                clearFiles() {
                    this.selectedFiles = [];
                    this.uploadError = '';
                },

                async startUpload() {
                    if (this.selectedFiles.length === 0) return;

                    this.isUploading = true;
                    this.uploadError = '';

                    try {
                        const formData = new FormData();

                        // 파일들을 FormData에 추가 (Laravel이 기대하는 형식으로)
                        console.log('업로드할 파일들:', this.selectedFiles);
                        this.selectedFiles.forEach((file, index) => {
                            formData.append('files[]', file); // Laravel 배열 형식
                            console.log(`파일 ${index} 추가:`, file.name, file.size);
                        });

                        console.log('FormData 전송 시작...');

                        // FormData 내용 확인
                        for (let [key, value] of formData.entries()) {
                            console.log('FormData 항목:', key, value);
                        }

                        const response = await fetch('/api/sandbox/gupsa/rfx/files/upload', {
                            method: 'POST',
                            body: formData
                        });

                        console.log('응답 상태:', response.status);
                        console.log('응답 헤더:', Object.fromEntries(response.headers.entries()));

                        if (!response.ok) {
                            const errorText = await response.text();
                            console.error('응답 에러 텍스트:', errorText);
                            throw new Error(`HTTP ${response.status}: ${errorText}`);
                        }

                        const result = await response.json();
                        console.log('API 응답:', result);

                        if (result.success) {
                            alert(`✅ ${this.selectedFiles.length}개 파일이 성공적으로 업로드되었습니다!`);
                            this.closeUploadModal();

                            // 파일 목록 새로고침 - 더 확실한 방법으로
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        } else {
                            this.uploadError = result.message || '업로드에 실패했습니다.';
                        }
                    } catch (error) {
                        console.error('Upload error:', error);
                        this.uploadError = '업로드 중 오류가 발생했습니다.';
                    } finally {
                        this.isUploading = false;
                    }
                },

                getTotalSize() {
                    const total = this.selectedFiles.reduce((sum, file) => sum + file.size, 0);
                    return this.formatFileSize(total);
                },

                getFileIcon(mimeType) {
                    const icons = {
                        'application/pdf': 'PDF',
                        'application/msword': 'DOC',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOC',
                        'application/vnd.ms-excel': 'XLS',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'XLS',
                        'text/markdown': 'MD'
                    };
                    return icons[mimeType] || 'FILE';
                },

                getFileTypeClass(mimeType) {
                    const classes = {
                        'application/pdf': 'bg-red-100 text-red-800',
                        'application/msword': 'bg-blue-100 text-blue-800',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'bg-blue-100 text-blue-800',
                        'application/vnd.ms-excel': 'bg-green-100 text-green-800',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'bg-green-100 text-green-800',
                        'text/markdown': 'bg-purple-100 text-purple-800'
                    };
                    return classes[mimeType] || 'bg-gray-100 text-gray-800';
                },

                getFileTypeName(mimeType) {
                    const names = {
                        'application/pdf': 'PDF',
                        'application/msword': 'Word',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'Word',
                        'application/vnd.ms-excel': 'Excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'Excel',
                        'text/markdown': 'Markdown'
                    };
                    return names[mimeType] || '알 수 없음';
                },

                getStatusClass(status) {
                    const statusClasses = {
                        'uploaded': 'bg-blue-100 text-blue-800',
                        'processing': 'bg-yellow-100 text-yellow-800',
                        'analyzed': 'bg-green-100 text-green-800',
                        'error': 'bg-red-100 text-red-800'
                    };
                    return statusClasses[status] || 'bg-gray-100 text-gray-800';
                },

                getStatusText(status) {
                    const statusTexts = {
                        'uploaded': '업로드됨',
                        'processing': '분석 중',
                        'analyzed': '분석 완료',
                        'error': '오류'
                    };
                    return statusTexts[status] || status;
                },

                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },

                formatDate(datetime) {
                    if (!datetime) return '';
                    const date = new Date(datetime);
                    return date.toLocaleDateString('ko-KR') + ' ' + date.toLocaleTimeString('ko-KR', { hour: '2-digit', minute: '2-digit' });
                }
            }
        }
    </script>
</div>
