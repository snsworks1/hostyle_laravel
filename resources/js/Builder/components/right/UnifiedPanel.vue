<template>
  <div 
    ref="panelRef"
    class="unified-panel"
    :class="{ 'panel-hidden': !ui.rightPanels.unified.visible }"
  >
    <div class="panel-header">
      <div class="panel-tabs">
        <button 
          :class="['tab-btn', { active: activeTab === 'layers' }]"
          @click="activeTab = 'layers'"
        >
          <i class="fas fa-layers"></i>
          레이어
        </button>
        <button 
          :class="['tab-btn', { active: activeTab === 'styles' }]"
          @click="activeTab = 'styles'"
        >
          <i class="fas fa-palette"></i>
          스타일
        </button>
        <button 
          :class="['tab-btn', { active: activeTab === 'theme' }]"
          @click="activeTab = 'theme'"
        >
          <i class="fas fa-paint-brush"></i>
          테마
        </button>
      </div>
      <div class="panel-controls">
        <button class="minimize-btn" @click="toggleMinimize">
          <i :class="isMinimized ? 'fas fa-expand' : 'fas fa-minus'"></i>
        </button>
        <button class="close-btn" @click="hidePanel">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    
    <div v-if="!isMinimized" class="panel-content">
      <!-- 레이어 탭 -->
      <div v-if="activeTab === 'layers'" class="tab-content">
        <div class="layers-tree">
          <div 
            v-for="component in layers" 
            :key="component.getId()"
            :class="['layer-item', { active: selectedComponent?.getId() === component.getId() }]"
            @click="selectComponent(component)"
          >
            <div class="layer-info">
              <i :class="getComponentIcon(component.get('type'))"></i>
              <span class="layer-name">{{ getComponentName(component) }}</span>
              <span class="layer-type">{{ getComponentType(component) }}</span>
            </div>
            <div class="layer-actions">
              <button 
                class="layer-action-btn"
                @click.stop="toggleComponentVisibility(component)"
                :title="component.get('visible') ? '숨기기' : '보이기'"
              >
                <i :class="component.get('visible') ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
              </button>
              <button 
                class="layer-action-btn"
                @click.stop="duplicateComponent(component)"
                title="복제"
              >
                <i class="fas fa-copy"></i>
              </button>
              <button 
                class="layer-action-btn delete-btn"
                @click.stop="deleteComponent(component)"
                title="삭제"
              >
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
        
        <div v-if="layers.length === 0" class="no-content">
          <i class="fas fa-layers"></i>
          <p>레이어가 없습니다</p>
          <span>캔버스에 요소를 추가하면 여기에 표시됩니다</span>
        </div>
      </div>
      
      <!-- 스타일 탭 -->
      <div v-if="activeTab === 'styles'" class="tab-content">
        <div v-if="selectedComponent" class="style-editor">
          <div class="component-info">
            <h4>{{ getComponentName(selectedComponent) }}</h4>
            <span class="component-type">{{ selectedComponent.get('tagName') }}</span>
          </div>
          
          <!-- 레이아웃 스타일 -->
          <div class="style-section">
            <h5>레이아웃</h5>
            <div class="style-group">
              <label>너비</label>
              <input 
                v-model="layoutStyles.width" 
                type="text" 
                placeholder="auto"
                @change="updateStyle('width', layoutStyles.width)"
              >
            </div>
            <div class="style-group">
              <label>높이</label>
              <input 
                v-model="layoutStyles.height" 
                type="text" 
                placeholder="auto"
                @change="updateStyle('height', layoutStyles.height)"
              >
            </div>
            <div class="style-group">
              <label>표시</label>
              <select 
                v-model="layoutStyles.display"
                @change="updateStyle('display', layoutStyles.display)"
              >
                <option value="block">Block</option>
                <option value="inline">Inline</option>
                <option value="inline-block">Inline-Block</option>
                <option value="flex">Flex</option>
                <option value="grid">Grid</option>
                <option value="none">None</option>
              </select>
            </div>
          </div>
          
          <!-- 여백 스타일 -->
          <div class="style-section">
            <h5>여백</h5>
            <div class="style-group">
              <label>마진</label>
              <div class="spacing-inputs">
                <input 
                  v-model="spacingStyles.marginTop" 
                  type="text" 
                  placeholder="0"
                  @change="updateStyle('margin-top', spacingStyles.marginTop)"
                >
                <input 
                  v-model="spacingStyles.marginRight" 
                  type="text" 
                  placeholder="0"
                  @change="updateStyle('margin-right', spacingStyles.marginRight)"
                >
                <input 
                  v-model="spacingStyles.marginBottom" 
                  type="text" 
                  placeholder="0"
                  @change="updateStyle('margin-bottom', spacingStyles.marginBottom)"
                >
                <input 
                  v-model="spacingStyles.marginLeft" 
                  type="text" 
                  placeholder="0"
                  @change="updateStyle('margin-left', spacingStyles.marginLeft)"
                >
              </div>
            </div>
            <div class="style-group">
              <label>패딩</label>
              <div class="spacing-inputs">
                <input 
                  v-model="spacingStyles.paddingTop" 
                  type="text" 
                  placeholder="0"
                  @change="updateStyle('padding-top', spacingStyles.paddingTop)"
                >
                <input 
                  v-model="spacingStyles.paddingRight" 
                  type="text" 
                  placeholder="0"
                  @change="updateStyle('padding-right', spacingStyles.paddingRight)"
                >
                <input 
                  v-model="spacingStyles.paddingBottom" 
                  type="text" 
                  placeholder="0"
                  @change="updateStyle('padding-bottom', spacingStyles.paddingBottom)"
                >
                <input 
                  v-model="spacingStyles.paddingLeft" 
                  type="text" 
                  placeholder="0"
                  @change="updateStyle('padding-left', spacingStyles.paddingLeft)"
                >
              </div>
            </div>
          </div>
          
          <!-- 텍스트 스타일 -->
          <div class="style-section">
            <h5>텍스트</h5>
            <div class="style-group">
              <label>색상</label>
              <input 
                v-model="textStyles.color" 
                type="color"
                @change="updateStyle('color', textStyles.color)"
              >
            </div>
            <div class="style-group">
              <label>폰트 크기</label>
              <input 
                v-model="textStyles.fontSize" 
                type="text" 
                placeholder="16px"
                @change="updateStyle('font-size', textStyles.fontSize)"
              >
            </div>
            <div class="style-group">
              <label>폰트 굵기</label>
              <select 
                v-model="textStyles.fontWeight"
                @change="updateStyle('font-weight', textStyles.fontWeight)"
              >
                <option value="normal">Normal</option>
                <option value="bold">Bold</option>
                <option value="100">100</option>
                <option value="200">200</option>
                <option value="300">300</option>
                <option value="400">400</option>
                <option value="500">500</option>
                <option value="600">600</option>
                <option value="700">700</option>
                <option value="800">800</option>
                <option value="900">900</option>
              </select>
            </div>
            <div class="style-group">
              <label>텍스트 정렬</label>
              <select 
                v-model="textStyles.textAlign"
                @change="updateStyle('text-align', textStyles.textAlign)"
              >
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
                <option value="justify">Justify</option>
              </select>
            </div>
          </div>
          
          <!-- 배경 스타일 -->
          <div class="style-section">
            <h5>배경</h5>
            <div class="style-group">
              <label>배경색</label>
              <input 
                v-model="backgroundStyles.backgroundColor" 
                type="color"
                @change="updateStyle('background-color', backgroundStyles.backgroundColor)"
              >
            </div>
            <div class="style-group">
              <label>테두리</label>
              <input 
                v-model="backgroundStyles.border" 
                type="text" 
                placeholder="1px solid #000"
                @change="updateStyle('border', backgroundStyles.border)"
              >
            </div>
            <div class="style-group">
              <label>테두리 반경</label>
              <input 
                v-model="backgroundStyles.borderRadius" 
                type="text" 
                placeholder="0"
                @change="updateStyle('border-radius', backgroundStyles.borderRadius)"
              >
            </div>
          </div>
        </div>
        
        <div v-else class="no-content">
          <i class="fas fa-palette"></i>
          <p>요소를 선택하세요</p>
          <span>캔버스에서 요소를 클릭하면 스타일을 편집할 수 있습니다</span>
        </div>
      </div>
      
      <!-- 테마 탭 -->
      <div v-if="activeTab === 'theme'" class="tab-content">
        <div class="theme-editor">
          <!-- 색상 테마 -->
          <div class="theme-section">
            <h5>색상</h5>
            <div class="color-group">
              <label>주요 색상</label>
              <div class="color-input-group">
                <input 
                  v-model="themeColors.primaryColor" 
                  type="color"
                  @change="updateTheme"
                >
                <input 
                  v-model="themeColors.primaryColor" 
                  type="text" 
                  placeholder="#667eea"
                  @change="updateTheme"
                >
              </div>
            </div>
            
            <div class="color-group">
              <label>보조 색상</label>
              <div class="color-input-group">
                <input 
                  v-model="themeColors.secondaryColor" 
                  type="color"
                  @change="updateTheme"
                >
                <input 
                  v-model="themeColors.secondaryColor" 
                  type="text" 
                  placeholder="#764ba2"
                  @change="updateTheme"
                >
              </div>
            </div>
            
            <div class="color-group">
              <label>강조 색상</label>
              <div class="color-input-group">
                <input 
                  v-model="themeColors.accentColor" 
                  type="color"
                  @change="updateTheme"
                >
                <input 
                  v-model="themeColors.accentColor" 
                  type="text" 
                  placeholder="#f093fb"
                  @change="updateTheme"
                >
              </div>
            </div>
            
            <div class="color-group">
              <label>텍스트 색상</label>
              <div class="color-input-group">
                <input 
                  v-model="themeColors.textColor" 
                  type="color"
                  @change="updateTheme"
                >
                <input 
                  v-model="themeColors.textColor" 
                  type="text" 
                  placeholder="#333"
                  @change="updateTheme"
                >
              </div>
            </div>
            
            <div class="color-group">
              <label>배경 색상</label>
              <div class="color-input-group">
                <input 
                  v-model="themeColors.backgroundColor" 
                  type="color"
                  @change="updateTheme"
                >
                <input 
                  v-model="themeColors.backgroundColor" 
                  type="text" 
                  placeholder="#fff"
                  @change="updateTheme"
                >
              </div>
            </div>
          </div>
          
          <!-- 타이포그래피 -->
          <div class="theme-section">
            <h5>타이포그래피</h5>
            <div class="style-group">
              <label>폰트 패밀리</label>
              <select v-model="themeTypography.fontFamily" @change="updateTheme">
                <option value="Inter, sans-serif">Inter</option>
                <option value="Roboto, sans-serif">Roboto</option>
                <option value="Open Sans, sans-serif">Open Sans</option>
                <option value="Lato, sans-serif">Lato</option>
                <option value="Poppins, sans-serif">Poppins</option>
                <option value="Montserrat, sans-serif">Montserrat</option>
                <option value="Source Sans Pro, sans-serif">Source Sans Pro</option>
                <option value="Arial, sans-serif">Arial</option>
                <option value="Helvetica, sans-serif">Helvetica</option>
                <option value="Georgia, serif">Georgia</option>
                <option value="Times New Roman, serif">Times New Roman</option>
              </select>
            </div>
            
            <div class="style-group">
              <label>기본 폰트 크기</label>
              <input 
                v-model="themeTypography.fontSize" 
                type="text" 
                placeholder="16px"
                @change="updateTheme"
              >
            </div>
            
            <div class="style-group">
              <label>제목 폰트 크기</label>
              <input 
                v-model="themeTypography.headingFontSize" 
                type="text" 
                placeholder="24px"
                @change="updateTheme"
              >
            </div>
            
            <div class="style-group">
              <label>줄 간격</label>
              <input 
                v-model="themeTypography.lineHeight" 
                type="text" 
                placeholder="1.6"
                @change="updateTheme"
              >
            </div>
          </div>
          
          <!-- 간격 -->
          <div class="theme-section">
            <h5>간격</h5>
            <div class="style-group">
              <label>기본 간격</label>
              <input 
                v-model="themeSpacing.baseSpacing" 
                type="text" 
                placeholder="8px"
                @change="updateTheme"
              >
            </div>
            
            <div class="style-group">
              <label>섹션 간격</label>
              <input 
                v-model="themeSpacing.sectionSpacing" 
                type="text" 
                placeholder="32px"
                @change="updateTheme"
              >
            </div>
          </div>
          
          <!-- 테마 액션 -->
          <div class="theme-actions">
            <button class="reset-btn" @click="resetTheme">
              <i class="fas fa-undo"></i>
              기본값으로
            </button>
            <button class="save-btn" @click="saveTheme">
              <i class="fas fa-save"></i>
              테마 저장
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useBuilderStore } from '../../stores/builderStore'
import { makeDraggableResizable, minimizePanel } from '../../utils/draggablePanel'
import { applyTheme, saveTheme as saveThemeUtil } from '../../utils/theme'

