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

                    <!-- Action Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 max-w-6xl mx-auto">
                        <!-- Card UMKM Login -->
                        <div class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                            <div class="absolute inset-0 bg-gradient-to-br from-red-500/20 to-pink-500/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="h-14 w-14 bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center rounded-2xl mx-auto mb-4 shadow-lg group-hover:shadow-red-500/25 transition-shadow duration-300">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>

                                <h2 class="text-xl font-bold text-white text-center mb-3">Masuk sebagai UMKM</h2>

                                <p class="text-gray-200 text-center mb-6 leading-relaxed text-sm">
                                    Kelola profil UMKM Anda, tambahkan produk, dan tingkatkan visibilitas usaha Anda di platform digital.
                                </p>

                                <div class="flex flex-col sm:flex-row justify-center gap-3">
                                    <Link 
                                        :href="route('login')"
                                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 border border-transparent rounded-xl font-semibold text-sm text-white tracking-wide hover:from-red-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-transparent transition-all duration-200 shadow-lg hover:shadow-red-500/25"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                        Login
                                    </Link>
                                    <Link 
                                        :href="route('register')"
                                        class="inline-flex items-center justify-center px-6 py-3 bg-white/10 border border-white/30 rounded-xl font-semibold text-sm text-white tracking-wide hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 focus:ring-offset-transparent transition-all duration-200"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                        </svg>
                                        Register
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Card User Default -->
                        <div class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="h-14 w-14 bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center rounded-2xl mx-auto mb-4 shadow-lg group-hover:shadow-blue-500/25 transition-shadow duration-300">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>

                                <h2 class="text-xl font-bold text-white text-center mb-3">Masuk sebagai User</h2>

                                <p class="text-gray-200 text-center mb-6 leading-relaxed text-sm">
                                    Jelajahi berbagai UMKM di sekitar Anda dan temukan produk-produk lokal berkualitas.
                                </p>

                                <div class="flex justify-center">
                                    <Link 
                                        :href="route('public.umkm.list')"
                                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-cyan-600 border border-transparent rounded-xl font-semibold text-sm text-white tracking-wide hover:from-blue-600 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-transparent transition-all duration-200 shadow-lg hover:shadow-blue-500/25"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        Lihat Daftar UMKM
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Card Mapping -->
                        <div class="group relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 hover:scale-105 hover:shadow-2xl">
                            <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-emerald-500/20 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="h-14 w-14 bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center rounded-2xl mx-auto mb-4 shadow-lg group-hover:shadow-green-500/25 transition-shadow duration-300">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>

                                <h2 class="text-xl font-bold text-white text-center mb-3">Peta UMKM</h2>

                                <p class="text-gray-200 text-center mb-6 leading-relaxed text-sm">
                                    Temukan lokasi UMKM terdekat dengan menggunakan fitur peta interaktif kami.
                                </p>

                                <div class="flex justify-center">
                                    <Link 
                                        :href="route('public.mapping')"
                                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 border border-transparent rounded-xl font-semibold text-sm text-white tracking-wide hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:ring-offset-transparent transition-all duration-200 shadow-lg hover:shadow-green-500/25"
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
                    
                    <!-- Statistics Section
                    <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto">
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold text-white mb-1">1000+</div>
                            <div class="text-gray-300 text-xs md:text-sm">UMKM Terdaftar</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold text-white mb-1">50+</div>
                            <div class="text-gray-300 text-xs md:text-sm">Kota Terjangkau</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold text-white mb-1">20+</div>
                            <div class="text-gray-300 text-xs md:text-sm">Kategori Usaha</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl md:text-3xl font-bold text-white mb-1">24/7</div>
                            <div class="text-gray-300 text-xs md:text-sm">Akses Platform</div>
                        </div>
                    </div> -->
                </div>
            </div>
            
            <!-- Footer -->
            <div class="absolute bottom-4 left-0 right-0 z-10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center">
                        <p class="text-gray-300 text-sm">
                            © 2025 Sistem UMKM. Mengembangkan ekonomi digital Indonesia.
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