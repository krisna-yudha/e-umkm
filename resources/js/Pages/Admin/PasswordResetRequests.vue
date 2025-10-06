<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Admin {
    id: number;
    name: string;
}

interface PasswordResetRequest {
    id: number;
    code: string;
    reason: string;
    status: 'pending' | 'approved' | 'rejected';
    admin_note?: string;
    approved_at?: string;
    created_at: string;
    user: User;
    admin?: Admin;
}

const props = defineProps<{
    requests: {
        data: PasswordResetRequest[];
        current_page: number;
        last_page: number;
        total: number;
    };
}>();

// Initialize data

const selectedRequest = ref<PasswordResetRequest | null>(null);
const showModal = ref(false);
const actionType = ref<'approve' | 'reject'>('approve');

const page = usePage();
const flashMessage = computed(() => {
    const flash = page.props.flash as any;
    return flash?.success || flash?.error;
});

const flashType = computed(() => {
    const flash = page.props.flash as any;
    return flash?.success ? 'success' : 'error';
});

const noteForm = useForm({
    note: '',
});

const openModal = (request: PasswordResetRequest, action: 'approve' | 'reject') => {
    // Open modal for request action
    selectedRequest.value = request;
    actionType.value = action;
    noteForm.note = '';
    noteForm.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedRequest.value = null;
    noteForm.reset();
    noteForm.clearErrors();
};

const submitAction = () => {
    if (!selectedRequest.value) {
        return;
    }

    const routeName = actionType.value === 'approve' 
        ? 'admin.password-reset.approve' 
        : 'admin.password-reset.reject';

    // Process request action

    noteForm.post(route(routeName, { passwordResetRequest: selectedRequest.value.id }), {
        onSuccess: (page) => {
            closeModal();
            router.reload({ only: ['requests'] });
        },
        onError: (errors) => {
            // Handle submission errors
        },
        onFinish: () => {
            // Request finished
        }
    });
};