// Store
const store = useBuilderStore()

// Reactive data
const panelRef = ref(null)
const isMinimized = ref(false)
const activeTab = ref('layers')
let draggableInstance = null

// 스타일 상태
const layoutStyles = ref({
  width: '',
  height: '',
  display: 'block'
})

const spacingStyles = ref({
  marginTop: '',
  marginRight: '',
  marginBottom: '',
  marginLeft: '',
  paddingTop: '',
  paddingRight: '',
  paddingBottom: '',
  paddingLeft: ''
})

const textStyles = ref({
  color: '#000000',
  fontSize: '',
  fontWeight: 'normal',
  textAlign: 'left'
})

const backgroundStyles = ref({
  backgroundColor: '#ffffff',
  border: '',
  borderRadius: ''
})

// 테마 상태
const themeColors = ref({
  primaryColor: '#667eea',
  secondaryColor: '#764ba2',
  accentColor: '#f093fb',
  textColor: '#333',
  backgroundColor: '#fff'
})

const themeTypography = ref({
  fontFamily: 'Inter, sans-serif',
  fontSize: '16px',
  headingFontSize: '24px',
  lineHeight: '1.6'
})

const themeSpacing = ref({
  baseSpacing: '8px',
  sectionSpacing: '32px'
})

// Computed
const ui = computed(() => store.ui)
const layers = computed(() => {
  if (!store.editor) return []
  return store.editor.DomComponents.getWrapper().components.toArray()
})
const selectedComponent = computed(() => store.editor?.getSelected())

