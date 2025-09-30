<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface User {
    id: number;
    name: string;
    email: string;
}

defineProps<{
    auth: {
        user: User;
    };
}>();

// Sample articles/news data - ini bisa nanti diambil dari database
const articles = [
    {
        id: 1,
        title: "Tips Meningkatkan Penjualan UMKM di Era Digital",
        excerpt: "Pelajari strategi-strategi efektif untuk meningkatkan visibility dan penjualan UMKM Anda melalui platform digital.",
        image: "https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=400&h=200&fit=crop",
        date: "25 September 2025",
        category: "Bisnis"
    },
    {
        id: 2,
        title: "Program Bantuan Pemerintah untuk UMKM 2025",
        excerpt: "Informasi terkini mengenai berbagai program bantuan dan subsidi yang tersedia untuk mendukung pertumbuhan UMKM.",
        image: "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=400&h=200&fit=crop",
        date: "22 September 2025",
        category: "Kebijakan"
    },
    {
        id: 3,
        title: "Strategi Pemasaran Online yang Efektif",
        excerpt: "Manfaatkan media sosial dan platform e-commerce untuk memperluas jangkauan pasar UMKM Anda.",
        image: "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400&h=200&fit=crop",
        date: "20 September 2025",
        category: "Marketing"
    }
];
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Dashboard
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Selamat datang di platform UMKM digital
                    </p>
                </div>
                <Link 
                    :href="route('user.profile')"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white tracking-wide hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-purple-500/25 w-full sm:w-auto"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Kelola Profile & UMKM
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Welcome Section -->
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-3xl p-6 sm:p-8 mb-8 text-white">
                    <div class="max-w-3xl">
                        <h1 class="text-2xl sm:text-3xl font-bold mb-4">
                            Selamat Datang, {{ auth.user.name }}! 👋
                        </h1>
                        <p class="text-purple-100 text-sm sm:text-base mb-6">
                            Platform digital terdepan untuk mendukung perkembangan dan digitalisasi Usaha Mikro, Kecil, dan Menengah di Indonesia. 
                            Kelola UMKM Anda dengan mudah dan jangkau lebih banyak pelanggan.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <Link 
                                :href="route('user.profile')"
                                class="inline-flex items-center justify-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl font-semibold text-sm text-white hover:bg-opacity-30 transition-all duration-200"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Mulai Kelola UMKM
                            </Link>
                            <Link 
                                :href="route('public.umkm.list')"
                                class="inline-flex items-center justify-center px-6 py-3 bg-transparent border border-white border-opacity-30 rounded-xl font-semibold text-sm text-white hover:bg-white hover:bg-opacity-10 transition-all duration-200"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Jelajahi UMKM Lain
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Articles/News Section -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">
                            Informasi & Tips UMKM
                        </h2>
                        <a href="#" class="text-purple-600 hover:text-purple-700 font-medium text-sm transition-colors duration-200">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <article 
                            v-for="article in articles" 
                            :key="article.id" 
                            class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] overflow-hidden"
                        >
                            <div class="relative h-40 sm:h-48 overflow-hidden">
                                <img 
                                    :src="article.image" 
                                    :alt="article.title"
                                    class="w-full h-full object-cover"
                                />
                                <div class="absolute top-3 left-3">
                                    <span class="bg-purple-600 text-white px-2 py-1 rounded-full text-xs font-medium">
                                        {{ article.category }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-4 sm:p-6">
                                <h3 class="font-bold text-base sm:text-lg text-gray-800 mb-2 line-clamp-2">
                                    {{ article.title }}
                                </h3>
                                <p class="text-gray-600 text-xs sm:text-sm mb-4 line-clamp-3">
                                    {{ article.excerpt }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500">{{ article.date }}</span>
                                    <a href="#" class="text-purple-600 hover:text-purple-700 font-medium text-xs sm:text-sm transition-colors duration-200">
                                        Baca Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <!-- Quick Actions
                <div class="bg-gray-50 rounded-2xl p-6 sm:p-8">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 mb-6">
                        Aksi Cepat
                    </h2>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <Link 
                            :href="route('user.profile')"
                            class="flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-purple-200 transition-all duration-200 group"
                        >
                            <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors duration-200">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-800">Kelola UMKM</p>
                                <p class="text-xs text-gray-500">Atur profil & data UMKM</p>
                            </div>
                        </Link>

                        <Link 
                            :href="route('public.umkm.list')"
                            class="flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-200 transition-all duration-200 group"
                        >
                            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-200">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-800">Jelajahi UMKM</p>
                                <p class="text-xs text-gray-500">Lihat UMKM lainnya</p>
                            </div>
                        </Link>

                        <a 
                            href="#"
                            class="flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-green-200 transition-all duration-200 group"
                        >
                            <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors duration-200">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-800">Panduan</p>
                                <p class="text-xs text-gray-500">Tutorial & tips</p>
                            </div>
                        </a>

                        <a 
                            href="#"
                            class="flex items-center p-4 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-yellow-200 transition-all duration-200 group"
                        >
                            <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center group-hover:bg-yellow-200 transition-colors duration-200">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 110 19.5 9.75 9.75 0 010-19.5z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-800">Bantuan</p>
                                <p class="text-xs text-gray-500">Pusat bantuan</p>
                            </div>
                        </a>
                    </div>
                </div> -->
            </div>
        </div>
    </AuthenticatedLayout>
</template>
