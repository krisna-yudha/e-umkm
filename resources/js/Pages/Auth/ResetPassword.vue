<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    token: string;
    user_id: string;
}>();

const form = useForm({
    token: props.token,
    user_id: props.user_id,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.reset.update'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <Head title="Reset Password - Sistem UMKM" />
        
        <!-- Background Elements - Static and Subtle -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-20"></div>
            <div class="absolute top-20 left-10 w-80 h-80 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-8"></div>
            <div class="absolute top-10 right-10 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-8"></div>
            <div class="absolute bottom-20 left-1/4 w-64 h-64 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-8"></div>
        </div>

        <div class="relative z-10 max-w-md w-full">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-white mb-2">Password Baru</h1>
                <p class="text-gray-300">Buat password baru untuk akun Anda</p>
            </div>

            <!-- Reset Password Card -->
            <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 shadow-2xl">
                <div class="mb-6 text-sm text-gray-300 leading-relaxed">
                    Buat password baru untuk akun Anda. Pastikan password yang Anda buat aman dan mudah diingat.
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="password" value="Password Baru" class="text-white font-semibold" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-2 block w-full bg-white/10 border border-white/20 text-white placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm"
                            v-model="form.password"
                            required
                            autofocus
                            autocomplete="new-password"
                            placeholder="Masukkan password baru"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Konfirmasi Password" class="text-white font-semibold" />
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-2 block w-full bg-white/10 border border-white/20 text-white placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi password baru"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="bg-white/5 rounded-xl p-4">
                        <p class="text-sm font-medium text-white mb-3">Password harus memenuhi kriteria:</p>
                        <ul class="text-xs text-gray-300 space-y-2">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Minimal 8 karakter
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Mengandung huruf dan angka
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Pastikan kedua password sama
                            </li>
                        </ul>
                    </div>

                    <div class="space-y-4">
                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-transparent transition-all duration-200 shadow-lg"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Menyimpan Password...</span>
                            <span v-else>Reset Password</span>
                        </button>

                        <div class="text-center">
                            <a
                                :href="route('login')"
                                class="text-gray-400 hover:text-gray-300 text-sm transition-colors duration-200"
                            >
                                ← Kembali ke Login
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Removed distracting blob animations for better UX */
</style>