// Methods
function selectComponent(component) {
  if (store.editor) {
    store.editor.select(component)
  }
}

function toggleComponentVisibility(component) {
  const visible = component.get('visible')
  component.set('visible', !visible)
  store.markChanged()
}

function duplicateComponent(component) {
  if (store.editor) {
    const cloned = component.clone()
    store.editor.addComponent(cloned)
    store.markChanged()
  }
}

function deleteComponent(component) {
  if (store.editor) {
    component.remove()
    store.markChanged()
  }
}

function getComponentIcon(type) {
  const iconMap = {
    'default': 'fas fa-cube',
    'text': 'fas fa-font',
    'image': 'fas fa-image',
    'video': 'fas fa-video',
    'map': 'fas fa-map-marker-alt'
  }
  return iconMap[type] || 'fas fa-cube'
}

function getComponentName(component) {
  const tagName = component.get('tagName')
  if (tagName === 'img') {
    return component.get('attributes')?.alt || '이미지'
  } else if (tagName === 'text') {
    return component.get('content')?.slice(0, 20) || '텍스트'
  }
  return component.get('tagName') || '요소'
}

function getComponentType(component) {
  const tagName = component.get('tagName')
  if (tagName === 'img') return '이미지'
  else if (tagName === 'text') return '텍스트'
  return tagName
}

