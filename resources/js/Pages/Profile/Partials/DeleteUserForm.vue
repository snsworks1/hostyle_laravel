<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title>
            <span class="text-white">회원 탈퇴</span>
        </template>
        <template #description>
            <span class="text-white/80">계정을 영구적으로 삭제합니다.</span>
        </template>
        <template #content>
            <div class="max-w-xl text-sm text-white/80">
                계정을 삭제하면 모든 데이터가 영구적으로 삭제됩니다. 필요한 데이터는 미리 백업해 주세요.
            </div>
            <div class="mt-5">
                <DangerButton @click="confirmUserDeletion" class="bg-red-600 text-white rounded-lg px-6 py-2">
                    회원 탈퇴
                </DangerButton>
            </div>
            <!-- Delete Account Confirmation Modal -->
            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    회원 탈퇴
                </template>
                <template #content>
                    정말로 계정을 삭제하시겠습니까? 계정 삭제 시 모든 데이터가 영구적으로 삭제됩니다. 계속하려면 비밀번호를 입력해 주세요.
                    <div class="mt-4">
                        <TextInput
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4 bg-white/5 text-white placeholder-white/60 border-white/30 rounded-lg"
                            placeholder="비밀번호"
                            autocomplete="current-password"
                            @keyup.enter="deleteUser"
                        />
                        <InputError :message="form.errors.password" class="mt-2 text-red-300" />
                    </div>
                </template>
                <template #footer>
                    <SecondaryButton @click="closeModal" class="bg-white/10 text-white border-white/30 border rounded-lg hover:bg-white/20">
                        취소
                    </SecondaryButton>
                    <DangerButton
                        class="ms-3 bg-red-600 text-white rounded-lg px-6 py-2"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        영구 삭제
                    </DangerButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>
