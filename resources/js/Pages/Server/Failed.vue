<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <div class="bg-white/10 border border-white/20 rounded-2xl p-10 max-w-lg w-full text-center shadow-xl">
      <h1 class="text-3xl font-bold text-red-400 mb-4">서버 생성 실패</h1>
      <div class="text-white text-lg mb-6">
        <span v-if="reason">{{ reason }}</span>
        <span v-else>알 수 없는 오류가 발생했습니다.</span>
      </div>
      <div class="flex flex-col gap-3">
        <button @click="goHome" class="bg-gradient-to-r from-blue-500 to-purple-500 text-white px-6 py-2 rounded-lg font-semibold shadow hover:from-blue-600 hover:to-purple-600 transition">홈으로 이동</button>
        <button @click="goServers" class="bg-white/10 border border-white/20 text-white px-6 py-2 rounded-lg font-semibold hover:bg-white/20 transition">서버 목록으로</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
const props = defineProps({
  reason: { type: String, default: '' }
});
function goHome() {
  router.visit('/');
}
function goServers() {
  router.visit('/select-server');
}
</script> 