function updateStyle(property, value) {
  if (selectedComponent.value) {
    selectedComponent.value.addStyle(property, value)
    store.markChanged()
  }
}

function loadComponentStyles() {
  if (!selectedComponent.value) return
  
  const styles = selectedComponent.value.getStyle()
  
  // 레이아웃 스타일 로드
  layoutStyles.value = {
    width: styles.width || '',
    height: styles.height || '',
    display: styles.display || 'block'
  }
  
  // 여백 스타일 로드
  spacingStyles.value = {
    marginTop: styles['margin-top'] || '',
    marginRight: styles['margin-right'] || '',
    marginBottom: styles['margin-bottom'] || '',
    marginLeft: styles['margin-left'] || '',
    paddingTop: styles['padding-top'] || '',
    paddingRight: styles['padding-right'] || '',
    paddingBottom: styles['padding-bottom'] || '',
    paddingLeft: styles['padding-left'] || ''
  }
  
  // 텍스트 스타일 로드
  textStyles.value = {
    color: styles.color || '#000000',
    fontSize: styles['font-size'] || '',
    fontWeight: styles['font-weight'] || 'normal',
    textAlign: styles['text-align'] || 'left'
  }
  
  // 배경 스타일 로드
  backgroundStyles.value = {
    backgroundColor: styles['background-color'] || '#ffffff',
    border: styles.border || '',
    borderRadius: styles['border-radius'] || ''
  }
}

