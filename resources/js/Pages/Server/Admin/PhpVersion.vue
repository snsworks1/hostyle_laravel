<template>
  <div class="max-w-2xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-white mb-4">PHP 버전설정</h1>
    <p class="text-white/70 mb-8">서버의 PHP 버전을 선택/변경할 수 있습니다.</p>
    <form class="space-y-6">
      <div>
        <label class="block text-white/80 mb-1">PHP 버전 선택</label>
        <select class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3">
          <option>8.2</option>
          <option>8.1</option>
          <option>7.4</option>
        </select>
      </div>
      <div class="flex justify-end">
        <button class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-blue-600 transition">저장</button>
      </div>
    </form>
  </div>
</template> 