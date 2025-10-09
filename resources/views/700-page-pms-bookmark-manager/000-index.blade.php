<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">북마크 관리</h1>
                    <p class="text-gray-600 mt-2">자주 사용하는 링크를 관리하세요</p>
                </div>
                <button wire:click="openBookmarkForm" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    새 북마크 추가
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <h3 class="font-medium text-gray-900 mb-3">카테고리</h3>
                    <nav class="space-y-2">
                        @foreach($categories as $category)
                        <button wire:click="selectCategory('{{ $category['id'] }}')" 
                                class="w-full text-left px-3 py-2 rounded-md transition-colors flex justify-between items-center {{ $selectedCategory === $category['id'] ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            <span>{{ $category['name'] }}</span>
                            <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full">{{ $category['count'] }}</span>
                        </button>
                        @endforeach
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Bookmarks Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($bookmarks as $bookmark)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow">
                        <!-- Bookmark Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg">{{ $bookmark['favicon'] }}</span>
                                <h3 class="font-medium text-gray-900 truncate">{{ $bookmark['title'] }}</h3>
                            </div>
                            <div class="flex items-center space-x-1">
                                <button wire:click="openBookmarkForm({{ $bookmark['id'] }})" 
                                        class="p-1 text-gray-400 hover:text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button wire:click="deleteBookmark({{ $bookmark['id'] }})" 
                                        class="p-1 text-gray-400 hover:text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- URL -->
                        <div class="mb-3">
                            <a href="{{ $bookmark['url'] }}" 
                               target="_blank" 
                               class="text-sm text-blue-600 hover:text-blue-800 truncate block">
                                {{ $bookmark['url'] }}
                            </a>
                        </div>

                        <!-- Description -->
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $bookmark['description'] }}</p>

                        <!-- Footer -->
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span class="px-2 py-1 bg-gray-100 rounded-full">
                                {{ collect($categories)->firstWhere('id', $bookmark['category'])['name'] ?? $bookmark['category'] }}
                            </span>
                            <span>{{ $bookmark['created_at'] }}</span>
                        </div>

                        <!-- Action Button -->
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <a href="{{ $bookmark['url'] }}" 
                               target="_blank" 
                               class="w-full bg-blue-600 text-white px-3 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                방문하기
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(empty($bookmarks))
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">북마크 없음</h3>
                    <p class="mt-1 text-sm text-gray-500">선택한 카테고리에 북마크가 없습니다.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Bookmark Form Modal -->
        @if($showBookmarkForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-lg">
                <h3 class="text-lg font-semibold mb-4">
                    {{ $editingBookmark ? '북마크 편집' : '새 북마크 추가' }}
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">제목</label>
                        <input type="text" 
                               wire:model="bookmarkForm.title"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="북마크 제목을 입력하세요">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                        <input type="url" 
                               wire:model="bookmarkForm.url"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="https://example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">카테고리</label>
                        <select wire:model="bookmarkForm.category" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">카테고리 선택</option>
                            <option value="development">개발</option>
                            <option value="design">디자인</option>
                            <option value="tools">도구</option>
                            <option value="reference">참고자료</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">설명</label>
                        <textarea wire:model="bookmarkForm.description"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  rows="3"
                                  placeholder="북마크에 대한 간단한 설명을 입력하세요"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button wire:click="closeBookmarkForm" 
                            class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        취소
                    </button>
                    <button wire:click="saveBookmark" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        {{ $editingBookmark ? '수정' : '생성' }}
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>