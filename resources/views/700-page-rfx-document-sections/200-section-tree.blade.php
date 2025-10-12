<div class="bg-white p-4 rounded-lg shadow">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">📁 섹션 구조</h3>

    @if(empty($sections))
        <p class="text-center text-gray-500 py-8">섹션이 없습니다</p>
    @else
        <div class="space-y-1">
            @foreach($sections as $section)
                @include('700-page-rfx-document-sections.200-section-tree-item', ['section' => $section, 'level' => 0])
            @endforeach
        </div>
    @endif
</div>
