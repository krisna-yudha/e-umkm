<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Modal state for delete confirmation
const showDeleteModal = ref(false);
const selectedUmkm = ref<{ href: string; name: string } | null>(null);
const isDeleting = ref(false);

const handleDelete = (e: Event, href: string, umkmName: string) => {
    e.preventDefault();
    selectedUmkm.value = { href, name: umkmName };
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (!selectedUmkm.value) return;
    
    isDeleting.value = true;
    router.delete(selectedUmkm.value.href, {
        onSuccess: () => {
            showDeleteModal.value = false;
            selectedUmkm.value = null;
        },
        onError: () => {
            alert('Gagal menghapus UMKM. Silakan coba lagi.');
        },
        onFinish: () => {
            isDeleting.value = false;
        }
    });
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    selectedUmkm.value = null;
};

const handleUmkmClick = (umkmId: number, event: Event) => {
    // Prevent click when interacting with buttons or links
    const target = event.target as HTMLElement;
    if (target.closest('button') || target.closest('a') || target.closest('[role="button"]')) {
        return;
    }
    
    // Navigate to UMKM detail page
    router.visit(route('umkm.show', umkmId));
};

const confirmToggleStatus = (e: Event, umkm: any) => {
    e.stopPropagation();
    const message = umkm.status === 'active' 
        ? 'Yakin ingin menonaktifkan UMKM ini?' 
        : 'Yakin ingin mengaktifkan UMKM ini?';
    
    if (confirm(message)) {
        router.patch(route('umkm.toggleStatus', umkm.id), {}, {
            preserveState: true,
            preserveScroll: true,
            onError: () => {
                alert('Gagal mengubah status UMKM. Silakan coba lagi.');
            }
        });
    }
};

const props = defineProps<{
    umkms: Array<{
        id: number;
        nama_umkm: string;
        deskripsi: string;
        kategori: string;
        alamat: string;
        no_telepon: string;
        email: string;
        latitude: number;
        longitude: number;
        gambar: string;
        status: string;
        whatsapp?: string;
        instagram?: string;
        facebook?: string;
        twitter?: string;
        website?: string;
        user: {
            name: string;
        };
    }>;
}>();
</script>

