<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ActionSection from '@/Components/ActionSection.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    sessions: Array,
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmLogout = () => {
    confirmingLogout.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route('other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            <span class="text-white">브라우저 세션 관리</span>
        </template>
        <template #description>
            <span class="text-white/80">다른 브라우저 및 기기에서 로그인된 세션을 관리할 수 있습니다.</span>
        </template>
        <template #content>
            <div class="max-w-xl text-sm text-white/80">
                필요하다면 다른 모든 브라우저/기기에서 로그아웃할 수 있습니다. 아래는 최근 세션 목록입니다. 계정이 노출된 것 같다면 비밀번호도 변경해 주세요.
            </div>
            <!-- Other Browser Sessions -->
            <div v-if="sessions.length > 0" class="mt-5 space-y-6">
                <div v-for="(session, i) in sessions" :key="i" class="flex items-center">
                    <div>
                        <svg v-if="session.agent.is_desktop" class="size-8 text-white/40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                        </svg>
                        <svg v-else class="size-8 text-white/40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </div>
                    <div class="ms-3">
                        <div class="text-sm text-white/80">
                            {{ session.agent.platform ? session.agent.platform : '알 수 없음' }} - {{ session.agent.browser ? session.agent.browser : '알 수 없음' }}
                        </div>
                        <div>
                            <div class="text-xs text-white/50">
                                {{ session.ip_address }},
                                <span v-if="session.is_current_device" class="text-green-400 font-semibold">현재 기기</span>
                                <span v-else>마지막 활동 {{ session.last_active }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center mt-5">
                <PrimaryButton @click="confirmLogout" class="bg-gradient-to-r from-purple-500 to-blue-500 border-0 text-white rounded-lg px-6 py-2">
                    다른 브라우저 세션 로그아웃
                </PrimaryButton>
                <ActionMessage :on="form.recentlySuccessful" class="ms-3 text-green-400">
                    완료되었습니다.
                </ActionMessage>
            </div>
            <!-- Log Out Other Devices Confirmation Modal -->
            <DialogModal :show="confirmingLogout" @close="closeModal">
                <template #title>
                    다른 브라우저 세션 로그아웃
                </template>
                <template #content>
                    다른 모든 브라우저/기기에서 로그아웃하려면 비밀번호를 입력해 주세요.
                    <div class="mt-4">
                        <TextInput
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4 bg-white/5 text-white placeholder-white/60 border-white/30 rounded-lg"
                            placeholder="비밀번호"
                            autocomplete="current-password"
                            @keyup.enter="logoutOtherBrowserSessions"
                        />
                        <InputError :message="form.errors.password" class="mt-2 text-red-300" />
                    </div>
                </template>
                <template #footer>
                    <SecondaryButton @click="closeModal" class="bg-white/10 text-white border-white/30 border rounded-lg hover:bg-white/20">
                        취소
                    </SecondaryButton>
                    <PrimaryButton
                        class="ms-3 bg-gradient-to-r from-purple-500 to-blue-500 border-0 text-white rounded-lg px-6 py-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="logoutOtherBrowserSessions"
                    >
                        로그아웃
                    </PrimaryButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>
