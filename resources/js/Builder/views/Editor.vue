<template>
  <div class="builder-editor">
    <!-- TopBar -->
    <TopBar />
    
    <!-- 메인 컨텐츠 영역 -->
    <div class="editor-main">
      <!-- 좌측 패널 -->
      <div class="left-panel">
        <div class="panel-tabs">
          <button 
            :class="['tab-btn', { active: activeLeftTab === 'pages' }]"
            @click="activeLeftTab = 'pages'"
          >
            <i class="fas fa-file-alt"></i>
            페이지
          </button>
          <button 
            :class="['tab-btn', { active: activeLeftTab === 'templates' }]"
            @click="activeLeftTab = 'templates'"
          >
            <i class="fas fa-th-large"></i>
            템플릿
          </button>
        </div>
        
        <div class="panel-content">
          <PageTree v-if="activeLeftTab === 'pages'" />
          <TemplateCatalog v-else-if="activeLeftTab === 'templates'" />
        </div>
      </div>
      
      <!-- 중앙 캔버스 -->
      <div class="center-panel">
        <CanvasPane />
      </div>
      
      <!-- 통합 우측 패널 -->
      <UnifiedPanel />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useBuilderStore } from '../stores/builderStore'
import TopBar from '../components/TopBar.vue'
import PageTree from '../components/left/PageTree.vue'
import TemplateCatalog from '../components/left/TemplateCatalog.vue'
import CanvasPane from '../components/center/CanvasPane.vue'
import UnifiedPanel from '../components/right/UnifiedPanel.vue'

// Store
const store = useBuilderStore()

// Reactive data
const activeLeftTab = ref('pages')

// 컴포넌트 마운트 시 초기화
onMounted(async () => {
  // 모의 데이터로 초기화
  const mockProject = {
    id: 1,
    name: '샘플 프로젝트',
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
})
</script>

<style scoped>
.builder-editor {
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: #ffffff;
}

.editor-main {
  flex: 1;
  display: flex;
  overflow: hidden;
}

.left-panel {
  width: 280px;
  background: #fafbfc;
  border-right: 1px solid #e1e5e9;
  display: flex;
  flex-direction: column;
}

.panel-tabs {
  display: flex;
  border-bottom: 1px solid #e1e5e9;
}

.tab-btn {
  flex: 1;
  padding: 12px 16px;
  border: none;
  background: transparent;
  cursor: pointer;
  color: #5a6c7d;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 8px;
  justify-content: center;
}

.tab-btn:hover {
  background: #f0f2f5;
  color: #2d3748;
}

.tab-btn.active {
  background: #667eea;
  color: white;
}

.panel-content {
  flex: 1;
  overflow-y: auto;
}

.center-panel {
  flex: 1;
  background: #fefefe;
  position: relative;
}

.right-panels {
  position: relative;
  width: 0;
}

/* 반응형 디자인 */
@media (max-width: 1200px) {
  .left-panel {
    width: 240px;
  }
}

@media (max-width: 768px) {
  .left-panel {
    width: 200px;
  }
  
  .tab-btn {
    padding: 8px 12px;
    font-size: 12px;
  }
}
</style>