<template>
    <Head title="Kelola UMKM" />

    <AuthenticatedLayout>
        <template #header>
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 -mx-4 -mt-4 px-4 pt-4 pb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="font-bold text-2xl text-white leading-tight mb-2">
                            🏢 Kelola UMKM
                        </h2>
                        <p class="text-blue-100 text-sm">Kelola  UMKM Anda dengan mudah</p>
                    </div>
                    <Link
                        :href="route('umkm.create')"
                        class="inline-flex items-center px-6 py-3 bg-white text-blue-600 border border-transparent rounded-xl font-semibold text-sm hover:bg-blue-50 focus:bg-blue-50 active:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600 transition-all duration-200 shadow-lg hover:shadow-xl"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tambah UMKM
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-200">
                    <div class="p-8 text-gray-900">
                        <div v-if="umkms.length === 0" class="text-center py-16">
                            <div class="mb-8">
                                <div class="mx-auto w-24 h-24 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mb-6">
                                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">🏢 Belum ada UMKM</h3>
                            <p class="text-gray-600 mb-8 max-w-md mx-auto leading-relaxed">Anda belum mendaftarkan UMKM apapun. Mulai perjalanan bisnis Anda dengan menambahkan UMKM pertama!</p>
                            <Link
                                :href="route('umkm.create')"
                                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah UMKM Pertama
                            </Link>
                        </div>

                        <div v-else>
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Daftar UMKM Anda</h3>
                            </div>
                            <div class="space-y-6">
                                <div
                                    v-for="umkm in umkms"
                                    :key="umkm.id"
                                    @click="handleUmkmClick(umkm.id, $event)"
                                    class="group bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-lg hover:border-blue-300 transition-all duration-300 cursor-pointer"
                                >
                                    <div class="flex flex-col sm:flex-row sm:items-center p-4 sm:p-6">
                                        <!-- Image Section -->
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 mx-auto sm:mx-0 sm:mr-6 mb-4 sm:mb-0">
                                            <div v-if="umkm.gambar" class="h-full w-full overflow-hidden rounded-xl shadow-md">
                                                <img 
                                                    :src="`/storage/${umkm.gambar}`" 
                                                    :alt="umkm.nama_umkm"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                />
                                            </div>
                                            <div v-else class="relative h-full w-full bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Content Section -->
                                        <div class="flex-1 min-w-0 text-center sm:text-left">
                                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-3">
                                                <div class="mb-2 sm:mb-0">
                                                    <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate mb-2 group-hover:text-blue-600 transition-colors duration-200">{{ umkm.nama_umkm }}</h3>
                                                    <div class="flex justify-center sm:justify-start">
                                                        <span 
                                                            :class="{
                                                                'bg-green-100 text-green-800 border-green-200': umkm.status === 'active',
                                                                'bg-red-100 text-red-800 border-red-200': umkm.status === 'inactive',
                                                                'bg-yellow-100 text-yellow-800 border-yellow-200': umkm.status === 'pending'
                                                            }"
                                                            class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full border"
                                                        >
                                                            <div :class="{
                                                                'bg-green-400': umkm.status === 'active',
                                                                'bg-red-400': umkm.status === 'inactive',
                                                                'bg-yellow-400': umkm.status === 'pending'
                                                            }" class="w-2 h-2 rounded-full mr-2"></div>
                                                            {{ umkm.status === 'active' ? 'Aktif' : umkm.status === 'inactive' ? 'Nonaktif' : 'Pending' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="flex flex-col space-y-2 mb-4 sm:mb-0">
                                                <div class="flex items-center justify-center sm:justify-start text-sm text-gray-600">
                                                    <div class="flex items-center bg-blue-50 px-3 py-1 rounded-lg">
                                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                        </svg>
                                                        <span class="font-medium text-blue-700">{{ umkm.kategori || 'Belum dikategorikan' }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center justify-center sm:justify-start text-sm text-gray-600">
                                                    <svg class="w-4 h-4 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate text-center sm:text-left">{{ umkm.alamat || 'Alamat belum diisi' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex-shrink-0 flex justify-center sm:ml-6 mt-4 sm:mt-0" @click.stop>
                                            <div class="flex items-center space-x-2">
                                                <!-- Edit Button -->
                                                <Link
                                                    :href="route('umkm.edit', umkm.id)"
                                                    class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg"
                                                    title="Edit UMKM"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </Link>

                                                <!-- Toggle Status Button -->
                                                <button
                                                    @click="confirmToggleStatus($event, umkm)"
                                                    :class="umkm.status === 'active' ? 'bg-orange-500 hover:bg-orange-600 focus:ring-orange-500' : 'bg-green-500 hover:bg-green-600 focus:ring-green-500'"
                                                    class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg"
                                                    :title="umkm.status === 'active' ? 'Nonaktifkan UMKM' : 'Aktifkan UMKM'"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path v-if="umkm.status === 'active'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364L18.364 5.636"/>
                                                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>

                                                <!-- Delete Button -->
                                                <button
                                                    @click="handleDelete($event, route('umkm.destroy', umkm.id), umkm.nama_umkm)"
                                                    class="inline-flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg"
                                                    title="Hapus UMKM"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="cancelDelete"></div>

                <!-- Modal content -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Hapus UMKM
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Apakah Anda yakin ingin menghapus UMKM <strong>{{ selectedUmkm?.name }}</strong>? 
                                        Tindakan ini tidak dapat dibatalkan dan semua data terkait akan hilang.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button 
                            type="button" 
                            @click="confirmDelete"
                            :disabled="isDeleting"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg v-if="isDeleting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ isDeleting ? 'Menghapus...' : 'Hapus' }}
                        </button>
                        <button 
                            type="button" 
                            @click="cancelDelete"
                            :disabled="isDeleting"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
}
</style>