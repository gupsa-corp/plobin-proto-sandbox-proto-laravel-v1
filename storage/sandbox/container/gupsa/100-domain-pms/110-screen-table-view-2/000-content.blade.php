{{-- 샌드박스 테이블 뷰 템플릿 --}}

<!-- 인라인 편집 스타일 -->
<style>
.editable-field {
    transition: background-color 0.2s ease;
    padding: 2px 4px;
    border-radius: 4px;
    min-height: 1.2em;
    position: relative;
}

.editable-field:hover {
    background-color: #f3f4f6;
    outline: 1px solid #d1d5db;
}

.editable-field.editing {
    background-color: #ffffff;
    outline: none;
}

.editable-select {
    transition: opacity 0.2s ease;
}

.editable-checkbox {
    transition: transform 0.1s ease;
}

.editable-checkbox:hover {
    transform: scale(1.1);
}

/* 편집 모드 시각적 표시 */
.editing::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    animation: editing-pulse 1.5s infinite;
}

@keyframes editing-pulse {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 0.4; }
}

/* 호버 시 편집 가능 표시 */
.editable-field::after {
    content: '✎';
    position: absolute;
    right: -16px;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0;
    font-size: 12px;
    color: #6b7280;
    transition: opacity 0.2s ease;
}

.editable-field:hover::after {
    opacity: 0.6;
}

.editable-field.editing::after {
    display: none;
}
</style>
<?php
    require_once __DIR__ . "/../../../../../../bootstrap.php";
    use App\Services\TemplateCommonService;

    $screenInfo = TemplateCommonService::getCurrentTemplateScreenInfo();
    $uploadPaths = TemplateCommonService::getTemplateUploadPaths();
