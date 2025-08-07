<template>
    <div class="w-64 bg-white/10 backdrop-blur-xl border-r border-white/20 min-h-screen">
        <nav class="mt-6 px-4">
            <div v-for="(menu, index) in sidebarMenus" :key="index" class="mb-4">
                <!-- 메인 메뉴 아이템 -->
                <div v-if="menu.children">
                    <button 
                        @click="toggleMenu(index)"
                        class="w-full flex items-center justify-between px-4 py-3 text-left text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 group"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="w-5 h-5 flex items-center justify-center">
                                <svg v-if="menu.icon === 'settings'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <svg v-else-if="menu.icon === 'users'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                <svg v-else-if="menu.icon === 'puzzle'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path>
                                </svg>
                                <svg v-else-if="menu.icon === 'chart-bar'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <svg v-else-if="menu.icon === 'server'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                                </svg>
                            </div>
                            <span class="font-medium">{{ menu.title }}</span>
                        </div>
                        <svg 
                            class="w-4 h-4 transition-transform duration-200" 
                            :class="{ 'rotate-180': openMenus.includes(index) }"
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <!-- 서브메뉴 -->
                    <div v-show="openMenus.includes(index)" class="ml-8 space-y-1">
                        <template v-for="submenu in menu.children" :key="submenu.title">
                            <a v-if="submenu.route && !submenu.onclick" :href="route(submenu.route, { id: serverId })" class="block px-3 py-2 text-sm text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200">
                                {{ submenu.title }}
                            </a>
                            <button v-else-if="submenu.onclick" @click="handleOnClick(submenu.onclick)" class="block px-3 py-2 text-sm text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 w-full text-left">
                                {{ submenu.title }}
                            </button>
                            <button v-else-if="submenu.children" @click.stop class="block px-3 py-2 text-sm text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 w-full text-left">
                                {{ submenu.title }}
                            </button>
                        </template>
                    </div>
                </div>
                <!-- 단일 메뉴 아이템 (route가 있으면 바로 이동) -->
                <a v-else-if="menu.route && !menu.onclick" :href="route(menu.route, { id: serverId })" class="flex items-center px-4 py-3 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200">
                    <div class="w-5 h-5 flex items-center justify-center mr-3">
                        <svg v-if="menu.icon === 'settings'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <svg v-else-if="menu.icon === 'users'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <svg v-else-if="menu.icon === 'puzzle'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path>
                        </svg>
                        <svg v-else-if="menu.icon === 'chart-bar'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <svg v-else-if="menu.icon === 'server'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                        </svg>
                    </div>
                    <span class="font-medium">{{ menu.title }}</span>
                </a>
                <!-- onclick이 있는 메뉴 아이템 -->
                <button v-else-if="menu.onclick" @click="handleOnClick(menu.onclick)" class="flex items-center px-4 py-3 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 w-full text-left">
                    <div class="w-5 h-5 flex items-center justify-center mr-3">
                        <svg v-if="menu.icon === 'settings'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <svg v-else-if="menu.icon === 'users'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <svg v-else-if="menu.icon === 'puzzle'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path>
                        </svg>
                        <svg v-else-if="menu.icon === 'chart-bar'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <svg v-else-if="menu.icon === 'server'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                        </svg>
                    </div>
                    <span class="font-medium">{{ menu.title }}</span>
                </button>
            </div>
        </nav>
        <!-- 업그레이드 안내 모달 -->
        <div v-if="showUpgradeModal" class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/40">
            <div class="bg-white rounded-xl shadow-2xl p-8 max-w-xs w-full text-center">
                <div class="text-lg font-bold mb-4 text-gray-800">플랜 업그레이드 필요</div>
                <div class="mb-6 text-gray-700">{{ upgradeMessage }}</div>
                <button @click="closeUpgradeModal" class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold">확인</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    sidebarMenus: {
        type: Array,
        default: () => []
    },
    plan: {
        type: String,
        default: ''
    },
    serverId: {
        type: [String, Number],
        required: true
    }
});

const openMenus = ref([]);
const showUpgradeModal = ref(false);
const upgradeMessage = ref('');

const planOrder = ['free', 'starter', 'business', 'enterprise'];
const canAccess = (submenu) => {
    if (!submenu.minPlan) return true;
    return planOrder.indexOf(props.plan) >= planOrder.indexOf(submenu.minPlan);
};
const handleMenuClick = (submenu, event) => {
    if (!canAccess(submenu)) {
        event.preventDefault();
        upgradeMessage.value = `${submenu.title} 기능은 ${submenu.minPlan} 플랜 이상에서 사용 가능합니다. 플랜을 업그레이드 해주세요.`;
        showUpgradeModal.value = true;
    }
};
const closeUpgradeModal = () => {
    showUpgradeModal.value = false;
};

