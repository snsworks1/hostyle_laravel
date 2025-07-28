<script setup>
import { usePage } from '@inertiajs/vue3';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server || {};
const allServers = page.props.allServers || [];
const sidebarMenus = page.props.sidebarMenus || [];

</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <!-- 기존 메인 컨텐츠 -->
        <div class="max-w-3xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">기본설정</h1>
          <p class="text-white/70 mb-8">사이트의 기본 정보를 설정하세요.</p>
          <form class="space-y-6">
            <div>
              <label class="block text-white/80 mb-1">이용약관</label>
              <textarea class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" rows="3" placeholder="이용약관 내용을 입력하세요"></textarea>
            </div>
            <div>
              <label class="block text-white/80 mb-1">사이트 이름</label>
              <input class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" placeholder="사이트 이름" />
            </div>
            <div>
              <label class="block text-white/80 mb-1">대표 도메인</label>
              <input class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" placeholder="예: example.com" />
            </div>
            <div>
              <label class="block text-white/80 mb-1">파비콘</label>
              <input type="file" class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" />
            </div>
            <div>
              <label class="block text-white/80 mb-1">언어 선택</label>
              <select class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3">
                <option>한국어</option>
                <option>English</option>
              </select>
            </div>
            <div>
              <label class="block text-white/80 mb-1">도메인 등록/가이드</label>
              <textarea class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" rows="2" placeholder="도메인 등록 안내 또는 가이드"></textarea>
            </div>
            <div class="flex justify-end">
              <button class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-blue-600 transition">저장</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template> 