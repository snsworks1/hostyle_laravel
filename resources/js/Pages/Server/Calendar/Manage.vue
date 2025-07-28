<template>
  <div class="max-w-5xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-white mb-4">달력 리스트 관리</h1>
    <p class="text-white/70 mb-8">사이트 내 달력을 생성·관리할 수 있습니다.</p>
    <div class="flex justify-end mb-4">
      <button class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-5 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-blue-600 transition">+ 달력 추가</button>
    </div>
    <div class="bg-white/10 rounded-xl border border-white/20 overflow-x-auto">
      <table class="min-w-full text-white">
        <thead>
          <tr class="bg-white/10">
            <th class="px-4 py-3 text-left">달력명</th>
            <th class="px-4 py-3 text-left">설명</th>
            <th class="px-4 py-3 text-left">생성일</th>
            <th class="px-4 py-3 text-left">관리</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="calendar in sampleCalendars" :key="calendar.id" class="border-b border-white/10">
            <td class="px-4 py-2">{{ calendar.name }}</td>
            <td class="px-4 py-2">{{ calendar.description }}</td>
            <td class="px-4 py-2">{{ calendar.created_at }}</td>
            <td class="px-4 py-2">
              <button class="text-blue-400 hover:underline mr-2">설정</button>
              <button class="text-red-400 hover:underline">삭제</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="sampleCalendars.length === 0" class="text-center text-white/60 py-8">등록된 달력이 없습니다.</div>
    </div>
  </div>
</template>
<script setup>
const sampleCalendars = [
  { id: 1, name: '행사 일정', description: '회사/사이트 행사 달력', created_at: '2024-07-01' },
  { id: 2, name: '예약 달력', description: '예약 관리용 달력', created_at: '2024-07-02' },
];
</script> 