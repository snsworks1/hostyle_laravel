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
const user = page.props.auth?.user || {};



// 데이터베이스 관리 관련 상태
const databases = ref([]);
const isLoadingDatabases = ref(false);
const showCreateDatabaseModal = ref(false);
const isCreatingDatabase = ref(false);
const isDeletingDatabase = ref(false);
const databaseMessage = ref('');
const databaseSuccess = ref(false);

// 데이터베이스 삭제 검증 관련 상태
const isVerifyingDatabaseDeletion = ref(false);
const databaseDeletionVerificationMessage = ref('');
const databaseDeletionVerificationSuccess = ref(false);
const isDatabaseDeletionVerified = ref(false);
const databaseDeletionPassword = ref('');
const showDatabaseDeletionModal = ref(false);
const databaseToDelete = ref(null);

// 데이터베이스 패스워드 변경 관련 상태
const isVerifyingDatabasePasswordChange = ref(false);
const databasePasswordChangeVerificationMessage = ref('');
const databasePasswordChangeVerificationSuccess = ref(false);
const isDatabasePasswordChangeVerified = ref(false);
const databasePasswordChangePassword = ref('');
const showDatabasePasswordChangeModal = ref(false);
const databaseToChangePassword = ref(null);
const newDatabasePassword = ref('');

// 데이터베이스 생성 폼
const newDatabase = ref({
  dbName: '',
  dbUsername: '',
  dbPassword: ''
});

// 데이터베이스 목록 조회
const fetchDatabases = async () => {
  isLoadingDatabases.value = true;
  try {
    const response = await axios.get(route('server.databases.fetch', server.id));
    console.log('Database API Response:', response.data); // 디버깅용
    
    if (response.data.success) {
      // 데이터베이스 배열이 실제로 있는지 확인
      const dbList = response.data.databases || [];
      if (Array.isArray(dbList)) {
        databases.value = dbList;
        if (dbList.length === 0) {
          databaseMessage.value = '등록된 데이터베이스가 없습니다.';
          databaseSuccess.value = true;
        }
      } else {
        console.error('Invalid database list format:', dbList);
        databaseMessage.value = '데이터베이스 목록 형식이 올바르지 않습니다.';
        databaseSuccess.value = false;
      }
    } else {
      databaseMessage.value = response.data.error || '데이터베이스 목록을 불러오는데 실패했습니다.';
      databaseSuccess.value = false;
    }
  } catch (error) {
    console.error('Database fetch error:', error);
    databaseMessage.value = error.response?.data?.error || '데이터베이스 목록을 불러오는 중 오류가 발생했습니다.';
    databaseSuccess.value = false;
  } finally {
    isLoadingDatabases.value = false;
  }
};

// 데이터베이스 생성
const createDatabase = async () => {
  if (!newDatabase.value.dbName || !newDatabase.value.dbUsername || !newDatabase.value.dbPassword) {
    databaseMessage.value = '모든 필드를 입력해주세요.';
    databaseSuccess.value = false;
    return;
  }

  isCreatingDatabase.value = true;
  try {
    const response = await axios.post(route('server.databases.create', server.id), newDatabase.value);
    if (response.data.success) {
      databaseMessage.value = response.data.message;
      databaseSuccess.value = true;
      showCreateDatabaseModal.value = false;
      // 폼 초기화
      newDatabase.value = {
        dbName: '',
        dbUsername: '',
        dbPassword: ''
      };
      // 데이터베이스 목록 새로고침
      await fetchDatabases();
    } else {
      databaseMessage.value = response.data.error || '데이터베이스 생성에 실패했습니다.';
      databaseSuccess.value = false;
    }
  } catch (error) {
    databaseMessage.value = error.response?.data?.error || '데이터베이스 생성 중 오류가 발생했습니다.';
    databaseSuccess.value = false;
  } finally {
    isCreatingDatabase.value = false;
  }
};