const getStatusBadge = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'approved':
            return 'bg-green-100 text-green-800';
        case 'rejected':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Kelola Permintaan Reset Password" />

    <AuthenticatedLayout>
        <template #header>
            <div class="bg-gradient-to-r from-red-600 to-orange-600 -mx-4 -mt-4 px-4 pt-4 pb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-2xl text-white leading-tight">
                                Reset Password
                            </h2>
                            <p class="text-red-100 text-sm">Kelola permintaan reset password pengguna</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-white font-semibold text-lg">{{ requests.total }}</div>
                        <div class="text-red-100 text-sm">Total Permintaan</div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-8 bg-gradient-to-br from-gray-50 to-red-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Flash Message -->
                <div v-if="flashMessage" 
                     :class="[
                         'mb-6 p-4 rounded-xl text-sm font-medium transition-all duration-300',
                         flashType === 'success' 
                             ? 'bg-green-50 border border-green-200 text-green-800'
                             : 'bg-red-50 border border-red-200 text-red-800'
                     ]">
                    {{ flashMessage }}
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-yellow-100">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-100 rounded-full">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Menunggu</p>
                                <p class="text-2xl font-bold text-gray-900">{{ requests.data.filter(r => r.status === 'pending').length }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-green-100">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-full">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Disetujui</p>
                                <p class="text-2xl font-bold text-gray-900">{{ requests.data.filter(r => r.status === 'approved').length }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-red-100">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-100 rounded-full">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Ditolak</p>
                                <p class="text-2xl font-bold text-gray-900">{{ requests.data.filter(r => r.status === 'rejected').length }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Daftar Permintaan Reset Password</h3>
                            <p class="text-gray-600 mt-1">Kelola dan proses permintaan reset password dari pengguna</p>
                        </div>
                    </div>

                    <div v-if="requests.data.length === 0" class="text-center py-16">
                        <div class="w-24 h-24 bg-gradient-to-br from-red-100 to-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada permintaan reset password</h4>
                        <p class="text-gray-600">Semua permintaan reset password telah diproses atau belum ada permintaan baru</p>
                    </div>

                    <div v-else class="space-y-6">
                        <div v-for="request in requests.data" :key="request.id" 
                             class="bg-gradient-to-r from-white to-gray-50 border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition-all duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4 mb-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <h4 class="font-bold text-lg text-gray-900">{{ request.user.name }}</h4>
                                                <span :class="getStatusBadge(request.status)" 
                                                      class="px-3 py-1 rounded-full text-xs font-semibold">
                                                    {{ request.status === 'pending' ? '⏳ Menunggu' : 
                                                       request.status === 'approved' ? '✅ Disetujui' : '❌ Ditolak' }}
                                                </span>
                                            </div>
                                            <p class="text-gray-600 mt-1">{{ request.user.email }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-white rounded-xl p-4 border border-gray-100 mb-4">
                                        <p class="text-sm font-semibold text-gray-700 mb-2">📝 Alasan Reset Password:</p>
                                        <p class="text-gray-800 leading-relaxed">{{ request.reason }}</p>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div class="bg-blue-50 rounded-lg p-3">
                                            <p class="text-xs font-semibold text-blue-700 mb-1">📅 Tanggal Permintaan</p>
                                            <p class="text-sm text-blue-800">{{ formatDate(request.created_at) }}</p>
                                        </div>
                                        <div v-if="request.approved_at" class="bg-green-50 rounded-lg p-3">
                                            <p class="text-xs font-semibold text-green-700 mb-1">✅ Tanggal Diproses</p>
                                            <p class="text-sm text-green-800">{{ formatDate(request.approved_at) }}</p>
                                        </div>
                                    </div>

                                    <div v-if="request.admin" class="bg-purple-50 rounded-lg p-3 mb-4">
                                        <p class="text-xs font-semibold text-purple-700 mb-1">👨‍💼 Diproses oleh</p>
                                        <p class="text-sm text-purple-800">{{ request.admin.name }}</p>
                                    </div>

                                    <div v-if="request.admin_note" class="bg-gray-50 rounded-lg p-4 mb-4 border-l-4 border-gray-400">
                                        <p class="text-xs font-semibold text-gray-700 mb-2">💬 Catatan Admin:</p>
                                        <p class="text-gray-800 italic">{{ request.admin_note }}</p>
                                    </div>
                                    
                                    <div v-if="request.status === 'approved'" class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-4 border border-green-200">
                                        <p class="text-sm font-semibold text-green-700 mb-2">🔐 Kode Verifikasi:</p>
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono text-xl font-bold text-green-800 bg-white px-3 py-1 rounded border">{{ request.code }}</span>
                                            <span class="text-xs text-green-600">Berikan kode ini kepada pengguna</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div v-if="request.status === 'pending'" class="flex flex-col space-y-2 ml-6">
                                    <button @click="openModal(request, 'approve')"
                                            class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-sm font-semibold rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                        ✅ Setujui
                                    </button>
                                    <button @click="openModal(request, 'reject')"
                                            class="px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 text-white text-sm font-semibold rounded-xl hover:from-red-600 hover:to-rose-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                        ❌ Tolak
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="requests.last_page > 1" class="mt-8 flex justify-center">
                        <nav class="flex space-x-2">
                            <a v-for="page in requests.last_page" :key="page"
                               :href="route('admin.password-reset-requests', { page })"
                               :class="[
                                   'px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200',
                                   page === requests.current_page 
                                       ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg' 
                                       : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 hover:shadow-md'
                               ]">
                                {{ page }}
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
            <div class="relative mx-auto p-8 border w-full max-w-md shadow-2xl rounded-2xl bg-white">
                <div class="text-center mb-6">
                    <div :class="[
                        'w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4',
                        actionType === 'approve' ? 'bg-green-100' : 'bg-red-100'
                    ]">
                        <svg v-if="actionType === 'approve'" class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <svg v-else class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        {{ actionType === 'approve' ? '✅ Setujui Permintaan' : '❌ Tolak Permintaan' }}
                    </h3>
                    <p class="text-gray-600 text-sm">
                        {{ actionType === 'approve' 
                            ? 'Pengguna akan menerima kode verifikasi untuk reset password' 
                            : 'Berikan alasan yang jelas mengapa permintaan ditolak' }}
                    </p>
                </div>
                
                <div v-if="selectedRequest" class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-4 mb-6 border border-blue-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="font-semibold text-gray-900">{{ selectedRequest.user.name }}</p>
                                                            <p class="text-sm text-gray-600">{{ selectedRequest.user.email }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                
                                                <form @submit.prevent="submitAction">
                                                    <div class="mb-6">
                                                        <label for="note" class="block text-sm font-semibold text-gray-700 mb-2">
                                                            {{ actionType === 'approve' ? '💬 Catatan (Opsional)' : '📝 Alasan Penolakan (Wajib)' }}
                                                        </label>
                                                        <textarea
                                                            id="note"
                                                            v-model="noteForm.note"
                                                            :required="actionType === 'reject'"
                                                            rows="4"
                                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                                            :placeholder="actionType === 'approve' 
                                                                ? 'Tambahkan catatan jika diperlukan...' 
                                                                : 'Jelaskan mengapa permintaan ini ditolak...'"
                                                        ></textarea>
                                                        <div v-if="noteForm.errors.note" class="mt-2 text-sm text-red-600">
                                                            {{ noteForm.errors.note }}
                                                        </div>
                                                    </div>
                                
                                                    <div class="flex space-x-3">
                                                        <button
                                                            type="button"
                                                            @click="closeModal"
                                                            class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200"
                                                        >
                                                            🚫 Batal
                                                        </button>
                                                        <button
                                                            type="submit"
                                                            :disabled="noteForm.processing"
                                                            :class="[
                                                                'flex-1 px-4 py-3 font-semibold rounded-xl transition-all duration-200 disabled:opacity-50',
                                                                actionType === 'approve' 
                                                                    ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white hover:from-green-600 hover:to-emerald-700' 
                                                                    : 'bg-gradient-to-r from-red-500 to-rose-600 text-white hover:from-red-600 hover:to-rose-700'
                                                            ]"
                                                        >
                                                            <span v-if="!noteForm.processing">
                                                                {{ actionType === 'approve' ? '✅ Setujui' : '❌ Tolak' }}
                                                            </span>
                                                            <span v-else>⏳ Memproses...</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </AuthenticatedLayout>
                                </template>