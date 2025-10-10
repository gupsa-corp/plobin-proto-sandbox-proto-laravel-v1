<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>캘린더 뷰 테스트 가이드</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">🗓️ PMS 캘린더 뷰 E2E 테스트 가이드</h1>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">✅ 구현된 기능들</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h3 class="font-medium text-gray-900 mb-2">📱 인터랙션 기능</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• 날짜 클릭으로 일정 확인</li>
                        <li>• 날짜 더블클릭으로 일정 추가</li>
                        <li>• "일정 추가" 버튼</li>
                        <li>• "오늘" 버튼으로 현재 날짜 이동</li>
                        <li>• 필터 토글 (우선순위/상태)</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900 mb-2">🎨 시각적 효과</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• 오늘 날짜 파란색 테두리</li>
                        <li>• 애니메이션 핑 효과</li>
                        <li>• 호버 시 확대 효과</li>
                        <li>• 일정 개수 뱃지</li>
                        <li>• 상태별 아이콘 (완료: ✓, 진행중: 점)</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">🧪 테스트 방법</h2>
            
            <div class="mb-4">
                <h3 class="font-medium text-gray-900 mb-2">1. 기본 페이지 접속</h3>
                <div class="bg-gray-50 p-4 rounded-md">
                    <a href="http://127.0.0.1:8001/pms/calendar" 
                       class="text-blue-600 hover:text-blue-800 underline" 
                       target="_blank">
                        🔗 http://127.0.0.1:8001/pms/calendar
                    </a>
                </div>
            </div>

            <div class="mb-4">
                <h3 class="font-medium text-gray-900 mb-2">2. 브라우저 개발자 도구 열기</h3>
                <div class="bg-gray-50 p-4 rounded-md text-sm">
                    <p><strong>Chrome/Edge:</strong> F12 또는 Ctrl+Shift+I</p>
                    <p><strong>Firefox:</strong> F12 또는 Ctrl+Shift+I</p>
                    <p><strong>Safari:</strong> Cmd+Option+I</p>
                </div>
            </div>

            <div class="mb-4">
                <h3 class="font-medium text-gray-900 mb-2">3. 콘솔(Console) 탭에서 테스트 명령어 실행</h3>
                <div class="space-y-3">
                    <div class="bg-gray-50 p-4 rounded-md">
                        <h4 class="font-medium text-gray-700 mb-2">🔹 모달 상태 확인</h4>
                        <code class="text-sm bg-gray-200 p-2 rounded block">checkModalState()</code>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-md">
                        <h4 class="font-medium text-gray-700 mb-2">🔹 모달 강제 열기 테스트</h4>
                        <code class="text-sm bg-gray-200 p-2 rounded block">testCreateModal()</code>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">🎯 UI 인터랙션 테스트</h2>
            <div class="space-y-4">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                    <div>
                        <h4 class="font-medium text-gray-900">필터 버튼 클릭</h4>
                        <p class="text-sm text-gray-600">헤더의 "필터" 버튼을 클릭하여 필터 패널이 나타나는지 확인</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                    <div>
                        <h4 class="font-medium text-gray-900">"일정 추가" 버튼 클릭</h4>
                        <p class="text-sm text-gray-600">우상단의 녹색 "일정 추가" 버튼을 클릭하여 모달이 나타나는지 확인</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                    <div>
                        <h4 class="font-medium text-gray-900">날짜 셀 더블클릭</h4>
                        <p class="text-sm text-gray-600">캘린더의 아무 날짜나 더블클릭하여 해당 날짜로 일정 추가 모달이 나타나는지 확인</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">4</div>
                    <div>
                        <h4 class="font-medium text-gray-900">뷰 모드 전환</h4>
                        <p class="text-sm text-gray-600">"주별"과 "월별" 버튼을 클릭하여 뷰가 전환되는지 확인</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold">5</div>
                    <div>
                        <h4 class="font-medium text-gray-900">네비게이션 버튼</h4>
                        <p class="text-sm text-gray-600">이전/다음 화살표 버튼과 "오늘" 버튼이 정상 작동하는지 확인</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <h3 class="font-medium text-yellow-800 mb-2">⚠️ 문제 발생 시</h3>
            <p class="text-sm text-yellow-700">
                만약 모달이 표시되지 않거나 버튼이 작동하지 않는다면, 
                브라우저 콘솔의 에러 메시지를 확인하고 <code>testCreateModal()</code> 명령어로 강제 테스트해보세요.
            </p>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h3 class="font-medium text-green-800 mb-2">✨ 추가 테스트 항목</h3>
            <ul class="text-sm text-green-700 space-y-1">
                <li>• 호버 효과: 날짜 셀에 마우스를 올렸을 때 확대 및 "더블클릭하여 일정 추가" 텍스트 표시</li>
                <li>• 오늘 날짜: 현재 날짜가 파란색 테두리와 애니메이션으로 강조</li>
                <li>• 프로젝트 표시: 기존 프로젝트들이 각 날짜에 색상별로 표시</li>
                <li>• 반응형 디자인: 브라우저 창 크기를 조절했을 때 레이아웃 적응</li>
            </ul>
        </div>
    </div>
</body>
</html>