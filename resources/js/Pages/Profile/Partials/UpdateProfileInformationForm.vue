<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
}>();

const user = usePage().props.auth.user;
const photoInput = ref<HTMLInputElement | null>(null);
const photoPreview = ref<string | null>(null);

const form = useForm({
    name: user.name,
    email: user.email,
    profile_photo: null as File | null,
});

const selectNewPhoto = () => {
    photoInput.value?.click();
};

const updatePhotoPreview = () => {
    const file = photoInput.value?.files?.[0];
    if (!file) return;

    form.profile_photo = file;
    
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
};

const deletePhoto = () => {
    form.profile_photo = null;
    photoPreview.value = null;
    if (photoInput.value) {
        photoInput.value.value = '';
    }
};

const getProfilePhotoUrl = () => {
    if (photoPreview.value) {
        return photoPreview.value;
    }
    if ((user as any).profile_photo) {
        return `/storage/${(user as any).profile_photo}`;
    }
    return null;
};
</script>

<template>
    <section>
        <form
            @submit.prevent="form.post(route('profile.update'), { preserveScroll: true })"
            class="space-y-6"
        >
                    <div class="flex items-center space-x-6">
                <div class="shrink-0">
                    <img
                        v-if="getProfilePhotoUrl()"
                        :src="getProfilePhotoUrl() || ''"
                        alt="Profile Photo"
                        class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg ring-4 ring-blue-100"
                    />
                    <div
                        v-else
                        class="h-24 w-24 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center border-4 border-white shadow-lg ring-4 ring-blue-100"
                    >
                        <svg class="h-10 w-10 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                </div>
                
                <div class="flex-1">
                    <InputLabel value="Foto Profile" class="mb-3 text-lg font-semibold text-gray-800" />
                    <div class="flex space-x-3">
                        <button
                            type="button"
                            @click="selectNewPhoto"
                            class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 border border-transparent rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform hover:scale-105 transition-all duration-200 shadow-lg"
                        >
                            📸 Pilih Foto
                        </button>
                        
                        <button
                            v-if="getProfilePhotoUrl()"
                            type="button"
                            @click="deletePhoto"
                            class="px-6 py-3 text-sm font-semibold text-red-600 bg-white border-2 border-red-300 rounded-lg hover:bg-red-50 hover:border-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transform hover:scale-105 transition-all duration-200 shadow-md"
                        >
                            🗑️ Hapus Foto
                        </button>
                    </div>
                    
                    <input
                        ref="photoInput"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="updatePhotoPreview"
                    />
                    
                    <InputError class="mt-2" :message="form.errors.profile_photo" />
                    
                    <p class="mt-2 text-sm text-gray-500 bg-gray-50 rounded-lg p-3 border border-gray-200">
                        💡 <strong>Tips:</strong> Gunakan foto dengan resolusi tinggi (JPG, PNG, atau GIF, maksimal 2MB) untuk hasil terbaik
                    </p>
                </div>
            </div>

            <!-- Name Field -->
            <div class="space-y-2">
                <InputLabel for="name" value="Nama Lengkap" class="text-lg font-semibold text-gray-800" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full px-4 py-3 text-lg border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 transition-all duration-200"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Masukkan nama lengkap Anda"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- Email Field -->
            <div class="space-y-2">
                <InputLabel for="email" value="Alamat Email" class="text-lg font-semibold text-gray-800" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full px-4 py-3 text-lg border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:ring-blue-500 transition-all duration-200"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="contoh@email.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Email Verification -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Alamat email Anda belum diverifikasi.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-blue-600 underline hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Klik di sini untuk mengirim ulang email verifikasi.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    Link verifikasi baru telah dikirim ke alamat email Anda.
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex items-center gap-6 pt-6 border-t border-gray-200">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-6 py-3 font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 border border-transparent rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-3 focus:ring-emerald-200 disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5 transition-all duration-200 shadow-lg hover:shadow-xl"
                >
                    <div class="flex items-center">
                        <svg v-if="form.processing" class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg v-if="!form.processing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span v-if="!form.processing">Simpan Perubahan</span>
                        <span v-else>⏳ Menyimpan...</span>
                    </div>
                </button>

                <Transition
                    enter-active-class="transition ease-in-out duration-300"
                    enter-from-class="opacity-0 transform translate-x-4"
                    enter-to-class="opacity-100 transform translate-x-0"
                    leave-active-class="transition ease-in-out duration-300"
                    leave-to-class="opacity-0 transform translate-x-4"
                >
                    <div
                        v-if="form.recentlySuccessful"
                        class="flex items-center px-4 py-2 bg-green-100 border border-green-300 rounded-lg"
                    >
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-green-800 font-semibold">✨ Berhasil disimpan!</span>
                    </div>
                </Transition>
            </div>
        </form>
    </section>
</template>
