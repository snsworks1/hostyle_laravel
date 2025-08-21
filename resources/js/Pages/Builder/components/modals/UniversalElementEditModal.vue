<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-semibold text-gray-900">
          {{ getModalTitle() }} 설정
        </h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="space-y-6">
        <!-- Element Type Indicator -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
          <div class="flex items-center space-x-2">
            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
            <span class="text-sm font-medium text-blue-800">
              {{ getElementTypeLabel() }} 편집 중
            </span>
          </div>
          <p class="text-sm text-blue-600 mt-1">
            {{ element.id }} - {{ element.sectionId }}
          </p>
        </div>

        <!-- Dynamic Form Based on Element Type -->
        <div v-if="element.type === 'text'" class="space-y-4">
          <!-- Text Content -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">텍스트 내용</label>
            <textarea
              v-model="formData.content"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="텍스트를 입력하세요"
            ></textarea>
          </div>

          <!-- Text Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">텍스트 타입</label>
            <select
              v-model="formData.type"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="h1">제목 1 (H1)</option>
              <option value="h2">제목 2 (H2)</option>
              <option value="h3">제목 3 (H3)</option>
              <option value="p">본문 (P)</option>
              <option value="span">인라인 텍스트</option>
            </select>
          </div>

          <!-- Font Size -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">글자 크기: {{ formData.fontSize }}px</label>
            <input
              v-model.number="formData.fontSize"
              type="range"
              min="12"
              max="72"
              class="w-full"
            />
          </div>

          <!-- Font Weight -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">글자 굵기</label>
            <select
              v-model="formData.fontWeight"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="300">Light (300)</option>
              <option value="400">Normal (400)</option>
              <option value="500">Medium (500)</option>
              <option value="600">SemiBold (600)</option>
              <option value="700">Bold (700)</option>
              <option value="800">ExtraBold (800)</option>
            </select>
          </div>

          <!-- Text Color -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">텍스트 색상</label>
            <input
              v-model="formData.color"
              type="color"
              class="w-full h-10 border border-gray-300 rounded-md"
            />
          </div>

          <!-- Text Alignment -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">정렬</label>
            <div class="flex space-x-2">
              <button
                v-for="align in alignments"
                :key="align.value"
                @click="formData.textAlign = align.value"
                :class="[
                  'p-2 border rounded transition-colors',
                  formData.textAlign === align.value
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

        <div v-else-if="element.type === 'button'" class="space-y-4">
          <!-- Button Text -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">버튼 텍스트</label>
            <input
              v-model="formData.text"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="버튼 텍스트를 입력하세요"
            />
          </div>

          <!-- Button Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">버튼 타입</label>
            <select
              v-model="formData.type"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="primary">주요 버튼</option>
              <option value="secondary">보조 버튼</option>
              <option value="outline">테두리 버튼</option>
              <option value="ghost">투명 버튼</option>
            </select>
          </div>

          <!-- Button Size -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">버튼 크기</label>
            <div class="grid grid-cols-3 gap-2">
              <button
                v-for="size in sizes"
                :key="size.value"
                @click="formData.size = size.value"
                :class="[
                  'p-2 text-sm border rounded transition-colors',
                  formData.size === size.value
                    ? 'border-blue-500 bg-blue-50 text-blue-700'
                    : 'border-gray-300 hover:border-gray-400'
                ]"
              >
                {{ size.label }}
              </button>
            </div>
          </div>

          <!-- Link URL -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">링크 URL</label>
            <input
              v-model="formData.url"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="https://example.com"
            />
          </div>

          <!-- Button Colors -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">배경 색상</label>
              <input
                v-model="formData.backgroundColor"
                type="color"
                class="w-full h-10 border border-gray-300 rounded-md"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">텍스트 색상</label>
              <input
                v-model="formData.textColor"
                type="color"
                class="w-full h-10 border border-gray-300 rounded-md"
              />
            </div>
          </div>

          <!-- Border Radius -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">모서리 둥글기: {{ formData.borderRadius }}px</label>
            <input
              v-model.number="formData.borderRadius"
              type="range"
              min="0"
              max="25"
              class="w-full"
            />
          </div>
        </div>

        <div v-else-if="element.type === 'icon'" class="space-y-4">
          <!-- Icon Selection -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label class="block text-sm font-medium text-gray-700">아이콘 선택</label>
              <button
                @click="showIconUploadModal = true"
                class="px-3 py-1 text-sm bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
              >
                아이콘 업로드
              </button>
            </div>
            <div class="grid grid-cols-4 gap-3">
              <button
                v-for="icon in availableIcons"
                :key="icon.value"
                @click="formData.icon = icon.value"
                :class="[
                  'p-3 border rounded-lg transition-colors flex flex-col items-center space-y-2',
                  formData.icon === icon.value
                    ? 'border-blue-500 bg-blue-50'
                    : 'border-gray-300 hover:border-gray-400'
                ]"
              >
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="icon.value === 'star'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                  <path v-else-if="icon.value === 'heart'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                  <path v-else-if="icon.value === 'lightning'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span class="text-xs text-gray-600">{{ icon.label }}</span>
              </button>
            </div>
          </div>

          <!-- Icon Size -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">아이콘 크기: {{ formData.size }}px</label>
            <input
              v-model.number="formData.size"
              type="range"
              min="16"
              max="96"
              class="w-full"
            />
          </div>

          <!-- Icon Color -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">아이콘 색상</label>
            <input
              v-model="formData.color"
              type="color"
              class="w-full h-10 border border-gray-300 rounded-md"
            />
          </div>

          <!-- Background Color -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">배경 색상</label>
            <input
              v-model="formData.backgroundColor"
              type="color"
              class="w-full h-10 border border-gray-300 rounded-md"
            />
          </div>
        </div>

        <!-- Common Properties for All Elements -->
        <div class="border-t pt-6 space-y-4">
          <h4 class="text-lg font-medium text-gray-900">공통 속성</h4>
          
          <!-- Element ID -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">요소 ID</label>
            <div class="flex space-x-2">
              <input
                v-model="formData.id"
                type="text"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="요소 ID"
              />
              <button
                @click="copyElementId"
                class="px-3 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors"
                title="ID 복사"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Custom CSS Classes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">CSS 클래스 (선택사항)</label>
            <input
              v-model="formData.cssClasses"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="custom-class another-class"
            />
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

    <!-- Icon Upload Modal -->
    <IconUploadModal
      v-if="showIconUploadModal"
      :server="server"
      @close="showIconUploadModal = false"
      @icon-selected="onIconSelected"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import IconUploadModal from './IconUploadModal.vue'

