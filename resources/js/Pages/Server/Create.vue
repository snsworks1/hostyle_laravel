<script setup>
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';
import Modal from '@/Components/Modal.vue';
import { CheckCircle, Calendar, CalendarRange, Percent, Info } from 'lucide-vue-next';

// props로 DB에서 가져온 플랜 데이터 받기
const props = defineProps({
    plans: {
        type: Array,
        default: () => []
    },
    regions: {
        type: Array,
        default: () => []
    },
    features: {
        type: Array,
        default: () => []
    },
    featurePlan: {
        type: Array,
        default: () => []
    }
});

// const plans = props.plans; // 중복 선언 제거

// regions 배열 동적 생성 (국기 등은 기본값)
const regionFlagMap = {
    'seoul': '🇰🇷',
    'korea': '🇰🇷',
    'tokyo': '🇯🇵',
    'japan': '🇯🇵',
    'singapore': '🇸🇬',
    'frankfurt': '🇩🇪',
    'germany': '🇩🇪',
    'us': '🇺🇸',
    'usa': '🇺🇸',
    'newyork': '🇺🇸',
    'london': '🇬🇧',
    'uk': '🇬🇧',
    'sydney': '🇦🇺',
    'australia': '🇦🇺',
    'canada': '🇨🇦',
    'paris': '🇫🇷',
    'france': '🇫🇷',
    'hongkong': '🇭🇰',
    'hk': '🇭🇰',
    'mumbai': '🇮🇳',
    'india': '🇮🇳',
};
const regions = props.regions.map(r => ({
    value: r,
    label: r,
    flag: regionFlagMap[r.toLowerCase()] || '🌏',
    country: ''
}));

// 플랫폼 순서: hostyle 웹 제작 플랫폼 → wordpress → custom
const platforms = [
    { value: 'cms', label: 'Hostyle 웹 제작 플랫폼', icon: '/images/payment_icon/cms.png', description: '드래그앤드롭 간편한 웹제작 플랫폼' },
    { value: 'wordpress', label: 'WordPress', icon: '/images/payment_icon/wordpress.png', description: '블로그, 포트폴리오, 쇼핑몰 등' },
    { value: 'custom', label: '자체구축', icon: '/images/payment_icon/자체구축.png', description: 'PHP / HTML / CSS / JavaScript' }
];

// form 초기값 동적 할당
const form = useForm({
    site_name: '',
    domain: '',
    region: regions[0]?.value || '',
    platform: platforms[0]?.value || '',
    plan: props.plans.length > 0 ? props.plans[0].name : '',
    months: 1,
    user_password: ''
});

const months = [
    { value: 1, label: '1개월', discount: 0 },
    { value: 3, label: '3개월', discount: 5 },
    { value: 6, label: '6개월', discount: 10 },
    { value: 12, label: '1년', discount: 20 }
];

// DB에서 가져온 플랜 데이터 사용 (props.plans가 비어있으면 기본값 사용)
const plans = props.plans.length > 0 ? props.plans : [
    { 
        value: 'free', 
        label: '무료', 
        price: '0',
        originalPrice: '0',
        trialDays: 15,
        specs: {
            storage: '1GB',
            traffic: '10GB/월',
            domains: '서브도메인',
            features: ['SEO', '고급 템플릿', '드래그앤드랍 빌더', '실시간 모니터링']
        }
    },
    { 
        value: 'starter', 
        label: 'Starter', 
        price: '9,900',
        originalPrice: '9,900',
        annualPrice: '8,900',
        specs: {
            storage: '5GB',
            traffic: '150GB/월',
            domains: '1개',
            features: ['SEO', 'SSL', 'SSL 인증서', '개별도메인', '게시판', '고급 템플릿', '드래그앤드랍 빌더', '실시간 모니터링', 'Hostyle 광고제거']
        }
    },
    { 
        value: 'business', 
        label: 'Business', 
        price: '19,900',
        originalPrice: '19,900',
        annualPrice: '17,900',
        specs: {
            storage: '20GB',
            traffic: '600GB/월',
            domains: '5개',
            features: ['SEO', 'SSL', 'SSL 인증서', '개별도메인', '게시판', '고급 분석', '고급 템플릿', '드래그앤드랍 빌더', '백업', '실시간 모니터링', '방문자 모니터링', '이메일 지원', 'Hostyle 광고제거', '커스텀 스크립트', '회원관리']
        }
    },
    { 
        value: 'enterprise', 
        label: 'Enterprise', 
        price: '39,900',
        originalPrice: '39,900',
        annualPrice: '35,900',
        specs: {
            storage: '100GB',
            traffic: '무제한',
            domains: '무제한',
            features: ['SEO', 'SSL', 'SSL 인증서', '개별도메인', '게시판', '고급 분석', '고급 템플릿', '드래그앤드랍 빌더', '백업', '실시간 모니터링', '방문자 모니터링', '이메일 지원', '전용 IP', 'Hostyle 광고제거', '커스텀 스크립트', '회원관리']
        }
    }
];

