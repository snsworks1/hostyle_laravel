<template>
  <div class="canvas-pane">
    <!-- 디바이스 스위처 -->
    <div class="device-switcher">
      <button 
        v-for="device in devices" 
        :key="device.name"
        :class="['device-btn', { active: ui.device === device.name.toLowerCase() }]"
        @click="switchDevice(device.name.toLowerCase())"
        :title="device.name"
      >
        <i :class="device.icon"></i>
        <span>{{ device.name }}</span>
      </button>
    </div>

    <!-- 캔버스 영역 -->
    <div class="canvas-container" :class="`device-${ui.device}`">
      <div id="gjs" class="gjs-canvas"></div>
      
      <!-- 블록 매니저 -->
      <div class="blocks-container">
        <div class="blocks-header">
          <h3>블록</h3>
          <button class="toggle-blocks" @click="toggleBlocks">
            <i :class="blocksVisible ? 'fas fa-chevron-down' : 'fas fa-chevron-up'"></i>
          </button>
        </div>
        <div id="blocks" class="blocks-content" :class="{ 'blocks-hidden': !blocksVisible }"></div>
      </div>
    </div>

    <!-- 로딩 인디케이터 -->
    <div v-if="isLoading" class="loading-overlay">
      <div class="loading-spinner">
        <i class="fas fa-spinner fa-spin"></i>
        <p>에디터 로딩 중...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useBuilderStore } from '../../stores/builderStore'

// Store
const store = useBuilderStore()

// Reactive data
const isLoading = ref(true)
const blocksVisible = ref(true)

// Computed
const ui = computed(() => store.ui)

// 디바이스 목록
const devices = [
  { name: 'Desktop', icon: 'fas fa-desktop' },
  { name: 'Tablet', icon: 'fas fa-tablet-alt' },
  { name: 'Mobile', icon: 'fas fa-mobile-alt' }
]

// Methods
function switchDevice(name) {
  store.switchDevice(name)
}

function toggleBlocks() {
  blocksVisible.value = !blocksVisible.value
}

// GrapesJS 초기화
async function initGrapesJS() {
  try {
    isLoading.value = true
    
    // 에디터 초기화
    await store.initEditor({
      canvasEl: '#gjs',
      blocksEl: '#blocks',
      layersEl: '#layers-container',
      stylesEl: '#style-manager'
    })
    
    // 초기 HTML 추가
    if (store.editor) {
      store.editor.addComponents(`
        <div style="padding: 40px; text-align: center; color: #666; font-family: Inter, sans-serif;">
          <h1 style="color: #667eea; margin-bottom: 20px;">웹빌더 에디터</h1>
          <p style="font-size: 16px; line-height: 1.6;">
            좌측에서 블록을 드래그하여 시작하세요.<br>
            우측 패널에서 레이어와 스타일을 관리할 수 있습니다.
          </p>
        </div>
      `)
      
      // 이벤트 리스너 추가
      store.editor.on('component:add', () => {
        store.markChanged()
      })
      
      store.editor.on('component:update', () => {
        store.markChanged()
      })
      
      store.editor.on('component:remove', () => {
        store.markChanged()
      })
      
      store.editor.on('style:change', () => {
        store.markChanged()
      })
      
      store.editor.on('undo:change', () => {
        store.updateUndoRedo()
      })
      
      store.editor.on('redo:change', () => {
        store.updateUndoRedo()
      })
    }
    
  } catch (error) {
    console.error('GrapesJS 초기화 실패:', error)
  } finally {
    isLoading.value = false
  }
}

// 컴포넌트 마운트 시 GrapesJS 초기화
onMounted(() => {
  initGrapesJS()
})

// 컴포넌트 언마운트 시 정리
onUnmounted(() => {
  if (store.editor) {
    store.editor.destroy()
  }
})
</script>

<style scoped>
.canvas-pane {
  position: relative;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.device-switcher {
  display: flex;
  justify-content: center;
  padding: 16px;
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.device-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  margin: 0 4px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 8px;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
  font-size: 14px;
}

.device-btn:hover {
  border-color: #667eea;
  color: #667eea;
}

.device-btn.active {
  background: #667eea;
  border-color: #667eea;
  color: white;
}

.canvas-container {
  flex: 1;
  position: relative;
  overflow: hidden;
}

.gjs-canvas {
  width: 100%;
  height: 100%;
}

/* 디바이스별 캔버스 크기 */
.device-desktop .gjs-canvas {
  max-width: 100%;
}

.device-tablet .gjs-canvas {
  max-width: 768px;
  margin: 0 auto;
  border-left: 1px solid #e5e7eb;
  border-right: 1px solid #e5e7eb;
}

.device-mobile .gjs-canvas {
  max-width: 375px;
  margin: 0 auto;
  border-left: 1px solid #e5e7eb;
  border-right: 1px solid #e5e7eb;
}

.blocks-container {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: white;
  border-top: 1px solid #e5e7eb;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

.blocks-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  background: #f8fafc;
  border-bottom: 1px solid #e5e7eb;
}

.blocks-header h3 {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.toggle-blocks {
  background: none;
  border: none;
  cursor: pointer;
  color: #6b7280;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s;
}

.toggle-blocks:hover {
  background: #e5e7eb;
  color: #374151;
}

.blocks-content {
  height: 200px;
  overflow-y: auto;
  transition: height 0.3s ease;
}

.blocks-content.blocks-hidden {
  height: 0;
  overflow: hidden;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.loading-spinner {
  text-align: center;
  color: #667eea;
}

.loading-spinner i {
  font-size: 48px;
  margin-bottom: 16px;
}

.loading-spinner p {
  margin: 0;
  font-size: 16px;
  color: #6b7280;
}

/* GrapesJS 스타일 오버라이드 */
:deep(.gjs-cv-canvas) {
  top: 0 !important;
}

:deep(.gjs-block-category) {
  border: none !important;
  margin-bottom: 8px !important;
}

:deep(.gjs-block-category .gjs-title) {
  background: #f3f4f6 !important;
  color: #374151 !important;
  font-weight: 600 !important;
  padding: 8px 12px !important;
  border-radius: 6px !important;
}

:deep(.gjs-block) {
  border: 1px solid #e5e7eb !important;
  border-radius: 6px !important;
  margin: 4px !important;
  transition: all 0.2s !important;
}

:deep(.gjs-block:hover) {
  border-color: #667eea !important;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2) !important;
}

:deep(.gjs-block-label) {
  color: #6b7280 !important;
  font-size: 12px !important;
  font-weight: 500 !important;
}
</style>
