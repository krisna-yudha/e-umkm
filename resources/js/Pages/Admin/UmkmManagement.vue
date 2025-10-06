<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    umkms: Array<{
        id: number;
        nama_umkm: string;
        deskripsi: string;
        kategori: string;
        alamat: string;
        no_telepon: string;
        email: string;
        status: string;
        gambar?: string;
        user: {
            name: string;
            email: string;
        };
    }>;
}>();

const showModal = ref(false);
const selectedUmkm = ref<any>(null);

const toggleStatus = (umkm: any) => {
    selectedUmkm.value = umkm;
    showModal.value = true;
};

const directToggle = (umkm: any) => {
    router.post(route('admin.umkm.toggle', umkm.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Success handled by page reload
        },
        onError: (errors) => {
            // Handle toggle error
        }
    });
};

const confirmToggle = () => {
    if (selectedUmkm.value) {
        router.post(route('admin.umkm.toggle', selectedUmkm.value.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                selectedUmkm.value = null;
            },
            onError: (errors) => {
                // Handle toggle error
                alert('Terjadi kesalahan saat mengubah status UMKM');
            }
        });
    }
};

const cancelToggle = () => {
    showModal.value = false;
    selectedUmkm.value = null;
};
</script>

<template>
    <Head title="Kelola UMKM - Admin" />
    <AuthenticatedLayout>
        <template #header>
            <div class="bg-gradient-to-r from-red-600 to-orange-600 -mx-4 -mt-4 px-4 pt-4 pb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-2xl text-white leading-tight">
                                Kelola UMKM
                            </h2>
                            <p class="text-red-100 text-sm">Pantau dan kelola status semua UMKM</p>
                        </div>
                    </div>
                    <Link
                        :href="route('admin.dashboard')"
                        class="inline-flex items-center px-6 py-3 bg-white text-red-600 border border-transparent rounded-xl font-semibold text-sm hover:bg-red-50 transition-all duration-200 shadow-lg"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Dashboard
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 bg-gradient-to-br from-gray-50 to-red-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-blue-100">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total UMKM</p>
                                <p class="text-2xl font-bold text-gray-900">{{ umkms?.length || 0 }}</p>
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
                                <p class="text-sm font-medium text-gray-600">UMKM Aktif</p>
                                <p class="text-2xl font-bold text-gray-900">{{ umkms?.filter(u => u.status === 'active').length || 0 }}</p>
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
                                <p class="text-sm font-medium text-gray-600">UMKM Nonaktif</p>
                                <p class="text-2xl font-bold text-gray-900">{{ umkms?.filter(u => u.status === 'inactive').length || 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- UMKM Table -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200">
                    <div class="px-8 py-6 border-b border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900">Daftar Semua UMKM</h3>
                        <p class="text-gray-600 text-sm mt-1">Kelola status dan pantau aktivitas UMKM</p>
                    </div>

                    <div v-if="!umkms || umkms.length === 0" class="text-center py-12">
                        <div class="w-20 h-20 bg-gradient-to-br from-red-100 to-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Belum ada UMKM terdaftar</h4>
                        <p class="text-gray-600">Sistem siap menerima pendaftaran UMKM baru</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UMKM</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="umkm in umkms" :key="umkm.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img v-if="umkm.gambar" :src="`/storage/${umkm.gambar}`" :alt="umkm.nama_umkm" class="h-10 w-10 rounded-full object-cover">
                                                <div v-else class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center">
                                                    <span class="text-white font-semibold">{{ umkm.nama_umkm.charAt(0) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ umkm.nama_umkm }}</div>
                                                <div class="text-sm text-gray-500">{{ umkm.alamat }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ umkm.user?.name }}</div>
                                        <div class="text-sm text-gray-500">{{ umkm.user?.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ umkm.kategori }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ umkm.no_telepon }}</div>
                                        <div class="text-sm text-gray-500">{{ umkm.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="umkm.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ umkm.status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button
                                            @click="toggleStatus(umkm)"
                                            @click.ctrl="directToggle(umkm)"
                                            :class="umkm.status === 'active' ? 'text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100' : 'text-green-600 hover:text-green-900 bg-green-50 hover:bg-green-100'"
                                            class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 border"
                                            :title="umkm.status === 'active' ? 'Klik untuk nonaktifkan UMKM' : 'Klik untuk aktifkan UMKM'"
                                        >
                                            <svg v-if="umkm.status === 'active'" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <svg v-else class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ umkm.status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                        
                                        <small class="block text-gray-400 text-xs mt-1">
                                            
                                        </small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Confirmation -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="cancelToggle"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 18.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Konfirmasi Perubahan Status
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500" v-if="selectedUmkm">
                                        Apakah Anda yakin ingin 
                                        <span class="font-semibold">{{ selectedUmkm.status === 'active' ? 'menonaktifkan' : 'mengaktifkan' }}</span> 
                                        UMKM <strong>{{ selectedUmkm.nama_umkm }}</strong>?
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            @click="confirmToggle"
                            type="button"
                            :class="selectedUmkm?.status === 'active' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm transition-colors"
                        >
                            {{ selectedUmkm?.status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                        <button
                            @click="cancelToggle"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>