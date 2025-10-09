<div x-data="apiDocumentation()" x-init="init()" class="p-6 bg-white">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">API 문서</h1>
        <p class="text-gray-600">Gupsa PMS 도메인에서 사용 가능한 API 엔드포인트 목록입니다.</p>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex items-center justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
        <span class="ml-2 text-gray-600">API 문서를 불러오는 중...</span>
    </div>

    <!-- Error State -->
    <div x-show="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">오류가 발생했습니다</h3>
                <div class="mt-2 text-sm text-red-700" x-text="errorMessage"></div>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div x-show="!loading && !error && data" class="space-y-6">
        <!-- API Info -->
        <div class="bg-blue-50 rounded-lg p-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">API 정보</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">제목</dt>
                    <dd class="text-sm text-gray-900" x-text="data?.info?.title"></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">버전</dt>
                    <dd class="text-sm text-gray-900" x-text="data?.info?.version"></dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">서버</dt>
                    <dd class="text-sm text-gray-900" x-text="data?.servers?.[0]?.url"></dd>
                </div>
            </div>
            <div class="mt-3">
                <dt class="text-sm font-medium text-gray-500">설명</dt>
                <dd class="text-sm text-gray-900" x-text="data?.info?.description"></dd>
            </div>
        </div>

        <!-- API Endpoints -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">API 엔드포인트</h2>
            
            <div class="space-y-4">
                <template x-for="(pathData, path) in data?.paths || {}" :key="path">
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <!-- Path Header -->
                        <div class="px-4 py-3 bg-gray-100 border-b border-gray-200">
                            <h3 class="font-mono text-sm font-semibold text-gray-900" x-text="path"></h3>
                        </div>
                        
                        <!-- Methods -->
                        <div class="divide-y divide-gray-200">
                            <template x-for="(methodData, method) in pathData" :key="`${path}-${method}`">
                                <div class="p-4">
                                    <!-- Method Badge and Summary -->
                                    <div class="flex items-center mb-3">
                                        <span 
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase"
                                            :class="{
                                                'bg-green-100 text-green-800': method === 'get',
                                                'bg-blue-100 text-blue-800': method === 'post',
                                                'bg-yellow-100 text-yellow-800': method === 'put',
                                                'bg-red-100 text-red-800': method === 'delete'
                                            }"
                                            x-text="method">
                                        </span>
                                        <span class="ml-3 text-sm font-medium text-gray-900" x-text="methodData.summary"></span>
                                    </div>
                                    
                                    <!-- Description -->
                                    <p class="text-sm text-gray-600 mb-4" x-text="methodData.description"></p>
                                    
                                    <!-- Parameters -->
                                    <div x-show="methodData.parameters && methodData.parameters.length > 0" class="mb-4">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">파라미터</h4>
                                        <div class="bg-gray-50 rounded p-3">
                                            <template x-for="param in methodData.parameters" :key="`${path}-${method}-param-${param.name}`">
                                                <div class="flex justify-between items-start py-1">
                                                    <div class="flex-1">
                                                        <span class="font-mono text-sm text-gray-900" x-text="param.name"></span>
                                                        <span x-show="param.required" class="text-red-500 text-xs ml-1">*</span>
                                                        <span class="text-xs text-gray-500 ml-2" x-text="`(${param.in})`"></span>
                                                    </div>
                                                    <div class="flex-1 text-right">
                                                        <span class="text-sm text-gray-600" x-text="param.description"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <!-- Response Examples -->
                                    <div x-show="methodData.responses" class="mb-4">
                                        <h4 class="text-sm font-medium text-gray-900 mb-2">응답 예시</h4>
                                        <div class="space-y-2">
                                            <template x-for="(response, statusCode) in methodData.responses" :key="`${path}-${method}-${statusCode}`">
                                                <div class="bg-gray-50 rounded p-3">
                                                    <div class="flex items-center mb-2">
                                                        <span 
                                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                                                            :class="{
                                                                'bg-green-100 text-green-800': statusCode.startsWith('2'),
                                                                'bg-yellow-100 text-yellow-800': statusCode.startsWith('4'),
                                                                'bg-red-100 text-red-800': statusCode.startsWith('5')
                                                            }"
                                                            x-text="statusCode">
                                                        </span>
                                                        <span class="ml-2 text-sm text-gray-700" x-text="response.description"></span>
                                                    </div>
                                                    
                                                    <!-- JSON Example -->
                                                    <div x-show="response.content && response.content['application/json'] && response.content['application/json'].schema && response.content['application/json'].schema.properties">
                                                        <div class="bg-gray-800 text-green-400 p-3 rounded text-xs font-mono overflow-x-auto">
                                                            <pre x-text="formatJsonExample(response.content['application/json'].schema)"></pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <!-- Tags -->
                                    <div x-show="methodData.tags && methodData.tags.length > 0" class="flex flex-wrap gap-2">
                                        <template x-for="tag in methodData.tags" :key="`${path}-${method}-tag-${tag}`">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800" x-text="tag"></span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- 액션 버튼들 -->
        <div class="flex justify-between items-center">
            <!-- 다운로드 버튼들 -->
            <div class="flex space-x-3">
                <button @click="downloadApiSpec('yaml')" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    YAML 다운로드
                </button>
                <button @click="downloadApiSpec('json')" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    JSON 다운로드
                </button>
            </div>
            
            <!-- 새로고침 버튼 -->
            <button @click="fetchApiDocs()" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                새로고침
            </button>
        </div>

        <!-- 생성 시간 -->
        <div class="text-xs text-gray-500 text-center">
            마지막 업데이트: <span x-text="lastUpdated"></span>
        </div>
    </div>
