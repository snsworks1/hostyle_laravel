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
        <div class="max-w-3xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">회원 설정</h1>
          <p class="text-white/70 mb-8">회원 관련 기본 설정을 관리할 수 있습니다.</p>
          <form class="space-y-6">
            <div>
              <label class="block text-white/80 mb-1">가입 승인 필요</label>
              <select class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3">
                <option>자동 승인</option>
                <option>관리자 승인 필요</option>
              </select>
            </div>
            <div>
              <label class="block text-white/80 mb-1">기본 권한</label>
              <select class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3">
                <option>일반회원</option>
                <option>에디터</option>
                <option>관리자</option>
              </select>
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