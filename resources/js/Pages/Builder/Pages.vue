<template>
  <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">웹 빌더</h1>
        <div class="flex gap-3">
          <button 
            @click="goToServerSettings"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg"
          >
            사이트 설정
          </button>
          <button 
            @click="showCreateModal = true"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
          >
            새 페이지 생성
          </button>
        </div>
      </div>

      <!-- 페이지 목록 -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="page in pages" 
          :key="page.id"
          class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow"
        >
          <h3 class="text-lg font-medium text-gray-900 mb-2">{{ page.title }}</h3>
          <p class="text-sm text-gray-500 mb-4">슬러그: {{ page.slug }}</p>
          
          <div class="flex items-center justify-between">
            <span 
              :class="[
                'px-2 py-1 text-xs rounded-full',
                page.is_published 
                  ? 'bg-green-100 text-green-800' 
                  : 'bg-gray-100 text-gray-800'
              ]"
            >
              {{ page.is_published ? '게시됨' : '초안' }}
            </span>
            
            <div class="flex gap-2">
              <button 
                @click="editPage(page.id)"
                class="text-blue-600 hover:text-blue-800 text-sm"
              >
                편집
              </button>
              <button 
                v-if="!page.is_published"
                @click="publishPage(page.id)"
                class="text-green-600 hover:text-green-800 text-sm"
              >
                게시
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- 새 페이지 생성 모달 -->
      <div v-if="showCreateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">새 페이지 생성</h3>
            
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">페이지 제목</label>
                <input 
                  v-model="newPage.title"
                  type="text" 
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                  placeholder="홈페이지"
                />
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">슬러그</label>
                <input 
                  v-model="newPage.slug"
                  type="text" 
                  class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                  placeholder="home"
                />
              </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
              <button 
                @click="showCreateModal = false"
                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50"
              >
                취소
              </button>
              <button 
                @click="createPage"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
              >
                생성
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
  serverId: number;
  pages: any[];
}>();

const showCreateModal = ref(false);
const newPage = reactive({
  title: '',
  slug: ''
});

function editPage(pageId: number) {
  router.visit(`/server/${props.serverId}/builder/app?page=${pageId}`);
}

function goToServerSettings() {
  router.visit(`/server/${props.serverId}/settings/basic`);
}

async function publishPage(pageId: number) {
  try {
    await fetch(`/api/builder/${props.serverId}/pages/${pageId}/publish`, { 
      method: 'POST' 
    });
    // 페이지 새로고침
    router.reload();
  } catch (error) {
    console.error('Failed to publish:', error);
  }
}

async function createPage() {
  try {
    const response = await fetch(`/api/builder/${props.serverId}/pages`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(newPage)
    });
    
    if (response.ok) {
      const page = await response.json();
      showCreateModal.value = false;
      newPage.title = '';
      newPage.slug = '';
      // 새로 생성된 페이지로 이동
      router.visit(`/server/${props.serverId}/builder/app?page=${page.id}`);
    }
  } catch (error) {
    console.error('Failed to create page:', error);
  }
}
</script>
