{{-- 분석 요청 목록 화면 --}}
<?php
    require_once __DIR__ . "/../../../../../../bootstrap.php";
use App\Services\TemplateCommonService;
    

    $screenInfo = TemplateCommonService::getCurrentTemplateScreenInfo();
    $uploadPaths = TemplateCommonService::getTemplateUploadPaths();
?>
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-indigo-50 p-6">
    {{-- 헤더 --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span class="text-purple-600">📊</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">분석 요청 목록</h1>
                    <p class="text-gray-600">파일 분석 요청 현황을 확인하고 관리하세요</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="refreshAnalysisRequests()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">새로고침</button>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto">

        <!-- 검색 및 필터 -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">검색</label>
                    <input type="text" id="search-input" placeholder="파일명 검색..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                    <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">모든 상태</option>
                        <option value="pending">대기</option>
                        <option value="processing">처리중</option>
                        <option value="completed">완료</option>
                        <option value="failed">실패</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">정렬</label>
                    <select id="sort-select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="requested_at_desc">최신순</option>
                        <option value="requested_at_asc">오래된순</option>
                        <option value="file_name_asc">파일명순 (ㄱ-ㅎ)</option>
                        <option value="file_name_desc">파일명순 (ㅎ-ㄱ)</option>
                        <option value="status_asc">상태순</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">표시 개수</label>
                    <select id="per-page-select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="10">10개</option>
                        <option value="25">25개</option>
                        <option value="50">50개</option>
                        <option value="100">100개</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    총 <span id="total-requests-count">0</span>개 요청
                </div>
                <button type="button" onclick="clearFilters()" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                    필터 초기화
                </button>
            </div>
        </div>

        <!-- 분석 요청 목록 -->
        <div class="bg-white rounded-lg shadow-md">
            <!-- 테이블 헤더 -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700">분석 요청 목록</span>
                </div>
            </div>

            <!-- 분석 요청 목록 컨테이너 -->
            <div id="requests-container" class="divide-y divide-gray-200">
                <!-- 로딩 상태 -->
                <div id="loading-state" class="px-6 py-12 text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500 mx-auto mb-4"></div>
                    <p class="text-gray-500">분석 요청 목록을 불러오는 중...</p>
                </div>

                <!-- 빈 상태 -->
                <div id="empty-state" class="px-6 py-12 text-center" style="display: none;">
                    <i class="fa fa-plus mx-auto h-12 w-12 text-gray-400 mb-4"></i>
                    <p class="text-gray-500 mb-4">분석 요청이 없습니다.</p>
                    <a href="<?= getScreenUrl('frontend', '008-screen-uploaded-files-list') ?>" class="text-purple-600 hover:text-purple-800 font-medium">
                        파일 목록에서 분석 요청하기
                    </a>
                </div>
            </div>

            <!-- 페이지네이션 -->
            <div id="pagination-container" class="px-6 py-4 border-t border-gray-200 bg-gray-50" style="display: none;">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        <span id="page-info">1-10 / 0</span>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" id="prev-page" class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white hover:bg-gray-50 disabled:opacity-50" disabled>
                            이전
                        </button>
                        <button type="button" id="next-page" class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white hover:bg-gray-50 disabled:opacity-50" disabled>
                            다음
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 분석 결과 상세 모달 -->
<div id="analysis-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" style="display: none;">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900" id="modal-title">분석 결과</h3>
            <button type="button" onclick="closeAnalysisModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa fa-plus h-6 w-6"></i>
            </button>
        </div>
        <div id="modal-content" class="space-y-4">
            <!-- 분석 결과가 여기에 표시됩니다 -->
        </div>
        <div class="flex justify-end space-x-3 mt-6">
            <button type="button" onclick="closeAnalysisModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200">
                닫기
            </button>
        </div>
    </div>
</div>

<script>
let currentRequests = [];
let currentPage = 1;
let totalPages = 1;
let perPage = 10;
let searchQuery = '';
let statusFilter = '';
let sortBy = 'requested_at_desc';

document.addEventListener('DOMContentLoaded', function() {
    loadAnalysisRequests();

    // 이벤트 리스너 설정
    document.getElementById('search-input').addEventListener('input', debounce(handleSearch, 300));
    document.getElementById('status-filter').addEventListener('change', handleFilterChange);
    document.getElementById('sort-select').addEventListener('change', handleSortChange);
    document.getElementById('per-page-select').addEventListener('change', handlePerPageChange);
    document.getElementById('prev-page').addEventListener('click', () => changePage(currentPage - 1));
    document.getElementById('next-page').addEventListener('click', () => changePage(currentPage + 1));
});

async function loadAnalysisRequests() {
    try {
        showLoading();

        // Mock 데이터 (샌드박스 환경용)
        const result = {
            "success": true,
            "data": [
                {
                    "id": 1,
                    "file_name": "AI_기술_동향_보고서_2025.pdf",
                    "file_size": 2048576,
                    "file_type": "pdf",
                    "status": "completed",
                    "priority": "high",
                    "requester_name": "김혁신",
                    "department": "기술연구팀",
                    "analysis_type": "문서 요약 분석",
                    "description": "2025년 AI 기술 동향에 대한 종합 분석 보고서 요약 및 핵심 인사이트 추출",
                    "progress_percentage": 100,
                    "result_summary": "AI 기술 발전 트렌드와 비즈니스 적용 방안에 대한 포괄적 분석 완료. 생성형 AI, 멀티모달 AI 등 주요 기술 영역별 상세 요약 제공.",
                    "ai_analysis_result": {"sections": 15, "key_insights": 8, "recommendations": 12, "confidence_score": 0.92},
                    "requested_at": "2025-09-08T09:00:00",
                    "completed_at": "2025-09-10T13:45:00"
                },
                {
                    "id": 2,
                    "file_name": "스마트시티_플랫폼_제안서.docx",
                    "file_size": 1536000,
                    "file_type": "docx",
                    "status": "completed",
                    "priority": "high",
                    "requester_name": "박도시",
                    "department": "사업개발팀",
                    "analysis_type": "제안서 분석",
                    "description": "스마트시티 플랫폼 구축 제안서 내용 분석 및 핵심 요소 추출",
                    "progress_percentage": 100,
                    "result_summary": "스마트시티 플랫폼의 기술 아키텍처, 예산 계획, 구현 로드맵 등 핵심 요소들이 체계적으로 분석됨.",
                    "ai_analysis_result": {"sections": 12, "budget_analysis": "상세", "technical_specs": "완료"},
                    "requested_at": "2025-09-10T11:00:00",
                    "completed_at": "2025-09-12T15:30:00"
                },
                {
                    "id": 3,
                    "file_name": "대규모_시스템_설계서.pdf",
                    "file_size": 4096000,
                    "file_type": "pdf",
                    "status": "processing",
                    "priority": "high",
                    "requester_name": "최아키",
                    "department": "시스템설계팀",
                    "analysis_type": "기술 문서 분석",
                    "description": "대규모 분산 시스템 아키텍처 설계 문서 분석 및 기술 검토",
                    "progress_percentage": 65,
                    "result_summary": null,
                    "ai_analysis_result": null,
                    "requested_at": "2025-09-14T10:30:00",
                    "completed_at": null
                },
                {
                    "id": 4,
                    "file_name": "클라우드_아키텍처_가이드.pdf",
                    "file_size": 1792000,
                    "file_type": "pdf",
                    "status": "pending",
                    "priority": "medium",
                    "requester_name": "김클라우드",
                    "department": "인프라팀",
                    "analysis_type": "가이드 문서 분석",
                    "description": "클라우드 네이티브 아키텍처 가이드 문서 검토 및 모범 사례 추출",
                    "progress_percentage": 0,
                    "result_summary": null,
                    "ai_analysis_result": null,
                    "requested_at": "2025-09-15T13:45:00",
                    "completed_at": null
                },
                {
                    "id": 5,
                    "file_name": "긴급_보안점검_리포트.pdf",
                    "file_size": 1024000,
                    "file_type": "pdf",
                    "status": "pending",
                    "priority": "urgent",
                    "requester_name": "최보안",
                    "department": "보안팀",
                    "analysis_type": "보안 분석",
                    "description": "긴급 보안 점검 결과 리포트 분석 및 취약점 식별",
                    "progress_percentage": 0,
                    "result_summary": null,
                    "ai_analysis_result": null,
                    "requested_at": "2025-09-15T16:30:00",
                    "completed_at": null
                },
                {
                    "id": 6,
                    "file_name": "손상된_문서파일.pdf",
                    "file_size": 512000,
                    "file_type": "pdf",
                    "status": "failed",
                    "priority": "medium",
                    "requester_name": "한실패",
                    "department": "품질관리팀",
                    "analysis_type": "문서 분석",
                    "description": "품질 관리 프로세스 문서 분석",
                    "progress_percentage": 0,
                    "result_summary": null,
                    "ai_analysis_result": null,
                    "error_message": "파일이 손상되어 읽을 수 없습니다. PDF 구조가 깨져있어 텍스트 추출이 불가능합니다.",
                    "requested_at": "2025-09-12T09:00:00",
                    "completed_at": null
                }
            ]
        };
        let requests = result.success ? result.data : [];

        // 검색 및 필터 적용
        let filteredRequests = requests;

        if (searchQuery) {
            filteredRequests = filteredRequests.filter(request =>
                request.file_name.toLowerCase().includes(searchQuery.toLowerCase())
            );
        }

        if (statusFilter) {
            filteredRequests = filteredRequests.filter(request => request.status === statusFilter);
        }

        // 정렬 적용
        filteredRequests.sort((a, b) => {
            switch (sortBy) {
                case 'requested_at_asc':
                    return new Date(a.requested_at) - new Date(b.requested_at);
                case 'file_name_asc':
                    return a.file_name.localeCompare(b.file_name);
                case 'file_name_desc':
                    return b.file_name.localeCompare(a.file_name);
                case 'status_asc':
                    return a.status.localeCompare(b.status);
                default:
                    return new Date(b.requested_at) - new Date(a.requested_at);
            }
        });

        // 페이지네이션 적용
        const startIndex = (currentPage - 1) * perPage;
        const endIndex = startIndex + perPage;
        currentRequests = filteredRequests.slice(startIndex, endIndex);

        totalPages = Math.ceil(filteredRequests.length / perPage);

        renderAnalysisRequests();
        updatePagination();
        updateStats(filteredRequests);
    } catch (error) {
        console.error('Error loading analysis requests:', error);
        showNotification('분석 요청 목록을 불러오는데 실패했습니다.', 'error');
        showEmpty();
    }
}

function showLoading() {
    document.getElementById('loading-state').style.display = 'block';
    document.getElementById('empty-state').style.display = 'none';
}

function showEmpty() {
    document.getElementById('loading-state').style.display = 'none';
    document.getElementById('empty-state').style.display = 'block';
}

function renderAnalysisRequests() {
    const container = document.getElementById('requests-container');
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');

    loadingState.style.display = 'none';

    if (currentRequests.length === 0) {
        emptyState.style.display = 'block';
        const requestListItems = container.querySelectorAll('.px-6.py-4.hover\\:bg-gray-50');
        requestListItems.forEach(item => item.remove());
        return;
    }

    emptyState.style.display = 'none';

    const requestsHtml = currentRequests.map(request => `
        <div class="px-6 py-4 hover:bg-gray-50">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    ${getAnalysisStatusIcon(request.status)}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-3">
                        <p class="text-sm font-medium text-gray-900 truncate cursor-pointer hover:text-purple-600"
                           onclick="showAnalysisDetails(${request.id})">
                            ${request.file_name}
                        </p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadge(request.status)}">
                            ${getStatusLabel(request.status)}
                        </span>
                    </div>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span>요청시간: ${formatDate(request.requested_at)}</span>
                        ${request.completed_at ? `<span>완료시간: ${formatDate(request.completed_at)}</span>` : ''}
                        <span>${formatFileSize(request.file_size)}</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    ${request.status === 'completed' ? `
                        <button type="button" onclick="showAnalysisDetails(${request.id})"
                                class="text-green-600 hover:text-green-800 p-1"
                                title="분석 결과 보기">
                            <i class="fa fa-plus h-5 w-5"></i>
                        </button>
                    ` : ''}
                    <button type="button" onclick="deleteAnalysisRequest(${request.id})"
                            class="text-red-600 hover:text-red-800 p-1"
                            title="요청 삭제">
                        <i class="fa fa-plus h-5 w-5"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');

    // 기존 요청 목록 항목들만 제거
    const requestListItems = container.querySelectorAll('.px-6.py-4.hover\\:bg-gray-50');
    requestListItems.forEach(item => item.remove());

    // 새로운 요청 목록 추가
    container.insertAdjacentHTML('beforeend', requestsHtml);
}

function getAnalysisStatusIcon(status) {
    const iconClasses = {
        'pending': 'text-yellow-500',
        'processing': 'text-blue-500',
        'completed': 'text-green-500',
        'failed': 'text-red-500'
    };

    const iconClass = iconClasses[status] || 'text-gray-500';

    return `<i class="fa fa-plus h-8 w-8 ${iconClass}"></i>`;
}

function getStatusBadge(status) {
    const badges = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'processing': 'bg-blue-100 text-blue-800',
        'completed': 'bg-green-100 text-green-800',
        'failed': 'bg-red-100 text-red-800'
    };
    return badges[status] || 'bg-gray-100 text-gray-800';
}

function getStatusLabel(status) {
    const labels = {
        'pending': '대기',
        'processing': '처리중',
        'completed': '완료',
        'failed': '실패'
    };
    return labels[status] || status;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('ko-KR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function handleSearch() {
    searchQuery = document.getElementById('search-input').value.trim();
    currentPage = 1;
    loadAnalysisRequests();
}

function handleFilterChange() {
    statusFilter = document.getElementById('status-filter').value;
    currentPage = 1;
    loadAnalysisRequests();
}

function handleSortChange() {
    sortBy = document.getElementById('sort-select').value;
    currentPage = 1;
    loadAnalysisRequests();
}

function handlePerPageChange() {
    perPage = parseInt(document.getElementById('per-page-select').value);
    currentPage = 1;
    loadAnalysisRequests();
}

function clearFilters() {
    document.getElementById('search-input').value = '';
    document.getElementById('status-filter').value = '';
    document.getElementById('sort-select').value = 'requested_at_desc';

    searchQuery = '';
    statusFilter = '';
    sortBy = 'requested_at_desc';
    currentPage = 1;

    loadAnalysisRequests();
}

function updatePagination() {
    const paginationContainer = document.getElementById('pagination-container');
    const pageInfo = document.getElementById('page-info');
    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');

    if (totalPages <= 1) {
        paginationContainer.style.display = 'none';
        return;
    }

    paginationContainer.style.display = 'block';
    pageInfo.textContent = `${(currentPage - 1) * perPage + 1}-${Math.min(currentPage * perPage, currentRequests.length)} / ${currentRequests.length}`;

    prevBtn.disabled = currentPage <= 1;
    nextBtn.disabled = currentPage >= totalPages;
}

function updateStats(allRequests) {
    const totalCount = document.getElementById('total-requests-count');
    if (totalCount) {
        totalCount.textContent = allRequests.length;
    }
}

function changePage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    loadAnalysisRequests();
}

async function deleteAnalysisRequest(requestId) {
    if (!confirm('정말로 이 분석 요청을 삭제하시겠습니까?')) return;

    try {
        const response = await fetch(`/api/sandbox/analysis-requests/${requestId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        if (!response.ok) {
            throw new Error('분석 요청 삭제에 실패했습니다.');
        }

        showNotification('분석 요청이 삭제되었습니다.', 'success');
        loadAnalysisRequests();
    } catch (error) {
        console.error('Error deleting analysis request:', error);
        showNotification('분석 요청 삭제에 실패했습니다.', 'error');
    }
}

async function showAnalysisDetails(requestId) {
    try {
        // Mock 데이터에서 해당 요청 찾기
        const mockData = [
            {
                "id": 1,
                "file_name": "AI_기술_동향_보고서_2025.pdf",
                "file_size": 2048576,
                "file_type": "pdf",
                "status": "completed",
                "priority": "high",
                "requester_name": "김혁신",
                "department": "기술연구팀",
                "analysis_type": "문서 요약 분석",
                "description": "2025년 AI 기술 동향에 대한 종합 분석 보고서 요약 및 핵심 인사이트 추출",
                "progress_percentage": 100,
                "result_summary": "AI 기술 발전 트렌드와 비즈니스 적용 방안에 대한 포괄적 분석 완료. 생성형 AI, 멀티모달 AI 등 주요 기술 영역별 상세 요약 제공.",
                "ai_analysis_result": {"sections": 15, "key_insights": 8, "recommendations": 12, "confidence_score": 0.92},
                "requested_at": "2025-09-08T09:00:00",
                "completed_at": "2025-09-10T13:45:00"
            },
            {
                "id": 2,
                "file_name": "스마트시티_플랫폼_제안서.docx",
                "file_size": 1536000,
                "file_type": "docx",
                "status": "completed",
                "priority": "high",
                "requester_name": "박도시",
                "department": "사업개발팀",
                "analysis_type": "제안서 분석",
                "description": "스마트시티 플랫폼 구축 제안서 내용 분석 및 핵심 요소 추출",
                "progress_percentage": 100,
                "result_summary": "스마트시티 플랫폼의 기술 아키텍처, 예산 계획, 구현 로드맵 등 핵심 요소들이 체계적으로 분석됨.",
                "ai_analysis_result": {"sections": 12, "budget_analysis": "상세", "technical_specs": "완료"},
                "requested_at": "2025-09-10T11:00:00",
                "completed_at": "2025-09-12T15:30:00"
            }
        ];

        const request = mockData.find(req => req.id === requestId);

        if (!request) {
            throw new Error('요청된 분석 결과를 찾을 수 없습니다.');
        }
        
        const modal = document.getElementById('analysis-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalContent = document.getElementById('modal-content');

        modalTitle.textContent = `분석 결과 - ${request.file_name}`;

        modalContent.innerHTML = `
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">파일명</label>
                    <p class="mt-1 text-sm text-gray-900">${request.file_name}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">상태</label>
                    <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadge(request.status)}">
                        ${getStatusLabel(request.status)}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">요청 시간</label>
                    <p class="mt-1 text-sm text-gray-900">${formatDate(request.requested_at)}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">완료 시간</label>
                    <p class="mt-1 text-sm text-gray-900">${request.completed_at ? formatDate(request.completed_at) : '처리 중'}</p>
                </div>
            </div>
            ${request.result_summary ? `
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">분석 결과 요약</label>
                    <div class="bg-gray-50 rounded-md p-4 max-h-64 overflow-y-auto">
                        <p class="text-sm text-gray-900">${request.result_summary}</p>
                    </div>
                </div>
            ` : ''}
            ${request.ai_analysis_result ? `
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">AI 분석 상세</label>
                    <div class="bg-blue-50 rounded-md p-4">
                        <pre class="text-sm text-gray-900 whitespace-pre-wrap">${JSON.stringify(request.ai_analysis_result, null, 2)}</pre>
                    </div>
                </div>
            ` : ''}
            ${request.error_message ? `
                <div class="mt-6">
                    <label class="block text-sm font-medium text-red-700 mb-2">오류 메시지</label>
                    <div class="bg-red-50 rounded-md p-4">
                        <p class="text-sm text-red-900">${request.error_message}</p>
                    </div>
                </div>
            ` : ''}
        `;

        modal.style.display = 'block';
    } catch (error) {
        console.error('Error loading analysis details:', error);
        showNotification('분석 결과를 불러오는데 실패했습니다.', 'error');
    }
}

function closeAnalysisModal() {
    document.getElementById('analysis-modal').style.display = 'none';
}

function refreshAnalysisRequests() {
    currentPage = 1;
    loadAnalysisRequests();
    showNotification('분석 요청 목록이 새로고침되었습니다.', 'success');
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    } text-white`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
</div>