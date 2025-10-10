<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI 문서 분석 결과 - {{ $requestInfo['file_name'] ?? 'Document' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireStyles
</head>
<body>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-100 p-6">
        <!-- Flash 메시지 -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center space-x-2">
                    <span class="text-green-600">✅</span>
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center space-x-2">
                    <span class="text-red-600">❌</span>
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- 헤더 -->
        @include('700-page-rfx-document-assets.100-header')

        <!-- 메인 컨텐츠 -->
        @if(count($assets) === 0)
            <!-- 빈 상태 -->
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-gray-400 text-2xl">📄</span>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">섹션이 없습니다</h3>
                <p class="text-gray-500 mb-4">AI 분석이 완료되면 섹션이 표시됩니다.</p>
                <a href="{{ route('rfx.ai-analysis') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    파일 목록으로 돌아가기
                </a>
            </div>
        @else
            <!-- 섹션 카드 목록 -->
            <div class="space-y-6">
                @foreach($assets as $asset)
                    @include('700-page-rfx-document-assets.200-asset-card', ['asset' => $asset, 'loop' => $loop])
                @endforeach
            </div>
        @endif

        <!-- 편집 모달 -->
        @include('700-page-rfx-document-assets.300-edit-modal')

        <!-- 맨 위로 버튼 -->
        <div class="fixed bottom-6 right-6">
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
                    class="px-4 py-2 bg-indigo-600 text-white shadow-lg rounded-lg hover:bg-indigo-700 transition-colors">
                ↑
            </button>
        </div>
    </div>

    @livewireScripts
    @include('700-page-rfx-document-assets.400-scripts')
</body>
</html>
