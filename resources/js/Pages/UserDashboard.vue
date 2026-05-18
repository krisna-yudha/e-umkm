<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    profile_photo?: string;
    user_type?: string;
}

interface Rating {
    id: number;
    rating: number;
    review?: string;
    helpful_count: number;
    created_at: string;
    umkm: {
        id: number;
        nama_umkm: string;
        kategori: string;
        gambar?: string;
    };
}

interface WishlistItem {
    id: number;
    umkm_id: number;
    created_at: string;
    umkm: {
        id: number;
        nama_umkm: string;
        kategori: string;
        gambar?: string;
        alamat: string;
    };
}

defineProps<{
    auth: {
        user: User;
    };
}>();

const ratings = ref<Rating[]>([]);
const wishlist = ref<WishlistItem[]>([]);
const loadingRatings = ref(true);
const loadingWishlist = ref(true);
const activeTab = ref('profile');

onMounted(() => {
    fetchUserRatings();
    fetchUserWishlist();
});

const fetchUserRatings = async () => {
    try {
        const response = await fetch('/user/ratings');
        if (response.ok) {
            const data = await response.json();
            ratings.value = data.ratings || [];
        }
    } catch (error) {
        console.error('Error fetching ratings:', error);
    } finally {
        loadingRatings.value = false;
    }
};

