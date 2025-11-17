<div>
    <!-- RFX 탭 네비게이션 -->
    @include('100-rfx-tab-navigation')

    <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">RFX 파일 업로드</h1>
            <p class="text-gray-600">문서를 업로드하여 AI 분석을 시작하세요</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Upload Area -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Upload Zone -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">파일 선택</h2>
                        
                        <!-- Drop Zone -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-gray-400 transition-colors">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">파일을 드래그하거나 클릭하여 선택</h3>
                            <p class="text-gray-600 mb-4">PDF, DOC, XLSX, 이미지 파일 등을 지원합니다</p>
                            
                            <input type="file" wire:model="files" multiple class="hidden" id="file-upload">
                            <label for="file-upload" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 cursor-pointer transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                파일 선택
                            </label>
                        </div>

                        <!-- Selected Files -->
                        @if(count($files) > 0)
                        <div class="mt-6">
                            <h3 class="font-medium text-gray-900 mb-3">선택된 파일 ({{ count($files) }}개)</h3>
                            <div class="space-y-2">
                                @foreach($files as $index => $file)
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $file->getClientOriginalName() }}</div>
                                            <div class="text-sm text-gray-500">{{ number_format($file->getSize() / 1024 / 1024, 2) }} MB</div>
                                        </div>
                                    </div>
                                    <button wire:click="removeFile({{ $index }})" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4">
                                <button wire:click="uploadFiles" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    업로드 시작
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- Upload Progress -->
                        @if(count($uploadProgress) > 0)
                        <div class="mt-6">
                            <h3 class="font-medium text-gray-900 mb-3">업로드 진행률</h3>
                            @foreach($uploadProgress as $index => $progress)
                            <div class="mb-2">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>파일 {{ $index + 1 }}</span>
                                    <span>{{ $progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Success Message -->
                        @if (session()->has('message'))
                        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="ml-2 text-green-700">{{ session('message') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- OCR 처리 요청 목록 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">OCR 처리 요청 목록</h2>

                        @if(count($ocrRequests) > 0)
                        <div class="space-y-3">
                            @foreach($ocrRequests as $request)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        @if(isset($request['file_type']) && str_contains($request['file_type'], 'pdf'))
                                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 18h12V6h-4V2H4v16zm-2 1V1a1 1 0 011-1h8.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a1 1 0 01-1 1H3a1 1 0 01-1-1z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $request['original_filename'] ?? 'Unknown' }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ isset($request['created_at']) ? date('Y-m-d H:i', strtotime($request['created_at'])) : '-' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="viewRequestDetail('{{ $request['request_id'] }}')"
                                            class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded hover:bg-blue-200">
                                        상세보기
                                    </button>
                                    <button wire:click="deleteRequest('{{ $request['request_id'] }}')"
                                            class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded hover:bg-red-200">
                                        삭제
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500 text-center py-8">OCR 처리 요청 내역이 없습니다.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Upload Guidelines -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="font-medium text-gray-900 mb-4">업로드 가이드라인</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        @foreach($guidelines as $guideline)
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $guideline }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- OCR 요청 상세보기 모달 -->
    @if($showDetailModal && $selectedRequest)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeDetailModal">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto" wire:click.stop>
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold text-gray-900">OCR 처리 결과</h2>
                    <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- 기본 정보 -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-3">기본 정보</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">파일명:</span>
                            <span class="ml-2 font-medium">{{ $selectedRequest['original_filename'] ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">요청 ID:</span>
                            <span class="ml-2 font-mono text-xs">{{ $selectedRequest['request_id'] ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">파일 타입:</span>
                            <span class="ml-2 font-medium">{{ $selectedRequest['file_type'] ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">생성일:</span>
                            <span class="ml-2">{{ isset($selectedRequest['created_at']) ? date('Y-m-d H:i:s', strtotime($selectedRequest['created_at'])) : '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">페이지 수:</span>
                            <span class="ml-2 font-medium">{{ $selectedRequest['total_pages'] ?? 0 }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">파일 크기:</span>
                            <span class="ml-2 font-medium">{{ isset($selectedRequest['file_size']) ? number_format($selectedRequest['file_size'] / 1024, 2) . ' KB' : '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 페이지별 정보 -->
                @if(isset($selectedRequest['pages']) && count($selectedRequest['pages']) > 0)
                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">페이지별 상세 정보</h3>
                    <div class="space-y-3">
                        @foreach($selectedRequest['pages'] as $page)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">페이지:</span>
                                    <span class="ml-2 font-medium">{{ $page['page_number'] ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">블록 수:</span>
                                    <span class="ml-2 font-medium">{{ $page['total_blocks'] ?? 0 }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">평균 신뢰도:</span>
                                    <span class="ml-2 font-medium">{{ isset($page['average_confidence']) ? number_format($page['average_confidence'] * 100, 1) . '%' : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- 전체 텍스트 -->
                @if(isset($selectedRequest['full_text']))
                <div>
                    <h3 class="font-semibold text-gray-900 mb-3">추출된 전체 텍스트</h3>
                    <div class="bg-gray-50 rounded-lg p-4 max-h-96 overflow-y-auto">
                        <pre class="text-sm text-gray-900 whitespace-pre-wrap">{{ $selectedRequest['full_text'] }}</pre>
                    </div>
                </div>
                @endif
            </div>

            <div class="p-6 border-t border-gray-200 bg-gray-50">
                <button wire:click="closeDetailModal" class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    닫기
                </button>
            </div>
        </div>
    </div>
    @endif
</div>