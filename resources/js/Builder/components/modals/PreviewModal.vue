<template>
  <div class="modal-overlay" @click="$emit('close')">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h3>미리보기</h3>
        <button class="close-btn" @click="$emit('close')">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-body">
        <div class="preview-options">
          <div class="option-group">
            <label>페이지 선택</label>
            <select v-model="selectedPageId">
              <option v-for="page in pages" :key="page.id" :value="page.id">
                {{ page.name }}
              </option>
            </select>
          </div>
          
          <div class="option-group">
            <label>버전</label>
            <select v-model="selectedVersion">
              <option value="latest">최신 버전</option>
              <option value="previous">이전 버전</option>
            </select>
          </div>
        </div>
        
        <div class="preview-info">
          <p>미리보기를 생성하면 새 탭에서 열립니다.</p>
          <p>현재 편집 중인 내용이 반영됩니다.</p>
        </div>
      </div>
      
      <div class="modal-footer">
        <button class="btn btn-secondary" @click="$emit('close')">취소</button>
        <button class="btn btn-primary" @click="generatePreview">
          <i class="fas fa-eye"></i>
          미리보기 생성
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useBuilderStore } from '../../stores/builderStore'

// Props & Emits
const emit = defineEmits(['close', 'preview'])

// Store
const store = useBuilderStore()

// Reactive data
const selectedPageId = ref(store.currentPageId)
const selectedVersion = ref('latest')

// Computed
const pages = computed(() => store.pages)

// Methods
function generatePreview() {
  const previewData = {
    pageId: selectedPageId.value,
    version: selectedVersion.value
  }
  
  emit('preview', previewData)
}
</script>

<style scoped>
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
  width: 90%;
  max-width: 500px;
  max-height: 80vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
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

.modal-body {
  padding: 24px;
}

.preview-options {
  margin-bottom: 24px;
}

.option-group {
  margin-bottom: 16px;
}

.option-group label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
  color: #374151;
}

.option-group select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

.preview-info {
  background: #f3f4f6;
  padding: 16px;
  border-radius: 8px;
}

.preview-info p {
  margin: 0 0 8px 0;
  color: #6b7280;
  font-size: 14px;
}

.preview-info p:last-child {
  margin-bottom: 0;
}

.modal-footer {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding: 20px 24px;
  border-top: 1px solid #e5e7eb;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
  border: 1px solid #d1d5db;
}

.btn-secondary:hover {
  background: #e5e7eb;
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-primary:hover {
  background: #5a67d8;
}
</style>
