<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    status: String,
})

const form = useForm({
    email: '',
})

const submit = () => {
    form.post(route('password.email'), {
        preserveState: true
    })
}
</script>

<template>
    <Head title="비밀번호 찾기" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 relative overflow-hidden">
        <!-- 돌아가기 버튼 -->
        <div class="absolute top-6 left-6 z-20">
            <Link href="/login" class="flex items-center space-x-2 text-white/80 hover:text-white transition-colors duration-200 group">
                <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="text-sm font-medium">로그인으로 돌아가기</span>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-3">비밀번호 찾기</h2>
                    <p class="text-white/70 text-sm leading-relaxed">가입하신 이메일 주소를 입력하시면<br>비밀번호 재설정 링크를 보내드립니다.</p>
        </div>

                <!-- 폼 -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- 이메일 입력 -->
            <div>
                        <label for="email" class="block text-sm font-medium text-white/90 mb-2">이메일 주소</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                            <input
                    id="email"
                    v-model="form.email"
                    type="email"
                                class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                placeholder="your@email.com"
                    required
                />
                        </div>
                        <div v-if="form.errors.email" class="mt-2 text-red-400 text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ form.errors.email }}
                        </div>
            </div>

                    <!-- 제출 버튼 -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                    >
                        <div v-if="form.processing" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            처리 중...
                        </div>
                        <span v-else>비밀번호 재설정 링크 보내기</span>
                    </button>
                </form>

                <!-- 성공 메시지 -->
                <div v-if="props.status" class="mt-6 p-4 bg-green-500/20 border border-green-500/30 rounded-xl">
                    <div class="flex items-center text-green-400">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm">{{ props.status }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fade-in {
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
    animation: fade-in 0.6s ease-out;
}
</style>
