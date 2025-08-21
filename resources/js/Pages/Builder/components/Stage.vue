<template>
  <div class="flex-1 flex flex-col bg-gradient-to-br from-gray-50 via-blue-50/20 to-purple-50/20">
    <!-- Toolbar -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-gray-200/50 px-6 py-4 shadow-sm">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-3">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
            <h2 class="text-lg font-semibold text-gray-900">
              {{ page?.name || '페이지를 선택하세요' }}
            </h2>
            <span v-if="page?.is_main" class="px-3 py-1 text-xs font-semibold text-green-700 bg-gradient-to-r from-green-100 to-green-200 rounded-full border border-green-300">
              메인 페이지
            </span>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <div class="flex items-center space-x-2 px-3 py-2 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <span class="text-sm text-gray-600 font-medium">좌측 도구 패널에서 요소를 추가하세요</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Canvas -->
    <div class="flex-1 overflow-auto p-4"
         @dragover.prevent="onDragOver"
         @dragleave="onDragLeave"
         @drop.prevent="onDrop">
      <div class="w-full mx-auto relative">
        <!-- Drop guide line -->
        <div v-if="dropGuide.visible" class="absolute left-0 right-0" :style="{ top: dropGuide.top + 'px' }">
          <div class="h-0.5 bg-blue-500 shadow-[0_0_0_2px_rgba(59,130,246,0.3)]"></div>
        </div>
        <!-- Page Container -->
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden w-full border border-gray-200/50 backdrop-blur-sm">
          <!-- Header Preview -->
          <div class="header h-16 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 flex items-center justify-center hover:from-blue-50 hover:to-purple-50 transition-all duration-300 group" data-element-id="header">
            <div class="flex items-center space-x-2">
              <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
              <span class="text-sm text-gray-500 group-hover:text-gray-700 font-medium transition-colors">헤더 영역</span>
            </div>
          </div>
          
          <!-- Content Area -->
          <div class="min-h-[600px] p-8 w-full" data-content-area>
            <div v-if="!page || !page.page_schema?.sections?.length" class="text-center py-20">
              <div class="mx-auto w-32 h-32 bg-gradient-to-br from-blue-100 to-purple-100 rounded-2xl flex items-center justify-center mb-8 transform hover:scale-105 transition-transform duration-300">
                <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
              </div>
              <h3 class="text-2xl font-bold text-gray-900 mb-3">빈 페이지입니다</h3>
              <p class="text-gray-600 mb-8 text-lg max-w-md mx-auto leading-relaxed">좌측 도구 패널에서 원하는 요소를 드래그하여 추가하거나 상단에서 다른 페이지를 선택하세요.</p>
              <div class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-xl p-6 max-w-lg mx-auto">
                <div class="flex items-center justify-center space-x-3">
                  <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                  </svg>
                  <p class="text-sm text-blue-700 font-medium">좌측 도구 패널에서 원하는 요소를 드래그하여 추가하세요</p>
                </div>
              </div>
            </div>
            
            <!-- Sections -->
            <div v-else class="space-y-8 w-full">
              <div
                v-for="(section, index) in page.page_schema.sections"
                :key="section.id || index"
                class="section-wrapper group relative w-full"
                :data-index="index"
              >
                <!-- Section Controls -->
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-all duration-200 z-10">
                  <div class="flex items-center space-x-1 bg-white border border-gray-200 rounded-xl shadow-lg px-3 py-2 backdrop-blur-sm">
                    <button
                      @click.stop="moveSection(index, index - 1)"
                      :disabled="index === 0"
                      class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-gray-400 transition-all duration-200"
                      title="위로 이동"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                      </svg>
                    </button>
                    <button
                      @click.stop="moveSection(index, index + 1)"
                      :disabled="index === page.page_schema.sections.length - 1"
                      class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-gray-400 transition-all duration-200"
                      title="아래로 이동"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                      </svg>
                    </button>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <button
                      @click.stop="removeSection(index)"
                      class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200"
                      title="섹션 삭제"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>
                
                <!-- Section Content -->
                <div 
                  class="section-content border-2 border-transparent hover:border-blue-300 hover:shadow-lg rounded-xl transition-all duration-200 w-full cursor-pointer transform hover:-translate-y-1"
                  :class="{ 'border-blue-500 shadow-blue-100 shadow-lg': selectedSectionIndex === index }"
                  :data-element-id="section.id"
                  @click="openFinder(section)"
                >
                  <component 
                    :is="getSectionComponent(section.type)"
                    :section="section"
                    :is-selected="selectedSectionIndex === index"
                    @element-click="handleElementClick"
                  />
                </div>
              </div>
            </div>
          </div>
          
          <!-- Footer Preview -->
          <div class="footer h-16 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200 flex items-center justify-center hover:from-purple-50 hover:to-blue-50 transition-all duration-300 group" data-element-id="footer">
            <div class="flex items-center space-x-2">
              <svg class="w-4 h-4 text-gray-400 group-hover:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              <span class="text-sm text-gray-500 group-hover:text-gray-700 font-medium transition-colors">푸터 영역</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import Hero from '../sections/Hero.vue'
