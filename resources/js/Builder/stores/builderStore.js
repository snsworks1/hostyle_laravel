import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import 'grapesjs/dist/css/grapes.min.css'

export const useBuilderStore = defineStore('builder', () => {
  // State
  const editor = ref(null)
  const project = ref({
    id: null,
    name: '',
    status: 'draft',
    currentVersion: 1,
    owner: null
  })
  
  const pages = ref([])
  const currentPageId = ref(null)
  const templates = ref([])
  
  const themeVars = ref({
    primaryColor: '#667eea',
    secondaryColor: '#764ba2',
    accentColor: '#f093fb',
    fontFamily: 'Inter, sans-serif',
    fontSize: '16px'
  })
  
  const ui = ref({
    rightPanels: {
      unified: { x: 20, y: 80, width: 380, height: 700, visible: true }
    },
    device: 'desktop',
    guides: false,
    snap: false
  })
  
  const flags = ref({
    isSaving: false,
    canSave: false,
    canUndo: false,
    canRedo: false,
    hasChanges: false
  })

  // Getters
  const currentPage = computed(() => 
    pages.value.find(p => p.id === currentPageId.value)
  )
  
  const canUndo = computed(() => 
    editor.value?.UndoManager?.canUndo() || false
  )
  
  const canRedo = computed(() => 
    editor.value?.UndoManager?.canRedo() || false
  )

  // Actions
  function initProject(data) {
    project.value = { ...project.value, ...data }
  }
  
  function setTemplates(list) {
    templates.value = list
  }
  
  function initEditor(elSelectors) {
    // 임시로 GrapesJS 직접 초기화
    return new Promise(async (resolve) => {
      try {
        const grapesjs = await import('grapesjs')
        const editorInstance = grapesjs.default.init({
          container: elSelectors.canvasEl || '#gjs',
          height: '100vh',
          width: 'auto',
          storageManager: { type: null },
          blockManager: {
            appendTo: elSelectors.blocksEl || '#blocks'
          },
          layerManager: {
            appendTo: elSelectors.layersEl || '#layers-container'
          },
          styleManager: {
            appendTo: elSelectors.stylesEl || '#style-manager'
          }
        })
        
        editor.value = editorInstance
        updateUndoRedo()
        resolve(editorInstance)
      } catch (error) {
        console.error('GrapesJS 초기화 실패:', error)
        resolve(null)
      }
    })
  }
  
  function markChanged() {
    flags.value.hasChanges = true
    flags.value.canSave = true
  }
  
  function updateUndoRedo() {
    if (editor.value) {
      flags.value.canUndo = canUndo.value
      flags.value.canRedo = canRedo.value
    }
  }
  
  function applyTheme(vars) {
    themeVars.value = { ...themeVars.value, ...vars }
    if (window.applyTheme) {
      window.applyTheme(vars)
    }
  }
  
  function persistPanelState(panelKey, state) {
    ui.value.rightPanels[panelKey] = { ...ui.value.rightPanels[panelKey], ...state }
    // localStorage에 저장
    localStorage.setItem(`builder_panel_${panelKey}`, JSON.stringify(ui.value.rightPanels[panelKey]))
  }
  
  function insertTemplate(template) {
    if (!editor.value) return
    
    try {
      editor.value.DomComponents.clear()
      if (template.project_data?.html) {
        editor.value.setComponents(template.project_data.html)
      }
      if (template.project_data?.css) {
        editor.value.setCss(template.project_data.css)
      }
      if (template.project_data?.js) {
        editor.value.setJs(template.project_data.js)
      }
      
      markChanged()
    } catch (error) {
      console.error('템플릿 삽입 실패:', error)
    }
  }
  
  function clearCanvas() {
    if (editor.value) {
      editor.value.DomComponents.clear()
      markChanged()
    }
  }
  
  async function saveProject() {
    if (!editor.value || !project.value.id) return
    
    flags.value.isSaving = true
    
    try {
      const projectData = {
        html: editor.value.getHtml(),
        css: editor.value.getCss(),
        js: editor.value.getJs()
      }
      
      // TODO: API 호출
      console.log('프로젝트 저장:', projectData)
      
      flags.value.hasChanges = false
      flags.value.canSave = false
      
      return { success: true }
    } catch (error) {
      console.error('저장 실패:', error)
      return { success: false, error: error.message }
    } finally {
      flags.value.isSaving = false
    }
  }
  
  async function previewProject() {
    if (!project.value.id) return
    
    try {
      // TODO: API 호출
      console.log('프리뷰 생성')
      return { success: true, preview_url: 'https://preview.example.com/abc123' }
    } catch (error) {
      console.error('프리뷰 생성 실패:', error)
      return { success: false, error: error.message }
    }
  }
  
  async function publishProject() {
    if (!project.value.id) return
    
    try {
      // TODO: API 호출
      console.log('프로젝트 게시')
      return { success: true, job_id: 'pub_98as...' }
    } catch (error) {
      console.error('게시 실패:', error)
      return { success: false, error: error.message }
    }
  }
  
  function switchDevice(name) {
    ui.value.device = name
    if (editor.value?.DeviceManager) {
      editor.value.DeviceManager.select(name)
    }
  }
  
  function toggleGuides() {
    ui.value.guides = !ui.value.guides
    // TODO: GrapesJS 가이드 플러그인 토글
  }
  
  function toggleSnap() {
    ui.value.snap = !ui.value.snap
    // TODO: GrapesJS 스냅 플러그인 토글
  }
  
  // 초기화 시 localStorage에서 패널 상태 복원
  function loadPanelStates() {
    Object.keys(ui.value.rightPanels).forEach(panelKey => {
      const saved = localStorage.getItem(`builder_panel_${panelKey}`)
      if (saved) {
        try {
          const savedState = JSON.parse(saved)
          ui.value.rightPanels[panelKey] = { ...ui.value.rightPanels[panelKey], ...savedState }
        } catch (e) {
          console.warn('패널 상태 복원 실패:', panelKey, e)
        }
      }
    })
  }
  
  // 초기화
  loadPanelStates()

  return {
    // State
    editor,
    project,
    pages,
    currentPageId,
    templates,
    themeVars,
    ui,
    flags,
    
    // Getters
    currentPage,
    canUndo,
    canRedo,
    
    // Actions
    initProject,
    setTemplates,
    initEditor,
    markChanged,
    updateUndoRedo,
    applyTheme,
    persistPanelState,
    insertTemplate,
    clearCanvas,
    saveProject,
    previewProject,
    publishProject,
    switchDevice,
    toggleGuides,
    toggleSnap
  }
})
