/**
 * 테마 CSS 변수를 관리하는 유틸리티
 */

/**
 * 테마 시트를 생성하거나 가져오는 함수
 * @param {Document} doc - 대상 문서 (기본값: 현재 문서)
 * @returns {HTMLStyleElement} 테마 스타일 태그
 */
export function ensureThemeSheet(doc = document) {
  let tag = doc.getElementById('theme-vars')
  if (!tag) {
    tag = doc.createElement('style')
    tag.id = 'theme-vars'
    tag.setAttribute('data-builder-theme', 'true')
    doc.head.appendChild(tag)
  }
  return tag
}

/**
 * 테마 변수를 적용하는 함수
 * @param {Object} vars - 테마 변수 객체
 * @param {Document} doc - 대상 문서 (기본값: 현재 문서)
 */
export function applyTheme(vars, doc = document) {
  const tag = ensureThemeSheet(doc)
  
  const cssVars = {
    '--primary-color': vars.primaryColor || '#667eea',
    '--secondary-color': vars.secondaryColor || '#764ba2',
    '--accent-color': vars.accentColor || '#f093fb',
    '--font-family': vars.fontFamily || 'Inter, sans-serif',
    '--font-size': vars.fontSize || '16px',
    '--text-color': vars.textColor || '#333',
    '--background-color': vars.backgroundColor || '#fff',
    '--border-color': vars.borderColor || '#e5e7eb',
    '--shadow-color': vars.shadowColor || 'rgba(0, 0, 0, 0.1)',
    '--success-color': vars.successColor || '#10b981',
    '--warning-color': vars.warningColor || '#f59e0b',
    '--error-color': vars.errorColor || '#ef4444'
  }
  
  const cssText = `:root {
    ${Object.entries(cssVars).map(([key, value]) => `${key}: ${value};`).join('\n  ')}
  }`
  
  tag.textContent = cssText
  
  console.log('테마가 적용되었습니다:', vars)
}

/**
 * 특정 테마 변수를 가져오는 함수
 * @param {string} varName - CSS 변수명 (예: 'primary-color')
 * @param {Document} doc - 대상 문서 (기본값: 현재 문서)
 * @returns {string} CSS 변수 값
 */
export function getThemeVar(varName, doc = document) {
  const computedStyle = getComputedStyle(doc.documentElement)
  return computedStyle.getPropertyValue(`--${varName}`).trim()
}

/**
 * 모든 테마 변수를 가져오는 함수
 * @param {Document} doc - 대상 문서 (기본값: 현재 문서)
 * @returns {Object} 테마 변수 객체
 */
export function getAllThemeVars(doc = document) {
  const computedStyle = getComputedStyle(doc.documentElement)
  const cssVars = {}
  
  // CSS 변수들을 순회하며 값 추출
  for (let i = 0; i < computedStyle.length; i++) {
    const prop = computedStyle[i]
    if (prop.startsWith('--') && prop.includes('-color') || prop.includes('-font') || prop.includes('-size')) {
      const key = prop.replace('--', '')
      cssVars[key] = computedStyle.getPropertyValue(prop).trim()
    }
  }
  
  return cssVars
}

/**
 * 테마를 초기화하는 함수
 * @param {Object} defaultVars - 기본 테마 변수
 * @param {Document} doc - 대상 문서 (기본값: 현재 문서)
 */
export function initTheme(defaultVars = {}, doc = document) {
  // localStorage에서 저장된 테마 불러오기
  const savedTheme = localStorage.getItem('builder_theme')
  if (savedTheme) {
    try {
      const theme = JSON.parse(savedTheme)
      applyTheme({ ...defaultVars, ...theme }, doc)
      return theme
    } catch (e) {
      console.warn('저장된 테마 로드 실패:', e)
    }
  }
  
  // 기본 테마 적용
  applyTheme(defaultVars, doc)
  return defaultVars
}

/**
 * 테마를 저장하는 함수
 * @param {Object} vars - 테마 변수 객체
 */
export function saveTheme(vars) {
  localStorage.setItem('builder_theme', JSON.stringify(vars))
}

/**
 * 테마를 리셋하는 함수
 * @param {Object} defaultVars - 기본 테마 변수
 * @param {Document} doc - 대상 문서 (기본값: 현재 문서)
 */
export function resetTheme(defaultVars = {}, doc = document) {
  localStorage.removeItem('builder_theme')
  applyTheme(defaultVars, doc)
  return defaultVars
}
