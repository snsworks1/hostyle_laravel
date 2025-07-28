<script setup>
import { usePage } from '@inertiajs/vue3';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server || {};
const allServers = page.props.allServers || [];
const sidebarMenus = page.props.sidebarMenus || [];

const sampleBackups = [
  { id: 1, date: '2024-07-01 10:00', filename: 'backup_20240701.zip', size: '120MB' },
  { id: 2, date: '2024-07-02 14:30', filename: 'backup_20240702.zip', size: '130MB' },
];
</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <div class="max-w-3xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">서버 백업 관리</h1>
          <p class="text-white/70 mb-8">서버의 백업 내역을 확인하고 백업/복원할 수 있습니다.</p>
          <div class="flex justify-end mb-4">
            <button class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-5 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-blue-600 transition">+ 백업 생성</button>
          </div>
          <div class="bg-white/10 rounded-xl border border-white/20 overflow-x-auto">
            <table class="min-w-full text-white">
              <thead>
                <tr class="bg-white/10">
                  <th class="px-4 py-3 text-left">백업일시</th>
                  <th class="px-4 py-3 text-left">파일명</th>
                  <th class="px-4 py-3 text-left">용량</th>
                  <th class="px-4 py-3 text-left">관리</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="backup in sampleBackups" :key="backup.id" class="border-b border-white/10">
                  <td class="px-4 py-2">{{ backup.date }}</td>
                  <td class="px-4 py-2">{{ backup.filename }}</td>
                  <td class="px-4 py-2">{{ backup.size }}</td>
                  <td class="px-4 py-2">
                    <button class="text-blue-400 hover:underline mr-2">복원</button>
                    <button class="text-red-400 hover:underline">삭제</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="sampleBackups.length === 0" class="text-center text-white/60 py-8">백업 내역이 없습니다.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 