<script setup>
import { usePage } from '@inertiajs/vue3';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server || {};
const allServers = page.props.allServers || [];
const sidebarMenus = page.props.sidebarMenus || [];

const sampleDbs = [
  { name: 'mydb1', tables: 12, size: '20MB' },
  { name: 'mydb2', tables: 5, size: '8MB' },
];
</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <div class="max-w-3xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">MySQL 관리</h1>
          <p class="text-white/70 mb-8">서버의 MySQL 데이터베이스를 관리할 수 있습니다.</p>
          <div class="bg-white/10 rounded-xl border border-white/20 overflow-x-auto mb-6">
            <table class="min-w-full text-white">
              <thead>
                <tr class="bg-white/10">
                  <th class="px-4 py-3 text-left">DB명</th>
                  <th class="px-4 py-3 text-left">테이블 수</th>
                  <th class="px-4 py-3 text-left">용량</th>
                  <th class="px-4 py-3 text-left">관리</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="db in sampleDbs" :key="db.name" class="border-b border-white/10">
                  <td class="px-4 py-2">{{ db.name }}</td>
                  <td class="px-4 py-2">{{ db.tables }}</td>
                  <td class="px-4 py-2">{{ db.size }}</td>
                  <td class="px-4 py-2">
                    <button class="text-blue-400 hover:underline mr-2">테이블 보기</button>
                    <button class="text-red-400 hover:underline">삭제</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="sampleDbs.length === 0" class="text-center text-white/60 py-8">등록된 데이터베이스가 없습니다.</div>
          </div>
          <div class="flex justify-end">
            <button class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-6 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-blue-600 transition">+ DB 추가</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 