const selectedPlan = ref(null);

function updateSelectedPlan() {
    selectedPlan.value = plans.find(
        p => p.name === form.plan || p.id === form.plan
    );
}

// 최초 로딩 시에도 selectedPlan 세팅
updateSelectedPlan();

// form.plan이 바뀔 때마다 selectedPlan도 자동 갱신
watch(() => form.plan, updateSelectedPlan);

// 할인된 가격 계산
const getDiscountedPrice = (plan, months) => {
  const priceNum = typeof plan.price === 'number' ? plan.price : parseInt(plan.price, 10);
  const selectedMonth = months.find(m => m.value === form.months);
  const discount = selectedMonth ? selectedMonth.discount : 0;
  return Math.round(priceNum * form.months * (1 - discount / 100));
};

const passwordValid = ref(null);
const passwordCheckLoading = ref(false);
let passwordCheckTimeout = null;

watch(() => form.user_password, (val) => {
    if (passwordCheckTimeout) clearTimeout(passwordCheckTimeout);
    if (!val) {
        passwordValid.value = null;
        return;
    }
    passwordCheckLoading.value = true;
    passwordCheckTimeout = setTimeout(async () => {
        try {
            const res = await axios.post('/user/password-check', { password: val });
            passwordValid.value = res.data.valid;
        } catch (e) {
            passwordValid.value = null;
        } finally {
            passwordCheckLoading.value = false;
        }
    }, 400); // 0.4초 debounce
});

const submit = () => {
    form.post(route('server.payment.dummy'));
};

const features = props.features || [];
const featurePlan = props.featurePlan || [];
const showFeatureModal = ref(false);
function isFeatureSupported(plan, feature) {
    return featurePlan.some(fp => fp.plan_id === plan.id && fp.feature_id === feature.id);
}

const showRefundModal = ref(false);
const refundAgree = ref(false);
const refundAgreeError = ref('');
const passwordError = ref('');

function handleRefundAgreeChange() {
    refundAgreeError.value = '';
}

function handleSubmit() {
    let error = false;
    if (!refundAgree.value) {
        refundAgreeError.value = '환불 약관에 동의해야 서버 생성/결제가 가능합니다.';
        error = true;
    } else {
        refundAgreeError.value = '';
    }
    if (passwordValid.value !== true) {
        passwordError.value = '대시보드 로그인 비밀번호가 일치해야 서버 생성/결제가 가능합니다.';
        error = true;
    } else {
        passwordError.value = '';
    }
    if (error) return;
    submit();
}
</script>