const toggleMenu = (index) => {
    const menuIndex = openMenus.value.indexOf(index);
    if (menuIndex > -1) {
        openMenus.value.splice(menuIndex, 1);
    } else {
        openMenus.value.push(index);
    }
};

const handleOnClick = (onclickFunction) => {
    if (onclickFunction === 'showPhpMyAdminModal') {
        // 직접 모달 함수 실행
        showPhpMyAdminModalDirect();
    } else if (onclickFunction === 'showPhpMyAdminModalDirect') {
        showPhpMyAdminModalDirect();
    
    } else if (onclickFunction === 'showCyberPanelFileManagerModalDirect') {
        showCyberPanelFileManagerModalDirect();
    } else if (typeof window[onclickFunction] === 'function') {
        window[onclickFunction]();
    }
};

const showPhpMyAdminModalDirect = () => {
    // 현재 서버 ID 가져오기
    const path = window.location.pathname;
    const match = path.match(/\/server\/(\d+)/);
    if (!match) {
        alert('서버 정보를 찾을 수 없습니다.');
        return;
    }
    
    const serverId = match[1];
    
    // 모달 HTML 생성
    const modalHtml = `
        <div id="phpmyadmin-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-8 w-full max-w-md mx-4 border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 shadow-lg mb-6">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">phpMyAdmin 자동 로그인</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">대시보드 로그인 패스워드를 입력해주세요.</p>
                    
                    <div class="space-y-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input
                                id="modalPassword"
                                type="password"
                                placeholder="패스워드를 입력하세요"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition duration-200"
                            />
                        </div>
                        <div id="modalError" class="hidden text-red-500 text-sm text-center bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3"></div>
                    </div>
                    
                    <div class="flex space-x-3 mt-6">
                        <button 
                            onclick="submitPhpMyAdminLoginDirect()"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-medium rounded-lg shadow-lg hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 transform hover:scale-105"
                        >
                            <span>로그인</span>
                        </button>
                        <button 
                            onclick="closePhpMyAdminModalDirect()"
                            class="flex-1 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg shadow-lg hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200"
                        >
                            취소
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // 모달 추가
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // 모달 외부 클릭 시 닫기
    document.getElementById('phpmyadmin-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePhpMyAdminModalDirect();
        }
    });
    
    // 패스워드 입력 필드에 포커스
    setTimeout(() => {
        document.getElementById('modalPassword').focus();
    }, 100);
    
    // ESC 키로 모달 닫기
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePhpMyAdminModalDirect();
        }
    });
    
    // 전역 함수로 등록
    window.closePhpMyAdminModalDirect = closePhpMyAdminModalDirect;
    window.submitPhpMyAdminLoginDirect = () => submitPhpMyAdminLoginDirect(serverId);
    

    
    // CyberPanel 파일매니저 전역 함수 등록
    window.closeCyberPanelFileManagerModalDirect = closeCyberPanelFileManagerModalDirect;
    window.submitCyberPanelFileManagerLoginDirect = () => submitCyberPanelFileManagerLoginDirect(serverId);
    window.showCyberPanelFileManagerModalDirect = showCyberPanelFileManagerModalDirect;
};

const closePhpMyAdminModalDirect = () => {
    const modal = document.getElementById('phpmyadmin-modal');
    if (modal) {
        modal.remove();
    }
};

const submitPhpMyAdminLoginDirect = (serverId) => {
    const password = document.getElementById('modalPassword').value;
    const errorDiv = document.getElementById('modalError');
    
    if (!password) {
        errorDiv.textContent = '대시보드 패스워드를 입력해주세요.';
        errorDiv.classList.remove('hidden');
        return;
    }
    
    // 로딩 상태 표시
    const loginBtn = document.querySelector('#phpmyadmin-modal button:last-child');
    loginBtn.innerHTML = '<span>처리 중...</span>';
    loginBtn.disabled = true;
    
    // CSRF 토큰 가져오기
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;
    

    
                // API 요청
            fetch(`/server/${serverId}/phpmyadmin/auto-login-form`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'text/html'
                },
                body: JSON.stringify({
                    dashboard_password: password
                })
            })
            .then(response => response.text())
            .then(html => {
                // HTML 응답을 새 창에서 열기
                const newWindow = window.open('', '_blank');
                newWindow.document.write(html);
                newWindow.document.close();
                closePhpMyAdminModalDirect();
            })
            .catch(error => {
                errorDiv.textContent = 'phpMyAdmin 자동 로그인 중 오류가 발생했습니다.';
                errorDiv.classList.remove('hidden');
            })
            .finally(() => {
                // 로딩 상태 해제
                loginBtn.innerHTML = '<span>로그인</span>';
                loginBtn.disabled = false;
            });
};

// CyberPanel 파일매니저 관련 함수들
const showCyberPanelFileManagerModalDirect = () => {
    // 현재 서버 ID 가져오기
    const path = window.location.pathname;
    const match = path.match(/\/server\/(\d+)/);
    if (!match) {
        alert('서버 정보를 찾을 수 없습니다.');
        return;
    }
    
    const serverId = match[1];
    console.log('CyberPanel 파일매니저 직접 접근 - 서버 ID:', serverId);
    
    // CSRF 토큰 가져오기
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;
    
    // 바로 새 창 열기 (로딩 화면 없이)
    const newWindow = window.open('', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
    
    if (!newWindow) {
        alert('팝업이 차단되었습니다. 브라우저에서 팝업을 허용해주세요.');
        return;
    }
    
    // 새 창에 로딩 메시지 표시
    newWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>CyberPanel 파일매니저 로딩 중...</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    text-align: center; 
                    padding: 50px; 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    margin: 0;
                    height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .container { 
                    background: rgba(255,255,255,0.1); 
                    padding: 30px; 
                    border-radius: 10px; 
                    backdrop-filter: blur(10px);
                }
                .spinner {
                    width: 50px;
                    height: 50px;
                    border: 3px solid rgba(255,255,255,0.3);
                    border-top: 3px solid white;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin: 0 auto 20px;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="spinner"></div>
                <h2>CyberPanel 파일매니저 로딩 중...</h2>
                <p>SSO 인증을 통해 파일매니저에 접속 중입니다.</p>
            </div>
        </body>
        </html>
    `);
    newWindow.document.close();
    
    // API 요청
    fetch(`/server/${serverId}/cyberpanel/filemanager/sso`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'text/html'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(html => {
        // 새 창에 파일매니저 HTML 작성
        newWindow.document.write(html);
        newWindow.document.close();
        
        // 성공 메시지 표시
        console.log('CyberPanel 파일매니저 연결 성공');
    })
    .catch(error => {
        console.error('CyberPanel 파일매니저 연결 오류:', error);
        
        // 오류 메시지를 새 창에 표시
        newWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>CyberPanel 파일매니저 오류</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        text-align: center; 
                        padding: 50px; 
                        background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
                        color: white;
                        margin: 0;
                        height: 100vh;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    .container { 
                        background: rgba(255,255,255,0.1); 
                        padding: 30px; 
                        border-radius: 10px; 
                        backdrop-filter: blur(10px);
                    }
                    .error-icon {
                        font-size: 48px;
                        margin-bottom: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="error-icon">❌</div>
                    <h2>CyberPanel 파일매니저 연결 실패</h2>
                    <p>오류: ${error.message}</p>
                    <button onclick="window.close()" style="
                        background: white; 
                        color: #dc2626; 
                        border: none; 
                        padding: 10px 20px; 
                        border-radius: 5px; 
                        cursor: pointer; 
                        margin-top: 20px;
                    ">창 닫기</button>
                </div>
            </body>
            </html>
        `);
        newWindow.document.close();
    });
};

const closeCyberPanelFileManagerModalDirect = () => {
    const modal = document.getElementById('cyberpanel-filemanager-modal');
    if (modal) {
        modal.remove();
    }
};

const submitCyberPanelFileManagerLoginDirect = (serverId) => {
    // 전역 변수에서 serverId 가져오기
    const actualServerId = window.currentCyberPanelFileManagerServerId || serverId;
    console.log('CyberPanel 파일매니저 연결 시도 - 전역 변수에서 서버 ID:', actualServerId);
    
    const errorDiv = document.getElementById('cyberpanelFileManagerModalError');
    
    // 로딩 상태 표시
    const loginBtn = document.querySelector('#cyberpanel-filemanager-modal button:first-child');
    loginBtn.innerHTML = '<span>연결 중...</span>';
    loginBtn.disabled = true;
    
    // CSRF 토큰 가져오기
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;
    
    console.log('CyberPanel 파일매니저 연결 시도 - 최종 서버 ID:', actualServerId);
    
    // API 요청
    fetch(`/server/${actualServerId}/cyberpanel/filemanager/sso`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'text/html'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(html => {
        // HTML 응답을 새 창에서 열기
        const newWindow = window.open('', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
        if (newWindow) {
            newWindow.document.write(html);
            newWindow.document.close();
            
            // 모달 닫기
            closeCyberPanelFileManagerModalDirect();
            
            // 성공 메시지 표시
            console.log('CyberPanel 파일매니저 연결 성공');
        } else {
            errorDiv.textContent = '팝업이 차단되었습니다. 브라우저에서 팝업을 허용해주세요.';
            errorDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('CyberPanel 파일매니저 연결 오류:', error);
        errorDiv.textContent = 'CyberPanel 파일매니저 연결 중 오류가 발생했습니다: ' + error.message;
        errorDiv.classList.remove('hidden');
    })
    .finally(() => {
        // 로딩 상태 해제
        loginBtn.innerHTML = '<span>CyberPanel 파일매니저 열기</span>';
        loginBtn.disabled = false;
    });
};


</script> 