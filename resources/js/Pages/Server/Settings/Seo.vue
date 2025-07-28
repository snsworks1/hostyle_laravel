<script setup>
import { usePage } from '@inertiajs/vue3';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server;
const allServers = page.props.allServers;
const sidebarMenus = page.props.sidebarMenus;

</script>
<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <!-- 기존 메인 컨텐츠 -->
        <div class="max-w-3xl mx-auto py-10 px-4">
          <h1 class="text-3xl font-bold text-white mb-4">SEO 설정</h1>
          <p class="text-white/70 mb-8">사이트의 SEO(검색엔진 최적화) 정보를 설정하세요.</p>
          <form class="space-y-6">
            <div>
              <label class="block text-white/80 mb-1">robot 설정</label>
              <textarea class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" rows="2" placeholder="User-agent: *\nDisallow: /admin"></textarea>
            </div>
            <div>
              <label class="block text-white/80 mb-1">사이트맵</label>
              <input class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" placeholder="사이트맵 URL" />
            </div>
            <div>
              <label class="block text-white/80 mb-1">페이지 제목</label>
              <input class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" placeholder="페이지 제목" />
            </div>
            <div>
              <label class="block text-white/80 mb-1">키워드</label>
              <input class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" placeholder="예: 호스팅, CMS, 홈페이지" />
            </div>
            <div>
              <label class="block text-white/80 mb-1">설명</label>
              <textarea class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" rows="2" placeholder="사이트 설명"></textarea>
            </div>
            <div>
              <label class="block text-white/80 mb-1">메타태그</label>
              <textarea class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" rows="2" placeholder="<meta name='author' content='Hostyle'>"></textarea>
            </div>
            <div>
              <label class="block text-white/80 mb-1">OG(Open Graph)</label>
              <textarea class="w-full bg-white/10 border border-white/20 rounded-lg text-white p-3" rows="2" placeholder="OG 태그 입력"></textarea>
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