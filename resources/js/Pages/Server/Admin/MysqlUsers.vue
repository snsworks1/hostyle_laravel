<template>
  <div class="max-w-3xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-white mb-4">MySQL 유저 관리</h1>
    <p class="text-white/70 mb-8">서버의 MySQL 데이터베이스 유저를 관리할 수 있습니다.</p>
    <div class="flex justify-end mb-4">
      <button class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-5 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-blue-600 transition">+ 유저 추가</button>
    </div>
    <div class="bg-white/10 rounded-xl border border-white/20 overflow-x-auto">
      <table class="min-w-full text-white">
        <thead>
          <tr class="bg-white/10">
            <th class="px-4 py-3 text-left">유저명</th>
            <th class="px-4 py-3 text-left">권한</th>
            <th class="px-4 py-3 text-left">관리</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in sampleUsers" :key="user.id" class="border-b border-white/10">
            <td class="px-4 py-2">{{ user.name }}</td>
            <td class="px-4 py-2">{{ user.role }}</td>
            <td class="px-4 py-2">
              <button class="text-blue-400 hover:underline mr-2">설정</button>
              <button class="text-red-400 hover:underline">삭제</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="sampleUsers.length === 0" class="text-center text-white/60 py-8">등록된 유저가 없습니다.</div>
    </div>
  </div>
</template>
<script setup>
const sampleUsers = [
  { id: 1, name: 'dbuser1', role: '읽기/쓰기' },
  { id: 2, name: 'dbuser2', role: '읽기전용' },
];
</script> 