?>
<div class="min-h-screen bg-gray-50 p-6"
     x-data="tableViewData2()"
     x-init="loadProjects()"
     x-cloak>

    {{-- 헤더 및 통계 --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span class="text-purple-600">🗂️</span>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">프로젝트 테이블 뷰 2</h1>
                    <p class="text-gray-600">실제 데이터베이스 연동으로 프로젝트를 체계적으로 관리하세요</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button @click="showColumnModal = true" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center space-x-2">
                    <span>⚙️</span>
                    <span>컬럼 관리</span>
                </button>
                <button @click="showCreateModal = true" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center space-x-2">
                    <span>+</span>
                    <span>새 프로젝트 생성</span>
                </button>
            </div>
        </div>

        {{-- 통계 카드 --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-sm text-blue-600">전체 프로젝트</div>
                <div class="text-2xl font-bold text-blue-800" x-text="stats.total"></div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-sm text-green-600">진행 중</div>
                <div class="text-2xl font-bold text-green-800" x-text="stats.in_progress"></div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-sm text-purple-600">완료</div>
                <div class="text-2xl font-bold text-purple-800" x-text="stats.completed"></div>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg">
                <div class="text-sm text-orange-600">평균 진행률</div>
                <div class="text-2xl font-bold text-orange-800" x-text="stats.avg_progress + '%'"></div>
            </div>
        </div>
    </div>

    {{-- 필터 바 --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-64">
                <input type="text"
                       x-model="filters.search"
                       @input="debounceSearch()"
                       placeholder="프로젝트명, 클라이언트 검색..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>
            <select x-model="filters.status"
                    @change="applyFilters()"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="">모든 상태</option>
                <option value="planned">계획</option>
                <option value="in_progress">진행 중</option>
                <option value="completed">완료</option>
                <option value="on_hold">보류</option>
            </select>
            <select x-model="filters.priority"
                    @change="applyFilters()"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option value="">모든 우선순위</option>
                <option value="high">높음</option>
                <option value="medium">보통</option>
                <option value="low">낮음</option>
            </select>
            <button @click="applyFilters()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">검색</button>
            <button x-show="hasActiveFilters()"
                    @click="clearFilters()"
                    class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">초기화</button>
        </div>
    </div>

    {{-- 메인 테이블 --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">프로젝트명</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">상태</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">우선순위</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">진행률</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">생성일</th>
                        <template x-for="column in dynamicColumns" :key="'header-' + column.id">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" x-text="column.column_label">
                            </th>
                        </template>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">액션</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr x-show="loading">
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">로딩 중...</td>
                    </tr>
                    <tr x-show="!loading && projects.length === 0">
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">프로젝트가 없습니다.</td>
                    </tr>
                    <template x-for="project in projects" :key="project.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 editable-field"
                                     @click="startEdit($event, project, 'name')"
                                     x-text="project.name"></div>
                                <div class="text-sm text-gray-500 editable-field"
                                     @click="startEdit($event, project, 'description')"
                                     x-text="project.description"></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select class="editable-select inline-flex px-2 py-1 text-xs font-semibold rounded-full border-none bg-transparent"
                                        :class="getStatusClass(project.status)"
                                        @change="updateField(project, 'status', $event.target.value)"
                                        x-model="project.status">
                                    <option value="planned">계획</option>
                                    <option value="in_progress">진행 중</option>
                                    <option value="completed">완료</option>
                                    <option value="on_hold">보류</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select class="editable-select inline-flex px-2 py-1 text-xs font-semibold rounded-full border-none bg-transparent"
                                        :class="getPriorityClass(project.priority)"
                                        @change="updateField(project, 'priority', $event.target.value)"
                                        x-model="project.priority">
                                    <option value="low">낮음</option>
                                    <option value="medium">보통</option>
                                    <option value="high">높음</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full" :style="`width: ${project.progress || 0}%`"></div>
                                    </div>
                                    <input type="number"
                                           class="w-12 text-xs border-none bg-transparent"
                                           min="0" max="100"
                                           @change="updateField(project, 'progress', parseInt($event.target.value))"
                                           x-model="project.progress">
                                    <span>%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="formatDate(project.created_at)"></td>
                            <template x-for="column in dynamicColumns" :key="'cell-' + project.id + '-' + column.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div x-show="column.column_type === 'text'"
                                         class="editable-field"
                                         @click="startEdit($event, project, 'custom_' + column.column_name)"
                                         x-text="project['custom_' + column.column_name] || ''"></div>
                                    <select x-show="column.column_type === 'select'"
                                            class="editable-select w-full border-none bg-transparent"
                                            :value="project['custom_' + column.column_name] || ''"
                                            @change="updateField(project, 'custom_' + column.column_name, $event.target.value)">
                                        <option value="">선택하세요</option>
                                        <template x-for="option in (column.options || '').split(',')" :key="option">
                                            <option :value="option.trim()" x-text="option.trim()"></option>
                                        </template>
                                    </select>
                                    <input x-show="column.column_type === 'checkbox'"
                                           type="checkbox"
                                           class="editable-checkbox"
                                           @change="updateField(project, 'custom_' + column.column_name, $event.target.checked ? 1 : 0)"
                                           :checked="project['custom_' + column.column_name] == 1">
                                </td>
                            </template>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button @click="openSidebar(project)" class="text-indigo-600 hover:text-indigo-900 mr-3">편집</button>
                                <button @click="deleteProject(project.id)" class="text-red-600 hover:text-red-900">삭제</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- 페이지네이션 --}}
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
            <div class="flex-1 flex justify-between sm:hidden">
                <button @click="prevPage()" :disabled="!pagination.hasPrev" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">이전</button>
                <button @click="nextPage()" :disabled="!pagination.hasNext" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">다음</button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        총 <span class="font-medium" x-text="pagination.total"></span>개 중
                        <span class="font-medium" x-text="pagination.offset + 1"></span>-<span class="font-medium" x-text="Math.min(pagination.offset + pagination.limit, pagination.total)"></span>개 표시
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                        <button @click="prevPage()" :disabled="!pagination.hasPrev" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">이전</button>
                        <button @click="nextPage()" :disabled="!pagination.hasNext" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">다음</button>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    {{-- 우측 사이드바 --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-40"
         @click="closeSidebar()"
         style="display: none;"></div>

    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 z-50 w-96 bg-white shadow-xl"
         style="display: none;">
        <div class="h-full flex flex-col" x-show="selectedProject">
            <!-- 사이드바 헤더 -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">프로젝트 편집</h3>
                    <button @click="closeSidebar()"
                            class="text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- 사이드바 콘텐츠 -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">프로젝트명</label>
                        <input type="text" :value="selectedProject ? selectedProject.name : ''"
                               @input="if(selectedProject) selectedProject.name = $event.target.value"
                               @blur="updateSelectedProject('name')"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">설명</label>
                        <textarea :value="selectedProject ? selectedProject.description : ''"
                                  @input="if(selectedProject) selectedProject.description = $event.target.value"
                                  @blur="updateSelectedProject('description')"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                        <select :value="selectedProject ? selectedProject.status : ''"
                                @change="if(selectedProject) { selectedProject.status = $event.target.value; updateSelectedProject('status'); }"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="planned">계획</option>
                            <option value="in_progress">진행 중</option>
                            <option value="completed">완료</option>
                            <option value="on_hold">보류</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
                        <select :value="selectedProject ? selectedProject.priority : ''"
                                @change="if(selectedProject) { selectedProject.priority = $event.target.value; updateSelectedProject('priority'); }"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="low">낮음</option>
                            <option value="medium">보통</option>
                            <option value="high">높음</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">진행률 (%)</label>
                        <input type="number" :value="selectedProject ? selectedProject.progress : 0"
                               @input="if(selectedProject) selectedProject.progress = parseInt($event.target.value)"
                               @blur="updateSelectedProject('progress')"
                               min="0" max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- 동적 컬럼들 -->
                    <template x-for="column in dynamicColumns" :key="'sidebar-' + column.id">
                        <div x-show="selectedProject">
                            <label class="block text-sm font-medium text-gray-700 mb-2" x-text="column.column_label"></label>
                            <input x-show="column.column_type === 'text'"
                                   type="text"
                                   :value="selectedProject ? selectedProject['custom_' + column.column_name] : ''"
                                   @input="if(selectedProject) updateCustomColumn('custom_' + column.column_name, $event.target.value)"
                                   @blur="updateSelectedProject('custom_' + column.column_name)"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <select x-show="column.column_type === 'select'"
                                    :value="selectedProject ? selectedProject['custom_' + column.column_name] : ''"
                                    @change="if(selectedProject) { updateCustomColumn('custom_' + column.column_name, $event.target.value); updateSelectedProject('custom_' + column.column_name); }"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">선택하세요</option>
                                <template x-for="option in (column.options || '').split(',')" :key="option">
                                    <option :value="option.trim()" x-text="option.trim()"></option>
                                </template>
                            </select>
                            <label x-show="column.column_type === 'checkbox'" class="flex items-center">
                                <input type="checkbox"
                                       :checked="selectedProject ? selectedProject['custom_' + column.column_name] == 1 : false"
                                       @change="if(selectedProject) { updateCustomColumn('custom_' + column.column_name, $event.target.checked ? 1 : 0); updateSelectedProject('custom_' + column.column_name); }"
                                       class="mr-2">
                                <span class="text-sm text-gray-600">체크</span>
                            </label>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 사이드바 푸터 -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex space-x-3">
                    <button @click="updateProject()"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        저장
                    </button>
                    <button @click="closeSidebar()"
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:ring-2 focus:ring-gray-500">
                        취소
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 컬럼 관리 모달 --}}
    <div x-show="showColumnModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="showColumnModal = false">
        <div class="relative top-20 mx-auto p-5 border w-3/4 max-w-4xl shadow-lg rounded-md bg-white" @click.stop>
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">컬럼 관리</h3>

                <!-- 기존 컬럼 목록 -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-700 mb-3">기존 컬럼</h4>
                    <div class="space-y-2">
                        <template x-for="column in dynamicColumns" :key="column.id">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="font-medium" x-text="column.column_label"></div>
                                    <div class="text-sm text-gray-500">
                                        타입: <span x-text="column.column_type"></span>
                                        <span x-show="column.options" x-text="' | 옵션: ' + column.options"></span>
                                    </div>
                                </div>
                                <button @click="deleteColumn(column.id)" class="text-red-600 hover:text-red-800 px-3 py-1 text-sm">
                                    삭제
                                </button>
                            </div>
                        </template>
                        <div x-show="dynamicColumns.length === 0" class="text-gray-500 text-center py-4">
                            설정된 컬럼이 없습니다.
                        </div>
                    </div>
                </div>

                <!-- 새 컬럼 추가 -->
                <div class="border-t pt-6">
                    <h4 class="text-md font-medium text-gray-700 mb-3">새 컬럼 추가</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">컬럼명</label>
                            <input type="text" x-model="newColumn.column_name"
                                   placeholder="예: 담당자"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">표시명</label>
                            <input type="text" x-model="newColumn.column_label"
                                   placeholder="예: 담당자"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">타입</label>
                            <select x-model="newColumn.column_type"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="text">텍스트</option>
                                <option value="select">드롭다운</option>
                                <option value="checkbox">체크박스</option>
                            </select>
                        </div>
                        <div x-show="newColumn.column_type === 'select'">
                            <label class="block text-sm font-medium text-gray-700 mb-2">옵션 (쉼표로 구분)</label>
                            <input type="text" x-model="newColumn.options"
                                   placeholder="예: 옵션1, 옵션2, 옵션3"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                    <div class="flex items-center mt-4">
                        <input type="checkbox" x-model="newColumn.is_required" class="mr-2">
                        <label class="text-sm text-gray-700">필수 컬럼</label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button @click="addColumn()"
                            :disabled="!newColumn.column_name || !newColumn.column_label"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50">
                        컬럼 추가
                    </button>
                    <button @click="showColumnModal = false" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">
                        닫기
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 생성/편집 모달 --}}
    <div x-show="showCreateModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeModal()">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="showEditModal ? '프로젝트 편집' : '새 프로젝트 생성'"></h3>
                <form @submit.prevent="saveProject()">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">프로젝트명</label>
                        <input type="text" x-model="formData.name" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">설명</label>
                        <textarea x-model="formData.description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                        <select x-model="formData.status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="planned">계획</option>
                            <option value="in_progress">진행 중</option>
                            <option value="completed">완료</option>
                            <option value="on_hold">보류</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
                        <select x-model="formData.priority" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="low">낮음</option>
                            <option value="medium">보통</option>
                            <option value="high">높음</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">진행률 (%)</label>
                        <input type="number" x-model="formData.progress" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="closeModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">취소</button>
                        <button type="submit" :disabled="saving" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 disabled:opacity-50">
                            <span x-show="!saving" x-text="showEditModal ? '수정' : '생성'"></span>
                            <span x-show="saving">저장 중...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function tableViewData2() {
    return {
        projects: [],
        loading: false,
        filters: {
            search: '',
            status: '',
            priority: ''
        },
        pagination: {
            total: 0,
            offset: 0,
            limit: 10,
            hasNext: false,
            hasPrev: false
        },
        stats: {
            total: 0,
            in_progress: 0,
            completed: 0,
            avg_progress: 0
        },
        showCreateModal: false,
        showEditModal: false,
        sidebarOpen: false,
        selectedProject: null,
        showColumnModal: false,
        dynamicColumns: [],
        newColumn: {
            column_name: '',
            column_label: '',
            column_type: 'text',
            options: '',
            is_required: false
        },
        formData: {
            id: null,
            name: '',
            description: '',
            status: 'planned',
            priority: 'medium',
            progress: 0
        },
        saving: false,
        searchTimeout: null,

        async loadProjects() {
            await this.loadColumns();
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    limit: this.pagination.limit,
                    offset: this.pagination.offset,
                    search: this.filters.search,
                    status: this.filters.status,
                    priority: this.filters.priority
                });

                const response = await fetch(`/api/sandbox/gupsa/pms/projects?${params}`);
                const result = await response.json();

                if (result.data && Array.isArray(result.data)) {
                    this.projects = result.data;
                    this.pagination = {
                        total: result.total || 0,
                        offset: ((result.current_page || 1) - 1) * (result.per_page || this.pagination.limit),
                        limit: result.per_page || this.pagination.limit,
                        hasNext: result.current_page < result.last_page,
                        hasPrev: result.current_page > 1
                    };
                    this.calculateStats();
                } else {
                    console.error('Projects API 오류:', result.message || result);
                    this.projects = [];
                }
            } catch (error) {
                console.error('프로젝트 로딩 실패:', error);
                this.projects = [];
            } finally {
                this.loading = false;
            }
        },

        calculateStats() {
            this.stats = {
                total: this.pagination.total,
                in_progress: this.projects.filter(p => p.status === 'in_progress').length,
                completed: this.projects.filter(p => p.status === 'completed').length,
                avg_progress: this.projects.length > 0 ? Math.round(this.projects.reduce((sum, p) => sum + (p.progress || 0), 0) / this.projects.length) : 0
            };
        },

        applyFilters() {
            this.pagination.offset = 0;
            this.loadProjects();
        },

        debounceSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.applyFilters();
            }, 500);
        },

        clearFilters() {
            this.filters = {
                search: '',
                status: '',
                priority: ''
            };
            this.applyFilters();
        },

        hasActiveFilters() {
            return this.filters.search || this.filters.status || this.filters.priority;
        },

        nextPage() {
            if (this.pagination.hasNext) {
                this.pagination.offset += this.pagination.limit;
                this.loadProjects();
            }
        },

        prevPage() {
            if (this.pagination.hasPrev) {
                this.pagination.offset = Math.max(0, this.pagination.offset - this.pagination.limit);
                this.loadProjects();
            }
        },

        async loadColumns() {
            try {
                const response = await fetch('/api/sandbox/gupsa/pms/columns');
                const result = await response.json();
                if (response.ok) {
                    this.dynamicColumns = result.data || [];
                } else {
                    console.error('컬럼 로딩 실패:', result.message);
                }
            } catch (error) {
                console.error('컬럼 로딩 오류:', error);
            }
        },

        async addColumn() {
            if (!this.newColumn.column_name || !this.newColumn.column_label) return;

            try {
                const response = await fetch('/api/sandbox/gupsa/pms/columns', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.newColumn)
                });

                const result = await response.json();
                if (response.ok) {
                    await this.loadColumns();
                    this.newColumn = {
                        column_name: '',
                        column_label: '',
                        column_type: 'text',
                        options: '',
                        is_required: false
                    };
                } else {
                    alert('컬럼 추가에 실패했습니다: ' + result.message);
                }
            } catch (error) {
                console.error('컬럼 추가 오류:', error);
                alert('컬럼 추가 중 오류가 발생했습니다.');
            }
        },

        async deleteColumn(columnId) {
            if (!confirm('정말로 이 컬럼을 삭제하시겠습니까?')) return;

            try {
                const response = await fetch(`/api/sandbox/gupsa/pms/columns/${columnId}`, {
                    method: 'DELETE'
                });

                if (response.ok) {
                    await this.loadColumns();
                    await this.loadProjects();
                } else {
                    alert('컬럼 삭제에 실패했습니다.');
                }
            } catch (error) {
                console.error('컬럼 삭제 오류:', error);
                alert('컬럼 삭제 중 오류가 발생했습니다.');
            }
        },

        openSidebar(project) {
            this.selectedProject = { ...project };
            this.sidebarOpen = true;
        },

        closeSidebar() {
            this.sidebarOpen = false;
            this.selectedProject = null;
        },

        async updateSelectedProject(field) {
            if (!this.selectedProject) return;

            try {
                const response = await fetch(`/api/sandbox/gupsa/pms/projects/${this.selectedProject.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        [field]: this.selectedProject[field]
                    })
                });

                if (response.ok) {
                    // 메인 테이블의 프로젝트도 업데이트
                    const projectIndex = this.projects.findIndex(p => p.id === this.selectedProject.id);
                    if (projectIndex !== -1) {
                        this.projects[projectIndex][field] = this.selectedProject[field];
                    }
                } else {
                    alert('업데이트에 실패했습니다.');
                }
            } catch (error) {
                console.error('업데이트 오류:', error);
            }
        },

        async updateProject() {
            if (!this.selectedProject) return;

            try {
                const response = await fetch(`/api/sandbox/gupsa/pms/projects/${this.selectedProject.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(this.selectedProject)
                });

                if (response.ok) {
                    await this.loadProjects();
                    this.closeSidebar();
                } else {
                    alert('프로젝트 업데이트에 실패했습니다.');
                }
            } catch (error) {
                console.error('프로젝트 업데이트 오류:', error);
                alert('프로젝트 업데이트 중 오류가 발생했습니다.');
            }
        },

        startEdit(event, project, field) {
            const element = event.target;
            const currentValue = project[field] || '';

            element.classList.add('editing');
            element.contentEditable = true;
            element.focus();

            // 텍스트 선택
            const range = document.createRange();
            range.selectNodeContents(element);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);

            const saveEdit = async () => {
                const newValue = element.textContent.trim();
                element.classList.remove('editing');
                element.contentEditable = false;

                if (newValue !== currentValue) {
                    await this.updateField(project, field, newValue);
                }
            };

            const cancelEdit = () => {
                element.textContent = currentValue;
                element.classList.remove('editing');
                element.contentEditable = false;
            };

            element.addEventListener('blur', saveEdit, { once: true });
            element.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    saveEdit();
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    cancelEdit();
                }
            }, { once: true });
        },

        async updateField(project, field, value) {
            try {
                const response = await fetch(`/api/sandbox/gupsa/pms/projects/${project.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        [field]: value
                    })
                });

                if (response.ok) {
                    project[field] = value;

                    // 사이드바가 열려있고 같은 프로젝트라면 업데이트
                    if (this.selectedProject && this.selectedProject.id === project.id) {
                        this.selectedProject[field] = value;
                    }
                } else {
                    alert('업데이트에 실패했습니다.');
                }
            } catch (error) {
                console.error('필드 업데이트 오류:', error);
                alert('업데이트 중 오류가 발생했습니다.');
            }
        },

        updateCustomColumn(columnName, value) {
            if (this.selectedProject) {
                this.selectedProject[columnName] = value;
            }
        },

        editProject(project) {
            this.formData = {
                id: project.id,
                name: project.name,
                description: project.description || '',
                status: project.status,
                priority: project.priority,
                progress: project.progress || 0
            };
            this.showEditModal = true;
        },

        async saveProject() {
            if (!this.formData.name.trim()) return;

            this.saving = true;
            try {
                const isEdit = this.showEditModal;
                const url = isEdit
                    ? `/api/sandbox/gupsa/pms/projects/${this.formData.id}`
                    : `/api/sandbox/gupsa/pms/projects`;

                const method = isEdit ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: this.formData.name,
                        description: this.formData.description,
                        status: this.formData.status,
                        priority: this.formData.priority,
                        progress: parseInt(this.formData.progress) || 0
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    this.closeModal();
                    this.loadProjects();
                } else {
                    alert(isEdit ? '프로젝트 수정에 실패했습니다.' : '프로젝트 생성에 실패했습니다.');
                }
            } catch (error) {
                console.error('프로젝트 저장 오류:', error);
                alert('프로젝트 저장 중 오류가 발생했습니다.');
            } finally {
                this.saving = false;
            }
        },

        async deleteProject(id) {
            if (!confirm('정말로 이 프로젝트를 삭제하시겠습니까?')) return;

            try {
                const response = await fetch(`/api/sandbox/gupsa/pms/projects/${id}`, {
                    method: 'DELETE'
                });

                if (response.ok) {
                    this.loadProjects();
                } else {
                    alert('프로젝트 삭제에 실패했습니다.');
                }
            } catch (error) {
                console.error('프로젝트 삭제 오류:', error);
                alert('프로젝트 삭제 중 오류가 발생했습니다.');
            }
        },

        closeModal() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.formData = {
                id: null,
                name: '',
                description: '',
                status: 'planned',
                priority: 'medium',
                progress: 0
            };
        },

        getStatusClass(status) {
            const statusClasses = {
                'planned': 'bg-gray-100 text-gray-800',
                'in_progress': 'bg-yellow-100 text-yellow-800',
                'on_hold': 'bg-orange-100 text-orange-800',
                'completed': 'bg-green-100 text-green-800'
            };
            return statusClasses[status] || 'bg-gray-100 text-gray-800';
        },

        getStatusText(status) {
            const statusTexts = {
                'planned': '계획',
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
                month: 'short',
                day: 'numeric'
            });
        }
    }
}
</script>
