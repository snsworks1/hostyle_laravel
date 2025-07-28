<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = useForm({
    code: '',
});

const twoFactorEnabled = computed(
    () => ! enabling.value && page.props.auth.user?.two_factor_enabled,
);

watch(twoFactorEnabled, () => {
    if (! twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            showQrCode(),
            showSetupKey(),
            showRecoveryCodes(),
        ]),
        onFinish: () => {
            enabling.value = false;
            confirming.value = props.requiresConfirmation;
        },
    });
};

const showQrCode = () => {
    return axios.get(route('two-factor.qr-code')).then(response => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route('two-factor.secret-key')).then(response => {
        setupKey.value = response.data.secretKey;
    });
}

const showRecoveryCodes = () => {
    return axios.get(route('two-factor.recovery-codes')).then(response => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmationForm.post(route('two-factor.confirm'), {
        errorBag: "confirmTwoFactorAuthentication",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};

const regenerateRecoveryCodes = () => {
    axios
        .post(route('two-factor.recovery-codes'))
        .then(() => showRecoveryCodes());
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
        },
    });
};
</script>

<template>
    <ActionSection>
        <template #title>
            <span class="text-white">2단계 인증</span>
        </template>
        <template #description>
            <span class="text-white/80">계정 보안을 위해 2단계 인증을 설정할 수 있습니다.</span>
        </template>
        <template #content>
            <h3 v-if="twoFactorEnabled && ! confirming" class="text-lg font-bold text-white">
                2단계 인증이 활성화되어 있습니다.
            </h3>
            <h3 v-else-if="twoFactorEnabled && confirming" class="text-lg font-bold text-white">
                2단계 인증 활성화 마무리
            </h3>
            <h3 v-else class="text-lg font-bold text-white">
                2단계 인증이 비활성화되어 있습니다.
            </h3>
            <div class="mt-3 max-w-xl text-sm text-white/80">
                <p>
                    2단계 인증을 활성화하면 로그인 시 인증 앱에서 생성된 보안 코드를 추가로 입력해야 합니다. Google Authenticator 등에서 QR코드를 스캔하거나 Setup Key를 입력해 주세요.
                </p>
            </div>
            <div v-if="twoFactorEnabled">
                <div v-if="qrCode">
                    <div class="mt-4 max-w-xl text-sm text-white/80">
                        <p v-if="confirming" class="font-semibold">
                            아래 QR코드를 인증 앱으로 스캔하거나 Setup Key를 입력한 후, 생성된 OTP 코드를 입력해 주세요.
                        </p>
                        <p v-else>
                            2단계 인증이 활성화되었습니다. 아래 QR코드를 인증 앱으로 스캔하거나 Setup Key를 입력해 주세요.
                        </p>
                    </div>
                    <div class="mt-4 p-2 inline-block bg-white rounded-lg" v-html="qrCode" />
                    <div v-if="setupKey" class="mt-4 max-w-xl text-sm text-white/80">
                        <p class="font-semibold">
                            Setup Key: <span v-html="setupKey"></span>
                        </p>
                    </div>
                    <div v-if="confirming" class="mt-4">
                        <InputLabel for="code" value="인증 코드" class="text-white" />
                        <TextInput
                            id="code"
                            v-model="confirmationForm.code"
                            type="text"
                            name="code"
                            class="block mt-1 w-1/2 bg-white/5 text-white placeholder-white/60 border-white/30 rounded-lg"
                            inputmode="numeric"
                            autofocus
                            autocomplete="one-time-code"
                            @keyup.enter="confirmTwoFactorAuthentication"
                        />
                        <InputError :message="confirmationForm.errors.code" class="mt-2 text-red-300" />
                    </div>
                </div>
                <div v-if="recoveryCodes.length > 0 && ! confirming">
                    <div class="mt-4 max-w-xl text-sm text-white/80">
                        <p class="font-semibold">
                            복구 코드를 안전한 곳에 보관하세요. 2단계 인증 기기를 분실했을 때 계정 복구에 사용할 수 있습니다.
                        </p>
                    </div>
                    <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-white/10 text-white rounded-lg border border-white/20">
                        <div v-for="code in recoveryCodes" :key="code">
                            {{ code }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5">
                <div v-if="! twoFactorEnabled">
                    <ConfirmsPassword @confirmed="enableTwoFactorAuthentication">
                        <PrimaryButton type="button" :class="{ 'opacity-25': enabling }" :disabled="enabling" class="bg-gradient-to-r from-purple-500 to-blue-500 border-0 text-white rounded-lg px-6 py-2">
                            2단계 인증 활성화
                        </PrimaryButton>
                    </ConfirmsPassword>
                </div>
                <div v-else>
                    <ConfirmsPassword @confirmed="confirmTwoFactorAuthentication">
                        <PrimaryButton
                            v-if="confirming"
                            type="button"
                            class="me-3 bg-gradient-to-r from-purple-500 to-blue-500 border-0 text-white rounded-lg px-6 py-2"
                            :class="{ 'opacity-25': enabling || confirmationForm.processing }"
                            :disabled="enabling || confirmationForm.processing"
                        >
                            인증 완료
                        </PrimaryButton>
                    </ConfirmsPassword>
                    <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
                        <SecondaryButton
                            v-if="recoveryCodes.length > 0 && ! confirming"
                            class="me-3 bg-white/10 text-white border-white/30 border rounded-lg hover:bg-white/20"
                        >
                            복구 코드 재발급
                        </SecondaryButton>
                    </ConfirmsPassword>
                    <ConfirmsPassword @confirmed="showRecoveryCodes">
                        <SecondaryButton
                            v-if="recoveryCodes.length === 0 && ! confirming"
                            class="me-3 bg-white/10 text-white border-white/30 border rounded-lg hover:bg-white/20"
                        >
                            복구 코드 보기
                        </SecondaryButton>
                    </ConfirmsPassword>
                    <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                        <SecondaryButton
                            v-if="confirming"
                            :class="{ 'opacity-25': disabling }"
                            :disabled="disabling"
                            class="bg-white/10 text-white border-white/30 border rounded-lg hover:bg-white/20"
                        >
                            취소
                        </SecondaryButton>
                    </ConfirmsPassword>
                    <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                        <DangerButton
                            v-if="! confirming"
                            :class="{ 'opacity-25': disabling }"
                            :disabled="disabling"
                            class="bg-red-600 text-white rounded-lg px-6 py-2"
                        >
                            2단계 인증 비활성화
                        </DangerButton>
                    </ConfirmsPassword>
                </div>
            </div>
        </template>
    </ActionSection>
</template>
