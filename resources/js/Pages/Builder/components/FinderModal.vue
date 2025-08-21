<template>
  <div 
    v-if="open" 
    ref="finderModal"
    class="fixed z-50 w-80 bg-gray-800/95 backdrop-blur border border-gray-600 rounded-xl shadow-2xl text-white cursor-move"
    :style="{ left: position.x + 'px', top: position.y + 'px' }"
    @mousedown="startDrag"
  >
    <!-- Header -->
    <div class="flex items-center justify-between p-3 border-b border-gray-600 bg-gray-700/50 rounded-t-xl cursor-move">
      <div class="flex items-center space-x-3">
        <span class="text-sm font-medium">FINDER</span>
        <span class="text-xs text-gray-300">
          {{ selectedElementData?.name || '요소 선택' }}
          <span v-if="selectedElementId" class="text-blue-300">#{{ selectedElementId }}</span>
        </span>
      </div>
      <div class="flex items-center space-x-2">
        <button 
          @click="toggleCSSEditor" 
          :class="{ 'bg-blue-700': showCSSEditor, 'bg-blue-600': !showCSSEditor }"
          class="px-2 py-1 text-xs rounded hover:bg-blue-700 transition-colors"
        >
          CSS
        </button>
        <button class="p-1 text-gray-400 hover:text-white">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
        </button>
        <button class="p-1 text-gray-400 hover:text-white">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
          </svg>
        </button>
        <button @click="$emit('close')" class="p-1 text-gray-400 hover:text-white">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Content -->
    <div class="p-3 space-y-4">
      <!-- CSS Editor -->
      <div v-if="selectedElementId && showCSSEditor" class="space-y-2">
        <div class="text-xs text-gray-300">CSS 편집 - {{ selectedElementData?.name }}</div>
        
        <!-- Dimensions -->
        <div class="space-y-2">
          <div class="text-xs text-gray-300">크기</div>
          <div class="flex items-center justify-between">
            <span class="text-xs text-gray-300">W:</span>
            <input v-model="dimensions.width" type="number" class="w-16 px-2 py-1 text-xs bg-gray-700 border border-gray-600 rounded text-white" />
          </div>
          <div class="flex items-center justify-between">
            <span class="text-xs text-gray-300">H:</span>
            <input v-model="dimensions.height" type="number" class="w-16 px-2 py-1 text-xs bg-gray-700 border border-gray-600 rounded text-white" />
          </div>
          <div class="flex items-center space-x-2">
            <input type="checkbox" id="auto" class="w-3 h-3" />
            <label for="auto" class="text-xs text-gray-300">Auto</label>
          </div>
        </div>
      </div>
      
      <!-- No Selection Message -->
      <div v-else class="p-3 bg-gray-700/50 rounded text-center">
        <div class="text-xs text-gray-400 mb-2">CSS 편집</div>
        <div class="text-xs text-gray-500">요소를 선택하면 CSS를 편집할 수 있습니다</div>
      </div>

      <!-- Padding/Margin Visual -->
      <div v-if="selectedElementId" class="space-y-2">
        <div class="text-xs text-gray-300">Padding/Margin</div>
        <div class="grid grid-cols-2 gap-2">
          <div class="text-center">
            <input v-model="spacing.top" type="number" class="w-12 px-1 py-1 text-xs bg-gray-700 border border-gray-600 rounded text-white text-center" placeholder="0" />
            <div class="text-xs text-gray-400 mt-1">Top</div>
          </div>
          <div class="text-center">
            <input v-model="spacing.right" type="number" class="w-12 px-1 py-1 text-xs bg-gray-700 border border-gray-600 rounded text-white text-center" placeholder="0" />
            <div class="text-xs text-gray-400 mt-1">Right</div>
          </div>
          <div class="text-center">
            <input v-model="spacing.bottom" type="number" class="w-12 px-1 py-1 text-xs bg-gray-700 border border-gray-600 rounded text-white text-center" placeholder="0" />
            <div class="text-xs text-gray-400 mt-1">Bottom</div>
          </div>
          <div class="text-center">
            <input v-model="spacing.left" type="number" class="w-12 px-1 py-1 text-xs bg-gray-700 border border-gray-600 rounded text-white text-center" placeholder="0" />
            <div class="text-xs text-gray-400 mt-1">Left</div>
          </div>
        </div>
      </div>

      <!-- Element Tree -->
      <!-- CSS Editor Toggle Message -->
      <div v-if="!selectedElementId && showCSSEditor" class="text-center py-4">
        <div class="text-xs text-gray-400">요소를 선택하면 CSS 편집이 가능합니다</div>
      </div>

      <div class="space-y-2">
        <div class="text-xs text-gray-300">Elements</div>
        <div class="max-h-48 overflow-y-auto space-y-1">
          <div v-for="element in (pageElements || elementTree)" :key="element.id" class="text-xs">
            <div class="flex items-center space-x-2">
              <button @click="toggleElement(element.id)" class="text-gray-400 hover:text-white">
                <svg v-if="element.expanded" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                <svg v-else class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
              <span class="text-gray-400">{{ element.icon }}</span>
              <button @click="selectElement(element.id)" class="text-gray-200 hover:text-blue-300 text-left">
                {{ element.name }}
              </button>
            </div>
            <!-- Child elements with indentation -->
            <div v-if="element.expanded && element.children" class="ml-6 mt-1 space-y-1">
              <div v-for="child in element.children" :key="child.id" class="flex items-center space-x-2">
                <span class="text-gray-400">{{ child.icon }}</span>
                <button @click="selectElement(child.id)" class="text-gray-300 hover:text-blue-300 text-left">
                  {{ child.name }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search -->
      <div class="space-y-2">
        <div class="text-xs text-gray-300">Q Search</div>
        <input type="text" placeholder="Search elements..." class="w-full px-2 py-1 text-xs bg-gray-700 border border-gray-600 rounded text-white placeholder-gray-400" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const props = defineProps<{
  open: boolean
  selectedElement?: any
  pageElements?: any[]
}>()

const emit = defineEmits<{ 
  close: []
  'scroll-to-element': [elementId: string]
}>()

const dimensions = ref({ width: 1080, height: 673 })
const spacing = ref({ top: 0, right: 0, bottom: 0, left: 0 })

// 드래그 기능
const position = ref({ x: 20, y: 80 })
const isDragging = ref(false)
const dragOffset = ref({ x: 0, y: 0 })

const startDrag = (event: MouseEvent) => {
  if (event.target === event.currentTarget || (event.target as HTMLElement).classList.contains('cursor-move')) {
    isDragging.value = true
    const rect = (event.currentTarget as HTMLElement).getBoundingClientRect()
    dragOffset.value = {
      x: event.clientX - rect.left,
      y: event.clientY - rect.top
    }
    
    document.addEventListener('mousemove', onDrag)
    document.addEventListener('mouseup', stopDrag)
    event.preventDefault()
  }
}

const onDrag = (event: MouseEvent) => {
  if (isDragging.value) {
    position.value = {
      x: event.clientX - dragOffset.value.x,
      y: event.clientY - dragOffset.value.y
    }
  }
}

const stopDrag = () => {
  isDragging.value = false
  document.removeEventListener('mousemove', onDrag)
  document.removeEventListener('mouseup', stopDrag)
}

// 선택된 요소 상태
const selectedElementId = ref<string>('')
const selectedElementData = ref<any>(null)

// CSS 편집기 상태
const showCSSEditor = ref(false)

const toggleCSSEditor = () => {
  showCSSEditor.value = !showCSSEditor.value
}

const elementTree = ref([
  { 
    id: 1, 
    name: '게시판', 
    icon: '📋', 
    expanded: true,
    children: [
      { id: 11, name: '게시판 헤더', icon: '📝' },
      { id: 12, name: '게시판 목록', icon: '📋' },
      { id: 13, name: '게시판 푸터', icon: '🔚' }
    ]
  },
  { 
    id: 2, 
    name: '탭 프레임', 
    icon: '📑', 
    expanded: false,
    children: [
      { id: 21, name: '탭 1', icon: '1️⃣' },
      { id: 22, name: '탭 2', icon: '2️⃣' },
      { id: 23, name: '탭 3', icon: '3️⃣' }
    ]
  },
  { 
    id: 3, 
    name: '프레임', 
    icon: '⬜', 
    expanded: true,
    children: [
      { id: 31, name: '프레임 내부', icon: '🔲' },
      { id: 32, name: '프레임 경계', icon: '📐' }
    ]
  },
  { 
    id: 4, 
    name: 'A 텍스트', 
    icon: 'A', 
    expanded: false,
    children: [
      { id: 41, name: '텍스트 스타일', icon: '🎨' },
      { id: 42, name: '텍스트 정렬', icon: '📏' }
    ]
  },
  { 
    id: 5, 
    name: '버튼', 
    icon: '🔘', 
    expanded: false,
    children: [
      { id: 51, name: '버튼 스타일', icon: '🎨' },
      { id: 52, name: '버튼 링크', icon: '🔗' }
    ]
  },
  { 
    id: 6, 
    name: '프레임', 
    icon: '⬜', 
    expanded: false,
    children: [
      { id: 61, name: '프레임 내용', icon: '📄' }
    ]
  },
  { 
    id: 7, 
    name: '라인', 
    icon: '➖', 
    expanded: false,
    children: [
      { id: 71, name: '라인 스타일', icon: '🎨' },
      { id: 72, name: '라인 두께', icon: '📏' }
    ]
  },
  { 
    id: 8, 
    name: '네비게이션', 
    icon: '🧭', 
    expanded: false,
    children: [
      { id: 81, name: '메뉴 항목 1', icon: '📌' },
      { id: 82, name: '메뉴 항목 2', icon: '📌' },
      { id: 83, name: '메뉴 항목 3', icon: '📌' }
    ]
  }
])

const toggleElement = (id: number) => {
  const element = elementTree.value.find(e => e.id === id)
  if (element) {
    element.expanded = !element.expanded
  }
}

const scrollToElement = (elementId: string) => {
  emit('scroll-to-element', elementId)
}

const selectElement = (elementId: string) => {
  selectedElementId.value = elementId
  // 선택된 요소의 데이터 찾기
  if (props.pageElements) {
    const findElement = (elements: any[], id: string): any => {
      for (const element of elements) {
        if (element.id === id) return element
        if (element.children) {
          const found = findElement(element.children, id)
          if (found) return found
        }
      }
      return null
    }
    
    selectedElementData.value = findElement(props.pageElements, elementId)
    
    // 선택된 요소의 CSS 속성 로드
    if (selectedElementData.value) {
      loadElementCSS(selectedElementData.value)
    }
    
    // 요소 선택 후 자동으로 스크롤
    emit('scroll-to-element', elementId)
  }
}

const loadElementCSS = (element: any) => {
  // 선택된 요소의 CSS 속성을 dimensions와 spacing에 로드
  // 실제 구현에서는 요소의 현재 CSS 값을 가져와야 함
  console.log('Loading CSS for element:', element)
}
</script>
