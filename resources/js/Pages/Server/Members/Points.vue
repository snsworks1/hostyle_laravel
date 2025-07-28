<script setup>
import { usePage } from '@inertiajs/vue3';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server || {};
const allServers = page.props.allServers || [];
const sidebarMenus = page.props.sidebarMenus || [];

const samplePoints = [
  { id: 1, member: '홍길동', amount: 1000, desc: '가입 축하', date: '2024-07-01' },
  { id: 2, member: '김영희', amount: 500, desc: '이벤트 참여', date: '2024-07-02' },
];
</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <div class="max-w-4xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">회원 포인트 관리</h1>
          <p class="text-white/70 mb-8">회원별 포인트 내역을 확인하고 지급/차감할 수 있습니다.</p>
          <div class="flex justify-end mb-4">
            <button class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-5 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-blue-600 transition">+ 포인트 지급</button>
          </div>
          <div class="bg-white/10 rounded-xl border border-white/20 overflow-x-auto">
            <table class="min-w-full text-white">
              <thead>
                <tr class="bg-white/10">
                  <th class="px-4 py-3 text-left">회원명</th>
                  <th class="px-4 py-3 text-left">포인트</th>
                  <th class="px-4 py-3 text-left">내역</th>
                  <th class="px-4 py-3 text-left">일시</th>
                  <th class="px-4 py-3 text-left">관리</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="pt in samplePoints" :key="pt.id" class="border-b border-white/10">
                  <td class="px-4 py-2">{{ pt.member }}</td>
                  <td class="px-4 py-2">{{ pt.amount }}</td>
                  <td class="px-4 py-2">{{ pt.desc }}</td>
                  <td class="px-4 py-2">{{ pt.date }}</td>
                  <td class="px-4 py-2">
                    <button class="text-blue-400 hover:underline mr-2">지급</button>
                    <button class="text-red-400 hover:underline">차감</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="samplePoints.length === 0" class="text-center text-white/60 py-8">포인트 내역이 없습니다.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 