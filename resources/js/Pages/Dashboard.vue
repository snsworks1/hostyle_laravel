<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    server: {
        type: Object,
        default: null
    },
    hasServers: {
        type: Boolean,
        default: false
    }
});
</script>

<template>
    <Head title="대시보드" />

    <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
        <!-- 헤더 -->
        <div class="bg-white/10 backdrop-blur-xl border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-2xl font-bold text-white">HOSTYLE</h1>
                        <span class="text-white/70">대시보드</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link 
                            :href="route('server.select')" 
                            class="text-white/70 hover:text-white transition-colors"
                        >
                            서버 목록
                        </Link>
                        <Link 
                            :href="route('server.create')" 
                            class="bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300 transform hover:scale-105"
                        >
                            + 새 서버
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- 메인 컨텐츠 -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- 서버가 없을 때 -->
            <div v-if="!hasServers" class="text-center py-12">
                <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-12 max-w-2xl mx-auto">
                    <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-8">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-4">첫 번째 서버를 생성해보세요!</h2>
                    <p class="text-xl text-white/70 mb-8">웹사이트를 시작하기 위해 서버를 생성하고 설정해보세요</p>
                    <Link 
                        :href="route('server.create')" 
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white font-bold rounded-lg transition-all duration-300 transform hover:scale-105"
                    >
                        서버 생성하기
                    </Link>
                </div>
            </div>

            <!-- 서버가 있을 때 -->
            <div v-else-if="server" class="space-y-8">
                <!-- 서버 정보 헤더 -->
                <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">{{ server.name }}</h2>
                                <p class="text-white/70">{{ server.domain }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-white font-medium">{{ server.platform }}</div>
                            <div class="text-white/50 text-sm">{{ server.plan }}</div>
                        </div>
                    </div>
                </div>

                <!-- 빠른 액션 -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6 hover:bg-white/20 transition-all duration-300 cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-bold">cPanel</div>
                                <div class="text-white/50 text-sm">관리 패널</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6 hover:bg-white/20 transition-all duration-300 cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-blue-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-bold">파일 관리</div>
                                <div class="text-white/50 text-sm">웹 파일</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6 hover:bg-white/20 transition-all duration-300 cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-bold">데이터베이스</div>
                                <div class="text-white/50 text-sm">MySQL 관리</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6 hover:bg-white/20 transition-all duration-300 cursor-pointer">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-500 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-bold">통계</div>
                                <div class="text-white/50 text-sm">사용량 현황</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 서버 상태 및 정보 -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- 서버 상태 -->
                    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6">
                        <h3 class="text-xl font-bold text-white mb-4">서버 상태</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-white/70">상태</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                    <span class="text-white font-medium">정상</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-white/70">CPU 사용률</span>
                                <span class="text-white">12%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-white/70">메모리 사용률</span>
                                <span class="text-white">45%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-white/70">디스크 사용률</span>
                                <span class="text-white">23%</span>
                            </div>
                        </div>
                    </div>

                    <!-- 도메인 정보 -->
                    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6">
                        <h3 class="text-xl font-bold text-white mb-4">도메인 정보</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-white/70">메인 도메인</span>
                                <span class="text-white">{{ server.domain }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-white/70">SSL 상태</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                    <span class="text-white">활성화</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-white/70">리전</span>
                                <span class="text-white">{{ server.region }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-white/70">생성일</span>
                                <span class="text-white">{{ server.created_at }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
