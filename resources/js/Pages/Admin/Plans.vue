<template>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
        <AdminHeader />
        <div class="flex flex-1">
            <AdminSidebar />
            <main class="flex-1 p-10">
                <h1 class="text-2xl font-bold mb-6 text-white">플랜 관리</h1>
                <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                    <div class="flex gap-2 items-center">
                        <button @click="openAdd" class="bg-gradient-to-r from-blue-500 to-purple-500 text-white px-6 py-2 rounded-lg font-semibold shadow hover:from-blue-600 hover:to-purple-600 transition">플랜 추가</button>
                        <select v-model="selectedServer" class="ml-4 bg-white/10 border border-white/20 rounded px-3 py-2 text-white">
                            <option value="all">전체 서버</option>
                            <option v-for="s in servers" :key="s.id" :value="s.id">{{ s.name }} ({{ s.host }})</option>
                        </select>
                        <button @click="syncPackages" class="ml-2 bg-gradient-to-r from-green-400 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold shadow hover:from-green-500 hover:to-blue-600 transition">사이버패널 패키지 동기화</button>
                    </div>
                </div>
                <div v-if="syncLoading" class="mb-4 text-blue-300">동기화 중입니다...</div>
                <div v-if="syncResult" class="mb-8">
                    <h2 class="text-lg font-bold mb-2 text-white">동기화 결과</h2>
                    <table class="w-full text-white/90 border border-white/20 rounded-xl">
                        <thead>
                            <tr>
                                <th class="text-left">서버</th>
                                <th class="text-left">플랜</th>
                                <th class="text-left">동기화 결과</th>
                                <th class="text-left">에러 메시지</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="r in syncResult" :key="r.server + r.plan">
                                <td>{{ r.server }}</td>
                                <td>{{ r.plan }}</td>
                                <td>
                                    <span v-if="r.action.includes('성공')" class="text-green-400 font-bold">{{ r.action }}</span>
                                    <span v-else-if="r.action.includes('실패')" class="text-red-400 font-bold">{{ r.action }}</span>
                                    <span v-else-if="r.action === '동일'" class="text-blue-400 font-bold">동일</span>
                                    <span v-else>{{ r.action }}</span>
                                </td>
                                <td>
                                    <span v-if="r.error" class="text-red-300">{{ r.error }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="bg-white/10 rounded-xl border border-white/20 p-8 text-white mb-8">
                    <table class="w-full text-white/90">
                        <thead>
                            <tr>
                                <th class="text-left">이름</th>
                                <th class="text-left">라벨</th>
                                <th class="text-left">가격</th>
                                <th class="text-left">체험일</th>
                                <th class="text-left">디스크</th>
                                <th class="text-left">월 트래픽</th>
                                <th class="text-left">도메인</th>
                                <th class="text-left">서브도메인</th>
                                <th class="text-left">DB</th>
                                <th class="text-left">이메일</th>
                                <th class="text-left">기능</th>
                                <th class="text-left">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="plan in plans" :key="plan.id">
                                <td>{{ plan.name }}</td>
                                <td>{{ plan.label }}</td>
                                <td>{{ plan.price.toLocaleString() }}원</td>
                                <td>{{ plan.trial_days || '-' }}</td>
                                <td>{{ plan.disk || '-' }}</td>
                                <td>{{ plan.traffic || '-' }}</td>
                                <td>{{ plan.domains ?? '-' }}</td>
                                <td>{{ plan.subdomains ?? '-' }}</td>
                                <td>{{ plan.databases ?? '-' }}</td>
                                <td>{{ plan.emails ?? '-' }}</td>
                                <td>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="f in plan.features" :key="f.id" class="bg-purple-500/80 text-white px-2 py-1 rounded text-xs">{{ f.name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <button @click="openEdit(plan)" class="bg-white/10 border border-white/20 rounded px-3 py-1 text-white hover:bg-white/20 mr-2">수정</button>
                                    <button @click="remove(plan.id)" class="bg-red-500/80 border border-white/20 rounded px-3 py-1 text-white hover:bg-red-600">삭제</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- 플랜 추가/수정 모달 -->
                <div v-if="showModal" class="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
                    <div class="bg-white/10 border border-white/20 rounded-2xl p-8 w-full max-w-lg text-white relative">
                        <button class="absolute top-4 right-4 text-white/60 hover:text-white text-2xl" @click="closeModal">&times;</button>
                        <h2 class="text-xl font-bold mb-4">{{ editMode ? '플랜 수정' : '플랜 추가' }}</h2>
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label class="block mb-1">이름</label>
                                <input v-model="form.name" class="w-full bg-white/10 border border-white/20 rounded px-2 py-1 text-white" required>
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1">라벨</label>
                                <input v-model="form.label" class="w-full bg-white/10 border border-white/20 rounded px-2 py-1 text-white" required>
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1">가격</label>
                                <input v-model.number="form.price" type="number" class="w-full bg-white/10 border border-white/20 rounded px-2 py-1 text-white" required>
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1">체험일</label>
                                <input v-model.number="form.trial_days" type="number" class="w-full bg-white/10 border border-white/20 rounded px-2 py-1 text-white">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1">디스크</label>
                                <input v-model="form.disk" class="w-full bg-white/10 border border-white/20 rounded px-2 py-1 text-white" placeholder="예: 10GB">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1">월 트래픽</label>
                                <input v-model="form.traffic" class="w-full bg-white/10 border border-white/20 rounded px-2 py-1 text-white" placeholder="예: 100GB/월">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1">도메인 개수</label>
                                <input v-model.number="form.domains" type="number" class="w-full bg-white/10 border border-white/20 rounded px-2 py-1 text-white" placeholder="예: 1">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1">서브도메인 개수</label>
                                <input v-model.number="form.subdomains" type="number" class="w-full bg-white/10 border border-white/20 rounded px-2 py-1 text-white" placeholder="예: 5">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1">DB 개수</label>
                                <input v-model.number="form.databases" type="number" class="w-full bg-white/10 border border-white/20 rounded px-2 py-1 text-white" placeholder="예: 2">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1">이메일 계정 수</label>
                                <input v-model.number="form.emails" type="number" class="w-full bg-white/10 border border-white/20 rounded px-2 py-1 text-white" placeholder="예: 3">
                            </div>
                            <div class="mb-4">
                                <label class="block mb-1">기능</label>
                                <div class="flex flex-wrap gap-2">
                                    <label v-for="f in features" :key="f.id" class="flex items-center gap-1 bg-white/10 px-2 py-1 rounded">
                                        <input type="checkbox" v-model="form.features" :value="f.id" class="accent-purple-500">
                                        <span>{{ f.name }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex justify-end mt-6">
                                <button type="submit" class="bg-gradient-to-r from-blue-500 to-purple-500 text-white px-6 py-2 rounded-lg font-semibold shadow hover:from-blue-600 hover:to-purple-600 transition">저장</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import AdminHeader from '@/Components/AdminHeader.vue';
import AdminSidebar from '@/Components/AdminSidebar.vue';
const props = defineProps({
  plans: { type: Array, default: () => [] },
  features: { type: Array, default: () => [] },
  servers: { type: Array, default: () => [] }
});
const plans = props.plans;
const features = props.features;
const servers = props.servers;
const selectedServer = ref('all');
const showModal = ref(false);
const editMode = ref(false);
const form = ref({ name: '', label: '', price: 0, trial_days: 0, features: [] });
const syncLoading = ref(false);
const syncResult = ref(null);
function openAdd() {
  showModal.value = true;
  editMode.value = false;
  form.value = { name: '', label: '', price: 0, trial_days: 0, features: [] };
}
function openEdit(plan) {
  showModal.value = true;
  editMode.value = true;
  form.value = { ...plan, features: plan.features.map(f => f.id) };
}
function closeModal() {
  showModal.value = false;
}
function submit() {
  if (editMode.value) {
    router.post(route('admin.plans.update', form.value.id), form.value);
  } else {
    router.post(route('admin.plans.store'), form.value);
  }
  closeModal();
  router.visit(route('admin.plans.index'));
}
function remove(id) {
  if (confirm('정말 삭제하시겠습니까?')) {
    router.delete(route('admin.plans.destroy', id));
    router.visit(route('admin.plans.index'));
  }
}
async function syncPackages() {
  syncLoading.value = true;
  syncResult.value = null;
  try {
    let payload = {};
    if (selectedServer.value !== 'all') {
      payload.server_id = selectedServer.value;
    }
    const res = await axios.post(route('admin.plans.sync-cyberpanel'), payload);
    syncResult.value = res.data.results;
  } catch (e) {
    alert('동기화 중 오류가 발생했습니다.');
  }
  syncLoading.value = false;
}
</script> 