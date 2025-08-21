<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-2xl font-bold text-gray-900">웹 빌더 도움말</h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="space-y-8">
        <!-- Quick Start -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
          <h4 class="text-lg font-semibold text-blue-900 mb-4">🚀 빠른 시작</h4>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="text-blue-600 font-bold">1</span>
              </div>
              <h5 class="font-medium text-blue-900 mb-2">페이지 생성</h5>
              <p class="text-sm text-blue-700">좌측 사이드바에서 새 페이지를 추가하세요</p>
            </div>
            <div class="text-center">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="text-blue-600 font-bold">2</span>
              </div>
              <h5 class="font-medium text-blue-900 mb-2">섹션 추가</h5>
              <p class="text-sm text-blue-700">상단 툴바에서 원하는 섹션을 추가하세요</p>
            </div>
            <div class="text-center">
              <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <span class="text-blue-600 font-bold">3</span>
              </div>
              <h5 class="font-medium text-blue-900 mb-2">컨텐츠 편집</h5>
              <p class="text-sm text-blue-700">우측 패널에서 텍스트와 스타일을 수정하세요</p>
            </div>
          </div>
        </div>

        <!-- Features -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">✨ 주요 기능</h4>
            <ul class="space-y-3 text-sm text-gray-700">
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                드래그 앤 드롭으로 섹션 순서 변경
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                실시간 미리보기 및 편집
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                반응형 디자인 지원
              </li>
              <li class="flex items-start">
                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                SEO 최적화 설정
              </li>
            </ul>
          </div>

          <div class="bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-semibold text-gray-900 mb-4">🎨 섹션 종류</h4>
            <ul class="space-y-3 text-sm text-gray-700">
              <li class="flex items-start">
                <span class="w-3 h-3 bg-blue-500 rounded-full mr-2 mt-1.5 flex-shrink-0"></span>
                <strong>Hero:</strong> 메인 소개 섹션
              </li>
              <li class="flex items-start">
                <span class="w-3 h-3 bg-green-500 rounded-full mr-2 mt-1.5 flex-shrink-0"></span>
                <strong>Features:</strong> 기능 소개 섹션
              </li>
              <li class="flex items-start">
                <span class="w-3 h-3 bg-purple-500 rounded-full mr-2 mt-1.5 flex-shrink-0"></span>
                <strong>CTA:</strong> 행동 유도 섹션
              </li>
              <li class="flex items-start">
                <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2 mt-1.5 flex-shrink-0"></span>
                <strong>Custom:</strong> 사용자 정의 섹션
              </li>
            </ul>
          </div>
        </div>

        <!-- Keyboard Shortcuts -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
          <h4 class="text-lg font-semibold text-yellow-900 mb-4">⌨️ 키보드 단축키</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="flex justify-between">
              <span class="text-yellow-800">Ctrl + Z</span>
              <span class="text-yellow-700">실행 취소</span>
            </div>
            <div class="flex justify-between">
              <span class="text-yellow-800">Ctrl + Y</span>
              <span class="text-yellow-700">다시 실행</span>
            </div>
            <div class="flex justify-between">
              <span class="text-yellow-800">Ctrl + S</span>
              <span class="text-yellow-700">저장</span>
            </div>
            <div class="flex justify-between">
              <span class="text-yellow-800">Delete</span>
              <span class="text-yellow-700">선택된 섹션 삭제</span>
            </div>
          </div>
        </div>

        <!-- Tips -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
          <h4 class="text-lg font-semibold text-green-900 mb-4">💡 유용한 팁</h4>
          <div class="space-y-3 text-sm text-green-800">
            <p>• 섹션을 클릭하면 우측 패널에서 속성을 편집할 수 있습니다</p>
            <p>• 드래그 앤 드롭으로 섹션의 순서를 쉽게 변경할 수 있습니다</p>
            <p>• 변경사항은 자동으로 저장되지만, 수동으로 저장하는 것을 권장합니다</p>
            <p>• 게시 전에 항상 미리보기를 확인하세요</p>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-8 pt-6 border-t border-gray-200 text-center">
        <p class="text-sm text-gray-500">
          더 자세한 도움이 필요하시면 <a href="#" class="text-blue-600 hover:text-blue-800 underline">고객 지원팀</a>에 문의하세요
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const emit = defineEmits<{
  close: []
}>()
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
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 0.5rem;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow: auto;
}

.modal-header {
  padding: 1.5rem 1.5rem 1rem;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.modal-close {
  padding: 0.5rem;
  border: none;
  background: none;
  color: #6b7280;
  cursor: pointer;
  border-radius: 0.375rem;
}

.modal-close:hover {
  background: #f3f4f6;
}

.modal-body {
  padding: 1.5rem;
}

.help-section {
  margin-bottom: 2rem;
}

.help-section:last-child {
  margin-bottom: 0;
}

.help-subtitle {
  font-size: 1rem;
  font-weight: 600;
  color: #374151;
  margin: 0 0 0.75rem 0;
}

.help-list {
  margin: 0;
  padding-left: 1.25rem;
}

.help-list li {
  margin-bottom: 0.5rem;
  color: #6b7280;
  line-height: 1.5;
}

.help-list li:last-child {
  margin-bottom: 0;
}

.help-links {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.help-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #3b82f6;
  text-decoration: none;
  padding: 0.5rem;
  border-radius: 0.375rem;
  transition: all 0.2s;
}

.help-link:hover {
  background: #eff6ff;
}

.modal-footer {
  padding: 1rem 1.5rem 1.5rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: flex-end;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.375rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}
</style>

