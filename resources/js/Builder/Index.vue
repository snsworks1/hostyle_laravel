<template>
  <div class="builder-page">
    <div v-if="isLoading" class="loading-screen">
      <div class="loading-content">
        <div class="loading-spinner">
          <i class="fas fa-spinner fa-spin"></i>
        </div>
        <h2>웹빌더 로딩 중...</h2>
        <p>프로젝트를 준비하고 있습니다</p>
      </div>
    </div>
    
    <div v-else-if="error" class="error-screen">
      <div class="error-content">
        <div class="error-icon">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h2>오류가 발생했습니다</h2>
        <p>{{ error }}</p>
        <button class="retry-btn" @click="retryLoad">
          <i class="fas fa-redo"></i>
          다시 시도
        </button>
      </div>
    </div>
    
    <Editor v-else />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useBuilderStore } from '../../Builder/stores/builderStore'
import Editor from '../../Builder/views/Editor.vue'

// Store
const store = useBuilderStore()

// Reactive data
const isLoading = ref(true)
const error = ref(null)

// Methods
async function loadProject() {
  try {
    isLoading.value = true
    error.value = null
    
    // URL에서 프로젝트 ID 추출 (예: /builder/123)
    const projectId = window.location.pathname.split('/').pop()
    
    if (projectId && projectId !== 'builder') {
      // 실제 프로젝트 로드
      // const result = await projectApi.getProject(projectId)
      // if (result.success) {
      //   store.initProject(result.data)
      // } else {
      //   throw new Error('프로젝트를 불러올 수 없습니다')
      // }
      
      // 임시로 모의 데이터 사용
      const mockProject = {
        id: parseInt(projectId),
        name: `프로젝트 ${projectId}`,
        status: 'draft',
        currentVersion: 1,
        owner: { id: 1, name: '사용자' }
      }
      
      const mockPages = [
        { id: 1, name: '홈', slug: 'home', isMain: true, visible: true },
        { id: 2, name: '소개', slug: 'about', isMain: false, visible: true },
        { id: 3, name: '연락처', slug: 'contact', isMain: false, visible: true }
      ]
      
      store.initProject(mockProject)
      store.pages = mockPages
      store.currentPageId = 1
      
    } else {
      // 새 프로젝트 생성
      const newProject = {
        id: Date.now(),
        name: '새 프로젝트',
        status: 'draft',
        currentVersion: 1,
        owner: { id: 1, name: '사용자' }
      }
      
      store.initProject(newProject)
    }
    
    // 템플릿 로드
    // const templatesResult = await templateApi.getTemplates()
    // if (templatesResult.success) {
    //   store.setTemplates(templatesResult.data)
    // }
    
  } catch (err) {
    console.error('프로젝트 로드 실패:', err)
    error.value = err.message || '알 수 없는 오류가 발생했습니다'
  } finally {
    isLoading.value = false
  }
}

function retryLoad() {
  loadProject()
}

// 컴포넌트 마운트 시 프로젝트 로드
onMounted(() => {
  loadProject()
})
</script>

<style scoped>
.builder-page {
  height: 100vh;
  width: 100vw;
  overflow: hidden;
}

.loading-screen,
.error-screen {
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.loading-content,
.error-content {
  text-align: center;
  max-width: 400px;
  padding: 40px;
}

.loading-spinner {
  margin-bottom: 24px;
}

.loading-spinner i {
  font-size: 64px;
  color: rgba(255, 255, 255, 0.8);
}

.loading-content h2,
.error-content h2 {
  margin: 0 0 16px 0;
  font-size: 28px;
  font-weight: 600;
}

.loading-content p,
.error-content p {
  margin: 0;
  font-size: 16px;
  opacity: 0.8;
  line-height: 1.5;
}

.error-icon {
  margin-bottom: 24px;
}

.error-icon i {
  font-size: 64px;
  color: #fbbf24;
}

.retry-btn {
  margin-top: 24px;
  padding: 12px 24px;
  background: rgba(255, 255, 255, 0.2);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 8px;
  color: white;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.retry-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
}
</style>
