<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-semibold text-gray-900">텍스트 설정</h3>
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
        <!-- Text Content -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">텍스트 내용</label>
          <textarea
            v-model="textData.content"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="텍스트를 입력하세요"
          ></textarea>
        </div>

        <!-- Text Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">텍스트 타입</label>
          <select
            v-model="textData.type"
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
          <label class="block text-sm font-medium text-gray-700 mb-2">글자 크기: {{ textData.fontSize }}px</label>
          <input
            v-model.number="textData.fontSize"
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
            v-model="textData.fontWeight"
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
            v-model="textData.color"
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
              @click="textData.textAlign = align.value"
              :class="[
                'p-2 border rounded transition-colors',
                textData.textAlign === align.value
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

        <!-- Line Height -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">줄 간격: {{ textData.lineHeight }}</label>
          <input
            v-model.number="textData.lineHeight"
            type="range"
            min="1"
            max="3"
            step="0.1"
            class="w-full"
          />
        </div>

        <!-- Link (optional) -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">링크 (선택사항)</label>
          <input
            v-model="textData.link"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="https://example.com"
          />
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

const textData = ref({
  content: '텍스트',
  type: 'p',
  fontSize: 16,
  fontWeight: '400',
  color: '#000000',
  textAlign: 'left',
  lineHeight: 1.5,
  link: ''
})

const alignments = [
  { value: 'left', label: '왼쪽 정렬' },
  { value: 'center', label: '가운데 정렬' },
  { value: 'right', label: '오른쪽 정렬' }
]

onMounted(() => {
  if (props.element) {
    // 기존 텍스트 데이터가 있으면 로드
    Object.assign(textData.value, props.element.props || {})
  }
})

const applyChanges = () => {
  const updatedElement = {
    ...props.element,
    props: { ...textData.value }
  }
  emit('apply', updatedElement)
}
</script>
