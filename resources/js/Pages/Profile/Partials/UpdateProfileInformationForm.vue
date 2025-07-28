<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation" class="!bg-transparent !shadow-none">
        <template #title>
            <span class="text-white">프로필 정보</span>
        </template>

        <template #description>
            <span class="text-white/80">이름, 이메일 등 회원 정보를 수정할 수 있습니다.</span>
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
                <input
                    id="photo"
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                >
                <InputLabel for="photo" value="프로필 사진" class="text-white" />
                <div v-show="! photoPreview" class="mt-2">
                    <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full size-20 object-cover">
                </div>
                <div v-show="photoPreview" class="mt-2">
                    <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center" :style="'background-image: url(' + photoPreview + ');'" />
                </div>
                <SecondaryButton class="mt-2 me-2 bg-white/10 text-white border-white/30 border rounded-lg hover:bg-white/20" type="button" @click.prevent="selectNewPhoto">
                    새 사진 선택
                </SecondaryButton>
                <SecondaryButton v-if="user.profile_photo_path" type="button" class="mt-2 bg-white/10 text-white border-white/30 border rounded-lg hover:bg-white/20" @click.prevent="deletePhoto">
                    사진 삭제
                </SecondaryButton>
                <InputError :message="form.errors.photo" class="mt-2 text-red-300" />
            </div>
            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="name" value="이름" class="text-white" />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full bg-white/5 border border-white/30 text-white placeholder-white/60 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    required
                    autocomplete="name"
                    placeholder="이름 입력"
                />
                <InputError :message="form.errors.name" class="mt-2 text-red-300" />
            </div>
            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="이메일" class="text-white" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full bg-white/5 border border-white/30 text-white placeholder-white/60 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                    required
                    autocomplete="username"
                    placeholder="이메일 입력"
                />
                <InputError :message="form.errors.email" class="mt-2 text-red-300" />
                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="text-sm mt-2 text-white/80">
                        이메일 인증이 완료되지 않았습니다.
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-blue-300 hover:text-blue-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            @click.prevent="sendEmailVerification"
                        >
                            인증 메일 다시 보내기
                        </Link>
                    </p>
                    <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-400">
                        인증 메일이 발송되었습니다.
                    </div>
                </div>
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
