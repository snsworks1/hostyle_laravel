<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <div v-if="!done && !error" class="flex flex-col items-center">
      <svg class="animate-spin h-12 w-12 text-purple-400 mb-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
      </svg>
      <div class="text-2xl text-white font-bold mb-2">서버를 생성중입니다...</div>
      <div class="text-white/70 text-sm">최대 1분 정도 소요될 수 있습니다.</div>
    </div>
    <div v-else-if="done" class="flex flex-col items-center">
      <svg class="h-12 w-12 text-green-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      <div class="text-2xl text-green-400 font-bold mb-2">생성이 완료되었습니다!</div>
      <div class="text-white/70 text-sm">잠시 후 서버 선택 페이지로 이동합니다.</div>
    </div>
    <div v-else-if="error" class="flex flex-col items-center">
      <svg class="h-12 w-12 text-red-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
      <div class="text-2xl text-red-400 font-bold mb-2">서버 생성에 실패했습니다</div>
      <div class="text-white/70 text-sm">{{ errorMsg }}</div>
      <button class="mt-4 px-4 py-2 bg-purple-600 text-white rounded" @click="goBack">다시 시도하기</button>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const done = ref(false);
const error = ref(false);
const errorMsg = ref('');

function goBack() {
  router.visit(route('server.create'));
}

onMounted(async () => {
  try {
    const res = await axios.post('/create-server/provision');
    if (res.data.success) {
      done.value = true;
      setTimeout(() => {
        router.visit(route('server.select'));
      }, 1000);
    } else {
      error.value = true;
      errorMsg.value = res.data.reason || '서버 생성에 실패했습니다.';
    }
  } catch (e) {
    error.value = true;
    errorMsg.value = '서버 생성 중 오류가 발생했습니다.';
  }
});
</script> 