const props = defineProps<{
  element: any
  server?: any
}>()

const emit = defineEmits<{
  close: []
  apply: [element: any]
}>()

// Form data based on element type
const formData = ref<any>({})

// Icon upload modal state
const showIconUploadModal = ref(false)

// Common properties
const alignments = [
  { value: 'left', label: '왼쪽 정렬' },
  { value: 'center', label: '가운데 정렬' },
  { value: 'right', label: '오른쪽 정렬' }
]

const sizes = [
  { value: 'small', label: '작음' },
  { value: 'medium', label: '보통' },
  { value: 'large', label: '큼' }
]

const availableIcons = [
  { value: 'star', label: '별' },
  { value: 'heart', label: '하트' },
  { value: 'lightning', label: '번개' },
  { value: 'default', label: '기본' }
]

// Computed properties
const getModalTitle = () => {
  const typeLabels: { [key: string]: string } = {
    text: '텍스트',
    button: '버튼',
    icon: '아이콘'
  }
  return typeLabels[props.element?.type] || '요소'
}

const getElementTypeLabel = () => {
  const typeLabels: { [key: string]: string } = {
    text: '텍스트',
    button: '버튼',
    icon: '아이콘'
  }
  return typeLabels[props.element?.type] || '요소'
}

// Initialize form data based on element type
onMounted(() => {
  if (props.element) {
    if (props.element.type === 'text') {
      formData.value = {
        content: props.element.props?.content || '텍스트',
        type: props.element.props?.type || 'p',
        fontSize: props.element.props?.fontSize || 16,
        fontWeight: props.element.props?.fontWeight || '400',
        color: props.element.props?.color || '#000000',
        textAlign: props.element.props?.textAlign || 'left',
        lineHeight: props.element.props?.lineHeight || 1.5,
        link: props.element.props?.link || '',
        id: props.element.id || '',
        cssClasses: props.element.props?.cssClasses || ''
      }
    } else if (props.element.type === 'button') {
      formData.value = {
        text: props.element.props?.text || '버튼',
        type: props.element.props?.type || 'primary',
        size: props.element.props?.size || 'medium',
        url: props.element.props?.url || '',
        target: props.element.props?.target || '_self',
        backgroundColor: props.element.props?.backgroundColor || '#3B82F6',
        textColor: props.element.props?.textColor || '#FFFFFF',
        borderRadius: props.element.props?.borderRadius || 6,
        icon: props.element.props?.icon || '',
        id: props.element.id || '',
        cssClasses: props.element.props?.cssClasses || ''
      }
    } else if (props.element.type === 'icon') {
      formData.value = {
        icon: props.element.props?.icon || 'star',
        color: props.element.props?.color || '#3B82F6',
        size: props.element.props?.size || 48,
        backgroundColor: props.element.props?.backgroundColor || '#DBEAFE',
        id: props.element.id || '',
        cssClasses: props.element.props?.cssClasses || ''
      }
    }
  }
})

const copyElementId = () => {
  navigator.clipboard.writeText(formData.value.id)
}

const applyChanges = () => {
  const updatedElement = {
    ...props.element,
    id: formData.value.id,
    props: { ...formData.value }
  }
  emit('apply', updatedElement)
}

const onIconSelected = (icon: any) => {
  // 업로드된 아이콘 정보를 formData에 설정
  formData.value.icon = icon.type || icon.name
  formData.value.iconUrl = icon.url
  formData.value.iconSize = icon.size || 48
  formData.value.iconColor = icon.color || '#3B82F6'
  formData.value.backgroundColor = icon.backgroundColor || '#DBEAFE'
  
  console.log('Icon selected:', icon)
}
</script>
