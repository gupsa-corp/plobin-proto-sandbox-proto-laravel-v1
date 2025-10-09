<div x-data="userPermissions()" x-init="init()" class="p-6 bg-white">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">현재 페이지 권한 확인</h1>
        <p class="text-gray-600">현재 프로젝트에서의 사용자 권한을 확인할 수 있습니다.</p>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex items-center justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        <span class="ml-2 text-gray-600">권한 정보를 불러오는 중...</span>
    </div>

    <!-- Error State -->
    <div x-show="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">오류가 발생했습니다</h3>
                <div class="mt-2 text-sm text-red-700" x-text="errorMessage"></div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div x-show="!loading && !error && data" class="space-y-6">
        <!-- 사용자 정보 -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">사용자 정보</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">이름</dt>
                    <dd class="text-sm text-gray-900" x-text="data?.user?.name"></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">이메일</dt>
                    <dd class="text-sm text-gray-900" x-text="data?.user?.email"></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">사용자 ID</dt>
                    <dd class="text-sm text-gray-900" x-text="data?.user?.id"></dd>
                </div>
            </div>
        </div>

        <!-- 현재 프로젝트 정보 -->
        <div class="bg-blue-50 rounded-lg p-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">현재 프로젝트</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">프로젝트명</dt>
                    <dd class="text-sm text-gray-900" x-text="data?.current_project?.name"></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">프로젝트 ID</dt>
                    <dd class="text-sm text-gray-900" x-text="data?.current_project?.id"></dd>
                </div>
            </div>
        </div>

        <!-- 권한 정보 -->
        <div class="bg-green-50 rounded-lg p-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">현재 프로젝트에서의 권한</h2>
            
            <!-- 역할 -->
            <div class="mb-4">
                <dt class="text-sm font-medium text-gray-500 mb-2">보유 역할</dt>
                <dd class="flex flex-wrap gap-2">
                    <template x-for="(role, index) in data?.current_permissions?.roles || []" :key="`role-${index}`">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="role"></span>
                    </template>
                    <span x-show="!data?.current_permissions?.roles?.length" class="text-sm text-gray-400">역할이 없습니다</span>
                </dd>
            </div>

            <!-- 구체적 권한 -->
            <div class="mb-4">
                <dt class="text-sm font-medium text-gray-500 mb-2">구체적 권한</dt>
                <dd class="flex flex-wrap gap-2">
                    <template x-for="(ability, index) in data?.current_permissions?.abilities || []" :key="`ability-${index}`">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" x-text="ability"></span>
                    </template>
                    <span x-show="!data?.current_permissions?.abilities?.length" class="text-sm text-gray-400">구체적 권한이 없습니다</span>
                </dd>
            </div>

            <!-- 주요 권한 상태 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-center">
                    <svg x-show="data?.current_permissions?.can_edit" class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <svg x-show="!data?.current_permissions?.can_edit" class="h-5 w-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium" 
                          :class="data?.current_permissions?.can_edit ? 'text-green-700' : 'text-red-700'">
                        프로젝트 수정
                    </span>
                </div>
                
                <div class="flex items-center">
                    <svg x-show="data?.current_permissions?.can_delete" class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <svg x-show="!data?.current_permissions?.can_delete" class="h-5 w-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium" 
                          :class="data?.current_permissions?.can_delete ? 'text-green-700' : 'text-red-700'">
                        프로젝트 삭제
                    </span>
                </div>

                <div class="flex items-center">
                    <svg x-show="data?.current_permissions?.can_manage_members" class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <svg x-show="!data?.current_permissions?.can_manage_members" class="h-5 w-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium" 
                          :class="data?.current_permissions?.can_manage_members ? 'text-green-700' : 'text-red-700'">
                        멤버 관리
                    </span>
                </div>
            </div>
        </div>

        <!-- 새로고침 버튼 -->
        <div class="flex justify-end">
            <button @click="fetchPermissions()" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                새로고침
            </button>
        </div>

        <!-- 생성 시간 -->
        <div class="text-xs text-gray-500 text-center" x-show="data?.generated_at">
            마지막 업데이트: <span x-text="data?.generated_at"></span>
        </div>
    </div>
</div>

<script>
function userPermissions() {
    return {
        loading: false,
        error: false,
        errorMessage: '',
        data: null,

        init() {
            this.fetchPermissions();
        },

        async fetchPermissions() {
            this.loading = true;
            this.error = false;
            this.errorMessage = '';

            try {
                // URL에서 프로젝트 ID 추출
                const urlParts = window.location.pathname.split('/');
                const projectIndex = urlParts.findIndex(part => part === 'projects');
                const projectId = projectIndex !== -1 ? urlParts[projectIndex + 1] : 1;

                const response = await fetch(`/api/sandbox/gupsa/pms/current-user-permissions?project_id=${projectId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'include'
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || `HTTP ${response.status}`);
                }

                this.data = await response.json();
                
            } catch (error) {
                console.error('권한 정보 조회 실패:', error);
                this.error = true;
                this.errorMessage = error.message || '권한 정보를 불러오는 중 오류가 발생했습니다.';
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>