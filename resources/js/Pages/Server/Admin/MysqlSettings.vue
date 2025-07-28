<script setup>
import { usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const page = usePage();
const server = page.props.server || {};
const allServers = page.props.allServers || [];
const sidebarMenus = page.props.sidebarMenus || [];
const user = page.props.auth?.user || {};

const sampleUsers = [
  { id: 1, name: 'dbuser1', role: '읽기/쓰기' },
  { id: 2, name: 'dbuser2', role: '읽기전용' },
];

// MySQL 패스워드 관련 상태
const newMysqlPassword = ref('');
const dashboardPassword = ref('');
const isChangingMysqlPassword = ref(false);
const isVerifyingUser = ref(false);
const mysqlPasswordMessage = ref('');
const mysqlPasswordSuccess = ref(false);
const showInitialPasswordInfo = ref(false);
const initialPasswordInfo = ref('');
const isUserVerified = ref(false);
const verificationMessage = ref('');
const verificationSuccess = ref(false);
const userEmail = ref('');
const serverDomain = ref('');

// 사용자 검증 함수
const verifyUser = async () => {
  if (!dashboardPassword.value) {
    verificationMessage.value = '대시보드 로그인 패스워드를 입력해주세요.';
    verificationSuccess.value = false;
    return;
  }

  isVerifyingUser.value = true;
  verificationMessage.value = '';
  verificationSuccess.value = false;

  try {
    const response = await axios.post(route('server.verify-user-mysql-password', server.id), {
      dashboard_password: dashboardPassword.value
    });

    if (response.data.success) {
      verificationMessage.value = response.data.message;
      verificationSuccess.value = true;
      isUserVerified.value = true;
      userEmail.value = response.data.user_email;
      serverDomain.value = response.data.server_domain;
      initialPasswordInfo.value = response.data.initial_password_info;
      showInitialPasswordInfo.value = true;
    } else {
      verificationMessage.value = response.data.message;
      verificationSuccess.value = false;
      isUserVerified.value = false;
    }
  } catch (error) {
    verificationMessage.value = error.response?.data?.message || '사용자 검증 중 오류가 발생했습니다.';
    verificationSuccess.value = false;
    isUserVerified.value = false;
  } finally {
    isVerifyingUser.value = false;
  }
};

// 초기 MySQL 패스워드 정보 조회
const getInitialMysqlPassword = async () => {
  try {
    const response = await axios.get(route('server.initial-mysql-password', server.id));
    if (response.data.success) {
      initialPasswordInfo.value = response.data.message;
      showInitialPasswordInfo.value = true;
    }
  } catch (error) {
    console.error('초기 패스워드 정보 조회 실패:', error);
  }
};

// MySQL 패스워드 변경 함수
const changeMysqlPassword = async () => {
  if (!isUserVerified.value) {
    mysqlPasswordMessage.value = '먼저 사용자 검증을 완료해주세요.';
    mysqlPasswordSuccess.value = false;
    return;
  }

  if (!newMysqlPassword.value || newMysqlPassword.value.length < 6) {
    mysqlPasswordMessage.value = '비밀번호는 최소 6자 이상이어야 합니다.';
    mysqlPasswordSuccess.value = false;
    return;
  }

  if (!dashboardPassword.value) {
    mysqlPasswordMessage.value = '대시보드 로그인 패스워드를 입력해주세요.';
    mysqlPasswordSuccess.value = false;
    return;
  }

  isChangingMysqlPassword.value = true;
  mysqlPasswordMessage.value = '';
  mysqlPasswordSuccess.value = false;

  try {
    const response = await axios.post(route('server.change-mysql-password', server.id), {
      new_password: newMysqlPassword.value,
      dashboard_password: dashboardPassword.value
    });

    if (response.data.success) {
      mysqlPasswordMessage.value = response.data.message;
      mysqlPasswordSuccess.value = true;
      newMysqlPassword.value = ''; // 입력 필드 초기화
      dashboardPassword.value = ''; // 검증 패스워드 초기화
      isUserVerified.value = false; // 검증 상태 초기화
    } else {
      mysqlPasswordMessage.value = response.data.message;
      mysqlPasswordSuccess.value = false;
    }
  } catch (error) {
    mysqlPasswordMessage.value = error.response?.data?.message || 'MySQL 패스워드 변경 중 오류가 발생했습니다.';
    mysqlPasswordSuccess.value = false;
  } finally {
    isChangingMysqlPassword.value = false;
  }
};

// 검증 상태 초기화
const resetVerification = () => {
  isUserVerified.value = false;
  verificationMessage.value = '';
  verificationSuccess.value = false;
  dashboardPassword.value = '';
  newMysqlPassword.value = '';
  showInitialPasswordInfo.value = false;
};
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
    <ServerHeader :server="server" :all-servers="allServers" />
    <div class="flex relative z-0">
      <ServerSidebar :sidebar-menus="sidebarMenus" :plan="server.plan" :server-id="server.id" />
      <div class="flex-1 p-8 relative z-0 transition-all duration-300">
        <div class="max-w-3xl mx-auto py-10 px-4 space-y-10">
          <h1 class="text-3xl font-bold text-white mb-4">MySQL 설정</h1>
          <p class="text-white/70 mb-8">MySQL 데이터베이스 패스워드와 사용자를 관리할 수 있습니다.</p>
          
          <!-- MySQL 패스워드 변경 -->
          <div class="bg-white/10 rounded-xl border border-white/20 p-6 mb-6">
            <h2 class="text-xl font-bold text-white mb-4">MySQL 패스워드 변경</h2>
            <div class="mb-4">
              <p class="text-white/70 text-sm mb-2">데이터베이스 접속에 사용되는 비밀번호를 변경할 수 있습니다.</p>
              <!-- 초기 패스워드 안내 -->
              <div class="mt-3 p-3 bg-blue-500/10 border border-blue-400/20 rounded-lg">
                <div class="flex items-center gap-2 mb-2">
                  <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <span class="text-blue-300 font-medium text-sm">초기 패스워드 안내</span>
                </div>
                <div class="text-blue-200 text-sm space-y-1">
                  <p><strong>이메일:</strong> {{ user.email || '사용자 이메일' }}</p>
                  <p><strong>서버 도메인:</strong> {{ server.domain }}</p>
                  <p><strong>데이터베이스 사용자명:</strong> {{ server.domain }}</p>
                  <p><strong>데이터베이스명:</strong> {{ server.domain }}</p>
                  <p><strong>초기 패스워드:</strong> 초기 MySQL 패스워드는 대시보드 로그인 시 사용한 패스워드와 동일합니다. 데이터베이스 접속 시 사용자명은 서버 도메인명을 사용하세요.</p>
                </div>
              </div>
            </div>
            <form @submit.prevent="verifyUser" class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-4">
              <div class="relative flex-1 min-w-0">
                <input 
                  v-model="dashboardPassword"
                  type="password" 
                  class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 placeholder-white/50 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-400/20 transition-all duration-200" 
                  placeholder="대시보드 로그인 패스워드 입력" 
                  :disabled="isVerifyingUser"
                />
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                  <svg class="w-5 h-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </div>
              </div>
              <button 
                type="submit"
                :disabled="isVerifyingUser || !dashboardPassword"
                :class="[
                  'px-8 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-400/50 min-w-[120px]',
                  isVerifyingUser || !dashboardPassword
                    ? 'bg-gray-600/50 text-white/50 cursor-not-allowed shadow-none'
                    : 'bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600 shadow-lg hover:shadow-xl'
                ]"
              >
                <div class="flex items-center justify-center gap-2">
                  <svg v-if="isVerifyingUser" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                  </svg>
                  <span v-if="isVerifyingUser">검증 중...</span>
                  <span v-else>검증</span>
                </div>
              </button>
            </form>
            <!-- 검증 메시지 표시 -->
            <div v-if="verificationMessage" class="mb-4 p-4 rounded-xl border-l-4 transition-all duration-300" :class="verificationSuccess ? 'bg-green-500/10 border-green-400 text-green-300' : 'bg-red-500/10 border-red-400 text-red-300'">
              <div class="flex items-center gap-3">
                <svg v-if="verificationSuccess" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg v-else class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ verificationMessage }}</span>
              </div>
            </div>

            <!-- 검증 성공 시 사용자 정보 표시 -->
            <div v-if="isUserVerified" class="mb-4 p-4 bg-green-500/10 border border-green-400/20 rounded-lg">
              <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-300 font-medium text-sm">사용자 검증 완료</span>
              </div>
              <p class="text-green-200 text-sm mb-3">하단에 새로운 데이터베이스 패스워드를 입력하세요.</p>
              <button 
                @click="resetVerification"
                class="text-green-300 hover:text-green-200 text-xs underline"
              >
                검증 상태 초기화
              </button>
            </div>

            <!-- 패스워드 변경 폼 (검증 완료 후에만 표시) -->
            <form v-if="isUserVerified" @submit.prevent="changeMysqlPassword" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
              <div class="relative flex-1 min-w-0">
                <input 
                  v-model="newMysqlPassword"
                  type="password" 
                  class="w-full bg-white/10 border border-white/20 rounded-xl text-white px-4 py-3 placeholder-white/50 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-400/20 transition-all duration-200" 
                  placeholder="새 비밀번호 입력 (최소 6자)" 
                  :disabled="isChangingMysqlPassword"
                />
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                  <svg class="w-5 h-5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </div>
              </div>
              <button 
                type="submit"
                :disabled="isChangingMysqlPassword || !newMysqlPassword"
                :class="[
                  'px-8 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-purple-400/50 min-w-[120px]',
                  isChangingMysqlPassword || !newMysqlPassword
                    ? 'bg-gray-600/50 text-white/50 cursor-not-allowed shadow-none'
                    : 'bg-gradient-to-r from-purple-500 to-blue-500 text-white hover:from-purple-600 hover:to-blue-600 shadow-lg hover:shadow-xl'
                ]"
              >
                <div class="flex items-center justify-center gap-2">
                  <svg v-if="isChangingMysqlPassword" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                  </svg>
                  <span v-if="isChangingMysqlPassword">변경 중...</span>
                  <span v-else>변경</span>
                </div>
              </button>
            </form>

            <!-- 검증이 완료되지 않은 경우 안내 메시지 -->
            <div v-if="!isUserVerified" class="mt-4 p-4 bg-yellow-500/10 border border-yellow-400/20 rounded-lg">
              <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span class="text-yellow-300 font-medium text-sm">패스워드 변경 전 검증 필요</span>
              </div>
              <p class="text-yellow-200 text-sm">MySQL 패스워드를 변경하려면 먼저 위에서 사용자 검증을 완료해주세요.</p>
            </div>
            <!-- 메시지 표시 -->
            <div v-if="mysqlPasswordMessage" class="mt-4 p-4 rounded-xl border-l-4 transition-all duration-300" :class="mysqlPasswordSuccess ? 'bg-green-500/10 border-green-400 text-green-300' : 'bg-red-500/10 border-red-400 text-red-300'">
              <div class="flex items-center gap-3">
                <svg v-if="mysqlPasswordSuccess" class="w-5 h-5 text-green-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg v-else class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">{{ mysqlPasswordMessage }}</span>
              </div>
            </div>
          </div>

          <!-- MySQL 유저 관리 -->
          <div class="bg-white/10 rounded-xl border border-white/20 p-6">
            <h2 class="text-xl font-bold text-white mb-4">MySQL 유저 관리</h2>
            <div class="mb-4">
              <p class="text-white/70 text-sm mb-2">데이터베이스 접속 권한을 가진 사용자를 관리할 수 있습니다.</p>
            </div>
            <div class="flex justify-end mb-6">
              <button class="bg-gradient-to-r from-purple-500 to-blue-500 text-white px-6 py-3 rounded-xl font-semibold hover:from-purple-600 hover:to-blue-600 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                유저 추가
              </button>
            </div>
            <div class="overflow-hidden rounded-xl border border-white/10">
              <table class="min-w-full text-white">
                <thead>
                  <tr class="bg-white/10">
                    <th class="px-6 py-4 text-left font-semibold text-white/90">유저명</th>
                    <th class="px-6 py-4 text-left font-semibold text-white/90">권한</th>
                    <th class="px-6 py-4 text-left font-semibold text-white/90">관리</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in sampleUsers" :key="user.id" class="border-b border-white/5 hover:bg-white/5 transition-colors duration-200">
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                          <span class="text-white text-sm font-bold">{{ user.name.charAt(0).toUpperCase() }}</span>
                        </div>
                        <span class="font-medium">{{ user.name }}</span>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" :class="user.role === '읽기/쓰기' ? 'bg-green-500/20 text-green-300' : 'bg-blue-500/20 text-blue-300'">
                        {{ user.role }}
                      </span>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-2">
                        <button class="text-blue-400 hover:text-blue-300 transition-colors duration-200 p-2 rounded-lg hover:bg-blue-500/10">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          </svg>
                        </button>
                        <button class="text-red-400 hover:text-red-300 transition-colors duration-200 p-2 rounded-lg hover:bg-red-500/10">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="sampleUsers.length === 0" class="text-center text-white/60 py-12">
              <svg class="w-12 h-12 mx-auto mb-4 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
              </svg>
              <p class="text-lg font-medium">등록된 유저가 없습니다</p>
              <p class="text-sm text-white/50 mt-1">새로운 데이터베이스 유저를 추가해보세요</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 