// 데이터베이스 삭제 검증
const verifyDatabaseDeletion = async () => {
  if (!databaseDeletionPassword.value) {
    databaseDeletionVerificationMessage.value = '대시보드 로그인 패스워드를 입력해주세요.';
    databaseDeletionVerificationSuccess.value = false;
    return;
  }

  isVerifyingDatabaseDeletion.value = true;
  databaseDeletionVerificationMessage.value = '';
  databaseDeletionVerificationSuccess.value = false;

  try {
    const response = await axios.post(route('server.databases.verify-deletion', server.id), {
      dashboard_password: databaseDeletionPassword.value
    });

    if (response.data.success) {
      databaseDeletionVerificationMessage.value = response.data.message;
      databaseDeletionVerificationSuccess.value = true;
      isDatabaseDeletionVerified.value = true;
    } else {
      databaseDeletionVerificationMessage.value = response.data.message;
      databaseDeletionVerificationSuccess.value = false;
      isDatabaseDeletionVerified.value = false;
    }
  } catch (error) {
    databaseDeletionVerificationMessage.value = error.response?.data?.message || '사용자 검증 중 오류가 발생했습니다.';
    databaseDeletionVerificationSuccess.value = false;
    isDatabaseDeletionVerified.value = false;
  } finally {
    isVerifyingDatabaseDeletion.value = false;
  }
};

// 데이터베이스 삭제 검증 상태 초기화
const resetDatabaseDeletionVerification = () => {
  isDatabaseDeletionVerified.value = false;
  databaseDeletionVerificationMessage.value = '';
  databaseDeletionVerificationSuccess.value = false;
  databaseDeletionPassword.value = '';
  databaseToDelete.value = null;
};

// 데이터베이스 삭제 모달 닫기
const closeDatabaseDeletionModal = () => {
  showDatabaseDeletionModal.value = false;
  resetDatabaseDeletionVerification();
};

// 데이터베이스 패스워드 변경 검증 상태 초기화
const resetDatabasePasswordChangeVerification = () => {
  isDatabasePasswordChangeVerified.value = false;
  databasePasswordChangeVerificationMessage.value = '';
  databasePasswordChangeVerificationSuccess.value = false;
  databasePasswordChangePassword.value = '';
  databaseToChangePassword.value = null;
  newDatabasePassword.value = '';
};

// 데이터베이스 패스워드 변경 모달 닫기
const closeDatabasePasswordChangeModal = () => {
  showDatabasePasswordChangeModal.value = false;
  resetDatabasePasswordChangeVerification();
};

// 데이터베이스 패스워드 변경 모달 열기
const openDatabasePasswordChangeModal = (db) => {
  console.log('Opening password change modal for:', db); // 디버깅용
  databaseToChangePassword.value = db;
  showDatabasePasswordChangeModal.value = true;
  // 검증 상태만 초기화 (모달은 열어둠)
  isDatabasePasswordChangeVerified.value = false;
  databasePasswordChangeVerificationMessage.value = '';
  databasePasswordChangeVerificationSuccess.value = false;
  databasePasswordChangePassword.value = '';
  newDatabasePassword.value = '';
};

// 데이터베이스 삭제 모달 열기
const openDatabaseDeletionModal = (dbName) => {
  console.log('Opening deletion modal for:', dbName); // 디버깅용
  databaseToDelete.value = dbName;
  showDatabaseDeletionModal.value = true;
  // 검증 상태만 초기화 (모달은 열어둠)
  isDatabaseDeletionVerified.value = false;
  databaseDeletionVerificationMessage.value = '';
  databaseDeletionVerificationSuccess.value = false;
  databaseDeletionPassword.value = '';
};

// 데이터베이스 패스워드 변경 검증
const verifyDatabasePasswordChange = async () => {
  if (!databasePasswordChangePassword.value) {
    databasePasswordChangeVerificationMessage.value = '대시보드 로그인 패스워드를 입력해주세요.';
    databasePasswordChangeVerificationSuccess.value = false;
    return;
  }

  isVerifyingDatabasePasswordChange.value = true;
  databasePasswordChangeVerificationMessage.value = '';
  databasePasswordChangeVerificationSuccess.value = false;

  try {
    const response = await axios.post(route('server.databases.verify-password-change', server.id), {
      dashboard_password: databasePasswordChangePassword.value
    });

    if (response.data.success) {
      databasePasswordChangeVerificationMessage.value = response.data.message;
      databasePasswordChangeVerificationSuccess.value = true;
      isDatabasePasswordChangeVerified.value = true;
    } else {
      databasePasswordChangeVerificationMessage.value = response.data.message;
      databasePasswordChangeVerificationSuccess.value = false;
      isDatabasePasswordChangeVerified.value = false;
    }
  } catch (error) {
    databasePasswordChangeVerificationMessage.value = error.response?.data?.message || '사용자 검증 중 오류가 발생했습니다.';
    databasePasswordChangeVerificationSuccess.value = false;
    isDatabasePasswordChangeVerified.value = false;
  } finally {
    isVerifyingDatabasePasswordChange.value = false;
  }
};

