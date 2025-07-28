<script setup>
import { usePage } from '@inertiajs/vue3';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server || {};
const allServers = page.props.allServers || [];
const sidebarMenus = page.props.sidebarMenus || [];

const sampleMembers = [
  { id: 1, name: '홍길동', email: 'hong@test.com', joined_at: '2024-07-01', status: '정상' },
  { id: 2, name: '김영희', email: 'kim@test.com', joined_at: '2024-07-02', status: '정상' },
];
</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <div class="max-w-5xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">회원 리스트</h1>
          <p class="text-white/70 mb-8">사이트에 가입한 회원 목록을 확인하고 관리할 수 있습니다.</p>
          <div class="flex justify-between mb-4">
            <input class="bg-white/10 border border-white/20 rounded-lg text-white px-4 py-2 w-64" placeholder="회원명/이메일 검색" />
            <button class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-5 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-blue-600 transition">+ 회원 추가</button>
          </div>
          <div class="bg-white/10 rounded-xl border border-white/20 overflow-x-auto">
            <table class="min-w-full text-white">
              <thead>
                <tr class="bg-white/10">
                  <th class="px-4 py-3 text-left">이름</th>
                  <th class="px-4 py-3 text-left">이메일</th>
                  <th class="px-4 py-3 text-left">가입일</th>
                  <th class="px-4 py-3 text-left">상태</th>
                  <th class="px-4 py-3 text-left">관리</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="member in sampleMembers" :key="member.id" class="border-b border-white/10">
                  <td class="px-4 py-2">{{ member.name }}</td>
                  <td class="px-4 py-2">{{ member.email }}</td>
                  <td class="px-4 py-2">{{ member.joined_at }}</td>
                  <td class="px-4 py-2">{{ member.status }}</td>
                  <td class="px-4 py-2">
                    <button class="text-blue-400 hover:underline mr-2">설정</button>
                    <button class="text-red-400 hover:underline">삭제</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="sampleMembers.length === 0" class="text-center text-white/60 py-8">등록된 회원이 없습니다.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 