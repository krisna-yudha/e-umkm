<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineProps<{
    status?: string;
}>();

const step = ref(1); // 1: Email Check, 2: Request Form, 3: Verification Code, 4: New Password
const isLoading = ref(false);
const message = ref('');
const messageType = ref(''); // 'success', 'error', 'info'
const userEmail = ref('');

// Form untuk cek email
const emailCheckForm = useForm({
    email: ''
});

// Form untuk request reset
const requestForm = useForm({
    email: '',
    reason: ''
});

// Form untuk verifikasi
const verifyForm = useForm({
    email: '',
    code: ''
});

// Form untuk password baru
const resetForm = useForm({
    email: '',
    code: '',
    password: '',
    password_confirmation: ''
});

const stepTitle = computed(() => {
    switch (step.value) {
        case 1: return 'Reset Password (Fast Mode)';
        case 2: return 'Permintaan Reset Password';
        case 3: return 'Verifikasi Kode';
        case 4: return 'Password Baru';
        default: return 'Reset Password';
    }
});

const formatCode = (event: any) => {
    let value = event.target.value.replace(/\D/g, '');
    if (value.length > 6) {
        value = value.substring(0, 6);
    }
    verifyForm.code = value;
    resetForm.code = value;
};

const checkEmail = async () => {
    if (!emailCheckForm.email) {
        showMessage('Masukkan email terlebih dahulu', 'error');
        return;
    }

    isLoading.value = true;
    
    try {
        const response = await fetch(`/api/check-reset-request?email=${encodeURIComponent(emailCheckForm.email)}`);
        
        if (response.ok) {
            const data = await response.json();
            userEmail.value = emailCheckForm.email;
            
            if (data.hasRequest) {
                if (data.status === 'approved') {
                    showMessage('Permintaan Anda sudah disetujui! Masukkan kode verifikasi.', 'success');
                    verifyForm.email = userEmail.value;
                    resetForm.email = userEmail.value;
                    step.value = 3;
                } else if (data.status === 'pending') {
                    showMessage('Permintaan Anda masih menunggu persetujuan admin.', 'info');
                    step.value = 3;
                    verifyForm.email = userEmail.value;
                    resetForm.email = userEmail.value;
                } else if (data.status === 'rejected') {
                    showMessage('Permintaan sebelumnya ditolak. Silakan buat permintaan baru.', 'info');
                    requestForm.email = userEmail.value;
                    step.value = 2;
                }
            } else {
                // No existing request, proceed to create new one
                requestForm.email = userEmail.value;
                step.value = 2;
            }
        } else if (response.status === 404) {
            // Email not found, but allow to proceed (will be handled by backend)
            requestForm.email = emailCheckForm.email;
            userEmail.value = emailCheckForm.email;
            step.value = 2;
        } else {
            // Other errors
            const errorData = await response.json().catch(() => ({ message: 'Terjadi kesalahan pada server' }));
            showMessage(errorData.message || 'Terjadi kesalahan saat memeriksa status', 'error');
        }
    } catch (error) {
        console.error('API call error:', error);
        // If API fails, allow user to proceed to create request
        requestForm.email = emailCheckForm.email;
        userEmail.value = emailCheckForm.email;
        step.value = 2;
        showMessage('Tidak dapat memeriksa status. Melanjutkan ke formulir permintaan.', 'info');
    }
    
    isLoading.value = false;
};

const submitRequest = async () => {
    if (requestForm.reason.length < 10) {
        showMessage('Alasan minimal 10 karakter', 'error');
        return;
    }

    isLoading.value = true;
    
    try {
        await requestForm.post(route('password.reset.request'), {
            onSuccess: () => {
                showMessage('Permintaan berhasil dikirim! Tunggu persetujuan admin.', 'success');
                verifyForm.email = requestForm.email;
                resetForm.email = requestForm.email;
                step.value = 3;
            },
            onError: (errors) => {
                showMessage(errors.email || errors.reason || 'Terjadi kesalahan', 'error');
            },
            onFinish: () => {
                isLoading.value = false;
            }
        });
    } catch (error) {
        isLoading.value = false;
        showMessage('Terjadi kesalahan jaringan', 'error');
    }
};

const submitVerification = async () => {
    if (verifyForm.code.length !== 6) {
        showMessage('Kode harus 6 digit', 'error');
        return;
    }

    isLoading.value = true;
    
    try {
        await verifyForm.post(route('password.reset.verify'), {
            onSuccess: () => {
                showMessage('Kode valid! Silakan buat password baru.', 'success');
                resetForm.code = verifyForm.code;
                step.value = 4;
            },
            onError: (errors) => {
                showMessage(errors.code || errors.email || 'Kode tidak valid atau belum disetujui admin', 'error');
            },
            onFinish: () => {
                isLoading.value = false;
            }
        });
    } catch (error) {
        isLoading.value = false;
        showMessage('Terjadi kesalahan jaringan', 'error');
    }
};

