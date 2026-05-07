<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    role?: string;
    user_type?: string;
}

const props = defineProps<{
    auth?: {
        user: User | null;
    };
}>();

// Smart navigation for login/dashboard button
const loginRoute = computed(() => {
    if (props.auth?.user) {
        return route('dashboard');
    }
    return route('login');
});

const loginButtonText = computed(() => {
    if (props.auth?.user) {
        return 'Dashboard';
    }
    return 'Login sebagai Penjual';
});

// Scroll animation
const visibleElements = ref<Set<string>>(new Set());

onMounted(() => {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    visibleElements.value.add(entry.target.id);
                    observer.unobserve(entry.target);
                }
            });
        },
        {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px',
        }
    );

    // Observe all elements with animation class
    document.querySelectorAll('.scroll-animate').forEach((el) => {
        observer.observe(el);
    });
});

const isVisible = (id: string) => visibleElements.value.has(id);
</script>

<template>
    <Head title="Sistem UMKM" />

    <GuestLayout>
        <!-- Hero Section -->
        <section class="relative min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-20"></div>
                <div class="absolute top-10 left-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
                <div class="absolute top-0 right-0 w-72 h-72 bg-yellow-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
            </div>
            
            <div class="relative z-10 flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8 py-8 sm:py-0">
                <div class="max-w-7xl mx-auto text-center w-full">
                    <!-- Header Section -->
                    <div class="mb-8 sm:mb-12 mt-4 sm:mt-8">
                        <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-4 sm:mb-6 tracking-tight">
                            <span class="block">Selamat Datang di</span>
                            <span class="block bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                                Sistem UMKM
                            </span>
                        </h1>
                        <p class="text-sm sm:text-lg md:text-xl text-gray-200 mb-6 sm:mb-8 max-w-3xl mx-auto leading-relaxed px-2 sm:px-0">
                            Platform digital terdepan untuk mendukung perkembangan dan digitalisasi 
                            <span class="font-semibold text-yellow-400">Usaha Mikro, Kecil, dan Menengah</span> 
                            di Indonesia
                        </p>
                    </div>

                    <!-- Two Main Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 max-w-2xl mx-auto mb-6 sm:mb-8">
                        <!-- Card Penjual -->
                        <Link 
                            :href="loginRoute"
                            class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-5 sm:p-6 hover:bg-white/20 transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer"
                        >
                            <div class="absolute inset-0 bg-gradient-to-br from-red-500/20 to-pink-500/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10 text-center">
                                <div class="h-14 w-14 sm:h-16 sm:w-16 bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center rounded-2xl mx-auto mb-3 sm:mb-4 shadow-lg group-hover:shadow-red-500/25 transition-shadow duration-300">
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <h2 class="text-lg sm:text-xl font-bold text-white">UMKM</h2>
                                <p class="text-xs sm:text-sm text-gray-200 mt-2">Login untuk mengelola bisnis Anda</p>
                            </div>
                        </Link>

                        <!-- Card Pengunjung -->
                        <Link 
                            :href="route('public.umkm.list')"
                            class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-5 sm:p-6 hover:bg-white/20 transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer"
                        >
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10 text-center">
                                <div class="h-14 w-14 sm:h-16 sm:w-16 bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center rounded-2xl mx-auto mb-3 sm:mb-4 shadow-lg group-hover:shadow-blue-500/25 transition-shadow duration-300">
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <h2 class="text-lg sm:text-xl font-bold text-white">Pengunjung</h2>
                                <p class="text-xs sm:text-sm text-gray-200 mt-2">Jelajahi dan temukan UMKM favorit</p>
                            </div>
                        </Link>
                    </div>

                    <!-- Scroll Indicator -->
                    <div class="flex justify-center gap-2 mt-4 sm:mt-6">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white/60 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-12 sm:py-16 md:py-20 lg:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
                <div class="text-center mb-10 sm:mb-12 md:mb-16 scroll-animate" id="features-title" :class="{ 'animate-in': isVisible('features-title') }">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-3 sm:mb-4">
                        Fitur Utama Platform
                    </h2>
                    <p class="text-sm sm:text-base md:text-lg text-gray-600 max-w-3xl mx-auto px-2 sm:px-0">
                        Kami menyediakan solusi lengkap untuk mengembangkan dan memasarkan bisnis UMKM Anda secara digital
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
                    <!-- Feature Card 1 -->
                    <div class="scroll-animate" id="feature-1" :class="{ 'animate-in': isVisible('feature-1') }" style="--animation-delay: 0ms" >
                        <div class="group relative bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-4 sm:p-6 md:p-8 hover:shadow-lg transition-all duration-300 hover:scale-105">
                            <div class="h-10 w-10 sm:h-12 sm:w-12 bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center rounded-lg mb-3 sm:mb-4">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-900 mb-2">Manajemen UMKM</h3>
                            <p class="text-gray-700 text-xs sm:text-sm md:text-base">Kelola profil bisnis, produk, dan layanan dengan mudah melalui dashboard yang intuitif</p>
                        </div>
                    </div>

                    <!-- Feature Card 2 -->
                    <div class="scroll-animate" id="feature-2" :class="{ 'animate-in': isVisible('feature-2') }" style="--animation-delay: 100ms" >
                        <div class="group relative bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-4 sm:p-6 md:p-8 hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <div class="h-12 w-12 bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center rounded-lg mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">Menu & Produk</h3>
                        <p class="text-gray-700 text-sm md:text-base">Tampilkan menu atau produk Anda dengan foto berkualitas dan deskripsi lengkap</p>
                        </div>
                    </div>

                    <!-- Feature Card 3 -->
                    <div class="scroll-animate" id="feature-3" :class="{ 'animate-in': isVisible('feature-3') }" style="--animation-delay: 200ms" >
                        <div class="group relative bg-gradient-to-br from-pink-50 to-pink-100 rounded-2xl p-4 sm:p-6 md:p-8 hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <div class="h-12 w-12 bg-gradient-to-br from-pink-500 to-pink-600 flex items-center justify-center rounded-lg mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">Profil Terpercaya</h3>
                        <p class="text-gray-700 text-sm md:text-base">Bangun reputasi dengan verifikasi data dan rating dari pelanggan</p>
                        </div>
                    </div>

                    <!-- Feature Card 4 -->
                    <div class="scroll-animate" id="feature-4" :class="{ 'animate-in': isVisible('feature-4') }" style="--animation-delay: 300ms" >
                        <div class="group relative bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-4 sm:p-6 md:p-8 hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <div class="h-12 w-12 bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center rounded-lg mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">Lokasi Bisnis</h3>
                        <p class="text-gray-700 text-sm md:text-base">Tampilkan lokasi UMKM Anda di peta untuk mudah ditemukan pelanggan</p>
                        </div>
                    </div>

                    <!-- Feature Card 5 -->
                    <div class="scroll-animate" id="feature-5" :class="{ 'animate-in': isVisible('feature-5') }" style="--animation-delay: 400ms" >
                        <div class="group relative bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-4 sm:p-6 md:p-8 hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <div class="h-12 w-12 bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center rounded-lg mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4.243 4.243a4 4 0 105.656 5.656l4.243-4.243"/>
                            </svg>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">Media Sosial</h3>
                        <p class="text-gray-700 text-sm md:text-base">Hubungkan akun media sosial untuk jangkauan yang lebih luas</p>
                        </div>
                    </div>

                    <!-- Feature Card 6 -->
                    <div class="scroll-animate" id="feature-6" :class="{ 'animate-in': isVisible('feature-6') }" style="--animation-delay: 500ms" >
                        <div class="group relative bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl p-4 sm:p-6 md:p-8 hover:shadow-lg transition-all duration-300 hover:scale-105">
                        <div class="h-12 w-12 bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center rounded-lg mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-2">Jangkauan Luas</h3>
                        <p class="text-gray-700 text-sm md:text-base">Tawarkan produk ke jutaan pengunjung platform setiap harinya</p>                        </div>                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="py-12 sm:py-16 md:py-20 lg:py-24 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
                <div class="text-center mb-10 sm:mb-12 md:mb-16 scroll-animate" id="how-works-title" :class="{ 'animate-in': isVisible('how-works-title') }">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-3 sm:mb-4">
                        Cara Menggunakan Platform
                    </h2>
                    <p class="text-sm sm:text-base md:text-lg text-gray-600 max-w-3xl mx-auto px-2 sm:px-0">
                        Tiga langkah mudah untuk memulai perjalanan digital UMKM Anda
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                    <!-- Step 1 -->
                    <div class="relative scroll-animate" id="step-1" :class="{ 'animate-in': isVisible('step-1') }" style="--animation-delay: 0ms">
                        <div class="flex flex-col items-center">
                            <div class="relative z-10 mb-4 sm:mb-6">
                                <div class="h-14 w-14 sm:h-16 sm:w-16 md:h-20 md:w-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-lg sm:text-xl md:text-2xl font-bold shadow-lg">
                                    1
                                </div>
                            </div>
                            <div class="bg-white rounded-2xl p-4 sm:p-6 md:p-8 shadow-md w-full">
                                <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-900 mb-2 sm:mb-3 text-center">Daftar Akun</h3>
                                <p class="text-gray-700 text-xs sm:text-sm md:text-base text-center">
                                    Buat akun UMKM dengan mudah menggunakan email Anda dan verifikasi identitas bisnis
                                </p>
                            </div>
                        </div>
                        <!-- Connector line for desktop -->
                        <div class="hidden md:block absolute top-20 -right-[calc(50%+20px)] w-[calc(50%+20px)] h-1 bg-gradient-to-r from-blue-500 to-transparent"></div>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative scroll-animate" id="step-2" :class="{ 'animate-in': isVisible('step-2') }" style="--animation-delay: 100ms">
                        <div class="flex flex-col items-center">
                            <div class="relative z-10 mb-4 sm:mb-6">
                                <div class="h-14 w-14 sm:h-16 sm:w-16 md:h-20 md:w-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white text-lg sm:text-xl md:text-2xl font-bold shadow-lg">
                                    2
                                </div>
                            </div>
                            <div class="bg-white rounded-2xl p-4 sm:p-6 md:p-8 shadow-md w-full">
                                <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-900 mb-2 sm:mb-3 text-center">Kelola Profil</h3>
                                <p class="text-gray-700 text-xs sm:text-sm md:text-base text-center">
                                    Lengkapi profil UMKM Anda dengan informasi lengkap dan foto berkualitas tinggi
                                </p>
                            </div>
                        </div>
                        <!-- Connector line for desktop -->
                        <div class="hidden md:block absolute top-20 -right-[calc(50%+20px)] w-[calc(50%+20px)] h-1 bg-gradient-to-r from-purple-500 to-transparent"></div>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative scroll-animate" id="step-3" :class="{ 'animate-in': isVisible('step-3') }" style="--animation-delay: 200ms">
                        <div class="flex flex-col items-center">
                            <div class="relative z-10 mb-4 sm:mb-6">
                                <div class="h-14 w-14 sm:h-16 sm:w-16 md:h-20 md:w-20 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center text-white text-lg sm:text-xl md:text-2xl font-bold shadow-lg">
                                    3
                                </div>
                            </div>
                            <div class="bg-white rounded-2xl p-4 sm:p-6 md:p-8 shadow-md w-full">
                                <h3 class="text-base sm:text-lg md:text-xl font-bold text-gray-900 mb-2 sm:mb-3 text-center">Mulai Berjualan</h3>
                                <p class="text-gray-700 text-xs sm:text-sm md:text-base text-center">
                                    Publikasikan produk atau menu Anda dan mulai terima pelanggan dari seluruh Indonesia
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="py-12 sm:py-16 md:py-20 lg:py-24 bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
                <div class="text-center mb-8 sm:mb-10 md:mb-12 scroll-animate" id="benefits-title" :class="{ 'animate-in': isVisible('benefits-title') }">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-3 sm:mb-4">
                        Mengapa Memilih Sistem UMKM?
                    </h2>
                    <p class="text-xs sm:text-sm md:text-base lg:text-lg text-gray-200 max-w-3xl mx-auto px-2 sm:px-0">
                        Platform yang dirancang khusus untuk mendukung pertumbuhan bisnis lokal Indonesia
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6 md:gap-8">
                    <!-- Benefit Card 1 -->
                    <div class="scroll-animate bg-white/10 backdrop-blur-lg rounded-2xl p-4 sm:p-6 md:p-8 border border-white/20 hover:bg-white/20 transition-all duration-300" id="benefit-1" :class="{ 'animate-in': isVisible('benefit-1') }" style="--animation-delay: 0ms">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg md:text-xl font-bold text-white mb-1 sm:mb-2">100% Gratis</h3>
                                <p class="text-gray-200 text-xs sm:text-sm md:text-base">Tidak ada biaya pendaftaran, biaya langganan, atau biaya tersembunyi. Mulai gratis hari ini!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Benefit Card 2 -->
                    <div class="scroll-animate bg-white/10 backdrop-blur-lg rounded-2xl p-4 sm:p-6 md:p-8 border border-white/20 hover:bg-white/20 transition-all duration-300" id="benefit-2" :class="{ 'animate-in': isVisible('benefit-2') }" style="--animation-delay: 100ms">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg md:text-xl font-bold text-white mb-1 sm:mb-2">Aman & Terpercaya</h3>
                                <p class="text-gray-200 text-xs sm:text-sm md:text-base">Data Anda dilindungi dengan enkripsi tingkat enterprise dan sistem keamanan berlapis</p>
                            </div>
                        </div>
                    </div>

                    <!-- Benefit Card 3 -->
                    <div class="scroll-animate bg-white/10 backdrop-blur-lg rounded-2xl p-4 sm:p-6 md:p-8 border border-white/20 hover:bg-white/20 transition-all duration-300" id="benefit-3" :class="{ 'animate-in': isVisible('benefit-3') }" style="--animation-delay: 200ms">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg md:text-xl font-bold text-white mb-1 sm:mb-2">Mudah Digunakan</h3>
                                <p class="text-gray-200 text-xs sm:text-sm md:text-base">Interface yang intuitif dan user-friendly. Tidak perlu keahlian teknis khusus untuk memulai</p>
                            </div>
                        </div>
                    </div>

                    <!-- Benefit Card 4 -->
                    <div class="scroll-animate bg-white/10 backdrop-blur-lg rounded-2xl p-4 sm:p-6 md:p-8 border border-white/20 hover:bg-white/20 transition-all duration-300" id="benefit-4" :class="{ 'animate-in': isVisible('benefit-4') }" style="--animation-delay: 300ms">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="h-12 w-12 sm:h-14 sm:w-14 md:h-16 md:w-16 bg-gradient-to-br from-pink-400 to-rose-500 rounded-xl flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg md:text-xl font-bold text-white mb-1 sm:mb-2">Dukungan Penuh</h3>
                                <p class="text-gray-200 text-xs sm:text-sm md:text-base">Tim support siap membantu 24/7 dan menyediakan panduan lengkap untuk kesuksesan Anda</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-12 sm:py-16 md:py-20 lg:py-24 bg-white">
            <div class="max-w-4xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 text-center scroll-animate" id="cta-section" :class="{ 'animate-in': isVisible('cta-section') }">
                <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4 sm:mb-6">
                    Siap Mengembangkan Bisnis Anda?
                </h2>
                <p class="text-sm sm:text-base md:text-lg text-gray-600 mb-6 sm:mb-8 md:mb-10 max-w-2xl mx-auto px-2 sm:px-0">
                    Bergabunglah dengan ribuan UMKM yang telah merasakan manfaat platform digital kami
                </p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                    <Link 
                        :href="loginRoute"
                        class="inline-block bg-gradient-to-r from-red-500 to-pink-600 text-white font-bold py-2 sm:py-3 px-6 sm:px-8 rounded-lg hover:shadow-lg transition-all duration-300 transform hover:scale-105 text-sm sm:text-base"
                    >
                        Jadilah UMKM Mitra Kami
                    </Link>
                    <Link 
                        :href="route('public.umkm.list')"
                        class="inline-block bg-gray-200 text-gray-900 font-bold py-2 sm:py-3 px-6 sm:px-8 rounded-lg hover:bg-gray-300 transition-all duration-300 text-sm sm:text-base"
                    >
                        Jelajahi UMKM
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-8 md:py-12 lg:py-16">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 sm:gap-8 mb-6 sm:mb-8">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4">Sistem UMKM</h3>
                        <p class="text-gray-400 text-xs sm:text-sm">Platform digital untuk mengembangkan dan memasarkan UMKM Indonesia</p>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4">Navigasi</h3>
                        <ul class="space-y-2 text-xs sm:text-sm text-gray-400">
                            <li><Link :href="route('public.umkm.list')" class="hover:text-white transition">Jelajahi UMKM</Link></li>
                            <li><Link :href="loginRoute" class="hover:text-white transition">Login UMKM</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold mb-3 sm:mb-4">Informasi</h3>
                        <ul class="space-y-2 text-xs sm:text-sm text-gray-400">
                            <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
                            <li><a href="#" class="hover:text-white transition">Kebijakan Privasi</a></li>
                            <li><a href="#" class="hover:text-white transition">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-gray-700 pt-6 sm:pt-8 text-center">
                    <p class="text-gray-400 text-xs sm:text-sm">
                        © 2026 Sistem UMKM. Semarang University Informatics Project.
                    </p>
                </div>
            </div>
        </footer>
    </GuestLayout>
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

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
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

/* Scroll Animation */
.scroll-animate {
  opacity: 0;
  transform: translateY(30px);
}

.scroll-animate.animate-in {
  animation: fadeInUp 0.6s ease-out forwards;
  animation-delay: var(--animation-delay, 0ms);
}

/* Smooth scroll behavior */
html {
  scroll-behavior: smooth;
}

/* Responsive improvements */
@media (max-width: 640px) {
  .animate-blob {
    width: 200px;
    height: 200px;
  }
}

@media (min-width: 768px) {
  .animate-blob {
    width: 288px;
    height: 288px;
  }
}

/* Enhanced hover effects for different screen sizes */
@media (hover: hover) {
  .group:hover {
    transform: translateY(-4px);
  }
}

/* Tablet and Desktop specific improvements */
@media (min-width: 768px) {
  section {
    scroll-behavior: smooth;
  }
}

/* Better spacing for different devices */
@media (max-width: 640px) {
  h1 {
    line-height: 1.2;
  }
  
  h2 {
    line-height: 1.3;
  }
}

@media (min-width: 1024px) {
  .grid-cols-3 {
    gap: 2rem;
  }
}
</style>