<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI 문서 분석 결과</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-100 p-6">
    <div class="max-w-7xl mx-auto">
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
                                <div class="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-full">
                                    {{ $request['fileName'] ?? 'N/A' }}
                                </div>
                            </div>
                            <p class="text-gray-600">OCR 텍스트 추출 및 AI 분석</p>
                            <div class="flex items-center space-x-3 mt-2">
                                <p class="text-sm text-indigo-600">
                                    요청 ID: #{{ $request['id'] }}
                                </p>
                                <span class="text-sm text-gray-500">|</span>
                                <p class="text-sm text-gray-500">
                                    완료: {{ $request['completedAt'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('rfx.ai-analysis') }}"
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        목록으로
                    </a>
                </div>
            </div>
        </div>

        <!-- OCR 결과 섹션 -->
        <div class="space-y-6">
            @if(isset($request['result']))
                <!-- 문서 정보 -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">📄 문서 정보</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">파일명</p>
                            <p class="text-base font-medium text-gray-900">{{ $request['result']['original_filename'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">파일 형식</p>
                            <p class="text-base font-medium text-gray-900">{{ strtoupper($request['result']['file_type'] ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">파일 크기</p>
                            <p class="text-base font-medium text-gray-900">
                                @php
                                    $bytes = $request['result']['file_size'] ?? 0;
                                    if ($bytes < 1024) {
                                        echo $bytes . ' B';
                                    } elseif ($bytes < 1048576) {
                                        echo round($bytes / 1024, 2) . ' KB';
                                    } else {
                                        echo round($bytes / 1048576, 2) . ' MB';
                                    }
                                @endphp
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">전체 페이지</p>
                            <p class="text-base font-medium text-gray-900">{{ ($request['result']['total_pages'] ?? 0) }}페이지</p>
                        </div>
                    </div>
                </div>

                <!-- 품질 지표 -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">📈 품질 지표</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-2">평균 신뢰도</p>
                            <div class="text-3xl font-bold text-purple-600">
                                @php
                                    $pages = $request['result']['pages'] ?? [];
                                    $avgConfidence = 0;
                                    if (count($pages) > 0) {
                                        $total = array_sum(array_column($pages, 'average_confidence'));
                                        $avgConfidence = ($total / count($pages)) * 100;
                                    }
                                    echo round($avgConfidence, 2) . '%';
                                @endphp
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-2">텍스트 블록 수</p>
                            <div class="text-3xl font-bold text-indigo-600">
                                {{ array_sum(array_column($request['result']['pages'] ?? [], 'total_blocks')) }}
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-2">처리된 페이지</p>
                            <div class="text-3xl font-bold text-blue-600">
                                {{ count($request['result']['pages'] ?? []) }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 페이지별 상세 정보 -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">📑 페이지별 상세 정보</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">페이지</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">블록 수</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">신뢰도</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">처리 시간</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">품질</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($request['result']['pages'] ?? [] as $page)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900 font-medium">{{ $page['page_number'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $page['total_blocks'] ?? 0 }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ round(($page['average_confidence'] ?? 0) * 100, 2) }}%
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            @php
                                                $seconds = $page['processing_time'] ?? 0;
                                                if ($seconds < 1) {
                                                    echo round($seconds * 1000) . 'ms';
                                                } elseif ($seconds < 60) {
                                                    echo round($seconds, 2) . '초';
                                                } else {
                                                    $minutes = floor($seconds / 60);
                                                    $secs = round($seconds % 60, 2);
                                                    echo "{$minutes}분 {$secs}초";
                                                }
                                            @endphp
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $confidence = $page['average_confidence'] ?? 0;
                                                if ($confidence >= 0.9) {
                                                    $gradeClass = 'bg-green-100 text-green-800';
                                                    $gradeText = '매우 높음';
                                                } elseif ($confidence >= 0.7) {
                                                    $gradeClass = 'bg-blue-100 text-blue-800';
                                                    $gradeText = '높음';
                                                } elseif ($confidence >= 0.5) {
                                                    $gradeClass = 'bg-yellow-100 text-yellow-800';
                                                    $gradeText = '보통';
                                                } else {
                                                    $gradeClass = 'bg-red-100 text-red-800';
                                                    $gradeText = '낮음';
                                                }
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full font-medium {{ $gradeClass }}">
                                                {{ $gradeText }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
