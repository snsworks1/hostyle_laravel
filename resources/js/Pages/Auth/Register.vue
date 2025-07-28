<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    marketing_consent: false,
    password: '',
    password_confirmation: '',
    terms: false,
});

// 모달 상태
const showPrivacyModal = ref(false);
const showTermsModal = ref(false);

// 비밀번호 표시/숨김 상태
const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

// 비밀번호 복잡도 검증
const passwordRequirements = ref({
    length: false,
    letter: false,
    number: false,
    special: false,
});

const validatePassword = (password) => {
    passwordRequirements.value = {
        length: password.length >= 8,
        letter: /[a-zA-Z]/.test(password),
        number: /\d/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password),
    };
};

const submit = () => {
    // 커스텀 검증
    if (!form.name.trim()) {
        form.setError('name', '이름을 입력해주세요.');
        return;
    }
    
    if (!form.email || !form.email.includes('@')) {
        form.setError('email', '유효한 이메일 주소를 입력해주세요.');
        return;
    }
    
    if (!form.phone.trim()) {
        form.setError('phone', '휴대전화 번호를 입력해주세요.');
        return;
    }
    
    if (!form.password || form.password.length < 8) {
        form.setError('password', '비밀번호는 최소 8자 이상이어야 합니다.');
        return;
    }
    
    if (!passwordRequirements.value.length || !passwordRequirements.value.letter || 
        !passwordRequirements.value.number || !passwordRequirements.value.special) {
        form.setError('password', '비밀번호는 8자 이상, 영문, 숫자, 특수문자를 포함해야 합니다.');
        return;
    }
    
    if (form.password !== form.password_confirmation) {
        form.setError('password_confirmation', '비밀번호가 일치하지 않습니다.');
        return;
    }
    
    if (!form.terms) {
        form.setError('terms', '이용약관에 동의해주세요.');
        return;
    }

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onSuccess: () => {
            // 회원가입 성공 후 서버 생성 페이지로 리다이렉트
            router.visit(route('server.create'), {
                method: 'get',
                preserveState: false,
                replace: true
            });
        },
    });
};

// 전화번호 포맷팅
const formatPhoneNumber = (value) => {
    if (!value) return '';
    
    // 숫자만 추출
    const numbers = value.replace(/[^\d]/g, '');
    
    // 11자리 제한
    const limited = numbers.slice(0, 11);
    
    // 포맷팅 (010-1234-5678)
    if (limited.length <= 3) {
        return limited;
    } else if (limited.length <= 7) {
        return `${limited.slice(0, 3)}-${limited.slice(3)}`;
    } else {
        return `${limited.slice(0, 3)}-${limited.slice(3, 7)}-${limited.slice(7)}`;
    }
};

const handlePhoneInput = (event) => {
    const formatted = formatPhoneNumber(event.target.value);
    form.phone = formatted;
};
</script>

