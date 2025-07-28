<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <div class="bg-white/10 rounded-2xl p-8 shadow-lg flex flex-col items-center">
      <div class="text-2xl font-bold text-white mb-4">[임시] 결제 더미 페이지</div>
      <div class="text-white/70 mb-6">여기서 실제 결제 모듈이 들어갈 예정입니다.</div>
      <button @click="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-lg text-lg transition-all duration-300">결제 완료</button>
    </div>
  </div>
</template>

<script setup>
import { router, useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const form = useForm(page.props.form || {});

const submit = () => {
  form.post(route('server.store'));
};
</script> 