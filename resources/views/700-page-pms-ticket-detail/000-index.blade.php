<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-4 mb-4">
                <button onclick="history.back()" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <h1 class="text-3xl font-bold text-gray-900">티켓 #{{ $ticket['id'] }}</h1>
                <span class="px-3 py-1 text-sm font-medium rounded-full
                    @if($ticket['status'] === 'open') bg-green-100 text-green-800
                    @elseif($ticket['status'] === 'in_progress') bg-blue-100 text-blue-800
                    @elseif($ticket['status'] === 'resolved') bg-purple-100 text-purple-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    @if($ticket['status'] === 'open') 열림
                    @elseif($ticket['status'] === 'in_progress') 진행중
                    @elseif($ticket['status'] === 'resolved') 해결됨
                    @else 닫힘
                    @endif
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Ticket Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-start">
                            <h2 class="text-xl font-semibold text-gray-900">{{ $ticket['title'] }}</h2>
                            <button wire:click="toggleEditForm" 
                                    class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        @if($showEditForm)
                        <!-- Edit Form -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">제목</label>
                                <input type="text" value="{{ $ticket['title'] }}" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">설명</label>
                                <textarea rows="4" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ $ticket['description'] }}</textarea>
                            </div>
                            <div class="flex space-x-3">
                                <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    저장
                                </button>
                                <button wire:click="toggleEditForm" 
                                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                                    취소
                                </button>
                            </div>
                        </div>
                        @else
                        <!-- View Mode -->
                        <div>
                            <h3 class="font-medium text-gray-900 mb-2">설명</h3>
                            <p class="text-gray-700 leading-relaxed">{{ $ticket['description'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Attachments -->
                @if(!empty($attachments))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">첨부파일</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($attachments as $attachment)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $attachment['name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $attachment['size'] }} • {{ $attachment['uploaded_at'] }}</div>
                                    </div>
                                </div>
                                <button class="text-blue-600 hover:text-blue-800">다운로드</button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Comments -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">댓글 ({{ count($comments) }})</h3>
                    </div>
                    
                    <div class="p-6">
                        <!-- Existing Comments -->
                        <div class="space-y-4 mb-6">
                            @foreach($comments as $comment)
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                        {{ $comment['avatar'] }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="font-medium text-gray-900">{{ $comment['author'] }}</span>
                                        <span class="text-sm text-gray-500">{{ $comment['created_at'] }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ $comment['content'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Add Comment -->
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        👤
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <textarea wire:model="newComment" 
                                              rows="3" 
                                              placeholder="댓글을 입력하세요..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    <div class="mt-3 flex justify-end">
                                        <button wire:click="addComment" 
                                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                            댓글 추가
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Status Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-medium text-gray-900 mb-4">상태 변경</h3>
                    <div class="space-y-2">
                        <button wire:click="updateTicketStatus('open')" 
                                class="w-full text-left px-3 py-2 rounded-md {{ $ticket['status'] === 'open' ? 'bg-green-100 text-green-800' : 'text-gray-700 hover:bg-gray-100' }}">
                            열림
                        </button>
                        <button wire:click="updateTicketStatus('in_progress')" 
                                class="w-full text-left px-3 py-2 rounded-md {{ $ticket['status'] === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'text-gray-700 hover:bg-gray-100' }}">
                            진행중
                        </button>
                        <button wire:click="updateTicketStatus('resolved')" 
                                class="w-full text-left px-3 py-2 rounded-md {{ $ticket['status'] === 'resolved' ? 'bg-purple-100 text-purple-800' : 'text-gray-700 hover:bg-gray-100' }}">
                            해결됨
                        </button>
                        <button wire:click="updateTicketStatus('closed')" 
                                class="w-full text-left px-3 py-2 rounded-md {{ $ticket['status'] === 'closed' ? 'bg-gray-100 text-gray-800' : 'text-gray-700 hover:bg-gray-100' }}">
                            닫힘
                        </button>
                    </div>
                </div>

                <!-- Ticket Info -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-medium text-gray-900 mb-4">티켓 정보</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">우선순위</dt>
                            <dd class="mt-1">
                                <select wire:change="updateTicketPriority($event.target.value)" 
                                        class="text-sm border-0 p-0 focus:ring-0">
                                    <option value="low" {{ $ticket['priority'] === 'low' ? 'selected' : '' }}>낮음</option>
                                    <option value="medium" {{ $ticket['priority'] === 'medium' ? 'selected' : '' }}>보통</option>
                                    <option value="high" {{ $ticket['priority'] === 'high' ? 'selected' : '' }}>높음</option>
                                    <option value="urgent" {{ $ticket['priority'] === 'urgent' ? 'selected' : '' }}>긴급</option>
                                </select>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">유형</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                    @if($ticket['type'] === 'bug') 버그
                                    @elseif($ticket['type'] === 'feature') 기능
                                    @elseif($ticket['type'] === 'improvement') 개선
                                    @else 기타
                                    @endif
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">보고자</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket['reporter'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">담당자</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket['assignee'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">프로젝트</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket['project'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">생성일</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket['created_at'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">수정일</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket['updated_at'] }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Tags -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-medium text-gray-900 mb-4">태그</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ticket['tags'] as $tag)
                        <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>