<template>
    <Head title="회원가입" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 relative overflow-hidden">
        <!-- 돌아가기 버튼 -->
        <div class="absolute top-6 left-6 z-20">
            <Link href="/" class="flex items-center space-x-2 text-white/80 hover:text-white transition-colors duration-200 group">
                <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="text-sm font-medium">메인으로 돌아가기</span>
            </Link>
        </div>

        <!-- 배경 효과 -->
        <div class="absolute inset-0 bg-black/20"></div>

        <!-- 애니메이션 배경 원들 -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-blue-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-72 h-72 bg-indigo-500/20 rounded-full blur-3xl animate-pulse delay-2000"></div>

        <!-- 메인 카드 -->
        <div class="relative z-10 w-full max-w-md mx-4">
            <div class="bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 p-8 animate-fade-in">
                <!-- 제목 -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-600 rounded-xl mb-4 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-3">HOSTYLE - 회원가입</h2>
                    <p class="text-white/70 text-sm leading-relaxed">새로운 계정을 만들어보세요</p>
                </div>

                <!-- 회원가입 폼 -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- 이름 입력 -->
                    <div class="relative">
                        <input 
                    id="name"
                    v-model="form.name"
                    type="text"
                            class="w-full px-4 py-4 border-b-2 border-white/30 focus:border-purple-400 focus:outline-none transition-colors duration-200 text-white placeholder-transparent peer bg-transparent" 
                            placeholder="이름"
                    required
                    autofocus
                    autocomplete="name"
                        >
                        <label
                            :class="[
                                'absolute left-4 -top-3 text-purple-200 transition-all duration-200 bg-transparent px-1 pointer-events-none',
                                (form.name ? 'text-s text-purple-300 leading-none' : ''),
                                (!form.name ? 'peer-placeholder-shown:text-base peer-placeholder-shown:top-4' : '')
                            ]"
                            for="name"
                        >이름 *</label>
                        <div v-if="form.errors.name" class="mt-2 text-red-200 text-sm">{{ form.errors.name }}</div>
            </div>

                    <!-- 이메일 입력 -->
                    <div class="relative">
                        <input 
                    id="email"
                    v-model="form.email"
                    type="email"
                            class="w-full px-4 py-4 border-b-2 border-white/30 focus:border-purple-400 focus:outline-none transition-colors duration-200 text-white placeholder-transparent peer bg-transparent" 
                            placeholder="이메일"
                    required
                    autocomplete="username"
                        >
                        <label
                            :class="[
                                'absolute left-4 -top-3 text-purple-200 transition-all duration-200 bg-transparent px-1 pointer-events-none',
                                (form.email ? 'text-s text-purple-300 leading-none' : ''),
                                (!form.email ? 'peer-placeholder-shown:text-base peer-placeholder-shown:top-4' : '')
                            ]"
                            for="email"
                        >이메일 *</label>
                        <div v-if="form.errors.email" class="mt-2 text-red-200 text-sm">{{ form.errors.email }}</div>
                    </div>

                    <!-- 연락처 입력 -->
                    <div class="relative">
                        <input 
                            id="phone" 
                            v-model="form.phone"
                            type="tel" 
                            class="w-full px-4 py-4 border-b-2 border-white/30 focus:border-purple-400 focus:outline-none transition-colors duration-200 text-white placeholder-transparent peer bg-transparent" 
                            placeholder="휴대전화 번호"
                            required
                            autocomplete="tel"
                        >
                        <label
                            :class="[
                                'absolute left-4 -top-3 text-purple-200 transition-all duration-200 bg-transparent px-1 pointer-events-none',
                                (form.phone ? 'text-s text-purple-300 leading-none' : ''),
                                (!form.phone ? 'peer-placeholder-shown:text-base peer-placeholder-shown:top-4' : '')
                            ]"
                            for="phone"
                        >휴대전화 번호 *</label>
                        <div v-if="form.errors.phone" class="mt-2 text-red-200 text-sm">{{ form.errors.phone }}</div>
            </div>

                    <!-- 비밀번호 입력 -->
                    <div class="relative">
                        <input 
                    id="password"
                    v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            class="w-full px-4 py-4 border-b-2 border-white/30 focus:border-purple-400 focus:outline-none transition-colors duration-200 text-white placeholder-transparent peer bg-transparent" 
                            placeholder="비밀번호"
                    required
                    autocomplete="new-password"
                        >
                        <label
                            :class="[
                                'absolute left-4 -top-3 text-purple-200 transition-all duration-200 bg-transparent px-1 pointer-events-none',
                                (form.password ? 'text-s text-purple-300 leading-none' : ''),
                                (!form.password ? 'peer-placeholder-shown:text-base peer-placeholder-shown:top-4' : '')
                            ]"
                            for="password"
                        >비밀번호 *</label>
                        <button 
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute top-0 bottom-0 right-0 pr-3 flex items-center justify-center text-purple-300 hover:text-white transition-colors h-[59px]"
                        >
                            <svg v-if="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                        <div class="mt-2 text-xs text-purple-300">
                            8자 이상, 영문+숫자+특수문자(@$!%*?&) 포함
                        </div>
                        <div v-if="form.errors.password" class="mt-2 text-red-200 text-sm">{{ form.errors.password }}</div>
            </div>

                    <!-- 비밀번호 확인 -->
                    <div class="relative">
                        <input 
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                            :type="showPasswordConfirmation ? 'text' : 'password'"
                            class="w-full px-4 py-4 border-b-2 border-white/30 focus:border-purple-400 focus:outline-none transition-colors duration-200 text-white placeholder-transparent peer bg-transparent" 
                            placeholder="비밀번호 확인"
                    required
                    autocomplete="new-password"
                        >
                        <label
                            :class="[
                                'absolute left-4 -top-3 text-purple-200 transition-all duration-200 bg-transparent px-1 pointer-events-none',
                                (form.password_confirmation ? 'text-s text-purple-300 leading-none' : ''),
                                (!form.password_confirmation ? 'peer-placeholder-shown:text-base peer-placeholder-shown:top-4' : '')
                            ]"
                            for="password_confirmation"
                        >비밀번호 확인 *</label>
                        <button 
                            type="button"
                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                            class="absolute top-0 bottom-0 right-0 pr-3 flex items-center justify-center text-purple-300 hover:text-white transition-colors h-[59px]"
                        >
                            <svg v-if="showPasswordConfirmation" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                        <div v-if="form.errors.password_confirmation" class="mt-2 text-red-200 text-sm">{{ form.errors.password_confirmation }}</div>
                    </div>

                    <!-- 약관 동의 -->
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3">
                            <input 
                                id="terms" 
                                v-model="form.terms"
                                type="checkbox" 
                                class="h-4 w-4 text-purple-400 focus:ring-purple-400 border-white/30 rounded bg-white/10 mt-1"
                                required
                            >
                            <label for="terms" class="text-sm text-purple-200 leading-relaxed">
                                <span>다음에 동의합니다: </span>
                                <button type="button" @click="showTermsModal = true" class="text-purple-300 hover:text-white underline">서비스 이용약관 [전문보기]</button>
                                <span> 및 </span>
                                <button type="button" @click="showPrivacyModal = true" class="text-purple-300 hover:text-white underline">개인정보 처리방침 [전문보기]</button>
                            </label>
                        </div>
                        <div v-if="form.errors.terms" class="text-red-200 text-sm">{{ form.errors.terms }}</div>
                    </div>

                    <!-- 마케팅 수신 동의 -->
                    <div class="flex items-start space-x-3">
                        <input 
                            id="marketing_consent" 
                            v-model="form.marketing_consent"
                            type="checkbox" 
                            class="h-4 w-4 text-purple-400 focus:ring-purple-400 border-white/30 rounded bg-white/10 mt-1"
                        >
                        <label for="marketing_consent" class="text-sm text-purple-200 leading-relaxed">
                            이벤트 등 프로모션 알림 수신 (선택)
                        </label>
            </div>

                    <!-- 안내 문구 -->
                    <div class="text-xs text-purple-300">
                        ※ 가입정보를 잘못 기재할 경우 이용에 불이익이 발생될 수 있습니다.
                    </div>

                    <!-- 회원가입 버튼 -->
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105 hover:shadow-xl active:scale-95"
                    >
                        <div v-if="form.processing" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            처리 중...
                        </div>
                        <span v-else>회원가입</span>
                    </button>
                </form>

                <!-- 로그인 링크 -->
                <div class="mt-6 text-center">
                    <p class="text-white/70 text-sm">
                        이미 계정이 있으신가요? 
                        <Link :href="route('login')" class="text-purple-300 hover:text-white font-medium underline decoration-purple-400/50 hover:decoration-white/70 transition-colors">
                            로그인하기
                        </Link>
                    </p>
                </div>

                <!-- 하단 링크들 -->
                <div class="mt-8 pt-6 border-t border-white/20">
                    <div class="flex justify-center space-x-6 text-xs text-purple-300">
                        <button @click="showPrivacyModal = true" class="hover:text-white transition-colors">개인정보 처리방침</button>
                        <button @click="showTermsModal = true" class="hover:text-white transition-colors">서비스 이용약관</button>
                        <!-- 이메일 미리보기 링크 삭제 -->
                    </div>
                </div>
            </div>
        </div>

        <!-- 개인정보 처리방침 모달 -->
        <div v-if="showPrivacyModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showPrivacyModal = false"></div>
            <div class="relative bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 p-6 max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">개인정보 처리방침</h3>
                    <button @click="showPrivacyModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="text-gray-700 text-sm leading-relaxed space-y-4">
                    <p>에스앤에스웍스(이하 "회사")은 개인정보 보호법에 따라 이용자의 개인정보를 보호하고 권익을 보호하기 위해 다음과 같은 개인정보 처리방침을 수립·공개합니다.</p>

                    <p><strong>1. 수집하는 개인정보 항목</strong><br>
                        - 필수항목: 이메일, 비밀번호, 이름, 전화번호, 서비스 이용기록, 결제정보(가상계좌/카드 정보) 사업장주소, 프로필 정보, 회사명 , 사업자등록번호
                    </p>

                    <p><strong>2. 개인정보 수집 방법</strong><br>
                        - 회원가입 시 사용자가 직접 입력<br>
                        - 서비스 이용 중 자동 수집(Cookie, 접속 IP 등)
                    </p>

                    <p><strong>3. 개인정보 이용 목적</strong><br>
                        - 회원가입 및 본인 확인<br>
                        - 서비스 제공 및 계약 이행<br>
                        - 고객 상담, 민원 처리<br>
                        - 요금 정산 및 결제<br>
                        - 서비스 개선 및 마케팅 활용(동의 시)
                    </p>

                    <p><strong>4. 개인정보 보유 및 이용기간</strong><br>
                        - 회원 탈퇴 시 즉시 삭제<br>
                        - 관계법령에 따라 일정기간 보존되는 경우:<br>
                        · 계약 또는 청약철회 기록: 5년<br>
                        · 대금결제 및 재화 공급 기록: 5년<br>
                        · 소비자 불만 또는 분쟁처리 기록: 3년
                    </p>

                    <p><strong>5. 개인정보 제3자 제공</strong><br>
                        회사는 원칙적으로 이용자의 개인정보를 외부에 제공하지 않습니다. 단, 다음의 경우에는 예외로 합니다:<br>
                        - 이용자의 사전 동의를 받은 경우<br>
                        - 법령에 의거하거나 수사기관의 요청이 있는 경우
                    </p>

                    <p><strong>6. 개인정보 처리 위탁</strong><br>
                        회사는 서비스 제공을 위해 아래와 같이 개인정보 처리를 위탁할 수 있습니다:<br>
                        - 결제처리: 토스페이먼츠㈜<br>
                        - 이메일 발송: Amazon SES (또는 Mailgun 등)
                    </p>

                    <p><strong>7. 정보주체의 권리와 행사 방법</strong><br>
                        - 개인정보 열람, 정정, 삭제, 처리정지 요청 가능<br>
                        - 회원정보 수정 또는 탈퇴를 통해 직접 처리 가능<br>
                        - 또는 개인정보 보호책임자에게 요청 가능
                    </p>

                    <p><strong>8. 개인정보 보호책임자</strong><br>
                        · 이름: 김대현<br>
                        · 이메일: support@snsworks.co.kr<br>
                        · 연락처: 010-5914-3150
                    </p>

                    <p><strong>9. 쿠키의 설치/운영 및 거부</strong><br>
                        - 웹사이트는 맞춤형 서비스 제공을 위해 쿠키(cookie)를 사용합니다.<br>
                        - 사용자는 브라우저 설정을 통해 쿠키 저장을 거부하거나 삭제할 수 있습니다.
                    </p>

                    <p class="text-xs text-gray-500">
                        본 방침은 2025년 6월 27일부터 시행됩니다.
                    </p>
                </div>
            </div>
        </div>

        <!-- 서비스 이용약관 모달 -->
        <div v-if="showTermsModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showTermsModal = false"></div>
            <div class="relative bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 p-6 max-w-2xl w-full max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">서비스 이용약관</h3>
                    <button @click="showTermsModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="text-gray-700 text-sm leading-relaxed space-y-4">
                    <p>
                        본 약관은 귀하가 당사의 웹호스팅 SaaS 서비스를 이용함에 있어 필요한 조건, 권리 및 의무를 규정합니다.
                    </p>

                    <p>
                        <strong>제1조 (목적)</strong><br>
                        본 약관은 사용자가 본 서비스에 가입하고, cPanel를 기반으로 한 웹사이트를 생성하고 운영하는 과정에서 발생할 수 있는 기본적인 이용 조건을 규정합니다.
                    </p>

                    <p>
                        <strong>제2조 (서비스 내용)</strong><br>
                        당사는 사용자의 신청에 따라 cPanel 기반 웹사이트를 자동으로 생성하며, WHM 기반 리셀러 서버를 통해 웹호스팅 환경을 제공합니다. 서비스 구성은 선택한 요금제에 따라 상이할 수 있습니다.
                    </p>

                    <p>
                        <strong>제3조 (회원가입 및 계정)</strong><br>
                        사용자는 유효한 이메일, 비밀번호, 연락처 등의 정보를 제공하고, 정해진 절차를 통해 회원가입을 완료해야 합니다. 회원 정보는 사용자 본인의 책임 하에 관리되어야 하며, 타인에게 공유 또는 양도할 수 없습니다.
                    </p>

                    <p>
                        <strong>제4조 (서비스 이용 요금)</strong><br>
                        서비스는 유료이며, 요금제에 따라 월 단위 또는 정기 결제 방식으로 운영됩니다. 결제 금액 및 조건은 서비스 페이지에 명시된 내용을 따릅니다.
                    </p>

                    <p>
                        <strong>제5조 (계정 정지 및 해지)</strong><br>
                        다음과 같은 경우 당사는 사전 통보 없이 계정 정지 또는 해지를 할 수 있습니다:<br>
                        - 이용약관을 위반하거나 불법적인 콘텐츠를 운영한 경우<br>
                        - cPanel 외 목적의 무단 사용, 리소스 과다 사용 등<br>
                        - 저작권 침해 목적 사용<br>
                        - 기타 서비스 안정성에 심각한 영향을 줄 경우
                    </p>

                    <p>
                        <strong>제6조 (데이터 보관 및 삭제)</strong><br>
                        서비스 해지 후 7일간 데이터가 보관되며, 이후 자동 삭제됩니다. 해지 전 백업은 사용자 책임입니다.
                    </p>

                    <p>
                        <strong>제7조 (면책 조항)</strong><br>
                        당사는 아래 사유로 인한 피해에 대해 책임지지 않습니다:<br>
                        - 사용자의 과실로 인한 정보 유출 또는 손해<br>
                        - IDC/서버 장애, 천재지변, 통신망 문제 등 외부 요인<br>
                        - 사용자의 콘텐츠 관리 부주의
                    </p>

                    <p>
                        <strong>제8조 (약관 변경)</strong><br>
                        당사는 서비스 개선을 위해 약관을 변경할 수 있으며, 변경 시 웹사이트에 사전 공지합니다.
                    </p>

                    <p class="text-xs text-gray-500">
                        본 약관은 2025년 6월 기준으로 작성되었으며, 최신 버전은 서비스 페이지를 통해 확인할 수 있습니다.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.8s ease-out;
}
</style>
