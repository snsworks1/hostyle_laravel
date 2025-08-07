<template>
  <div class="max-w-4xl mx-auto py-10 px-4">
    <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-8">
      <h1 class="text-3xl font-bold text-white mb-6">CyberPanel 파일매니저 SSO 테스트</h1>
      
      <!-- 서버 정보 표시 -->
      <div v-if="page.props.server" class="mb-6 p-4 bg-blue-500/20 rounded-lg border border-blue-500/30">
        <h3 class="text-lg font-semibold text-white mb-2">서버 정보</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
          <div>
            <span class="text-blue-300">서버 ID:</span>
            <span class="text-white ml-2">{{ page.props.server.id }}</span>
          </div>
          <div>
            <span class="text-blue-300">도메인:</span>
            <span class="text-white ml-2">{{ page.props.server.domain }}</span>
          </div>
          <div>
            <span class="text-blue-300">FQDN:</span>
            <span class="text-white ml-2">{{ page.props.server.fqdn }}</span>
          </div>
          <div>
            <span class="text-blue-300">플랫폼:</span>
            <span class="text-white ml-2">{{ page.props.server.platform }}</span>
          </div>
        </div>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- 실제 API 구조 테스트 -->
        <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl p-6 text-white">
          <h3 class="text-lg font-semibold mb-4">1. 실제 API 구조 테스트</h3>
          <p class="text-sm mb-4">실제 CyberPanel API 엔드포인트들을 테스트합니다.</p>
          <button 
            @click="testRealApis"
            :disabled="loading.realApis"
            class="w-full bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition duration-200"
          >
            {{ loading.realApis ? '테스트 중...' : '실제 API 구조 테스트' }}
          </button>
        </div>

        <!-- 새로운 API 방식 테스트 -->
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-6 text-white">
          <h3 class="text-lg font-semibold mb-4">2. 새로운 API 방식 테스트</h3>
          <p class="text-sm mb-4">기존 CyberPanel API를 활용한 파일매니저 접근을 테스트합니다.</p>
          <button 
            @click="testNewApiFileManager"
            :disabled="loading.newApiFileManager"
            class="w-full bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition duration-200"
          >
            {{ loading.newApiFileManager ? '테스트 중...' : '새로운 API 방식 테스트' }}
          </button>
        </div>
      </div>

      <!-- 결과 표시 -->
      <div v-if="results" class="space-y-6">
        <div class="bg-white/5 rounded-xl p-6">
          <h3 class="text-xl font-semibold text-white mb-4">테스트 결과</h3>
          
          <!-- 실제 API 구조 테스트 결과 -->
          <div v-if="results.real_api_tests" class="mb-6">
            <h4 class="text-lg font-medium text-white mb-3">실제 API 구조 테스트 결과</h4>
            <div class="bg-gray-800 rounded-lg p-4 text-sm">
              <pre class="text-orange-400">{{ JSON.stringify(results.real_api_tests, null, 2) }}</pre>
            </div>
          </div>

          <!-- 새로운 API 테스트 결과 -->
          <div v-if="results.new_api_tests" class="mb-6">
            <h4 class="text-lg font-medium text-white mb-3">새로운 API 테스트 결과</h4>
            <div class="bg-gray-800 rounded-lg p-4 text-sm">
              <pre class="text-blue-400">{{ JSON.stringify(results.new_api_tests, null, 2) }}</pre>
            </div>
          </div>
        </div>
      </div>

      <!-- 에러 메시지 -->
      <div v-if="error" class="mt-6 bg-red-500/20 border border-red-500/50 rounded-xl p-6">
        <h3 class="text-lg font-semibold text-red-400 mb-2">오류 발생</h3>
        <p class="text-red-300">{{ error }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const serverId = page.props.server?.id || page.props.serverId

// 디버깅을 위한 로그
console.log('Page props:', page.props)
console.log('Server ID:', serverId)

// 서버 ID가 없으면 URL에서 추출
const getServerId = () => {
  if (serverId) return serverId
  
  // URL에서 서버 ID 추출
  const path = window.location.pathname
  const match = path.match(/\/server\/(\d+)/)
  return match ? match[1] : null
}

const currentServerId = getServerId()
console.log('Current Server ID:', currentServerId)

const loading = ref({
  newApiFileManager: false,
  realApis: false
})

const results = ref(null)
const error = ref(null)



const testNewApiFileManager = async () => {
  if (!currentServerId) {
    error.value = '서버 ID를 찾을 수 없습니다. 페이지를 새로고침해주세요.'
    return
  }
  
  loading.value.newApiFileManager = true
  error.value = null
  
  try {
    const response = await fetch(`/server/${currentServerId}/cyberpanel/test/new-api-filemanager`)
    
    if (response.ok) {
      const html = await response.text()
      
      // 새 창에서 HTML 열기
      const newWindow = window.open('', '_blank', 'width=1200,height=800')
      if (newWindow) {
        newWindow.document.write(html)
        newWindow.document.close()
        results.value = { ...results.value, newApiFileManager: '새로운 API 방식 테스트 창이 열렸습니다.' }
      } else {
        error.value = '팝업이 차단되었습니다. 브라우저에서 팝업을 허용해주세요.'
      }
    } else {
      const data = await response.json()
      error.value = data.error || '새로운 API 방식 테스트 중 오류가 발생했습니다.'
    }
  } catch (err) {
    error.value = '네트워크 오류가 발생했습니다: ' + err.message
  } finally {
    loading.value.newApiFileManager = false
  }
}

const testRealApis = async () => {
  if (!currentServerId) {
    error.value = '서버 ID를 찾을 수 없습니다. 페이지를 새로고침해주세요.'
    return
  }
  
  loading.value.realApis = true
  error.value = null
  
  try {
    const response = await fetch(`/server/${currentServerId}/cyberpanel/test/real-apis`)
    
    if (response.ok) {
      const data = await response.json()
      results.value = data
    } else {
      const data = await response.json()
      error.value = data.error || '실제 API 구조 테스트 중 오류가 발생했습니다.'
    }
  } catch (err) {
    error.value = '네트워크 오류가 발생했습니다: ' + err.message
  } finally {
    loading.value.realApis = false
  }
}
</script> 