const fetchUserWishlist = async () => {
    try {
        const response = await fetch('/user/wishlist');
        if (response.ok) {
            const data = await response.json();
            wishlist.value = data.wishlists || [];
        }
    } catch (error) {
        console.error('Error fetching wishlist:', error);
    } finally {
        loadingWishlist.value = false;
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const getRatingStars = (rating: number) => {
    return '⭐'.repeat(rating);
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Dashboard - User" />

        <!-- Header Section -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white py-12 px-4">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-4xl font-bold mb-4">Selamat Datang, {{ auth.user.name }}! 👋</h1>
                <p class="text-purple-100 text-lg">Platform Eksplorasi & Rating UMKM Lokal</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Tabs Navigation -->
            <div class="flex gap-4 mb-8 border-b border-gray-200">
                <button
                    @click="activeTab = 'profile'"
                    :class="activeTab === 'profile' ? 'border-b-4 border-purple-600 text-purple-600' : 'text-gray-600'"
                    class="px-6 py-4 font-semibold transition-colors"
                >
                    👤 Data Diri
                </button>
                <button
                    @click="activeTab = 'ratings'"
                    :class="activeTab === 'ratings' ? 'border-b-4 border-purple-600 text-purple-600' : 'text-gray-600'"
                    class="px-6 py-4 font-semibold transition-colors"
                >
                    ⭐ Rating & Review ({{ ratings.length }})
                </button>
                <button
                    @click="activeTab = 'wishlist'"
                    :class="activeTab === 'wishlist' ? 'border-b-4 border-purple-600 text-purple-600' : 'text-gray-600'"
                    class="px-6 py-4 font-semibold transition-colors"
                >
                    ❤️ Wishlist ({{ wishlist.length }})
                </button>
            </div>

            <!-- Profile Tab -->
            <div v-if="activeTab === 'profile'" class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Profile Card -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-blue-600 h-32"></div>
                        <div class="px-6 pb-6 text-center -mt-16">
                            <!-- Profile Photo -->
                            <div class="mb-4 flex justify-center">
                                <img
                                    v-if="auth.user.profile_photo"
                                    :src="`/storage/${auth.user.profile_photo}`"
                                    :alt="auth.user.name"
                                    class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover"
                                />
                                <div
                                    v-else
                                    class="w-32 h-32 rounded-full border-4 border-white shadow-lg bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center"
                                >
                                    <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ auth.user.name }}</h2>
                            <p class="text-gray-600 mb-4">{{ auth.user.email }}</p>
                            <div class="flex justify-center gap-2">
                                <span class="inline-block px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-semibold">
                                    👤 {{ auth.user.user_type === 'umkm' ? 'Pelaku UMKM' : 'Pembeli Biasa' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="mt-8 bg-white rounded-2xl shadow-lg p-6 space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">📊 Statistik</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Rating & Review</span>
                                <span class="font-bold text-purple-600">{{ ratings.length }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Wishlist</span>
                                <span class="font-bold text-blue-600">{{ wishlist.length }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Kontribusi Total</span>
                                <span class="font-bold text-green-600">{{ ratings.length + wishlist.length }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile Button -->
                    <Link
                        href="/profile"
                        class="mt-8 w-full inline-block text-center px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-bold rounded-lg hover:from-purple-700 hover:to-blue-700 transition-all"
                    >
                        ✏️ Edit Profil
                    </Link>
                </div>

                <!-- Info Section -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Feature Cards -->
                    <div class="grid grid-cols-1 gap-4">
                        <!-- Rating Feature -->
                        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-lg p-6 border-l-4 border-orange-500">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">⭐ Beri Rating & Review</h3>
                            <p class="text-gray-700 mb-4">Bagikan pengalaman Anda dengan UMKM lain. Rating dan review Anda membantu pembeli lain membuat keputusan terbaik.</p>
                            <Link href="/umkm-list" class="inline-block text-orange-600 hover:text-orange-800 font-semibold">
                                Jelajahi UMKM →
                            </Link>
                        </div>

                        <!-- Wishlist Feature -->
                        <div class="bg-gradient-to-br from-pink-50 to-red-50 rounded-lg p-6 border-l-4 border-pink-500">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">❤️ Simpan Favorit</h3>
                            <p class="text-gray-700 mb-4">Simpan UMKM favorit Anda untuk akses cepat. Kelola wishlist Anda dengan mudah dari halaman ini.</p>
                            <button
                                @click="activeTab = 'wishlist'"
                                class="inline-block text-pink-600 hover:text-pink-800 font-semibold"
                            >
                                Lihat Wishlist →
                            </button>
                        </div>

                        <!-- Explore Feature -->
                        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg p-6 border-l-4 border-blue-500">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">🗺️ Jelajahi Peta UMKM</h3>
                            <p class="text-gray-700 mb-4">Temukan UMKM di sekitar Anda menggunakan peta interaktif. Lihat lokasi dan informasi lengkap setiap UMKM.</p>
                            <Link href="/mapping" class="inline-block text-blue-600 hover:text-blue-800 font-semibold">
                                Buka Peta →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ratings Tab -->
            <div v-if="activeTab === 'ratings'">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">⭐ Rating & Review Saya</h2>

                    <div v-if="loadingRatings" class="text-center py-12">
                        <div class="animate-spin">⏳</div>
                        <p class="text-gray-600 mt-2">Memuat data...</p>
                    </div>

                    <div v-else-if="ratings.length === 0" class="text-center py-12">
                        <div class="text-5xl mb-4">⭐</div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Rating</h3>
                        <p class="text-gray-600 mb-6">Anda belum memberikan rating atau review apapun.</p>
                        <Link href="/umkm-list" class="inline-block px-6 py-3 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 transition-all">
                            Mulai Rating Sekarang
                        </Link>
                    </div>

                    <div v-else class="space-y-4">
                        <div v-for="rating in ratings" :key="rating.id" class="border-l-4 border-purple-500 p-4 bg-gray-50 rounded-lg hover:bg-purple-50 transition-all">
                            <div class="flex items-start gap-4">
                                <!-- UMKM Image -->
                                <img
                                    v-if="rating.umkm.gambar"
                                    :src="`/storage/${rating.umkm.gambar}`"
                                    :alt="rating.umkm.nama_umkm"
                                    class="w-20 h-20 rounded-lg object-cover flex-shrink-0"
                                />
                                <div v-else class="w-20 h-20 rounded-lg bg-gray-300 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <Link
                                        :href="`/umkm-public/${rating.umkm.id}`"
                                        class="text-lg font-bold text-purple-600 hover:text-purple-800 mb-1 block truncate"
                                    >
                                        {{ rating.umkm.nama_umkm }}
                                    </Link>
                                    <p class="text-sm text-gray-600 mb-2">{{ rating.umkm.kategori }}</p>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-yellow-500">{{ getRatingStars(rating.rating) }}</span>
                                        <span class="text-sm text-gray-700 font-semibold">{{ rating.rating }}/5</span>
                                    </div>
                                    <p v-if="rating.review" class="text-gray-700 mb-2">{{ rating.review }}</p>
                                    <p class="text-xs text-gray-500">{{ formatDate(rating.created_at) }}</p>
                                </div>

                                <!-- Stats -->
                                <div class="text-right flex-shrink-0">
                                    <p class="text-sm text-gray-600">👍 {{ rating.helpful_count }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wishlist Tab -->
            <div v-if="activeTab === 'wishlist'">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">❤️ Wishlist Saya</h2>

                    <div v-if="loadingWishlist" class="text-center py-12">
                        <div class="animate-spin">⏳</div>
                        <p class="text-gray-600 mt-2">Memuat data...</p>
                    </div>

                    <div v-else-if="wishlist.length === 0" class="text-center py-12">
                        <div class="text-5xl mb-4">❤️</div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Wishlist Kosong</h3>
                        <p class="text-gray-600 mb-6">Belum ada UMKM yang disimpan di wishlist Anda.</p>
                        <Link href="/umkm-list" class="inline-block px-6 py-3 bg-pink-600 text-white font-bold rounded-lg hover:bg-pink-700 transition-all">
                            Jelajahi UMKM
                        </Link>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Link
                            v-for="item in wishlist"
                            :key="item.id"
                            :href="`/umkm-public/${item.umkm_id}`"
                            class="group bg-gradient-to-br from-pink-50 to-red-50 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-300 border border-pink-100 hover:border-pink-300"
                        >
                            <!-- Image -->
                            <div class="relative overflow-hidden bg-gray-200 h-40">
                                <img
                                    v-if="item.umkm.gambar"
                                    :src="`/storage/${item.umkm.gambar}`"
                                    :alt="item.umkm.nama_umkm"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="absolute top-2 right-2 bg-pink-500 text-white px-2 py-1 rounded-full text-sm font-bold">❤️</div>
                            </div>

                            <!-- Content -->
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 mb-1 line-clamp-2 group-hover:text-pink-600">
                                    {{ item.umkm.nama_umkm }}
                                </h3>
                                <p class="text-xs text-gray-600 mb-2">{{ item.umkm.kategori }}</p>
                                <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ item.umkm.alamat }}</p>
                                <p class="text-xs text-gray-500">Ditambahkan: {{ formatDate(item.created_at) }}</p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
