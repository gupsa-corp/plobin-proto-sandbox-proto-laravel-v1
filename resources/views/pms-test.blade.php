<!DOCTYPE html>
<html>
<head>
    <title>PMS 기능 테스트</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">PMS 기능 실제 작동 테스트</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 프로젝트 목록 페이지 테스트 -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">프로젝트 목록 페이지</h2>
                <div class="space-y-3">
                    <a href="/pms/projects" target="_blank" 
                       class="block bg-blue-600 text-white px-4 py-2 rounded text-center hover:bg-blue-700">
                        프로젝트 목록 열기
                    </a>
                    <div class="text-sm text-gray-600">
                        <p>✅ 체크할 항목:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>"새 프로젝트 추가" 버튼 클릭 시 모달 열림</li>
                            <li>"상세보기" 버튼 클릭 시 메시지 표시</li>
                            <li>"편집" 버튼 클릭 시 편집 모달 열림</li>
                            <li>테이블 뷰 버튼 클릭 시 페이지 전환</li>
                            <li>검색, 필터링, 정렬 기능</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 테이블 뷰 페이지 테스트 -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">테이블 뷰 페이지</h2>
                <div class="space-y-3">
                    <a href="/pms/table-view" target="_blank" 
                       class="block bg-green-600 text-white px-4 py-2 rounded text-center hover:bg-green-700">
                        테이블 뷰 열기
                    </a>
                    <div class="text-sm text-gray-600">
                        <p>✅ 체크할 항목:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>"컬럼 설정" 버튼 클릭 시 모달 열림</li>
                            <li>테이블 헤더 클릭 시 정렬</li>
                            <li>"편집" 버튼 클릭 시 편집 모달 열림</li>
                            <li>컬럼 선택/해제 기능</li>
                            <li>필터링 기능</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Livewire 상태 확인 -->
        <div class="bg-white p-6 rounded-lg shadow mt-6">
            <h2 class="text-xl font-semibold mb-4">Livewire 상태 확인</h2>
            <div x-data="{ 
                livewireLoaded: false,
                buttonFound: false,
                wireclickFound: false 
            }" 
            x-init="
                document.addEventListener('livewire:load', () => { livewireLoaded = true });
                buttonFound = !!document.querySelector('[wire\\:click=\\'openCreateModal\\']');
                wireclickFound = document.documentElement.outerHTML.includes('wire:click');
            " class="space-y-2">
                <div x-show="livewireLoaded" class="text-green-600">✅ Livewire가 성공적으로 로드됨</div>
                <div x-show="!livewireLoaded" class="text-red-600">❌ Livewire 로드 실패</div>
                <div x-show="buttonFound" class="text-green-600">✅ wire:click 버튼 발견됨</div>
                <div x-show="!buttonFound" class="text-red-600">❌ wire:click 버튼 없음</div>
                <div x-show="wireclickFound" class="text-green-600">✅ HTML에 wire:click 속성 존재</div>
                <div x-show="!wireclickFound" class="text-red-600">❌ HTML에 wire:click 속성 없음</div>
            </div>
        </div>

        <!-- 브라우저 콘솔 확인 -->
        <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mt-6">
            <h3 class="font-semibold text-yellow-800">브라우저 콘솔 확인 방법:</h3>
            <ol class="list-decimal list-inside mt-2 text-yellow-700 space-y-1">
                <li>F12 또는 Ctrl+Shift+I로 개발자 도구 열기</li>
                <li>Console 탭 선택</li>
                <li>다음 메시지들이 출력되는지 확인:
                    <ul class="list-disc list-inside ml-4 mt-1">
                        <li>"PMS Project List page loaded"</li>
                        <li>"Livewire loaded successfully"</li>
                        <li>"Create button found: [HTMLElement]"</li>
                    </ul>
                </li>
                <li>버튼 클릭 시 "Create button clicked!" 메시지 확인</li>
            </ol>
        </div>
    </div>
</body>
</html>