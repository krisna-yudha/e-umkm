<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    email?: string;
}>();

const form = useForm({
    email: props.email || '',
    code: '',
});

const showEmailForm = ref(!props.email);

const formatCode = (event: any) => {
    // Only allow numbers and limit to 6 digits
    let value = event.target.value.replace(/\D/g, '');
    if (value.length > 6) {
        value = value.substring(0, 6);
    }
    form.code = value;
};

const submitEmailCheck = () => {
    // First verify that this email has a pending/approved request
    form.transform((data) => ({
        email: data.email,
        action: 'check_email'
    })).post(route('password.reset.verify'), {
        onSuccess: () => {
            showEmailForm.value = false;
        },
        onError: () => {
            // Stay on email form if error
        }
    });
};

const submit = () => {
    form.post(route('password.reset.verify'));
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <Head title="Verifikasi Kode - Sistem UMKM" />
        
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
                <h1 class="text-3xl font-extrabold text-white mb-2">Kode Verifikasi</h1>
                <p class="text-gray-300">Masukkan kode yang diberikan admin</p>
            </div>

            <!-- Verification Card -->
            <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 shadow-2xl">
                
                <!-- Email Verification Form (if no email provided) -->
                <div v-if="showEmailForm">
                    <div class="mb-6 text-sm text-gray-300 leading-relaxed">
                        Masukkan email yang Anda gunakan untuk meminta reset password. Kami akan memverifikasi bahwa Anda memiliki permintaan yang valid.
                    </div>

                    <form @submit.prevent="submitEmailCheck" class="space-y-6">
                        <div>
                            <InputLabel for="email" value="Email" class="text-white font-semibold" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-2 block w-full bg-white/10 border border-white/20 text-white placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Masukkan email Anda"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="bg-white/5 rounded-xl p-4">
                            <p class="text-sm text-white mb-2">ℹ️ Informasi:</p>
                            <ul class="text-xs text-gray-300 space-y-1">
                                <li>• Email harus sama dengan yang digunakan saat request reset</li>
                                <li>• Pastikan permintaan Anda sudah disetujui admin</li>
                                <li>• Hubungi admin jika mengalami kesulitan</li>
                            </ul>
                        </div>

                        <div class="space-y-4">
                            <button
                                type="submit"
                                class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-transparent transition-all duration-200 shadow-lg"
                                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Memverifikasi Email...</span>
                                <span v-else>Verifikasi Email</span>
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

                <!-- Code Verification Form -->
                <div v-else>
                    <div class="mb-6 text-sm text-gray-300 leading-relaxed">
                        Masukkan kode verifikasi 6 digit yang diberikan admin setelah permintaan reset password Anda disetujui.
                    </div>

                    <div class="mb-6 p-4 bg-white/10 border border-white/20 rounded-xl">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-300 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                            <div class="text-sm text-white">
                                Email: <span class="font-semibold text-blue-200">{{ form.email }}</span>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="code" value="Kode Verifikasi" class="text-white font-semibold" />
                            
                            <TextInput
                                id="code"
                                type="text"
                                class="mt-2 block w-full bg-white/10 border border-white/20 text-white placeholder-gray-300 rounded-xl px-4 py-4 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm text-center text-2xl tracking-[0.5em] font-mono"
                                v-model="form.code"
                                required
                                autofocus
                                maxlength="6"
                                placeholder="000000"
                                @input="formatCode"
                            />

                            <InputError class="mt-2" :message="form.errors.code" />
                            <InputError class="mt-2" :message="form.errors.email" />
                            
                            <div class="mt-3 text-xs text-gray-400 text-center">
                                Masukkan 6 digit kode yang diberikan admin
                            </div>
                        </div>

                        <div class="bg-white/5 rounded-xl p-4">
                            <p class="text-sm text-white mb-2">💡 Tips:</p>
                            <ul class="text-xs text-gray-300 space-y-1">
                                <li>• Kode terdiri dari 6 digit angka</li>
                                <li>• Pastikan permintaan Anda sudah disetujui admin</li>
                                <li>• Hubungi admin jika belum menerima kode</li>
                            </ul>
                        </div>

                        <div class="space-y-4">
                            <button
                                type="submit"
                                class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-transparent transition-all duration-200 shadow-lg"
                                :class="{ 'opacity-50 cursor-not-allowed': form.processing || form.code.length !== 6 }"
                                :disabled="form.processing || form.code.length !== 6"
                            >
                                <span v-if="form.processing">Memverifikasi...</span>
                                <span v-else>Verifikasi Kode</span>
                            </button>

                            <div class="text-center space-y-2">
                                <button
                                    type="button"
                                    @click="showEmailForm = true; form.reset(); form.clearErrors()"
                                    class="block w-full text-blue-300 hover:text-blue-200 text-sm transition-colors duration-200"
                                >
                                    ← Ganti Email
                                </button>
                                <a
                                    :href="route('login')"
                                    class="block text-gray-400 hover:text-gray-300 text-sm transition-colors duration-200"
                                >
                                    Kembali ke Login
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
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