import Features from '../sections/Features.vue'
import Cta from '../sections/Cta.vue'

const props = defineProps<{
  page: any
}>()

const emit = defineEmits<{
  'update-schema': [schema: any]
  'open-element-modal': [section: any, index: number]
  'open-element-edit-modal': [elementType: string, elementData: any]
  'open-finder': [element: any]
}>()

const selectedSectionIndex = ref(-1)

const dropGuide = ref<{ visible: boolean; top: number }>({ visible: false, top: 0 })

const onDragOver = (e: DragEvent) => {
  const tpl = e.dataTransfer?.types.includes('application/x-builder-template')
  if (!tpl) return
  const container = (e.currentTarget as HTMLElement).querySelector('[data-content-area]') as HTMLElement
  if (!container) return
  const rect = container.getBoundingClientRect()
  const offsetY = e.clientY - rect.top
  // find nearest section boundary
  const wrappers = Array.from(container.querySelectorAll('.section-wrapper')) as HTMLElement[]
  let guideTop = rect.top
  let nearest = rect.top
  for (const w of wrappers) {
    const r = w.getBoundingClientRect()
    if (e.clientY < r.top + r.height / 2) {
      nearest = r.top
      break
    }
    nearest = r.bottom
  }
  dropGuide.value = { visible: true, top: nearest - rect.top }
}

const onDragLeave = () => {
  dropGuide.value.visible = false
}

const onDrop = (e: DragEvent) => {
  const data = e.dataTransfer?.getData('application/x-builder-template')
  if (!data || !props.page?.page_schema) return
  const tpl = JSON.parse(data)
  const container = (e.currentTarget as HTMLElement).querySelector('[data-content-area]') as HTMLElement
  const rect = container.getBoundingClientRect()
  const wrappers = Array.from(container.querySelectorAll('.section-wrapper')) as HTMLElement[]
  let insertIndex = wrappers.length
  for (let i = 0; i < wrappers.length; i++) {
    const r = wrappers[i].getBoundingClientRect()
    if (e.clientY < r.top + r.height / 2) { insertIndex = i; break }
  }

  const newSection = {
    id: Date.now().toString(),
    type: tpl.type,
    props: getDefaultProps(tpl.type)
  }
  const newSections = [...(props.page.page_schema.sections || [])]
  newSections.splice(insertIndex, 0, newSection)
  const newSchema = { ...props.page.page_schema, sections: newSections }
  emit('update-schema', newSchema)
  selectedSectionIndex.value = insertIndex
  dropGuide.value.visible = false
}

const selectSection = (index: number) => {
  selectedSectionIndex.value = index
}

const openElementModal = (section: any, index: number) => {
  console.log('openElementModal called:', section, index)
  emit('open-element-modal', section, index)
}

const openFinder = (section: any) => {
  console.log('openFinder called:', section)
  // 페이지의 전체 요소 구조를 전달
  const pageElements = extractPageElements(props.page)
  emit('open-finder', { section, pageElements })
}

