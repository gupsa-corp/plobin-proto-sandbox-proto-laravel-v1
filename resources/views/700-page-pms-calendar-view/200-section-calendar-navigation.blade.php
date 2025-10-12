<!-- Calendar Controls -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
    <div class="flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <button wire:click="previousPeriod"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200 hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <button wire:click="goToToday"
                    class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200 transition-colors">
                오늘
            </button>
        </div>

        <h2 class="text-xl font-semibold text-gray-900">
            {{ \Carbon\Carbon::parse($currentDate)->format($viewMode === 'month' ? 'Y년 M월' : 'Y년 M월 W주') }}
        </h2>

        <button wire:click="nextPeriod"
                class="p-2 hover:bg-gray-100 rounded-lg transition-all duration-200 hover:scale-105">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>
</div>
