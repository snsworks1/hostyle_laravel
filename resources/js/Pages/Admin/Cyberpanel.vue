<template>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
        <AdminHeader />
        <div class="flex flex-1">
            <AdminSidebar />
            <main class="flex-1 p-10">
                <h1 class="text-2xl font-bold mb-6 text-white">사이버패널 연동</h1>
                <div class="bg-white/10 rounded-xl border border-white/20 p-8 text-white mb-8">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label>이름</label>
                            <input v-model="form.name" class="bg-white/10 border border-white/20 rounded px-2 py-1 ml-2" required>
                        </div>
                        <div>
                            <label>리전</label>
                            <select v-model="form.region" class="bg-white/10 border border-white/20 rounded px-2 py-1 ml-2" required>
                                <option value="한국(서울)">한국(서울)</option>
                                <option value="일본(도쿄)">일본(도쿄)</option>
                                <option value="미국(뉴욕)">미국(뉴욕)</option>
                                <option value="중국(홍콩)">중국(홍콩)</option>
                                <option value="싱가폴(아시아)">싱가폴(아시아)</option>
                            </select>
                        </div>
                        <div>
                            <label>Host</label>
                            <input v-model="form.host" class="bg-white/10 border border-white/20 rounded px-2 py-1 ml-2" required>
                        </div>
                        <div>
                            <label>Port</label>
                            <input v-model="form.api_port" type="number" class="bg-white/10 border border-white/20 rounded px-2 py-1 ml-2" required>
                        </div>
                        <div>
                            <label>API User</label>
                            <input v-model="form.api_user" class="bg-white/10 border border-white/20 rounded px-2 py-1 ml-2" required>
                        </div>
                        <div>
                            <label>API Password</label>
                            <input v-model="form.api_password" type="password" class="bg-white/10 border border-white/20 rounded px-2 py-1 ml-2" required>
                        </div>
                        <div>
                            <label>SSL</label>
                            <input v-model="form.ssl" type="checkbox" class="ml-2">
                        </div>
                        <div>
                            <label>Status</label>
                            <select v-model="form.status" class="bg-white/10 border border-white/20 rounded px-2 py-1 ml-2">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div>
                            <label>메모</label>
                            <input v-model="form.notes" class="bg-white/10 border border-white/20 rounded px-2 py-1 ml-2">
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-blue-500 to-purple-500 text-white px-6 py-2 rounded-lg font-semibold shadow hover:from-blue-600 hover:to-purple-600 transition">신규 서버 연동</button>
                    </form>
                </div>
                <div class="bg-white/10 rounded-xl border border-white/20 p-8 text-white">
                    <h2 class="text-xl font-bold mb-4">연동된 서버 목록</h2>
                    <table class="w-full text-white/90">
                        <thead>
                            <tr>
                                <th class="text-left">이름</th>
                                <th class="text-left">Host</th>
                                <th class="text-left">Port</th>
                                <th class="text-left">상태</th>
                                <th class="text-left">연동여부</th>
                                <th class="text-left">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="s in servers" :key="s.id">
                                <td>{{ s.name }}</td>
                                <td>{{ s.host }}</td>
                                <td>{{ s.api_port }}</td>
                                <td>{{ s.status }}</td>
                                <td>
                                    <span v-if="connectionStatus[s.id]?.loading">확인 중...</span>
                                    <span v-else-if="connectionStatus[s.id]?.success === true" class="text-green-400">연동 성공</span>
                                    <span v-else-if="connectionStatus[s.id]?.success === false" class="text-red-400">연동 실패</span>
                                    <span v-if="connectionStatus[s.id]?.message && !connectionStatus[s.id]?.loading" class="block text-xs text-white/60">{{ connectionStatus[s.id].message }}</span>
                                </td>
                                <td>
                                    <button @click="remove(s.id)" class="text-red-400 hover:underline">삭제</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import AdminHeader from '@/Components/AdminHeader.vue';
import AdminSidebar from '@/Components/AdminSidebar.vue';

const props = defineProps({
  servers: {
    type: Array,
    default: () => []
  }
});
const servers = props.servers;

const form = useForm({
    name: '',
    region: '',
    host: '',
    api_port: 8090,
    api_user: '',
    api_password: '',
    ssl: false,
    status: 'active',
    notes: '',
});

const connectionStatus = ref({}); // { [id]: { loading, success, message } }

function submit() {
    form.post(route('admin.cyberpanel.store'));
}

function remove(id) {
    if (confirm('정말 삭제하시겠습니까?')) {
        router.delete(route('admin.cyberpanel.destroy', id));
    }
}

async function testConnection(id) {
    connectionStatus.value[id] = { loading: true, success: null, message: '연동 확인 중...' };
    try {
        const res = await axios.post(route('admin.cyberpanel.test', id));
        connectionStatus.value[id] = {
            loading: false,
            success: res.data.success,
            message: res.data.message
        };
    } catch (e) {
        connectionStatus.value[id] = {
            loading: false,
            success: false,
            message: e.response?.data?.message || '연동 실패'
        };
    }
}

onMounted(() => {
    servers.forEach(s => testConnection(s.id));
});
</script> 