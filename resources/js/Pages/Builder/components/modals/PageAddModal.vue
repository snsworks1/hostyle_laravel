<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-medium text-gray-900">새 페이지 추가</h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="createPage" class="space-y-4">
        <!-- Page Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">페이지 이름</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="페이지 이름을 입력하세요"
            @input="onNameInput"
          />
        </div>

        <!-- Page Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">페이지 타입</label>
          <select
            v-model="form.type"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="page">일반 페이지</option>
            <option value="popup">팝업</option>
            <option value="link">링크</option>
            <option value="category">카테고리</option>
            <option value="data">데이터</option>
          </select>
        </div>

        <!-- Slug -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">URL 슬러그</label>
          <input
            v-model="form.slug"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="url-slug"
          />
          <p class="text-xs text-gray-500 mt-1">영문, 숫자, 하이픈만 사용 가능합니다</p>
        </div>

        <!-- Template Selection -->
        <div v-if="form.type === 'page'">
          <label class="block text-sm font-medium text-gray-700 mb-2">템플릿 선택</label>
          <div class="grid grid-cols-2 gap-3">
            <button
              v-for="template in templates"
              :key="template.id"
              type="button"
              @click="selectTemplate(template)"
              :class="[
                'p-3 border rounded-lg text-left transition-colors',
                selectedTemplate?.id === template.id
                  ? 'border-blue-500 bg-blue-50'
                  : 'border-gray-300 hover:border-gray-400'
              ]"
            >
              <div class="text-sm font-medium text-gray-900">{{ template.name }}</div>
              <div class="text-xs text-gray-500">{{ template.description }}</div>
            </button>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-3 pt-6 border-t">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            취소
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? '생성 중...' : '페이지 생성' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const emit = defineEmits<{
  close: []
  created: [page: any]
}>()

const loading = ref(false)
const form = ref({
  name: '',
  type: 'page',
  slug: ''
})

const selectedTemplate = ref<any>(null)

const templates = [
  {
    id: 'blank',
    name: '빈 페이지',
    description: '빈 페이지에서 시작'
  },
  {
    id: 'hero',
    name: 'Hero 페이지',
    description: '메인 소개 페이지'
  },
  {
    id: 'landing',
    name: '랜딩 페이지',
    description: '전환율 최적화 페이지'
  },
  {
    id: 'contact',
    name: '연락처 페이지',
    description: '문의 및 연락처 정보'
  }
]

const selectTemplate = (template: any) => {
  selectedTemplate.value = template
}

const createPage = async () => {
  if (!form.value.name || !form.value.slug) return

  loading.value = true
  try {
    // CSRF 토큰 가져오기
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    
    const response = await fetch(`/server/${window.location.pathname.split('/')[2]}/builder/api/pages`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': token || ''
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        name: form.value.name,
        slug: form.value.slug,
        type: form.value.type
      })
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const page = await response.json()
    console.log('페이지 생성 성공:', page)
    
    emit('created', page)
  } catch (error) {
    console.error('Failed to create page:', error)
    alert('페이지 생성에 실패했습니다.')
  } finally {
    loading.value = false
  }
}

// Slug 자동 생성
const generateSlug = () => {
  if (form.value.name) {
    form.value.slug = form.value.name
      .toLowerCase()
      .replace(/[^a-z0-9가-힣]/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-|-$/g, '')
  }
}

// 이름 입력 시 자동으로 slug 생성
const onNameInput = () => {
  if (!form.value.slug) {
    generateSlug()
  }
}
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
  max-width: 500px;
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

.form-group {
  margin-bottom: 1rem;
}

.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.5rem;
}

.form-input,
.form-select {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.modal-footer {
  padding: 1rem 1.5rem 1.5rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  gap: 0.75rem;
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

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f3f4f6;
  color: #374151;
}

.btn-secondary:hover:not(:disabled) {
  background: #e5e7eb;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #2563eb;
}
</style>

