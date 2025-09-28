<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <Head title="Register - Sistem UMKM" />
        
        <!-- Background Elements -->
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-20"></div>
            <div class="absolute top-10 left-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
            <div class="absolute top-0 right-0 w-72 h-72 bg-yellow-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
        </div>

        <div class="relative z-10 max-w-md w-full">
            <!-- Header -->
            <div class="text-center mb-8">
                <Link href="/" class="inline-block">
                    <h1 class="text-3xl font-extrabold text-white mb-2">Sistem UMKM</h1>
                </Link>
                <p class="text-gray-300">Bergabung dengan komunitas UMKM</p>
            </div>

            <!-- Register Card -->
            <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 shadow-2xl">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="name" value="Nama Lengkap" class="text-white font-semibold" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-2 block w-full bg-white/10 border border-white/20 text-white placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Masukkan nama lengkap Anda"
                        />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" class="text-white font-semibold" />
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-2 block w-full bg-white/10 border border-white/20 text-white placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Masukkan email Anda"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Password" class="text-white font-semibold" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-2 block w-full bg-white/10 border border-white/20 text-white placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            placeholder="Buat password yang kuat"
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
                            placeholder="Ulangi password Anda"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div>
                        <InputLabel for="role" value="Daftar sebagai" class="text-white font-semibold" />
                        <select
                            id="role"
                            class="mt-2 block w-full bg-white/10 border border-white/20 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm"
                            v-model="form.role"
                            required
                        >
                            <option value="user" class="bg-gray-800 text-white">User (Pencari UMKM)</option>
                            <option value="umkm" class="bg-gray-800 text-white">Pelaku UMKM</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.role" />
                    </div>

                    <div class="space-y-4">
                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-green-500 to-blue-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-green-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-transparent transition-all duration-200 shadow-lg"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Mendaftar...</span>
                            <span v-else>Daftar Sekarang</span>
                        </button>

                        <div class="text-center">
                            <span class="text-gray-300 text-sm">Sudah punya akun? </span>
                            <Link :href="route('login')" class="text-blue-300 hover:text-blue-200 font-semibold text-sm">
                                Masuk di sini
                            </Link>
                        </div>

                        <div class="text-center">
                            <Link href="/" class="text-gray-400 hover:text-gray-300 text-sm">
                                ← Kembali ke beranda
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}
</style>
