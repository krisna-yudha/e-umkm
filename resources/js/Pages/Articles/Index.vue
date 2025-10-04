<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Article {
    title: string;
    excerpt: string;
    link: string;
    date: string;
    image?: string;
    category: string;
}

const props = defineProps<{
    articles: Article[]
}>();

const page = usePage();

// Smart navigation based on user authentication and role
const backUrl = computed(() => {
    // Get user from Inertia page props
    const user = page.props.auth?.user as any;
    
    if (user) {
        // User is authenticated, determine destination based on role
        if (user.role === 'admin') {
            return '/admin/dashboard';
        } else {
            return '/dashboard';
        }
    }
    
    // User is not authenticated, go to home
    return '/';
});

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const handleArticleClick = (articleLink: string, event: Event) => {
    // Prevent click when interacting with the "Baca Selengkapnya" link
    const target = event.target as HTMLElement;
    if (target.closest('a') || target.closest('[role="button"]')) {
        return;
    }
    
    // Open article in new tab
    window.open(articleLink, '_blank');
};

const getCategoryColor = (category: string) => {
    const colors: { [key: string]: string } = {
        'Bisnis': 'bg-blue-100 text-blue-800',
        'Kebijakan': 'bg-green-100 text-green-800',
        'Pelatihan': 'bg-purple-100 text-purple-800',
        'Informasi UMKM': 'bg-yellow-100 text-yellow-800',
        'Tutorial': 'bg-indigo-100 text-indigo-800',
        'Event': 'bg-pink-100 text-pink-800',
        'Inovasi': 'bg-cyan-100 text-cyan-800',
        'default': 'bg-gray-100 text-gray-800'
    };
    
    return colors[category] || colors['default'];
};
</script>

<template>
    <Head title="Informasi & Tips UMKM" />
    
    <GuestLayout>
        <!-- Background sesuai tema homepage -->
        <div class="min-h-screen" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <!-- Hero Section -->
            <div class="text-white relative overflow-hidden">
                <!-- Background pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 20px 20px;"></div>
                </div>
                
                <div class="max-w-7xl mx-auto px-3 sm:px-4 py-8 sm:py-12 relative">
                    <div class="text-center">
                        <!-- Back Button -->
                        <div class="flex justify-start mb-6">
                            <Link 
                                :href="backUrl" 
                                class="inline-flex items-center bg-white bg-opacity-20 backdrop-blur-sm text-white hover:bg-opacity-30 transition-all duration-200 p-2 rounded-lg border border-white border-opacity-30"
                            >
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                <span class="text-sm sm:text-base">Kembali</span>
                            </Link>
                        </div>

                        <div class="flex items-center justify-center mb-6">
                            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                                <svg class="w-12 h-12 sm:w-16 sm:h-16 text-white mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        </div>

                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4 bg-gradient-to-r from-white to-gray-200 bg-clip-text text-transparent">
                            Informasi & Tips UMKM
                        </h1>
                        <p class="text-base sm:text-lg text-white text-opacity-90 mb-8 max-w-3xl mx-auto leading-relaxed">
                            Dapatkan informasi terbaru, tips, dan panduan untuk mengembangkan 
                            <span class="text-yellow-300 font-semibold">UMKM Anda</span> dari Dinas Koperasi dan UMKM Kota Semarang
                        </p>
                    </div>
                </div>
            </div>

            <!-- Articles Section -->
            <div class="max-w-7xl mx-auto px-3 sm:px-4 pb-8 sm:pb-12 relative -mt-6 sm:-mt-8">
                <div class="bg-white bg-opacity-95 backdrop-blur-lg overflow-hidden shadow-2xl rounded-2xl sm:rounded-3xl border border-white border-opacity-50">
                    
                    <!-- Header -->
                    <div class="px-4 sm:px-8 py-4 sm:py-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg sm:text-xl font-semibold text-gray-900 flex items-center">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                    Artikel Terbaru
                                </h2>
                                <p class="text-sm text-gray-600 mt-1">{{ articles.length }} artikel tersedia</p>
                            </div>
                            <div class="text-right">
                                <a href="https://www.diskopumkm.semarangkota.go.id" 
                                   target="_blank"
                                   class="text-xs sm:text-sm text-purple-600 hover:text-purple-800 font-medium">
                                    Lihat Semua →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Articles Grid -->
                    <div class="p-4 sm:p-6 md:p-8">
                        <div v-if="articles.length === 0" class="text-center py-8 sm:py-12">
                            <div class="text-4xl sm:text-6xl mb-4">📰</div>
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2">Belum Ada Artikel</h3>
                            <p class="text-sm sm:text-base text-gray-500">Artikel sedang dimuat atau tidak tersedia saat ini</p>
                        </div>

                        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                            <article 
                                v-for="(article, index) in articles" 
                                :key="index" 
                                @click="handleArticleClick(article.link, $event)"
                                class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1 cursor-pointer active:scale-95 touch-manipulation"
                            >
                                <!-- Article Image -->
                                <div class="aspect-w-16 aspect-h-9 bg-gradient-to-br from-purple-100 to-blue-100">
                                    <img 
                                        v-if="article.image" 
                                        :src="article.image" 
                                        :alt="article.title"
                                        class="w-full h-32 sm:h-40 object-cover"
                                        @error="(event) => {
                                            const target = event.target as HTMLImageElement;
                                            if (target) target.style.display = 'none';
                                        }"
                                    >
                                    <div v-else class="w-full h-32 sm:h-40 bg-gradient-to-br from-purple-100 to-blue-100 flex items-center justify-center">
                                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Article Content -->
                                <div class="p-4">
                                    <!-- Category Badge -->
                                    <div class="flex items-center justify-between mb-3">
                                        <span :class="getCategoryColor(article.category)" class="px-2 py-1 rounded-full text-xs font-medium">
                                            {{ article.category }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ formatDate(article.date) }}</span>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="font-semibold text-gray-900 text-sm sm:text-base mb-2 line-clamp-2 leading-tight">
                                        {{ article.title }}
                                    </h3>

                                    <!-- Excerpt -->
                                    <p class="text-gray-600 text-xs sm:text-sm mb-4 line-clamp-3">
                                        {{ article.excerpt }}
                                    </p>

                                    <!-- Read More Button -->
                                    <a 
                                        :href="article.link" 
                                        target="_blank"
                                        @click.stop
                                        class="inline-flex items-center text-purple-600 hover:text-purple-800 font-medium text-sm transition-colors duration-200"
                                    >
                                        Baca Selengkapnya
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        </div>

                        <!-- Footer Info -->
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <div class="text-center">
                                <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-4 sm:p-6 mb-4">
                                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">
                                        📚 Sumber Informasi
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-4">
                                        Artikel-artikel ini bersumber dari website resmi Dinas Koperasi dan UMKM Kota Semarang
                                    </p>
                                    <a 
                                        href="https://www.diskopumkm.semarangkota.go.id" 
                                        target="_blank"
                                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 font-medium text-sm"
                                    >
                                        Kunjungi Website Resmi
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                    </a>
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

/* Mobile optimizations */
@media screen and (max-width: 768px) {
    article {
        transform: none !important;
    }
    
    article:hover {
        transform: none !important;
    }
}
</style>