const submitNewPassword = async () => {
    if (resetForm.password.length < 8) {
        showMessage('Password minimal 8 karakter', 'error');
        return;
    }
    
    if (resetForm.password !== resetForm.password_confirmation) {
        showMessage('Konfirmasi password tidak cocok', 'error');
        return;
    }

    isLoading.value = true;
    
    try {
        await resetForm.post(route('password.reset.update'), {
            onSuccess: () => {
                showMessage('Password berhasil direset! Silakan login.', 'success');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 2000);
            },
            onError: (errors) => {
                showMessage(errors.password || 'Gagal reset password', 'error');
            },
            onFinish: () => {
                isLoading.value = false;
            }
        });
    } catch (error) {
        isLoading.value = false;
        showMessage('Terjadi kesalahan jaringan', 'error');
    }
};

const showMessage = (text: string, type: string) => {
    message.value = text;
    messageType.value = type;
    setTimeout(() => {
        message.value = '';
    }, 5000);
};

const goToStep = (targetStep: number) => {
    step.value = targetStep;
    message.value = '';
};

const resetToStart = () => {
    step.value = 1;
    message.value = '';
    userEmail.value = '';
    emailCheckForm.reset();
    requestForm.reset();
    verifyForm.reset();
    resetForm.reset();
};

const createNewRequest = () => {
    if (!emailCheckForm.email) {
        showMessage('Masukkan email terlebih dahulu', 'error');
        return;
    }
    
    requestForm.email = emailCheckForm.email;
    userEmail.value = emailCheckForm.email;
    step.value = 2;
    message.value = '';
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <Head title="Reset Password - Sistem UMKM" />
        
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
                <h1 class="text-3xl font-extrabold text-white mb-2">{{ stepTitle }}</h1>
                <div class="flex justify-center space-x-2 mt-4">
                    <div v-for="i in 4" :key="i" 
                         :class="['w-2 h-2 rounded-full transition-all duration-300', 
                                 i <= step ? 'bg-blue-400' : 'bg-white/30']">
                    </div>
                </div>
            </div>

            <!-- Message -->
            <div v-if="message" 
                 :class="['mb-6 p-4 rounded-xl text-sm font-medium transition-all duration-300',
                         messageType === 'success' ? 'bg-green-500/20 border border-green-500/30 text-green-300' : 
                         messageType === 'info' ? 'bg-blue-500/20 border border-blue-500/30 text-blue-300' :
                         'bg-red-500/20 border border-red-500/30 text-red-300']">
                {{ message }}
            </div>

            <!-- Form Card -->
            <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-8 shadow-2xl">
                
                <!-- Step 1: Email Check -->
                <form v-if="step === 1" @submit.prevent="checkEmail" class="space-y-6">
                    <div class="text-center text-gray-300 mb-6">
                        <p class="mb-2">Masukkan email Anda untuk:</p>
                        <ul class="text-sm space-y-1">
                            <li>🔍 <strong>Cek Status</strong> - Periksa permintaan reset yang sudah ada</li>
                            <li>🆕 <strong>Buat Permintaan Baru</strong> - Ajukan permintaan reset password</li>
                        </ul>
                    </div>

                    <div>
                        <label class="block text-white font-semibold mb-2">Email</label>
                        <input
                            type="email"
                            v-model="emailCheckForm.email"
                            required
                            autofocus
                            class="w-full bg-white/10 border border-white/20 text-white placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm"
                            placeholder="Masukkan email Anda"
                        />
                    </div>

                    <div class="space-y-3">
                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-lg disabled:opacity-50"
                        >
                            <span v-if="isLoading">Mengecek...</span>
                            <span v-else>Cek Status</span>
                        </button>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-white/20"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 text-gray-300">atau</span>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="createNewRequest"
                            :disabled="!emailCheckForm.email || isLoading"
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200 shadow-lg disabled:opacity-50"
                        >
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Buat Permintaan Baru
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Step 2: Request Reset -->
                <form v-else-if="step === 2" @submit.prevent="submitRequest" class="space-y-6">
                    <div class="text-center text-gray-300 mb-6">
                        <p>Buat permintaan reset password baru</p>
                    </div>

                    <div>
                        <label class="block text-white font-semibold mb-2">Email</label>
                        <input
                            type="email"
                            v-model="requestForm.email"
                            required
                            readonly
                            class="w-full bg-white/5 border border-white/20 text-gray-300 rounded-xl px-4 py-3 cursor-not-allowed"
                        />
                    </div>

                    <div>
                        <label class="block text-white font-semibold mb-2">Alasan Reset Password</label>
                        <textarea
                            v-model="requestForm.reason"
                            required
                            rows="4"
                            class="w-full bg-white/10 border border-white/20 text-white placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm resize-none"
                            placeholder="Jelaskan mengapa Anda memerlukan reset password (minimal 10 karakter)"
                        ></textarea>
                        
                        <div class="mt-2 text-xs text-gray-400 bg-white/5 rounded-lg p-3">
                            <p class="font-medium mb-1">Contoh alasan yang baik:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>"Saya lupa password karena tidak login dalam waktu lama"</li>
                                <li>"Password tidak berfungsi setelah mencoba beberapa kali"</li>
                                <li>"Akun saya mungkin telah dikompromikan"</li>
                            </ul>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="isLoading"
                        class="w-full bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-all duration-200 shadow-lg disabled:opacity-50"
                    >
                        <span v-if="isLoading">Mengirim...</span>
                        <span v-else>Kirim Permintaan</span>
                    </button>

                    <div class="text-center">
                        <button
                            type="button"
                            @click="resetToStart"
                            class="text-blue-300 hover:text-blue-200 text-sm"
                        >
                            ← Ganti Email
                        </button>
                    </div>
                </form>

                <!-- Step 3: Verification -->
                <div v-else-if="step === 3" class="space-y-6">
                    <div class="text-center text-gray-300 mb-6">
                        <p>Masukkan kode verifikasi 6 digit yang diberikan admin</p>
                        <p class="text-sm mt-2">Email: <span class="text-blue-300 font-semibold">{{ userEmail }}</span></p>
                    </div>

                    <form @submit.prevent="submitVerification" class="space-y-6">
                        <div>
                            <label class="block text-white font-semibold mb-2 text-center">Kode Verifikasi</label>
                            <input
                                type="text"
                                v-model="verifyForm.code"
                                @input="formatCode"
                                maxlength="6"
                                required
                                autofocus
                                class="w-full bg-white/10 border border-white/20 text-white placeholder-gray-300 rounded-xl px-4 py-4 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm text-center text-2xl tracking-[0.5em] font-mono"
                                placeholder="000000"
                            />
                            
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

                        <button
                            type="submit"
                            :disabled="isLoading || verifyForm.code.length !== 6"
                            class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-lg disabled:opacity-50"
                        >
                            <span v-if="isLoading">Memverifikasi...</span>
                            <span v-else>Verifikasi Kode</span>
                        </button>

                        <div class="text-center space-y-2">
                            <button
                                type="button"
                                @click="goToStep(2)"
                                class="block w-full text-blue-300 hover:text-blue-200 text-sm"
                            >
                                ← Buat Permintaan Baru
                            </button>
                            <button
                                type="button"
                                @click="resetToStart"
                                class="block w-full text-gray-400 hover:text-gray-300 text-sm"
                            >
                                Ganti Email
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 4: New Password -->
                <div v-else-if="step === 4" class="space-y-6">
                    <div class="text-center text-gray-300 mb-6">
                        <p>Buat password baru untuk akun Anda</p>
                    </div>

                    <form @submit.prevent="submitNewPassword" class="space-y-6">
                        <div>
                            <label class="block text-white font-semibold mb-2">Password Baru</label>
                            <input
                                type="password"
                                v-model="resetForm.password"
                                required
                                minlength="8"
                                autofocus
                                class="w-full bg-white/10 border border-white/20 text-white placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm"
                                placeholder="Masukkan password baru (min. 8 karakter)"
                            />
                        </div>

                        <div>
                            <label class="block text-white font-semibold mb-2">Konfirmasi Password</label>
                            <input
                                type="password"
                                v-model="resetForm.password_confirmation"
                                required
                                class="w-full bg-white/10 border border-white/20 text-white placeholder-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent backdrop-blur-sm"
                                placeholder="Ulangi password baru"
                            />
                        </div>

                        <button
                            type="submit"
                            :disabled="isLoading"
                            class="w-full bg-gradient-to-r from-purple-500 to-pink-600 text-white font-semibold py-3 px-4 rounded-xl hover:from-purple-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-200 shadow-lg disabled:opacity-50"
                        >
                            <span v-if="isLoading">Menyimpan...</span>
                            <span v-else>Reset Password</span>
                        </button>

                        <div class="text-center">
                            <button
                                type="button"
                                @click="goToStep(3)"
                                class="text-blue-300 hover:text-blue-200 text-sm"
                            >
                                ← Kembali ke Verifikasi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Navigation Links -->
                <div class="mt-6 text-center space-y-2">
                    <a href="/login" class="block text-gray-400 hover:text-gray-300 text-sm transition-colors duration-200">
                        ← Kembali ke Login
                    </a>
                    <a href="/" class="block text-gray-500 hover:text-gray-400 text-xs transition-colors duration-200">
                        Kembali ke Beranda
                    </a>
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