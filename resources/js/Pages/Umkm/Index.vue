<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const handleDelete = (e: Event, href: string) => {
    e.preventDefault();
    if (window.confirm('Apakah Anda yakin ingin menghapus UMKM ini?')) {
        router.delete(href);
    }
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
                                    class="group bg-white border border-gray-200 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:border-blue-300 transition-all duration-300 hover:-translate-y-1 cursor-pointer active:scale-[0.99] touch-manipulation"
                                >
                                    <div class="flex">
                                        <!-- Image Section -->
                                        <div class="w-32 h-32 flex-shrink-0">
                                            <div v-if="umkm.gambar" class="h-full overflow-hidden">
                                                <img 
                                                    :src="`/storage/${umkm.gambar}`" 
                                                    :alt="umkm.nama_umkm"
                                                    class="w-full h-full object-cover"
                                                />
                                            </div>
                                            <div v-else class="relative h-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center overflow-hidden">
                                                <div class="absolute inset-0 bg-black bg-opacity-10"></div>
                                                <div class="text-center text-white z-10">
                                                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-1 backdrop-blur-sm">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                    </div>
                                                    <p class="text-xs opacity-90 font-medium">Foto</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Content Section -->
                                        <div class="flex-1 p-4">
                                            <div class="flex justify-between items-start mb-2">
                                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors duration-200 leading-tight">{{ umkm.nama_umkm }}</h3>
                                                <span 
                                                    :class="{
                                                        'bg-green-100 text-green-800 border-green-200': umkm.status === 'active',
                                                        'bg-red-100 text-red-800 border-red-200': umkm.status === 'inactive',
                                                        'bg-yellow-100 text-yellow-800 border-yellow-200': umkm.status === 'pending'
                                                    }"
                                                    class="px-2 py-1 text-xs font-semibold rounded-full border backdrop-blur-sm"
                                                >
                                                    {{ umkm.status === 'active' ? '✓ Aktif' : umkm.status === 'inactive' ? '✗ Nonaktif' : '🕐 Pending' }}
                                                </span>
                                            </div>
                                            
                                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                </svg>
                                                <span class="font-medium">{{ umkm.kategori || 'Kategori belum diisi' }}</span>
                                            </div>
                                            
                                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="truncate">{{ umkm.alamat || 'Alamat belum diisi' }}</span>
                                            </div>

                                            <!-- Contact Info in horizontal layout -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                    <span>{{ umkm.no_telepon || 'Telepon belum diisi' }}</span>
                                                </div>

                                                <!-- Action buttons in horizontal layout -->
                                                <div class="flex space-x-2" @click.stop>
                                                    <Link
                                                        :href="route('umkm.show', umkm.id)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200"
                                                    >
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
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