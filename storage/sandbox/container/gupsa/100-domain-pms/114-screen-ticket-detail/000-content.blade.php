<div class="min-h-screen bg-gray-50 p-6" x-data="ticketDetailData()" x-init="loadTicketDetail()">
    {{-- Header with Bookmark Button --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <button @click="goBack()" 
                        class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span class="text-purple-600">🎫</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900" x-text="ticket.name || '티켓 상세'"></h1>
                    <p class="text-gray-600">티켓의 세부 정보를 확인하고 관리하세요</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button @click="toggleBookmark()"
                        :class="isBookmarked ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-gray-500 hover:bg-yellow-500'"
                        class="px-4 py-2 text-white rounded-lg flex items-center space-x-2"
                        :title="isBookmarked ? '즐겨찾기에서 제거' : '즐겨찾기에 추가'">
                    <svg class="w-4 h-4" :fill="isBookmarked ? 'currentColor' : 'none'" 
                         :stroke="isBookmarked ? 'none' : 'currentColor'" 
                         viewBox="0 0 20 20">
                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/>
                    </svg>
                    <span x-text="isBookmarked ? '즐겨찾기 해제' : '즐겨찾기'"></span>
                </button>
                <button @click="editTicket()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    편집
                </button>
            </div>
        </div>
    </div>

    {{-- Loading State --}}
    <div x-show="loading" class="flex items-center justify-center py-12">
        <div class="text-gray-500">로딩 중...</div>
    </div>

    {{-- Ticket Detail Content --}}
    <div x-show="!loading && ticket.id" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Basic Information --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">기본 정보</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">티켓 이름</label>
                        <div class="text-gray-900" x-text="ticket.name"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                        <div class="text-gray-600" x-text="ticket.id"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">상태</label>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full"
                              :class="getStatusClass(ticket.status)"
                              x-text="getStatusText(ticket.status)"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">우선순위</label>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full"
                              :class="getPriorityClass(ticket.priority)"
                              x-text="getPriorityText(ticket.priority)"></span>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">설명</h2>
                <div class="text-gray-700 whitespace-pre-wrap" x-text="ticket.description || '설명이 없습니다.'"></div>
            </div>

            {{-- Progress Section --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">진행률</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">전체 진행률</span>
                        <span class="text-sm text-gray-600" x-text="`${ticket.progress || 0}%`"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full transition-all duration-300"
                             :style="`width: ${ticket.progress || 0}%`"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Timeline --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">타임라인</h2>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">생성됨</div>
                            <div class="text-xs text-gray-500" x-text="formatDate(ticket.created_at)"></div>
                        </div>
                    </div>
                    <div x-show="ticket.updated_at !== ticket.created_at" class="flex items-start space-x-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">최종 수정</div>
                            <div class="text-xs text-gray-500" x-text="formatDate(ticket.updated_at)"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">액션</h2>
                <div class="space-y-3">
                    <button @click="editTicket()"
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>편집</span>
                    </button>
                    <button @click="deleteTicket()"
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>삭제</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Error State --}}
    <div x-show="!loading && !ticket.id" class="bg-white rounded-lg shadow-sm p-8 text-center">
        <div class="text-gray-500 mb-4">
            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">티켓을 찾을 수 없습니다</h3>
        <p class="text-gray-600 mb-6">요청하신 티켓이 존재하지 않거나 접근할 수 없습니다.</p>
        <button @click="goBack()" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            목록으로 돌아가기
        </button>
    </div>
</div>

<script>
function ticketDetailData() {
    return {
        ticket: {},
        loading: false,
        isBookmarked: false,

        async loadTicketDetail() {
            this.loading = true;
            try {
                // URL에서 티켓 ID 추출
                const urlParams = new URLSearchParams(window.location.search);
                const ticketId = urlParams.get('id') || 1; // 기본값 1

                const response = await fetch(`/api/sandbox/gupsa/pms/projects/${ticketId}`);
                const result = await response.json();

                if (result.success && result.data) {
                    this.ticket = result.data;
                    this.checkBookmarkStatus();
                } else {
                    console.error('티켓 로드 실패:', result.message || result);
                    this.ticket = {};
                }
            } catch (error) {
                console.error('티켓 로드 중 오류:', error);
                this.ticket = {};
            } finally {
                this.loading = false;
            }
        },

        // 즐겨찾기 상태 확인
        checkBookmarkStatus() {
            if (!this.ticket.id) return;
            
            try {
                const saved = localStorage.getItem('sandbox_bookmarks_gupsa_pms');
                if (!saved) {
                    this.isBookmarked = false;
                    return;
                }
                
                const bookmarks = JSON.parse(saved);
                const bookmarkTitle = `🎫 ${this.ticket.name}`;
                const ticketUrl = `/sandbox/gupsa/100-domain-pms/114-screen-ticket-detail?id=${this.ticket.id}`;
                
                this.isBookmarked = bookmarks.some(b => b.title === bookmarkTitle || b.url === ticketUrl);
            } catch (error) {
                console.error('즐겨찾기 상태 확인 오류:', error);
                this.isBookmarked = false;
            }
        },

        goBack() {
            // 테이블 뷰로 돌아가기
            const baseUrl = window.location.href.split('/114-screen-ticket-detail')[0];
            window.location.href = baseUrl + '/103-screen-table-view';
        },

        editTicket() {
            alert('편집 기능은 아직 구현되지 않았습니다.');
        },

        async deleteTicket() {
            if (!confirm('정말로 이 티켓을 삭제하시겠습니까?')) return;

            try {
                const response = await fetch(`/api/sandbox/gupsa/pms/projects/${this.ticket.id}`, {
                    method: 'DELETE'
                });

                const result = await response.json();

                if (result.success) {
                    alert('티켓이 삭제되었습니다.');
                    this.goBack();
                } else {
                    alert('티켓 삭제에 실패했습니다.');
                }
            } catch (error) {
                console.error('티켓 삭제 오류:', error);
                alert('티켓 삭제 중 오류가 발생했습니다.');
            }
        },

        // 즐겨찾기 토글 기능
        async toggleBookmark() {
            if (this.isBookmarked) {
                await this.removeFromBookmarks();
            } else {
                await this.addToBookmarks();
            }
        },

        // 즐겨찾기에서 제거
        async removeFromBookmarks() {
            try {
                const saved = localStorage.getItem('sandbox_bookmarks_gupsa_pms');
                if (!saved) return;

                let bookmarks = JSON.parse(saved);
                const bookmarkTitle = `🎫 ${this.ticket.name}`;
                const ticketUrl = `/sandbox/gupsa/100-domain-pms/114-screen-ticket-detail?id=${this.ticket.id}`;

                // 해당 북마크 제거
                bookmarks = bookmarks.filter(b => b.title !== bookmarkTitle && b.url !== ticketUrl);

                // localStorage에 저장
                localStorage.setItem('sandbox_bookmarks_gupsa_pms', JSON.stringify(bookmarks));

                // 상태 업데이트
                this.isBookmarked = false;

                // 성공 메시지 표시
                alert(`"${this.ticket.name}" 티켓이 즐겨찾기에서 제거되었습니다!`);

                // 사이드바 새로고침
                this.refreshSidebar();

            } catch (error) {
                console.error('티켓 즐겨찾기 제거 오류:', error);
                alert('티켓 즐겨찾기 제거 중 오류가 발생했습니다.');
            }
        },

        // 즐겨찾기 추가 기능
        async addToBookmarks() {
            try {
                // localStorage에서 기존 북마크 데이터 로드
                const saved = localStorage.getItem('sandbox_bookmarks_gupsa_pms');
                let bookmarks = [];
                let nextId = 1;

                if (saved) {
                    bookmarks = JSON.parse(saved);
                    nextId = Math.max(...bookmarks.map(b => b.id), 0) + 1;
                }

                // 티켓 북마크 정보
                const bookmarkTitle = `🎫 ${this.ticket.name}`;
                const ticketUrl = `/sandbox/gupsa/100-domain-pms/114-screen-ticket-detail?id=${this.ticket.id}`;

                // 이미 존재하는지 확인
                const existingBookmark = bookmarks.find(b => b.title === bookmarkTitle || b.url === ticketUrl);
                if (existingBookmark) {
                    alert(`"${this.ticket.name}" 티켓이 이미 즐겨찾기에 추가되어 있습니다.`);
                    return;
                }

                // "티켓" 폴더 찾기 또는 생성
                let ticketFolder = bookmarks.find(b => b.type === 'folder' && b.title === '티켓');
                if (!ticketFolder) {
                    // 티켓 폴더가 없으면 생성
                    ticketFolder = {
                        id: nextId++,
                        type: 'folder',
                        title: '티켓',
                        parent_id: null,
                        order: bookmarks.filter(b => !b.parent_id).length,
                        created_at: new Date().toISOString()
                    };
                    bookmarks.push(ticketFolder);
                }

                // 새 티켓 북마크 추가
                const newBookmark = {
                    id: nextId,
                    type: 'bookmark',
                    title: bookmarkTitle,
                    url: ticketUrl,
                    parent_id: ticketFolder.id,
                    order: bookmarks.filter(b => b.parent_id === ticketFolder.id).length,
                    created_at: new Date().toISOString(),
                    metadata: {
                        ticket_id: this.ticket.id,
                        status: this.ticket.status,
                        priority: this.ticket.priority,
                        progress: this.ticket.progress
                    }
                };

                bookmarks.push(newBookmark);

                // localStorage에 저장
                localStorage.setItem('sandbox_bookmarks_gupsa_pms', JSON.stringify(bookmarks));

                // 상태 업데이트
                this.isBookmarked = true;

                // 성공 메시지 표시
                alert(`"${this.ticket.name}" 티켓이 즐겨찾기에 추가되었습니다!`);

                // 사이드바 새로고침
                this.refreshSidebar();

            } catch (error) {
                console.error('티켓 즐겨찾기 추가 오류:', error);
                alert('티켓 즐겨찾기 추가 중 오류가 발생했습니다.');
            }
        },

        // 사이드바 새로고침 함수
        refreshSidebar() {
            try {
                // 부모 창의 사이드바 새로고침 함수 호출
                if (window.parent && window.parent.refreshSidebarBookmarks) {
                    window.parent.refreshSidebarBookmarks();
                }
            } catch (error) {
                console.error('사이드바 새로고침 오류:', error);
            }
        },

        getStatusClass(status) {
            const statusClasses = {
                'pending': 'bg-gray-100 text-gray-800',
                'in_progress': 'bg-yellow-100 text-yellow-800',
                'on_hold': 'bg-orange-100 text-orange-800',
                'completed': 'bg-green-100 text-green-800'
            };
            return statusClasses[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusText(status) {
            const statusTexts = {
                'pending': '대기',
                'in_progress': '진행 중',
                'on_hold': '보류',
                'completed': '완료'
            };
            return statusTexts[status] || status;
        },

        getPriorityClass(priority) {
            const priorityClasses = {
                'low': 'bg-green-100 text-green-800',
                'medium': 'bg-yellow-100 text-yellow-800',
                'high': 'bg-red-100 text-red-800'
            };
            return priorityClasses[priority] || 'bg-gray-100 text-gray-800';
        },

        getPriorityText(priority) {
            const priorityTexts = {
                'low': '낮음',
                'medium': '보통',
                'high': '높음'
            };
            return priorityTexts[priority] || priority;
        },

        formatDate(datetime) {
            if (!datetime) return '';
            const date = new Date(datetime);
            return date.toLocaleDateString('ko-KR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
}
</script>

<!-- Alpine.js provided by Livewire - CDN removed to prevent conflicts -->