// 데이터베이스 패스워드 변경
const changeDatabasePassword = async () => {
  if (!isDatabasePasswordChangeVerified.value) {
    databasePasswordChangeVerificationMessage.value = '먼저 사용자 검증을 완료해주세요.';
    databasePasswordChangeVerificationSuccess.value = false;
    return;
  }

  if (!databaseToChangePassword.value) {
    databasePasswordChangeVerificationMessage.value = '변경할 데이터베이스 정보가 없습니다.';
    databasePasswordChangeVerificationSuccess.value = false;
    return;
  }

  if (!newDatabasePassword.value || newDatabasePassword.value.length < 8) {
    databasePasswordChangeVerificationMessage.value = '새 패스워드는 최소 8자 이상이어야 합니다.';
    databasePasswordChangeVerificationSuccess.value = false;
    return;
  }

  isDeletingDatabase.value = true; // 재사용
  try {
    const response = await axios.post(route('server.databases.change-password', server.id), {
      dbName: databaseToChangePassword.value.dbName || databaseToChangePassword.value.name,
      dbUsername: databaseToChangePassword.value.dbUser || databaseToChangePassword.value.dbUsername || databaseToChangePassword.value.username,
      new_password: newDatabasePassword.value,
      dashboard_password: databasePasswordChangePassword.value
    });
    if (response.data.success) {
      databaseMessage.value = response.data.message;
      databaseSuccess.value = true;
      // 모달 닫기
      closeDatabasePasswordChangeModal();
      // 데이터베이스 목록 새로고침
      await fetchDatabases();
    } else {
      databasePasswordChangeVerificationMessage.value = response.data.error || '데이터베이스 패스워드 변경에 실패했습니다.';
      databasePasswordChangeVerificationSuccess.value = false;
    }
  } catch (error) {
    console.error('패스워드 변경 오류:', error);
    databasePasswordChangeVerificationMessage.value = error.response?.data?.error || '데이터베이스 패스워드 변경 중 오류가 발생했습니다.';
    databasePasswordChangeVerificationSuccess.value = false;
  } finally {
    isDeletingDatabase.value = false;
  }
};

// 데이터베이스 삭제
const deleteDatabase = async () => {
  if (!isDatabaseDeletionVerified.value) {
    databaseDeletionVerificationMessage.value = '먼저 사용자 검증을 완료해주세요.';
    databaseDeletionVerificationSuccess.value = false;
    return;
  }

  if (!databaseToDelete.value) {
    databaseDeletionVerificationMessage.value = '삭제할 데이터베이스 정보가 없습니다.';
    databaseDeletionVerificationSuccess.value = false;
    return;
  }

  isDeletingDatabase.value = true;
  try {
    const response = await axios.delete(route('server.databases.delete', server.id), {
      data: { 
        dbName: databaseToDelete.value,
        dashboard_password: databaseDeletionPassword.value
      }
    });
    if (response.data.success) {
      databaseMessage.value = response.data.message;
      databaseSuccess.value = true;
      // 모달 닫기
      closeDatabaseDeletionModal();
      // 데이터베이스 목록 새로고침
      await fetchDatabases();
    } else {
      databaseDeletionVerificationMessage.value = response.data.error || '데이터베이스 삭제에 실패했습니다.';
      databaseDeletionVerificationSuccess.value = false;
    }
  } catch (error) {
    databaseDeletionVerificationMessage.value = error.response?.data?.error || '데이터베이스 삭제 중 오류가 발생했습니다.';
    databaseDeletionVerificationSuccess.value = false;
  } finally {
    isDeletingDatabase.value = false;
  }
};



// 초기 데이터베이스인지 확인하는 함수
const isInitialDatabase = (db) => {
  const dbName = db.dbName || db.name;
  const dbUser = db.dbUser || db.dbUsername || db.username;
  const serverDomain = server.domain;
  
  // 데이터베이스명이나 사용자명이 서버 도메인과 일치하면 초기 데이터베이스
  return dbName === serverDomain || dbUser === serverDomain;
};

