<template>
  <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
    <!-- Header -->
    <div class="p-4 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900">페이지 관리</h3>
        <button
          @click="$emit('page-created')"
          class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-md"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-gray-200">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="activeTab = tab.id"
        :class="[
          'flex-1 px-4 py-3 text-sm font-medium border-b-2 transition-colors',
          activeTab === tab.id
            ? 'border-blue-500 text-blue-600'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
        ]"
      >
        {{ tab.name }}
      </button>
    </div>

    <!-- Tab Content -->
    <div class="flex-1 overflow-y-auto">
      <!-- Pages Tab -->
      <div v-if="activeTab === 'pages'" class="p-4">
        <div class="space-y-2">
          <div
            v-for="page in rootPages"
            :key="page.id"
            class="group"
          >
            <PageNode
              :page="page"
              :current-page="currentPage"
              @select="$emit('page-selected', $event)"
            />
          </div>
        </div>
        
        <button
          @click="$emit('page-created')"
          class="w-full mt-4 px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 hover:border-blue-300 transition-colors"
        >
          + 새 페이지 추가
        </button>
      </div>

      <!-- Popups Tab -->
      <div v-if="activeTab === 'popups'" class="p-4">
        <div class="space-y-2">
          <div
            v-for="popup in popups"
            :key="popup.id"
            class="p-3 bg-gray-50 border border-gray-200 rounded-md hover:bg-gray-100 cursor-pointer"
            @click="$emit('page-selected', popup.id)"
          >
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-gray-900">{{ popup.name }}</span>
              <span class="text-xs text-gray-500">팝업</span>
            </div>
          </div>
        </div>
        
        <button
          @click="$emit('page-created')"
          class="w-full mt-4 px-4 py-2 text-sm font-medium text-purple-600 bg-purple-50 border border-purple-200 rounded-md hover:bg-purple-100 hover:border-purple-300 transition-colors"
        >
          + 새 팝업 추가
        </button>
      </div>

      <!-- Data Tab -->
      <div v-if="activeTab === 'data'" class="p-4">
        <div class="space-y-4">
          <div class="p-3 bg-gray-50 border border-gray-200 rounded-md">
            <h4 class="text-sm font-medium text-gray-900 mb-2">사이트 설정</h4>
            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-xs text-gray-600">브랜드 색상</span>
                <div class="w-4 h-4 rounded border border-gray-300" :style="{ backgroundColor: tokens.brandColor }"></div>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-xs text-gray-600">컨텐츠 너비</span>
                <span class="text-xs text-gray-900">{{ tokens.contentWidth }}</span>
              </div>
              <div class="flex items-center justify-between">
                <span class="text-xs text-gray-600">기본 폰트</span>
                <span class="text-xs text-gray-900">{{ tokens.fontBase }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import PageNode from './PageNode.vue'

const props = defineProps<{
  serverId: number
  pages: any[]
  currentPage: any
}>()

const emit = defineEmits<{
  'page-selected': [pageId: number]
  'page-created': [page?: any]
}>()

const activeTab = ref('pages')

const tabs = [
  { id: 'pages', name: '페이지' },
  { id: 'popups', name: '팝업' },
  { id: 'data', name: '데이터' }
]

const rootPages = computed(() => 
  props.pages.filter(p => p.parent_id === 0 || !p.parent_id)
)

const popups = computed(() => 
  props.pages.filter(p => p.type === 'popup')
)

const tokens = computed(() => ({
  brandColor: '#111111',
  contentWidth: '1080px',
  fontBase: 'Noto Sans KR, sans-serif'
}))
</script>

<style scoped>
.sidebar {
  height: 100%;
  display: flex;
  flex-direction: column;
  background: white;
}

.sidebar-header {
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.sidebar-title {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.btn-help {
  padding: 0.5rem;
  border: none;
  background: none;
  color: #6b7280;
  cursor: pointer;
  border-radius: 0.375rem;
  transition: all 0.2s;
}

.btn-help:hover {
  background: #f3f4f6;
  color: #374151;
}

.sidebar-content {
  flex: 1;
  overflow: auto;
  padding: 1rem;
}

.section {
  margin-bottom: 2rem;
}

.section:last-child {
  margin-bottom: 0;
}

.section-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.75rem 0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.popup-list,
.data-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.popup-item,
.data-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.5rem;
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  background: #f9fafb;
}

.popup-name,
.data-name {
  font-size: 0.875rem;
  color: #374151;
  flex: 1;
}

.popup-actions,
.data-actions {
  display: flex;
  gap: 0.25rem;
}

.btn-action {
  padding: 0.25rem;
  border: none;
  background: none;
  color: #6b7280;
  cursor: pointer;
  border-radius: 0.25rem;
  transition: all 0.2s;
}

.btn-action:hover {
  background: #e5e7eb;
  color: #374151;
}

.btn-add-popup {
  padding: 0.5rem;
  border: 1px dashed #d1d5db;
  border-radius: 0.375rem;
  background: white;
  color: #6b7280;
  cursor: pointer;
  font-size: 0.875rem;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  justify-content: center;
}

.btn-add-popup:hover {
  border-color: #9ca3af;
  color: #374151;
  background: #f9fafb;
}

.sidebar-footer {
  padding: 1rem;
  border-top: 1px solid #e5e7eb;
}

.btn-add-page {
  width: 100%;
  padding: 0.75rem;
  border: none;
  border-radius: 0.375rem;
  background: #3b82f6;
  color: white;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  justify-content: center;
}

.btn-add-page:hover {
  background: #2563eb;
}
</style>
