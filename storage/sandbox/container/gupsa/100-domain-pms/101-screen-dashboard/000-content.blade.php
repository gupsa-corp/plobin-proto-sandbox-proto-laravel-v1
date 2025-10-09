{{-- 샌드박스 대시보드 템플릿 --}}
<?php
    require_once __DIR__ . '/../../../../../../bootstrap.php';
    use App\Services\TemplateCommonService;

    $screenInfo = TemplateCommonService::getCurrentTemplateScreenInfo();
    $uploadPaths = TemplateCommonService::getTemplateUploadPaths();

    // 현재 컨텍스트 정보 추출
    $sandboxName = 'gupsa'; // 현재 샌드박스명
    $domainName = '100-domain-pms'; // 현재 도메인명

    // URL에서 자동 감지
    $currentPath = $_SERVER['REQUEST_URI'] ?? '';
    if (preg_match('/\/sandbox\/([^\/]+)\/([^\/]+)\//', $currentPath, $matches)) {
        $sandboxName = $matches[1];
        $domainName = $matches[2];
    }
?>
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6"
     x-data="dashboardData()"
     x-init="loadDashboardStats()"
     x-cloak>
    {{-- 헤더 --}}
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-xl">📊</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">프로젝트 대시보드</h1>
                        <p class="text-gray-600">실시간 프로젝트 현황을 한눈에 확인하세요</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">마지막 업데이트</div>
                    <div class="text-lg font-semibold text-gray-900" x-text="lastUpdated">로딩 중...</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 통계 카드들 --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">전체 프로젝트</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.totalProjects">-</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span class="text-blue-600">📁</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-500">+12%</span>
                <span class="text-gray-500 ml-1">지난 달 대비</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">진행 중</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.activeProjects">-</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span class="text-green-600">⚡</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-500">+5</span>
                <span class="text-gray-500 ml-1">이번 주</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">완료</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.completedProjects">-</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span class="text-purple-600">✅</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-500">+3</span>
                <span class="text-gray-500 ml-1">이번 주</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">팀 멤버</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="stats.teamMembers">-</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <span class="text-orange-600">👥</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-green-500">+7</span>
                <span class="text-gray-500 ml-1">지난 달 대비</span>
            </div>
        </div>
    </div>

    {{-- 최근 활동 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">최근 활동</h3>
            <div class="space-y-4">
                <div x-show="recentActivities.length === 0" class="text-gray-500 text-sm">
                    활동 기록을 로딩 중...
                </div>
                <template x-for="(activity, index) in recentActivities" :key="activity.id">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 text-sm" x-text="index + 1"></span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900" x-text="activity.name + ' 업데이트'"></p>
                            <p class="text-xs text-gray-500" x-text="formatTimeAgo(activity.updated_at)"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">프로젝트 진행률</h3>
            <div class="space-y-4">
                <div x-show="projectProgress.length === 0" class="text-gray-500 text-sm">
                    프로젝트 진행률을 로딩 중...
                </div>
                <template x-for="project in projectProgress" :key="project.id">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700" x-text="project.name"></span>
                            <span class="text-gray-500" x-text="project.progress + '%'"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" :style="`width: ${project.progress}%`"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript에서 URL 파싱하여 컨텍스트 정보 추출
function extractSandboxConfig() {
    const currentPath = window.location.pathname;
    // 경로 패턴: /sandbox/{sandboxName}/{domainName}/{screenName}
    const pathMatch = currentPath.match(/\/sandbox\/([^\/]+)\/([^\/]+)\//);

    if (pathMatch) {
        return {
            sandbox: pathMatch[1], // gupsa
            domain: pathMatch[2]   // 100-domain-pms
        };
    }

    // 기본값 (fallback)
    return {
        sandbox: 'gupsa',
        domain: '100-domain-pms'
    };
}

const SANDBOX_CONFIG = extractSandboxConfig();

function dashboardData() {
    return {
        stats: {
            totalProjects: 0,
            activeProjects: 0,
            completedProjects: 0,
            teamMembers: 0
        },
        recentActivities: [],
        projectProgress: [],
        lastUpdated: '로딩 중...',

        async loadDashboardStats() {
            try {
                // 대시보드 통계 API와 프로젝트 목록 API 모두 호출
                const [statsResponse, projectsResponse] = await Promise.all([
                    fetch('/api/sandbox/gupsa/pms/dashboard/stats'),
                    fetch('/api/sandbox/gupsa/pms/projects')
                ]);

                const statsResult = await statsResponse.json();
                const projectsResult = await projectsResponse.json();

                console.log('Dashboard 통계 API 호출 성공:', statsResult);
                console.log('Dashboard 프로젝트 API 호출 성공:', projectsResult);

                // 통계 데이터 설정
                if (statsResult.projects_count !== undefined) {
                    this.stats = {
                        totalProjects: statsResult.projects_count,
                        activeProjects: Math.floor(statsResult.projects_count * 0.6), // 임시 계산
                        completedProjects: Math.floor(statsResult.projects_count * 0.3), // 임시 계산
                        teamMembers: Math.max(8, statsResult.projects_count * 2)
                    };
                }

                // 프로젝트 데이터가 있으면 추가 처리
                if (projectsResult.data && Array.isArray(projectsResult.data)) {
                    this.generateProjectData(projectsResult.data);
                }

                this.lastUpdated = this.formatDateTime(statsResult.generated_at || new Date().toISOString());

            } catch (error) {
                console.error('Dashboard 데이터 로딩 실패:', error);
                this.setFallbackData();
            }
        },

        generateProjectData(projects) {
            // 최근 활동 생성 (업데이트 시간 기준)
            this.recentActivities = projects
                .sort((a, b) => new Date(b.updated_at || b.created_at || Date.now()) - new Date(a.updated_at || a.created_at || Date.now()))
                .slice(0, 5)
                .map(project => ({
                    id: project.id,
                    name: project.name,
                    updated_at: project.updated_at || project.created_at || new Date().toISOString()
                }));

            // 프로젝트 진행률 (상위 5개)
            this.projectProgress = projects
                .filter(p => p.progress > 0)
                .sort((a, b) => (b.progress || 0) - (a.progress || 0))
                .slice(0, 5)
                .map(project => ({
                    id: project.id,
                    name: project.name,
                    progress: project.progress || 0
                }));
        },

        setFallbackData() {
            // 실패 시 - 데이터가 없을 때 처리
            console.error('샌드박스에서 값을 조회하지 못했습니다.');
            this.stats = {
                totalProjects: 0,
                activeProjects: 0,
                completedProjects: 0,
                teamMembers: 0
            };
            this.recentActivities = [];
            this.projectProgress = [];
            this.lastUpdated = this.formatDateTime(new Date().toISOString());
        },

        formatDateTime(datetime) {
            if (!datetime) return '알 수 없음';
            const date = new Date(datetime);
            return date.toLocaleString('ko-KR', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        formatTimeAgo(datetime) {
            if (!datetime) return '알 수 없음';
            const now = new Date();
            const past = new Date(datetime);
            const diffInMinutes = Math.floor((now - past) / (1000 * 60));

            if (diffInMinutes < 1) return '방금 전';
            if (diffInMinutes < 60) return `${diffInMinutes}분 전`;

            const diffInHours = Math.floor(diffInMinutes / 60);
            if (diffInHours < 24) return `${diffInHours}시간 전`;

            const diffInDays = Math.floor(diffInHours / 24);
            if (diffInDays < 30) return `${diffInDays}일 전`;

            return past.toLocaleDateString('ko-KR');
        }
    }
}
</script>

