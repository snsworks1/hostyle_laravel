<template>
  <div class="w-80 bg-white border-l border-gray-200 flex flex-col">
    <!-- Header -->
    <div class="p-4 border-b border-gray-200">
      <h3 class="text-lg font-medium text-gray-900">
        {{ selectedSection ? '섹션 속성' : '페이지 속성' }}
      </h3>
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-y-auto p-4">
      <!-- Section Properties -->
      <div v-if="selectedSection" class="space-y-6">
        <!-- Section Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">섹션 타입</label>
          <div class="px-3 py-2 bg-gray-50 border border-gray-200 rounded-md">
            <span class="text-sm text-gray-900 capitalize">{{ selectedSection.type }}</span>
          </div>
        </div>

        <!-- Section Props -->
        <div v-if="selectedSection.props" class="space-y-4">
          <div v-for="(value, key) in selectedSection.props" :key="key">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ getPropLabel(key) }}</label>
            
            <!-- Text Input -->
            <input
              v-if="typeof value === 'string'"
              v-model="selectedSection.props[key]"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @input="updateSectionProps"
            />
            
            <!-- Number Input -->
            <input
              v-else-if="typeof value === 'number'"
              v-model.number="selectedSection.props[key]"
              type="number"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              @input="updateSectionProps"
            />
            
            <!-- Boolean Input -->
            <div v-else-if="typeof value === 'boolean'" class="flex items-center">
              <input
                v-model="selectedSection.props[key]"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                @change="updateSectionProps"
              />
              <label class="ml-2 text-sm text-gray-700">{{ key }}</label>
            </div>
            
            <!-- Array Input (Features) -->
            <div v-else-if="Array.isArray(value) && key === 'features'" class="space-y-3">
              <div
                v-for="(feature, index) in value"
                :key="index"
                class="p-3 border border-gray-200 rounded-md"
              >
                <div class="grid grid-cols-2 gap-2 mb-2">
                  <input
                    v-model="feature.title"
                    type="text"
                    placeholder="제목"
                    class="px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                    @input="updateSectionProps"
                  />
                  <input
                    v-model="feature.icon"
                    type="text"
                    placeholder="아이콘"
                    class="px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                    @input="updateSectionProps"
                  />
                </div>
                <textarea
                  v-model="feature.description"
                  placeholder="설명"
                  rows="2"
                  class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                  @input="updateSectionProps"
                ></textarea>
              </div>
              <button
                @click="addFeature"
                class="w-full px-3 py-2 text-sm text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100"
              >
                + 기능 추가
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Page Properties (Hidden in Builder) -->
      <div v-else-if="page" class="space-y-6">
        <div class="p-4 border border-gray-200 rounded-md bg-gray-50">
          <h4 class="text-sm font-medium text-gray-900 mb-2">페이지 속성 관리</h4>
          <p class="text-xs text-gray-600 mb-3">페이지 이름/타입/SEO/옵션 등은 빌더 밖의 설정 화면에서 관리합니다.</p>
          <a
            :href="`/server/${serverId}/settings/seo`"
            target="_blank"
            class="inline-flex items-center px-3 py-2 text-xs font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700"
          >
            SEO 설정으로 이동
          </a>
        </div>
      </div>

      <!-- No Selection -->
      <div v-else class="text-center py-12">
        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
          <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
        </div>
        <h3 class="text-sm font-medium text-gray-900 mb-2">선택된 항목이 없습니다</h3>
        <p class="text-xs text-gray-500">편집할 섹션이나 페이지를 선택하세요</p>
      </div>
    </div>

    <!-- Quick Actions -->
    <div v-if="!selectedSection && page" class="p-4 border-t border-gray-200">
      <h4 class="text-sm font-medium text-gray-900 mb-3">빠른 섹션 추가</h4>
      <div class="grid grid-cols-2 gap-2">
        <button
          @click="addSection('hero')"
          class="px-3 py-2 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100"
        >
          Hero
        </button>
        <button
          @click="addSection('features')"
          class="px-3 py-2 text-xs font-medium text-green-600 bg-green-50 border border-green-200 rounded-md hover:bg-green-100"
        >
          Features
        </button>
        <button
          @click="addSection('cta')"
          class="px-3 py-2 text-xs font-medium text-purple-600 bg-purple-50 border border-purple-200 rounded-md hover:bg-purple-100"
        >
          CTA
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  selectedSection: any
  page: any
  serverId?: number
}>()

const emit = defineEmits<{
  'update-section': [sectionId: string, props: any]
  'add-section': [sectionType: string]
}>()

const updateSectionProps = () => {
  if (props.selectedSection) {
    emit('update-section', props.selectedSection.id.toString(), props.selectedSection.props)
  }
}

const updatePageName = () => {
  // 페이지 이름 업데이트 로직
}

const updatePageType = () => {
  // 페이지 타입 업데이트 로직
}

const updatePageSeo = () => {
  // SEO 설정 업데이트 로직
}

const updatePageOptions = () => {
  // 페이지 옵션 업데이트 로직
}

const addFeature = () => {
  if (props.selectedSection?.props?.features) {
    props.selectedSection.props.features.push({
      title: '새 기능',
      description: '기능 설명',
      icon: 'star'
    })
    updateSectionProps()
  }
}

const addSection = (type: string) => {
  emit('add-section', type)
}

const getPropLabel = (key: string) => {
  const labels: Record<string, string> = {
    title: '제목',
    subtitle: '부제목',
    align: '정렬',
    py: '상하 여백',
    button: '버튼 텍스트',
    url: '링크 URL',
    features: '기능 목록'
  }
  return labels[key] || key
}
</script>

<style scoped>
.inspector-panel {
  width: 320px;
  background: white;
  border-left: 1px solid #e5e7eb;
  display: flex;
  flex-direction: column;
  height: 100%;
}

.panel-header {
  padding: 1rem;
  border-bottom: 1px solid #e5e7eb;
  background: #f9fafb;
}

.panel-title {
  font-size: 1rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.panel-content {
  flex: 1;
  overflow: auto;
  padding: 1rem;
}

.no-selection {
  height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.no-selection-content {
  text-align: center;
}

.section-inspector {
  margin-bottom: 2rem;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.section-title {
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
  margin: 0;
  text-transform: capitalize;
}

.btn-remove {
  padding: 0.5rem;
  border: none;
  background: none;
  color: #ef4444;
  cursor: pointer;
  border-radius: 0.375rem;
  transition: all 0.2s;
}

.btn-remove:hover {
  background: #fef2f2;
}

.section-form {
  margin-bottom: 1rem;
}

.add-section {
  border-top: 1px solid #e5e7eb;
  padding-top: 1rem;
}

.add-section-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 1rem 0;
}

.section-buttons {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.btn-add-section {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background: white;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s;
  text-align: left;
  width: 100%;
}

.btn-add-section:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.section-icon {
  width: 1rem;
  color: #6b7280;
}

.section-name {
  font-size: 0.875rem;
  font-weight: 500;
}
</style>

