<template>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
        <AdminHeader />
        <div class="flex flex-1">
            <AdminSidebar />
            <main class="flex-1 p-10">
                <h1 class="text-2xl font-bold mb-6 text-white">사용자 서버 관리</h1>
                <div class="bg-white/10 rounded-xl border border-white/20 p-8 text-white mb-8">
                    <table class="w-full text-white/90">
                        <thead>
                            <tr>
                                <th class="text-left">서버명</th>
                                <th class="text-left">도메인</th>
                                <th class="text-left">플랜</th>
                                <th class="text-left">상태</th>
                                <th class="text-left">유저명</th>
                                <th class="text-left">이메일</th>
                                <th class="text-left">사이버패널 연동</th>
                                <th class="text-left">상세</th>
                                <th class="text-left">생성일</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="s in servers" :key="s.id">
                                <td>{{ s.site_name }}</td>
                                <td>{{ s.domain }}</td>
                                <td>{{ s.plan }}</td>
                                <td>{{ s.status }}</td>
                                <td>{{ s.user_name }}</td>
                                <td>{{ s.user_email }}</td>
                                <td>
                                    <span v-if="s.cyberpanel_linked" class="text-green-400 font-bold">연동됨</span>
                                    <span v-else class="text-red-400 font-bold">미연동</span>
                                </td>
                                <td>
                                    <button class="bg-white/10 border border-white/20 rounded px-3 py-1 text-white hover:bg-white/20" @click="showDetail(s)">상세보기</button>
                                </td>
                                <td>{{ s.created_at ? s.created_at.substring(0, 10) : '' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="detailServer" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
                    <div class="bg-white/10 border border-white/20 rounded-2xl p-8 w-full max-w-xl text-white relative">
                        <button class="absolute top-4 right-4 text-white/60 hover:text-white text-2xl" @click="detailServer = null">&times;</button>
                        <h2 class="text-xl font-bold mb-4">서버 상세 정보</h2>
                        <div class="mb-2"><b>서버명:</b> {{ detailServer.site_name }}</div>
                        <div class="mb-2"><b>도메인:</b> {{ detailServer.domain }}</div>
                        <div class="mb-2"><b>플랜:</b> {{ detailServer.plan }}</div>
                        <div class="mb-2"><b>상태:</b> {{ detailServer.status }}</div>
                        <div class="mb-2"><b>유저명:</b> {{ detailServer.user_name }}</div>
                        <div class="mb-2"><b>이메일:</b> {{ detailServer.user_email }}</div>
                        <div class="mb-2"><b>사이버패널 연동:</b> <span :class="detailServer.cyberpanel_linked ? 'text-green-400' : 'text-red-400'">{{ detailServer.cyberpanel_linked ? '연동됨' : '미연동' }}</span></div>
                        <div class="mb-2"><b>생성일:</b> {{ detailServer.created_at ? detailServer.created_at.substring(0, 10) : '' }}</div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import AdminHeader from '@/Components/AdminHeader.vue';
import AdminSidebar from '@/Components/AdminSidebar.vue';
const props = defineProps({
  servers: { type: Array, default: () => [] }
});
const servers = props.servers.map(s => ({
  ...s,
  cyberpanel_linked: s.domain && s.domain.includes('cyberpanel') // 샘플: 실제 연동 여부는 서버에서 판단 필요
}));
const detailServer = ref(null);
function showDetail(s) {
  detailServer.value = s;
}
</script> 