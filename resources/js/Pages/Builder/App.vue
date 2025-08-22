<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Top Header -->
    <div class="bg-gradient-to-r from-white to-gray-50 border-b border-gray-200 px-6 py-4 shadow-sm">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5z"></path>
              </svg>
            </div>
            <div>
              <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">웹사이트 빌더</h1>
              <span class="text-xs text-gray-500">{{ server?.site_name || '로딩 중...' }}</span>
            </div>
          </div>
          
          <!-- Page Dropdown -->
          <div class="relative">
            <button 
              @click="showPageDropdown = !showPageDropdown"
              class="flex items-center space-x-2 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm hover:shadow-md"
            >
              <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <span class="font-medium">{{ currentPage?.name || '페이지 선택' }}</span>
              <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': showPageDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div v-if="showPageDropdown" class="absolute top-full left-0 mt-2 w-72 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden backdrop-blur-sm">
              <div class="p-3 bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-200">
                <button 
                  @click="showPageAddModal = true; showPageDropdown = false"
                  class="w-full px-4 py-2.5 text-sm font-medium text-blue-600 hover:bg-white hover:shadow-sm rounded-lg text-left transition-all duration-200 mb-2 flex items-center space-x-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  <span>새 페이지 추가</span>
                </button>
                <button 
                  @click="showPopupAddModal = true; showPageDropdown = false"
                  class="w-full px-4 py-2.5 text-sm font-medium text-purple-600 hover:bg-white hover:shadow-sm rounded-lg text-left transition-all duration-200 flex items-center space-x-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m0 0V1a1 1 0 011-1h2a1 1 0 011 1v18a1 1 0 01-1 1H5a1 1 0 01-1-1V1a1 1 0 011-1h2a1 1 0 011 1v3z"></path>
                  </svg>
                  <span>새 팝업 추가</span>
                </button>
              </div>
              
              <div class="max-h-64 overflow-y-auto p-2">
                <div 
                  v-for="page in pages" 
                  :key="page.id"
                  @click="selectPage(page.id); showPageDropdown = false"
                  class="px-3 py-3 text-sm hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 cursor-pointer flex items-center justify-between rounded-lg mb-1 transition-all duration-200 group"
                  :class="{ 'bg-gradient-to-r from-blue-100 to-purple-100 text-blue-700 shadow-sm': currentPage?.id === page.id }"
                >
                  <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 rounded-full" :class="page.is_main ? 'bg-green-500' : 'bg-gray-400'"></div>
                    <span class="font-medium">{{ page.name }}</span>
                    <span v-if="page.is_main" class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">메인</span>
                  </div>
                  <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <button 
                      @click.stop="duplicatePage(page.id)"
                      class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-100 rounded-md transition-colors duration-200"
                      title="복제"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                      </svg>
                    </button>
                    <button 
                      v-if="!page.is_main"
                      @click.stop="deletePage(page.id)"
                      class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-100 rounded-md transition-colors duration-200"
                      title="삭제"
                    >
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <!-- Undo/Redo -->
          <div class="flex items-center space-x-1 bg-white rounded-xl border border-gray-200 p-1 shadow-sm">
            <button 
              @click="undo" 
              :disabled="!canUndo"
              class="p-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-gray-600 transition-all duration-200"
              title="실행 취소"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
              </svg>
            </button>
            
            <button 
              @click="redo" 
              :disabled="!canRedo"
              class="p-2 text-gray-600 hover:bg-blue-50 hover:text-blue-600 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent disabled:hover:text-gray-600 transition-all duration-200"
              title="다시 실행"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H9"></path>
              </svg>
            </button>
          </div>
          
          <!-- Save/Publish -->
          <button 
            @click="savePage" 
            class="px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 border border-transparent rounded-xl hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm hover:shadow-md flex items-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
            </svg>
            <span>저장</span>
          </button>
          
          <button 
            @click="publishPage" 
            class="px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-green-600 border border-transparent rounded-xl hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-sm hover:shadow-md flex items-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
            </svg>
            <span>게시</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex h-screen">
      <!-- Left Tools Panel (Fixed) -->
      <div class="w-14 bg-gradient-to-b from-gray-50 to-gray-100 border-r border-gray-300 flex-shrink-0 flex flex-col items-center py-3 overflow-y-auto shadow-inner">
        <button 
          v-for="tool in tools" 
          :key="tool.id"
          @click="openPalette(tool.id)"
          class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:bg-blue-50 hover:border-blue-300 hover:scale-110 hover:shadow-lg transition-all duration-200 group relative mb-2 hover:-translate-y-0.5"
          :title="tool.title"
        >
          <img :src="tool.icon" :alt="tool.title" class="w-4 h-4 group-hover:scale-110 transition-transform duration-200">
          
          <!-- Enhanced Tooltip -->
          <div class="absolute left-full ml-3 px-3 py-2 bg-gray-900 text-white text-sm rounded-lg shadow-xl opacity-0 group-hover:opacity-100 transition-all duration-200 whitespace-nowrap pointer-events-none z-20 transform translate-x-2 group-hover:translate-x-0">
            {{ tool.title }}
            <!-- Tooltip Arrow -->
            <div class="absolute top-1/2 left-0 transform -translate-y-1/2 -translate-x-1 w-0 h-0 border-t-4 border-b-4 border-r-4 border-transparent border-r-gray-900"></div>
          </div>
        </button>
      </div>

      <!-- Center Stage (Full Width) -->
      <div class="flex-1 flex flex-col relative">
        <TemplatePalette :open="paletteOpen" :title="paletteTitle" @close="paletteOpen=false" />
        <Stage 
          :page="currentPage"
          @update-schema="updatePageSchema"
          @open-element-modal="openElementModal"
          @open-element-edit-modal="openElementEditModal"
          @open-finder="openFinder"
        />
      </div>
    </div>

    <!-- Modals -->
    <PageAddModal 
      v-if="showAddPageModal"
      @close="showAddPageModal = false"
      @created="onPageCreated"
    />
    
    <PopupAddModal 
      v-if="showAddPopupModal"
      @close="showAddPopupModal = false"
      @created="onPopupCreated"
    />
    
    <HelpModal 
      v-if="showHelpModal"
      @close="showHelpModal = false"
    />

    <ElementEditModal
      v-if="showElementEditModal && selectedElement"
      :element="selectedElement"
      @close="showElementEditModal = false"
      @apply="onElementEditApply"
    />

    <UniversalElementEditModal
      v-if="showUniversalElementModal && selectedEditElement"
      :element="selectedEditElement"
      :server="server"
      @close="showUniversalElementModal = false"
      @apply="onUniversalElementEditApply"
    />

    <FinderModal
      v-if="showFinderModal"
      :open="showFinderModal"
      :selected-element="selectedFinderElement"
      :page-elements="pageElements"
      @close="showFinderModal = false"
      @scroll-to-element="scrollToElement"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useBuilder } from '@/builder/store'
