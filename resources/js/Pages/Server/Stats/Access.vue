<script setup>
import { usePage } from '@inertiajs/vue3';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server || {};
const allServers = page.props.allServers || [];
const sidebarMenus = page.props.sidebarMenus || [];

const sampleStats = [
  { id: 1, date: '2024-07-01', visitors: 120, pageviews: 350, device: 'PC' },
  { id: 2, date: '2024-07-02', visitors: 80, pageviews: 200, device: 'Mobile' },
];
</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <div class="max-w-4xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">접속 통계</h1>
          <p class="text-white/70 mb-8">사이트 접속 통계를 확인할 수 있습니다.</p>
          <div class="flex justify-end mb-4">
            <input type="date" class="bg-white/10 border border-white/20 rounded-lg text-white px-4 py-2 mr-2" />
            <input type="date" class="bg-white/10 border border-white/20 rounded-lg text-white px-4 py-2" />
          </div>
          <div class="bg-white/10 rounded-xl border border-white/20 overflow-x-auto">
            <table class="min-w-full text-white">
              <thead>
                <tr class="bg-white/10">
                  <th class="px-4 py-3 text-left">일자</th>
                  <th class="px-4 py-3 text-left">방문자수</th>
                  <th class="px-4 py-3 text-left">페이지뷰</th>
                  <th class="px-4 py-3 text-left">기기</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="stat in sampleStats" :key="stat.id" class="border-b border-white/10">
                  <td class="px-4 py-2">{{ stat.date }}</td>
                  <td class="px-4 py-2">{{ stat.visitors }}</td>
                  <td class="px-4 py-2">{{ stat.pageviews }}</td>
                  <td class="px-4 py-2">{{ stat.device }}</td>
                </tr>
              </tbody>
            </table>
            <div v-if="sampleStats.length === 0" class="text-center text-white/60 py-8">통계 데이터가 없습니다.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 