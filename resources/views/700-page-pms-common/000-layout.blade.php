<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'PMS' }} - Plobin Proto</title>

    <script>
        // 초기 URL 전송 (페이지 로드 시)
        window.parent.postMessage({ type: 'url-change', url: window.location.href }, '*');

        // popstate 이벤트 (뒤로/앞으로 버튼)
        window.addEventListener('popstate', function() {
            window.parent.postMessage({ type: 'url-change', url: window.location.href }, '*');
        });

        // pushState/replaceState 오버라이드 (SPA 페이지 이동 감지)
        (function() {
            const originalPushState = history.pushState;
            const originalReplaceState = history.replaceState;

            history.pushState = function() {
                originalPushState.apply(this, arguments);
                window.parent.postMessage({ type: 'url-change', url: window.location.href }, '*');
            };

            history.replaceState = function() {
                originalReplaceState.apply(this, arguments);
                window.parent.postMessage({ type: 'url-change', url: window.location.href }, '*');
            };
        })();
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    @livewireStyles
    @stack('styles')
    @stack('scripts-head')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- PMS 공통 네비게이션 -->
        @include('700-page-pms-common.100-header-navigation')

        <!-- 메인 콘텐츠 영역 -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
