

<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
        <!-- 헤더 컴포넌트 -->
        <ServerHeader :server="props.server" :all-servers="props.allServers" @dropdown-toggle="handleDropdownToggle" />
        
        <!-- 드롭다운 오버레이: 드롭다운이 열렸을 때만 헤더 바로 아래에 위치 -->
        <div v-if="isDropdownOpen" class="fixed left-0 right-0 top-[64px] h-[300px] z-[999]" style="pointer-events:auto;"></div>
        
        <div class="flex relative z-0">
            <!-- 사이드바 컴포넌트 -->
            <ServerSidebar :sidebar-menus="props.sidebarMenus" :plan="props.server.plan" :server-id="props.server.id" />

            <!-- 메인 콘텐츠 -->
            <div class="flex-1 p-8 relative z-0 transition-all duration-300">
                <div class="max-w-5xl mx-auto space-y-8">
                    <!-- 상단 요약 카드 -->
                    <div class="bg-white/10 rounded-2xl shadow-xl p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-8">
                        <div class="flex-1 min-w-0">
                            <div class="text-3xl font-extrabold text-white truncate">{{ props.server.site_name }}</div>
                            <div class="text-lg text-white/80 truncate">{{ props.server.domain }}.hostylefree.xyz</div>
                            <div class="text-base text-white/60 mt-2">플랜: <span class="font-bold text-white/90">{{ props.server.plan }}</span> | 만료일: <span class="font-bold text-white/90">{{ expiresAt }}</span></div>
                        </div>
                        <div class="flex gap-3 flex-wrap justify-end items-center relative">
                            <button class="px-6 py-3 rounded-full bg-pink-500 text-white text-base font-bold shadow hover:bg-pink-600 transition">사이트 설정</button>
                            <button class="px-6 py-3 rounded-full bg-blue-500 text-white text-base font-bold shadow hover:bg-blue-600 transition">업그레이드</button>
                            <button class="px-6 py-3 rounded-full bg-green-500 text-white text-base font-bold shadow hover:bg-green-600 transition">통계 보기</button>
                            <!-- 더보기 드롭다운 -->
                            <div class="relative">
                                <button @click="showMore = !showMore" class="px-6 py-3 rounded-full bg-white/20 text-white text-base font-bold shadow hover:bg-white/30 transition">더 보기</button>
                                <div v-if="showMore" class="absolute right-0 mt-2 w-40 bg-white rounded-xl shadow-lg z-50">
                                    <button @click="handleRefund" class="block w-full text-left px-4 py-3 text-red-500 hover:bg-gray-100 rounded-t-xl">환불/삭제</button>
                                    <button @click="showMore = false" class="block w-full text-left px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-b-xl">닫기</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 주요 정보/통계 카드 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- 트래픽 -->
                        <div class="bg-white/10 rounded-2xl shadow-xl p-8 flex flex-col gap-3">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-lg font-bold text-blue-300">트래픽</span>
                                <span class="text-base text-white/50">(월)</span>
                            </div>
                            <div class="text-3xl font-extrabold text-white">{{ trafficUsedGB }}GB / {{ trafficTotalGB }}GB</div>
                            <div class="w-full bg-white/10 rounded h-3 mt-2">
                                <div class="bg-blue-500 h-3 rounded" :style="{ width: trafficPercent + '%' }"></div>
                            </div>
                            <div class="text-base text-white/50 mt-1">{{ trafficPercent }}% 사용</div>
                        </div>
                        <!-- 디스크 -->
                        <div class="bg-white/10 rounded-2xl shadow-xl p-8 flex flex-col gap-3">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-lg font-bold text-green-300">디스크</span>
                                <span class="text-base text-white/50">(전체)</span>
                            </div>
                            <div class="text-3xl font-extrabold text-white">{{ diskUsedGB }}GB / {{ diskTotalGB }}GB</div>
                            <div class="w-full bg-white/10 rounded h-3 mt-2">
                                <div class="bg-green-500 h-3 rounded" :style="{ width: diskPercent + '%' }"></div>
                            </div>
                            <div class="text-base text-white/50 mt-1">{{ diskPercent }}% 사용</div>
                        </div>
                        <!-- DB -->
                        <div class="bg-white/10 rounded-2xl shadow-xl p-8 flex flex-col gap-3">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-lg font-bold text-yellow-300">DB</span>
                            </div>
                            <div class="text-3xl font-extrabold text-white">{{ stats?.dbUsed || 0 }} / {{ stats?.dbAllowed || 0 }}</div>
                            <div class="w-full bg-white/10 rounded h-3 mt-2">
                                <div class="bg-yellow-400 h-3 rounded" :style="{ width: dbPercent + '%' }"></div>
                            </div>
                            <div class="text-base text-white/50 mt-1">{{ dbPercent }}% 사용</div>
                        </div>
                        <!-- 방문자 통계(더미) -->
                        <div class="bg-white/10 rounded-2xl shadow-xl p-8 flex flex-col gap-3 min-h-[180px] justify-center items-center">
                            <div class="text-lg font-bold text-white/80 mb-2">방문자 통계</div>
                            <div class="w-full h-24 flex items-end gap-1">
                                <div v-for="i in 7" :key="i" class="flex-1 bg-blue-400/30 rounded-t" :style="{ height: (10 + Math.random() * 60) + 'px' }"></div>
                            </div>
                            <div class="flex justify-between w-full text-sm text-white/40 mt-2">
                                <span>7/19</span><span>7/20</span><span>7/21</span><span>7/22</span><span>7/23</span><span>7/24</span><span>7/25</span>
                            </div>
                            <div class="text-sm text-white/40 mt-2">(그래프는 더미입니다)</div>
                        </div>
                    </div>

                    <!-- 기타 정보 카드 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="bg-white/10 rounded-2xl shadow-xl p-8 flex flex-col gap-3">
                            <div class="flex justify-between text-white/70 text-base"><span>플랫폼</span><span class="text-white font-bold">{{ props.server.platform }}</span></div>
                            <div class="flex justify-between text-white/70 text-base"><span>지역</span><span class="text-white font-bold">{{ props.server.region }}</span></div>
                            <div class="flex justify-between text-white/70 text-base"><span>생성일</span><span class="text-white font-bold">{{ createdAt }}</span></div>
                            <div class="flex justify-between text-white/70 text-base"><span>상태</span><span class="text-white font-bold">{{ props.server.status }}</span></div>
                        </div>
                        <div class="bg-white/10 rounded-2xl shadow-xl p-8 flex flex-col gap-3">
                            <div class="flex justify-between text-white/70 text-base"><span>만료까지 남은 일수</span><span class="text-white font-bold">{{ props.server.days_remaining }}일</span></div>
                            <div class="flex justify-between text-white/70 text-base"><span>개월 수</span><span class="text-white font-bold">{{ props.server.months }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import ServerHeader from '@/Components/ServerHeader.vue';
import ServerSidebar from '@/Components/ServerSidebar.vue';

const props = defineProps({
  server: { type: Object, required: true },
  allServers: { type: Array, default: () => [] },
  sidebarMenus: { type: Array, default: () => [] },
  websiteInfo: { type: Object, default: null }
});

const isDropdownOpen = ref(false);
const showMore = ref(false);
const stats = ref(null);
const statsLoading = ref(false);
const statsError = ref(null);

const fetchStats = async () => {
  statsLoading.value = true;
  statsError.value = null;
  try {
    const res = await fetch(`/server/${props.server.id}/fetch-website-data`);
    if (!res.ok) throw new Error('API 호출 실패');
    const data = await res.json();
    if (data.error) throw new Error(data.error);
    stats.value = data;
  } catch (e) {
    statsError.value = e.message;
  } finally {
    statsLoading.value = false;
  }
};

onMounted(() => {
  fetchStats();
});

const handleDropdownToggle = (isOpen) => {
  isDropdownOpen.value = isOpen;
};

const handleUpgrade = () => {
  alert('업그레이드 기능은 곧 제공됩니다!');
};
const handleRefund = () => {
  showMore.value = false;
  if (confirm('정말 환불 및 서버 삭제를 진행하시겠습니까?')) {
    // TODO: 환불/삭제 API 연동
    alert('환불/삭제 기능은 곧 제공됩니다!');
  }
};

// 날짜 포맷 (YYYY-MM-DD)
const formatDate = (dateStr) => {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  if (isNaN(d)) return dateStr.split('T')[0].slice(0, 10); // 혹시라도 소수점 등 이상값 방지
  return d.toISOString().slice(0, 10);
};
const expiresAt = computed(() => formatDate(props.server.expires_at));
const createdAt = computed(() => formatDate(props.server.created_at));

// GB 변환 및 퍼센트 계산
const diskUsedGB = computed(() => ((stats.value?.diskUsage || 0) / 1024).toFixed(2));
const diskTotalGB = computed(() => ((stats.value?.diskInMBTotal || 0) / 1024).toFixed(2));
const diskPercent = computed(() => {
  const used = stats.value?.diskUsage || 0;
  const total = stats.value?.diskInMBTotal || 1;
  return Math.min(100, ((used / total) * 100).toFixed(1));
});
const trafficUsedGB = computed(() => ((stats.value?.bwInMB || 0) / 1024).toFixed(2));
const trafficTotalGB = computed(() => ((stats.value?.bwInMBTotal || 0) / 1024).toFixed(2));
const trafficPercent = computed(() => {
  const used = stats.value?.bwInMB || 0;
  const total = stats.value?.bwInMBTotal || 1;
  return Math.min(100, ((used / total) * 100).toFixed(1));
});
const dbPercent = computed(() => {
  const used = stats.value?.dbUsed || 0;
  const total = stats.value?.dbAllowed || 1;
  return Math.min(100, ((used / total) * 100).toFixed(1));
});
</script>

<style scoped>
</style> 