<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    auth: Object,
});

onMounted(() => {
    router.post(route('verification.send'));
});

const resendEmail = () => {
    router.post(route('verification.send'));
};
</script>

<template>
    <Head title="이메일 인증 - Hostyle" />

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
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    .animate-pulse-slow {
        animation: pulse 2s infinite;
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
                    <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-white/30 to-white/10 backdrop-blur-md rounded-3xl flex items-center justify-center shadow-2xl border border-white/40 relative overflow-hidden animate-pulse-slow">
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

                <!-- 이메일 인증 안내 -->
                <div class="text-center space-y-6">
                    <!-- 이메일 아이콘 -->
                    <div class="w-20 h-20 mx-auto bg-gradient-to-r from-green-500/20 to-emerald-500/20 rounded-full flex items-center justify-center border border-green-400/30">
                        <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>

                    <!-- 제목 -->
                <div>
                        <h2 class="text-2xl font-bold text-white mb-2">이메일 인증이 필요합니다</h2>
                        <p class="text-purple-200 text-sm">회원가입이 완료되었습니다!</p>
                    </div>

                    <!-- 설명 -->
                    <div class="bg-white/5 backdrop-blur-sm rounded-xl p-6 border border-white/10">
                        <p class="text-purple-200 text-sm leading-relaxed mb-4">
                            입력하신 이메일 주소로 인증 링크를 발송했습니다.<br>
                            이메일을 확인하여 계정을 활성화해주세요.
                        </p>
                        
                        <div class="flex items-center justify-center space-x-2 text-yellow-300 text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span>인증 링크는 60분 후에 만료됩니다</span>
                        </div>
                    </div>

                    <!-- 버튼들 -->
                    <div class="space-y-4">
                        <button 
                            @click="resendEmail"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-lg text-sm font-medium text-white bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300 transform hover:scale-105 hover:shadow-xl active:scale-95"
                    >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            인증 이메일 다시 보내기
                        </button>

                    <Link
                            :href="route('login')" 
                            class="w-full flex justify-center py-3 px-4 border border-white/20 rounded-lg text-sm font-medium text-white hover:bg-white/10 transition-all duration-300"
                    >
                            로그인 페이지로 돌아가기
                    </Link>
                    </div>

                    <!-- 추가 안내 -->
                    <div class="text-center">
                        <p class="text-xs text-purple-300">
                            이메일이 보이지 않나요?<br>
                            스팸 폴더를 확인해보세요.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
