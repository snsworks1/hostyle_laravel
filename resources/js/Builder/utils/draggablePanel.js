import interact from 'interactjs'

/**
 * 패널을 드래그하고 리사이즈할 수 있게 만드는 함수
 * @param {HTMLElement} el - 대상 DOM 요소
 * @param {Object} options - 옵션 객체
 * @param {Function} options.onChange - 상태 변경 시 호출될 콜백
 * @param {Object} options.initState - 초기 상태 {x, y, width, height}
 * @param {boolean} options.constrainToParent - 부모 요소 내에서만 이동할지 여부
 */
export function makeDraggableResizable(el, { onChange, initState, constrainToParent = false } = {}) {
  let { x = 0, y = 0, width = 320, height = 480 } = initState || {}
  
  // 초기 위치와 크기 설정
  el.style.position = 'fixed'
  el.style.transform = `translate(${x}px, ${y}px)`
  el.style.width = `${width}px`
  el.style.height = `${height}px`
  el.style.zIndex = '1000'
  
  // 드래그 가능하게 설정
  interact(el).draggable({
    inertia: true,
    modifiers: constrainToParent ? [
      interact.modifiers.restrictRect({
        restriction: 'parent',
        endOnly: true
      })
    ] : [],
    autoScroll: true,
    listeners: {
      move(event) {
        x += event.dx
        y += event.dy
        
        el.style.transform = `translate(${x}px, ${y}px)`
        
        // 콜백 호출
        onChange?.({ x, y, width, height })
      }
    }
  }).resizable({
    edges: { left: true, right: true, bottom: true, top: true },
    margin: 10,
    modifiers: [
      interact.modifiers.restrictEdges({
        outer: 'parent',
        endOnly: true
      }),
      interact.modifiers.restrictSize({
        min: { width: 200, height: 200 }
      })
    ],
    inertia: true
  }).on('resizemove', (event) => {
    width = event.rect.width
    height = event.rect.height
    
    el.style.width = `${width}px`
    el.style.height = `${height}px`
    
    // 콜백 호출
    onChange?.({ x, y, width, height })
  })
  
  // 정리 함수 반환
  return {
    destroy() {
      interact(el).unset()
    },
    updatePosition(newX, newY) {
      x = newX
      y = newY
      el.style.transform = `translate(${x}px, ${y}px)`
    },
    updateSize(newWidth, newHeight) {
      width = newWidth
      height = newHeight
      el.style.width = `${width}px`
      el.style.height = `${height}px`
    }
  }
}

/**
 * 패널을 토글할 수 있는 함수
 * @param {HTMLElement} el - 대상 DOM 요소
 * @param {boolean} visible - 표시 여부
 */
export function togglePanel(el, visible) {
  if (visible) {
    el.style.display = 'block'
    el.style.opacity = '1'
  } else {
    el.style.display = 'none'
    el.style.opacity = '0'
  }
}

/**
 * 패널을 최소화/최대화하는 함수
 * @param {HTMLElement} el - 대상 DOM 요소
 * @param {boolean} minimized - 최소화 여부
 */
export function minimizePanel(el, minimized) {
  if (minimized) {
    el.style.height = '40px'
    el.style.overflow = 'hidden'
  } else {
    el.style.height = ''
    el.style.overflow = ''
  }
}
