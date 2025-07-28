
<template>
    <Head title="서버 선택" />
    <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">

    <ServerHeader :server="server" :all-servers="allServers" @dropdown-toggle="handleDropdownToggle" />
    <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
        <!-- 헤더 -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-white mb-4">서버를 선택하세요</h2>
                <p class="text-xl text-white/70">관리할 서버를 선택하여 대시보드로 이동합니다</p>
            </div>
            <!-- 서버 목록 -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                    v-for="server in servers" 
                    :key="server.id"
                    class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6 hover:bg-white/20 transition-all duration-300 transform hover:scale-105 cursor-pointer"
                    @click="$inertia.visit(route('server.show', { id: server.id }))"
                >
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <div class="text-white font-bold text-lg">{{ server.site_name }}</div>
                            <div class="text-white/70 text-sm">{{ server.domain }}</div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="text-white/70 text-sm">플랜: <span class="text-white font-semibold">{{ server.plan }}</span></div>
                            <div class="text-white/70 text-sm">플랫폼: <span class="text-white font-semibold">{{ server.platform }}</span></div>
                        </div>
                        <div class="text-right">
                            <div class="text-white/70 text-sm">남은 일수</div>
                            <div class="text-2xl font-bold text-white">{{ Math.floor(server.days_remaining) }}일</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</template> 

<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import ServerHeader from '@/Components/ServerHeader.vue';

defineProps({
    servers: {
        type: Array,
        default: () => []
    }
});

const page = usePage();
const allServers = Array.isArray(page.props.servers) ? page.props.servers : [];
const server = allServers.length > 0 ? allServers[0] : { site_name: '서버 없음', domain: '', days_remaining: 0, plan: '' };

const getPlanPrice = (plan) => {
    const prices = {
        'free': '0',
        'starter': '9,900',
        'business': '29,900',
        'enterprise': '99,900'
    };
    return prices[plan] || '0';
};
</script>