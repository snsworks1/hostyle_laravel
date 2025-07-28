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
                            <a v-if="submenu.route" :href="route(submenu.route, { id: serverId })" class="block px-3 py-2 text-sm text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200">
                                {{ submenu.title }}
                            </a>
                            <button v-else-if="submenu.children" @click.stop class="block px-3 py-2 text-sm text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200 w-full text-left">
                                {{ submenu.title }}
                            </button>
                        </template>
                    </div>
                </div>
                <!-- 단일 메뉴 아이템 (route가 있으면 바로 이동) -->
                <a v-else-if="menu.route" :href="route(menu.route, { id: serverId })" class="flex items-center px-4 py-3 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200">
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
</script> 