<template>
    <Head title="서버 생성" />

    <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
        <!-- 헤더 -->
        <div class="bg-white/10 backdrop-blur-xl border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-2xl font-bold text-white">HOSTYLE</h1>
                        <span class="text-white/70">서버 생성</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <Link 
                            :href="route('server.select')" 
                            class="text-white/70 hover:text-white transition-colors"
                        >
                            서버 목록으로
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- 메인 컨텐츠 -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-white mb-4">새 서버를 생성하세요</h2>
                <p class="text-xl text-white/70">몇 가지 정보만 입력하면 바로 시작할 수 있습니다</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- 메인 폼 -->
                <div class="flex-1">
                    <form @submit.prevent="submit" class="space-y-8">
                        <!-- 1단계: 사이트 정보 -->
                        <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-8">
                            <h3 class="text-2xl font-bold text-white mb-6">1. 사이트 정보</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- 사이트 이름 -->
                                <div>
                                    <label class="block text-white font-medium mb-2">사이트 이름 *</label>
                                    <input 
                                        v-model="form.site_name"
                                        type="text"
                                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:border-purple-400 focus:outline-none transition-colors"
                                        placeholder="예: 내 블로그"
                                        required
                                    >
                                    <div v-if="form.errors.site_name" class="mt-2 text-red-300 text-sm">{{ form.errors.site_name }}</div>
                                </div>

                                <!-- 도메인 -->
                                <div>
                                    <label class="block text-white font-medium mb-2">도메인 *</label>
                                    <div class="relative">
                                        <input 
                                            v-model="form.domain"
                                            type="text"
                                            class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:border-purple-400 focus:outline-none transition-colors pr-20"
                                            placeholder="예: myblog"
                                            required
                                        >
                                        <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/50">.hostylefree.xyz</span>
                                    </div>
                                    <div v-if="form.errors.domain" class="mt-2 text-red-300 text-sm">{{ form.errors.domain }}</div>
                                </div>
                                <!-- 대시보드 로그인 비밀번호 -->
                                <div class="md:col-span-2">
                                    <label class="block text-white font-medium mb-2">대시보드 로그인 비밀번호 *</label>
                                    <input 
                                        v-model="form.user_password"
                                        type="password"
                                        class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:border-purple-400 focus:outline-none transition-colors"
                                        placeholder="로그인 시 사용한 비밀번호를 입력하세요"
                                        required
                                    >
                                    <div class="mt-2 text-white/60 text-sm">이 비밀번호는 <b>웹파일관리자 / DB 계정에도 사용됩니다.</b></div>
                                    <div v-if="form.errors.user_password" class="mt-2 text-red-300 text-sm">{{ form.errors.user_password }}</div>
                                    <div v-if="passwordCheckLoading" class="mt-2 text-blue-300 text-sm">비밀번호 확인 중...</div>
                                    <div v-else-if="passwordValid === true" class="mt-2 text-green-400 text-sm">비밀번호가 일치합니다.</div>
                                    <div v-else-if="passwordValid === false" class="mt-2 text-red-400 text-sm">비밀번호가 일치하지 않습니다.</div>
                                </div>
                            </div>
                        </div>

                        <!-- 2단계: 리전 선택 -->
                        <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-8">
                            <h3 class="text-2xl font-bold text-white mb-6">2. 서버 리전 선택</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div 
                                    v-for="region in regions" 
                                    :key="region.value"
                                    :class="[
                                        'p-4 border-2 rounded-lg cursor-pointer transition-all duration-300 flex items-center gap-3',
                                        form.region === region.value 
                                            ? 'border-purple-400 bg-purple-500/20' 
                                            : 'border-white/20 bg-white/5 hover:bg-white/10'
                                    ]"
                                    @click="form.region = region.value"
                                >
                                    <span class="text-2xl">{{ region.flag }}</span>
                                    <div>
                                        <div class="text-white font-medium">{{ region.label }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3단계: 플랫폼 선택 -->
                        <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-8">
                            <h3 class="text-2xl font-bold text-white mb-6">3. 플랫폼 선택</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div 
                                    v-for="platform in platforms" 
                                    :key="platform.value"
                                    :class="[
                                        'p-6 border-2 rounded-lg cursor-pointer transition-all duration-300',
                                        form.platform === platform.value 
                                            ? 'border-purple-400 bg-purple-500/20' 
                                            : 'border-white/20 bg-white/5 hover:bg-white/10'
                                    ]"
                                    @click="form.platform = platform.value"
                                >
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto mb-4 bg-white/10 rounded-lg flex items-center justify-center overflow-hidden">
                                            <img :src="platform.icon" :alt="platform.label" class="w-10 h-10 object-contain" />
                                        </div>
                                        <div class="text-white font-bold mb-2">{{ platform.label }}</div>
                                        <div class="text-white/60 text-sm">{{ platform.description }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4단계: 요금제 선택 -->
                        <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-8">
                            <div class="flex items-start justify-between">
                                <h3 class="text-2xl font-bold text-white mb-6">4. 요금제 선택</h3>
                                <button type="button" class="ml-auto px-4 py-2 bg-purple-600 text-white rounded shadow text-sm font-semibold hover:bg-purple-700 transition" @click="showFeatureModal = true">
                                    요금제별 기능
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div 
                                    v-for="plan in plans" 
                                    :key="plan.id || plan.value"
                                    :class="[
                                        'flex flex-col items-center border-2 rounded-xl cursor-pointer transition-all duration-200 relative bg-white/5 min-h-[210px] py-4 px-2 md:px-3',
                                        form.plan === (plan.value || plan.name)
                                            ? 'border-purple-400 bg-purple-500/10'
                                            : 'border-white/20 hover:bg-white/10',
                                        'text-white',
                                        hoveredPlan === (plan.value || plan.name) ? 'scale-[1.03] shadow-lg z-10 border-purple-400' : ''
                                    ]"
                                    @click="form.plan = plan.value || plan.name; updateSelectedPlan()"
                                    @mouseover="hoveredPlan = plan.value || plan.name"
                                    @mouseleave="hoveredPlan = null"
                                    style="transition: box-shadow 0.2s, transform 0.2s;"
                                >
                                    <!-- 추천 뱃지: 카드 상단 테두리와 겹치게 -->
                                    <div v-if="plan.value === 'business' || plan.name === 'business'" class="absolute left-1/2 -translate-x-1/2 -top-8 translate-y-1/2 bg-gradient-to-r from-pink-400 to-purple-500 text-white text-[12px] font-bold px-3 py-0.5 rounded-full shadow z-20 flex items-center gap-1 border-2 border-white/30">
                                        <span>🔥</span> 추천
                                    </div>
                                    <!-- 무료 플랜 강조: 15일 무료 뱃지 카드 상단 테두리와 겹치게 -->
                                    <div v-if="plan.value === 'free' || plan.name === 'free'" class="absolute left-1/2 -translate-x-1/2 -top-8 translate-y-1/2 bg-green-600 text-white text-[12px] font-bold px-3 py-0.5 rounded-full shadow z-20 border-2 border-white/30">15일 무료</div>
                                    <div class="text-center w-full flex flex-col flex-1 justify-between">
                                        <div class="font-bold text-base mb-0.5 text-xl">
                                            <template v-if="plan.value === 'free' || plan.name === 'free'">
                                                <span class="text-green-500">무료</span>
                                            </template>
                                            <template v-else>
                                                {{ plan.label || plan.name }}
                                            </template>
                                        </div>
                                        <div class="text-[11px] mb-1 text-white">
                                            <template v-if="plan.value === 'free' || plan.name === 'free'">개인 실험/테스트, 소규모 임시 사이트에 적합</template>
                                            <template v-else-if="plan.value === 'starter' || plan.name === 'starter'">개인 블로그, 포트폴리오, 소규모 홈페이지 추천</template>
                                            <template v-else-if="plan.value === 'business' || plan.name === 'business'">중소기업, 단체, 트래픽 많은 사이트에 추천</template>
                                            <template v-else-if="plan.value === 'enterprise' || plan.name === 'enterprise'">대규모 서비스, 기업/기관, 고성능이 필요한 경우</template>
                                        </div>
                                        <!-- 금액/월 정렬 -->
                                        <div class="flex items-end justify-center mb-1 h-8">
                                            <template v-if="plan.value === 'free' || plan.name === 'free'">
                                                <span class="text-lg font-extrabold text-white">0원</span>
                                            </template>
                                            <template v-else>
                                                <span class="text-2xl font-extrabold">₩{{ Number(plan.price).toLocaleString() }}</span>
                                                <span class="ml-0.5 text-s font-semibold text-white/70 mb-0.5">/월</span>
                                            </template>
                                        </div>
                                        <div class="w-full text-[13px] mt-1 space-y-0.5 text-left">
                                            <div>디스크: <b>{{ plan.disk || plan.specs?.storage }}</b></div>
                                            <div>트래픽: <b>{{ plan.traffic || plan.specs?.traffic }}</b></div>
                                            <div>도메인: <b>{{ plan.domains || plan.specs?.domains }}</b>개</div>
                                            <div>DB: <b>{{ plan.databases || plan.specs?.databases }}</b>개</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 기존 통합 기능 표 제거 -->
                        <!-- 요금제별 기능 모달 -->
                        <Modal :show="showFeatureModal" @close="showFeatureModal = false">
                            <div class="p-1 bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 rounded-lg shadow-xl w-full max-w-full mx-auto border border-white/20 z-[100] relative">
                                <h3 class="text-base font-bold mb-2 text-white text-center">요금제별 지원 기능</h3>
                                <div class="overflow-x-auto w-full">
                                    <table class="w-full table-auto border-collapse bg-white/10 rounded-lg text-[11px]">
                                        <thead>
                                            <tr>
                                                <th class="p-1 text-purple-200 text-[11px] text-left font-semibold whitespace-nowrap">기능</th>
                                                <th v-for="plan in plans" :key="plan.id || plan.value" class="p-1 text-purple-300 font-bold text-center text-[11px] whitespace-nowrap">
                                                    {{ plan.label || plan.name }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="feature in features" :key="feature.id" class="border-t border-white/20">
                                                <td class="p-1 text-white/80 text-[11px] whitespace-nowrap">{{ feature.name }}</td>
                                                <td v-for="plan in plans" :key="plan.id || plan.value" class="p-1 text-center">
                                                    <span v-if="isFeatureSupported(plan, feature)" class="text-green-400 text-base">✔️</span>
                                                    <span v-else class="text-red-400 text-base">❌</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="mt-2 px-4 py-1 bg-gradient-to-r from-purple-700 to-blue-700 hover:from-purple-800 hover:to-blue-800 text-white rounded-lg font-semibold block mx-auto shadow transition-all duration-300 text-xs z-[110] relative focus:outline-none" @click.stop="showFeatureModal = false" @mousedown.stop tabindex="0" autofocus>닫기</button>
                            </div>
                        </Modal>

                        <!-- 5단계: 결제 기간 (무료 플랜이면 숨김) -->
                        <div v-if="!(selectedPlan && (selectedPlan.value === 'free' || selectedPlan.name === 'free'))" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-8">
                            <h3 class="text-2xl font-bold text-white mb-6">5. 결제 기간</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div 
                                    v-for="month in months" 
                                    :key="month.value"
                                    :class="[
                                        'p-4 border-2 rounded-lg cursor-pointer transition-all duration-300',
                                        form.months === month.value 
                                            ? 'border-purple-400 bg-purple-500/20' 
                                            : 'border-white/20 bg-white/5 hover:bg-white/10'
                                    ]"
                                    @click="form.months = month.value"
                                >
                                    <div class="text-center">
                                        <div class="text-white font-bold text-lg mb-1">{{ month.label }}</div>
                                        <div v-if="month.discount > 0" class="text-green-400 text-sm">{{ month.discount }}% 할인</div>
                                        <div v-else class="text-white/60 text-sm">할인 없음</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- 사이드바: 선택된 플랜 정보 -->
                <div class="lg:w-96">
                    <div class="bg-gradient-to-br from-purple-900/60 via-blue-900/60 to-indigo-900/60 backdrop-blur-2xl rounded-2xl border border-white/30 shadow-2xl p-7 sticky top-6 flex flex-col gap-4 min-h-[480px]">
                        <h3 class="text-lg font-bold text-white mb-2 tracking-tight">6. 최종 주문서</h3>
                        <!-- 사이트 이름/도메인 표시 -->
                        <div v-if="form.site_name || form.domain" class="mb-2 px-4 py-2 bg-white/10 rounded-xl text-center flex flex-col gap-1">
                            <div v-if="form.site_name" class="text-base font-semibold text-white truncate">{{ form.site_name }}</div>
                            <div v-if="form.domain" class="text-xs text-white/70 truncate">{{ form.domain }}.hostylefree.xyz</div>
                        </div>
                        <div v-if="selectedPlan" class="flex-1 flex flex-col gap-4">
                            <!-- 요금제명/뱃지/설명 -->
                            <div class="text-center p-4 bg-white/10 rounded-xl flex flex-col items-center gap-1 relative">
                                <div class="flex items-center justify-center gap-2 mb-1">
                                    <span class="text-2xl font-extrabold" :class="selectedPlan.value === 'free' || selectedPlan.name === 'free' ? 'text-green-400' : 'text-white'">{{ selectedPlan.label || selectedPlan.name }}</span>
                                    <span v-if="selectedPlan.value === 'business' || selectedPlan.name === 'business'" class="bg-gradient-to-r from-pink-400 to-purple-500 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow border-2 border-white/20 ml-1">🔥 추천</span>
                                    <span v-if="selectedPlan.value === 'free' || selectedPlan.name === 'free'" class="bg-green-600 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow border-2 border-white/20 ml-1">15일 무료</span>
                                </div>
                                <div class="text-xs text-white/80 mb-1">
                                    <template v-if="selectedPlan.value === 'free' || selectedPlan.name === 'free'">개인 실험/테스트, 소규모 임시 사이트에 적합</template>
                                    <template v-else-if="selectedPlan.value === 'starter' || selectedPlan.name === 'starter'">개인 블로그, 포트폴리오, 소규모 홈페이지 추천</template>
                                    <template v-else-if="selectedPlan.value === 'business' || selectedPlan.name === 'business'">중소기업, 단체, 트래픽 많은 사이트에 추천</template>
                                    <template v-else-if="selectedPlan.value === 'enterprise' || selectedPlan.name === 'enterprise'">대규모 서비스, 기업/기관, 고성능이 필요한 경우</template>
                                </div>
                            </div>
                            <!-- 주요 스펙 -->
                            <div class="grid grid-cols-2 gap-2 text-sm text-white/90">
                                <div class="flex items-center gap-2 bg-white/10 rounded-lg px-3 py-2"><span class="i-lucide-hard-drive text-lg text-blue-300"></span> <b>디스크</b>: {{ selectedPlan.disk }}</div>
                                <div class="flex items-center gap-2 bg-white/10 rounded-lg px-3 py-2"><span class="i-lucide-activity text-lg text-blue-300"></span> <b>트래픽</b>: {{ selectedPlan.traffic }}</div>
                                <div class="flex items-center gap-2 bg-white/10 rounded-lg px-3 py-2"><span class="i-lucide-globe text-lg text-blue-300"></span> <b>도메인</b>: {{ selectedPlan.domains }}개</div>
                                <div class="flex items-center gap-2 bg-white/10 rounded-lg px-3 py-2"><span class="i-lucide-database text-lg text-blue-300"></span> <b>DB</b>: {{ selectedPlan.databases }}개</div>
                            </div>
                            <!-- 결제 정보 -->
                            <div class="bg-gradient-to-r from-purple-700/40 to-blue-700/40 rounded-xl p-4 flex flex-col gap-2 text-white/90 shadow-inner">
                                <div class="flex justify-between text-sm">
                                    <span>월 요금</span>
                                    <span class="font-bold">₩{{ Number(selectedPlan.price).toLocaleString() }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span>결제 기간</span>
                                    <span>
                                        <template v-if="selectedPlan.value === 'free' || selectedPlan.name === 'free'">
                                            체험 기한 15일
                                        </template>
                                        <template v-else>
                                            {{ months.find(m => m.value === form.months)?.label }}
                                        </template>
                                    </span>
                                </div>
                                <div v-if="months.find(m => m.value === form.months)?.discount > 0 && !(selectedPlan.value === 'free' || selectedPlan.name === 'free')" class="flex justify-between text-green-400 text-sm items-end">
                                    <span>할인</span>
                                    <span>
                                        -{{ months.find(m => m.value === form.months)?.discount }}%
                                        <span class="text-xs text-green-200 ml-1">(₩{{ (Number(selectedPlan.price) * form.months * months.find(m => m.value === form.months)?.discount / 100).toLocaleString() }})</span>
                                    </span>
                                </div>
                                <div class="flex justify-between text-lg font-bold border-t border-white/20 pt-2 mt-2">
                                    <span>총 결제 금액</span>
                                    <span class="text-yellow-300">₩{{ getDiscountedPrice(selectedPlan, months).toLocaleString() }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- 환불 약관 동의 안내 -->
                        <div class="text-xs text-white/50 mb-1">환불 약관 동의는 전자상거래법 및 소비자보호법에 따라 <span class="font-semibold text-purple-200">필수</span>입니다.</div>
                        <!-- 환불 약관 동의 체크박스 -->
                        <div class="flex items-center gap-2 mb-2">
                            <input type="checkbox" id="refund-agree" v-model="refundAgree" @change="handleRefundAgreeChange" class="w-5 h-5 text-purple-600 bg-gray-100 border-gray-300 rounded focus:ring-purple-500 focus:ring-2">
                            <label for="refund-agree" class="text-white text-sm select-none cursor-pointer flex items-center gap-1">
                                <span class="text-red-400 text-base font-bold mr-1">*</span>
                                <span class="font-semibold text-purple-300">환불 약관</span>에 동의합니다
                                <button type="button"
                                    class="ml-3 px-3 py-1 rounded-full bg-blue-600/90 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-1 shadow transition-all duration-200 border border-blue-300/40 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    @click="showRefundModal = true"
                                >
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z"/></svg>
                                    약관 보기
                                </button>
                            </label>
                        </div>
                        <!-- 안내 문구: 체크 미동의/비밀번호 불일치 상태에서 항상 노출 -->
                        <div v-if="!refundAgree" class="mb-1 text-red-400 text-sm">환불 약관에 동의해야 서버 생성/결제가 가능합니다.</div>
                        <div v-if="passwordValid !== true" class="mb-2 text-red-400 text-sm">대시보드 로그인 비밀번호가 일치해야 서버 생성/결제가 가능합니다.</div>
                        <!-- 서버생성/결제 버튼 (체크박스 미동의/비밀번호 불일치 시 비활성화, 회색처리) -->
                        <button 
                            type="button"
                            @click="handleSubmit"
                            :class="[
                                'mt-2 w-full font-bold py-4 rounded-lg text-lg transition-all duration-300 transform shadow-xl',
                                form.processing || !refundAgree || passwordValid !== true
                                    ? 'bg-gray-400 text-white/70 cursor-not-allowed'
                                    : 'bg-gradient-to-r from-purple-500/80 to-blue-500/80 hover:from-purple-600 hover:to-blue-600 text-white hover:scale-105'
                            ]"
                            :disabled="form.processing || !refundAgree || passwordValid !== true"
                        >
                            <span v-if="form.processing">처리중...</span>
                            <span v-else>서버생성 / 결제</span>
                        </button>
                        <!-- 환불 정책 모달 -->
                        <Modal :show="showRefundModal" @close="showRefundModal = false">
                            <div class="p-0 bg-gradient-to-br from-[#181f3a] via-[#1a223f] to-[#232b45] rounded-2xl shadow-2xl max-w-2xl w-full mx-auto border border-white/15 overflow-hidden">
                                <div class="flex justify-between items-center px-8 pt-7 pb-3 border-b border-white/10 bg-white/5">
                                    <h3 class="text-2xl font-extrabold text-white tracking-tight">환불 정책 안내</h3>
                                    <button @click="showRefundModal = false" class="text-white/60 hover:text-white text-3xl leading-none">&times;</button>
                                </div>
                                <div class="px-8 py-6 space-y-5">
                                    <!-- 무료 체험 -->
                                    <div class="rounded-xl bg-[#232b45]/80 border border-white/10 shadow-sm flex items-start gap-4 p-5">
                                        <CheckCircle class="text-green-400 w-7 h-7 mt-1" />
                                        <div>
                                            <div class="font-bold text-lg text-white mb-1">1. 무료 체험</div>
                                            <div class="text-white/90 text-base leading-relaxed">
                                                <span class="font-bold text-green-300">무료 플랜(체험 기간)</span> 중에는 <span class="font-bold text-red-400">환불이 불가</span>합니다.
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 1개월 결제 -->
                                    <div class="rounded-xl bg-[#232b45]/80 border border-white/10 shadow-sm flex items-start gap-4 p-5">
                                        <Calendar class="text-blue-400 w-7 h-7 mt-1" />
                                        <div>
                                            <div class="font-bold text-lg text-white mb-1">2. 1개월 결제</div>
                                            <ul class="list-disc ml-5 space-y-1 text-white/90 text-base">
                                                <li><span class="font-bold text-yellow-300">14일 이내</span>: 사용일수만큼 <span class="font-bold text-green-300">일할 계산 환불</span></li>
                                                <li><span class="font-bold text-red-400">15일 이후</span>: <span class="font-bold text-red-400">환불 불가</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- 3/6/12개월 결제 -->
                                    <div class="rounded-xl bg-[#232b45]/80 border border-white/10 shadow-sm flex items-start gap-4 p-5">
                                        <CalendarRange class="text-purple-300 w-7 h-7 mt-1" />
                                        <div>
                                            <div class="font-bold text-lg text-white mb-1">3. 3/6/12개월 결제</div>
                                            <ul class="list-disc ml-5 space-y-1 text-white/90 text-base">
                                                <li><span class="font-bold text-yellow-300">14일 이내</span>: 1개월치 <span class="font-bold text-green-300">일할 계산</span> + 나머지 개월 <span class="font-bold text-green-300">전액 환불</span></li>
                                                <li><span class="font-bold text-red-400">15일 이후</span>: 1개월 사용으로 간주 + 나머지 개월 <span class="font-bold text-green-300">전액 환불</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- 할인 금액(위약금) -->
                                    <div class="rounded-xl bg-[#232b45]/80 border border-white/10 shadow-sm flex items-start gap-4 p-5">
                                        <Percent class="text-pink-300 w-7 h-7 mt-1" />
                                        <div>
                                            <div class="font-bold text-lg text-white mb-1">4. 할인 금액(위약금)</div>
                                            <div class="text-white/90 text-base leading-relaxed">
                                                <span class="font-bold text-pink-200">3/6/12개월 결제</span> 시 적용된 <span class="font-bold text-yellow-300">할인 금액</span>은 환불 시 <span class="font-bold text-red-400">위약금</span>으로 차감됩니다.
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 기타 안내 -->
                                    <div class="rounded-xl bg-[#232b45]/80 border border-white/10 shadow-sm flex items-start gap-4 p-5">
                                        <Info class="text-gray-300 w-7 h-7 mt-1" />
                                        <div>
                                            <div class="font-bold text-lg text-white mb-1">5. 기타 안내</div>
                                            <ul class="list-disc ml-5 space-y-1 text-white/90 text-base">
                                                <li>환불 금액은 <span class="font-bold text-green-300">최소 0원 보장</span> (마이너스 환불 없음)</li>
                                                <li>환불 정책은 <span class="font-bold text-blue-300">전자상거래법 및 소비자보호법</span>을 준수합니다.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-8 pb-7 pt-3 bg-white/5 border-t border-white/10">
                                    <button type="button" class="w-full bg-gradient-to-r from-purple-700 to-blue-700 hover:from-purple-800 hover:to-blue-800 text-white rounded-lg font-bold py-4 text-lg shadow transition-all duration-300" @click="showRefundModal = false">닫기</button>
                                </div>
                            </div>
                        </Modal>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 