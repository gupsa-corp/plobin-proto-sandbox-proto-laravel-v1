<div class="p-8 bg-white rounded-lg shadow-lg max-w-md mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-4">간단한 Livewire 테스트</h1>
    
    <div class="mb-4">
        <p class="text-lg">{{ $message }}</p>
    </div>
    
    <button wire:click="increment" 
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
        클릭하세요! ({{ $clickCount }})
    </button>
    
    <div class="mt-4 text-sm text-gray-600">
        <p>이 버튼이 작동한다면 Livewire가 정상입니다.</p>
        <p>클릭할 때마다 숫자가 증가해야 합니다.</p>
    </div>
</div>