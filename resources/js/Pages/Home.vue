<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Sistem UMKM" />

    <GuestLayout>
        <!-- Hero Section -->
        <div class="relative min-h-screen bg-gradient-to-br from-blue-900 via-purple-900 to-indigo-900 overflow-hidden">
            <!-- Background Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-20"></div>
                <div class="absolute top-10 left-10 w-72 h-72 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
                <div class="absolute top-0 right-0 w-72 h-72 bg-yellow-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
                <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
            </div>
            
            <div class="relative z-10 flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
                <div class="max-w-7xl mx-auto text-center">
                    <!-- Header Section -->
                    <div class="mb-12 mt-8">
                        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6 tracking-tight">
                            <span class="block">Selamat Datang di</span>
                            <span class="block bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                                Sistem UMKM
                            </span>
                        </h1>
                        <p class="text-base sm:text-lg md:text-xl text-gray-200 mb-8 max-w-3xl mx-auto leading-relaxed">
                            Platform digital terdepan untuk mendukung perkembangan dan digitalisasi 
                            <span class="font-semibold text-yellow-400">Usaha Mikro, Kecil, dan Menengah</span> 
                            di Indonesia
                        </p>
                    </div>

                    <!-- Two Main Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto">
                        <!-- Card Penjual -->
                        <Link 
                            :href="route('login')"
                            class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer"
                        >
                            <div class="absolute inset-0 bg-gradient-to-br from-red-500/20 to-pink-500/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10 text-center">
                                <div class="h-16 w-16 bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center rounded-2xl mx-auto mb-4 shadow-lg group-hover:shadow-red-500/25 transition-shadow duration-300">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white">Penjual</h2>
                            </div>
                        </Link>

                        <!-- Card Pembeli -->
                        <Link 
                            :href="route('public.umkm.list')"
                            class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 hover:scale-105 hover:shadow-xl cursor-pointer"
                        >
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10 text-center">
                                <div class="h-16 w-16 bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center rounded-2xl mx-auto mb-4 shadow-lg group-hover:shadow-blue-500/25 transition-shadow duration-300">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white">Pembeli</h2>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="absolute bottom-0 left-0 right-0 z-10 mb-4 md:mb-0">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center">
                        <p class="text-gray-300 text-xs sm:text-sm leading-tight">
                            © 2025 Sistem UMKM. Program Kerja KKN PPM XXVII GASAL 2025/2026.
                        </p>
                    </div>
                </div>
            </div>
        </div>
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

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}

/* Responsive improvements */
@media (max-width: 640px) {
  .animate-blob {
    width: 200px;
    height: 200px;
  }
}
</style>