import Sidebar from './components/Sidebar.vue'
import Stage from './components/Stage.vue'

import TemplatePalette from './components/TemplatePalette.vue'
import FinderModal from './components/FinderModal.vue'
import PageAddModal from './components/modals/PageAddModal.vue'
import PopupAddModal from './components/modals/PopupAddModal.vue'
import HelpModal from './components/modals/HelpModal.vue'
import ElementEditModal from './components/modals/ElementEditModal.vue'
import UniversalElementEditModal from './components/modals/UniversalElementEditModal.vue'

const props = defineProps<{
  server: any
  serverId: number
  initialPageId?: number
}>()

const store = useBuilder()
const showAddPageModal = ref(false)
const showAddPopupModal = ref(false)
const showPageAddModal = ref(false)
const showPopupAddModal = ref(false)
const showHelpModal = ref(false)
const showElementEditModal = ref(false)
const selectedElement = ref<any>(null)
const selectedElementIndex = ref(-1)

// 페이지 드롭다운 상태
const showPageDropdown = ref(false)

// 범용 요소 편집 모달
const showUniversalElementModal = ref(false)
const selectedEditElement = ref<any>(null)

// palette state
const paletteOpen = ref(false)
const paletteTitle = ref('요소 템플릿')

// tools data
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
  { id: 'dbc', title: '데이터', icon: '/img/e/side/ic_data.svg' },
  { id: 'ifm', title: 'HTML/Iframe', icon: '/img/e/side/ic_code.svg' },
  { id: 'ele', title: '사용자 정의', icon: '/img/e/side/ic_copy.svg' }
]
const openPalette = (id: string) => {
  paletteTitle.value = {
    dsn: '디자인된 컨텐츠', frm: '프레임', txt: '텍스트', img: '이미지', ico: '아이콘', btn: '버튼',
    sld: '슬라이드', lin: '라인', spa: '여백', vdo: '동영상', map: '지도', sns: '소셜미디어', tbl: '테이블',
    fom: '폼', brd: '게시판', gal: '갤러리', cal: '캘린더', tab: '탭 프레임', lnb: '네비게이션 바', dbc: '데이터', ifm: 'HTML/Iframe', ele: '사용자 정의'
  }[id] || '요소 템플릿'
  paletteOpen.value = true
}

