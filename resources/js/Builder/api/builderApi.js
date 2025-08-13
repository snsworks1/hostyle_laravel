/**
 * 웹빌더 API 호출을 위한 래퍼
 */

// API 기본 URL (환경별로 분리 가능)
const BASE_URL = import.meta.env.VITE_BUILDER_API_URL || '/api/builder'

/**
 * API 요청 헤더 생성
 */
function getHeaders() {
  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
  
  // CSRF 토큰이 있다면 추가
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  if (token) {
    headers['X-CSRF-TOKEN'] = token
  }
  
  return headers
}

/**
 * API 요청 함수
 */
async function apiRequest(endpoint, options = {}) {
  const url = `${BASE_URL}${endpoint}`
  
  const config = {
    headers: getHeaders(),
    ...options
  }
  
  try {
    const response = await fetch(url, config)
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }
    
    const data = await response.json()
    return { success: true, data }
  } catch (error) {
    console.error('API 요청 실패:', error)
    return { success: false, error: error.message }
  }
}

/**
 * 프로젝트 관련 API
 */
export const projectApi = {
  /**
   * 프로젝트 정보 조회
   */
  async getProject(projectId) {
    return apiRequest(`/projects/${projectId}`)
  },
  
  /**
   * 프로젝트 저장
   */
  async saveProject(projectId, data) {
    return apiRequest(`/projects/${projectId}/save`, {
      method: 'POST',
      body: JSON.stringify(data)
    })
  },
  
  /**
   * 프로젝트 미리보기
   */
  async previewProject(projectId, data) {
    return apiRequest(`/projects/${projectId}/preview`, {
      method: 'POST',
      body: JSON.stringify(data)
    })
  },
  
  /**
   * 프로젝트 게시
   */
  async publishProject(projectId, data) {
    return apiRequest(`/projects/${projectId}/publish`, {
      method: 'POST',
      body: JSON.stringify(data)
    })
  }
}

/**
 * 페이지 관련 API
 */
export const pageApi = {
  /**
   * 페이지 목록 조회
   */
  async getPages(projectId) {
    return apiRequest(`/projects/${projectId}/pages`)
  },
  
  /**
   * 페이지 생성
   */
  async createPage(projectId, pageData) {
    return apiRequest(`/projects/${projectId}/pages`, {
      method: 'POST',
      body: JSON.stringify(pageData)
    })
  },
  
  /**
   * 페이지 수정
   */
  async updatePage(projectId, pageId, pageData) {
    return apiRequest(`/projects/${projectId}/pages/${pageId}`, {
      method: 'PUT',
      body: JSON.stringify(pageData)
    })
  },
  
  /**
   * 페이지 삭제
   */
  async deletePage(projectId, pageId) {
    return apiRequest(`/projects/${projectId}/pages/${pageId}`, {
      method: 'DELETE'
    })
  }
}

/**
 * 템플릿 관련 API
 */
export const templateApi = {
  /**
   * 템플릿 목록 조회
   */
  async getTemplates(filters = {}) {
    const queryParams = new URLSearchParams()
    
    if (filters.category) queryParams.append('category', filters.category)
    if (filters.search) queryParams.append('search', filters.search)
    if (filters.page) queryParams.append('page', filters.page)
    if (filters.perPage) queryParams.append('per_page', filters.perPage)
    
    const queryString = queryParams.toString()
    const endpoint = queryString ? `/templates?${queryString}` : '/templates'
    
    return apiRequest(endpoint)
  },
  
  /**
   * 템플릿 상세 조회
   */
  async getTemplate(templateId) {
    return apiRequest(`/templates/${templateId}`)
  }
}

/**
 * 에셋 관련 API
 */
export const assetApi = {
  /**
   * 이미지 업로드
   */
  async uploadImage(file, projectId) {
    const formData = new FormData()
    formData.append('image', file)
    formData.append('project_id', projectId)
    
    return apiRequest('/assets/upload', {
      method: 'POST',
      body: formData,
      headers: {} // FormData 사용 시 Content-Type 자동 설정
    })
  },
  
  /**
   * 에셋 목록 조회
   */
  async getAssets(projectId, type = 'all') {
    return apiRequest(`/assets?project_id=${projectId}&type=${type}`)
  }
}

/**
 * 사용자 설정 관련 API
 */
export const userApi = {
  /**
   * 사용자 설정 저장
   */
  async saveSettings(settings) {
    return apiRequest('/user/settings', {
      method: 'POST',
      body: JSON.stringify(settings)
    })
  },
  
  /**
   * 사용자 설정 조회
   */
  async getSettings() {
    return apiRequest('/user/settings')
  }
}

/**
 * 통계 관련 API
 */
export const statsApi = {
  /**
   * 프로젝트 통계 조회
   */
  async getProjectStats(projectId) {
    return apiRequest(`/projects/${projectId}/stats`)
  },
  
  /**
   * 사용자 통계 조회
   */
  async getUserStats() {
    return apiRequest('/user/stats')
  }
}

/**
 * 모의 API 응답 (개발용)
 */
export const mockApi = {
  /**
   * 모의 템플릿 데이터
   */
  getMockTemplates() {
    return [
      {
        id: 1,
        title: '기업 홈페이지',
        category: 'corporate',
        thumbnail_url: 'https://via.placeholder.com/300x200/667eea/ffffff?text=기업+홈페이지',
        project_data: {
          html: '<div style="padding: 20px; text-align: center; background: #f8fafc;"><h1>기업 홈페이지</h1><p>전문적이고 신뢰할 수 있는 기업 이미지를 전달합니다.</p></div>',
          css: 'h1 { color: #667eea; font-family: Inter, sans-serif; } p { color: #64748b; }',
          js: ''
        }
      },
      {
        id: 2,
        title: '포트폴리오',
        category: 'portfolio',
        thumbnail_url: 'https://via.placeholder.com/300x200/764ba2/ffffff?text=포트폴리오',
        project_data: {
          html: '<div style="padding: 20px; text-align: center; background: #fef3c7;"><h1>포트폴리오</h1><p>창의적이고 개성 있는 작업물을 선보입니다.</p></div>',
          css: 'h1 { color: #764ba2; font-family: Inter, sans-serif; } p { color: #92400e; }',
          js: ''
        }
      },
      {
        id: 3,
        title: '블로그',
        category: 'blog',
        thumbnail_url: 'https://via.placeholder.com/300x200/f093fb/ffffff?text=블로그',
        project_data: {
          html: '<div style="padding: 20px; text-align: center; background: #fce7f3;"><h1>블로그</h1><p>독자와 소통하는 따뜻한 공간입니다.</p></div>',
          css: 'h1 { color: #f093fb; font-family: Inter, sans-serif; } p { color: #be185d; }',
          js: ''
        }
      }
    ]
  },
  
  /**
   * 모의 프로젝트 데이터
   */
  getMockProject() {
    return {
      id: 1,
      name: '샘플 프로젝트',
      status: 'draft',
      currentVersion: 1,
      owner: { id: 1, name: '사용자' },
      pages: [
        { id: 1, name: '홈', slug: 'home', isMain: true, visible: true },
        { id: 2, name: '소개', slug: 'about', isMain: false, visible: true },
        { id: 3, name: '연락처', slug: 'contact', isMain: false, visible: true }
      ]
    }
  }
}

// 기본 export
export default {
  projectApi,
  pageApi,
  templateApi,
  assetApi,
  userApi,
  statsApi,
  mockApi
}
