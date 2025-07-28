<template>
  <div class="max-w-5xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-white mb-4">게시판 관리</h1>
    <p class="text-white/70 mb-8">사이트 내 게시판을 생성·관리할 수 있습니다.</p>
    <div class="flex justify-end mb-4">
      <button class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-5 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-blue-600 transition">+ 게시판 추가</button>
    </div>
    <div class="bg-white/10 rounded-xl border border-white/20 overflow-x-auto">
      <table class="min-w-full text-white">
        <thead>
          <tr class="bg-white/10">
            <th class="px-4 py-3 text-left">게시판명</th>
            <th class="px-4 py-3 text-left">설명</th>
            <th class="px-4 py-3 text-left">생성일</th>
            <th class="px-4 py-3 text-left">관리</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="board in sampleBoards" :key="board.id" class="border-b border-white/10">
            <td class="px-4 py-2">{{ board.name }}</td>
            <td class="px-4 py-2">{{ board.description }}</td>
            <td class="px-4 py-2">{{ board.created_at }}</td>
            <td class="px-4 py-2">
              <button class="text-blue-400 hover:underline mr-2">설정</button>
              <button class="text-red-400 hover:underline">삭제</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="sampleBoards.length === 0" class="text-center text-white/60 py-8">등록된 게시판이 없습니다.</div>
    </div>
  </div>
</template>
<script setup>
const sampleBoards = [
  { id: 1, name: '공지사항', description: '사이트 공지 게시판', created_at: '2024-07-01' },
  { id: 2, name: 'Q&A', description: '질문과 답변', created_at: '2024-07-02' },
];
</script> 