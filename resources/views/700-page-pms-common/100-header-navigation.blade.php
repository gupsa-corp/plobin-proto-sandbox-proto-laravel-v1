<div class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- 로고 영역 -->
            <div class="flex items-center">
                <h1 class="text-2xl font-bold text-gray-900">PMS</h1>
            </div>

            <!-- 네비게이션 탭 -->
            <nav class="flex space-x-1">
                <a href="{{ route('pms.dashboard') }}"
                   class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('pms.dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    대시보드
                </a>

                <a href="{{ route('pms.projects') }}"
                   class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('pms.projects') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    프로젝트
                </a>

                <a href="{{ route('pms.kanban') }}"
                   class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('pms.kanban') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    칸반보드
                </a>

                <a href="{{ route('pms.gantt') }}"
                   class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('pms.gantt') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    간트차트
                </a>

                <a href="{{ route('pms.calendar') }}"
                   class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('pms.calendar') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    캘린더
                </a>

                <a href="{{ route('pms.table-view') }}"
                   class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('pms.table-view') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    테이블뷰
                </a>

                <a href="{{ route('pms.permissions') }}"
                   class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('pms.permissions') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    권한관리
                </a>
            </nav>
        </div>
    </div>
</div>