// finder modal state
const showFinderModal = ref(false)
const selectedFinderElement = ref<any>(null)
const pageElements = ref<any[]>([])
const openFinder = (data: any) => {
  selectedFinderElement.value = data.section
  pageElements.value = data.pageElements
  showFinderModal.value = true
}

const scrollToElement = (elementId: string) => {
  console.log('App.vue - Scroll to element:', elementId)
  
  // Stage 컴포넌트를 찾아서 직접 스크롤 처리
  let targetElement: HTMLElement | null = null
  
  // 헤더/푸터 요소
  if (elementId === 'header' || elementId === 'footer') {
    targetElement = document.querySelector(`[data-element-id="${elementId}"]`) as HTMLElement
    console.log('App.vue - Header/Footer element found:', targetElement)
  }
  // 섹션 요소 (예: section-1755763329635)
  else if (elementId.startsWith('section-')) {
    const sectionId = elementId.replace('section-', '')
    console.log('App.vue - Looking for section with ID:', sectionId)
    
    // 먼저 하위 요소를 찾아보고, 없으면 섹션 자체를 찾음
    targetElement = document.querySelector(`[data-element-id="${sectionId}"]`) as HTMLElement
    if (!targetElement) {
      // 섹션 자체를 찾음
      targetElement = document.querySelector(`[data-element-id="${sectionId.split('-')[0]}"]`) as HTMLElement
    }
    console.log('App.vue - Section element found:', targetElement)
  }
  // 섹션 하위 요소 (제목, 부제목, 버튼, 기능 등) - 직접 data-element-id로 찾음
  else {
    console.log('App.vue - Looking for element with exact ID:', elementId)
    targetElement = document.querySelector(`[data-element-id="${elementId}"]`) as HTMLElement
    console.log('App.vue - Direct element found:', targetElement)
  }
  
  if (targetElement) {
    console.log('App.vue - Scrolling to element:', targetElement)
    // 부드러운 스크롤
    targetElement.scrollIntoView({ 
      behavior: 'smooth', 
      block: 'center',
      inline: 'center'
    })
    
    // 하이라이트 효과 추가
    targetElement.classList.add('!border-blue-500', '!border-4', 'ring-4', 'ring-blue-300')
    
    // 3초 후 하이라이트 제거
    setTimeout(() => {
      targetElement?.classList.remove('!border-blue-500', '!border-4', 'ring-4', 'ring-blue-300')
    }, 3000)
    
    console.log('App.vue - Scroll and highlight completed')
  } else {
    console.warn('App.vue - Target element not found for ID:', elementId)
    // 디버깅을 위해 모든 data-element-id를 로그
    const allElements = document.querySelectorAll('[data-element-id]')
    console.log('App.vue - Available elements with data-element-id:', Array.from(allElements).map(el => ({
      id: el.getAttribute('data-element-id'),
      class: el.className
    })))
  }
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
    expanded: true,
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
      expanded: true,
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
    expanded: true,
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

const pages = computed(() => store.pages)
const currentPage = computed(() => store.currentPage)
const selectedSection = computed(() => store.selectedSection)
const canUndo = computed(() => store.canUndo)
const canRedo = computed(() => store.canRedo)

const selectPage = (pageId: number) => {
  store.selectPage(pageId)
}

const updatePageSchema = (schema: any) => {
  if (currentPage.value) {
    store.updatePageSchema(currentPage.value.id, schema)
  }
}

const updateSection = (sectionId: string, props: any) => {
  store.updateSectionProps(sectionId, props)
}

const addSection = (sectionType: string) => {
  store.add(sectionType)
}

const savePage = async () => {
  if (currentPage.value) {
    await store.save()
  }
}

const publishPage = async () => {
  if (currentPage.value) {
    await store.publish()
  }
}

const undo = () => {
  store.undo()
}

const redo = () => {
  store.redo()
}

const onPageCreated = (page: any) => {
  showAddPageModal.value = false
  store.loadPages()
  store.selectPage(page.id)
}

// 페이지 복제
const duplicatePage = (pageId: number) => {
  const page = pages.value.find(p => p.id === pageId)
  if (page) {
    const duplicatedPage = {
      ...page,
      id: Date.now(),
      name: `${page.name} (복사본)`,
      is_main: false
    }
    store.addPage(duplicatedPage)
  }
}

// 페이지 삭제
const deletePage = (pageId: number) => {
  if (confirm('정말로 이 페이지를 삭제하시겠습니까?')) {
    store.removePage(pageId)
    if (currentPage.value?.id === pageId) {
      // 현재 페이지가 삭제된 경우 첫 번째 페이지 선택
      if (pages.value.length > 0) {
        store.selectPage(pages.value[0].id)
      }
    }
  }
}

const onPopupCreated = (popup: any) => {
  showAddPopupModal.value = false
  store.loadPages()
}

const openElementModal = (element: any, index: number) => {
  console.log('App.vue - openElementModal called:', element, index)
  selectedElement.value = element
  selectedElementIndex.value = index
  showElementEditModal.value = true
  console.log('Modal state updated:', { showElementEditModal: showElementEditModal.value, selectedElement: selectedElement.value })
}

const openElementEditModal = (elementType: string, elementData: any) => {
  console.log('App.vue - openElementEditModal called:', elementType, elementData)
  selectedEditElement.value = elementData
  showUniversalElementModal.value = true
}

const onElementEditApply = (element: any) => {
  if (selectedElementIndex.value >= 0 && currentPage.value) {
    // 섹션의 속성을 업데이트
    const newSchema = { ...currentPage.value.page_schema }
    newSchema.sections[selectedElementIndex.value] = element
    updatePageSchema(newSchema)
  }
  showElementEditModal.value = false
}

const onUniversalElementEditApply = (element: any) => {
  console.log('Universal element edit applied:', element)
  
  // 현재 페이지의 스키마를 업데이트
  if (currentPage.value && currentPage.value.page_schema) {
    const newSchema = { ...currentPage.value.page_schema }
    
    // 섹션을 찾아서 해당 요소의 props를 업데이트
    if (newSchema.sections) {
      newSchema.sections = newSchema.sections.map((section: any) => {
        if (section.id === element.sectionId) {
          // 섹션의 props를 업데이트
          const updatedProps = { ...section.props }
          
          // 요소 타입에 따라 props 업데이트
          if (element.type === 'text') {
            if (element.id === 'section-title') {
              updatedProps.title = element.props.content
            } else if (element.id === 'section-subtitle') {
              updatedProps.subtitle = element.props.content
            } else if (element.id.startsWith('feature-title-')) {
              const index = parseInt(element.id.replace('feature-title-', ''))
              if (!updatedProps.features) updatedProps.features = []
              if (!updatedProps.features[index]) updatedProps.features[index] = {}
              updatedProps.features[index].title = element.props.content
            } else if (element.id.startsWith('feature-description-')) {
              const index = parseInt(element.id.replace('feature-description-', ''))
              if (!updatedProps.features) updatedProps.features = []
              if (!updatedProps.features[index]) updatedProps.features[index] = {}
              updatedProps.features[index].description = element.props.content
            } else if (element.id === 'cta-title') {
              updatedProps.title = element.props.content
            } else if (element.id === 'cta-subtitle') {
              updatedProps.subtitle = element.props.content
            } else if (element.id === 'hero-title') {
              updatedProps.title = element.props.content
            } else if (element.id === 'hero-subtitle') {
              updatedProps.subtitle = element.props.content
            }
          } else if (element.type === 'button') {
            if (element.id === 'primary-button' || element.id === 'cta-primary-button') {
              updatedProps.button = element.props.text
              updatedProps.url = element.props.url
            } else if (element.id === 'secondary-button' || element.id === 'cta-secondary-button') {
              updatedProps.secondaryButtonText = element.props.text
            }
          } else if (element.type === 'icon') {
            if (element.id.startsWith('feature-icon-')) {
              const index = parseInt(element.id.replace('feature-icon-', ''))
              if (!updatedProps.features) updatedProps.features = []
              if (!updatedProps.features[index]) updatedProps.features[index] = {}
              updatedProps.features[index].icon = element.props.icon
            }
          }
          
          return { ...section, props: updatedProps }
        }
        return section
      })
    }
    
    // 스키마 업데이트
    store.updatePageSchema(currentPage.value.id, newSchema)
    console.log('Schema updated with new element data:', element)
  }
  
  showUniversalElementModal.value = false
  selectedEditElement.value = null
}

onMounted(async () => {
  if (props.serverId) {
    store.setIds(props.serverId, props.initialPageId)
    await store.loadPages()
    
    if (props.initialPageId) {
      store.selectPage(props.initialPageId)
    } else if (pages.value.length > 0) {
      store.selectPage(pages.value[0].id)
    }
    
    // 웹빌더 진입 시 FINDER 자동 열기
    setTimeout(() => {
      if (currentPage.value) {
        const elements = extractPageElements(currentPage.value)
        pageElements.value = elements
        showFinderModal.value = true
        console.log('FINDER 자동 열기 완료')
      }
    }, 1500)
  }
})

watch(() => props.serverId, async (newId) => {
  if (newId) {
    store.setIds(newId)
    await store.loadPages()
  }
}, { immediate: true })
</script>

<style scoped>
.builder-layout {
  height: 100vh;
  display: flex;
  flex-direction: column;
  background: #f8fafc;
}

.builder-header {
  background: white;
  border-bottom: 1px solid #e5e7eb;
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.page-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.page-selector {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.page-dropdown {
  padding: 0.5rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background: white;
  color: #374151;
  font-size: 0.875rem;
  min-width: 200px;
}

.page-dropdown:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.header-right {
  display: flex;
  gap: 0.75rem;
}

.btn-toolbar {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  background: white;
  color: #374151;
  cursor: pointer;
  font-size: 0.875rem;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-toolbar:hover:not(:disabled) {
  background: #f9fafb;
  border-color: #9ca3af;
}

.btn-toolbar:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.builder-main {
  flex: 1;
  display: grid;
  grid-template-columns: 280px 1fr 320px;
  overflow: hidden;
}

.builder-sidebar {
  background: white;
  border-right: 1px solid #e5e7eb;
  overflow: hidden;
}

.builder-stage {
  background: #f8fafc;
  overflow: auto;
}

.builder-inspector {
  background: white;
  border-left: 1px solid #e5e7eb;
  overflow: hidden;
}

.no-page-selected {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.no-page-content {
  text-align: center;
  max-width: 400px;
}
</style>
