<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-semibold text-gray-900">아이콘 업로드</h3>
        <button
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="space-y-6">
        <!-- Upload Area -->
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors">
          <div class="space-y-4">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="text-gray-600">
              <label for="file-upload" class="cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                <span>파일 업로드</span>
                <input 
                  id="file-upload" 
                  name="file-upload" 
                  type="file" 
                  class="sr-only" 
                  accept="image/*"
                  @change="handleFileUpload"
                />
              </label>
              <p class="pl-1">또는 드래그 앤 드롭</p>
            </div>
            <p class="text-xs text-gray-500">PNG, JPG, GIF, SVG 최대 2MB</p>
          </div>
        </div>

        <!-- File Preview -->
        <div v-if="selectedFile" class="space-y-4">
          <h4 class="text-lg font-medium text-gray-900">업로드할 파일</h4>
          <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
            <img 
              v-if="filePreview" 
              :src="filePreview" 
              alt="File preview" 
              class="w-16 h-16 object-cover rounded"
            />
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-900">{{ selectedFile.name }}</p>
              <p class="text-sm text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
            </div>
            <button
              @click="removeFile"
              class="p-2 text-red-400 hover:text-red-600 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Upload Settings -->
        <div v-if="selectedFile" class="space-y-4">
          <h4 class="text-lg font-medium text-gray-900">업로드 설정</h4>
          
          <!-- Icon Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">아이콘 이름</label>
            <input
              v-model="iconName"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              placeholder="아이콘 이름을 입력하세요"
            />
          </div>

          <!-- Icon Size -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">아이콘 크기: {{ iconSize }}px</label>
            <input
              v-model.number="iconSize"
              type="range"
              min="16"
              max="96"
              class="w-full"
            />
          </div>

          <!-- Icon Color -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">아이콘 색상</label>
            <input
              v-model="iconColor"
              type="color"
              class="w-full h-10 border border-gray-300 rounded-md"
            />
          </div>

          <!-- Background Color -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">배경 색상</label>
            <input
              v-model="backgroundColor"
              type="color"
              class="w-full h-10 border border-gray-300 rounded-md"
            />
          </div>
        </div>

        <!-- Existing Icons -->
        <div class="border-t pt-6">
          <h4 class="text-lg font-medium text-gray-900 mb-4">기존 아이콘</h4>
          <div class="grid grid-cols-6 gap-4">
            <div 
              v-for="icon in existingIcons" 
              :key="icon.id"
              class="p-3 border border-gray-200 rounded-lg hover:border-blue-400 cursor-pointer transition-colors"
              @click="selectExistingIcon(icon)"
            >
              <img 
                :src="icon.url" 
                :alt="icon.name"
                class="w-8 h-8 mx-auto mb-2 object-contain"
              />
              <p class="text-xs text-gray-600 text-center truncate">{{ icon.name }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="flex items-center justify-end space-x-3 pt-6 border-t mt-6">
        <button
          @click="$emit('close')"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          취소
        </button>
        <button
          v-if="selectedFile"
          @click="uploadIcon"
          :disabled="uploading"
          class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ uploading ? '업로드 중...' : '업로드' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const props = defineProps<{
  server: any
}>()

const emit = defineEmits<{
  close: []
  'icon-selected': [icon: any]
}>()

// File upload state
const selectedFile = ref<File | null>(null)
const filePreview = ref<string | null>(null)
const uploading = ref(false)

// Icon settings
const iconName = ref('')
const iconSize = ref(48)
const iconColor = ref('#3B82F6')
const backgroundColor = ref('#DBEAFE')

// Existing icons (mock data - 실제로는 서버에서 가져옴)
const existingIcons = ref([
  { id: 1, name: '별', url: '/icons/star.svg', type: 'star' },
  { id: 2, name: '하트', url: '/icons/heart.svg', type: 'heart' },
  { id: 3, name: '번개', url: '/icons/lightning.svg', type: 'lightning' },
  { id: 4, name: '체크', url: '/icons/check.svg', type: 'check' },
  { id: 5, name: '플러스', url: '/icons/plus.svg', type: 'plus' },
  { id: 6, name: '마이너스', url: '/icons/minus.svg', type: 'minus' }
])

onMounted(() => {
  // 서버에서 기존 아이콘 목록 가져오기
  loadExistingIcons()
})

const loadExistingIcons = async () => {
  try {
    // TODO: 실제 API 호출로 교체
    // const response = await fetch(`/api/server/${props.server.id}/icons`)
    // existingIcons.value = await response.json()
    console.log('Loading existing icons...')
  } catch (error) {
    console.error('Failed to load existing icons:', error)
  }
}

const handleFileUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    const file = target.files[0]
    
    // 파일 크기 체크 (2MB)
    if (file.size > 2 * 1024 * 1024) {
      alert('파일 크기는 2MB 이하여야 합니다.')
      return
    }
    
    // 파일 타입 체크
    if (!file.type.startsWith('image/')) {
      alert('이미지 파일만 업로드 가능합니다.')
      return
    }
    
    selectedFile.value = file
    iconName.value = file.name.replace(/\.[^/.]+$/, '') // 확장자 제거
    
    // 파일 미리보기 생성
    const reader = new FileReader()
    reader.onload = (e) => {
      filePreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

const removeFile = () => {
  selectedFile.value = null
  filePreview.value = null
  iconName.value = ''
}

const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const uploadIcon = async () => {
  if (!selectedFile.value) return
  
  uploading.value = true
  
  try {
    // FormData 생성
    const formData = new FormData()
    formData.append('icon', selectedFile.value)
    formData.append('name', iconName.value)
    formData.append('size', iconSize.value.toString())
    formData.append('color', iconColor.value)
    formData.append('backgroundColor', backgroundColor.value)
    
    // TODO: 실제 업로드 API 호출
    // const response = await fetch(`/api/server/${props.server.id}/icons/upload`, {
    //   method: 'POST',
    //   body: formData
    // })
    
    // Mock response
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    const uploadedIcon = {
      id: Date.now(),
      name: iconName.value,
      url: filePreview.value,
      type: 'custom',
      size: iconSize.value,
      color: iconColor.value,
      backgroundColor: backgroundColor.value
    }
    
    // 기존 아이콘 목록에 추가
    existingIcons.value.push(uploadedIcon)
    
    // 선택된 아이콘으로 설정
    selectExistingIcon(uploadedIcon)
    
    console.log('Icon uploaded successfully:', uploadedIcon)
  } catch (error) {
    console.error('Failed to upload icon:', error)
    alert('아이콘 업로드에 실패했습니다.')
  } finally {
    uploading.value = false
  }
}

const selectExistingIcon = (icon: any) => {
  emit('icon-selected', icon)
  emit('close')
}
</script>
