<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
    <div class="flex space-x-2 overflow-x-auto">
        <!-- 프로젝트 리스트 탭 -->
        <a href="{{ route('pms.unified', ['tab' => 'projects']) }}"
           wire:navigate
           class="px-4 py-2 rounded-lg transition-colors flex items-center whitespace-nowrap {{ $currentTab === 'projects' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            프로젝트 리스트
        </a>

        <!-- 캘린더 탭 -->
        <a href="{{ route('pms.unified', ['tab' => 'calendar']) }}"
           wire:navigate
           class="px-4 py-2 rounded-lg transition-colors flex items-center whitespace-nowrap {{ $currentTab === 'calendar' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            캘린더 뷰
        </a>

        <!-- 칸반 보드 탭 -->
        <a href="{{ route('pms.unified', ['tab' => 'kanban']) }}"
           wire:navigate
           class="px-4 py-2 rounded-lg transition-colors flex items-center whitespace-nowrap {{ $currentTab === 'kanban' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
            </svg>
            칸반 보드
        </a>

        <!-- 간트 차트 탭 -->
        <a href="{{ route('pms.unified', ['tab' => 'gantt']) }}"
           wire:navigate
           class="px-4 py-2 rounded-lg transition-colors flex items-center whitespace-nowrap {{ $currentTab === 'gantt' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            간트 차트
        </a>

        <!-- 테이블 뷰 탭 -->
        <a href="{{ route('pms.unified', ['tab' => 'table']) }}"
           wire:navigate
           class="px-4 py-2 rounded-lg transition-colors flex items-center whitespace-nowrap {{ $currentTab === 'table' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            테이블 뷰
        </a>
    </div>
</div>
