<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    umkms: Array<{
        id: number;
        nama_umkm: string;
        deskripsi: string;
        kategori: string;
        alamat: string;
        no_telepon: string;
        email: string;
        gambar: string;
        user: {
            name: string;
        };
    }>;
}>();
</script>

<template>
    <Head title="Daftar UMKM" />

    <GuestLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header Section -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">🏪 Daftar UMKM</h1>
                                <p class="text-indigo-100">Jelajahi berbagai UMKM lokal terbaik di sekitar Anda</p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <Link 
                                    :href="route('home')"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-white/20 border border-white/30 rounded-xl font-semibold text-sm text-white tracking-wide hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-200"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Kembali ke Beranda
                                </Link>
                                <Link 
                                    :href="route('public.mapping')"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-indigo-600 rounded-xl font-semibold text-sm tracking-wide hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                    Lihat Peta
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Section -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div v-if="umkms.length === 0" class="text-center py-16">
                        <div class="mb-4">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada UMKM</h3>
                        <p class="text-gray-500 text-lg">Belum ada UMKM yang terdaftar di sistem.</p>
                    </div>

                    <div v-else class="p-8">
                        <!-- Stats Bar -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 mb-8">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-blue-800 font-semibold">{{ umkms.length }} UMKM</span>
                                    <span class="text-blue-600 ml-2">siap melayani Anda</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- UMKM Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <div
                                v-for="umkm in umkms"
                                :key="umkm.id"
                                class="group bg-white border border-gray-100 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl hover:scale-105 transition-all duration-300"
                            >
                                <!-- Image Section -->
                                <div class="relative overflow-hidden">
                                    <div v-if="umkm.gambar" class="h-56 overflow-hidden">
                                        <img 
                                            :src="`/storage/${umkm.gambar}`" 
                                            :alt="umkm.nama_umkm"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                        />
                                    </div>
                                    <div v-else class="h-56 bg-gradient-to-br from-blue-100 to-indigo-200 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <!-- Category Badge -->
                                    <div class="absolute top-4 right-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/90 text-indigo-700 backdrop-blur-sm shadow-lg">
                                            {{ umkm.kategori }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Content Section -->
                                <div class="p-6">
                                    <div class="mb-4">
                                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors duration-200">{{ umkm.nama_umkm }}</h3>
                                        <div class="flex items-center text-sm text-gray-600 mb-3">
                                            <svg class="w-4 h-4 mr-2 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            <span class="font-medium text-gray-700">{{ umkm.user.name }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Description -->
                                    <div v-if="umkm.deskripsi" class="mb-4">
                                        <p class="text-gray-600 text-sm line-clamp-3 leading-relaxed">{{ umkm.deskripsi }}</p>
                                    </div>

                                    <!-- Contact Information -->
                                    <div class="space-y-3">
                                        <div class="flex items-start">
                                            <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                                <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-600 leading-relaxed">{{ umkm.alamat }}</p>
                                        </div>

                                        <div v-if="umkm.no_telepon" class="flex items-center">
                                            <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center mr-3 flex-shrink-0">
                                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                            </div>
                                            <a :href="`tel:${umkm.no_telepon}`" class="text-sm text-green-600 hover:text-green-800 font-medium transition-colors duration-200">{{ umkm.no_telepon }}</a>
                                        </div>

                                        <div v-if="umkm.email" class="flex items-center">
                                            <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0">
                                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <a :href="`mailto:${umkm.email}`" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">{{ umkm.email }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.line-clamp-3 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
}

/* Hover effects */
@media (hover: hover) {
    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }
    
    .group:hover .group-hover\:text-indigo-600 {
        color: rgb(79 70 229);
    }
}

/* Mobile optimizations */
@media (max-width: 640px) {
    .group {
        transform: none !important;
    }
    
    .group:hover {
        transform: none !important;
    }
}
</style>