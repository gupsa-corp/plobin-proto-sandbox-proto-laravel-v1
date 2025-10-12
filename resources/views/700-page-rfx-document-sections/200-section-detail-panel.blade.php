<div class="bg-white p-6 rounded-lg shadow">
    @if($selectedSection)
        <!-- 섹션 헤더 -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                📂 섹션 {{ $selectedSection['section_number'] }}: {{ $selectedSection['title'] }}
            </h2>
        </div>

        <!-- 섹션 통계 -->
        @if(isset($selectedSection['statistics']))
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded">
                <p class="text-sm text-gray-600">블록 수</p>
                <p class="text-2xl font-bold text-blue-600">{{ $selectedSection['statistics']['total_blocks'] ?? 0 }}</p>
            </div>

            <div class="bg-green-50 p-4 rounded">
                <p class="text-sm text-gray-600">평균 신뢰도</p>
                <p class="text-2xl font-bold text-green-600">
                    {{ isset($selectedSection['statistics']['average_confidence']) ? number_format($selectedSection['statistics']['average_confidence'], 2) : 'N/A' }}
                </p>
            </div>

            <div class="bg-purple-50 p-4 rounded">
                <p class="text-sm text-gray-600">총 글자 수</p>
                <p class="text-2xl font-bold text-purple-600">{{ $selectedSection['statistics']['total_characters'] ?? 0 }}</p>
            </div>
        </div>
        @endif

        <!-- 하위 섹션 목록 -->
        @if(!empty($selectedSection['subsections']))
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">하위 섹션</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($selectedSection['subsections'] as $subsection)
                    <div wire:click="selectSection('{{ $subsection['section_id'] }}')"
                         class="p-3 border border-gray-200 rounded cursor-pointer hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-gray-900">
                                {{ $subsection['section_number'] }}. {{ $subsection['title'] }}
                            </span>
                            <span class="text-sm text-gray-500">({{ $subsection['block_count'] }}블록)</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- 섹션 내 블록 목록 -->
        @include('700-page-rfx-document-sections.200-section-blocks-list')
    @else
        <!-- 섹션 미선택 상태 -->
        <div class="flex flex-col items-center justify-center py-16">
            <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-lg text-gray-500">좌측에서 섹션을 선택해주세요</p>
        </div>
    @endif
</div>