function updateTheme() {
  const themeVars = {
    ...themeColors.value,
    ...themeTypography.value,
    ...themeSpacing.value
  }
  
  // Store 업데이트
  store.applyTheme(themeVars)
  
  // CSS 변수 적용
  applyTheme(themeVars)
}

function resetTheme() {
  const defaultTheme = {
    primaryColor: '#667eea',
    secondaryColor: '#764ba2',
    accentColor: '#f093fb',
    textColor: '#333',
    backgroundColor: '#fff',
    fontFamily: 'Inter, sans-serif',
    fontSize: '16px',
    headingFontSize: '24px',
    lineHeight: '1.6',
    baseSpacing: '8px',
    sectionSpacing: '32px'
  }
  
  // 상태 초기화
  themeColors.value = {
    primaryColor: defaultTheme.primaryColor,
    secondaryColor: defaultTheme.secondaryColor,
    accentColor: defaultTheme.accentColor,
    textColor: defaultTheme.textColor,
    backgroundColor: defaultTheme.backgroundColor
  }
  
  themeTypography.value = {
    fontFamily: defaultTheme.fontFamily,
    fontSize: defaultTheme.fontSize,
    headingFontSize: defaultTheme.headingFontSize,
    lineHeight: defaultTheme.lineHeight
  }
  
  themeSpacing.value = {
    baseSpacing: defaultTheme.baseSpacing,
    sectionSpacing: defaultTheme.sectionSpacing
  }
  
  // 테마 적용
  updateTheme()
}

function saveTheme() {
  const themeVars = {
    ...themeColors.value,
    ...themeTypography.value,
    ...themeSpacing.value
  }
  
  // 유틸리티로 저장
  saveThemeUtil(themeVars)
  
  // Store에도 저장
  store.applyTheme(themeVars)
  
  console.log('테마가 저장되었습니다:', themeVars)
}

function toggleMinimize() {
  isMinimized.value = !isMinimized.value
  if (panelRef.value) {
    minimizePanel(panelRef.value, isMinimized.value)
  }
}