const extractPageElements = (page: any) => {
  if (!page?.page_schema?.sections) return []
  
  const elements = []
  
  // 헤더 영역
  elements.push({
    id: 'header',
    name: '헤더',
    icon: '📱',
    type: 'header',
    expanded: false,
    children: [
      { id: 'header-logo', name: '로고', icon: '🖼️' },
      { id: 'header-nav', name: '네비게이션', icon: '🧭' },
      { id: 'header-actions', name: '액션 버튼', icon: '🔘' }
    ]
  })
  
  // 각 섹션과 그 하위 요소들
  page.page_schema.sections.forEach((section: any, sectionIndex: number) => {
    const sectionElement = {
      id: `section-${section.id}`,
      name: `${section.type} 섹션`,
      icon: getSectionIcon(section.type),
      type: 'section',
      expanded: false,
      children: getSectionChildren(section, sectionIndex)
    }
    elements.push(sectionElement)
  })
  
  // 푸터 영역
  elements.push({
    id: 'footer',
    name: '푸터',
    icon: '👣',
    type: 'footer',
    expanded: false,
    children: [
      { id: 'footer-links', name: '링크', icon: '🔗' },
      { id: 'footer-social', name: '소셜', icon: '📱' },
      { id: 'footer-copyright', name: '저작권', icon: '©️' }
    ]
  })
  
  return elements
}

const getSectionIcon = (type: string) => {
  const icons: Record<string, string> = {
    hero: '🌟',
    features: '✨',
    cta: '🎯',
    default: '⬜'
  }
  return icons[type] || icons.default
}

const getSectionChildren = (section: any, sectionIndex: number) => {
  const children = []
  
  // 섹션 제목
  if (section.props?.title) {
    children.push({
      id: `section-${section.id}-title`,
      name: '제목',
      icon: 'A',
      type: 'text',
      sectionIndex,
      elementType: 'title'
    })
  }
  
  // 섹션 부제목
  if (section.props?.subtitle) {
    children.push({
      id: `section-${section.id}-subtitle`,
      name: '부제목',
      icon: 'A',
      type: 'text',
      sectionIndex,
      elementType: 'subtitle'
    })
  }
  
  // 버튼
  if (section.props?.button) {
    children.push({
      id: `section-${section.id}-button`,
      name: '버튼',
      icon: '🔘',
      type: 'button',
      sectionIndex,
      elementType: 'button'
    })
  }
  
  // 기능 목록 (Features 섹션)
  if (section.props?.features && Array.isArray(section.props.features)) {
    section.props.features.forEach((feature: any, featureIndex: number) => {
      children.push({
        id: `section-${section.id}-feature-${featureIndex}`,
        name: `기능 ${featureIndex + 1}`,
        icon: '✨',
        type: 'feature',
        sectionIndex,
        featureIndex,
        elementType: 'feature'
      })
    })
  }
  
  return children
}



const handleElementClick = (elementType: string, elementId: string, elementData: any, event: Event) => {
  console.log('Stage - handleElementClick:', elementType, elementId, elementData)
  
  // 하위 요소 클릭 시 해당 요소를 선택 상태로 만들기
  if (elementId && elementId !== '') {
    // 현재 선택된 섹션의 하위 요소를 선택
    const sectionIndex = selectedSectionIndex.value
    if (sectionIndex >= 0 && props.page?.page_schema?.sections?.[sectionIndex]) {
      const section = props.page.page_schema.sections[sectionIndex]
      
      // 섹션에 선택된 하위 요소 정보 추가
      if (!section.selectedElement) {
        section.selectedElement = {}
      }
      section.selectedElement = {
        type: elementType,
        id: elementId,
        data: elementData
      }
      
      console.log('Stage - Element selected:', section.selectedElement)
    }
  }
  
  emit('open-element-edit-modal', elementType, elementData)
}

const addSection = (type: string) => {
  if (!props.page?.page_schema) return
  
  const newSection = {
    id: Date.now().toString(),
    type,
    props: getDefaultProps(type)
  }
  
  const newSchema = {
    ...props.page.page_schema,
    sections: [...(props.page.page_schema.sections || []), newSection]
  }
  
  emit('update-schema', newSchema)
  selectedSectionIndex.value = newSchema.sections.length - 1
}

