<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Article {
    id?: number;
    title: string;
    excerpt: string;
    image?: string;
    date: string;
    category: string;
    link: string;
}

defineProps<{
    auth: {
        user: User;
    };
}>();

const articles = ref<Article[]>([]);
const loading = ref(true);
const showDropdown = ref(false);

// Toggle dropdown
const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

// Close dropdown when clicking outside
const closeDropdown = (event: Event) => {
    const dropdown = document.querySelector('.dropdown');
    if (dropdown && !dropdown.contains(event.target as Node)) {
        showDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', closeDropdown);
    fetchArticles();
});

onUnmounted(() => {
    document.removeEventListener('click', closeDropdown);
});

// Function to get category color
const getCategoryColor = (category: string): string => {
    const colors: { [key: string]: string } = {
        'Bisnis': 'bg-purple-600',
        'Kebijakan': 'bg-blue-600', 
        'Marketing': 'bg-green-600',
        'Teknologi': 'bg-red-600',
        'Pelatihan': 'bg-yellow-600',
        'Tutorial': 'bg-indigo-600',
        'Event': 'bg-pink-600',
        'Informasi UMKM': 'bg-teal-600',
        'default': 'bg-gray-600'
    };
    return colors[category] || colors['default'];
};

// Fetch articles from our API
const fetchArticles = async () => {
    try {
        const response = await fetch('/articles/api');
        const data = await response.json();
        articles.value = data.slice(0, 3); // Show only first 3 articles on dashboard
    } catch (error) {
        console.error('Error fetching articles:', error);
        // Fallback to default articles if API fails
        articles.value = [
            {
                id: 1,
                title: "Tips Meningkatkan Penjualan UMKM di Era Digital",
                excerpt: "Pelajari strategi-strategi efektif untuk meningkatkan visibility dan penjualan UMKM Anda melalui platform digital.",
                image: "/kotasmg.png",
                date: "25 September 2025",
                category: "Bisnis",
                link: "https://www.diskopumkm.semarangkota.go.id"
            },
            {
                id: 2,
                title: "Program Bantuan Pemerintah untuk UMKM 2025",
                excerpt: "Informasi terkini mengenai berbagai program bantuan dan subsidi yang tersedia untuk mendukung pertumbuhan UMKM.",
                image: "/kotasmg.png",
                date: "22 September 2025",
                category: "Kebijakan",
                link: "https://www.diskopumkm.semarangkota.go.id"
            },
            {
                id: 3,
                title: "Strategi Pemasaran Online yang Efektif",
                excerpt: "Manfaatkan media sosial dan platform e-commerce untuk memperluas jangkauan pasar UMKM Anda.",
                image: "/kotasmg.png",
                date: "20 September 2025",
                category: "Marketing",
                link: "https://www.diskopumkm.semarangkota.go.id"
            }
        ];
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchArticles();
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Dashboard
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Selamat datang di platform UMKM digital
                    </p>
                </div>
                
                User Dropdown Menu
                <div class="relative">
                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-700">{{ auth.user.name }}</p>
                            <p class="text-xs text-gray-500">{{ auth.user.email }}</p>
                        </div>
                        <div class="dropdown relative">
                            <button 
                                @click="toggleDropdown"
                                class="flex items-center p-2 text-gray-600 hover:text-gray-800 focus:outline-none transition-colors duration-200"
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div 
                                v-show="showDropdown"
                                class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 transition-all duration-200"
                            >
                                <Link 
                                    :href="route('logout')" 
                                    method="post" 
                                    as="button"
                                    class="flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition-colors duration-200 border-b border-gray-100"
                                >
                                    <svg class="w-4 h-4 mr-3 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Log Out
                                </Link>
                                <button 
                                    @click="showDropdown = false"
                                    class="flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200 rounded-b-lg"
                                >
                                    <svg class="w-4 h-4 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Help
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template> -->

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
                        <Link :href="route('articles.index')" class="text-purple-600 hover:text-purple-700 font-medium text-sm transition-colors duration-200">
                            Lihat Semua
                        </Link>
                    </div>

                    <!-- Loading State -->
                    <div v-if="loading" class="grid grid-cols-1 gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="i in 3" :key="i" class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden animate-pulse">
                            <div class="h-40 sm:h-48 bg-gray-200"></div>
                            <div class="p-4 sm:p-6">
                                <div class="h-4 bg-gray-200 rounded mb-2"></div>
                                <div class="h-3 bg-gray-200 rounded mb-4"></div>
                                <div class="flex justify-between">
                                    <div class="h-3 bg-gray-200 rounded w-16"></div>
                                    <div class="h-3 bg-gray-200 rounded w-20"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Articles Grid -->
                    <div v-else class="grid grid-cols-1 gap-4 sm:gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <article 
                            v-for="(article, index) in articles" 
                            :key="article.id || index" 
                            class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] overflow-hidden"
                        >
                            <div class="relative h-40 sm:h-48 overflow-hidden">
                                <img 
                                    :src="article.image || '/kotasmg.png'" 
                                    :alt="article.title"
                                    class="w-full h-full object-cover"
                                    @error="(event) => (event.target as HTMLImageElement).src = '/kotasmg.png'"
                                />
                                <div class="absolute top-3 left-3">
                                    <span :class="`${getCategoryColor(article.category)} text-white px-2 py-1 rounded-full text-xs font-medium`">
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
                                    <a 
                                        :href="article.link" 
                                        target="_blank" 
                                        rel="noopener noreferrer"
                                        class="text-purple-600 hover:text-purple-700 font-medium text-xs sm:text-sm transition-colors duration-200 flex items-center gap-1"
                                    >
                                        Baca Selengkapnya
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
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
