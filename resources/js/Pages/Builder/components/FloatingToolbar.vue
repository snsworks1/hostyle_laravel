<template>
  <div 
    class="fixed z-40 top-24 left-4 w-12 bg-white/90 backdrop-blur border border-gray-200 rounded-xl shadow-lg select-none"
    :style="{ transform: `translate(${pos.x}px, ${pos.y}px)` }"
    @mousedown.stop
  >
    <!-- Drag handle -->
    <div 
      class="cursor-move p-2 border-b border-gray-200 flex items-center justify-center bg-gray-50 rounded-t-xl"
      @mousedown="startDrag"
    >
      <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16M4 14h16" />
      </svg>
    </div>

    <!-- Tool icons -->
    <div class="flex flex-col py-2">
      <button v-for="tool in tools" :key="tool.id" :title="tool.title" class="p-2 hover:bg-gray-100" @click="$emit('open-palette', tool.id)">
        <img :src="tool.icon" class="w-6 h-6" />
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'

const emit = defineEmits<{ 'open-palette': [id: string] }>()

const pos = ref({ x: 0, y: 0 })
let dragging = false
let start = { x: 0, y: 0 }

const startDrag = (e: MouseEvent) => {
  dragging = true
  start = { x: e.clientX - pos.value.x, y: e.clientY - pos.value.y }
  window.addEventListener('mousemove', onMove)
  window.addEventListener('mouseup', endDrag)
}

const onMove = (e: MouseEvent) => {
  if (!dragging) return
  pos.value = { x: e.clientX - start.x, y: e.clientY - start.y }
}

const endDrag = () => {
  dragging = false
  window.removeEventListener('mousemove', onMove)
  window.removeEventListener('mouseup', endDrag)
}

onBeforeUnmount(() => endDrag())

const tools = [
  { id: 'dsn', title: '디자인된 컨텐츠', icon: '/img/e/side/ic_cube.svg' },
  { id: 'frm', title: '프레임', icon: '/img/e/side/ic_frame.svg' },
  { id: 'txt', title: '텍스트', icon: '/img/e/side/ic_text.svg' },
  { id: 'img', title: '이미지', icon: '/img/e/side/ic_image.svg' },
  { id: 'ico', title: '아이콘', icon: '/img/e/side/ic_emogi.svg' },
  { id: 'btn', title: '버튼', icon: '/img/e/side/ic_btn.svg' },
  { id: 'sld', title: '슬라이드', icon: '/img/e/side/ic_carousel.svg' },
  { id: 'lin', title: '라인', icon: '/img/e/side/ic_line.svg' },
  { id: 'spa', title: '여백', icon: '/img/e/side/ic_blank.svg' },
  { id: 'vdo', title: '동영상', icon: '/img/e/side/ic_video.svg' },
  { id: 'map', title: '지도', icon: '/img/e/side/ic_map.svg' },
  { id: 'sns', title: '소셜미디어', icon: '/img/e/side/ic_sns.svg' },
  { id: 'tbl', title: '테이블', icon: '/img/e/side/ic_table.svg' },
  { id: 'fom', title: '폼', icon: '/img/e/side/ic_form.svg' },
  { id: 'brd', title: '게시판', icon: '/img/e/side/ic_board.svg' },
  { id: 'gal', title: '갤러리', icon: '/img/e/side/ic_grid.svg' },
  { id: 'cal', title: '캘린더', icon: '/img/e/side/ic_calendar.svg' },
  { id: 'tab', title: '탭 프레임', icon: '/img/e/side/ic_tab.svg' },
  { id: 'lnb', title: '네비게이션 바', icon: '/img/e/side/ic_navi.svg' },
  { id: 'dbc', title: '데이터 컨텐츠', icon: '/img/e/side/ic_data.svg' },
  { id: 'ifm', title: 'HTML / Iframe', icon: '/img/e/side/ic_copy.svg' },
  { id: 'ele', title: '사용자 정의 요소', icon: '/img/e/side/ic_copy.svg' }
]
</script>

<style scoped>
</style>


