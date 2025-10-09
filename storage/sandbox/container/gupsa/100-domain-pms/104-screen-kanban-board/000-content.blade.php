{{-- 샌드박스 칸반 보드 템플릿 --}}
<?php 
    require_once __DIR__ . "/../../../../../../bootstrap.php";
use App\Services\TemplateCommonService;
    

    $screenInfo = TemplateCommonService::getCurrentTemplateScreenInfo();
    $uploadPaths = TemplateCommonService::getTemplateUploadPaths();
?><div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 p-6" 
     x-data="kanbanData()" 
     x-init="loadKanbanBoards()"
     x-cloak>
    {{-- 헤더 --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span class="text-purple-600">📋</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">칸반 보드</h1>
                    <p class="text-gray-600">프로젝트 진행 상황을 시각적으로 관리하세요</p>
                </div>
            </div>
            <button @click="showAddCardModal('todo')" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">새 카드 추가</button>
        </div>
    </div>

    {{-- 칸반 보드 --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6" 
         :class="{'md:grid-cols-3': showSidebar}">
        <div x-show="loading" class="col-span-full text-center py-12">
            <div class="text-gray-500">칸반 보드를 로딩 중...</div>
        </div>
        
        <template x-for="board in boards" :key="board.column.id">
            <div class="bg-gray-100 rounded-lg p-4 min-h-96"
                 @dragover.prevent="$event.currentTarget.classList.add('bg-gray-200')"
                 @dragleave="$event.currentTarget.classList.remove('bg-gray-200')"
                 @drop="handleDrop($event, board.column.id); $event.currentTarget.classList.remove('bg-gray-200')">
                {{-- 칼럼 헤더 --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full" 
                             :class="getColumnColorClass(board.column.color)"></div>
                        <h3 class="font-semibold text-gray-900" x-text="board.column.title"></h3>
                    </div>
                    <span class="bg-gray-200 text-gray-600 text-sm px-2 py-1 rounded-full" 
                          x-text="board.count"></span>
                </div>

                {{-- 카드들 --}}
                <div class="space-y-3">
                    <template x-for="card in board.cards" :key="card.id">
                        <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer"
                             :class="{ 'opacity-50 scale-95': draggedCard && draggedCard.id === card.id }"
                             @click="selectCard(card)"
                             :draggable="true"
                             @dragstart="startDrag($event, card)"
                             @dragover.prevent
                             @drop="handleDrop($event, board.column.id)"
                             @dragend="draggedCard = null"
                             @mousedown="$event.target.style.cursor = 'grabbing'"
                             @mouseup="$event.target.style.cursor = 'pointer'">
                            <div class="flex items-start justify-between mb-3">
                                <h4 class="text-sm font-medium text-gray-900" x-text="card.title">
                                </h4>
                                <span class="text-xs px-2 py-1 rounded-full" 
                                      :class="getPriorityClass(card.priority)"
                                      x-text="getPriorityText(card.priority)">
                                </span>
                            </div>
                            
                            <p class="text-xs text-gray-600 mb-3" 
                               x-text="card.description || '설명이 없습니다.'">
                            </p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-1">
                                    <div class="w-5 h-5 bg-gray-300 rounded-full"></div>
                                    <span class="text-xs text-gray-500" x-text="card.assignee || '미배정'"></span>
                                </div>
                                <div class="text-xs text-gray-400" x-text="formatDate(card.updated_at)">
                                </div>
                            </div>
                            
                            <div class="mt-3 pt-3 border-t border-gray-100" x-show="card.progress > 0">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="text-gray-600">진행률</span>
                                    <span class="text-gray-500" x-text="card.progress + '%'"></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1">
                                    <div class="bg-blue-500 h-1 rounded-full" :style="`width: ${card.progress}%`"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    {{-- 새 카드 추가 버튼 --}}
                    <button @click="showAddCardModal(board.column.id)" 
                            class="w-full p-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-400 hover:border-gray-400 hover:text-gray-600 text-sm">
                        + 새 카드 추가
                    </button>
                </div>
            </div>
        </template>
    </div>

    {{-- 안내 메시지 --}}
    <div class="mt-8 bg-white rounded-lg p-4 border border-blue-200">
        <div class="flex items-center space-x-2 text-blue-700">
            <span>💡</span>
            <span class="text-sm">카드를 드래그하여 다른 칼럼으로 이동할 수 있습니다. 카드를 클릭하면 상세 정보를 볼 수 있습니다.</span>
        </div>
    </div>

    {{-- 카드 상세 사이드바 --}}
    <div x-show="showSidebar" 
         x-transition:enter="transition ease-in-out duration-300"
         x-transition:enter-start="transform translate-x-full"
         x-transition:enter-end="transform translate-x-0"
         x-transition:leave="transition ease-in-out duration-300"
         x-transition:leave-start="transform translate-x-0"
         x-transition:leave-end="transform translate-x-full"
         class="fixed inset-y-0 right-0 w-96 bg-white shadow-2xl z-50 flex flex-col">
        
        {{-- 사이드바 헤더 --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">카드 상세 정보</h2>
            <button @click="closeSidebar()" 
                    class="text-gray-400 hover:text-gray-600 p-1">
                <i class="fa fa-plus w-6 h-6"></i>
            </button>
        </div>

        {{-- 사이드바 내용 --}}
        <div class="flex-1 overflow-y-auto p-6">
            <template x-if="selectedCard">
                <div>
                    {{-- 카드 제목 --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">제목</label>
                        <input type="text" 
                               x-model="selectedCard.title"
                               @input="cardEdited = true"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    {{-- 상태 --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                        <select x-model="selectedCard.column_id" 
                                @change="cardEdited = true"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="todo">할 일</option>
                            <option value="in-progress">진행 중</option>
                            <option value="review">검토</option>
                            <option value="done">완료</option>
                        </select>
                    </div>

                    {{-- 우선순위 --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
                        <select x-model="selectedCard.priority" 
                                @change="cardEdited = true"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="low">낮음</option>
                            <option value="medium">보통</option>
                            <option value="high">높음</option>
                        </select>
                    </div>

                    {{-- 설명 --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">설명</label>
                        <textarea x-model="selectedCard.description"
                                  @input="cardEdited = true"
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                                  placeholder="카드에 대한 상세 설명을 입력하세요..."></textarea>
                    </div>

                    {{-- 진행률 --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            진행률 (<span x-text="selectedCard.progress || 0"></span>%)
                        </label>
                        <input type="range" 
                               x-model="selectedCard.progress"
                               @input="cardEdited = true"
                               min="0" 
                               max="100" 
                               step="5"
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>0%</span>
                            <span>50%</span>
                            <span>100%</span>
                        </div>
                    </div>

                    {{-- 팀 멤버 수 --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">팀 멤버 수</label>
                        <input type="number" 
                               x-model="selectedCard.team_members"
                               @input="cardEdited = true"
                               min="1" 
                               max="20"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    {{-- 생성/수정 시간 --}}
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm text-gray-600">
                            <div class="mb-2">
                                <span class="font-medium">생성일:</span> 
                                <span x-text="formatFullDate(selectedCard.created_at)"></span>
                            </div>
                            <div>
                                <span class="font-medium">수정일:</span> 
                                <span x-text="formatFullDate(selectedCard.updated_at)"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        {{-- 사이드바 푸터 --}}
        <div class="p-6 border-t border-gray-200 bg-gray-50">
            <div class="flex space-x-3">
                <button @click="saveCard()" 
                        :disabled="!cardEdited"
                        :class="cardEdited ? 'bg-purple-600 hover:bg-purple-700' : 'bg-gray-400 cursor-not-allowed'"
                        class="flex-1 px-4 py-2 text-white rounded-lg font-medium transition-colors">
                    <span x-show="!saving">저장</span>
                    <span x-show="saving">저장 중...</span>
                </button>
                <button @click="closeSidebar()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    취소
                </button>
            </div>
        </div>
    </div>

    {{-- 사이드바 배경 오버레이 --}}
    <div x-show="showSidebar" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeSidebar()"
         class="fixed inset-0 bg-black bg-opacity-50 z-40"></div>

    {{-- 새 카드 추가 모달 --}}
    <div x-show="showAddModal" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.self="closeAddCardModal()">
        
        <div x-show="showAddModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white rounded-lg shadow-xl w-full max-w-md">
            
            {{-- 모달 헤더 --}}
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">새 카드 추가</h3>
                <button @click="closeAddCardModal()" 
                        class="text-gray-400 hover:text-gray-600 p-1">
                    <i class="fa fa-plus w-6 h-6"></i>
                </button>
            </div>

            {{-- 모달 내용 --}}
            <div class="p-6">
                {{-- 제목 --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">제목</label>
                    <input type="text" 
                           x-model="newCard.title"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="카드 제목을 입력하세요...">
                </div>

                {{-- 설명 --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">설명</label>
                    <textarea x-model="newCard.description"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none"
                              placeholder="카드에 대한 설명을 입력하세요..."></textarea>
                </div>

                {{-- 우선순위 --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
                    <select x-model="newCard.priority" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="low">낮음</option>
                        <option value="medium" selected>보통</option>
                        <option value="high">높음</option>
                    </select>
                </div>

                {{-- 팀 멤버 수 --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">팀 멤버 수</label>
                    <input type="number" 
                           x-model="newCard.team_members"
                           min="1" 
                           max="20"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            </div>

            {{-- 모달 푸터 --}}
            <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                <button @click="closeAddCardModal()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    취소
                </button>
                <button @click="createNewCard()" 
                        :disabled="!newCard.title.trim() || creatingCard"
                        :class="newCard.title.trim() && !creatingCard ? 'bg-purple-600 hover:bg-purple-700' : 'bg-gray-400 cursor-not-allowed'"
                        class="px-4 py-2 text-white rounded-lg font-medium transition-colors">
                    <span x-show="!creatingCard">추가</span>
                    <span x-show="creatingCard">추가 중...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function kanbanData() {
    return {
        boards: [],
        loading: false,
        draggedCard: null,
        showSidebar: false,
        selectedCard: null,
        cardEdited: false,
        saving: false,
        originalCardData: null,
        showAddModal: false,
        newCard: {
            title: '',
            description: '',
            priority: 'medium',
            team_members: 1,
            column_id: 'todo'
        },
        creatingCard: false,
        
        async loadKanbanBoards() {
            this.loading = true;
            try {
                console.log('칸반 보드 API 호출:', '/api/sandbox/gupsa/pms/projects');
                const response = await fetch('/api/sandbox/gupsa/pms/projects');
                const result = await response.json();

                if (result.data && Array.isArray(result.data)) {
                    // 프로젝트 데이터를 칸반 보드 형태로 변환
                    this.boards = this.convertProjectsToKanbanBoards(result.data);
                    console.log('칸반 보드 데이터 로드 성공:', this.boards);
                } else {
                    console.error('칸반 보드 API 오류:', '데이터 없음');
                    this.boards = [];
                }
            } catch (error) {
                console.error('칸반 보드 로딩 실패:', error);
                this.boards = [];
            } finally {
                this.loading = false;
            }
        },

        convertProjectsToKanbanBoards(projects) {
            // 칸반 보드 칼럼 정의
            const columns = [
                { id: 'planned', title: '계획됨', color: 'blue' },
                { id: 'in_progress', title: '진행 중', color: 'yellow' },
                { id: 'on_hold', title: '보류', color: 'purple' },
                { id: 'completed', title: '완료됨', color: 'green' }
            ];

            // 프로젝트를 상태별로 그룹화하여 칸반 보드 생성
            return columns.map(column => {
                const cards = projects
                    .filter(project => project.status === column.id || (project.status === 'planned' && column.id === 'planned'))
                    .map(project => ({
                        id: project.id,
                        title: project.name,
                        description: project.description,
                        priority: project.priority || 'medium',
                        progress: project.progress || 0,
                        column_id: column.id,
                        assignee: project.client || '미배정',
                        team_members: Math.floor(Math.random() * 5) + 1, // 임시 값
                        created_at: new Date().toISOString(),
                        updated_at: new Date().toISOString()
                    }));

                return {
                    column: column,
                    cards: cards,
                    count: cards.length
                };
            });
        },

        
        startDrag(event, card) {
            this.draggedCard = card;
            event.dataTransfer.effectAllowed = 'move';
        },
        
        async handleDrop(event, columnId) {
            event.preventDefault();

            if (!this.draggedCard || !this.draggedCard.id) {
                console.error('Invalid dragged card:', this.draggedCard);
                this.draggedCard = null;
                return;
            }

            // 카드가 같은 컬럼으로 이동하는 경우 무시
            if (this.draggedCard.column_id === columnId) {
                this.draggedCard = null;
                return;
            }

            const originalCard = this.draggedCard;

            // 임시로 UI에서만 카드 이동 (API 구현 후 제거 예정)
            this.moveCardInUI(originalCard, columnId);
            console.log('카드 이동 (로컬 전용):', originalCard.title, '→', columnId);
            this.draggedCard = null;

            // TODO: 카드 업데이트 API 구현 후 활성화 예정
            /*
            try {
                // API 호출 구현 필요
            } catch (error) {
                console.error('카드 이동 오류:', error);
            }
            */
        },
        
        moveCardInUI(card, targetColumnId) {
            if (!card || !card.id) {
                console.error('Invalid card object:', card);
                return;
            }
            
            // 원래 컬럼에서 카드 제거
            this.boards.forEach(board => {
                if (!board.cards) return;
                const cardIndex = board.cards.findIndex(c => c && c.id === card.id);
                if (cardIndex !== -1) {
                    board.cards.splice(cardIndex, 1);
                    board.count = board.cards.length;
                }
            });
            
            // 타겟 컬럼에 카드 추가
            const targetBoard = this.boards.find(b => b && b.column && b.column.id === targetColumnId);
            if (targetBoard && targetBoard.cards) {
                card.column_id = targetColumnId;
                targetBoard.cards.push(card);
                targetBoard.count = targetBoard.cards.length;
            }
        },
        
        selectCard(card) {
            // 드래그 중에는 사이드바 열지 않음
            if (this.draggedCard) return;
            
            console.log('카드 선택:', card);
            this.selectedCard = JSON.parse(JSON.stringify(card)); // 깊은 복사
            this.originalCardData = JSON.parse(JSON.stringify(card)); // 원본 데이터 보관
            this.showSidebar = true;
            this.cardEdited = false;
        },

        closeSidebar() {
            if (this.cardEdited) {
                const confirmClose = confirm('변경사항이 있습니다. 저장하지 않고 닫으시겠습니까?');
                if (!confirmClose) return;
            }
            
            this.showSidebar = false;
            this.selectedCard = null;
            this.originalCardData = null;
            this.cardEdited = false;
        },

        async saveCard() {
            if (!this.selectedCard || !this.cardEdited) return;

            this.saving = true;

            try {
                // 임시로 로컬에서만 업데이트 (API 구현 후 제거 예정)
                this.updateCardInUI(this.selectedCard);

                // 상태가 변경된 경우 카드를 다른 컬럼으로 이동
                if (this.originalCardData.column_id !== this.selectedCard.column_id) {
                    this.moveCardInUI(this.selectedCard, this.selectedCard.column_id);
                }

                this.cardEdited = false;
                this.originalCardData = JSON.parse(JSON.stringify(this.selectedCard));

                console.log('카드 저장 완료 (로컬 전용):', this.selectedCard.title);

                // TODO: 카드 업데이트 API 구현 후 활성화 예정
                /*
                try {
                    // API 호출 구현 필요
                } catch (error) {
                    console.error('카드 저장 오류:', error);
                }
                */
            } finally {
                this.saving = false;
            }
        },

        updateCardInUI(updatedCard) {
            // 모든 보드에서 해당 카드를 찾아서 업데이트
            this.boards.forEach(board => {
                const cardIndex = board.cards.findIndex(c => c.id === updatedCard.id);
                if (cardIndex !== -1) {
                    board.cards[cardIndex] = Object.assign(board.cards[cardIndex], updatedCard);
                }
            });
        },

        getStatusFromColumnId(columnId) {
            const statusMap = {
                'todo': 'planned',
                'in-progress': 'in_progress',
                'review': 'on_hold',
                'done': 'completed'
            };
            return statusMap[columnId] || 'planned';
        },

        showTemporaryMessage(message, type = 'info') {
            // 임시 메시지 표시 (향후 토스트 알림으로 대체 가능)
            console.log(`${type.toUpperCase()}: ${message}`);
        },
        
        showAddCardModal(columnId) {
            console.log('새 카드 추가:', columnId);
            this.newCard = {
                title: '',
                description: '',
                priority: 'medium',
                team_members: 1,
                column_id: columnId
            };
            this.showAddModal = true;
        },

        closeAddCardModal() {
            this.showAddModal = false;
            this.newCard = {
                title: '',
                description: '',
                priority: 'medium',
                team_members: 1,
                column_id: 'todo'
            };
        },

        async createNewCard() {
            if (!this.newCard.title.trim()) return;

            this.creatingCard = true;

            try {
                // 임시로 로컬에서만 새 카드 생성 (실제 API 연결 시 수정 필요)
                const newCard = {
                    id: Date.now(), // 임시 ID
                    title: this.newCard.title.trim(),
                    description: this.newCard.description.trim(),
                    priority: this.newCard.priority,
                    progress: 0,
                    column_id: this.newCard.column_id,
                    assignee: '미배정',
                    team_members: parseInt(this.newCard.team_members) || 1,
                    created_at: new Date().toISOString(),
                    updated_at: new Date().toISOString()
                };

                // UI에 새 카드 추가
                const targetBoard = this.boards.find(b => b.column.id === this.newCard.column_id);
                if (targetBoard) {
                    targetBoard.cards.push(newCard);
                    targetBoard.count = targetBoard.cards.length;
                }

                this.closeAddCardModal();
                console.log('새 카드 생성 성공 (로컬 전용):', newCard.title);

                // TODO: 실제 API 연결 시 활성화 예정
                /*
                const pathParts = window.location.pathname.split('/');
                const sandboxIndex = pathParts.indexOf('sandbox');
                const sandboxTemplate = pathParts[sandboxIndex + 1];

                const response = await fetch(`/api/sandbox/${sandboxTemplate}/projects`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name: this.newCard.title.trim(),
                        description: this.newCard.description.trim(),
                        priority: this.newCard.priority,
                        status: this.getStatusFromColumnId(this.newCard.column_id)
                    })
                });
                */
            } catch (error) {
                console.error('카드 생성 오류:', error);
                alert('카드 생성 중 오류가 발생했습니다.');
            } finally {
                this.creatingCard = false;
            }
        },
        
        getColumnColorClass(color) {
            const colorClasses = {
                'blue': 'bg-blue-400',
                'yellow': 'bg-yellow-400',
                'purple': 'bg-purple-400',
                'green': 'bg-green-400'
            };
            return colorClasses[color] || 'bg-gray-400';
        },
        
        getPriorityClass(priority) {
            const priorityClasses = {
                'high': 'bg-red-100 text-red-600',
                'medium': 'bg-yellow-100 text-yellow-600',
                'low': 'bg-green-100 text-green-600',
                'normal': 'bg-gray-100 text-gray-600'
            };
            return priorityClasses[priority] || 'bg-gray-100 text-gray-600';
        },
        
        getPriorityText(priority) {
            const priorityTexts = {
                'high': '높음',
                'medium': '보통',
                'low': '낮음',
                'normal': '일반'
            };
            return priorityTexts[priority] || priority || '일반';
        },
        
        formatDate(datetime) {
            if (!datetime) return '';
            const date = new Date(datetime);
            return date.toLocaleDateString('ko-KR', { month: 'numeric', day: 'numeric' });
        },

        formatFullDate(datetime) {
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

