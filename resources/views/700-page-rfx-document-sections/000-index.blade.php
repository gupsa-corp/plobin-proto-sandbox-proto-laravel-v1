<div class="min-h-screen bg-gray-50">
    <!-- 헤더 네비게이션 -->
    @include('700-page-rfx-document-sections.100-header-navigation')

    <div class="max-w-[1920px] mx-auto px-4 py-6">
        <!-- 플래시 메시지 -->
        @if (session()->has('message'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- 좌측: 섹션 트리 (1컬럼) -->
            <div class="lg:col-span-1 space-y-4">
                <!-- 통계 패널 -->
                @include('700-page-rfx-document-sections.300-statistics-panel')

                <!-- 섹션 트리 -->
                @include('700-page-rfx-document-sections.200-section-tree')
            </div>

            <!-- 우측: 섹션 상세 (3컬럼) -->
            <div class="lg:col-span-3">
                @include('700-page-rfx-document-sections.200-section-detail-panel')
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
@include('700-page-rfx-document-sections.400-scripts')
