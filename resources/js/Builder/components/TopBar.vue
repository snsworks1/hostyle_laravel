<template>
  <div class="top-bar">
    <div class="top-bar-left">
      <!-- 프로젝트 정보 -->
      <div class="project-info">
        <h1 class="project-name">{{ project.name || '새 프로젝트' }}</h1>
        <span class="project-status" :class="`status-${project.status}`">
          {{ getStatusText(project.status) }}
        </span>
      </div>
    </div>

    <div class="top-bar-center">
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
        </button>
      </div>

      <!-- 가이드/스냅 토글 -->
      <div class="view-controls">
        <button 
          class="control-btn"
          @click="toggleUnifiedPanel"
          :title="ui.rightPanels.unified.visible ? '패널 숨기기' : '패널 보이기'"
        >
          <i :class="ui.rightPanels.unified.visible ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
        </button>
        <button 
          :class="['control-btn', { active: ui.guides }]"
          @click="toggleGuides"
          title="가이드 표시"
        >
          <i class="fas fa-ruler-combined"></i>
        </button>
        <button 
          :class="['control-btn', { active: ui.snap }]"
          @click="toggleSnap"
          title="스냅 활성화"
        >
          <i class="fas fa-magnet"></i>
        </button>
      </div>
    </div>

    <div class="top-bar-right">
      <!-- Undo/Redo -->
      <div class="history-controls">
        <button 
          class="history-btn"
          :disabled="!canUndo"
          @click="undo"
          title="실행 취소"
        >
          <i class="fas fa-undo"></i>
        </button>
        <button 
          class="history-btn"
          :disabled="!canRedo"
          @click="redo"
          title="다시 실행"
        >
          <i class="fas fa-redo"></i>
        </button>
      </div>

      <!-- 메인 액션 버튼들 -->
      <div class="main-actions">
        <button 
          class="action-btn save-btn"
          :disabled="!canSave || isSaving"
          @click="saveProject"
        >
          <i v-if="isSaving" class="fas fa-spinner fa-spin"></i>
          <i v-else class="fas fa-save"></i>
          {{ isSaving ? '저장 중...' : '저장' }}
        </button>

        <button 
          class="action-btn preview-btn"
          @click="showPreviewModal = true"
        >
          <i class="fas fa-eye"></i>
          미리보기
        </button>

        <button 
          class="action-btn publish-btn"
          @click="showPublishModal = true"
        >
          <i class="fas fa-rocket"></i>
          게시
        </button>
      </div>

      <!-- 도움말 -->
      <button class="help-btn" @click="showHelp = true" title="도움말">
        <i class="fas fa-question-circle"></i>
      </button>
    </div>

    <!-- 모달들 -->
    <PreviewModal 
      v-if="showPreviewModal" 
      @close="showPreviewModal = false"
      @preview="handlePreview"
    />
    
    <PublishModal 
      v-if="showPublishModal" 
      @close="showPublishModal = false"
      @publish="handlePublish"
    />

    <!-- 도움말 모달 -->
    <div v-if="showHelp" class="help-modal" @click="showHelp = false">
      <div class="help-content" @click.stop>
        <div class="help-header">
          <h3>웹빌더 도움말</h3>
          <button class="close-btn" @click="showHelp = false">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="help-body">
          <h4>단축키</h4>
          <ul>
            <li><kbd>Ctrl + S</kbd> - 저장</li>
            <li><kbd>Ctrl + Z</kbd> - 실행 취소</li>
            <li><kbd>Ctrl + Y</kbd> - 다시 실행</li>
            <li><kbd>Ctrl + P</kbd> - 미리보기</li>
            <li><kbd>Delete</kbd> - 선택된 요소 삭제</li>
          </ul>
          
          <h4>사용법</h4>
          <ul>
            <li>좌측에서 블록을 드래그하여 캔버스에 추가</li>
            <li>우측 패널에서 레이어와 스타일 관리</li>
            <li>패널은 자유롭게 드래그하여 위치 조정 가능</li>
            <li>테마 설정으로 전체적인 디자인 통일성 확보</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useBuilderStore } from '../stores/builderStore'
import PreviewModal from './modals/PreviewModal.vue'
import PublishModal from './modals/PublishModal.vue'

// Store
const store = useBuilderStore()

// Reactive data
const showPreviewModal = ref(false)
const showPublishModal = ref(false)
const showHelp = ref(false)

// Computed
const project = computed(() => store.project)
const ui = computed(() => store.ui)
const canSave = computed(() => store.flags.canSave)
const isSaving = computed(() => store.flags.isSaving)
const canUndo = computed(() => store.canUndo)
const canRedo = computed(() => store.canRedo)

// 디바이스 목록
const devices = [
  { name: 'Desktop', icon: 'fas fa-desktop' },
  { name: 'Tablet', icon: 'fas fa-tablet-alt' },
  { name: 'Mobile', icon: 'fas fa-mobile-alt' }
]

