{{-- 폼 제출 데모 화면 --}}
<?php
    require_once __DIR__ . "/../../../../../../bootstrap.php";
use App\Services\TemplateCommonService;


    $screenInfo = TemplateCommonService::getCurrentTemplateScreenInfo();
    $uploadPaths = TemplateCommonService::getTemplateUploadPaths();
?>
<div class="min-h-screen bg-gradient-to-br from-green-50 to-blue-100 p-6"
     x-data="formDemoData()"
     x-init="init()"
     x-cloak>

    {{-- 헤더 --}}
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-xl">📝</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">폼 제출 데모</h1>
                        <p class="text-gray-600">다양한 폼을 제출해보세요</p>
                    </div>
                </div>
                <div class="text-right">
                    <a href="../109-screen-form-history/000-content.blade.php"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        📋 제출 내역 보기
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 폼 선택 --}}
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">폼 유형 선택</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button @click="setFormType('사용자 등록')"
                    :class="currentFormType === '사용자 등록' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-3 rounded-lg font-medium transition-colors">
                👤 사용자 등록
            </button>
            <button @click="setFormType('문의하기')"
                    :class="currentFormType === '문의하기' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-3 rounded-lg font-medium transition-colors">
                💬 문의하기
            </button>
            <button @click="setFormType('피드백')"
                    :class="currentFormType === '피드백' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-3 rounded-lg font-medium transition-colors">
                ⭐ 피드백
            </button>
        </div>
    </div>

    {{-- 폼 영역 --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form @submit.prevent="submitForm()" class="space-y-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4" x-text="currentFormType + ' 폼'"></h3>

            {{-- 사용자 등록 폼 --}}
            <div x-show="currentFormType === '사용자 등록'" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">이름 *</label>
                    <input type="text" x-model="forms.userRegistration.name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">이메일 *</label>
                    <input type="email" x-model="forms.userRegistration.email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">전화번호</label>
                    <input type="tel" x-model="forms.userRegistration.phone"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">메시지</label>
                    <textarea x-model="forms.userRegistration.message" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"></textarea>
                </div>
            </div>

            {{-- 문의하기 폼 --}}
            <div x-show="currentFormType === '문의하기'" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">제목 *</label>
                    <input type="text" x-model="forms.inquiry.subject" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">우선순위</label>
                    <select x-model="forms.inquiry.priority"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <option value="낮음">낮음</option>
                        <option value="보통">보통</option>
                        <option value="높음">높음</option>
                        <option value="긴급">긴급</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">내용 *</label>
                    <textarea x-model="forms.inquiry.content" rows="5" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"></textarea>
                </div>
            </div>

            {{-- 피드백 폼 --}}
            <div x-show="currentFormType === '피드백'" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">평점 *</label>
                    <div class="flex items-center space-x-2">
                        <template x-for="star in 5" :key="star">
                            <button type="button" @click="forms.feedback.rating = star"
                                    :class="star <= forms.feedback.rating ? 'text-yellow-500' : 'text-gray-300'"
                                    class="text-2xl hover:text-yellow-400 transition-colors">
                                ⭐
                            </button>
                        </template>
                        <span class="text-sm text-gray-600 ml-2" x-text="forms.feedback.rating + '점'"></span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">카테고리</label>
                    <select x-model="forms.feedback.category"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <option value="UI/UX">UI/UX</option>
                        <option value="기능">기능</option>
                        <option value="성능">성능</option>
                        <option value="기타">기타</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">피드백 내용 *</label>
                    <textarea x-model="forms.feedback.feedback" rows="4" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" x-model="forms.feedback.recommend" id="recommend"
                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <label for="recommend" class="ml-2 block text-sm text-gray-700">
                        다른 사람에게 추천하시겠습니까?
                    </label>
                </div>
            </div>

            {{-- 제출 버튼 --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    <span x-show="submitting" class="text-blue-600">제출 중...</span>
                </div>
                <button type="submit" :disabled="submitting"
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!submitting">✉️ 제출하기</span>
                    <span x-show="submitting" class="flex items-center">
                        <div class="animate-spin -ml-1 mr-3 h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>
                        제출 중...
                    </span>
                </button>
            </div>
        </form>
    </div>

    {{-- 성공 메시지 --}}
    <div x-show="showSuccessMessage"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg"
         style="display: none;">
        <div class="flex items-center">
            <span class="mr-2">✅</span>
            <span>폼이 성공적으로 제출되었습니다!</span>
        </div>
    </div>
</div>

<script>
function formDemoData() {
    return {
        currentFormType: '사용자 등록',
        submitting: false,
        showSuccessMessage: false,

        forms: {
            userRegistration: {
                name: '',
                email: '',
                phone: '',
                message: ''
            },
            inquiry: {
                subject: '',
                priority: '보통',
                content: ''
            },
            feedback: {
                rating: 5,
                category: 'UI/UX',
                feedback: '',
                recommend: false
            }
        },

        init() {
            // 초기화 로직
        },

        setFormType(type) {
            this.currentFormType = type;
        },

        getCurrentFormData() {
            switch (this.currentFormType) {
                case '사용자 등록':
                    return this.forms.userRegistration;
                case '문의하기':
                    return this.forms.inquiry;
                case '피드백':
                    return this.forms.feedback;
                default:
                    return {};
            }
        },

        async submitForm() {
            this.submitting = true;

            try {
                const formData = this.getCurrentFormData();

                const response = await fetch('/sandbox/gupsa/101-domain-rfx/100-common/100-Controllers/FormSubmission/Controller.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        form_name: this.currentFormType,
                        form_data: formData
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        this.resetCurrentForm();
                        this.showSuccessMessage = true;
                        setTimeout(() => {
                            this.showSuccessMessage = false;
                        }, 3000);
                    } else {
                        alert('제출 실패: ' + result.message);
                    }
                } else {
                    alert('서버 오류가 발생했습니다.');
                }
            } catch (error) {
                alert('제출 중 오류가 발생했습니다: ' + error.message);
            }

            this.submitting = false;
        },

        resetCurrentForm() {
            switch (this.currentFormType) {
                case '사용자 등록':
                    this.forms.userRegistration = {
                        name: '',
                        email: '',
                        phone: '',
                        message: ''
                    };
                    break;
                case '문의하기':
                    this.forms.inquiry = {
                        subject: '',
                        priority: '보통',
                        content: ''
                    };
                    break;
                case '피드백':
                    this.forms.feedback = {
                        rating: 5,
                        category: 'UI/UX',
                        feedback: '',
                        recommend: false
                    };
                    break;
            }
        }
    }
}
</script>

<!-- Alpine.js 스크립트 -->
<!-- Alpine.js provided by Livewire - CDN removed to prevent conflicts -->