</div>

<script>
function apiDocumentation() {
    return {
        loading: false,
        error: false,
        errorMessage: '',
        data: null,
        lastUpdated: '',

        init() {
            this.fetchApiDocs();
        },

        async fetchApiDocs() {
            this.loading = true;
            this.error = false;
            this.errorMessage = '';

            try {
                const response = await fetch('/api/sandbox/gupsa/pms/api-docs', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'include'
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || `HTTP ${response.status}`);
                }

                this.data = await response.json();
                this.lastUpdated = new Date().toLocaleString('ko-KR');
                
            } catch (error) {
                console.error('API 문서 조회 실패:', error);
                this.error = true;
                this.errorMessage = error.message || 'API 문서를 불러오는 중 오류가 발생했습니다.';
            } finally {
                this.loading = false;
            }
        },


        formatJsonExample(schema) {
            if (!schema || !schema.properties) return '{}';
            
            const example = {};
            for (const [key, prop] of Object.entries(schema.properties)) {
                if (prop.example !== undefined) {
                    example[key] = prop.example;
                } else if (prop.type === 'string') {
                    example[key] = 'string';
                } else if (prop.type === 'number' || prop.type === 'integer') {
                    example[key] = 0;
                } else if (prop.type === 'boolean') {
                    example[key] = true;
                } else if (prop.type === 'array') {
                    example[key] = [];
                } else if (prop.type === 'object') {
                    example[key] = {};
                }
            }
            
            return JSON.stringify(example, null, 2);
        },

        async downloadApiSpec(format) {
            try {
                const url = `/api/sandbox/gupsa/pms/api-docs/download/${format}`;
                
                // 다운로드 링크 생성 및 클릭
                const link = document.createElement('a');
                link.href = url;
                link.style.display = 'none';
                
                // CSRF 토큰과 함께 요청
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'include'
                });

                if (!response.ok) {
                    throw new Error(`다운로드 실패: HTTP ${response.status}`);
                }

                // Blob으로 변환하여 다운로드
                const blob = await response.blob();
                const downloadUrl = window.URL.createObjectURL(blob);
                
                link.href = downloadUrl;
                link.download = `gupsa-pms-api-docs.${format}`;
                document.body.appendChild(link);
                link.click();
                
                // 정리
                document.body.removeChild(link);
                window.URL.revokeObjectURL(downloadUrl);
                
                console.log(`API 스펙 ${format.toUpperCase()} 다운로드 완료`);
                
            } catch (error) {
                console.error('API 스펙 다운로드 실패:', error);
                alert(`API 스펙 다운로드 중 오류가 발생했습니다: ${error.message}`);
            }
        }
    };
}
</script>