// 컴포넌트 마운트 시 데이터베이스 목록 조회
onMounted(() => {
  fetchDatabases();
});
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <div class="max-w-6xl mx-auto py-10 px-4 space-y-10">
          <h1 class="text-3xl font-bold text-white mb-4">MySQL 설정</h1>
          <p class="text-white/70 mb-8">MySQL 데이터베이스 패스워드와 사용자를 관리할 수 있습니다.</p>
          


          <!-- 데이터베이스 관리 -->
          <div class="bg-white/10 rounded-xl border border-white/20 p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
              <div class="lg:col-span-2">
                <h2 class="text-xl font-bold text-white mb-4">데이터베이스 관리</h2>
                <!-- 초기 데이터베이스 안내 -->
                <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-400/30 rounded-xl p-4">
                  <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                      <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <h3 class="text-blue-300 font-semibold text-sm mb-2">초기 데이터베이스 안내</h3>
                      <p class="text-blue-200 text-sm leading-relaxed">
                        서버 도메인명과 동일한 데이터베이스는 <span class="font-semibold text-blue-300">초기 데이터베이스</span>입니다. 
                        초기 데이터베이스의 패스워드는 <span class="font-semibold text-yellow-300 bg-yellow-500/20 px-1 rounded">대시보드 로그인 패스워드</span>와 동일합니다.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="lg:col-span-1 flex items-center justify-center lg:justify-end">
                <button 
                  @click="showCreateDatabaseModal = true"
                  class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-8 py-4 rounded-xl font-semibold hover:from-purple-600 hover:to-blue-600 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center gap-3 group w-full lg:w-auto"
                >
                  <div class="w-6 h-6 bg-white/20 rounded-lg flex items-center justify-center group-hover:bg-white/30 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                  </div>
                  <span>데이터베이스 생성</span>
                </button>
              </div>
            </div>



            <!-- 데이터베이스 목록 -->
            <div class="overflow-hidden rounded-xl border border-white/10">
              <div v-if="isLoadingDatabases" class="flex flex-col items-center justify-center py-16">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500/20 to-blue-500/20 rounded-full flex items-center justify-center mb-4">
                  <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                  </svg>
                </div>
                <h3 class="text-lg font-semibold text-white/80 mb-2">데이터베이스 목록을 불러오는 중...</h3>
                <p class="text-white/60 text-sm">잠시만 기다려주세요</p>
              </div>
              
              <table v-else-if="databases.length > 0" class="min-w-full text-white">
                <thead>
                  <tr class="bg-gradient-to-r from-white/10 to-white/5">
                    <th class="px-8 py-4 text-left font-semibold text-white/90 w-2/5">데이터베이스명</th>
                    <th class="px-8 py-4 text-left font-semibold text-white/90 w-2/5">사용자명</th>
                    <th class="px-8 py-4 text-left font-semibold text-white/90 w-1/5">관리</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="db in databases" :key="db.dbName || db.name" class="border-b border-white/5 hover:bg-white/5 transition-all duration-200">
                    <td class="px-8 py-6">
                      <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                          </svg>
                        </div>
                        <div class="flex flex-col gap-1">
                          <span class="font-semibold text-white">{{ db.dbName || db.name || 'N/A' }}</span>
                          <!-- 초기 데이터베이스 표시 -->
                          <span v-if="isInitialDatabase(db)" class="text-xs text-blue-300 bg-blue-500/20 px-2 py-1 rounded-full inline-flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            초기 데이터베이스
                          </span>
                        </div>
                      </div>
                    </td>
                    <td class="px-8 py-6">
                      <div class="flex flex-col gap-1">
                        <span class="text-white/90 font-medium">{{ db.dbUser || db.dbUsername || db.username || 'N/A' }}</span>
                        <!-- 초기 패스워드 안내 -->
                        <span v-if="isInitialDatabase(db)" class="text-xs text-yellow-300 bg-yellow-500/20 px-2 py-1 rounded-full inline-flex items-center gap-1">
                          <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                          </svg>
                          초기 패스워드: 대시보드 로그인 패스워드
                        </span>
                      </div>
                    </td>
                    <td class="px-8 py-6">
                      <div class="flex items-center gap-3">
                        <button 
                          @click="openDatabasePasswordChangeModal(db)"
                          :disabled="isDeletingDatabase"
                          class="text-blue-400 hover:text-blue-300 transition-all duration-200 p-3 rounded-xl hover:bg-blue-500/10 disabled:opacity-50 disabled:cursor-not-allowed group"
                          title="패스워드 변경"
                        >
                          <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                          </svg>
                        </button>
                        <button 
                          @click="openDatabaseDeletionModal(db.dbName || db.name)"
                          :disabled="isDeletingDatabase"
                          class="text-red-400 hover:text-red-300 transition-all duration-200 p-3 rounded-xl hover:bg-red-500/10 disabled:opacity-50 disabled:cursor-not-allowed group"
                          title="삭제"
                        >
                          <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
              
              <div v-else class="text-center py-16">
                <div class="w-20 h-20 bg-gradient-to-r from-purple-500/20 to-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                  <svg class="w-10 h-10 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                  </svg>
                </div>
                <h3 class="text-xl font-semibold text-white/80 mb-2">등록된 데이터베이스가 없습니다</h3>
                <p class="text-white/60 mb-6">새로운 데이터베이스를 생성하여 웹사이트를 시작해보세요</p>
                <button 
                  @click="showCreateDatabaseModal = true"
                  class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-6 py-3 rounded-xl font-semibold hover:from-purple-600 hover:to-blue-600 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center gap-2 mx-auto"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  첫 번째 데이터베이스 생성
                </button>
              </div>
            </div>

            <!-- 데이터베이스 관련 메시지 -->
            <div v-if="databaseMessage" class="mt-4 p-4 rounded-xl border-l-4 transition-all duration-300" :class="databaseSuccess ? 'bg-green-500/10 border-green-400 text-green-300' : 'bg-red-500/10 border-red-400 text-red-300'">
              <div class="flex items-center gap-3">
                <svg v-if="databaseSuccess" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg v-else class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ databaseMessage }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 데이터베이스 생성 모달 -->
    <div v-if="showCreateDatabaseModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-bold text-white">새 데이터베이스 생성</h3>
          <button 
            @click="showCreateDatabaseModal = false"
            class="text-white/60 hover:text-white transition-colors duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="createDatabase" class="space-y-4">
          <div>
            <label class="block text-white/80 text-sm font-medium mb-2">데이터베이스명</label>
            <input 
              v-model="newDatabase.dbName"
              type="text" 
              class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 placeholder-white/50 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-400/20 transition-all duration-200" 
              placeholder="데이터베이스명 입력"
              required
            />
          </div>
          
          <div>
            <label class="block text-white/80 text-sm font-medium mb-2">사용자명</label>
            <input 
              v-model="newDatabase.dbUsername"
              type="text" 
              class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 placeholder-white/50 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-400/20 transition-all duration-200" 
              placeholder="사용자명 입력"
              required
            />
          </div>
          
          <div>
            <label class="block text-white/80 text-sm font-medium mb-2">비밀번호</label>
            <input 
              v-model="newDatabase.dbPassword"
              type="password" 
              class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 placeholder-white/50 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-400/20 transition-all duration-200" 
              placeholder="비밀번호 입력 (최소 8자)"
              required
            />
          </div>
          

          
          <div class="flex gap-3 pt-4">
            <button 
              type="button"
              @click="showCreateDatabaseModal = false"
              class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white hover:bg-white/20 transition-all duration-200"
            >
              취소
            </button>
            <button 
              type="submit"
              :disabled="isCreatingDatabase"
              :class="[
                'flex-1 px-4 py-3 rounded-xl font-semibold transition-all duration-300',
                isCreatingDatabase
                  ? 'bg-gray-600/50 text-white/50 cursor-not-allowed'
                  : 'bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600'
              ]"
            >
              <div class="flex items-center justify-center gap-2">
                <svg v-if="isCreatingDatabase" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span v-if="isCreatingDatabase">생성 중...</span>
                <span v-else>생성</span>
              </div>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- 데이터베이스 삭제 검증 모달 -->
    <div v-if="showDatabaseDeletionModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-bold text-white">데이터베이스 삭제</h3>
          <button 
            @click="closeDatabaseDeletionModal"
            class="text-white/60 hover:text-white transition-colors duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="mb-6">
          <div class="flex items-center gap-3 mb-4 p-4 bg-red-500/10 border border-red-400/20 rounded-lg">
            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div>
              <p class="text-red-300 font-medium">삭제할 데이터베이스: <span class="font-bold">{{ databaseToDelete }}</span></p>
              <p class="text-red-200 text-sm mt-1">이 작업은 되돌릴 수 없습니다.</p>
            </div>
          </div>
        </div>

        <!-- 검증이 완료되지 않은 경우 -->
        <div v-if="!isDatabaseDeletionVerified" class="space-y-4">
          <p class="text-white/70 text-sm">데이터베이스를 삭제하려면 대시보드 로그인 패스워드로 검증을 완료해주세요.</p>
          
          <div class="space-y-4">
            <div>
              <label class="block text-white/80 text-sm font-medium mb-2">대시보드 로그인 패스워드</label>
              <input 
                v-model="databaseDeletionPassword"
                type="password" 
                class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 placeholder-white/50 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-400/20 transition-all duration-200" 
                placeholder="패스워드 입력" 
                :disabled="isVerifyingDatabaseDeletion"
                @keyup.enter="verifyDatabaseDeletion"
              />
            </div>
            
            <button 
              @click="verifyDatabaseDeletion"
              :disabled="isVerifyingDatabaseDeletion || !databaseDeletionPassword"
              :class="[
                'w-full px-4 py-3 rounded-xl font-semibold transition-all duration-300',
                isVerifyingDatabaseDeletion || !databaseDeletionPassword
                  ? 'bg-gray-600/50 text-white/50 cursor-not-allowed'
                  : 'bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600'
              ]"
            >
              <div class="flex items-center justify-center gap-2">
                <svg v-if="isVerifyingDatabaseDeletion" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span v-if="isVerifyingDatabaseDeletion">검증 중...</span>
                <span v-else>검증</span>
              </div>
            </button>
          </div>

          <!-- 검증 메시지 표시 -->
          <div v-if="databaseDeletionVerificationMessage" class="p-4 rounded-xl border-l-4 transition-all duration-300" :class="databaseDeletionVerificationSuccess ? 'bg-green-500/10 border-green-400 text-green-300' : 'bg-red-500/10 border-red-400 text-red-300'">
            <div class="flex items-center gap-3">
              <svg v-if="databaseDeletionVerificationSuccess" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <svg v-else class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span class="font-medium">{{ databaseDeletionVerificationMessage }}</span>
            </div>
          </div>
        </div>

        <!-- 검증이 완료된 경우 -->
        <div v-else class="space-y-4">
          <div class="p-4 bg-green-500/10 border border-green-400/20 rounded-lg">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span class="text-green-300 font-medium text-sm">검증 완료</span>
            </div>
            <p class="text-green-200 text-sm">이제 데이터베이스를 삭제할 수 있습니다.</p>
          </div>

          <div class="flex gap-3">
            <button 
              @click="closeDatabaseDeletionModal"
              class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white hover:bg-white/20 transition-all duration-200"
            >
              취소
            </button>
            <button 
              @click="deleteDatabase"
              :disabled="isDeletingDatabase"
              :class="[
                'flex-1 px-4 py-3 rounded-xl font-semibold transition-all duration-300',
                isDeletingDatabase
                  ? 'bg-gray-600/50 text-white/50 cursor-not-allowed'
                  : 'bg-gradient-to-r from-red-500 to-red-600 text-white hover:from-red-600 hover:to-red-700'
              ]"
            >
              <div class="flex items-center justify-center gap-2">
                <svg v-if="isDeletingDatabase" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span v-if="isDeletingDatabase">삭제 중...</span>
                <span v-else>삭제</span>
              </div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- 데이터베이스 패스워드 변경 모달 -->
    <div v-if="showDatabasePasswordChangeModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-bold text-white">데이터베이스 패스워드 변경</h3>
          <button 
            @click="closeDatabasePasswordChangeModal"
            class="text-white/60 hover:text-white transition-colors duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div class="mb-6">
          <div class="flex items-center gap-3 mb-4 p-4 bg-blue-500/10 border border-blue-400/20 rounded-lg">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
              <p class="text-blue-300 font-medium">변경할 데이터베이스: <span class="font-bold">{{ databaseToChangePassword?.dbName || databaseToChangePassword?.name }}</span></p>
              <p class="text-blue-200 text-sm mt-1">사용자명: {{ databaseToChangePassword?.dbUser || databaseToChangePassword?.dbUsername || databaseToChangePassword?.username }}</p>
            </div>
          </div>
        </div>

        <!-- 검증이 완료되지 않은 경우 -->
        <div v-if="!isDatabasePasswordChangeVerified" class="space-y-4">
          <p class="text-white/70 text-sm">데이터베이스 패스워드를 변경하려면 대시보드 로그인 패스워드로 검증을 완료해주세요.</p>
          
          <div class="space-y-4">
            <div>
              <label class="block text-white/80 text-sm font-medium mb-2">대시보드 로그인 패스워드</label>
              <input 
                v-model="databasePasswordChangePassword"
                type="password" 
                class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 placeholder-white/50 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-400/20 transition-all duration-200" 
                placeholder="패스워드 입력" 
                :disabled="isVerifyingDatabasePasswordChange"
                @keyup.enter="verifyDatabasePasswordChange"
              />
            </div>
            
            <button 
              @click="verifyDatabasePasswordChange"
              :disabled="isVerifyingDatabasePasswordChange || !databasePasswordChangePassword"
              :class="[
                'w-full px-4 py-3 rounded-xl font-semibold transition-all duration-300',
                isVerifyingDatabasePasswordChange || !databasePasswordChangePassword
                  ? 'bg-gray-600/50 text-white/50 cursor-not-allowed'
                  : 'bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600'
              ]"
            >
              <div class="flex items-center justify-center gap-2">
                <svg v-if="isVerifyingDatabasePasswordChange" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                <span v-if="isVerifyingDatabasePasswordChange">검증 중...</span>
                <span v-else>검증</span>
              </div>
            </button>
          </div>

          <!-- 검증 메시지 표시 -->
          <div v-if="databasePasswordChangeVerificationMessage" class="p-4 rounded-xl border-l-4 transition-all duration-300" :class="databasePasswordChangeVerificationSuccess ? 'bg-green-500/10 border-green-400 text-green-300' : 'bg-red-500/10 border-red-400 text-red-300'">
            <div class="flex items-center gap-3">
              <svg v-if="databasePasswordChangeVerificationSuccess" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <svg v-else class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span class="font-medium">{{ databasePasswordChangeVerificationMessage }}</span>
            </div>
          </div>
        </div>

        <!-- 검증이 완료된 경우 -->
        <div v-else class="space-y-4">
          <div class="p-4 bg-green-500/10 border border-green-400/20 rounded-lg">
            <div class="flex items-center gap-2 mb-2">
              <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span class="text-green-300 font-medium text-sm">검증 완료</span>
            </div>
            <p class="text-green-200 text-sm">이제 새로운 패스워드를 입력해주세요.</p>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-white/80 text-sm font-medium mb-2">새 패스워드</label>
              <input 
                v-model="newDatabasePassword"
                type="password" 
                class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 placeholder-white/50 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-400/20 transition-all duration-200" 
                placeholder="새 패스워드 입력 (최소 8자)" 
                :disabled="isDeletingDatabase"
                @keyup.enter="changeDatabasePassword"
              />
            </div>
            
            <div class="flex gap-3">
              <button 
                @click="closeDatabasePasswordChangeModal"
                class="flex-1 px-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white hover:bg-white/20 transition-all duration-200"
              >
                취소
              </button>
              <button 
                @click="changeDatabasePassword"
                :disabled="isDeletingDatabase || !newDatabasePassword || newDatabasePassword.length < 8"
                :class="[
                  'flex-1 px-4 py-3 rounded-xl font-semibold transition-all duration-300',
                  isDeletingDatabase || !newDatabasePassword || newDatabasePassword.length < 8
                    ? 'bg-gray-600/50 text-white/50 cursor-not-allowed'
                    : 'bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700'
                ]"
              >
                <div class="flex items-center justify-center gap-2">
                  <svg v-if="isDeletingDatabase" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                  </svg>
                  <span v-if="isDeletingDatabase">변경 중...</span>
                  <span v-else>변경</span>
                </div>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 