const removeSection = (index: number) => {
  if (!props.page?.page_schema) return
  
  const newSections = [...props.page.page_schema.sections]
  newSections.splice(index, 1)
  
  const newSchema = {
    ...props.page.page_schema,
    sections: newSections
  }
  
  emit('update-schema', newSchema)
  selectedSectionIndex.value = -1
}

const moveSection = (oldIndex: number, newIndex: number) => {
  if (!props.page?.page_schema || newIndex < 0 || newIndex >= props.page.page_schema.sections.length) return
  
  const newSections = [...props.page.page_schema.sections]
  const [movedSection] = newSections.splice(oldIndex, 1)
  newSections.splice(newIndex, 0, movedSection)
  
  const newSchema = {
    ...props.page.page_schema,
    sections: newSections
  }
  
  emit('update-schema', newSchema)
  selectedSectionIndex.value = newIndex
}

const getSectionComponent = (type: string) => {
  switch (type) {
    case 'hero': return Hero
    case 'features': return Features
    case 'cta': return Cta
    default: return 'div'
  }
}

const getDefaultProps = (type: string) => {
  switch (type) {
    case 'hero':
      return {
        title: '환영합니다',
        subtitle: '당신의 웹사이트에 오신 것을 환영합니다',
        align: 'center',
        py: 80
      }
    case 'features':
      return {
        title: '주요 기능',
        subtitle: '우리 서비스의 핵심 기능들을 확인하세요',
        features: [
          { title: '기능 1', description: '설명 1', icon: 'star' },
          { title: '기능 2', description: '설명 2', icon: 'heart' },
          { title: '기능 3', description: '설명 3', icon: 'lightning' }
        ]
      }
    case 'cta':
      return {
        title: '지금 시작하세요',
        subtitle: '무료로 가입하고 모든 기능을 체험해보세요',
        button: '가입하기',
        url: '#'
      }
    default:
      return {}
  }
}
</script>

<style scoped>
.stage-container {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.stage-toolbar {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  padding: 0.75rem 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.toolbar-left,
.toolbar-right {
  display: flex;
  gap: 0.5rem;
}

.btn-tool {
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background: white;
  color: #6b7280;
  cursor: pointer;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.btn-tool:hover:not(:disabled) {
  background: #f9fafb;
  border-color: #9ca3af;
}

.btn-tool:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-save,
.btn-publish {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.875rem;
}

.btn-save {
  background: #3b82f6;
  color: white;
}

.btn-save:hover {
  background: #2563eb;
}

.btn-publish {
  background: #10b981;
  color: white;
}

.btn-publish:hover {
  background: #059669;
}

.stage-content {
  flex: 1;
  overflow: auto;
  padding: 2rem;
  display: flex;
  justify-content: center;
}

.stage-canvas {
  width: 100%;
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  min-height: 600px;
}

.sections-container {
  min-height: 600px;
}

.section-wrapper {
  margin: 1rem;
  border: 2px solid transparent;
  border-radius: 0.5rem;
  transition: all 0.2s;
}

.section-wrapper:hover {
  border-color: #e5e7eb;
}

.section-wrapper.selected {
  border-color: #3b82f6;
}

.section-header {
  background: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  padding: 0.5rem 0.75rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 0.5rem 0.5rem 0 0;
}

.section-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.drag-handle {
  color: #9ca3af;
  cursor: move;
  padding: 0.25rem;
}

.drag-handle:hover {
  color: #6b7280;
}

.section-type {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  text-transform: capitalize;
}

.section-actions {
  display: flex;
  gap: 0.25rem;
}

.btn-action {
  padding: 0.25rem;
  border: none;
  background: none;
  color: #6b7280;
  cursor: pointer;
  border-radius: 0.25rem;
  transition: all 0.2s;
}

.btn-action:hover {
  background: #e5e7eb;
  color: #374151;
}

.section-content {
  padding: 1rem;
}

.empty-stage {
  height: 600px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-content {
  text-align: center;
  max-width: 400px;
}
</style>

