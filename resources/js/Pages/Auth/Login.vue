<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
    canResetPassword: Boolean,
    status: String,
    flash: Object,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

// 모달 상태
const showPrivacyModal = ref(false);
const showTermsModal = ref(false);

// 비밀번호 표시/숨김 상태
const showPassword = ref(false);

// 회원가입 성공 메시지 상태
const registrationSuccess = ref(false);
const successMessage = ref('');

onMounted(() => {
    // URL 파라미터에서 회원가입 성공 메시지 확인
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('registered') === 'true') {
        registrationSuccess.value = true;
        successMessage.value = urlParams.get('message') || '회원가입이 완료되었습니다! 로그인해주세요.';
        // 5초 후 메시지 자동 숨김
        setTimeout(() => {
            registrationSuccess.value = false;
        }, 5000);
    }
    // 비밀번호 재설정 등 status 메시지 쿼리 파라미터로 전달 시 표시
    if (urlParams.get('status')) {
        successMessage.value = urlParams.get('status');
        registrationSuccess.value = true;
        setTimeout(() => {
            registrationSuccess.value = false;
        }, 5000);
    }
});

const submit = () => {
    // 커스텀 검증
    if (!form.email || !form.email.includes('@')) {
        form.setError('email', '이메일 주소에 @를 포함해 주세요.');
        return;
    }
    
    if (!form.password || form.password.length < 6) {
        form.setError('password', '비밀번호는 최소 6자 이상이어야 합니다.');
        return;
    }

    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Hostyle - 로그인" />
    
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
        
        <!-- 움직이는 배경 원들 -->
        <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-purple-500/30 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-indigo-500/25 rounded-full blur-3xl animate-pulse delay-500"></div>

        <!-- 메인 카드 -->
        <div class="relative z-10 w-full max-w-md mx-4 animate-fade-in">
            <div class="bg-white/10 backdrop-blur-lg rounded-2xl shadow-2xl border border-white/20 p-8">
                <!-- 로고 영역 -->
                <div class="text-center mb-8">
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-white/30 to-white/10 backdrop-blur-md rounded-3xl flex items-center justify-center shadow-2xl border border-white/40 relative overflow-hidden">
                        <!-- 내부 그라데이션 효과 -->
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-400/20 to-blue-400/20"></div>
                        <!-- 로고 아이콘 -->
                        <div class="relative z-10 w-14 h-14 bg-gradient-to-br from-purple-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-2xl tracking-wider">H</span>
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold text-white mb-3 tracking-wide">HOSTYLE</h1>
                    <p class="text-purple-200 text-sm leading-relaxed max-w-xs mx-auto">카페, 병원, 소상공인을 위한<br>쉽고 빠른 홈페이지 생성 플랫폼</p>
                </div>

                <!-- 상태 메시지 (회원가입/이메일 전송 등 모든 성공 메시지) -->
                <div v-if="props.flash && props.flash.success" class="mb-6 p-4 bg-green-500/20 border border-green-400/30 rounded-xl text-green-200 text-center animate-fade-in">
                    <div class="flex items-center justify-center space-x-2 mb-2">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="font-semibold text-green-300">안내</span>
                    </div>
                    <p class="text-sm">{{ props.flash.success }}</p>
                </div>

                <!-- 로그인 폼 -->
                <form @submit.prevent="submit" class="space-y-6">
            <div>
                        <label for="email" class="block text-sm font-medium text-purple-200 mb-2">이메일</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input 
                    id="email"
                    v-model="form.email"
                                type="text" 
                                class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-purple-300 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent backdrop-blur-sm transition-all duration-300 transform focus:scale-[1.02]" 
                                placeholder="your@email.com"
                    autocomplete="username"
                            >
                        </div>
                        <div v-if="form.errors.email" class="mt-3 p-3 bg-red-500/10 border border-red-400/30 rounded-lg text-red-200 text-sm flex items-start space-x-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ form.errors.email }}</span>
                        </div>
            </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-purple-200 mb-2">비밀번호</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input 
                    id="password"
                    v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                class="w-full pl-10 pr-12 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-purple-300 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent backdrop-blur-sm transition-all duration-300 transform focus:scale-[1.02]" 
                                placeholder="••••••••"
                    autocomplete="current-password"
                            >
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-purple-300 hover:text-white transition-colors"
                            >
                                <svg v-if="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                                <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                        <div v-if="form.errors.password" class="mt-3 p-3 bg-red-500/10 border border-red-400/30 rounded-lg text-red-200 text-sm flex items-start space-x-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ form.errors.password }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                v-model="form.remember"
                                type="checkbox" 
                                class="h-4 w-4 text-purple-400 focus:ring-purple-400 border-white/30 rounded bg-white/10"
                            >
                            <label for="remember" class="ml-2 block text-sm text-purple-200">
                                로그인 상태 유지
                            </label>
                        </div>
                        <div class="text-sm">
                            <Link 
                                v-if="props.canResetPassword" 
                                :href="route('password.request')" 
                                class="inline-flex items-center space-x-1 text-purple-300 hover:text-white transition-colors font-medium underline decoration-purple-400/50 hover:decoration-white/70 hover:scale-105 transform duration-200"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                <span>비밀번호 찾기</span>
                            </Link>
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-105 hover:shadow-xl active:scale-95"
                    >
                        <span v-if="form.processing" class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        로그인
                    </button>

                    <div class="text-center">
                        <p class="text-sm text-purple-200">
                            계정이 없으신가요? 
                            <Link :href="route('register')" class="font-medium text-purple-300 hover:text-white transition-colors">
                                회원가입
                            </Link>
                        </p>
                    </div>
                </form>

                <!-- 하단 링크들 -->
                <div class="mt-8 pt-6 border-t border-white/20">
                    <div class="flex justify-center space-x-6 text-xs text-purple-300">
                        <button @click="showPrivacyModal = true" class="hover:text-white transition-colors">개인정보 처리방침</button>
                        <button @click="showTermsModal = true" class="hover:text-white transition-colors">서비스 이용약관</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 개인정보 처리방침 모달 -->
        <div v-if="showPrivacyModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click="showPrivacyModal = false">
            <div class="bg-white rounded-xl w-full max-w-2xl max-h-[80vh] overflow-y-auto" @click.stop>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">개인정보 처리방침</h2>
                        <button @click="showPrivacyModal = false" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
            </div>

                    <div class="text-sm text-gray-700 leading-relaxed space-y-4">
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
        </div>

        <!-- 서비스 이용약관 모달 -->
        <div v-if="showTermsModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" @click="showTermsModal = false">
            <div class="bg-white rounded-xl w-full max-w-2xl max-h-[80vh] overflow-y-auto" @click.stop>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-gray-800">서비스 이용약관</h2>
                        <button @click="showTermsModal = false" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
            </div>

                    <div class="text-sm text-gray-700 leading-relaxed space-y-4">
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
    </div>
</template>
