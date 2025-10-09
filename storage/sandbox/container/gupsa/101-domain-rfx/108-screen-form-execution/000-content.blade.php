{{-- 폼 실행 화면 템플릿 --}}
<?php
    require_once __DIR__ . "/../../../../../../bootstrap.php";
use App\Services\TemplateCommonService;


    $screenInfo = TemplateCommonService::getCurrentTemplateScreenInfo();
    $uploadPaths = TemplateCommonService::getTemplateUploadPaths();
?>
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-blue-100 p-6"
     x-data="formExecutionData()"
     x-init="init()"
     x-cloak>

    {{-- 헤더 --}}
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-xl">📝</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">폼 실행 화면</h1>
                        <p class="text-gray-600">JSON 기반 동적 폼을 실행하고 관리하세요</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">연결 상태</div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full"
                             :class="connectionStatus === 'connected' ? 'bg-green-500' : 'bg-red-500'"></div>
                        <span class="text-sm font-medium" x-text="connectionStatusText">확인 중...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 폼 JSON 로더 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- 좌측: 폼 설정 및 로더 --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- JSON 업로드/입력 카드 --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">폼 JSON 로드</h3>

                {{-- 샘플 JSON 버튼들 --}}
                <div class="space-y-3 mb-4">
                    <button @click="loadSampleForm('contact')"
                            class="w-full px-4 py-2 text-left border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="font-medium text-gray-900">연락처 폼</div>
                        <div class="text-sm text-gray-500">이름, 이메일, 메시지 필드</div>
                    </button>
                    <button @click="loadSampleForm('survey')"
                            class="w-full px-4 py-2 text-left border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="font-medium text-gray-900">설문조사 폼</div>
                        <div class="text-sm text-gray-500">다양한 입력 타입 예제</div>
                    </button>
                    <button @click="loadSampleForm('registration')"
                            class="w-full px-4 py-2 text-left border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="font-medium text-gray-900">회원가입 폼</div>
                        <div class="text-sm text-gray-500">검증 규칙 포함</div>
                    </button>
                    <button @click="loadSampleForm('tire_factory_survey')"
                            class="w-full px-4 py-2 text-left border border-gray-200 rounded-lg hover:bg-gray-50 bg-gradient-to-r from-blue-50 to-purple-50 border-blue-200">
                        <div class="font-medium text-gray-900">🏭 타이어 공장 수요 조사</div>
                        <div class="text-sm text-gray-600">2025년 시장 데이터 기반 (현대기아 746만대, EV 22.1% 성장)</div>
                    </button>
                </div>

                {{-- JSON 입력 영역 --}}
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">
                        또는 직접 JSON 입력
                    </label>
                    <textarea
                        x-model="jsonInput"
                        placeholder='{"name": "example", "description": "설명", "components": [], "settings": {}}'
                        class="w-full h-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 text-sm font-mono"
                        @input="validateJson()">
                    </textarea>
                    <div x-show="jsonError" class="text-red-500 text-sm" x-text="jsonError"></div>
                    <button @click="loadJsonForm()"
                            :disabled="!isValidJson"
                            class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:bg-gray-300 disabled:cursor-not-allowed">
                        JSON 로드
                    </button>
                </div>
            </div>

            {{-- 폼 정보 카드 --}}
            <div x-show="currentForm.name" class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">폼 정보</h3>
                <div class="space-y-2">
                    <div>
                        <span class="text-sm font-medium text-gray-600">이름:</span>
                        <span class="text-sm text-gray-900 ml-2" x-text="currentForm.name"></span>
                    </div>
                    <div x-show="currentForm.description">
                        <span class="text-sm font-medium text-gray-600">설명:</span>
                        <span class="text-sm text-gray-900 ml-2" x-text="currentForm.description"></span>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-600">컴포넌트 수:</span>
                        <span class="text-sm text-gray-900 ml-2" x-text="currentForm.components?.length || 0"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 우측: 폼 실행 영역 --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">폼 실행</h3>
                    <div class="flex space-x-2">
                        <button @click="resetForm()"
                                x-show="currentForm.name"
                                class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                            초기화
                        </button>
                        <button @click="previewForm()"
                                x-show="currentForm.name"
                                class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            미리보기
                        </button>
                    </div>
                </div>

                {{-- 폼이 로드되지 않았을 때 --}}
                <div x-show="!currentForm.name" class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-gray-400 text-2xl">📝</span>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">폼을 선택해주세요</h4>
                    <p class="text-gray-500">좌측에서 샘플 폼을 선택하거나 JSON을 직접 입력해주세요.</p>
                </div>

                {{-- 동적 폼 렌더링 --}}
                <form x-show="currentForm.name"
                      @submit.prevent="submitForm()"
                      class="space-y-6">

                    <template x-for="component in currentForm.components" :key="component.key">
                        <div class="space-y-2">
                            {{-- 레이블 --}}
                            <label x-show="component.label"
                                   class="block text-sm font-medium text-gray-700"
                                   x-text="component.label + (component.required ? ' *' : '')">
                            </label>

                            {{-- 입력 필드 타입별 렌더링 --}}
                            <template x-if="component.type === 'text' || component.type === 'email' || component.type === 'password'">
                                <input :type="component.type || 'text'"
                                       :name="component.key"
                                       :placeholder="component.placeholder || ''"
                                       :required="component.required"
                                       x-model="formData[component.key]"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            </template>

                            <template x-if="component.type === 'textarea'">
                                <textarea :name="component.key"
                                         :placeholder="component.placeholder || ''"
                                         :required="component.required"
                                         :rows="component.rows || 3"
                                         x-model="formData[component.key]"
                                         class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                </textarea>
                            </template>

                            <template x-if="component.type === 'select'">
                                <select :name="component.key"
                                        :required="component.required"
                                        x-model="formData[component.key]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">선택해주세요</option>
                                    <template x-for="option in component.options" :key="option.value">
                                        <option :value="option.value" x-text="option.label"></option>
                                    </template>
                                </select>
                            </template>

                            <template x-if="component.type === 'radio'">
                                <div class="space-y-2">
                                    <template x-for="option in component.options" :key="option.value">
                                        <label class="flex items-center">
                                            <input type="radio"
                                                   :name="component.key"
                                                   :value="option.value"
                                                   :required="component.required"
                                                   x-model="formData[component.key]"
                                                   class="mr-2 text-purple-600 focus:ring-purple-500">
                                            <span x-text="option.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </template>

                            <template x-if="component.type === 'checkbox'">
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           :name="component.key"
                                           :required="component.required"
                                           x-model="formData[component.key]"
                                           class="mr-2 text-purple-600 focus:ring-purple-500 rounded">
                                    <span x-text="component.checkboxLabel || component.label"></span>
                                </label>
                            </template>

                            {{-- 도움말 텍스트 --}}
                            <p x-show="component.helpText"
                               class="text-sm text-gray-500"
                               x-text="component.helpText"></p>
                        </div>
                    </template>

                    {{-- 제출 버튼 --}}
                    <div x-show="currentForm.components?.length > 0" class="pt-4 border-t">
                        <div class="flex justify-end space-x-3">
                            <button type="button"
                                    @click="resetForm()"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                재설정
                            </button>
                            <button type="submit"
                                    :disabled="isSubmitting"
                                    class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 disabled:bg-gray-400">
                                <span x-show="!isSubmitting">제출</span>
                                <span x-show="isSubmitting">제출 중...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 제출 결과 모달 --}}
    <div x-show="showResultModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">

        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">제출 결과</h3>
                        <button @click="showResultModal = false" class="text-gray-400 hover:text-gray-600">
                            <span class="sr-only">닫기</span>
                            <i class="fa fa-plus h-6 w-6"></i>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div x-show="submitResult.success" class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <span class="text-green-600 mr-2">✅</span>
                                <span class="text-green-800 font-medium">폼이 성공적으로 제출되었습니다!</span>
                            </div>
                            <pre x-show="submitResult.data"
                                 class="mt-2 text-sm text-green-700 bg-green-100 p-2 rounded overflow-auto max-h-40"
                                 x-text="JSON.stringify(submitResult.data, null, 2)"></pre>
                        </div>

                        <div x-show="!submitResult.success" class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <span class="text-red-600 mr-2">❌</span>
                                <span class="text-red-800 font-medium">제출 중 오류가 발생했습니다</span>
                            </div>
                            <p x-show="submitResult.message"
                               class="mt-2 text-sm text-red-700"
                               x-text="submitResult.message"></p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button"
                            @click="showResultModal = false"
                            class="inline-flex w-full justify-center rounded-md bg-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 sm:ml-3 sm:w-auto">
                        확인
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function formExecutionData() {
    return {
        // 연결 상태
        connectionStatus: 'checking', // checking, connected, disconnected
        connectionStatusText: '확인 중...',

        // JSON 입력 관련
        jsonInput: '',
        jsonError: '',
        isValidJson: false,

        // 현재 폼
        currentForm: {
            name: '',
            description: '',
            components: [],
            settings: {}
        },

        // 폼 데이터
        formData: {},

        // 제출 관련
        isSubmitting: false,
        showResultModal: false,
        submitResult: {
            success: false,
            data: null,
            message: ''
        },

        // 초기화
        async init() {
            await this.checkConnection();
        },

        // 연결 상태 확인
        async checkConnection() {
            try {
                const currentSandbox = window.location.pathname.split('/')[2];
                const response = await fetch(`/sandbox/${currentSandbox}/backend/form-creator.php?action=status`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        this.connectionStatus = 'connected';
                        this.connectionStatusText = '연결됨';
                        console.log('DB 상태:', result.data.database);
                    } else {
                        this.connectionStatus = 'disconnected';
                        this.connectionStatusText = '연결 오류';
                    }
                } else {
                    this.connectionStatus = 'disconnected';
                    this.connectionStatusText = '연결 실패';
                }
            } catch (error) {
                this.connectionStatus = 'disconnected';
                this.connectionStatusText = '연결 불가';
                console.error('Connection check failed:', error);
            }
        },

        // JSON 유효성 검증
        validateJson() {
            this.jsonError = '';
            this.isValidJson = false;

            if (!this.jsonInput.trim()) {
                return;
            }

            try {
                const parsed = JSON.parse(this.jsonInput);
                if (parsed.name && parsed.components && Array.isArray(parsed.components)) {
                    this.isValidJson = true;
                } else {
                    this.jsonError = '필수 필드가 누락되었습니다: name, components';
                }
            } catch (error) {
                this.jsonError = 'JSON 형식이 올바르지 않습니다: ' + error.message;
            }
        },

        // 샘플 폼 로드
        loadSampleForm(type) {
            const samples = {
                contact: {
                    name: "연락처 폼",
                    description: "간단한 연락처 수집 폼입니다",
                    components: [
                        {
                            key: "name",
                            type: "text",
                            label: "이름",
                            placeholder: "이름을 입력해주세요",
                            required: true
                        },
                        {
                            key: "email",
                            type: "email",
                            label: "이메일",
                            placeholder: "email@example.com",
                            required: true
                        },
                        {
                            key: "message",
                            type: "textarea",
                            label: "메시지",
                            placeholder: "메시지를 입력해주세요",
                            rows: 4,
                            required: true
                        }
                    ],
                    settings: {
                        submitUrl: "http://localhost:8500/sandbox/form-creator",
                        method: "POST"
                    }
                },
                survey: {
                    name: "설문조사 폼",
                    description: "다양한 입력 타입을 포함한 설문조사 폼입니다",
                    components: [
                        {
                            key: "satisfaction",
                            type: "radio",
                            label: "만족도를 선택해주세요",
                            required: true,
                            options: [
                                { value: "very_satisfied", label: "매우 만족" },
                                { value: "satisfied", label: "만족" },
                                { value: "neutral", label: "보통" },
                                { value: "dissatisfied", label: "불만족" }
                            ]
                        },
                        {
                            key: "category",
                            type: "select",
                            label: "관심 분야",
                            required: true,
                            options: [
                                { value: "tech", label: "기술" },
                                { value: "design", label: "디자인" },
                                { value: "business", label: "비즈니스" },
                                { value: "marketing", label: "마케팅" }
                            ]
                        },
                        {
                            key: "newsletter",
                            type: "checkbox",
                            label: "뉴스레터 구독",
                            checkboxLabel: "뉴스레터 구독에 동의합니다"
                        },
                        {
                            key: "comments",
                            type: "textarea",
                            label: "추가 의견",
                            placeholder: "의견이 있으시면 자유롭게 작성해주세요",
                            rows: 3
                        }
                    ],
                    settings: {
                        submitUrl: "http://localhost:8500/sandbox/form-creator",
                        method: "POST"
                    }
                },
                registration: {
                    name: "회원가입 폼",
                    description: "검증 규칙이 포함된 회원가입 폼입니다",
                    components: [
                        {
                            key: "username",
                            type: "text",
                            label: "사용자명",
                            placeholder: "사용자명을 입력해주세요",
                            required: true,
                            helpText: "영문, 숫자 조합으로 4-20자"
                        },
                        {
                            key: "email",
                            type: "email",
                            label: "이메일",
                            placeholder: "email@example.com",
                            required: true
                        },
                        {
                            key: "password",
                            type: "password",
                            label: "비밀번호",
                            placeholder: "비밀번호를 입력해주세요",
                            required: true,
                            helpText: "8자 이상, 영문+숫자+특수문자 조합"
                        },
                        {
                            key: "terms",
                            type: "checkbox",
                            label: "이용약관 동의",
                            checkboxLabel: "이용약관에 동의합니다",
                            required: true
                        }
                    ],
                    settings: {
                        submitUrl: "http://localhost:8500/sandbox/form-creator",
                        method: "POST"
                    }
                },
                tire_factory_survey: {
                    name: "타이어 공장 고객 수요 조사 (2025년 시장 데이터 기반)",
                    description: "2025년 자동차 산업 동향을 반영한 타이어 제조업체 설립을 위한 전문 시장 조사. 현대기아 746만대 생산계획, EV 타이어 시장 22.1% 성장률, 전체 타이어 시장 $256.1B 규모 반영",
                    components: [
                        {
                            key: "company_name",
                            type: "text",
                            label: "회사명/사업자명",
                            placeholder: "예: OO자동차, OO정비센터, 개인사업자",
                            required: true,
                            helpText: "타이어를 구매하는 주체명을 입력해주세요"
                        },
                        {
                            key: "business_type",
                            type: "select",
                            label: "업종 분류",
                            required: true,
                            options: [
                                { value: "automotive_manufacturer", label: "자동차 제조업 (현대차, 기아 등)" },
                                { value: "automotive_dealer", label: "자동차 딜러/판매업" },
                                { value: "service_center", label: "정비업/서비스센터" },
                                { value: "transportation", label: "운수업/물류업" },
                                { value: "rental", label: "렌터카/리스업" },
                                { value: "fleet_management", label: "차량관리업" },
                                { value: "government", label: "정부/공공기관" },
                                { value: "other", label: "기타" }
                            ],
                            helpText: "주요 사업 분야를 선택해주세요"
                        },
                        {
                            key: "annual_revenue",
                            type: "select",
                            label: "연간 매출 규모",
                            required: true,
                            options: [
                                { value: "under_1b", label: "10억원 미만" },
                                { value: "1b_5b", label: "10억원~50억원" },
                                { value: "5b_10b", label: "50억원~100억원" },
                                { value: "10b_50b", label: "100억원~500억원" },
                                { value: "over_50b", label: "500억원 이상" }
                            ]
                        },
                        {
                            key: "customer_type",
                            type: "radio",
                            label: "주요 고객층",
                            required: true,
                            options: [
                                { value: "b2b", label: "B2B (기업고객 위주)" },
                                { value: "b2c", label: "B2C (개인고객 위주)" },
                                { value: "b2g", label: "B2G (정부/공공기관 위주)" },
                                { value: "mixed", label: "혼합 (B2B+B2C)" }
                            ]
                        },
                        {
                            key: "monthly_tire_volume",
                            type: "select",
                            label: "월간 타이어 구매량 (2025년 기준)",
                            required: true,
                            options: [
                                { value: "under_100", label: "100개 미만" },
                                { value: "100_500", label: "100개~500개" },
                                { value: "500_1000", label: "500개~1,000개" },
                                { value: "1000_5000", label: "1,000개~5,000개" },
                                { value: "5000_10000", label: "5,000개~10,000개" },
                                { value: "over_10000", label: "10,000개 이상" }
                            ],
                            helpText: "승용차, 상용차, 특수차량 타이어 모두 포함"
                        },
                        {
                            key: "current_suppliers",
                            type: "select",
                            label: "현재 주요 타이어 공급업체",
                            required: true,
                            options: [
                                { value: "hankook", label: "한국타이어테크놀로지" },
                                { value: "kumho", label: "금호타이어" },
                                { value: "nexen", label: "넥센타이어" },
                                { value: "bridgestone", label: "브리지스톤(일본)" },
                                { value: "michelin", label: "미쉐린(프랑스)" },
                                { value: "continental", label: "컨티넨탈(독일)" },
                                { value: "pirelli", label: "피렐리(이탈리아)" },
                                { value: "chinese_brands", label: "중국 브랜드 (링롱, 트라이앵글 등)" },
                                { value: "mixed_suppliers", label: "여러 브랜드 혼합 사용" },
                                { value: "other", label: "기타" }
                            ]
                        },
                        {
                            key: "average_price_range",
                            type: "select",
                            label: "평균 타이어 구매 단가 (개당)",
                            required: true,
                            options: [
                                { value: "under_50k", label: "5만원 미만 (저가형)" },
                                { value: "50k_100k", label: "5만원~10만원 (중저가형)" },
                                { value: "100k_200k", label: "10만원~20만원 (중가형)" },
                                { value: "200k_300k", label: "20만원~30만원 (고가형)" },
                                { value: "over_300k", label: "30만원 이상 (프리미엄)" }
                            ],
                            helpText: "승용차 기준, VAT 포함 가격"
                        },
                        {
                            key: "ev_transition_plan",
                            type: "select",
                            label: "2025년 EV 타이어 수요 예상 비중",
                            required: true,
                            options: [
                                { value: "no_ev", label: "EV 타이어 수요 없음 (0%)" },
                                { value: "under_10", label: "10% 미만" },
                                { value: "10_25", label: "10%~25%" },
                                { value: "25_50", label: "25%~50%" },
                                { value: "50_75", label: "50%~75%" },
                                { value: "over_75", label: "75% 이상" }
                            ],
                            helpText: "중국 EV 판매비중 60% 달성 예상, 글로벌 EV 타이어 시장 22.1% 성장 전망"
                        },
                        {
                            key: "ev_tire_requirements",
                            type: "radio",
                            label: "EV 타이어 특수 요구사항 중 가장 중요한 요소",
                            required: true,
                            options: [
                                { value: "low_rolling_resistance", label: "낮은 회전저항 (연비 향상)" },
                                { value: "high_load_capacity", label: "높은 하중 지지력 (배터리 무게)" },
                                { value: "noise_reduction", label: "소음 저감 (조용한 주행)" },
                                { value: "durability", label: "내구성 (잦은 가속/감속)" },
                                { value: "smart_features", label: "스마트 기능 (IoT, 센서 등)" }
                            ],
                            helpText: "EV는 기존 내연기관 차량과 다른 타이어 특성이 필요합니다"
                        },
                        {
                            key: "smart_tire_interest",
                            type: "radio",
                            label: "스마트 타이어 기술 도입 관심도",
                            required: true,
                            options: [
                                { value: "very_interested", label: "매우 관심 있음 (즉시 도입 희망)" },
                                { value: "interested", label: "관심 있음 (1년 내 도입 검토)" },
                                { value: "neutral", label: "보통 (시장 성숙 후 검토)" },
                                { value: "not_interested", label: "관심 없음 (기존 타이어로 충분)" }
                            ],
                            helpText: "2025년 Goodyear SightLine 등 스마트 타이어 기술 상용화 예정"
                        },
                        {
                            key: "growth_forecast_2025",
                            type: "select",
                            label: "2025년 예상 타이어 구매량 증가율",
                            required: true,
                            options: [
                                { value: "decrease", label: "감소 예상 (-10% 이상)" },
                                { value: "maintain", label: "현상 유지 (±5% 이내)" },
                                { value: "slight_increase", label: "소폭 증가 (5%~15%)" },
                                { value: "moderate_increase", label: "중간 증가 (15%~30%)" },
                                { value: "high_increase", label: "대폭 증가 (30% 이상)" }
                            ],
                            helpText: "현대기아 746만대 생산계획, 전체 타이어 시장 6.3% 성장 전망"
                        },
                        {
                            key: "supplier_selection_criteria",
                            type: "radio",
                            label: "신규 타이어 공급업체 선정 시 최우선 기준",
                            required: true,
                            options: [
                                { value: "price", label: "가격 경쟁력" },
                                { value: "quality", label: "품질 및 성능" },
                                { value: "technology", label: "기술력 및 혁신" },
                                { value: "service", label: "서비스 및 지원" },
                                { value: "delivery", label: "공급 안정성" },
                                { value: "sustainability", label: "친환경성" }
                            ]
                        },
                        {
                            key: "contract_duration_preference",
                            type: "select",
                            label: "장기 공급계약 선호 기간",
                            required: true,
                            options: [
                                { value: "spot", label: "스팟 거래 (건별 계약)" },
                                { value: "1_year", label: "1년 계약" },
                                { value: "2_3_years", label: "2~3년 계약" },
                                { value: "3_5_years", label: "3~5년 계약" },
                                { value: "over_5_years", label: "5년 이상 장기계약" }
                            ]
                        },
                        {
                            key: "sustainability_importance",
                            type: "radio",
                            label: "친환경 및 지속가능성 중요도",
                            required: true,
                            options: [
                                { value: "very_important", label: "매우 중요 (구매 결정 주요 요소)" },
                                { value: "important", label: "중요 (동일 조건시 우선 고려)" },
                                { value: "somewhat_important", label: "어느 정도 중요 (부가적 고려사항)" },
                                { value: "not_important", label: "중요하지 않음 (가격/성능이 우선)" }
                            ],
                            helpText: "재활용 소재 사용, 탄소중립 등 지속가능성 트렌드 반영"
                        },
                        {
                            key: "recycled_tire_acceptance",
                            type: "radio",
                            label: "재활용 소재 타이어 사용 의향",
                            required: true,
                            options: [
                                { value: "fully_accept", label: "적극 수용 (같은 성능이면 선호)" },
                                { value: "accept_with_discount", label: "가격 할인시 수용" },
                                { value: "accept_partially", label: "일부 용도에만 수용" },
                                { value: "not_accept", label: "수용하지 않음" }
                            ]
                        },
                        {
                            key: "additional_requirements",
                            type: "textarea",
                            label: "추가 요구사항 및 의견",
                            placeholder: "타이어 공급업체에게 바라는 점, 특별한 요구사항, 시장 전망에 대한 의견 등을 자유롭게 작성해주세요",
                            rows: 4,
                            helpText: "귀하의 의견은 타이어 공장 설립 계획 수립에 소중한 자료로 활용됩니다"
                        }
                    ],
                    settings: {
                        submitUrl: "/api/sandbox/tire-factory-survey",
                        method: "POST"
                    }
                }
            };

            if (samples[type]) {
                this.currentForm = samples[type];
                this.jsonInput = JSON.stringify(samples[type], null, 2);
                this.initFormData();
            }
        },

        // JSON 폼 로드
        loadJsonForm() {
            if (!this.isValidJson) return;

            try {
                this.currentForm = JSON.parse(this.jsonInput);
                this.initFormData();
            } catch (error) {
                console.error('JSON 파싱 오류:', error);
            }
        },

        // 폼 데이터 초기화
        initFormData() {
            this.formData = {};
            this.currentForm.components.forEach(component => {
                this.formData[component.key] = component.type === 'checkbox' ? false : '';
            });
        },

        // 폼 재설정
        resetForm() {
            this.initFormData();
        },

        // 폼 미리보기
        previewForm() {
            alert('미리보기 기능: 현재 폼 구성\n' + JSON.stringify(this.currentForm, null, 2));
        },

        // 폼 제출
        async submitForm() {
            this.isSubmitting = true;

            try {
                const submitUrl = '/api/sandbox/form-submission/save';

                const response = await fetch(submitUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        formName: this.currentForm.name,
                        formData: this.formData,
                        timestamp: new Date().toISOString()
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        this.submitResult = {
                            success: true,
                            data: result.data,
                            message: result.message || '성공적으로 제출되었습니다'
                        };
                        console.log('폼 제출 완료:', result.data);

                        // 폼 리셋
                        this.resetForm();
                    } else {
                        throw new Error(result.message || '서버에서 오류가 발생했습니다');
                    }
                } else {
                    const errorText = await response.text();
                    throw new Error(`HTTP ${response.status}: ${errorText}`);
                }
            } catch (error) {
                this.submitResult = {
                    success: false,
                    data: null,
                    message: error.message
                };
                console.error('폼 제출 오류:', error);
            }

            this.isSubmitting = false;
            this.showResultModal = true;
        }
    }
}
</script>

<!-- Alpine.js 스크립트 -->
<!-- Alpine.js provided by Livewire - CDN removed to prevent conflicts -->
