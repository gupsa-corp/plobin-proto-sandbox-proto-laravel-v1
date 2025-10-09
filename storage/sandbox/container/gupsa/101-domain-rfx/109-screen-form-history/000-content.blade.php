{{-- 폼 전송 내역 화면 --}}
<?php 
    require_once __DIR__ . "/../../../../../../bootstrap.php";
use App\Services\TemplateCommonService;
    

    $screenInfo = TemplateCommonService::getCurrentTemplateScreenInfo();
    $uploadPaths = TemplateCommonService::getTemplateUploadPaths();
?>
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-100 p-6" 
     x-data="formHistoryData()" 
     x-init="init()"
     x-cloak>
    
    {{-- 헤더 --}}
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-xl">📋</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">폼 전송 내역</h1>
                        <p class="text-gray-600">제출된 폼 데이터를 조회하고 관리하세요</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">총 제출 건수</div>
                    <div class="text-2xl font-bold text-gray-900" x-text="totalSubmissions">-</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 필터 및 검색 --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            {{-- 폼 이름 필터 --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">폼 이름</label>
                <select x-model="filters.formName" @change="applyFilters()" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    <option value="">전체</option>
                    <template x-for="formName in formNames" :key="formName">
                        <option :value="formName" x-text="formName"></option>
                    </template>
                </select>
            </div>
            
            {{-- 날짜 필터 --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">시작 날짜</label>
                <input type="date" x-model="filters.startDate" @change="applyFilters()"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">종료 날짜</label>
                <input type="date" x-model="filters.endDate" @change="applyFilters()"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>
            
            {{-- 검색 --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">검색</label>
                <div class="relative">
                    <input type="text" x-model="filters.search" @input="debounceSearch()"
                           placeholder="데이터 내용으로 검색..."
                           class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                        <span class="text-gray-400">🔍</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- 필터 액션 --}}
        <div class="mt-4 flex justify-between items-center">
            <div class="text-sm text-gray-500">
                <span x-text="filteredSubmissions.length"></span>건 표시 중 
                (전체 <span x-text="totalSubmissions"></span>건)
            </div>
            <div class="flex space-x-2">
                <button @click="resetFilters()" 
                        class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    필터 초기화
                </button>
                <button @click="exportData()" 
                        class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700">
                    CSV 내보내기
                </button>
            </div>
        </div>
    </div>

    {{-- 컨트롤 헤더 --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">제출 내역</h3>
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600">정렬:</label>
                    <select x-model="sortField" @change="sortSubmissions(); updatePagination();"
                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                        <option value="id">ID</option>
                        <option value="form_name">폼 이름</option>
                        <option value="submitted_at">제출 시간</option>
                    </select>
                    <button @click="toggleSortDirection()"
                            class="px-2 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm">
                        <span x-text="sortDirection === 'asc' ? '↑' : '↓'"></span>
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600">페이지당:</label>
                    <select x-model="pagination.perPage" @change="changePage(1)"
                            class="px-2 py-1 border border-gray-300 rounded text-sm">
                        <option value="10">10개</option>
                        <option value="25">25개</option>
                        <option value="50">50개</option>
                        <option value="100">100개</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- 로딩 상태 --}}
    <div x-show="loading" class="bg-white rounded-xl shadow-sm p-8 text-center mb-6">
        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500">
            <div class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500">⟳</div>
            데이터를 불러오는 중...
        </div>
    </div>

    {{-- 데이터 없음 --}}
    <div x-show="!loading && paginatedSubmissions.length === 0" class="bg-white rounded-xl shadow-sm p-8 text-center mb-6">
        <div class="text-gray-400 text-6xl mb-4">📭</div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">제출된 데이터가 없습니다</h3>
        <p class="text-gray-500">조건에 맞는 폼 제출 내역이 없습니다.</p>
    </div>

    {{-- 카드 그리드 --}}
    <div x-show="!loading && paginatedSubmissions.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <template x-for="submission in paginatedSubmissions" :key="submission.id">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                {{-- 카드 헤더 --}}
                <div class="p-6 pb-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-blue-500 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm" x-text="submission.id"></span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900" x-text="'ID: ' + submission.id"></div>
                                <div class="text-sm text-gray-500" x-text="formatDateTime(submission.submitted_at)"></div>
                            </div>
                        </div>
                    </div>

                    {{-- 폼 이름 --}}
                    <div class="mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"
                              x-text="submission.form_name"></span>
                    </div>

                    {{-- 데이터 미리보기 --}}
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">제출 데이터</h4>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-sm text-gray-600 line-clamp-3" x-text="formatFormData(submission.form_data)"></p>
                            <button @click="viewDetails(submission)"
                                    class="text-green-600 hover:text-green-800 text-xs mt-2 font-medium">
                                자세히 보기 →
                            </button>
                        </div>
                    </div>
                </div>

                {{-- 카드 액션 --}}
                <div class="px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            <span x-text="formatDateTime(submission.submitted_at)"></span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button @click="viewDetails(submission)"
                                    class="text-green-600 hover:text-green-800 text-sm font-medium">
                                보기
                            </button>
                            <button @click="exportSingle(submission)"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                내보내기
                            </button>
                            <button @click="deleteSubmission(submission)"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                삭제
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- 페이지네이션 --}}
    <div x-show="!loading && filteredSubmissions.length > 0" class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                <span x-text="(pagination.currentPage - 1) * pagination.perPage + 1"></span>-<span x-text="Math.min(pagination.currentPage * pagination.perPage, filteredSubmissions.length)"></span>
                / <span x-text="filteredSubmissions.length"></span>개 표시
            </div>
            <div class="flex space-x-1">
                <button @click="changePage(pagination.currentPage - 1)"
                        :disabled="pagination.currentPage === 1"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    이전
                </button>
                <template x-for="page in paginationPages" :key="page">
                    <button @click="changePage(page)"
                            :class="page === pagination.currentPage ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                        <span x-text="page"></span>
                    </button>
                </template>
                <button @click="changePage(pagination.currentPage + 1)"
                        :disabled="pagination.currentPage >= Math.ceil(filteredSubmissions.length / pagination.perPage)"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    다음
                </button>
            </div>
        </div>
    </div>

    {{-- 상세보기 모달 --}}
    <div x-show="showDetailsModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
        
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">제출 상세 정보</h3>
                        <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-600">
                            <span class="sr-only">닫기</span>
                            <i class="fa fa-plus h-6 w-6"></i>
                        </button>
                    </div>
                    
                    <div x-show="selectedSubmission" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">제출 ID</label>
                                <div class="text-lg font-semibold" x-text="selectedSubmission?.id"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">폼 이름</label>
                                <div class="text-lg" x-text="selectedSubmission?.form_name"></div>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-600">제출 시간</label>
                                <div x-text="formatDateTime(selectedSubmission?.submitted_at)"></div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">제출 데이터</label>
                            <pre class="bg-gray-50 p-4 rounded-lg text-sm overflow-auto max-h-64" 
                                 x-text="JSON.stringify(selectedSubmission?.form_data, null, 2)"></pre>
                        </div>
                        
                        <div x-show="selectedSubmission?.ip_address || selectedSubmission?.user_agent">
                            <label class="block text-sm font-medium text-gray-600 mb-2">메타데이터</label>
                            <div class="bg-gray-50 p-4 rounded-lg text-sm space-y-1">
                                <div x-show="selectedSubmission?.ip_address">
                                    <strong>IP 주소:</strong> <span x-text="selectedSubmission?.ip_address"></span>
                                </div>
                                <div x-show="selectedSubmission?.user_agent">
                                    <strong>User Agent:</strong> <span x-text="selectedSubmission?.user_agent"></span>
                                </div>
                                <div x-show="selectedSubmission?.session_id">
                                    <strong>세션 ID:</strong> <span x-text="selectedSubmission?.session_id"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" 
                            @click="showDetailsModal = false"
                            class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto">
                        확인
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function formHistoryData() {
    return {
        // 데이터
        submissions: [],
        filteredSubmissions: [],
        paginatedSubmissions: [],
        formNames: [],
        totalSubmissions: 0,
        
        // UI 상태
        loading: true,
        showDetailsModal: false,
        selectedSubmission: null,
        
        // 필터
        filters: {
            formName: '',
            startDate: '',
            endDate: '',
            search: ''
        },
        
        // 정렬
        sortField: 'id',
        sortDirection: 'desc',
        
        // 페이지네이션
        pagination: {
            currentPage: 1,
            perPage: 25
        },
        
        // 검색 디바운스
        searchTimeout: null,
        
        // 초기화
        async init() {
            await this.loadSubmissions();
        },
        
        // 제출 데이터 로드 (데모용 모크 데이터)
        async loadSubmissions() {
            this.loading = true;

            // 서버 라우팅 문제로 인해 임시로 모크 데이터 사용
            await new Promise(resolve => setTimeout(resolve, 1000)); // 로딩 시뮬레이션

            this.submissions = [
                {
                    id: 5,
                    form_name: '주문 취소',
                    form_data: {
                        order_id: 'ORD-2025-001',
                        reason: '변심',
                        refund_method: '신용카드',
                        additional_info: '빠른 처리 부탁드립니다.'
                    },
                    submitted_at: '2025-09-15 09:00:48',
                    ip_address: '192.168.1.104',
                    user_agent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:91.0) Gecko/20100101 Firefox/91.0',
                    session_id: 'sess_66e6d123456'
                },
                {
                    id: 4,
                    form_name: '사용자 등록',
                    form_data: {
                        name: '김영희',
                        email: 'kim@example.com',
                        phone: '010-9876-5432',
                        message: '회원가입 하고 싶습니다.'
                    },
                    submitted_at: '2025-09-15 08:30:48',
                    ip_address: '192.168.1.103',
                    user_agent: 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
                    session_id: 'sess_66e6d123455'
                },
                {
                    id: 3,
                    form_name: '피드백',
                    form_data: {
                        rating: 5,
                        feedback: '매우 만족합니다.',
                        recommend: true,
                        category: 'UI/UX'
                    },
                    submitted_at: '2025-09-15 06:30:48',
                    ip_address: '192.168.1.102',
                    user_agent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15',
                    session_id: 'sess_66e6d123454'
                },
                {
                    id: 2,
                    form_name: '문의하기',
                    form_data: {
                        subject: '서비스 이용 관련',
                        content: '서비스 이용 중 문제가 발생했습니다.',
                        priority: '높음'
                    },
                    submitted_at: '2025-09-14 09:30:48',
                    ip_address: '192.168.1.101',
                    user_agent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                    session_id: 'sess_66e6d123453'
                },
                {
                    id: 1,
                    form_name: '사용자 등록',
                    form_data: {
                        name: '홍길동',
                        email: 'hong@example.com',
                        phone: '010-1234-5678',
                        message: '가입 문의드립니다.'
                    },
                    submitted_at: '2025-09-13 09:30:48',
                    ip_address: '192.168.1.100',
                    user_agent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    session_id: 'sess_66e6d123452'
                }
            ];

            this.totalSubmissions = this.submissions.length;
            this.extractFormNames();
            this.applyFilters();
            this.loading = false;
        },
        
        // 폼 이름 목록 추출
        extractFormNames() {
            const names = [...new Set(this.submissions.map(s => s.form_name))];
            this.formNames = names.sort();
        },
        
        // 필터 적용
        applyFilters() {
            let filtered = [...this.submissions];
            
            // 폼 이름 필터
            if (this.filters.formName) {
                filtered = filtered.filter(s => s.form_name === this.filters.formName);
            }
            
            // 날짜 필터
            if (this.filters.startDate) {
                const startDate = new Date(this.filters.startDate);
                filtered = filtered.filter(s => new Date(s.submitted_at) >= startDate);
            }
            
            if (this.filters.endDate) {
                const endDate = new Date(this.filters.endDate);
                endDate.setHours(23, 59, 59, 999); // 하루 끝까지
                filtered = filtered.filter(s => new Date(s.submitted_at) <= endDate);
            }
            
            // 검색 필터
            if (this.filters.search) {
                const search = this.filters.search.toLowerCase();
                filtered = filtered.filter(s => {
                    const formData = JSON.stringify(s.form_data).toLowerCase();
                    return formData.includes(search) || 
                           s.form_name.toLowerCase().includes(search);
                });
            }
            
            this.filteredSubmissions = filtered;
            this.sortSubmissions();
            this.pagination.currentPage = 1;
            this.updatePagination();
        },
        
        // 정렬
        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortField = field;
                this.sortDirection = 'desc';
            }
            this.sortSubmissions();
            this.updatePagination();
        },

        toggleSortDirection() {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            this.sortSubmissions();
            this.updatePagination();
        },
        
        sortSubmissions() {
            this.filteredSubmissions.sort((a, b) => {
                let aVal = a[this.sortField];
                let bVal = b[this.sortField];
                
                if (this.sortField === 'submitted_at') {
                    aVal = new Date(aVal);
                    bVal = new Date(bVal);
                }
                
                if (aVal < bVal) return this.sortDirection === 'asc' ? -1 : 1;
                if (aVal > bVal) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });
        },
        
        // 페이지네이션
        changePage(page) {
            const maxPage = Math.ceil(this.filteredSubmissions.length / this.pagination.perPage);
            if (page >= 1 && page <= maxPage) {
                this.pagination.currentPage = page;
                this.updatePagination();
            }
        },
        
        updatePagination() {
            const start = (this.pagination.currentPage - 1) * this.pagination.perPage;
            const end = start + this.pagination.perPage;
            this.paginatedSubmissions = this.filteredSubmissions.slice(start, end);
        },
        
        get paginationPages() {
            const totalPages = Math.ceil(this.filteredSubmissions.length / this.pagination.perPage);
            const current = this.pagination.currentPage;
            const pages = [];
            
            let start = Math.max(1, current - 2);
            let end = Math.min(totalPages, current + 2);
            
            if (current <= 3) {
                end = Math.min(5, totalPages);
            }
            if (current >= totalPages - 2) {
                start = Math.max(1, totalPages - 4);
            }
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            return pages;
        },
        
        // 검색 디바운스
        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.applyFilters();
            }, 300);
        },
        
        // 필터 초기화
        resetFilters() {
            this.filters = {
                formName: '',
                startDate: '',
                endDate: '',
                search: ''
            };
            this.applyFilters();
        },
        
        // 상세보기
        viewDetails(submission) {
            this.selectedSubmission = submission;
            this.showDetailsModal = true;
        },
        
        // 단일 데이터 내보내기
        exportSingle(submission) {
            const data = {
                id: submission.id,
                form_name: submission.form_name,
                form_data: submission.form_data,
                submitted_at: submission.submitted_at
            };
            this.downloadJSON(data, `submission_${submission.id}.json`);
        },
        
        // 전체 데이터 CSV 내보내기
        exportData() {
            if (this.filteredSubmissions.length === 0) {
                alert('내보낼 데이터가 없습니다.');
                return;
            }
            
            const headers = ['ID', '폼 이름', '제출 시간'];
            const allKeys = new Set();
            
            // 모든 폼 데이터 키 수집
            this.filteredSubmissions.forEach(s => {
                if (s.form_data) {
                    Object.keys(s.form_data).forEach(key => allKeys.add(key));
                }
            });
            
            headers.push(...Array.from(allKeys));
            
            const csvContent = [
                headers.join(','),
                ...this.filteredSubmissions.map(s => {
                    const row = [
                        s.id,
                        `"${s.form_name}"`,
                        `"${this.formatDateTime(s.submitted_at)}"`
                    ];
                    
                    // 폼 데이터 값들 추가
                    allKeys.forEach(key => {
                        const value = s.form_data?.[key] || '';
                        row.push(`"${String(value).replace(/"/g, '""')}"`);
                    });
                    
                    return row.join(',');
                })
            ].join('\\n');
            
            this.downloadCSV(csvContent, 'form_submissions.csv');
        },
        
        // 제출 데이터 삭제 (데모용 모크)
        async deleteSubmission(submission) {
            if (!confirm(`제출 ID ${submission.id}를 삭제하시겠습니까?\\n이 작업은 되돌릴 수 없습니다.`)) {
                return;
            }

            // 모크 데이터에서 해당 항목 제거
            this.submissions = this.submissions.filter(s => s.id !== submission.id);
            this.totalSubmissions = this.submissions.length;
            this.extractFormNames();
            this.applyFilters();

            alert('제출 데이터가 삭제되었습니다.');
        },
        
        // 유틸리티 함수들
        formatDateTime(datetime) {
            if (!datetime) return '-';
            const date = new Date(datetime);
            return date.toLocaleString('ko-KR', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        },
        
        formatFormData(formData) {
            if (!formData) return '-';
            const entries = Object.entries(formData);
            if (entries.length === 0) return '-';
            
            return entries
                .slice(0, 3)
                .map(([key, value]) => `${key}: ${String(value).substring(0, 20)}${String(value).length > 20 ? '...' : ''}`)
                .join(', ') + (entries.length > 3 ? '...' : '');
        },
        
        downloadJSON(data, filename) {
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            URL.revokeObjectURL(url);
        },
        
        downloadCSV(csvContent, filename) {
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            URL.revokeObjectURL(url);
        }
    }
}
</script>

<!-- Alpine.js 스크립트 -->
<!-- Alpine.js provided by Livewire - CDN removed to prevent conflicts -->