<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import ServerHeader from '@/Components/ServerHeader.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';

defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});

const page = usePage();
const allServers = Array.isArray(page.props.servers) ? page.props.servers : [];
const server = allServers.length > 0 ? allServers[0] : { site_name: '내 계정', domain: '', days_remaining: 0, plan: '' };

// sessions가 undefined/null이면 []로 대체
const safeSessions = computed(() => Array.isArray(page.props.sessions) ? page.props.sessions : []);

console.log('allServers:', allServers);
console.log('server:', server);
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900">
        <ServerHeader :server="server" :all-servers="allServers" />
        <div class="max-w-4xl mx-auto py-10 px-4">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">내 계정</h1>
                <p class="text-white/60">회원 정보 및 보안 설정을 관리하세요.</p>
            </div>
            <div class="space-y-8">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6">
                    <h2 class="text-xl font-bold text-white mb-4">프로필 정보</h2>
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />
                </div>
                <div v-if="$page.props.jetstream.canUpdatePassword" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6">
                    <h2 class="text-xl font-bold text-white mb-4">비밀번호 변경</h2>
                    <UpdatePasswordForm />
                </div>
                <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6">
                    <h2 class="text-xl font-bold text-white mb-4">2단계 인증</h2>
                    <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication" />
                </div>
                <div class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6">
                    <h2 class="text-xl font-bold text-white mb-4">브라우저 세션 관리</h2>
                    <LogoutOtherBrowserSessionsForm :sessions="safeSessions" />
                </div>
                <div v-if="$page.props.jetstream.hasAccountDeletionFeatures" class="bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 p-6">
                    <h2 class="text-xl font-bold text-white mb-4">회원 탈퇴</h2>
                    <DeleteUserForm />
                </div>
            </div>
        </div>
    </div>
</template>
