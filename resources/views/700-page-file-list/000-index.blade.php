<x-300-layout-common.000-app>
    <x-slot:title>파일 목록</x-slot:title>
    
    {{-- 업로드된 파일 목록 관리 --}}
    <div x-data="uploadedFilesList()" x-init="init()" class="p-6">
    <!-- 헤더 -->
    <div class="mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">업로드 파일 목록</h1>
                <p class="text-gray-600 mt-1">업로드된 모든 문서를 관리하고 AI 분석을 요청하세요</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('file-upload') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    파일 업로드
                </a>
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
                    <option value="pending">대기중</option>
                    <option value="processing">분석 중</option>
                    <option value="completed">분석 완료</option>
                    <option value="failed">오류</option>
                </select>
                <select x-model="typeFilter" @change="loadFiles()" class="px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">모든 파일 타입</option>
                    <option value="pdf">PDF</option>
                    <option value="doc">DOC/DOCX</option>
                    <option value="xls">XLS/XLSX</option>
                    <option value="txt">텍스트</option>
                    <option value="image">이미지</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-500" x-text="pagination.total + '개 파일'">로딩 중...</span>
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
                        <th @click="sortBy('analysis_status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            상태 <span x-show="sortField === 'analysis_status'" x-text="sortDirection === 'asc' ? '↑' : '↓'"></span>
                        </th>
                        <th @click="sortBy('created_at')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            업로드일 <span x-show="sortField === 'created_at'" x-text="sortDirection === 'asc' ? '↑' : '↓'"></span>
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
                            <a href="{{ route('file-upload') }}" class="text-blue-600 hover:text-blue-900 ml-2">파일 업로드하기</a>
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
                                        <div class="text-sm text-gray-500" x-text="file.file_name"></div>
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
                                      :class="getStatusClass(file.analysis_status)"
                                      x-text="getStatusText(file.analysis_status)">
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(file.created_at)"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button @click="viewFile(file)" class="text-blue-600 hover:text-blue-900">보기</button>
                                    <button @click="downloadFile(file)" class="text-green-600 hover:text-green-900">다운로드</button>
                                    <button @click="requestAnalysis(file)"
                                            :disabled="file.analysis_status === 'processing'"
                                            :class="file.analysis_status === 'processing' ? 'text-gray-400 cursor-not-allowed' : 'text-purple-600 hover:text-purple-900'">
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

    @include('700-page-file-list.300-file-detail-modal')
    @include('700-page-file-list.300-bulk-analysis-modal')
    @include('700-page-file-list.400-file-list-scripts')
    </div>
</x-300-layout-common.000-app>