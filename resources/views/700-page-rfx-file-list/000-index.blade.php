<div>
    <!-- RFX 탭 네비게이션 -->
    @include('100-rfx-tab-navigation')

    <div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">파일 관리</h1>
            <p class="text-gray-600">업로드된 파일을 관리하고 분석 상태를 확인하세요</p>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">검색</label>
                        <input 
                            type="text" 
                            wire:model.debounce.300ms="search"
                            placeholder="파일명 또는 태그로 검색..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                    
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">상태</label>
                        <select wire:model="statusFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">전체</option>
                            <option value="uploaded">업로드됨</option>
                            <option value="analyzing">분석중</option>
                            <option value="completed">완료</option>
                            <option value="error">오류</option>
                        </select>
                    </div>
                    
                    <!-- Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">파일 타입</label>
                        <select wire:model="typeFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">전체</option>
                            <option value="pdf">PDF</option>
                            <option value="docx">DOCX</option>
                            <option value="xlsx">XLSX</option>
                        </select>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-end">
                        <a href="/rfx/upload" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-center">
                            새 파일 업로드
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Files List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <!-- Table Header -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">파일 목록 ({{ count($files) }}개)</h2>
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <span>정렬:</span>
                        <button wire:click="sortBy('name')" class="hover:text-gray-900 {{ $sortBy === 'name' ? 'font-semibold' : '' }}">
                            이름 
                            @if($sortBy === 'name')
                                @if($sortDirection === 'asc') ↑ @else ↓ @endif
                            @endif
                        </button>
                        <span>|</span>
                        <button wire:click="sortBy('uploadedAt')" class="hover:text-gray-900 {{ $sortBy === 'uploadedAt' ? 'font-semibold' : '' }}">
                            업로드일
                            @if($sortBy === 'uploadedAt')
                                @if($sortDirection === 'asc') ↑ @else ↓ @endif
                            @endif
                        </button>
                    </div>
                </div>

                <!-- Files Table -->
                @if(count($files) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">파일</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">크기</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">상태</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">업로드일</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">태그</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">작업</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($files as $file)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            @if($file['type'] === 'pdf')
                                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6h-4V2H4v16zm-2 1V1a1 1 0 011-1h8.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a1 1 0 01-1 1H3a1 1 0 01-1-1z"/>
                                                </svg>
                                            @elseif(in_array($file['type'], ['doc', 'docx']))
                                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6h-4V2H4v16zm-2 1V1a1 1 0 011-1h8.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a1 1 0 01-1 1H3a1 1 0 01-1-1z"/>
                                                </svg>
                                            @elseif(in_array($file['type'], ['xls', 'xlsx']))
                                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6h-4V2H4v16zm-2 1V1a1 1 0 011-1h8.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a1 1 0 01-1 1H3a1 1 0 01-1-1z"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6h-4V2H4v16zm-2 1V1a1 1 0 011-1h8.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V19a1 1 0 01-1 1H3a1 1 0 01-1-1z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $file['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $file['originalName'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $file['size'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full font-medium
                                        @if($file['status'] === 'completed') bg-green-100 text-green-800
                                        @elseif($file['status'] === 'analyzing') bg-yellow-100 text-yellow-800
                                        @elseif($file['status'] === 'error') bg-red-100 text-red-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        @if($file['status'] === 'completed') 완료
                                        @elseif($file['status'] === 'analyzing') 분석중
                                        @elseif($file['status'] === 'error') 오류
                                        @else 업로드됨
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $file['uploadedAt'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($file['tags'] as $tag)
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="selectFile({{ $file['id'] }})" class="text-blue-600 hover:text-blue-900">
                                            상세보기
                                        </button>
                                        @if($file['status'] === 'uploaded')
                                        <button wire:click="analyzeFile({{ $file['id'] }})" class="text-green-600 hover:text-green-900">
                                            분석시작
                                        </button>
                                        @endif
                                        <button wire:click="deleteFile({{ $file['id'] }})" class="text-red-600 hover:text-red-900">
                                            삭제
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">파일이 없습니다</h3>
                    <p class="mt-1 text-sm text-gray-500">새 파일을 업로드하여 시작하세요.</p>
                    <div class="mt-6">
                        <a href="/rfx/upload" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            파일 업로드
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- File Detail Modal -->
        @if($selectedFile)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="selectFile(null)">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" wire:click.stop>
                <div class="mt-3">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">파일 상세 정보</h3>
                        <button wire:click="selectFile(null)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">파일명</label>
                            <p class="text-sm text-gray-900">{{ $selectedFile['name'] }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">크기</label>
                            <p class="text-sm text-gray-900">{{ $selectedFile['size'] }}</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">상태</label>
                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                @if($selectedFile['status'] === 'completed') bg-green-100 text-green-800
                                @elseif($selectedFile['status'] === 'analyzing') bg-yellow-100 text-yellow-800
                                @elseif($selectedFile['status'] === 'error') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                @if($selectedFile['status'] === 'completed') 완료
                                @elseif($selectedFile['status'] === 'analyzing') 분석중
                                @elseif($selectedFile['status'] === 'error') 오류
                                @else 업로드됨
                                @endif
                            </span>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">업로드일</label>
                            <p class="text-sm text-gray-900">{{ $selectedFile['uploadedAt'] }}</p>
                        </div>
                        
                        @if($selectedFile['analyzedAt'])
                        <div>
                            <label class="block text-sm font-medium text-gray-700">분석 완료일</label>
                            <p class="text-sm text-gray-900">{{ $selectedFile['analyzedAt'] }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">태그</label>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($selectedFile['tags'] as $tag)
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                        
                        @if($selectedFile['summary'])
                        <div>
                            <label class="block text-sm font-medium text-gray-700">요약</label>
                            <p class="text-sm text-gray-900 mt-1">{{ $selectedFile['summary'] }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">다운로드 횟수</label>
                            <p class="text-sm text-gray-900">{{ $selectedFile['downloadCount'] }}회</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Success Message -->
        @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg">
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
</div>