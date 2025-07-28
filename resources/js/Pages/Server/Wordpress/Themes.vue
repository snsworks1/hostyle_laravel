<script setup>
import { usePage } from '@inertiajs/vue3';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server || {};
const allServers = page.props.allServers || [];
const sidebarMenus = page.props.sidebarMenus || [];

const sampleThemes = [
  { name: 'Astra', desc: '가볍고 빠른 무료 테마', installed: true },
  { name: 'OceanWP', desc: '다목적 무료 테마', installed: false },
  { name: 'Divi', desc: '프리미엄 빌더 테마', installed: false },
];
</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <div class="max-w-3xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">워드프레스 테마설치</h1>
          <p class="text-white/70 mb-8">워드프레스 테마를 설치/관리할 수 있습니다.</p>
          <div class="bg-white/10 rounded-xl border border-white/20 overflow-x-auto mb-6">
            <table class="min-w-full text-white">
              <thead>
                <tr class="bg-white/10">
                  <th class="px-4 py-3 text-left">테마명</th>
                  <th class="px-4 py-3 text-left">설명</th>
                  <th class="px-4 py-3 text-left">상태</th>
                  <th class="px-4 py-3 text-left">관리</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="theme in sampleThemes" :key="theme.name" class="border-b border-white/10">
                  <td class="px-4 py-2">{{ theme.name }}</td>
                  <td class="px-4 py-2">{{ theme.desc }}</td>
                  <td class="px-4 py-2">
                    <span v-if="theme.installed" class="text-green-400 font-semibold">설치됨</span>
                    <span v-else class="text-gray-400">미설치</span>
                  </td>
                  <td class="px-4 py-2">
                    <button v-if="!theme.installed" class="text-blue-400 hover:underline mr-2">설치</button>
                    <button v-if="theme.installed" class="text-red-400 hover:underline">삭제</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="sampleThemes.length === 0" class="text-center text-white/60 py-8">등록된 테마가 없습니다.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 