<template>
    <div class="bg-white/10 backdrop-blur-xl border-b border-white/20 relative overflow-visible z-[9999]">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-3">
                <!-- 왼쪽: HOSTYLE 로고 -->
                <div class="flex items-center space-x-3 flex-shrink-0">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-blue-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">HOSTYLE</h1>
                        <p class="text-xs text-white/60">Cloud Hosting Platform</p>
                    </div>
                </div>

                <!-- 중앙: 서버 드롭다운 -->
                <div class="flex-1 flex justify-center px-4">
                    <div class="relative dropdown-container max-w-lg w-full">
                        <button 
                            ref="serverDropdownBtn"
                            @click="toggleServerDropdown"
                            class="flex items-center space-x-3 px-4 py-3 bg-white/10 hover:bg-white/20 rounded-xl text-white/90 hover:text-white transition-all duration-200 border border-white/20 hover:border-white/30 w-full max-w-[400px]"
                        >
                            <!-- 서버 아이콘 -->
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                                </svg>
                            </div>
                            
                            <!-- 서버 정보 -->
                            <div class="text-left flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-1">
                                    <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                                    <span class="text-xs text-blue-400 font-medium">현재 서버</span>
                                </div>
                                <div class="font-semibold text-white text-sm truncate">{{ server.site_name }}</div>
                                <div class="text-xs text-white/60 truncate">{{ server.domain }}</div>
                            </div>
                            
                            <!-- 드롭다운 화살표 -->
                            <svg 
                                class="w-5 h-5 transition-transform duration-200 flex-shrink-0" 
                                :class="{ 'rotate-180': showServerDropdown }"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- 서버 드롭다운 메뉴 -->
                        <div 
                            ref="serverDropdownMenu"
                            v-show="showServerDropdown" 
                            class="absolute top-full left-0 mt-2 border border-white/20 rounded-xl shadow-2xl z-[999999] w-full max-w-[400px]"
                            style="background: rgba(30, 41, 59, 0.8) !important; backdrop-filter: blur(20px) !important; -webkit-backdrop-filter: blur(20px) !important;"
                        >
                            <!-- 드롭다운 헤더 -->
                            <div class="px-4 py-2 border-b border-white/20">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-white">내 서버 목록</span>
                                    <span class="text-xs text-white/50">({{ allServers.length }}개)</span>
                                </div>
                            </div>
                            
                            <div class="p-2">
                                <!-- 현재 서버 정보 표시 -->
                                <div class="px-3 py-2 mb-2 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-lg border border-blue-500/30">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                                        <span class="text-xs text-blue-400 font-medium">현재 서버</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-7 h-7 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                                                </svg>
                                            </div>
                                            <div class="text-left min-w-0 flex-1">
                                                <div class="text-white font-semibold text-sm truncate">{{ server.site_name }}</div>
                                                <div class="text-xs text-white/60 truncate">{{ server.domain }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <div class="text-xs text-white/70">{{ server.platform }}</div>
                                            <div class="text-xs" :class="server.days_remaining <= 30 ? 'text-red-400' : 'text-white/60'">
                                                {{ server.days_remaining > 0 ? Math.floor(server.days_remaining) + '일' : '만료' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- 다른 서버들 -->
                                <div v-if="allServers.filter(s => s.id !== server.id).length > 0" class="mb-2">
                                    <div class="text-xs text-white/50 mb-1 px-1">다른 서버들</div>
                                    <div v-for="s in allServers.filter(s => s.id !== server.id)" :key="s.id" class="mb-1">
                                        <a 
                                            :href="route('server.show', { id: s.id })"
                                            class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-white/10 transition-all duration-200 group"
                                        >
                                            <div class="flex items-center space-x-3">
                                                <div class="w-2 h-2 bg-white/40 rounded-full group-hover:bg-white/60 transition-colors"></div>
                                                <div class="w-7 h-7 bg-white/10 rounded-lg flex items-center justify-center group-hover:bg-white/20 transition-colors flex-shrink-0">
                                                    <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                                                    </svg>
                                                </div>
                                                <div class="text-left min-w-0 flex-1">
                                                    <div class="text-white font-medium text-sm group-hover:text-white transition-colors truncate">{{ s.site_name }}</div>
                                                    <div class="text-xs text-white/60 truncate">{{ s.domain }}</div>
                                                </div>
                                            </div>
                                            <div class="text-right flex-shrink-0">
                                                <div class="text-xs text-white/70">{{ s.platform }}</div>
                                                <div class="text-xs" :class="s.days_remaining <= 30 ? 'text-red-400' : 'text-white/60'">
                                                    {{ s.days_remaining > 0 ? Math.floor(s.days_remaining) + '일' : '만료' }}
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- 새 서버 생성 -->
                                <div class="border-t border-white/20 pt-2">
                                    <Link 
                                        :href="route('server.create')"
                                        class="flex items-center px-3 py-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 group"
                                    >
                                        <div class="w-7 h-7 bg-green-500/20 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-500/30 transition-colors flex-shrink-0">
                                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                        <span class="font-medium text-sm">새 서버 생성</span>
                                    </Link>
                                </div>
                            </div>
                        </div>
                        <!-- 오버레이: 드롭다운이 열렸을 때만 드롭다운 바로 아래에 위치 -->
                        <div v-if="showServerDropdown" class="fixed left-1/2" style="transform:translateX(-50%); width:400px; top:110px; height:calc(100vh - 110px); z-index:99998; pointer-events:auto;" @click="showServerDropdown = false"></div>
                    </div>
                </div>

                <!-- 오른쪽: 가이드 & 계정 설정 -->
                <div class="flex items-center space-x-3 flex-shrink-0">
                    <!-- 관리자 페이지 링크 -->
                    <template v-if="$page.props.auth && $page.props.auth.user && $page.props.auth.user.is_admin">
                        <a href="/admin/users" class="flex items-center space-x-2 px-3 py-2 bg-gradient-to-r from-pink-500 to-purple-500 text-white rounded-lg font-semibold shadow hover:from-pink-600 hover:to-purple-600 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v4a1 1 0 001 1h3m10-5h3a1 1 0 011 1v4a1 1 0 01-1 1h-3m-10 4h10m-10 4h10"></path>
                            </svg>
                            <span>관리자 페이지</span>
                        </a>
                    </template>
                    <!-- 서버 정보 표시 -->
                    <div class="text-right">
                        <div class="text-sm text-white/70">{{ server.days_remaining > 0 ? Math.floor(server.days_remaining) + '일 남음' : '만료됨' }}</div>
                        <div class="text-xs bg-green-500/20 text-green-400 px-2 py-1 rounded-full">{{ server.plan }} 플랜</div>
                    </div>

                    <!-- 가이드 링크 -->
                    <a 
                        href="https://docs.hostyle.com" 
                        target="_blank"
                        class="flex items-center space-x-2 px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-white/90 hover:text-white transition-all duration-200"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>가이드</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>

                    <!-- 계정 설정 드롭다운 -->
                    <div class="relative dropdown-container">
                        <button 
                            ref="accountDropdownBtn"
                            @click="toggleAccountDropdown"
                            class="flex items-center space-x-2 px-3 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-white/90 hover:text-white transition-all duration-200"
                        >
                            <div class="w-7 h-7 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <svg 
                                class="w-4 h-4 transition-transform duration-200" 
                                :class="{ 'rotate-180': showAccountDropdown }"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- 계정 드롭다운 메뉴 -->
                        <div 
                            ref="accountDropdownMenu"
                            v-show="showAccountDropdown" 
                            class="absolute top-full right-0 mt-2 w-48 border border-white/20 rounded-xl shadow-2xl z-[999999]"
                             style="background: rgba(30, 41, 59, 0.8) !important; backdrop-filter: blur(20px) !important; -webkit-backdrop-filter: blur(20px) !important;">
                            <div class="p-2">
                                <Link 
                                    :href="route('profile.show')"
                                    class="flex items-center px-3 py-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    내 계정
                                </Link>
                                <Link 
                                    :href="route('server.select')"
                                    class="flex items-center px-3 py-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                    </svg>
                                    서버 목록
                                </Link>
                                <div class="border-t border-white/20 my-1"></div>
                                <form @submit.prevent="logout" class="w-full">
                                    <button 
                                        type="submit"
                                        class="w-full flex items-center px-3 py-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        로그아웃
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const emit = defineEmits(['dropdown-toggle']);

const props = defineProps({
    server: {
        type: Object,
        required: true
    },
    allServers: {
        type: Array,
        default: () => []
    }
});

const showServerDropdown = ref(false);
const showAccountDropdown = ref(false);

// ref 추가
const serverDropdownBtn = ref(null);
const serverDropdownMenu = ref(null);
const accountDropdownBtn = ref(null);
const accountDropdownMenu = ref(null);

// 드롭다운 상태 변경을 부모에게 전달
watch([showServerDropdown, showAccountDropdown], ([serverOpen, accountOpen]) => {
    emit('dropdown-toggle', serverOpen || accountOpen);
});

const toggleServerDropdown = () => {
    showServerDropdown.value = !showServerDropdown.value;
    if (showServerDropdown.value) {
        showAccountDropdown.value = false;
    }
};

const toggleAccountDropdown = () => {
    showAccountDropdown.value = !showAccountDropdown.value;
    if (showAccountDropdown.value) {
        showServerDropdown.value = false;
    }
};

const logout = () => {
    router.post(route('logout'));
};

// 외부 클릭 시 드롭다운 닫기 (버튼/메뉴 모두 제외)
const handleClickOutside = (event) => {
    // 서버 드롭다운
    if (
        showServerDropdown.value &&
        !serverDropdownBtn.value.contains(event.target) &&
        !serverDropdownMenu.value.contains(event.target)
    ) {
        showServerDropdown.value = false;
    }
    // 계정 드롭다운
    if (
        showAccountDropdown.value &&
        !accountDropdownBtn.value.contains(event.target) &&
        !accountDropdownMenu.value.contains(event.target)
    ) {
        showAccountDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script> 