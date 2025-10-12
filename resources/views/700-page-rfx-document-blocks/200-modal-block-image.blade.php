@if($showImageModal)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeImageModal">
    <div class="bg-white rounded-lg p-6 max-w-4xl max-h-[90vh] overflow-auto" wire:click.stop>
        <!-- 헤더 -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">블록 이미지</h3>
            <button wire:click="closeImageModal" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- 이미지 -->
        <div class="flex items-center justify-center bg-gray-100 rounded">
            @if($selectedBlockImage)
                <img src="{{ $selectedBlockImage }}" alt="블록 이미지" class="max-w-full h-auto">
            @else
                <p class="text-gray-500 py-8">이미지를 불러올 수 없습니다</p>
            @endif
        </div>

        <!-- 닫기 버튼 -->
        <div class="mt-4 flex justify-end">
            <button wire:click="closeImageModal"
                    class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                닫기
            </button>
        </div>
    </div>
</div>
@endif
