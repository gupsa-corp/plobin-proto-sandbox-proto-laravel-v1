<div class="min-h-screen bg-gray-50">
    <!-- 헤더 네비게이션 -->
    @include('700-page-rfx-document-blocks.100-header-navigation')

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
            <!-- 좌측: 필터 + 블록 목록 (3컬럼) -->
            <div class="lg:col-span-3 space-y-4">
                <!-- 필터 패널 -->
                @include('700-page-rfx-document-blocks.200-filter-panel')

                <!-- 통계 패널 -->
                @include('700-page-rfx-document-blocks.300-statistics-panel')

                <!-- 블록 목록 -->
                @include('700-page-rfx-document-blocks.200-block-list')
            </div>

            <!-- 우측: 블록 상세 (1컬럼) -->
            <div class="lg:col-span-1">
                @include('700-page-rfx-document-blocks.200-block-detail-panel')
            </div>
        </div>
    </div>

    <!-- 이미지 모달 -->
    @include('700-page-rfx-document-blocks.200-modal-block-image')

    <!-- 편집 모달 -->
    @include('700-page-rfx-document-blocks.200-modal-block-edit')

    <!-- JavaScript -->
    @include('700-page-rfx-document-blocks.400-scripts')
</div>
