<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-semibold text-gray-900">{{ element?.type || '요소' }} 설정</h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Tabs -->
      <div class="flex border-b border-gray-200 mb-6">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-4 py-2 text-sm font-medium border-b-2 transition-colors',
            activeTab === tab.id
              ? 'border-blue-500 text-blue-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
          ]"
        >
          {{ tab.name }}
        </button>
      </div>

      <!-- Tab Content -->
      <div class="space-y-6">
        <!-- Basic Tab -->
        <div v-if="activeTab === 'basic'" class="space-y-4">
          <!-- Content Width -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">컨텐츠 넓이</label>
            <div class="grid grid-cols-3 gap-3">
              <button
                v-for="width in contentWidths"
                :key="width.value"
                @click="element.props.contentWidth = width.value"
                :class="[
                  'p-3 border rounded-lg text-center transition-colors',
                  element.props.contentWidth === width.value
                    ? 'border-blue-500 bg-blue-50 text-blue-700'
                    : 'border-gray-300 hover:border-gray-400'
                ]"
              >
                <div class="text-sm font-medium">{{ width.label }}</div>
                <div class="text-xs text-gray-500">{{ width.description }}</div>
              </button>
            </div>
            <div v-if="element.props.contentWidth === 'custom'" class="mt-3">
              <input
                v-model.number="element.props.customWidth"
                type="number"
                placeholder="픽셀 값 입력"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <!-- Height -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">높이</label>
            <div class="flex items-center space-x-3">
              <label class="flex items-center">
                <input
                  v-model="element.props.heightType"
                  type="radio"
                  value="auto"
                  class="mr-2"
                />
                <span class="text-sm">자동</span>
              </label>
              <label class="flex items-center">
                <input
                  v-model="element.props.heightType"
                  type="radio"
                  value="custom"
                  class="mr-2"
                />
                <span class="text-sm">사용자 정의</span>
              </label>
            </div>
            <div v-if="element.props.heightType === 'custom'" class="mt-3">
              <input
                v-model.number="element.props.customHeight"
                type="number"
                placeholder="픽셀 값 입력"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <!-- Alignment -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">정렬</label>
            <div class="flex space-x-2">
              <button
                v-for="align in alignments"
                :key="align.value"
                @click="element.props.align = align.value"
                :class="[
                  'p-2 border rounded transition-colors',
                  element.props.align === align.value
                    ? 'border-blue-500 bg-blue-50 text-blue-700'
                    : 'border-gray-300 hover:border-gray-400'
                ]"
                :title="align.label"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="align.value === 'left'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h6"></path>
                  <path v-else-if="align.value === 'center'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                  <path v-else-if="align.value === 'right'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M6 18h14"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Border Tab -->
        <div v-if="activeTab === 'border'" class="space-y-4">
          <!-- Border Color -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">테두리 색상</label>
            <input
              v-model="element.props.borderColor"
              type="color"
              class="w-full h-10 border border-gray-300 rounded-md"
            />
          </div>

          <!-- Border Size -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">테두리 크기: {{ element.props.borderSize || 0 }}px</label>
            <input
              v-model.number="element.props.borderSize"
              type="range"
              min="0"
              max="10"
              class="w-full"
            />
          </div>

          <!-- Border Radius -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">라운딩: {{ element.props.borderRadius || 0 }}px</label>
            <input
              v-model.number="element.props.borderRadius"
              type="range"
              min="0"
              max="50"
              class="w-full"
            />
          </div>
        </div>

        <!-- Background Tab -->
        <div v-if="activeTab === 'background'" class="space-y-4">
          <!-- Background Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">배경 타입</label>
            <select
              v-model="element.props.backgroundType"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="solid">단색</option>
              <option value="gradient">그라데이션</option>
              <option value="image">이미지</option>
            </select>
          </div>

          <!-- Background Color -->
          <div v-if="element.props.backgroundType === 'solid'">
            <label class="block text-sm font-medium text-gray-700 mb-2">배경 색상</label>
            <input
              v-model="element.props.backgroundColor"
              type="color"
              class="w-full h-10 border border-gray-300 rounded-md"
            />
          </div>

          <!-- Background Image -->
          <div v-if="element.props.backgroundType === 'image'">
            <label class="block text-sm font-medium text-gray-700 mb-2">배경 이미지 URL</label>
            <input
              v-model="element.props.backgroundImage"
              type="text"
              placeholder="https://example.com/image.jpg"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>

        <!-- Link Tab -->
        <div v-if="activeTab === 'link'" class="space-y-4">
          <!-- Link URL -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">링크</label>
            <input
              v-model="element.props.link"
              type="text"
              placeholder="https://domain.com"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- Link Target -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">링크 타겟</label>
            <select
              v-model="element.props.linkTarget"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="_self">현재 창</option>
              <option value="_blank">새 창</option>
              <option value="_parent">부모 프레임</option>
              <option value="_top">최상위 프레임</option>
            </select>
          </div>

          <!-- Open in New Window -->
          <div class="flex items-center">
            <input
              v-model="element.props.openInNewWindow"
              type="checkbox"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label class="ml-2 text-sm text-gray-700">새 창에서 열기</label>
          </div>
        </div>

        <!-- Content Tab -->
        <div v-if="activeTab === 'content'" class="space-y-4">
          <!-- Text Content -->
          <div v-if="element.props.title !== undefined">
            <label class="block text-sm font-medium text-gray-700 mb-2">제목</label>
            <input
              v-model="element.props.title"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <div v-if="element.props.subtitle !== undefined">
            <label class="block text-sm font-medium text-gray-700 mb-2">부제목</label>
            <input
              v-model="element.props.subtitle"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <div v-if="element.props.button !== undefined">
            <label class="block text-sm font-medium text-gray-700 mb-2">버튼 텍스트</label>
            <input
              v-model="element.props.button"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <!-- Copy Content -->
          <div class="bg-gray-50 p-3 rounded-md">
            <label class="block text-sm font-medium text-gray-700 mb-2">컨텐츠 복사</label>
            <div class="flex items-center space-x-2">
              <button
                @click="copyContent"
                class="px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100"
              >
                복사하기
              </button>
              <input
                :value="element.id"
                readonly
                class="px-3 py-2 text-sm bg-gray-100 border border-gray-300 rounded-md text-gray-600"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-6">
        <button
          @click="$emit('close')"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          취소
        </button>
        <button
          @click="applyChanges"
          class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          적용
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const props = defineProps<{
  element: any
}>()

