<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI 문서 분석 결과</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- RFX 탭 네비게이션 -->
    @include('100-rfx-tab-navigation')

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-100 p-6" x-data="documentAnalysisData(1)" x-init="init()">

        <!-- 헤더 -->
        <div class="mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center">
                            <span class="text-white text-xl">🧠</span>
                        </div>
                        <div>
                            <div class="flex items-center space-x-4">
                                <h1 class="text-2xl font-bold text-gray-900">AI 문서 분석 결과</h1>
                                <div class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-full" x-text="documentVersion">v1.0</div>
                            </div>
                            <p class="text-gray-600">팔란티어 온톨로지 기반 에셋 분류 및 분석</p>
                            <div class="flex items-center space-x-3 mt-2">
                                <p x-show="documentData.file" class="text-sm text-indigo-600" x-text="documentData.file?.original_name">Document 1.pdf</p>
                                <div class="flex items-center space-x-2">
                                    <label for="file-selector" class="text-xs text-gray-500">파일 선택:</label>
                                    <select id="file-selector" @change="changeFile($event.target.value)" :value="fileId" class="text-xs bg-white border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        <template x-for="(name, id) in fileNames" :key="id">
                                            <option :value="id" x-text="`${id}. ${name}`" :selected="id == fileId"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-right space-y-2">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center space-x-2">
                                <label for="json-version-selector" class="text-xs text-gray-500">JSON 버전:</label>
                                <select id="json-version-selector" @change="loadJsonVersion($event.target.value)" :value="currentJsonVersion" class="text-xs bg-white border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-indigo-500">
                                    <template x-for="version in availableJsonVersions" :key="version.id">
                                        <option :value="version.id" x-text="version.name"></option>
                                    </template>
                                </select>
                            </div>
                            <button @click="showJsonManager = true" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">
                                📁 JSON 관리
                            </button>
                            <button @click="saveCurrentJson()" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors">
                                💾 저장
                            </button>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">섹션 표시</div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500">1-30</span>
                                <span class="text-sm font-medium text-indigo-600" x-text="`${displayedSections || 30}개`">30개</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- JSON 관리 모달 -->
        <div x-show="showJsonManager" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="showJsonManager = false" style="display: none;">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden" @click.stop>
                <!-- 모달 헤더 -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <span class="text-2xl">📁</span>
                            <h2 class="text-xl font-bold">JSON 데이터 관리</h2>
                        </div>
                        <button @click="showJsonManager = false" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fa fa-times w-6 h-6"></i>
                        </button>
                    </div>
                </div>

                <!-- 모달 컨텐츠 -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <!-- 현재 데이터 저장 -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <span class="text-green-500 mr-2">💾</span>
                            현재 데이터 저장
                        </h3>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="flex items-center space-x-4 mb-3">
                                <input type="text" x-model="saveFileName" placeholder="파일명을 입력하세요 (예: 프로젝트_분석_v1)" class="flex-1 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <button @click="saveToLocalStorage()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors flex items-center space-x-2">
                                    <span>💾</span>
                                    <span>로컬 저장</span>
                                </button>
                                <button @click="downloadCurrentJson()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors flex items-center space-x-2">
                                    <span>⬇️</span>
                                    <span>다운로드</span>
                                </button>
                            </div>
                            <p class="text-sm text-gray-600">
                                JSON 버전: <span class="font-medium text-green-700" x-text="currentJsonVersion">v1.0</span> |
                                문서 버전: <span class="font-medium text-green-700" x-text="documentVersion">v1.0</span> |
                                파일: <span class="font-medium text-green-700" x-text="fileNames[fileId]">Document 1.pdf</span> |
                                섹션 수: <span class="font-medium text-green-700" x-text="documentData.assets?.length || 0">3</span>개
                            </p>
                        </div>
                    </div>

                    <!-- 저장된 데이터 불러오기 -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <span class="text-blue-500 mr-2">📂</span>
                            저장된 데이터 불러오기
                        </h3>

                        <!-- 로컬 저장소 -->
                        <div class="mb-6">
                            <h4 class="text-md font-medium text-gray-800 mb-3">로컬 저장소</h4>
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                <div x-show="savedJsonFiles.length === 0" class="text-center text-gray-500 py-4">
                                    저장된 파일이 없습니다
                                </div>
                                <div x-show="savedJsonFiles.length > 0" class="space-y-2" style="display: none;">
                                    <template x-for="(file, index) in savedJsonFiles" :key="file.id">
                                        <div class="flex items-center justify-between bg-white p-3 rounded border hover:bg-gray-50">
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-900" x-text="file.fileName"></div>
                                                <div class="text-sm text-gray-500">
                                                    <span x-text="file.version"></span> |
                                                    <span x-text="file.documentVersion || 'v1.0'"></span> |
                                                    <span x-text="file.originalFileName"></span> |
                                                    <span x-text="file.sectionsCount"></span>개 섹션 |
                                                    <span x-text="new Date(file.createdAt).toLocaleString('ko-KR')"></span>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <button @click="loadFromLocalStorage(file.id)" class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">
                                                    불러오기
                                                </button>
                                                <button @click="deleteFromLocalStorage(file.id)" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors">
                                                    삭제
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- 파일에서 불러오기 -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 mb-3">파일에서 불러오기</h4>
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <input type="file" accept=".json" @change="handleFileUpload($event)" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-yellow-600 file:text-white hover:file:bg-yellow-700">
                                <p class="text-sm text-gray-600 mt-2">JSON 파일을 선택하여 데이터를 불러올 수 있습니다</p>
                            </div>
                        </div>
                    </div>

                    <!-- 저장소 통계 -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <span class="text-purple-500 mr-2">📊</span>
                            저장소 통계
                        </h3>
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <div class="text-2xl font-bold text-purple-600" x-text="savedJsonFiles.length">0</div>
                                    <div class="text-sm text-gray-600">저장된 파일</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-purple-600" x-text="getTotalStorageSize()">0</div>
                                    <div class="text-sm text-gray-600">사용 용량 (KB)</div>
                                </div>
                                <div>
                                    <div class="text-2xl font-bold text-purple-600" x-text="getUniqueVersionsCount()">0</div>
                                    <div class="text-sm text-gray-600">버전 종류</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 모달 푸터 -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                    <button @click="clearAllLocalStorage()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors">
                        🗑️ 전체 삭제
                    </button>
                    <button @click="showJsonManager = false" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                        닫기
                    </button>
                </div>
            </div>
        </div>

        <!-- 로딩 상태 -->
        <div x-show="isLoading" class="text-center py-12" style="display: none;">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500 mx-auto mb-4"></div>
            <p class="text-gray-600">분석 결과를 불러오는 중...</p>
        </div>

        <!-- 빈 상태 -->
        <div x-show="!isLoading && (!documentData.assets || documentData.assets.length === 0)" class="text-center py-12" style="display: none;">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-gray-400 text-2xl">📄</span>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">분석된 에셋이 없습니다</h3>
            <p class="text-gray-500 mb-4">문서가 아직 분석되지 않았거나 분석에 실패했을 수 있습니다.</p>
            <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fa fa-arrow-left w-4 h-4 mr-2"></i>
                파일 목록으로 돌아가기
            </a>
        </div>

        <!-- 에셋 목록 -->
        <div x-show="!isLoading && documentData.assets && documentData.assets.length > 0" class="space-y-6">
            <template x-for="(asset, index) in documentData.assets.slice(0, 30)" :key="asset.id">
                <div class="bg-white rounded-xl shadow-sm overflow-hidden border-l-4" :class="getAssetBorderColor(asset.asset_type)">
                    <!-- 에셋 헤더 -->
                    <div class="bg-gray-50 px-6 py-3 border-b">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <span class="text-lg" x-text="asset.asset_type_icon"></span>
                                <h3 class="text-lg font-semibold text-gray-900" x-text="asset.section_title"></h3>
                                <span class="text-xs bg-gray-200 text-gray-600 px-2 py-1 rounded-full" x-text="asset.asset_type_name"></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs text-gray-500" x-text="`섹션 ${index + 1}`"></span>
                                <span x-text="asset.summary?.status_icon"></span>
                            </div>
                        </div>
                    </div>

                    <!-- 에셋 컨텐츠 -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- 원문 -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <span class="text-blue-500 mr-2">📄</span>
                                    원문
                                </h4>
                                <div class="bg-blue-50 p-3 rounded-lg">
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap" x-text="asset.content"></p>
                                </div>
                            </div>

                            <!-- AI 요약 -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="text-green-500 mr-2">🤖</span>
                                        AI 요약
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div x-show="!isEditing(index, 'ai_summary') && asset.summary?.versions?.length > 1" class="flex items-center space-x-1">
                                            <span class="text-xs text-gray-500">버전:</span>
                                            <select @change="switchSectionVersion(index, $event.target.value)" class="text-xs bg-white border border-gray-300 rounded px-1 py-0.5 focus:ring-1 focus:ring-green-500">
                                                <template x-for="version in asset.summary?.versions || []" :key="version.id">
                                                    <option :value="version.version_number" :selected="version.is_current" x-text="version.version_display_name"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <button @click="toggleEditMode(index, 'ai_summary')" class="text-xs px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors" x-text="isEditing(index, 'ai_summary') ? '취소' : '편집'">
                                        </button>
                                    </div>
                                </h4>
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div x-show="!isEditing(index, 'ai_summary') && asset.summary?.versions?.length > 0" class="mb-2 pb-2 border-b border-green-200">
                                        <template x-for="version in asset.summary?.versions || []" :key="version.id">
                                            <div x-show="version.is_current" class="flex items-center justify-between text-xs text-green-600">
                                                <span x-text="version.version_display_name"></span>
                                                <span x-show="version.created_at" x-text="new Date(version.created_at).toLocaleString('ko-KR')"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <p x-show="!isEditing(index, 'ai_summary')" class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap" x-text="asset.summary?.ai_summary"></p>

                                    <div x-show="isEditing(index, 'ai_summary')" class="space-y-3">
                                        <textarea x-model="editingContent[index]['ai_summary']" class="w-full p-2 border border-gray-300 rounded resize-vertical min-h-[100px] text-sm" placeholder="AI 요약을 입력하세요..."></textarea>
                                        <div class="flex space-x-2">
                                            <button @click="saveEdit(index, 'ai_summary')" class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors">
                                                💾 저장 (새 버전)
                                            </button>
                                            <button @click="cancelEdit(index, 'ai_summary')" class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700 transition-colors">
                                                ❌ 취소
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 도움되는 내용 -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <span class="text-purple-500 mr-2">💡</span>
                                    도움되는 내용
                                </h4>
                                <div class="bg-purple-50 p-3 rounded-lg">
                                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap" x-text="asset.summary?.helpful_content"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- 맨 위로 버튼 -->
        <div class="fixed bottom-6 right-6">
            <button @click="window.scrollTo({top: 0, behavior: 'smooth'})" class="px-4 py-2 bg-indigo-600 text-white shadow-lg rounded-lg hover:bg-indigo-700">
                <i class="fa fa-arrow-up w-5 h-5"></i>
            </button>
        </div>
    </div>

    @include('700-page-rfx-main.400-javascript')
</body>
</html>
