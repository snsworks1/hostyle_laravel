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
          <h1 class="text-3xl font-bold text-white mb-4">소셜 설정</h1>
          <p class="text-white/70 mb-8">사이트의 소셜 연동 및 외부 서비스 설정을 관리하세요.</p>
          <form class="space-y-6">
            <div>
              <label class="block text-white/80 mb-1">구글 로그인 연동</label>
              <input class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" placeholder="Google Client ID" />
            </div>
            <div>
              <label class="block text-white/80 mb-1">네이버 지도 연동</label>
              <input class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" placeholder="Naver Map API Key" />
            </div>
            <div>
              <label class="block text-white/80 mb-1">카카오 지도 연동</label>
              <input class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" placeholder="Kakao Map API Key" />
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