<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-medium text-gray-900">새 팝업 추가</h3>
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
      <form @submit.prevent="createPopup" class="space-y-4">
        <!-- Popup Name -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">팝업 이름</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="팝업 이름을 입력하세요"
          />
        </div>

        <!-- Popup Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">팝업 타입</label>
          <select
            v-model="form.type"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="popup">일반 팝업</option>
            <option value="modal">모달 팝업</option>
            <option value="toast">토스트 알림</option>
            <option value="slide">슬라이드 팝업</option>
          </select>
        </div>

        <!-- Trigger Type -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">트리거 방식</label>
          <select
            v-model="form.trigger"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="page_load">페이지 로드 시</option>
            <option value="scroll">스크롤 시</option>
            <option value="click">클릭 시</option>
            <option value="time">시간 지연</option>
            <option value="exit">페이지 이탈 시</option>
          </select>
        </div>

        <!-- Delay (for time trigger) -->
        <div v-if="form.trigger === 'time'">
          <label class="block text-sm font-medium text-gray-700 mb-2">지연 시간 (초)</label>
          <input
            v-model.number="form.delay"
            type="number"
            min="1"
            max="60"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="5"
          />
        </div>

        <!-- Size -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">팝업 크기</label>
          <div class="grid grid-cols-3 gap-2">
            <button
              v-for="size in sizes"
              :key="size.value"
              type="button"
              @click="form.size = size.value"
              :class="[
                'p-2 text-xs border rounded transition-colors',
                form.size === size.value
                  ? 'border-blue-500 bg-blue-50 text-blue-700'
                  : 'border-gray-300 hover:border-gray-400'
              ]"
            >
              {{ size.label }}
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
            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loading ? '생성 중...' : '팝업 생성' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const emit = defineEmits<{
  close: []
  created: [popup: any]
}>()

const loading = ref(false)
const form = ref({
  name: '',
  type: 'popup',
  trigger: 'page_load',
  delay: 5,
  size: 'medium'
})

const sizes = [
  { value: 'small', label: '작음' },
  { value: 'medium', label: '보통' },
  { value: 'large', label: '큼' }
]

const createPopup = async () => {
  if (!form.value.name) return

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
        slug: `popup-${Date.now()}`,
        type: 'popup',
        popup_settings: {
          type: form.value.type,
          trigger: form.value.trigger,
          delay: form.value.delay,
          size: form.value.size
        }
      })
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const popup = await response.json()
    console.log('팝업 생성 성공:', popup)
    
    emit('created', popup)
  } catch (error) {
    console.error('Failed to create popup:', error)
    alert('팝업 생성에 실패했습니다.')
  } finally {
    loading.value = false
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

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
}

.form-input:focus {
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

