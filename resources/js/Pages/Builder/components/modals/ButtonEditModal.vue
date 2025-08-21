<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-semibold text-gray-900">버튼 설정</h3>
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
      <div class="space-y-4">
        <!-- Button Text -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">버튼 텍스트</label>
          <input
            v-model="buttonData.text"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="버튼 텍스트를 입력하세요"
          />
        </div>

        <!-- Button Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">버튼 타입</label>
          <select
            v-model="buttonData.type"
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
              @click="buttonData.size = size.value"
              :class="[
                'p-2 text-sm border rounded transition-colors',
                buttonData.size === size.value
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
            v-model="buttonData.url"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="https://example.com"
          />
        </div>

        <!-- Link Target -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">링크 열기 방식</label>
          <div class="flex items-center space-x-4">
            <label class="flex items-center">
              <input
                v-model="buttonData.target"
                type="radio"
                value="_self"
                class="mr-2"
              />
              <span class="text-sm">현재 창</span>
            </label>
            <label class="flex items-center">
              <input
                v-model="buttonData.target"
                type="radio"
                value="_blank"
                class="mr-2"
              />
              <span class="text-sm">새 창</span>
            </label>
          </div>
        </div>

        <!-- Button Colors -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">배경 색상</label>
            <input
              v-model="buttonData.backgroundColor"
              type="color"
              class="w-full h-10 border border-gray-300 rounded-md"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">텍스트 색상</label>
            <input
              v-model="buttonData.textColor"
              type="color"
              class="w-full h-10 border border-gray-300 rounded-md"
            />
          </div>
        </div>

        <!-- Border Radius -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">모서리 둥글기: {{ buttonData.borderRadius }}px</label>
          <input
            v-model.number="buttonData.borderRadius"
            type="range"
            min="0"
            max="25"
            class="w-full"
          />
        </div>

        <!-- Icon -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">아이콘 (선택사항)</label>
          <select
            v-model="buttonData.icon"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">아이콘 없음</option>
            <option value="arrow-right">→ 오른쪽 화살표</option>
            <option value="download">⬇ 다운로드</option>
            <option value="external">🔗 외부 링크</option>
            <option value="play">▶ 재생</option>
          </select>
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

const buttonData = ref({
  text: '버튼',
  type: 'primary',
  size: 'medium',
  url: '',
  target: '_self',
  backgroundColor: '#3B82F6',
  textColor: '#FFFFFF',
  borderRadius: 6,
  icon: ''
})

const sizes = [
  { value: 'small', label: '작음' },
  { value: 'medium', label: '보통' },
  { value: 'large', label: '큼' }
]

onMounted(() => {
  if (props.element) {
    // 기존 버튼 데이터가 있으면 로드
    Object.assign(buttonData.value, props.element.props || {})
  }
})

const applyChanges = () => {
  const updatedElement = {
    ...props.element,
    props: { ...buttonData.value }
  }
  emit('apply', updatedElement)
}
</script>
