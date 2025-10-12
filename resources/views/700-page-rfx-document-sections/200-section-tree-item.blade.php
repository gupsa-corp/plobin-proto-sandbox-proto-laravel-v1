@php
    $indent = $level * 20;
    $isExpanded = in_array($section['section_id'], $expandedSections);
    $hasSubsections = !empty($section['subsections']);
@endphp

<div class="section-tree-item">
    <div wire:click="selectSection('{{ $section['section_id'] }}')"
         class="flex items-center space-x-2 p-2 rounded cursor-pointer hover:bg-gray-100 transition {{ isset($selectedSection) && $selectedSection['section_id'] === $section['section_id'] ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}"
         style="padding-left: {{ $indent + 8 }}px">

        <!-- 펼치기/접기 버튼 -->
        @if($hasSubsections)
            <button wire:click.stop="toggleSection('{{ $section['section_id'] }}')"
                    class="text-gray-500 hover:text-gray-700 w-4">
                {{ $isExpanded ? '▼' : '▶' }}
            </button>
        @else
            <span class="w-4"></span>
        @endif

        <!-- 섹션 아이콘 -->
        <span>{{ $level === 0 ? '📁' : '📄' }}</span>

        <!-- 섹션 번호 및 제목 -->
        <div class="flex-1 min-w-0">
            <span class="font-medium text-gray-900 truncate block">
                {{ $section['section_number'] }}. {{ $section['title'] }}
            </span>
            <span class="text-xs text-gray-500">({{ $section['block_count'] }}블록)</span>
        </div>
    </div>

    <!-- 하위 섹션 (재귀) -->
    @if($hasSubsections && $isExpanded)
        @foreach($section['subsections'] as $subsection)
            @include('700-page-rfx-document-sections.200-section-tree-item', [
                'section' => $subsection,
                'level' => $level + 1
            ])
        @endforeach
    @endif
</div>
