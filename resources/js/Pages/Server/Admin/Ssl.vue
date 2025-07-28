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
        <div class="max-w-2xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">SSL 관리</h1>
          <p class="text-white/70 mb-8">서버의 SSL 인증서를 관리할 수 있습니다.</p>
          <div class="bg-white/10 rounded-xl border border-white/20 p-6 mb-6">
            <div class="mb-2 text-white/80">인증서 상태: <span class="text-green-400 font-semibold">유효</span></div>
            <div class="mb-2 text-white/80">만료일: 2024-12-31</div>
            <div class="mb-2 text-white/80">도메인: example.com</div>
          </div>
          <div class="flex justify-end">
            <button class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-blue-600 transition">인증서 발급/갱신</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 