function hidePanel() {
  store.persistPanelState('unified', { visible: false })
}

// 선택된 컴포넌트 변경 시 스타일 로드
watch(selectedComponent, () => {
  loadComponentStyles()
}, { immediate: true })

// 컴포넌트 마운트 시 드래그 가능하게 설정
onMounted(() => {
  if (panelRef.value) {
    // 통합 패널이 store에 없다면 초기화
    if (!store.ui.rightPanels.unified) {
      store.ui.rightPanels.unified = { 
        x: 20, 
        y: 80, 
        width: 380, 
        height: 700, 
        visible: true 
      }
    }
    
    const panelState = store.ui.rightPanels.unified
    
    draggableInstance = makeDraggableResizable(panelRef.value, {
      onChange: (state) => {
        store.persistPanelState('unified', state)
      },
      initState: panelState
    })
    
    // 초기 테마 로드
    const currentTheme = store.themeVars
    if (currentTheme) {
      themeColors.value = {
        primaryColor: currentTheme.primaryColor || '#667eea',
        secondaryColor: currentTheme.secondaryColor || '#764ba2',
        accentColor: currentTheme.accentColor || '#f093fb',
        textColor: currentTheme.textColor || '#333',
        backgroundColor: currentTheme.backgroundColor || '#fff'
      }
      
      themeTypography.value = {
        fontFamily: currentTheme.fontFamily || 'Inter, sans-serif',
        fontSize: currentTheme.fontSize || '16px',
        headingFontSize: currentTheme.headingFontSize || '24px',
        lineHeight: currentTheme.lineHeight || '1.6'
      }
      
      themeSpacing.value = {
        baseSpacing: currentTheme.baseSpacing || '8px',
        sectionSpacing: currentTheme.sectionSpacing || '32px'
      }
    }
  }
})

// 컴포넌트 언마운트 시 정리
onUnmounted(() => {
  if (draggableInstance) {
    draggableInstance.destroy()
  }
})
</script>

<style scoped>
.unified-panel {
  position: fixed;
  top: 80px;
  right: 20px;
  width: 380px;
  height: 700px;
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
  border: 1px solid #e8eaed;
  display: flex;
  flex-direction: column;
  z-index: 1000;
  transition: all 0.3s ease;
}

.unified-panel.panel-hidden {
  display: none;
}

.panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px;
  background: #f8fafc;
  border-bottom: 1px solid #e8eaed;
  border-radius: 16px 16px 0 0;
  cursor: move;
}

.panel-tabs {
  display: flex;
  gap: 4px;
}

.tab-btn {
  padding: 8px 12px;
  border: none;
  background: transparent;
  border-radius: 8px;
  cursor: pointer;
  color: #5a6c7d;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 6px;
}

.tab-btn:hover {
  background: #f0f2f5;
  color: #2d3748;
}

.tab-btn.active {
  background: #667eea;
  color: white;
}

.panel-controls {
  display: flex;
  gap: 4px;
}

.minimize-btn,
.close-btn {
  padding: 6px;
  border: none;
  background: transparent;
  border-radius: 6px;
  cursor: pointer;
  color: #5a6c7d;
  transition: all 0.2s;
}

.minimize-btn:hover,
.close-btn:hover {
  background: #f0f2f5;
  color: #2d3748;
}

.panel-content {
  flex: 1;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.tab-content {
  flex: 1;
  overflow-y: auto;
  padding: 16px;
}

/* 레이어 스타일 */
.layers-tree {
  flex: 1;
  overflow-y: auto;
}

.layer-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  margin-bottom: 2px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
}

.layer-item:hover {
  background: #f0f2f5;
}

.layer-item.active {
  background: #eff6ff;
  border: 1px solid #3b82f6;
}

.layer-info {
  display: flex;
  align-items: center;
  gap: 8px;
  flex: 1;
}

.layer-info i {
  color: #5a6c7d;
  font-size: 12px;
  width: 16px;
  text-align: center;
}

