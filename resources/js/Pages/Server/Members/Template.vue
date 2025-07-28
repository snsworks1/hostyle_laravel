<script setup>
import { usePage } from '@inertiajs/vue3';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server || {};
const allServers = page.props.allServers || [];
const sidebarMenus = page.props.sidebarMenus || [];

const sampleTemplates = [
  { id: 1, name: '기본 회원가입', description: '이름, 이메일, 비밀번호' },
  { id: 2, name: '확장 회원가입', description: '이름, 이메일, 비밀번호, 휴대폰, 주소' },
];
</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <div class="max-w-4xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">회원가입 템플릿 관리</h1>
          <p class="text-white/70 mb-8">회원가입 시 사용할 입력폼 템플릿을 관리할 수 있습니다.</p>
          <div class="flex justify-end mb-4">
            <button class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-5 py-2 rounded-lg font-semibold hover:from-purple-600 hover:to-blue-600 transition">+ 템플릿 추가</button>
          </div>
          <div class="bg-white/10 rounded-xl border border-white/20 overflow-x-auto">
            <table class="min-w-full text-white">
              <thead>
                <tr class="bg-white/10">
                  <th class="px-4 py-3 text-left">템플릿명</th>
                  <th class="px-4 py-3 text-left">설명</th>
                  <th class="px-4 py-3 text-left">관리</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="tpl in sampleTemplates" :key="tpl.id" class="border-b border-white/10">
                  <td class="px-4 py-2">{{ tpl.name }}</td>
                  <td class="px-4 py-2">{{ tpl.description }}</td>
                  <td class="px-4 py-2">
                    <button class="text-blue-400 hover:underline mr-2">설정</button>
                    <button class="text-red-400 hover:underline">삭제</button>
                  </td>
                </tr>
              </tbody>
            </table>
            <div v-if="sampleTemplates.length === 0" class="text-center text-white/60 py-8">등록된 템플릿이 없습니다.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 