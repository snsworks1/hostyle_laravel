<script setup>
import { usePage } from '@inertiajs/vue3';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server || {};
const allServers = page.props.allServers || [];
const sidebarMenus = page.props.sidebarMenus || [];

const sampleVisits = [
  { id: 1, visitor: '홍길동', ip: '1.2.3.4', date: '2024-07-01 10:00', device: 'PC' },
  { id: 2, visitor: '김영희', ip: '5.6.7.8', date: '2024-07-02 14:30', device: 'Mobile' },
];
</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <div class="max-w-4xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">방문기록 통계</h1>
          <p class="text-white/70 mb-8">사이트 방문자 기록을 확인할 수 있습니다.</p>
          <div class="flex justify-end mb-4">
            <input type="date" class="bg-white/10 border border-white/20 rounded-lg text-white px-4 py-2 mr-2" />
            <input type="date" class="bg-white/10 border border-white/20 rounded-lg text-white px-4 py-2" />
          </div>
          <div class="bg-white/10 rounded-xl border border-white/20 overflow-x-auto">
            <table class="min-w-full text-white">
              <thead>
                <tr class="bg-white/10">
                  <th class="px-4 py-3 text-left">방문자</th>
                  <th class="px-4 py-3 text-left">IP</th>
                  <th class="px-4 py-3 text-left">방문일시</th>
                  <th class="px-4 py-3 text-left">기기</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="visit in sampleVisits" :key="visit.id" class="border-b border-white/10">
                  <td class="px-4 py-2">{{ visit.visitor }}</td>
                  <td class="px-4 py-2">{{ visit.ip }}</td>
                  <td class="px-4 py-2">{{ visit.date }}</td>
                  <td class="px-4 py-2">{{ visit.device }}</td>
                </tr>
              </tbody>
            </table>
            <div v-if="sampleVisits.length === 0" class="text-center text-white/60 py-8">방문기록이 없습니다.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 