// Methods
function getStatusText(status) {
  const statusMap = {
    draft: '초안',
    review: '검토',
    published: '게시됨',
    archived: '보관됨'
  }
  return statusMap[status] || status
}

function switchDevice(name) {
  store.switchDevice(name)
}

function toggleGuides() {
  store.toggleGuides()
}

function toggleSnap() {
  store.toggleSnap()
}

function toggleUnifiedPanel() {
  const currentState = store.ui.rightPanels.unified.visible
  store.persistPanelState('unified', { visible: !currentState })
}

function undo() {
  if (store.editor?.UndoManager) {
    store.editor.UndoManager.undo()
    store.updateUndoRedo()
  }
}

function redo() {
  if (store.editor?.UndoManager) {
    store.editor.UndoManager.redo()
    store.updateUndoRedo()
  }
}

async function saveProject() {
  const result = await store.saveProject()
  if (result.success) {
    // 성공 알림
    console.log('프로젝트가 저장되었습니다.')
  } else {
    // 에러 알림
    console.error('저장 실패:', result.error)
  }
}

async function handlePreview(data) {
  const result = await store.previewProject()
  if (result.success) {
    // 새 탭에서 미리보기 열기
    window.open(result.preview_url, '_blank')
  }
  showPreviewModal.value = false
}

async function handlePublish(data) {
  const result = await store.publishProject()
  if (result.success) {
    // 성공 알림
    console.log('프로젝트가 게시되었습니다.')
  }
  showPublishModal.value = false
}

// 단축키 이벤트
function handleKeydown(event) {
  if (event.ctrlKey || event.metaKey) {
    switch (event.key) {
      case 's':
        event.preventDefault()
        if (canSave.value) saveProject()
        break
      case 'z':
        event.preventDefault()
        if (event.shiftKey) {
          redo()
        } else {
          undo()
        }
        break
      case 'p':
        event.preventDefault()
        showPreviewModal.value = true
        break
    }
  } else if (event.key === 'Delete') {
    // 선택된 요소 삭제
    if (store.editor) {
      const selected = store.editor.getSelected()
      if (selected) {
        selected.remove()
        store.markChanged()
      }
    }
  }
}

// 컴포넌트 마운트 시 키보드 이벤트 리스너 추가
onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

// 컴포넌트 언마운트 시 이벤트 리스너 제거
onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
.top-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 60px;
  padding: 0 20px;
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.top-bar-left,
.top-bar-center,
.top-bar-right {
  display: flex;
  align-items: center;
  gap: 16px;
}

.project-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.project-name {
  font-size: 18px;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.project-status {
  font-size: 12px;
  padding: 2px 8px;
  border-radius: 12px;
  font-weight: 500;
}

.status-draft { background: #fef3c7; color: #92400e; }
.status-review { background: #dbeafe; color: #1e40af; }
.status-published { background: #d1fae5; color: #065f46; }
.status-archived { background: #f3f4f6; color: #6b7280; }

.device-switcher {
  display: flex;
  background: #f3f4f6;
  border-radius: 8px;
  padding: 4px;
}

.device-btn {
  padding: 8px 12px;
  border: none;
  background: transparent;
  border-radius: 6px;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
}

.device-btn:hover {
  background: #e5e7eb;
  color: #374151;
}

.device-btn.active {
  background: #667eea;
  color: white;
}

.view-controls {
  display: flex;
  gap: 8px;
}

.control-btn {
  padding: 8px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
}

.control-btn:hover {
  border-color: #667eea;
  color: #667eea;
}

.control-btn.active {
  background: #667eea;
  border-color: #667eea;
  color: white;
}

.history-controls {
  display: flex;
  gap: 4px;
}

.history-btn {
  padding: 8px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
}

.history-btn:hover:not(:disabled) {
  border-color: #667eea;
  color: #667eea;
}

.history-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.main-actions {
  display: flex;
  gap: 8px;
}

.action-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}

.save-btn {
  background: #10b981;
  color: white;
}

.save-btn:hover:not(:disabled) {
  background: #059669;
}

.save-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.preview-btn {
  background: #3b82f6;
  color: white;
}

.preview-btn:hover {
  background: #2563eb;
}

.publish-btn {
  background: #f59e0b;
  color: white;
}

.publish-btn:hover {
  background: #d97706;
}

.help-btn {
  padding: 8px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
}

.help-btn:hover {
  border-color: #667eea;
  color: #667eea;
}

/* 도움말 모달 */
.help-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.help-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  max-height: 80vh;
  overflow-y: auto;
}

.help-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.help-header h3 {
  margin: 0;
  color: #111827;
}

.close-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #6b7280;
  padding: 4px;
}

.help-body {
  padding: 24px;
}

.help-body h4 {
  margin: 0 0 12px 0;
  color: #374151;
}

.help-body ul {
  margin: 0 0 20px 0;
  padding-left: 20px;
}

.help-body li {
  margin-bottom: 8px;
  color: #6b7280;
}

kbd {
  background: #f3f4f6;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  padding: 2px 6px;
  font-size: 12px;
  font-family: monospace;
}
</style>
