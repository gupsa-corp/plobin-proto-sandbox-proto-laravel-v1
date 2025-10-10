<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFX Navigation Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- RFX 탭 네비게이션 테스트 -->
    @include('100-rfx-tab-navigation')
    
    <div class="p-6">
        <h1 class="text-2xl font-bold">RFX Navigation Test Page</h1>
        <p class="mt-4">탭 네비게이션이 정상적으로 표시되었다면 성공입니다.</p>
        
        <div class="mt-6 space-y-2">
            <p><strong>현재 URL:</strong> {{ request()->url() }}</p>
            <p><strong>현재 경로:</strong> {{ request()->path() }}</p>
            <p><strong>rfx/dashboard 매치:</strong> {{ request()->is('rfx/dashboard') ? 'Yes' : 'No' }}</p>
            <p><strong>rfx/forms 매치:</strong> {{ request()->is('rfx/forms') ? 'Yes' : 'No' }}</p>
        </div>
    </div>
</body>
</html>