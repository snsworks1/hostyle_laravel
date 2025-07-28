<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <FormSection @submitted="updatePassword">
        <template #title>
            <span class="text-white">비밀번호 변경</span>
        </template>
        <template #description>
            <span class="text-white/80">계정 보안을 위해 주기적으로 강력한 비밀번호로 변경해 주세요.</span>
        </template>
        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="current_password" value="현재 비밀번호" class="text-white" />
                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full bg-white/5 text-white placeholder-white/60 border-white/30 rounded-lg"
                    autocomplete="current-password"
                    placeholder="현재 비밀번호 입력"
                />
                <InputError :message="form.errors.current_password" class="mt-2 text-red-300" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="password" value="새 비밀번호" class="text-white" />
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full bg-white/5 text-white placeholder-white/60 border-white/30 rounded-lg"
                    autocomplete="new-password"
                    placeholder="새 비밀번호 입력"
                />
                <InputError :message="form.errors.password" class="mt-2 text-red-300" />
            </div>
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="password_confirmation" value="새 비밀번호 확인" class="text-white" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full bg-white/5 text-white placeholder-white/60 border-white/30 rounded-lg"
                    autocomplete="new-password"
                    placeholder="새 비밀번호 재입력"
                />
                <InputError :message="form.errors.password_confirmation" class="mt-2 text-red-300" />
            </div>
        </template>
        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3 text-green-400">
                저장되었습니다.
            </ActionMessage>
            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="bg-gradient-to-r from-purple-500 to-blue-500 border-0 text-white rounded-lg px-6 py-2">
                저장
            </PrimaryButton>
        </template>
    </FormSection>
</template>
