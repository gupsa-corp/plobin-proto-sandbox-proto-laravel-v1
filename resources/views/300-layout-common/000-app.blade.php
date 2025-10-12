<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'PMS System' }}</title>
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
<body>
    {{ $slot }}

    @livewireScripts
    @stack('scripts')
</body>
</html>
