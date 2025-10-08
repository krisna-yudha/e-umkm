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
                            <div class="space-y-4">
                                <div
                                    v-for="umkm in umkms"
                                    :key="umkm.id"
                                    @click="handleUmkmClick(umkm.id, $event)"
                                    class="group bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden hover:shadow-md hover:border-gray-300 transition-all duration-200 cursor-pointer"
                                >
                                    <div class="flex items-center p-4">
                                        <!-- Image Section -->
                                        <div class="w-16 h-16 flex-shrink-0 mr-4">
                                            <div v-if="umkm.gambar" class="h-full w-full overflow-hidden rounded-lg">
                                                <img 
                                                    :src="`/storage/${umkm.gambar}`" 
                                                    :alt="umkm.nama_umkm"
                                                    class="w-full h-full object-cover"
                                                />
                                            </div>
                                            <div v-else class="relative h-full w-full bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Content Section -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between mb-1">
                                                <h3 class="text-base font-semibold text-gray-900 truncate">{{ umkm.nama_umkm }}</h3>
                                                <span 
                                                    :class="{
                                                        'bg-green-100 text-green-700': umkm.status === 'active',
                                                        'bg-red-100 text-red-700': umkm.status === 'inactive',
                                                        'bg-yellow-100 text-yellow-700': umkm.status === 'pending'
                                                    }"
                                                    class="px-2 py-1 text-xs font-medium rounded-full flex-shrink-0 ml-2"
                                                >
                                                    {{ umkm.status === 'active' ? 'Aktif' : umkm.status === 'inactive' ? 'Nonaktif' : 'Pending' }}
                                                </span>
                                            </div>
                                            
                                            <div class="flex items-center text-sm text-gray-600 mb-1">
                                                <svg class="w-4 h-4 mr-1 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                </svg>
                                                <span class="truncate">{{ umkm.kategori || 'Kategori belum diisi' }}</span>
                                            </div>
                                            
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-1 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="truncate">{{ umkm.alamat || 'Alamat belum diisi' }}</span>
                                            </div>
                                        </div>

                                        <!-- Action Button -->
                                        <div class="flex-shrink-0 ml-4" @click.stop>
                                            <Link
                                                :href="route('umkm.show', umkm.id)"
                                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                                            >
                                                Lihat Detail
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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