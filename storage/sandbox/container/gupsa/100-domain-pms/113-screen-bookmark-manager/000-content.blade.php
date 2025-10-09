{{-- 북마크 매니저 샌드박스 템플릿 --}}
<?php
    require_once __DIR__ . '/../../../../../../bootstrap.php';
    use App\Services\TemplateCommonService;

    $screenInfo = TemplateCommonService::getCurrentTemplateScreenInfo();
    $uploadPaths = TemplateCommonService::getTemplateUploadPaths();

    // 현재 컨텍스트 정보 추출
    $sandboxName = 'gupsa'; // 현재 샌드박스명
    $domainName = '100-domain-pms'; // 현재 도메인명

    // URL에서 자동 감지
    $currentPath = $_SERVER['REQUEST_URI'] ?? '';
    if (preg_match('/\/sandbox\/([^\/]+)\/([^\/]+)\//', $currentPath, $matches)) {
        $sandboxName = $matches[1];
        $domainName = $matches[2];
    }
?>

<div class="bookmark-manager-container min-h-screen bg-gray-50 p-6"
     x-data="bookmarkManager()"
     x-init="initializeBookmarks()"
     x-cloak>
    
    {{-- 헤더 툴바 --}}
    <div class="bg-white shadow-sm rounded-lg mb-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h2 class="text-xl font-semibold text-gray-900">즐겨찾기 관리</h2>
                    <span class="text-sm text-gray-500" x-text="bookmarkCount + '개 항목'"></span>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            @click="addFolder()">
                        <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        폴더 추가
                    </button>
                    
                    <button type="button" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            @click="addBookmark()">
                        <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        북마크 추가
                    </button>
                </div>
            </div>
            
            <div class="mt-4 flex items-center space-x-4">
                <div class="flex-1">
                    <input type="text" 
                           x-model="searchQuery"
                           @input="filterBookmarks()"
                           placeholder="북마크 검색..." 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>
                
                <div class="flex items-center space-x-2">
                    <button type="button" 
                            class="p-2 text-gray-400 hover:text-gray-500"
                            @click="expandAll()"
                            title="모두 펼치기">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <button type="button" 
                            class="p-2 text-gray-400 hover:text-gray-500"
                            @click="collapseAll()"
                            title="모두 접기">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- 북마크 트리 --}}
        <div class="p-6">
            <div x-show="filteredBookmarks.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">북마크가 없습니다</h3>
                <p class="mt-1 text-sm text-gray-500">첫 번째 북마크를 추가해보세요.</p>
                <div class="mt-6">
                    <button type="button" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                            @click="addBookmark()">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        북마크 추가
                    </button>
                </div>
            </div>
            
            <div x-show="filteredBookmarks.length > 0" id="bookmark-tree" class="space-y-2">
                <template x-for="item in filteredBookmarks" :key="item.id">
                    <div x-show="item.parent_id === null">
                        <div x-data="{ expanded: true }" class="bookmark-item">
                            {{-- 폴더 렌더링 --}}
                            <div x-show="item.type === 'folder'">
                                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg group cursor-pointer" 
                                     @click="expanded = !expanded">
                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-500 transform transition-transform duration-200" 
                                                 :class="{ 'rotate-90': expanded }"
                                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </div>
                                        
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M10 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2h-8l-2-2z"/>
                                            </svg>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-900 truncate" x-text="item.title"></div>
                                            <div class="text-sm text-gray-500" x-text="getChildCount(item.id) + '개 항목'"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button type="button" 
                                                class="p-1 text-gray-400 hover:text-blue-600"
                                                @click.stop="editBookmark(item)"
                                                title="수정">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        
                                        <button type="button" 
                                                class="p-1 text-gray-400 hover:text-red-600"
                                                @click.stop="deleteBookmark(item)"
                                                title="삭제">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                {{-- 하위 항목들 --}}
                                <div x-show="expanded" class="ml-8 space-y-1" x-transition>
                                    <template x-for="child in getChildren(item.id)" :key="child.id">
                                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg group">
                                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                                    </svg>
                                                </div>
                                                
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium text-gray-900 truncate" x-text="child.title"></div>
                                                    <div class="text-sm text-gray-500 truncate" x-text="child.url"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <a :href="child.url" 
                                                   target="_blank"
                                                   class="p-1 text-gray-400 hover:text-green-600"
                                                   title="새창에서 열기">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                    </svg>
                                                </a>
                                                
                                                <button type="button" 
                                                        class="p-1 text-gray-400 hover:text-blue-600"
                                                        @click="editBookmark(child)"
                                                        title="수정">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                
                                                <button type="button" 
                                                        class="p-1 text-gray-400 hover:text-red-600"
                                                        @click="deleteBookmark(child)"
                                                        title="삭제">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            {{-- 북마크 렌더링 --}}
                            <div x-show="item.type === 'bookmark'">
                                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg group">
                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                            </svg>
                                        </div>
                                        
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-gray-900 truncate" x-text="item.title"></div>
                                            <div class="text-sm text-gray-500 truncate" x-text="item.url"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a :href="item.url" 
                                           target="_blank"
                                           class="p-1 text-gray-400 hover:text-green-600"
                                           title="새창에서 열기">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                        
                                        <button type="button" 
                                                class="p-1 text-gray-400 hover:text-blue-600"
                                                @click="editBookmark(item)"
                                                title="수정">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        
                                        <button type="button" 
                                                class="p-1 text-gray-400 hover:text-red-600"
                                                @click="deleteBookmark(item)"
                                                title="삭제">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    
    {{-- 북마크/폴더 추가 모달 --}}
    <div x-show="showAddModal" 
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         @click.self="showAddModal = false"
         x-transition>
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" x-text="modalTitle"></h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" @click="showAddModal = false">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form @submit.prevent="saveBookmark()">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">타입</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" x-model="form.type" value="bookmark" class="h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">북마크</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" x-model="form.type" value="folder" class="h-4 w-4 text-blue-600">
                                <span class="ml-2 text-sm text-gray-700">폴더</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">제목</label>
                        <input type="text" 
                               x-model="form.title"
                               required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="mb-4" x-show="form.type === 'bookmark'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                        <input type="url" 
                               x-model="form.url"
                               placeholder="https://example.com"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">상위 폴더</label>
                        <select x-model="form.parent_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">최상위</option>
                            <template x-for="folder in folders" :key="folder.id">
                                <option :value="folder.id" x-text="folder.title"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md"
                                @click="showAddModal = false">
                            취소
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            저장
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    {{-- 수정 모달 --}}
    <div x-show="showEditModal" 
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         @click.self="showEditModal = false"
         x-transition>
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" x-text="editForm.type === 'folder' ? '폴더 수정' : '북마크 수정'"></h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" @click="showEditModal = false">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <form @submit.prevent="updateBookmark()">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">제목</label>
                        <input type="text" 
                               x-model="editForm.title"
                               required
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="mb-4" x-show="editForm.type === 'bookmark'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                        <input type="url" 
                               x-model="editForm.url"
                               placeholder="https://example.com"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">상위 폴더</label>
                        <select x-model="editForm.parent_id"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">최상위</option>
                            <template x-for="folder in folders" :key="folder.id">
                                <option :value="folder.id" 
                                        x-text="folder.title"
                                        :disabled="folder.id === editForm.id"></option>
                            </template>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md"
                                @click="showEditModal = false">
                            취소
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            수정
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function bookmarkManager() {
    return {
        bookmarks: [],
        filteredBookmarks: [],
        searchQuery: '',
        showAddModal: false,
        showEditModal: false,
        modalTitle: '북마크 추가',
        nextId: 1,
        
        form: {
            type: 'bookmark',
            title: '',
            url: '',
            parent_id: null
        },
        
        editForm: {
            id: null,
            type: 'bookmark',
            title: '',
            url: '',
            parent_id: null
        },

        get bookmarkCount() {
            return this.bookmarks.length;
        },

        get folders() {
            return this.bookmarks.filter(b => b.type === 'folder');
        },

        initializeBookmarks() {
            // localStorage에서 북마크 데이터 로드
            const saved = localStorage.getItem('sandbox_bookmarks_gupsa_pms');
            if (saved) {
                this.bookmarks = JSON.parse(saved);
                this.nextId = Math.max(...this.bookmarks.map(b => b.id), 0) + 1;
            } else {
                // 초기 데모 데이터
                this.bookmarks = [
                    {
                        id: 1,
                        type: 'folder',
                        title: '개발 도구',
                        parent_id: null,
                        order: 0,
                        created_at: new Date().toISOString()
                    },
                    {
                        id: 2,
                        type: 'bookmark',
                        title: 'GitHub',
                        url: 'https://github.com',
                        parent_id: 1,
                        order: 0,
                        created_at: new Date().toISOString()
                    },
                    {
                        id: 3,
                        type: 'bookmark',
                        title: 'Stack Overflow',
                        url: 'https://stackoverflow.com',
                        parent_id: 1,
                        order: 1,
                        created_at: new Date().toISOString()
                    },
                    {
                        id: 4,
                        type: 'bookmark',
                        title: 'Laravel 문서',
                        url: 'https://laravel.com/docs',
                        parent_id: null,
                        order: 1,
                        created_at: new Date().toISOString()
                    }
                ];
                this.nextId = 5;
                this.saveBookmarks();
            }
            this.filterBookmarks();
        },

        filterBookmarks() {
            if (!this.searchQuery.trim()) {
                this.filteredBookmarks = [...this.bookmarks];
            } else {
                const query = this.searchQuery.toLowerCase();
                this.filteredBookmarks = this.bookmarks.filter(bookmark => 
                    bookmark.title.toLowerCase().includes(query) || 
                    (bookmark.url && bookmark.url.toLowerCase().includes(query))
                );
            }
        },

        getChildren(parentId) {
            return this.filteredBookmarks.filter(b => b.parent_id === parentId);
        },

        getChildCount(parentId) {
            return this.bookmarks.filter(b => b.parent_id === parentId).length;
        },

        addBookmark() {
            this.modalTitle = '북마크 추가';
            this.form = {
                type: 'bookmark',
                title: '',
                url: '',
                parent_id: null
            };
            this.showAddModal = true;
        },

        addFolder() {
            this.modalTitle = '폴더 추가';
            this.form = {
                type: 'folder',
                title: '',
                url: '',
                parent_id: null
            };
            this.showAddModal = true;
        },

        saveBookmark() {
            const bookmark = {
                id: this.nextId++,
                type: this.form.type,
                title: this.form.title,
                url: this.form.type === 'bookmark' ? this.form.url : '',
                parent_id: this.form.parent_id || null,
                order: this.bookmarks.filter(b => b.parent_id === (this.form.parent_id || null)).length,
                created_at: new Date().toISOString()
            };

            this.bookmarks.push(bookmark);
            this.saveBookmarks();
            this.filterBookmarks();
            this.showAddModal = false;
        },

        editBookmark(bookmark) {
            this.editForm = {
                id: bookmark.id,
                type: bookmark.type,
                title: bookmark.title,
                url: bookmark.url || '',
                parent_id: bookmark.parent_id
            };
            this.showEditModal = true;
        },

        updateBookmark() {
            const index = this.bookmarks.findIndex(b => b.id === this.editForm.id);
            if (index !== -1) {
                this.bookmarks[index] = {
                    ...this.bookmarks[index],
                    title: this.editForm.title,
                    url: this.editForm.type === 'bookmark' ? this.editForm.url : '',
                    parent_id: this.editForm.parent_id || null,
                    updated_at: new Date().toISOString()
                };
                this.saveBookmarks();
                this.filterBookmarks();
            }
            this.showEditModal = false;
        },

        deleteBookmark(bookmark) {
            if (confirm(bookmark.type === 'folder' ? 
                '이 폴더와 모든 하위 항목을 삭제하시겠습니까?' : 
                '이 북마크를 삭제하시겠습니까?')) {
                
                if (bookmark.type === 'folder') {
                    // 하위 항목들도 재귀적으로 삭제
                    this.deleteRecursive(bookmark.id);
                }
                
                this.bookmarks = this.bookmarks.filter(b => b.id !== bookmark.id);
                this.saveBookmarks();
                this.filterBookmarks();
            }
        },

        deleteRecursive(parentId) {
            const children = this.bookmarks.filter(b => b.parent_id === parentId);
            children.forEach(child => {
                if (child.type === 'folder') {
                    this.deleteRecursive(child.id);
                }
                this.bookmarks = this.bookmarks.filter(b => b.id !== child.id);
            });
        },

        expandAll() {
            // Alpine.js에서 직접 DOM 조작은 권장되지 않지만, 
            // 이 경우 각 폴더의 expanded 상태를 true로 설정
            this.$nextTick(() => {
                document.querySelectorAll('[x-data*="expanded"]').forEach(el => {
                    if (el.__x && el.__x.$data) {
                        el.__x.$data.expanded = true;
                    }
                });
            });
        },

        collapseAll() {
            this.$nextTick(() => {
                document.querySelectorAll('[x-data*="expanded"]').forEach(el => {
                    if (el.__x && el.__x.$data) {
                        el.__x.$data.expanded = false;
                    }
                });
            });
        },

        saveBookmarks() {
            localStorage.setItem('sandbox_bookmarks_gupsa_pms', JSON.stringify(this.bookmarks));
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }

.bookmark-item:hover {
    background-color: #f9fafb;
}
</style>