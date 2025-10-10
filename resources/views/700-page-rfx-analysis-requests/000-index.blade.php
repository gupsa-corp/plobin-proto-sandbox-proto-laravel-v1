<div>
    <!-- RFX 탭 네비게이션 -->
    @include('100-rfx-tab-navigation')

    <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">분석 요청 관리</h1>
                    <p class="text-gray-600">문서 분석 요청을 생성하고 관리하세요</p>
                </div>
                <button wire:click="openCreateModal" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    새 요청 생성
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Requests List -->
            <div class="lg:col-span-2">
                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                                <select wire:model="statusFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">전체</option>
                                    <option value="pending">대기중</option>
                                    <option value="in_progress">진행중</option>
                                    <option value="completed">완료</option>
                                    <option value="cancelled">취소됨</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
                                <select wire:model="priorityFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">전체</option>
                                    <option value="high">높음</option>
                                    <option value="medium">보통</option>
                                    <option value="low">낮음</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">생성일</label>
                                <input type="date" wire:model="dateFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requests List -->
                <div class="space-y-4">
                    @foreach($requests as $request)
                    <div 
                        wire:click="selectRequest({{ $request['id'] }})"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 cursor-pointer hover:shadow-md transition-shadow {{ $selectedRequest && $selectedRequest['id'] === $request['id'] ? 'ring-2 ring-blue-500' : '' }}"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $request['title'] }}</h3>
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        @if($request['status'] === 'completed') bg-green-100 text-green-800
                                        @elseif($request['status'] === 'in_progress') bg-blue-100 text-blue-800
                                        @elseif($request['status'] === 'cancelled') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        @if($request['status'] === 'completed') 완료
                                        @elseif($request['status'] === 'in_progress') 진행중
                                        @elseif($request['status'] === 'cancelled') 취소됨
                                        @else 대기중
                                        @endif
                                    </span>
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        @if($request['priority'] === 'high') bg-red-100 text-red-800
                                        @elseif($request['priority'] === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        @if($request['priority'] === 'high') 높음
                                        @elseif($request['priority'] === 'medium') 보통
                                        @else 낮음
                                        @endif
                                    </span>
                                </div>
                                
                                <p class="text-gray-600 mb-3 line-clamp-2">{{ $request['description'] }}</p>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-500">
                                    <div>
                                        <span class="font-medium">요청자:</span> {{ $request['requester'] }}
                                    </div>
                                    <div>
                                        <span class="font-medium">담당자:</span> {{ $request['assignee'] ?? '미배정' }}
                                    </div>
                                    <div>
                                        <span class="font-medium">문서:</span> {{ $request['documentCount'] }}개
                                    </div>
                                    <div>
                                        <span class="font-medium">마감:</span> {{ \Carbon\Carbon::parse($request['requiredBy'])->format('m/d') }}
                                    </div>
                                </div>
                                
                                @if($request['status'] === 'in_progress')
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                        <span>진행률</span>
                                        <span>{{ $request['completedPercentage'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $request['completedPercentage'] }}%"></div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Request Detail -->
            <div class="lg:col-span-1">
                @if($selectedRequest)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">요청 상세</h3>
                        <button wire:click="deleteRequest({{ $selectedRequest['id'] }})" class="text-red-600 hover:text-red-800 text-sm">
                            삭제
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">제목</label>
                            <p class="text-gray-900">{{ $selectedRequest['title'] }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">설명</label>
                            <p class="text-gray-700 text-sm leading-relaxed">{{ $selectedRequest['description'] }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">상태</label>
                                <select 
                                    wire:change="updateRequestStatus({{ $selectedRequest['id'] }}, $event.target.value)"
                                    class="w-full border border-gray-300 rounded px-2 py-1 text-sm"
                                >
                                    <option value="pending" {{ $selectedRequest['status'] === 'pending' ? 'selected' : '' }}>대기중</option>
                                    <option value="in_progress" {{ $selectedRequest['status'] === 'in_progress' ? 'selected' : '' }}>진행중</option>
                                    <option value="completed" {{ $selectedRequest['status'] === 'completed' ? 'selected' : '' }}>완료</option>
                                    <option value="cancelled" {{ $selectedRequest['status'] === 'cancelled' ? 'selected' : '' }}>취소됨</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">우선순위</label>
                                <select 
                                    wire:change="updateRequestPriority({{ $selectedRequest['id'] }}, $event.target.value)"
                                    class="w-full border border-gray-300 rounded px-2 py-1 text-sm"
                                >
                                    <option value="low" {{ $selectedRequest['priority'] === 'low' ? 'selected' : '' }}>낮음</option>
                                    <option value="medium" {{ $selectedRequest['priority'] === 'medium' ? 'selected' : '' }}>보통</option>
                                    <option value="high" {{ $selectedRequest['priority'] === 'high' ? 'selected' : '' }}>높음</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">요청자</label>
                                <p class="text-gray-900 text-sm">{{ $selectedRequest['requester'] }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">담당자</label>
                                <p class="text-gray-900 text-sm">{{ $selectedRequest['assignee'] ?? '미배정' }}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">생성일</label>
                                <p class="text-gray-900 text-sm">{{ \Carbon\Carbon::parse($selectedRequest['createdAt'])->format('Y-m-d H:i') }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">마감일</label>
                                <p class="text-gray-900 text-sm">{{ \Carbon\Carbon::parse($selectedRequest['requiredBy'])->format('Y-m-d') }}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">문서 수</label>
                                <p class="text-gray-900 text-sm">{{ $selectedRequest['documentCount'] }}개</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">예상 시간</label>
                                <p class="text-gray-900 text-sm">{{ $selectedRequest['estimatedHours'] }}시간</p>
                            </div>
                        </div>
                        
                        @if($selectedRequest['status'] === 'completed' && isset($selectedRequest['completedAt']))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">완료일</label>
                            <p class="text-gray-900 text-sm">{{ \Carbon\Carbon::parse($selectedRequest['completedAt'])->format('Y-m-d H:i') }}</p>
                        </div>
                        @endif
                        
                        @if($selectedRequest['status'] === 'cancelled' && isset($selectedRequest['cancelReason']))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">취소 사유</label>
                            <p class="text-gray-700 text-sm">{{ $selectedRequest['cancelReason'] }}</p>
                        </div>
                        @endif
                        
                        @if($selectedRequest['status'] === 'in_progress')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">진행률</label>
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-1">
                                <span>{{ $selectedRequest['completedPercentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $selectedRequest['completedPercentage'] }}%"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">요청을 선택하세요</h3>
                    <p class="text-gray-500">왼쪽 목록에서 요청을 선택하여 상세 정보를 확인하세요.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Create Request Modal -->
        @if($showCreateModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">새 분석 요청</h3>
                        <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="createRequest" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">제목</label>
                            <input type="text" wire:model="newRequest.title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">설명</label>
                            <textarea wire:model="newRequest.description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">우선순위</label>
                                <select wire:model="newRequest.priority" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="low">낮음</option>
                                    <option value="medium">보통</option>
                                    <option value="high">높음</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">마감일</label>
                                <input type="date" wire:model="newRequest.requiredBy" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeCreateModal" class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                                취소
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                생성
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- Success Message -->
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
    </div>
    </div>
</div>