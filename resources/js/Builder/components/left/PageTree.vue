<template>
  <div class="page-tree">
    <div class="page-tree-header">
      <h3>페이지</h3>
      <button class="add-page-btn" @click="showAddPageModal = true">
        <i class="fas fa-plus"></i>
      </button>
    </div>
    
    <div class="page-list">
      <div 
        v-for="page in pages" 
        :key="page.id"
        :class="['page-item', { active: currentPageId === page.id }]"
        @click="selectPage(page.id)"
      >
        <div class="page-info">
          <i class="fas fa-file-alt page-icon"></i>
          <span class="page-name">{{ page.name }}</span>
          <span v-if="page.isMain" class="main-badge">메인</span>
        </div>
        <div class="page-actions">
          <button 
            class="page-action-btn"
            @click.stop="togglePageVisibility(page.id)"
            :title="page.visible ? '숨기기' : '보이기'"
          >
            <i :class="page.visible ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
          </button>
          <button 
            class="page-action-btn"
            @click.stop="showPageSettings(page)"
            title="설정"
          >
            <i class="fas fa-cog"></i>
          </button>
        </div>
      </div>
    </div>
    
    <!-- 페이지 추가 모달 -->
    <div v-if="showAddPageModal" class="modal-overlay" @click="showAddPageModal = false">
      <div class="modal-content" @click.stop>
        <h3>새 페이지 추가</h3>
        <div class="form-group">
          <label>페이지명</label>
          <input v-model="newPageName" type="text" placeholder="페이지명을 입력하세요">
        </div>
        <div class="form-group">
          <label>슬러그</label>
          <input v-model="newPageSlug" type="text" placeholder="페이지 슬러그">
        </div>
        <div class="form-actions">
          <button @click="showAddPageModal = false">취소</button>
          <button @click="addPage" :disabled="!newPageName">추가</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useBuilderStore } from '../../stores/builderStore'

// Store
const store = useBuilderStore()

// Reactive data
const showAddPageModal = ref(false)
const newPageName = ref('')
const newPageSlug = ref('')

// Computed
const pages = computed(() => store.pages)
const currentPageId = computed(() => store.currentPageId)

// Methods
function selectPage(pageId) {
  store.currentPageId = pageId
}

function togglePageVisibility(pageId) {
  const page = pages.value.find(p => p.id === pageId)
  if (page) {
    page.visible = !page.visible
  }
}

function showPageSettings(page) {
  // TODO: 페이지 설정 모달 구현
  console.log('페이지 설정:', page)
}

function addPage() {
  if (!newPageName.value) return
  
  const newPage = {
    id: Date.now(), // 임시 ID
    name: newPageName.value,
    slug: newPageSlug.value || newPageName.value.toLowerCase().replace(/\s+/g, '-'),
    isMain: pages.value.length === 0, // 첫 번째 페이지면 메인
    visible: true
  }
  
  store.pages.push(newPage)
  
  // 폼 초기화
  newPageName.value = ''
  newPageSlug.value = ''
  showAddPageModal.value = false
}
</script>

<style scoped>
.page-tree {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.page-tree-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px;
  border-bottom: 1px solid #e5e7eb;
}

.page-tree-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}

.add-page-btn {
  padding: 6px 10px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 6px;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
}

.add-page-btn:hover {
  border-color: #667eea;
  color: #667eea;
}

.page-list {
  flex: 1;
  overflow-y: auto;
  padding: 8px;
}

.page-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px;
  margin-bottom: 4px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.page-item:hover {
  background: #f3f4f6;
}

.page-item.active {
  background: #eff6ff;
  border: 1px solid #3b82f6;
}

.page-info {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
}

.page-icon {
  color: #6b7280;
  font-size: 14px;
}

.page-name {
  font-weight: 500;
  color: #374151;
}

.main-badge {
  background: #10b981;
  color: white;
  font-size: 10px;
  padding: 2px 6px;
  border-radius: 10px;
  font-weight: 600;
}

.page-actions {
  display: flex;
  gap: 4px;
  opacity: 0;
  transition: opacity 0.2s;
}

.page-item:hover .page-actions {
  opacity: 1;
}

.page-action-btn {
  padding: 4px 6px;
  border: none;
  background: transparent;
  border-radius: 4px;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
}

.page-action-btn:hover {
  background: #e5e7eb;
  color: #374151;
}

/* 모달 스타일 */
.modal-overlay {
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

.modal-content {
  background: white;
  border-radius: 12px;
  padding: 24px;
  width: 90%;
  max-width: 400px;
}

.modal-content h3 {
  margin: 0 0 20px 0;
  color: #111827;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
  color: #374151;
}

.form-group input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 24px;
}

.form-actions button {
  padding: 8px 16px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.form-actions button:first-child {
  background: white;
  color: #6b7280;
}

.form-actions button:first-child:hover {
  background: #f3f4f6;
}

.form-actions button:last-child {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

.form-actions button:last-child:hover {
  background: #5a67d8;
}

.form-actions button:last-child:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
