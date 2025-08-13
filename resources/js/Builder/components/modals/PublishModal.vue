<template>
  <div class="modal-overlay" @click="$emit('close')">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h3>프로젝트 게시</h3>
        <button class="close-btn" @click="$emit('close')">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <div class="modal-body">
        <div class="publish-options">
          <div class="option-group">
            <label>게시 대상</label>
            <select v-model="publishTarget">
              <option value="production">프로덕션</option>
              <option value="staging">스테이징</option>
              <option value="preview">미리보기</option>
            </select>
          </div>
          
          <div class="option-group">
            <label>게시 전략</label>
            <select v-model="publishStrategy">
              <option value="static">정적 파일</option>
              <option value="dynamic">동적 렌더링</option>
              <option value="hybrid">하이브리드</option>
            </select>
          </div>
          
          <div class="option-group">
            <label>도메인</label>
            <input 
              v-model="publishDomain" 
              type="text" 
              placeholder="example.com"
            >
          </div>
        </div>
        
        <div class="publish-info">
          <div class="info-item">
            <i class="fas fa-info-circle"></i>
            <span>게시 전에 자동으로 저장됩니다.</span>
          </div>
          <div class="info-item">
            <i class="fas fa-clock"></i>
            <span>게시는 몇 분 정도 소요될 수 있습니다.</span>
          </div>
        </div>
      </div>
      
      <div class="modal-footer">
        <button class="btn btn-secondary" @click="$emit('close')">취소</button>
        <button class="btn btn-primary" @click="publishProject">
          <i class="fas fa-rocket"></i>
          게시하기
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Props & Emits
const emit = defineEmits(['close', 'publish'])

// Reactive data
const publishTarget = ref('production')
const publishStrategy = ref('static')
const publishDomain = ref('')

// Methods
function publishProject() {
  const publishData = {
    target: publishTarget.value,
    strategy: publishStrategy.value,
    domain: publishDomain.value
  }
  
  emit('publish', publishData)
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

.publish-options {
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

.option-group select,
.option-group input {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

.publish-info {
  background: #f0f9ff;
  padding: 16px;
  border-radius: 8px;
  border: 1px solid #bae6fd;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
  color: #0369a1;
  font-size: 14px;
}

.info-item:last-child {
  margin-bottom: 0;
}

.info-item i {
  color: #0ea5e9;
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
  background: #f59e0b;
  color: white;
}

.btn-primary:hover {
  background: #d97706;
}
</style>
