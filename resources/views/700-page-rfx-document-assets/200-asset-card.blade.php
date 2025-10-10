<div class="bg-white rounded-xl shadow-sm overflow-hidden border-l-4 @if($asset['asset_type'] === 'invoice') border-blue-400 @elseif($asset['asset_type'] === 'payment') border-green-400 @else border-gray-400 @endif">
    <!-- 헤더 -->
    <div class="bg-gray-50 px-6 py-3 border-b">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <span class="text-lg">{{ $asset['asset_type_icon'] }}</span>
                <h3 class="text-lg font-semibold text-gray-900">{{ $asset['section_title'] }}</h3>
                <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">{{ $asset['asset_type_name'] }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="text-xs text-gray-500">섹션 {{ $loop->iteration }}</span>
                <button wire:click="toggleAssetStatus('{{ $asset['id'] }}')"
                        class="text-xl hover:scale-110 transition-transform"
                        title="상태 토글">
                    {{ $asset['status_icon'] }}
                </button>
            </div>
        </div>
    </div>

    <!-- 3-Column 그리드 레이아웃 -->
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- 📄 원문 -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                    <span class="text-blue-500 mr-2">📄</span>
                    원문
                </h4>
                <div class="bg-blue-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $asset['content'] }}</p>
                </div>
            </div>

            @if(isset($asset['summary']))
            <!-- 🤖 AI 요약 -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-green-500 mr-2">🤖</span>
                        AI 요약
                    </div>
                    <div class="flex items-center space-x-2">
                        @if(count($asset['summary']['versions']) > 1)
                        <div class="flex items-center space-x-1">
                            <span class="text-xs text-gray-500">버전:</span>
                            <select wire:model="selectedVersions.{{ $asset['id'] }}"
                                    wire:change="switchVersion('{{ $asset['id'] }}', $event.target.value)"
                                    class="text-xs bg-white border border-gray-300 rounded px-1 py-0.5 focus:ring-1 focus:ring-green-500">
                                @foreach($asset['summary']['versions'] as $version)
                                <option value="{{ $version['version_timestamp'] }}">
                                    {{ $version['edited_by'] === 'ai' ? 'AI 생성' : '사용자 편집' }} (v{{ $loop->iteration }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <button wire:click="regenerateSummary('{{ $asset['id'] }}')"
                                wire:loading.attr="disabled"
                                class="text-xs px-2 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="regenerateSummary('{{ $asset['id'] }}')">
                                🔄 재분석
                            </span>
                            <span wire:loading wire:target="regenerateSummary('{{ $asset['id'] }}')">
                                분석 중...
                            </span>
                        </button>
                        <button wire:click="openEditModal('{{ $asset['id'] }}', 'summary')"
                                class="text-xs px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                            편집
                        </button>
                    </div>
                </h4>
                <div class="bg-green-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $asset['summary']['ai_summary'] }}</p>
                </div>
            </div>

            <!-- 💡 도움되는 내용 -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                    <span class="text-purple-500 mr-2">💡</span>
                    도움되는 내용
                </h4>
                <div class="bg-purple-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $asset['summary']['helpful_content'] }}</p>
                </div>
            </div>
            @else
            <!-- Summary가 없는 경우 -->
            <div class="col-span-2">
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="text-center mb-4">
                        <p class="text-sm text-gray-600 mb-2">AI 요약이 아직 생성되지 않았습니다.</p>
                        <button wire:click="generateSummary('{{ $asset['id'] }}')"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="generateSummary('{{ $asset['id'] }}')">
                                🤖 AI 요약 생성
                            </span>
                            <span wire:loading wire:target="generateSummary('{{ $asset['id'] }}')">
                                생성 중...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
