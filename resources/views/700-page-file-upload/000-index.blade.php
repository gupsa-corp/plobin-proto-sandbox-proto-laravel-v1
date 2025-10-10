<x-300-layout-common.000-app>
    <x-slot:title>파일 업로드</x-slot:title>
    
    {{-- 파일 업로드 템플릿 --}}
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 p-6">

    {{-- 헤더 --}}
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-xl">📤</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">파일 업로드</h1>
                        <p class="text-gray-600">여러 파일을 한 번에 업로드하세요</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">업로드 제한</div>
                    <div class="text-lg font-semibold text-gray-900">10MB / 파일</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 업로드 영역 --}}
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm p-8 mb-6">
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-green-500 hover:bg-green-50 transition-colors cursor-pointer">
                <div class="mb-4">
                    <span class="text-6xl">📁</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">파일을 드래그 앤 드롭하거나 클릭하여 선택하세요</h3>
                <p class="text-gray-600 mb-4">JPG, PNG, PDF, DOC, XLS, TXT 등 다양한 형식을 지원합니다</p>
                <input type="file" id="file-input" multiple class="hidden" accept="*/*">
                <button type="button" onclick="document.getElementById('file-input').click()" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition-colors">
                    파일 선택
                </button>
            </div>
        </div>

        {{-- 선택된 파일 목록 --}}
        <div id="selected-files" class="bg-white rounded-xl shadow-sm p-6 mb-6 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="mr-2">📋</span>
                선택된 파일 (<span id="file-count">0</span>개)
            </h3>
            <div id="file-list" class="space-y-3 mb-4">
                {{-- 선택된 파일들이 여기에 표시됩니다 --}}
            </div>
            <div class="flex justify-between items-center pt-4 border-t">
                <div class="text-sm text-gray-600">
                    총 용량: <span id="total-size" class="font-semibold">0 MB</span>
                </div>
                <div class="space-x-2">
                    <button type="button" id="clear-files" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        전체 삭제
                    </button>
                    <button type="button" id="upload-files" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        업로드 시작
                    </button>
                </div>
            </div>
        </div>

        {{-- 업로드 진행 상황 --}}
        <div id="upload-progress" class="bg-white rounded-xl shadow-sm p-6 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="mr-2">⏳</span>
                업로드 진행 상황
            </h3>
            <div class="space-y-3" id="progress-list">
                {{-- 업로드 진행 상황이 여기에 표시됩니다 --}}
            </div>
            <div class="mt-4 pt-4 border-t">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>전체 진행률</span>
                    <span id="overall-progress-text">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div id="overall-progress-bar" class="bg-green-500 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>

        {{-- 업로드 완료 --}}
        <div id="upload-complete" class="bg-white rounded-xl shadow-sm p-6 hidden">
            <div class="text-center">
                <div class="mb-4">
                    <span class="text-6xl">✅</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">업로드 완료!</h3>
                <p class="text-gray-600 mb-4">모든 파일이 성공적으로 업로드되었습니다.</p>
                <div class="flex justify-center space-x-4">
                    <button type="button" onclick="window.location.reload()" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        새 업로드
                    </button>
                    <button type="button" onclick="window.location.href='{{ route('file-list') }}'" class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        파일 목록 보기
                    </button>
                </div>
            </div>
        </div>

        {{-- 업로드 가이드라인 --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <span class="mr-2">ℹ️</span>
                업로드 가이드라인
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">지원 형식</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• 이미지: JPG, PNG, GIF, WebP</li>
                        <li>• 문서: PDF, DOC, DOCX, TXT</li>
                        <li>• 스프레드시트: XLS, XLSX, CSV</li>
                        <li>• 기타: 다양한 파일 형식</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">제한사항</h4>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• 최대 파일 크기: 10MB</li>
                        <li>• 한 번에 최대 20개 파일</li>
                        <li>• 총 업로드 크기: 50MB</li>
                        <li>• 악성코드 자동 검사</li>
                        <li>• 안전한 저장소에 보관</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @include('700-page-file-upload.400-upload-scripts')
    </div>
</x-300-layout-common.000-app>