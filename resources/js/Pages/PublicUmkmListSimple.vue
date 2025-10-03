<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Interface for user
interface User {
    id: number;
    name: string;
    email: string;
    role?: string;
}

// Interface for pagination
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationData {
    data: any[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: PaginationLink[];
    prev_page_url: string | null;
    next_page_url: string | null;
}

// Props with pagination data and auth
const props = defineProps<{
    umkms: PaginationData;
    auth?: {
        user: User | null;
    };
}>();

// Search functionality
const searchQuery = ref('');

// View toggle functionality
const isGridView = ref(true);

// Smart navigation - determine home route based on auth status
const homeRoute = computed(() => {
    // If user is logged in, go to dashboard
    if (props.auth?.user) {
        return route('dashboard');
    }
    // If not logged in, go to public home
    return route('home');
});

// Determine home button text based on auth status
const homeButtonText = computed(() => {
    if (props.auth?.user) {
        return 'Kembali ke Dashboard';
    }
    return 'Kembali ke Beranda';
});

const filteredUmkms = computed(() => {
    if (!searchQuery.value) return props.umkms.data;
    
    return props.umkms.data.filter(umkm => 
        umkm.nama_umkm?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        umkm.kategori?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        umkm.alamat?.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Handle image error
const handleImageError = (event: Event) => {
    const target = event.target as HTMLImageElement;
    if (target) {
        target.style.display = 'none';
    }
};

// Pagination functions
const goToPage = (url: string | null) => {
    if (url) {
        router.get(url, {}, {
            preserveState: true,
            preserveScroll: true
        });
    }
};

const isSearching = computed(() => {
    return searchQuery.value.trim() !== '';
});
</script>

<template>
    <Head title="Daftar UMKM" />
    
    <GuestLayout>
        <!-- Background sesuai tema homepage -->
        <div class="min-h-screen" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <!-- Hero Section dengan styling yang lebih sesuai -->
            <div class="text-white relative overflow-hidden">
                <!-- Background pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 20px 20px;"></div>
                </div>
                
                <div class="max-w-7xl mx-auto px-4 py-20 relative">
                    <div class="text-center">
                        <h1 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">
                            Daftar UMKM
                        </h1>
                        <p class="text-xl text-white text-opacity-90 mb-10 max-w-3xl mx-auto leading-relaxed">
                            Platform digital terdepan untuk mendukung perkembangan dan digitalisasi 
                            <span class="text-yellow-300 font-semibold">Usaha Mikro, Kecil, dan Menengah</span> di Indonesia
                        </p>
                        
                        <!-- Navigation Buttons dengan styling card homepage -->
                        <div class="flex flex-col sm:flex-row gap-6 justify-center">
                            <Link 
                                :href="homeRoute" 
                                class="group relative overflow-hidden bg-white bg-opacity-10 backdrop-blur-md border border-white border-opacity-20 rounded-2xl p-4 hover:bg-opacity-20 transition-all duration-300 shadow-xl"
                            >
                                <div class="flex items-center justify-center space-x-3">
                                    <div class="w-12 h-12 bg-blue-500 bg-opacity-20 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    </div>
                                    <span class="text-white font-semibold">{{ homeButtonText }}</span>
                                </div>
                            </Link>
                            
                            <Link 
                                href="/mapping" 
                                class="group relative overflow-hidden bg-green-500 bg-opacity-20 backdrop-blur-md border border-green-400 border-opacity-30 rounded-2xl p-4 hover:bg-opacity-30 transition-all duration-300 shadow-xl"
                            >
                                <div class="flex items-center justify-center space-x-3">
                                    <div class="w-12 h-12 bg-green-500 bg-opacity-30 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-white font-semibold">Peta UMKM</span>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto px-4 pb-12 -mt-8 relative">
                
                <!-- Control Panel -->
                <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-3xl border border-white border-opacity-20 shadow-2xl p-4 sm:p-6 mb-8">
                    <div class="space-y-4">
                        <!-- Search Bar -->
                        <div class="relative w-full">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari UMKM, kategori, atau lokasi..."
                                class="w-full pl-12 pr-4 py-3 sm:py-4 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-2xl focus:ring-2 focus:ring-white focus:ring-opacity-50 focus:border-white focus:border-opacity-50 transition-all text-white placeholder-white placeholder-opacity-70 text-sm sm:text-base"
                            />
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-4 h-4 sm:w-5 sm:h-5 text-white text-opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        <!-- View Toggle -->
                        <div class="flex justify-center">
                            <div class="flex bg-white bg-opacity-10 backdrop-blur-sm rounded-2xl p-1 border border-white border-opacity-20">
                                <button
                                    @click="isGridView = true"
                                    :class="[
                                        'flex items-center justify-center px-4 py-2 sm:py-3 rounded-xl transition-all duration-200',
                                        isGridView 
                                            ? 'bg-white bg-opacity-20 text-white shadow-lg' 
                                            : 'text-white text-opacity-70 hover:text-opacity-100'
                                    ]"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                    </svg>
                                </button>
                                <button
                                    @click="isGridView = false"
                                    :class="[
                                        'flex items-center justify-center px-4 py-2 sm:py-3 rounded-xl transition-all duration-200',
                                        !isGridView 
                                            ? 'bg-white bg-opacity-20 text-white shadow-lg' 
                                            : 'text-white text-opacity-70 hover:text-opacity-100'
                                    ]"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Search Results Info -->
                    <div v-if="searchQuery" class="mt-4 text-sm text-white text-opacity-80">
                        Menampilkan {{ isSearching ? filteredUmkms.length : umkms.from + '-' + umkms.to }} dari {{ umkms.total }} UMKM
                    </div>
                </div>
                
                <!-- Content dengan Grid dan List View -->
                <div v-if="filteredUmkms && filteredUmkms.length > 0">
                    <!-- Grid View -->
                    <div v-if="isGridView" class="grid gap-4 sm:gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
                        <div 
                            v-for="umkm in filteredUmkms" 
                            :key="umkm.id" 
                            class="group relative overflow-hidden bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl sm:rounded-3xl border border-white border-opacity-20 shadow-2xl hover:bg-opacity-20 transition-all duration-300"
                        >
                            <!-- Card Header -->
                            <div class="relative h-32 sm:h-40 overflow-hidden">
                                <!-- Background Image or Gradient -->
                                <div v-if="umkm.gambar" class="absolute inset-0">
                                    <img 
                                        :src="`/storage/${umkm.gambar}`" 
                                        :alt="umkm.nama_umkm"
                                        class="w-full h-full object-cover"
                                        @error="handleImageError"
                                    />
                                    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                                </div>
                                <div v-else class="absolute inset-0 bg-gradient-to-br from-purple-400 via-pink-500 to-red-500">
                                    <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                                </div>
                                
                                <div class="absolute top-3 left-3 sm:top-4 sm:left-4">
                                    <span class="bg-white bg-opacity-90 backdrop-blur-sm px-2 py-1 sm:px-3 sm:py-1 rounded-full text-xs font-semibold text-gray-800">
                                        {{ umkm.kategori || 'Kategori' }}
                                    </span>
                                </div>
                                <div class="absolute bottom-3 right-3 sm:bottom-4 sm:right-4">
                                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Content -->
                            <div class="p-4 sm:p-6">
                                <h3 class="font-bold text-lg sm:text-xl text-white mb-2 sm:mb-3 group-hover:text-yellow-300 transition-colors line-clamp-2">
                                    {{ umkm.nama_umkm || 'Nama UMKM' }}
                                </h3>
                                
                                <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                                    <div class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-white text-opacity-60 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-xs sm:text-sm text-white text-opacity-80 line-clamp-2">{{ umkm.alamat || 'Alamat tidak tersedia' }}</span>
                                    </div>

                                    <div v-if="umkm.deskripsi" class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-white text-opacity-60 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xs sm:text-sm text-white text-opacity-80 line-clamp-3">{{ umkm.deskripsi }}</span>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <Link 
                                    :href="route('public.umkm.show', umkm.id)"
                                    class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-2.5 sm:py-3 px-4 rounded-xl sm:rounded-2xl font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-300 flex items-center justify-center gap-2 shadow-lg text-sm sm:text-base"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Lihat Detail
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- List View -->
                    <div v-else class="space-y-4">
                        <div 
                            v-for="umkm in filteredUmkms" 
                            :key="umkm.id" 
                            class="group bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl border border-white border-opacity-20 shadow-xl hover:bg-opacity-20 transition-all duration-300"
                        >
                            <div class="flex flex-col lg:flex-row">
                                <!-- Left side - Image -->
                                <div class="lg:w-48 h-32 lg:h-auto relative overflow-hidden lg:rounded-l-2xl rounded-t-2xl lg:rounded-tr-none">
                                    <!-- Background Image or Gradient -->
                                    <div v-if="umkm.gambar" class="absolute inset-0">
                                        <img 
                                            :src="`/storage/${umkm.gambar}`" 
                                            :alt="umkm.nama_umkm"
                                            class="w-full h-full object-cover"
                                            @error="handleImageError"
                                        />
                                        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                                    </div>
                                    <div v-else class="absolute inset-0 bg-gradient-to-br from-purple-400 via-pink-500 to-red-500">
                                        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                                        <!-- Default Icon -->
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-white text-opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div class="absolute top-3 left-3">
                                        <span class="bg-white bg-opacity-90 backdrop-blur-sm px-2 py-1 rounded-lg text-xs font-semibold text-gray-800">
                                            {{ umkm.kategori || 'Kategori' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Right side - Content -->
                                <div class="flex-1 p-6">
                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between h-full">
                                        <div class="flex-1 lg:pr-6">
                                            <h3 class="font-bold text-xl text-white mb-2 group-hover:text-yellow-300 transition-colors">
                                                {{ umkm.nama_umkm || 'Nama UMKM' }}
                                            </h3>
                                            
                                            <div class="flex flex-wrap gap-4 mb-3">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-white text-opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    <span class="text-sm text-white text-opacity-80">{{ umkm.alamat || 'Alamat tidak tersedia' }}</span>
                                                </div>
                                                
                                                <div v-if="umkm.no_telepon" class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-white text-opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                    </svg>
                                                    <span class="text-sm text-white text-opacity-80">{{ umkm.no_telepon }}</span>
                                                </div>
                                            </div>

                                            <p v-if="umkm.deskripsi" class="text-sm text-white text-opacity-80 line-clamp-2">
                                                {{ umkm.deskripsi }}
                                            </p>
                                        </div>

                                        <!-- Action Button -->
                                        <div class="mt-4 lg:mt-0 lg:flex-shrink-0">
                                            <Link 
                                                :href="route('public.umkm.show', umkm.id)"
                                                class="w-full lg:w-auto bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-6 rounded-xl font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-300 flex items-center justify-center gap-2 shadow-lg"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
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
                
                <!-- Empty State -->
                <div v-else class="text-center py-16">
                    <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-3xl border border-white border-opacity-20 shadow-2xl p-12 max-w-md mx-auto">
                        <div class="bg-white bg-opacity-20 backdrop-blur-sm w-24 h-24 rounded-3xl flex items-center justify-center mx-auto mb-6 border border-white border-opacity-30">
                            <svg class="w-12 h-12 text-white text-opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">
                            {{ searchQuery ? 'Tidak ada hasil pencarian' : 'Belum ada UMKM yang terdaftar' }}
                        </h3>
                        <p class="text-white text-opacity-80 mb-6">
                            {{ searchQuery ? `Tidak ditemukan UMKM dengan kata kunci "${searchQuery}"` : 'Saat ini belum ada UMKM yang mendaftar di sistem kami.' }}
                        </p>
                        <button 
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-3 rounded-2xl font-semibold hover:from-blue-600 hover:to-purple-700 transition-all duration-300 shadow-lg"
                        >
                            Hapus Pencarian
                        </button>
                    </div>
                </div>
                
                <!-- Pagination Component -->
                <div v-if="!isSearching && umkms.total > umkms.per_page" class="mt-12">
                    <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl border border-white border-opacity-20 shadow-xl p-6">
                        <div class="flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
                            <!-- Pagination Info -->
                            <div class="text-white text-opacity-80 text-sm">
                                Menampilkan {{ umkms.from }}-{{ umkms.to }} dari {{ umkms.total }} UMKM
                            </div>
                            
                            <!-- Pagination Links -->
                            <div class="flex items-center space-x-2">
                                <!-- Previous Button -->
                                <button
                                    @click="goToPage(umkms.prev_page_url)"
                                    :disabled="!umkms.prev_page_url"
                                    :class="[
                                        'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                        umkms.prev_page_url
                                            ? 'bg-white bg-opacity-20 text-white hover:bg-opacity-30 border border-white border-opacity-30'
                                            : 'bg-gray-500 bg-opacity-20 text-gray-400 cursor-not-allowed border border-gray-500 border-opacity-30'
                                    ]"
                                >
                                    <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    Sebelumnya
                                </button>
                                
                                <!-- Page Numbers -->
                                <div class="hidden sm:flex items-center space-x-1">
                                    <template v-for="(link, index) in umkms.links" :key="index">
                                        <button
                                            v-if="link.label !== 'Previous' && link.label !== 'Next' && link.url"
                                            @click="goToPage(link.url)"
                                            :class="[
                                                'min-w-[40px] h-10 rounded-lg text-sm font-medium transition-all duration-200',
                                                link.active
                                                    ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white shadow-lg'
                                                    : 'bg-white bg-opacity-20 text-white hover:bg-opacity-30 border border-white border-opacity-30'
                                            ]"
                                            v-html="link.label"
                                        />
                                        <span
                                            v-else-if="link.label !== 'Previous' && link.label !== 'Next' && !link.url"
                                            class="min-w-[40px] h-10 flex items-center justify-center text-white text-opacity-60 text-sm"
                                            v-html="link.label"
                                        />
                                    </template>
                                </div>
                                
                                <!-- Mobile Page Info -->
                                <div class="sm:hidden bg-white bg-opacity-20 text-white px-3 py-2 rounded-lg text-sm border border-white border-opacity-30">
                                    {{ umkms.current_page }} / {{ umkms.last_page }}
                                </div>
                                
                                <!-- Next Button -->
                                <button
                                    @click="goToPage(umkms.next_page_url)"
                                    :disabled="!umkms.next_page_url"
                                    :class="[
                                        'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200',
                                        umkms.next_page_url
                                            ? 'bg-white bg-opacity-20 text-white hover:bg-opacity-30 border border-white border-opacity-30'
                                            : 'bg-gray-500 bg-opacity-20 text-gray-400 cursor-not-allowed border border-gray-500 border-opacity-30'
                                    ]"
                                >
                                    Selanjutnya
                                    <svg class="w-4 h-4 ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>