const emit = defineEmits<{
  close: []
  apply: [element: any]
}>()

const activeTab = ref('basic')

const tabs = [
  { id: 'basic', name: '기본' },
  { id: 'border', name: '테두리' },
  { id: 'background', name: '배경' },
  { id: 'link', name: '링크' },
  { id: 'content', name: '컨텐츠' }
]

const contentWidths = [
  { value: 'screen', label: '스크린', description: '전체 너비' },
  { value: 'default', label: '기본값', description: '1080px' },
  { value: 'custom', label: '사용자 정의', description: '픽셀 값' }
]

const alignments = [
  { value: 'left', label: '좌측 정렬' },
  { value: 'center', label: '중앙 정렬' },
  { value: 'right', label: '우측 정렬' }
]

// 기본값 설정
onMounted(() => {
  if (props.element) {
    // 기본 속성들이 없으면 초기화
    if (!props.element.props.contentWidth) {
      props.element.props.contentWidth = 'default'
    }
    if (!props.element.props.heightType) {
      props.element.props.heightType = 'auto'
    }
    if (!props.element.props.align) {
      props.element.props.align = 'center'
    }
    if (!props.element.props.borderColor === undefined) {
      props.element.props.borderColor = '#000000'
    }
    if (!props.element.props.borderSize === undefined) {
      props.element.props.borderSize = 0
    }
    if (!props.element.props.borderRadius === undefined) {
      props.element.props.borderRadius = 0
    }
    if (!props.element.props.backgroundType === undefined) {
      props.element.props.backgroundType = 'solid'
    }
    if (!props.element.props.backgroundColor === undefined) {
      props.element.props.backgroundColor = '#ffffff'
    }
    if (!props.element.props.link === undefined) {
      props.element.props.link = ''
    }
    if (!props.element.props.linkTarget === undefined) {
      props.element.props.linkTarget = '_self'
    }
    if (!props.element.props.openInNewWindow === undefined) {
      props.element.props.openInNewWindow = false
    }
  }
})

const copyContent = () => {
  navigator.clipboard.writeText(props.element.id)
  // 복사 완료 알림 (실제로는 toast나 alert 사용)
  alert('요소 ID가 복사되었습니다: ' + props.element.id)
}

const applyChanges = () => {
  emit('apply', props.element)
}
</script>
