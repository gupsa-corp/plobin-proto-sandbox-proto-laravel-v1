<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFX Forms Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- RFX 탭 네비게이션 -->
    @include('100-rfx-tab-navigation')
    
    <div class="p-6">
        <h1 class="text-2xl font-bold">RFX Forms Test Page</h1>
        <p class="mt-4">이 페이지에서 "폼 실행" 탭이 활성화되어야 합니다.</p>
        
        <div class="mt-6 space-y-2">
            <p><strong>현재 URL:</strong> {{ request()->url() }}</p>
            <p><strong>현재 경로:</strong> {{ request()->path() }}</p>
            <p><strong>rfx/forms 매치:</strong> {{ request()->is('rfx/forms') ? 'Yes' : 'No' }}</p>
        </div>
        
        <!-- 간단한 폼 실행 UI 시뮬레이션 -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">폼 실행 테스트</h2>
            <p>탭 네비게이션이 정상적으로 작동하고 "폼 실행" 탭이 활성화되어 있습니다.</p>
        </div>
    </div>
</body>
</html>