.layer-name {
  font-size: 14px;
  color: #2d3748;
  font-weight: 500;
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.layer-type {
  font-size: 10px;
  color: #5a6c7d;
  background: #f0f2f5;
  padding: 2px 6px;
  border-radius: 8px;
  white-space: nowrap;
}

.layer-actions {
  display: flex;
  gap: 2px;
  opacity: 0;
  transition: opacity 0.2s;
}

.layer-item:hover .layer-actions {
  opacity: 1;
}

.layer-action-btn {
  padding: 4px 6px;
  border: none;
  background: transparent;
  border-radius: 6px;
  cursor: pointer;
  color: #5a6c7d;
  transition: all 0.2s;
}

.layer-action-btn:hover {
  background: #f0f2f5;
  color: #2d3748;
}

.layer-action-btn.delete-btn:hover {
  background: #fee2e2;
  color: #dc2626;
}

/* 스타일 에디터 */
.component-info {
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid #e8eaed;
}

.component-info h4 {
  margin: 0 0 4px 0;
  font-size: 16px;
  color: #1a202c;
}

.component-type {
  font-size: 12px;
  color: #5a6c7d;
  background: #f0f2f5;
  padding: 2px 8px;
  border-radius: 10px;
}

.style-section {
  margin-bottom: 24px;
}

.style-section h5 {
  margin: 0 0 12px 0;
  font-size: 14px;
  font-weight: 600;
  color: #2d3748;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.style-group {
  margin-bottom: 12px;
}

.style-group label {
  display: block;
  margin-bottom: 6px;
  font-size: 12px;
  font-weight: 500;
  color: #6b7280;
}

.style-group input,
.style-group select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  background: white;
}

.style-group input:focus,
.style-group select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.style-group input[type="color"] {
  height: 40px;
  padding: 4px;
}

.spacing-inputs {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 4px;
}

.spacing-inputs input {
  text-align: center;
  font-size: 12px;
  padding: 6px 8px;
}

/* 테마 에디터 */
.theme-section {
  margin-bottom: 24px;
}

.theme-section h5 {
  margin: 0 0 12px 0;
  font-size: 14px;
  font-weight: 600;
  color: #2d3748;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.color-group,
.style-group {
  margin-bottom: 16px;
}

.color-group label,
.style-group label {
  display: block;
  margin-bottom: 6px;
  font-size: 12px;
  font-weight: 500;
  color: #5a6c7d;
}

.color-input-group {
  display: flex;
  gap: 8px;
  align-items: center;
}

.color-input-group input[type="color"] {
  width: 40px;
  height: 40px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 2px;
  cursor: pointer;
}

.color-input-group input[type="text"] {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  font-family: monospace;
}

.color-input-group input:focus,
.style-group input:focus,
.style-group select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.theme-actions {
  display: flex;
  gap: 8px;
  margin-top: 24px;
  padding-top: 16px;
  border-top: 1px solid #e8eaed;
}

.reset-btn,
.save-btn {
  flex: 1;
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.reset-btn {
  background: #f0f2f5;
  color: #5a6c7d;
  border: 1px solid #d1d5db;
}

.reset-btn:hover {
  background: #e8eaed;
  color: #2d3748;
}

.save-btn {
  background: #667eea;
  color: white;
}

.save-btn:hover {
  background: #5a67d8;
}

/* 공통 스타일 */
.no-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: #8b9aa8;
  padding: 40px 20px;
  text-align: center;
}

.no-content i {
  font-size: 48px;
  margin-bottom: 16px;
}

.no-content p {
  margin: 0 0 8px 0;
  font-size: 16px;
  font-weight: 500;
}

.no-content span {
  font-size: 14px;
  line-height: 1.4;
}

/* 최소화 상태 */
.unified-panel.minimized .panel-content {
  display: none;
}

.unified-panel.minimized {
  height: 60px;
}
</style>
