<script setup>
import { usePage } from '@inertiajs/vue3';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server;
const allServers = page.props.allServers;
const sidebarMenus = page.props.sidebarMenus;

</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <!-- 기존 메인 컨텐츠 -->
        <div class="max-w-3xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">추가기능 설정</h1>
          <p class="text-white/70 mb-8">사이트의 추가 스크립트 및 확장 기능을 설정하세요.</p>
          <form class="space-y-6">
            <div>
              <label class="block text-white/80 mb-1">헤더 스크립트</label>
              <textarea class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" rows="3" placeholder="<script> ... </script>"></textarea>
            </div>
            <div>
              <label class="block text-white/80 mb-1">푸터 스크립트</label>
              <textarea class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" rows="3" placeholder="<script> ... </script>"></textarea>
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