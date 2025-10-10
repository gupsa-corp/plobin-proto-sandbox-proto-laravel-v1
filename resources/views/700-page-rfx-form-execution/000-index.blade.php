<div>
    <!-- RFX 탭 네비게이션 -->
    @include('100-rfx-tab-navigation')

    <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">폼 실행</h1>
            <p class="text-gray-600">문서 처리 폼을 실행하고 결과를 관리하세요</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Available Forms -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">사용 가능한 폼</h2>
                        
                        <!-- Type Filter -->
                        <div class="mt-4">
                            <select wire:model="typeFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">모든 타입</option>
                                <option value="extraction">추출</option>
                                <option value="validation">검증</option>
                                <option value="summary">요약</option>
                                <option value="analysis">분석</option>
                                <option value="review">검토</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($forms as $form)
                            <div 
                                wire:click="selectForm({{ $form['id'] }})"
                                class="p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 {{ $selectedForm && $selectedForm['id'] === $form['id'] ? 'ring-2 ring-blue-500 bg-blue-50' : '' }}"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900">{{ $form['name'] }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $form['description'] }}</p>
                                        
                                        <div class="mt-2 flex items-center space-x-4 text-xs text-gray-500">
                                            <span class="px-2 py-1 bg-gray-100 rounded">{{ $form['category'] }}</span>
                                            <span>v{{ $form['version'] }}</span>
                                            <span>{{ $form['executionCount'] }}회 실행</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        @if($selectedForm)
                        <div class="mt-6">
                            <button wire:click="openExecuteModal" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                폼 실행
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Execution History -->
            <div class="lg:col-span-2">
                <!-- Status Filter -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">실행 이력</h2>
                            <select wire:model="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">모든 상태</option>
                                <option value="pending">대기중</option>
                                <option value="running">실행중</option>
                                <option value="completed">완료</option>
                                <option value="failed">실패</option>
                                <option value="cancelled">취소됨</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Executions List -->
                <div class="space-y-4">
                    @foreach($executions as $execution)
                    <div 
                        wire:click="selectExecution({{ $execution['id'] }})"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 cursor-pointer hover:shadow-md transition-shadow {{ $selectedExecution && $selectedExecution['id'] === $execution['id'] ? 'ring-2 ring-blue-500' : '' }}"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $execution['formName'] }}</h3>
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        @if($execution['status'] === 'completed') bg-green-100 text-green-800
                                        @elseif($execution['status'] === 'running') bg-blue-100 text-blue-800
                                        @elseif($execution['status'] === 'failed') bg-red-100 text-red-800
                                        @elseif($execution['status'] === 'cancelled') bg-gray-100 text-gray-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        @if($execution['status'] === 'completed') 완료
                                        @elseif($execution['status'] === 'running') 실행중
                                        @elseif($execution['status'] === 'failed') 실패
                                        @elseif($execution['status'] === 'cancelled') 취소됨
                                        @else 대기중
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600 mb-3">
                                    <div>
                                        <span class="font-medium">실행자:</span> {{ $execution['executedBy'] }}
                                    </div>
                                    <div>
                                        <span class="font-medium">처리된 문서:</span> {{ $execution['documentsProcessed'] }}개
                                    </div>
                                    <div>
                                        <span class="font-medium">성공:</span> {{ $execution['successCount'] }}개
                                    </div>
                                    <div>
                                        <span class="font-medium">오류:</span> {{ $execution['errorCount'] }}개
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <div>
                                        @if($execution['startedAt'])
                                        <span>시작: {{ \Carbon\Carbon::parse($execution['startedAt'])->format('m/d H:i') }}</span>
                                        @elseif(isset($execution['scheduledAt']))
                                        <span>예정: {{ \Carbon\Carbon::parse($execution['scheduledAt'])->format('m/d H:i') }}</span>
                                        @endif
                                        
                                        @if($execution['completedAt'])
                                        <span class="ml-4">완료: {{ \Carbon\Carbon::parse($execution['completedAt'])->format('m/d H:i') }}</span>
                                        @endif
                                        
                                        @if($execution['duration'])
                                        <span class="ml-4">소요시간: {{ $execution['duration'] }}</span>
                                        @endif
                                    </div>
                                    
                                    @if($execution['resultSize'])
                                    <span>결과 크기: {{ $execution['resultSize'] }}</span>
                                    @endif
                                </div>
                                
                                @if($execution['status'] === 'running' && isset($execution['progress']))
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                        <span>진행률</span>
                                        <span>{{ $execution['progress'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $execution['progress'] }}%"></div>
                                    </div>
                                </div>
                                @endif
                                
                                @if($execution['status'] === 'failed' && isset($execution['errorMessage']))
                                <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                                    {{ $execution['errorMessage'] }}
                                </div>
                                @endif
                                
                                @if($execution['status'] === 'cancelled' && isset($execution['cancelReason']))
                                <div class="mt-2 p-2 bg-gray-50 border border-gray-200 rounded text-sm text-gray-700">
                                    {{ $execution['cancelReason'] }}
                                </div>
                                @endif
                            </div>
                            
                            <div class="ml-4 flex flex-col space-y-2">
                                @if($execution['status'] === 'completed')
                                <button wire:click.stop="downloadResult({{ $execution['id'] }})" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                    다운로드
                                </button>
                                @endif
                                
                                @if($execution['status'] === 'failed')
                                <button wire:click.stop="retryExecution({{ $execution['id'] }})" class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                    재시도
                                </button>
                                @endif
                                
                                @if($execution['status'] === 'running')
                                <button wire:click.stop="cancelExecution({{ $execution['id'] }})" class="text-sm bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                                    취소
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Execute Form Modal -->
        @if($showExecuteModal && $selectedForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $selectedForm['name'] }} 실행</h3>
                        <button wire:click="closeExecuteModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <p class="text-gray-600 mb-6">{{ $selectedForm['description'] }}</p>
                    
                    <form wire:submit.prevent="executeForm" class="space-y-4">
                        @foreach($formData as $field)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $field['label'] }}
                                @if($field['required'])
                                <span class="text-red-500">*</span>
                                @endif
                            </label>
                            
                            @if($field['type'] === 'select')
                            <select wire:model="formData.{{ $field['name'] }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" {{ $field['required'] ? 'required' : '' }}>
                                <option value="">선택하세요</option>
                                @foreach($field['options'] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                            
                            @elseif($field['type'] === 'checkbox')
                            <div class="space-y-2">
                                @foreach($field['options'] as $option)
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="formData.{{ $field['name'] }}" value="{{ $option }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $option }}</span>
                                </label>
                                @endforeach
                            </div>
                            
                            @elseif($field['type'] === 'number')
                            <input type="number" wire:model="formData.{{ $field['name'] }}" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" {{ $field['required'] ? 'required' : '' }}>
                            
                            @else
                            <input type="text" wire:model="formData.{{ $field['name'] }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" {{ $field['required'] ? 'required' : '' }}>
                            @endif
                        </div>
                        @endforeach
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeExecuteModal" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                                취소
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                실행
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- Success/Error Messages -->
        @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-2 text-green-700">{{ session('message') }}</p>
            </div>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg">
            <div class="flex">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="ml-2 text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif
    </div>
    </div>
</div>