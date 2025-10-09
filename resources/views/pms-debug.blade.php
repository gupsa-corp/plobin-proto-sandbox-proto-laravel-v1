<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PMS Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">PMS 실시간 디버깅</h1>
        
        <!-- 실제 컴포넌트 로드 -->
        @livewire('pms.project-list.livewire')
        
        <!-- 디버그 정보 -->
        <div class="mt-8 bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">디버그 정보</h2>
            <div id="debug-info" class="space-y-2 text-sm font-mono">
                <div>페이지 로드 시간: <span id="load-time"></span></div>
                <div>Livewire 상태: <span id="livewire-status">로딩 중...</span></div>
                <div>버튼 클릭 횟수: <span id="click-count">0</span></div>
            </div>
            
            <button onclick="testLivewire()" class="mt-4 bg-red-600 text-white px-4 py-2 rounded">
                Livewire 강제 테스트
            </button>
        </div>
    </div>

    <script>
        let clickCount = 0;
        document.getElementById('load-time').textContent = new Date().toLocaleTimeString();
        
        document.addEventListener('livewire:load', function () {
            document.getElementById('livewire-status').textContent = '✅ 로드됨';
            
            // 모든 wire:click 버튼에 클릭 리스너 추가
            document.querySelectorAll('[wire\\:click]').forEach(button => {
                button.addEventListener('click', function() {
                    clickCount++;
                    document.getElementById('click-count').textContent = clickCount;
                    console.log('Button clicked:', this.getAttribute('wire:click'));
                });
            });
        });
        
        function testLivewire() {
            if (window.Livewire) {
                console.log('Livewire 객체 존재:', window.Livewire);
                console.log('컴포넌트 개수:', Object.keys(window.Livewire.components.componentsById).length);
            } else {
                console.log('Livewire 객체 없음');
            }
        }
    </script>
    
    @livewireScripts
</body>
</html>