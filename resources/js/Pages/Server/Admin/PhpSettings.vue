<script setup>
import { usePage, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server || {};
const allServers = page.props.allServers || [];
const sidebarMenus = page.props.sidebarMenus || [];

// PHP 버전 관련 상태
const selectedPhpVersion = ref(server.php_version || '8.0');
const isChangingPhp = ref(false);
const phpChangeMessage = ref('');
const phpChangeSuccess = ref(false);

// PHP 설정 관련 상태
const phpSettings = ref({
  memory_limit: '128M',
  upload_max_filesize: '16M',
  post_max_size: '20M',
  max_execution_time: 30,
  max_input_time: 60,
});
const recommendedSettings = ref({});
const isLoadingSettings = ref(false);
const isUpdatingSettings = ref(false);
const settingsMessage = ref('');
const settingsSuccess = ref(false);
const currentPlan = ref('');
const isDefaultSettings = ref(false);

// 슬라이더 관련 상태
const memoryLimitValue = ref(128);
const uploadMaxFilesizeValue = ref(16);
const postMaxSizeValue = ref(20);
const maxExecutionTimeValue = ref(30);
const maxInputTimeValue = ref(60);

// 슬라이더 범위 설정 (플랜 기준값으로 동적 설정)
const memoryLimitRange = computed(() => {
  const planValue = parsePlanValue(recommendedSettings.value.memory_limit);
  return { min: 64, max: planValue, step: 64 };
});

const uploadMaxFilesizeRange = computed(() => {
  const planValue = parsePlanValue(recommendedSettings.value.upload_max_filesize);
  return { min: 8, max: planValue, step: 8 };
});

const postMaxSizeRange = computed(() => {
  const planValue = parsePlanValue(recommendedSettings.value.post_max_size);
  return { min: 10, max: planValue, step: 10 };
});

const maxExecutionTimeRange = computed(() => {
  const planValue = recommendedSettings.value.max_execution_time || 600;
  return { min: 10, max: planValue, step: 10 };
});

const maxInputTimeRange = computed(() => {
  const planValue = recommendedSettings.value.max_input_time || 600;
  return { min: 30, max: planValue, step: 30 };
});

// 플랜 초과 체크
const showUpgradeAlert = ref(false);

// 사용 가능한 PHP 버전들
const availablePhpVersions = [
  { value: '8.0', label: 'PHP 8.0' },
  { value: '8.1', label: 'PHP 8.1' },
  { value: '8.2', label: 'PHP 8.2' },
  { value: '8.3', label: 'PHP 8.3' }
];

// 현재 PHP 버전과 동일한 버전은 비활성화
const isVersionDisabled = computed(() => (version) => {
  return version.value === server.php_version;
});

// PHP 버전 변경 함수
const changePhpVersion = async () => {
  if (selectedPhpVersion.value === server.php_version) {
    phpChangeMessage.value = '이미 설치된 PHP 버전입니다.';
    phpChangeSuccess.value = false;
    return;
  }

  isChangingPhp.value = true;
  phpChangeMessage.value = '';
  phpChangeSuccess.value = false;

  try {
    const response = await axios.post(route('server.change-php-version', server.id), {
      php_version: selectedPhpVersion.value
    });

    if (response.data.success) {
      phpChangeMessage.value = response.data.message;
      phpChangeSuccess.value = true;
      // 서버 정보 업데이트
      server.php_version = selectedPhpVersion.value;
    } else {
      phpChangeMessage.value = response.data.message;
      phpChangeSuccess.value = false;
    }
  } catch (error) {
    phpChangeMessage.value = error.response?.data?.message || 'PHP 버전 변경 중 오류가 발생했습니다.';
    phpChangeSuccess.value = false;
  } finally {
    isChangingPhp.value = false;
  }
};

// PHP 설정 조회
const loadPhpSettings = async () => {
  isLoadingSettings.value = true;
  try {
    const response = await axios.get(route('server.php-settings.get', server.id));
    if (response.data.success) {
      phpSettings.value = response.data.current_settings;
      recommendedSettings.value = response.data.recommended_settings;
      currentPlan.value = response.data.plan;
      isDefaultSettings.value = response.data.is_default_settings || false;
      
      // 슬라이더 값 업데이트
      updateSliderValues();
      
      // 업그레이드 알림 체크
      showUpgradeAlert.value = shouldShowUpgradeAlert.value;
    }
  } catch (error) {
    console.error('PHP 설정 조회 실패:', error);
  } finally {
    isLoadingSettings.value = false;
  }
};

// 메모리 포맷팅 함수
const formatMemory = (value) => {
  if (typeof value === 'string') {
    return value;
  }
  if (value >= 1024) {
    return `${(value / 1024).toFixed(1)}G`;
  }
  return `${value}M`;
};

// 플랜 기준값 파싱
const parsePlanValue = (value) => {
  if (typeof value === 'string') {
    const match = value.match(/^(\d+)([MG])$/);
    if (match) {
      const num = parseInt(match[1]);
      const unit = match[2];
      return unit === 'G' ? num * 1024 : num;
    }
  }
  return parseInt(value) || 0;
};

// 플랜 초과 체크 computed
const isMemoryLimitExceeded = computed(() => {
  const currentValue = parsePlanValue(phpSettings.value.memory_limit);
  const recommendedValue = parsePlanValue(recommendedSettings.value.memory_limit);
  return currentValue > recommendedValue;
});

const isUploadMaxFilesizeExceeded = computed(() => {
  const currentValue = parsePlanValue(phpSettings.value.upload_max_filesize);
  const recommendedValue = parsePlanValue(recommendedSettings.value.upload_max_filesize);
  return currentValue > recommendedValue;
});

const isPostMaxSizeExceeded = computed(() => {
  const currentValue = parsePlanValue(phpSettings.value.post_max_size);
  const recommendedValue = parsePlanValue(recommendedSettings.value.post_max_size);
  return currentValue > recommendedValue;
});

const isMaxExecutionTimeExceeded = computed(() => {
  const currentValue = phpSettings.value.max_execution_time;
  const recommendedValue = recommendedSettings.value.max_execution_time;
  return currentValue > recommendedValue;
});

const isMaxInputTimeExceeded = computed(() => {
  const currentValue = phpSettings.value.max_input_time;
  const recommendedValue = recommendedSettings.value.max_input_time;
  return currentValue > recommendedValue;
});

// 업그레이드 알림 표시 여부
const shouldShowUpgradeAlert = computed(() => {
  return isMemoryLimitExceeded.value || 
         isUploadMaxFilesizeExceeded.value || 
         isPostMaxSizeExceeded.value || 
         isMaxExecutionTimeExceeded.value || 
         isMaxInputTimeExceeded.value;
});

// 슬라이더 값 변경 감지
const updateSliderValues = () => {
  // Memory Limit
  const memoryValue = parsePlanValue(phpSettings.value.memory_limit);
  memoryLimitValue.value = Math.min(Math.max(memoryValue, memoryLimitRange.value.min), memoryLimitRange.value.max);
  
  // Upload Max Filesize
  const uploadValue = parsePlanValue(phpSettings.value.upload_max_filesize);
  uploadMaxFilesizeValue.value = Math.min(Math.max(uploadValue, uploadMaxFilesizeRange.value.min), uploadMaxFilesizeRange.value.max);
  
  // Post Max Size
  const postValue = parsePlanValue(phpSettings.value.post_max_size);
  postMaxSizeValue.value = Math.min(Math.max(postValue, postMaxSizeRange.value.min), postMaxSizeRange.value.max);
  
  // Max Execution Time
  maxExecutionTimeValue.value = Math.min(Math.max(phpSettings.value.max_execution_time, maxExecutionTimeRange.value.min), maxExecutionTimeRange.value.max);
  
  // Max Input Time
  maxInputTimeValue.value = Math.min(Math.max(phpSettings.value.max_input_time, maxInputTimeRange.value.min), maxInputTimeRange.value.max);
};

// 슬라이더 값 변경 시 phpSettings 업데이트
const updatePhpSettingsFromSliders = () => {
  // 플랜 기준값으로 제한
  const planMemoryLimit = parsePlanValue(recommendedSettings.value.memory_limit);
  const planUploadMaxFilesize = parsePlanValue(recommendedSettings.value.upload_max_filesize);
  const planPostMaxSize = parsePlanValue(recommendedSettings.value.post_max_size);
  const planMaxExecutionTime = recommendedSettings.value.max_execution_time;
  const planMaxInputTime = recommendedSettings.value.max_input_time;

  // 슬라이더 값을 플랜 기준값으로 제한
  memoryLimitValue.value = Math.min(memoryLimitValue.value, planMemoryLimit);
  uploadMaxFilesizeValue.value = Math.min(uploadMaxFilesizeValue.value, planUploadMaxFilesize);
  postMaxSizeValue.value = Math.min(postMaxSizeValue.value, planPostMaxSize);
  maxExecutionTimeValue.value = Math.min(maxExecutionTimeValue.value, planMaxExecutionTime);
  maxInputTimeValue.value = Math.min(maxInputTimeValue.value, planMaxInputTime);

  phpSettings.value.memory_limit = formatMemory(memoryLimitValue.value);
  phpSettings.value.upload_max_filesize = formatMemory(uploadMaxFilesizeValue.value);
  phpSettings.value.post_max_size = formatMemory(postMaxSizeValue.value);
  phpSettings.value.max_execution_time = maxExecutionTimeValue.value;
  phpSettings.value.max_input_time = maxInputTimeValue.value;
  
  // 업그레이드 알림 업데이트
  showUpgradeAlert.value = shouldShowUpgradeAlert.value;
};

// PHP 설정 업데이트
const updatePhpSettings = async () => {
  isUpdatingSettings.value = true;
  settingsMessage.value = '';
  settingsSuccess.value = false;

  try {
    // reactive 객체를 일반 객체로 변환
    const settingsData = {
      memory_limit: phpSettings.value.memory_limit,
      upload_max_filesize: phpSettings.value.upload_max_filesize,
      post_max_size: phpSettings.value.post_max_size,
      max_execution_time: parseInt(phpSettings.value.max_execution_time),
      max_input_time: parseInt(phpSettings.value.max_input_time),
    };

    // 디버깅을 위한 로그
    console.log('전송할 데이터:', settingsData);
    console.log('현재 phpSettings.value:', phpSettings.value);

    const response = await axios.post(route('server.php-settings.update', server.id), settingsData);
    
    if (response.data.success) {
      settingsMessage.value = response.data.message;
      settingsSuccess.value = true;
    } else {
      settingsMessage.value = response.data.message;
      settingsSuccess.value = false;
    }
  } catch (error) {
    settingsMessage.value = error.response?.data?.message || 'PHP 설정 업데이트 중 오류가 발생했습니다.';
    settingsSuccess.value = false;
  } finally {
    isUpdatingSettings.value = false;
  }
};

// 권장값으로 설정
const applyRecommendedSettings = () => {
  phpSettings.value = { ...recommendedSettings.value };
  updateSliderValues();
  showUpgradeAlert.value = false;
  updatePhpSettings();
};

// 슬라이더 값 변경 감지
import { watch } from 'vue';

watch([memoryLimitValue, uploadMaxFilesizeValue, postMaxSizeValue, maxExecutionTimeValue, maxInputTimeValue], () => {
  updatePhpSettingsFromSliders();
});

// 페이지 로드 시 PHP 설정 조회
onMounted(() => {
  loadPhpSettings();
});
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <div class="max-w-3xl mx-auto py-10 px-4 space-y-10">
          <h1 class="text-3xl font-bold text-white mb-4">PHP 설정</h1>
          <p class="text-white/70 mb-8">PHP 버전과 성능 설정을 관리할 수 있습니다.</p>
          
          <!-- PHP 버전 설정 -->
          <div class="bg-white/10 rounded-xl border border-white/20 p-6 mb-6">
            <h2 class="text-xl font-bold text-white mb-4">PHP 버전 설정</h2>
            <div class="mb-4">
              <p class="text-white/70 text-sm mb-2">현재 PHP 버전: <span class="font-bold text-green-400">{{ server.php_version || '8.0' }}</span></p>
            </div>
            <form @submit.prevent="changePhpVersion" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
              <div class="relative flex-1 min-w-0">
                <select 
                  v-model="selectedPhpVersion"
                  class="w-full appearance-none bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 pr-10 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-400/20 transition-all duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                  :disabled="isChangingPhp"
                  style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23ffffff%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 12px center; background-size: 12px auto;"
                >
                  <option 
                    v-for="version in availablePhpVersions" 
                    :key="version.value" 
                    :value="version.value"
                    :disabled="isVersionDisabled(version)"
                    class="bg-gray-800 text-white py-2"
                  >
                    {{ version.label }}
                    <span v-if="isVersionDisabled(version)"> (이미 설치됨)</span>
                  </option>
                </select>
                <!-- 커스텀 화살표 아이콘 -->
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                  <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                  </svg>
                </div>
              </div>
              <button 
                type="submit"
                :disabled="isChangingPhp || selectedPhpVersion === server.php_version"
                :class="[
                  'px-8 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-400/50 min-w-[120px]',
                  isChangingPhp || selectedPhpVersion === server.php_version
                    ? 'bg-gray-600/50 text-white/50 cursor-not-allowed shadow-none'
                    : 'bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600 shadow-lg hover:shadow-xl'
                ]"
              >
                <div class="flex items-center justify-center gap-2">
                  <svg v-if="isChangingPhp" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                  </svg>
                  <span v-if="isChangingPhp">변경 중...</span>
                  <span v-else>변경</span>
                </div>
              </button>
            </form>
            <!-- 메시지 표시 -->
            <div v-if="phpChangeMessage" class="mt-4 p-4 rounded-xl border-l-4 transition-all duration-300" :class="phpChangeSuccess ? 'bg-green-500/10 border-green-400 text-green-300' : 'bg-red-500/10 border-red-400 text-red-300'">
              <div class="flex items-center gap-3">
                <svg v-if="phpChangeSuccess" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg v-else class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ phpChangeMessage }}</span>
              </div>
            </div>
          </div>

          <!-- PHP 성능 설정 -->
          <div class="bg-white/10 rounded-xl border border-white/20 p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-xl font-bold text-white">PHP 성능 설정</h2>
              <div class="flex items-center gap-2">
                <span class="text-white/60 text-sm">현재 플랜: {{ currentPlan }}</span>
                <button 
                  @click="applyRecommendedSettings"
                  class="px-4 py-2 bg-blue-500/20 text-blue-300 rounded-lg text-sm hover:bg-blue-500/30 transition-colors"
                >
                  권장값 적용
                </button>
              </div>
            </div>

            <!-- 기본값 안내 메시지 -->
            <div v-if="isDefaultSettings" class="mb-6 p-4 bg-yellow-500/10 border border-yellow-400/20 rounded-lg">
              <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div>
                  <p class="text-yellow-300 font-medium">기본 설정 감지</p>
                  <p class="text-yellow-200 text-sm">현재 vhost.conf에 PHP 성능 설정이 없습니다. 플랜에 맞는 권장값이 자동으로 적용되었습니다.</p>
                </div>
              </div>
            </div>

            <!-- 플랜별 권장 설정 정보 -->
            <div class="mb-6 p-4 bg-blue-500/10 border border-blue-400/20 rounded-lg">
              <h3 class="text-blue-300 font-medium mb-3">플랜별 권장 설정</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
                <div class="bg-white/5 p-3 rounded">
                  <div class="font-medium text-blue-200 mb-2">무료</div>
                  <div class="space-y-1 text-blue-100">
                    <div>Memory: 128M</div>
                    <div>Upload: 16M</div>
                    <div>Post: 20M</div>
                    <div>Exec: 30s</div>
                  </div>
                </div>
                <div class="bg-white/5 p-3 rounded">
                  <div class="font-medium text-blue-200 mb-2">Starter</div>
                  <div class="space-y-1 text-blue-100">
                    <div>Memory: 256M</div>
                    <div>Upload: 32M</div>
                    <div>Post: 40M</div>
                    <div>Exec: 60s</div>
                  </div>
                </div>
                <div class="bg-white/5 p-3 rounded">
                  <div class="font-medium text-blue-200 mb-2">Business</div>
                  <div class="space-y-1 text-blue-100">
                    <div>Memory: 512M</div>
                    <div>Upload: 64M</div>
                    <div>Post: 80M</div>
                    <div>Exec: 120s</div>
                  </div>
                </div>
                <div class="bg-white/5 p-3 rounded">
                  <div class="font-medium text-blue-200 mb-2">Enterprise</div>
                  <div class="space-y-1 text-blue-100">
                    <div>Memory: 1024M</div>
                    <div>Upload: 128M</div>
                    <div>Post: 128M</div>
                    <div>Exec: 300s</div>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-if="isLoadingSettings" class="flex items-center justify-center py-8">
              <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
              </svg>
              <span class="ml-3 text-white">설정을 불러오는 중...</span>
            </div>

            <form v-else @submit.prevent="updatePhpSettings" class="space-y-6">
              <!-- 플랜 업그레이드 알림 -->
              <div v-if="showUpgradeAlert" class="mb-6 p-4 bg-orange-500/10 border border-orange-400/20 rounded-lg">
                <div class="flex items-center gap-3">
                  <svg class="w-5 h-5 text-orange-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                  </svg>
                  <div>
                    <p class="text-orange-300 font-medium">플랜 업그레이드 필요</p>
                    <p class="text-orange-200 text-sm">현재 설정이 플랜 기준값을 초과했습니다. 더 높은 설정을 사용하려면 플랜을 업그레이드하세요.</p>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Memory Limit -->
                <div class="bg-white/5 p-4 rounded-lg border border-white/10">
                  <div class="flex items-center justify-between mb-3">
                    <label class="block text-white/80 text-sm font-medium">Memory Limit</label>
                    <span class="text-xs text-white/60">{{ formatMemory(phpSettings.memory_limit) }}</span>
                  </div>
                  <p class="text-white/60 text-xs mb-4">PHP 스크립트가 사용할 수 있는 최대 메모리 용량</p>
                  <div class="relative">
                    <input 
                      v-model="memoryLimitValue"
                      type="range" 
                      :min="memoryLimitRange.min" 
                      :max="memoryLimitRange.max" 
                      :step="memoryLimitRange.step"
                      class="w-full h-2 bg-white/20 rounded-lg appearance-none cursor-pointer slider"
                      :class="{ 'slider-exceeded': isMemoryLimitExceeded }"
                    />
                    <div class="flex justify-between text-xs text-white/60 mt-2">
                      <span>{{ formatMemory(memoryLimitRange.min + 'M') }}</span>
                      <span v-if="recommendedSettings.memory_limit" class="text-blue-300">권장: {{ recommendedSettings.memory_limit }}</span>
                      <span>{{ formatMemory(memoryLimitRange.max + 'M') }}</span>
                    </div>
                  </div>
                </div>

                <!-- Upload Max Filesize -->
                <div class="bg-white/5 p-4 rounded-lg border border-white/10">
                  <div class="flex items-center justify-between mb-3">
                    <label class="block text-white/80 text-sm font-medium">Upload Max Filesize</label>
                    <span class="text-xs text-white/60">{{ formatMemory(phpSettings.upload_max_filesize) }}</span>
                  </div>
                  <p class="text-white/60 text-xs mb-4">한 번에 업로드할 수 있는 파일의 최대 크기</p>
                  <div class="relative">
                    <input 
                      v-model="uploadMaxFilesizeValue"
                      type="range" 
                      :min="uploadMaxFilesizeRange.min" 
                      :max="uploadMaxFilesizeRange.max" 
                      :step="uploadMaxFilesizeRange.step"
                      class="w-full h-2 bg-white/20 rounded-lg appearance-none cursor-pointer slider"
                      :class="{ 'slider-exceeded': isUploadMaxFilesizeExceeded }"
                    />
                    <div class="flex justify-between text-xs text-white/60 mt-2">
                      <span>{{ formatMemory(uploadMaxFilesizeRange.min + 'M') }}</span>
                      <span v-if="recommendedSettings.upload_max_filesize" class="text-blue-300">권장: {{ recommendedSettings.upload_max_filesize }}</span>
                      <span>{{ formatMemory(uploadMaxFilesizeRange.max + 'M') }}</span>
                    </div>
                  </div>
                </div>

                <!-- Post Max Size -->
                <div class="bg-white/5 p-4 rounded-lg border border-white/10">
                  <div class="flex items-center justify-between mb-3">
                    <label class="block text-white/80 text-sm font-medium">Post Max Size</label>
                    <span class="text-xs text-white/60">{{ formatMemory(phpSettings.post_max_size) }}</span>
                  </div>
                  <p class="text-white/60 text-xs mb-4">POST 요청으로 전송할 수 있는 데이터의 최대 크기</p>
                  <div class="relative">
                    <input 
                      v-model="postMaxSizeValue"
                      type="range" 
                      :min="postMaxSizeRange.min" 
                      :max="postMaxSizeRange.max" 
                      :step="postMaxSizeRange.step"
                      class="w-full h-2 bg-white/20 rounded-lg appearance-none cursor-pointer slider"
                      :class="{ 'slider-exceeded': isPostMaxSizeExceeded }"
                    />
                    <div class="flex justify-between text-xs text-white/60 mt-2">
                      <span>{{ formatMemory(postMaxSizeRange.min + 'M') }}</span>
                      <span v-if="recommendedSettings.post_max_size" class="text-blue-300">권장: {{ recommendedSettings.post_max_size }}</span>
                      <span>{{ formatMemory(postMaxSizeRange.max + 'M') }}</span>
                    </div>
                  </div>
                </div>

                <!-- Max Execution Time -->
                <div class="bg-white/5 p-4 rounded-lg border border-white/10">
                  <div class="flex items-center justify-between mb-3">
                    <label class="block text-white/80 text-sm font-medium">Max Execution Time</label>
                    <span class="text-xs text-white/60">{{ phpSettings.max_execution_time }}초</span>
                  </div>
                  <p class="text-white/60 text-xs mb-4">PHP 스크립트가 실행될 수 있는 최대 시간</p>
                  <div class="relative">
                    <input 
                      v-model="maxExecutionTimeValue"
                      type="range" 
                      :min="maxExecutionTimeRange.min" 
                      :max="maxExecutionTimeRange.max" 
                      :step="maxExecutionTimeRange.step"
                      class="w-full h-2 bg-white/20 rounded-lg appearance-none cursor-pointer slider"
                      :class="{ 'slider-exceeded': isMaxExecutionTimeExceeded }"
                    />
                    <div class="flex justify-between text-xs text-white/60 mt-2">
                      <span>{{ maxExecutionTimeRange.min }}초</span>
                      <span v-if="recommendedSettings.max_execution_time" class="text-blue-300">권장: {{ recommendedSettings.max_execution_time }}초</span>
                      <span>{{ maxExecutionTimeRange.max }}초</span>
                    </div>
                  </div>
                </div>

                <!-- Max Input Time -->
                <div class="bg-white/5 p-4 rounded-lg border border-white/10">
                  <div class="flex items-center justify-between mb-3">
                    <label class="block text-white/80 text-sm font-medium">Max Input Time</label>
                    <span class="text-xs text-white/60">{{ phpSettings.max_input_time }}초</span>
                  </div>
                  <p class="text-white/60 text-xs mb-4">PHP가 입력 데이터를 파싱할 수 있는 최대 시간</p>
                  <div class="relative">
                    <input 
                      v-model="maxInputTimeValue"
                      type="range" 
                      :min="maxInputTimeRange.min" 
                      :max="maxInputTimeRange.max" 
                      :step="maxInputTimeRange.step"
                      class="w-full h-2 bg-white/20 rounded-lg appearance-none cursor-pointer slider"
                      :class="{ 'slider-exceeded': isMaxInputTimeExceeded }"
                    />
                    <div class="flex justify-between text-xs text-white/60 mt-2">
                      <span>{{ maxInputTimeRange.min }}초</span>
                      <span v-if="recommendedSettings.max_input_time" class="text-blue-300">권장: {{ recommendedSettings.max_input_time }}초</span>
                      <span>{{ maxInputTimeRange.max }}초</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <button 
                  type="submit"
                  :disabled="isUpdatingSettings || shouldShowUpgradeAlert"
                  :class="[
                    'px-8 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-400/50',
                    isUpdatingSettings || shouldShowUpgradeAlert
                      ? 'bg-gray-600/50 text-white/50 cursor-not-allowed shadow-none'
                      : 'bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600 shadow-lg hover:shadow-xl'
                  ]"
                >
                  <div class="flex items-center justify-center gap-2">
                    <svg v-if="isUpdatingSettings" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <span v-if="isUpdatingSettings">업데이트 중...</span>
                    <span v-else-if="shouldShowUpgradeAlert">플랜 업그레이드 필요</span>
                    <span v-else>설정 저장</span>
                  </div>
                </button>
              </div>
            </form>

            <!-- 설정 메시지 표시 -->
            <div v-if="settingsMessage" class="mt-4 p-4 rounded-xl border-l-4 transition-all duration-300" :class="settingsSuccess ? 'bg-green-500/10 border-green-400 text-green-300' : 'bg-red-500/10 border-red-400 text-red-300'">
              <div class="flex items-center gap-3">
                <svg v-if="settingsSuccess" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg v-else class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ settingsMessage }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* 슬라이더 스타일 */
.slider {
  -webkit-appearance: none;
  appearance: none;
  height: 8px;
  border-radius: 4px;
  background: linear-gradient(to right, #8b5cf6, #3b82f6);
  outline: none;
  transition: all 0.3s ease;
}

.slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #ffffff;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
}

.slider::-webkit-slider-thumb:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.slider::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #ffffff;
  cursor: pointer;
  border: none;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
}

.slider::-moz-range-thumb:hover {
  transform: scale(1.1);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

/* 플랜 초과 시 슬라이더 스타일 */
.slider-exceeded {
  background: linear-gradient(to right, #f59e0b, #ef4444);
}

.slider-exceeded::-webkit-slider-thumb {
  background: #f59e0b;
}

.slider-exceeded::-moz-range-thumb {
  background: #f59e0b;
}
</style> 