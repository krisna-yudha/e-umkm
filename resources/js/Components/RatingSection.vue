<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

interface User {
    id: number;
    name: string;
    profile_photo?: string;
    user_type?: string;
}

interface RatingData {
    id: number;
    rating: number;
    review?: string;
    helpful_count: number;
    created_at: string;
    user: User;
}

interface RatingStats {
    average_rating: number;
    total_ratings: number;
    distribution: Record<number, { count: number; percentage: number }>;
}

interface Umkm {
    id: number;
    nama_umkm: string;
}

const props = defineProps<{
    umkm: Umkm;
    currentUser?: { id: number; name: string; user_type?: string; user_type_old?: string } | null;
}>();

const ratings = ref<RatingData[]>([]);
const stats = ref<RatingStats | null>(null);
const isLoading = ref(false);
const isSubmitting = ref(false);

// Form state
const userRating = ref(0);
const userReview = ref('');
const existingRating = ref<RatingData | null>(null);
const hoverRating = ref(0);

// Debug: Log current user
console.log('RatingSection - currentUser:', props.currentUser);
const csrfToken = window.axios.defaults.headers.common['X-CSRF-TOKEN'];
console.log('RatingSection - axios config:', {
    withCredentials: window.axios.defaults.withCredentials,
    headers: {
        'X-CSRF-TOKEN': typeof csrfToken === 'string' ? csrfToken.substring(0, 20) + '...' : 'NOT SET',
        'X-Requested-With': window.axios.defaults.headers.common['X-Requested-With']
    }
});

const canEditRating = computed(() => {
    // Sederhana: Jika user ada = bisa rating. Backend akan validate user_type
    const canRate = !!props.currentUser;
    console.log('canEditRating:', canRate, 'User:', props.currentUser?.name || 'None');
    return canRate;
});

const isEditingRating = computed(() => {
    return !!existingRating.value;
});

const averageStars = computed(() => {
    return stats.value?.average_rating || 0;
});

const totalCount = computed(() => {
    return stats.value?.total_ratings || 0;
});

const getRatingPercentage = (rating: number) => {
    return stats.value?.distribution[rating]?.percentage || 0;
};

const getRatingCount = (rating: number) => {
    return stats.value?.distribution[rating]?.count || 0;
};

// Load ratings
const loadRatings = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(`/api/v1/umkms/${props.umkm.id}/ratings`);
        ratings.value = response.data.ratings;
        stats.value = {
            average_rating: response.data.average_rating,
            total_ratings: response.data.total_ratings,
            distribution: response.data.distribution,
        };

        // Check if current user has already rated
        if (props.currentUser) {
            existingRating.value = ratings.value.find(r => r.user.id === props.currentUser?.id) || null;
            if (existingRating.value) {
                userRating.value = existingRating.value.rating;
                userReview.value = existingRating.value.review || '';
            }
        }
    } catch (error) {
        console.error('Error loading ratings:', error);
    } finally {
        isLoading.value = false;
    }
};

// Submit rating
const submitRating = async () => {
    if (!canEditRating.value) {
        alert('Anda harus login terlebih dahulu');
        window.location.href = '/login/user';
        return;
    }

    if (userRating.value === 0) {
        alert('Silakan pilih rating bintang');
        return;
    }

    isSubmitting.value = true;
    try {
        console.log('=== SUBMITTING RATING ===');
        console.log('User:', props.currentUser);
        console.log('UMKM ID:', props.umkm.id);
        console.log('Rating:', userRating.value);
        console.log('Axios defaults:', {
            withCredentials: window.axios.defaults.withCredentials,
            baseURL: window.axios.defaults.baseURL,
            headers: {
                'X-Requested-With': window.axios.defaults.headers.common['X-Requested-With'],
                'X-CSRF-TOKEN': typeof window.axios.defaults.headers.common['X-CSRF-TOKEN'] === 'string' ? 'PRESENT' : 'MISSING'
            }
        });

        const response = await axios.post(`/api/v1/umkms/${props.umkm.id}/ratings`, {
            rating: userRating.value,
            review: userReview.value || null,
        });

        console.log('✅ Rating submitted successfully:', response.data);

        // Reload ratings after successful submission
        await loadRatings();
        userRating.value = 0;
        userReview.value = '';
        alert('✅ Rating berhasil disimpan!');
    } catch (error: any) {
        console.error('❌ Error submitting rating:', error);
        console.error('Response status:', error.response?.status);
        console.error('Response data:', error.response?.data);
        console.error('Request URL:', error.config?.url);
        console.error('Request headers:', error.config?.headers);
        
        // Detailed error handling
        if (error.response?.status === 401) {
            alert('❌ Sesi Anda telah berakhir (401). Silakan login kembali.');
            window.location.href = '/login/user';
        } else if (error.response?.status === 403) {
            alert('❌ Anda tidak memiliki izin untuk memberikan rating. Hanya pengguna biasa yang dapat memberikan rating.');
        } else if (error.response?.data?.message) {
            alert('❌ Error: ' + error.response.data.message);
        } else if (error.message) {
            alert('❌ Error: ' + error.message);
        } else {
            alert('❌ Gagal menyimpan rating. Silakan coba lagi.');
        }
    } finally {
        isSubmitting.value = false;
    }
};

// Format date
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

// Delete rating
const deleteRating = async (ratingId: number) => {
    if (!confirm('Apakah Anda yakin ingin menghapus rating ini?')) return;

    try {
        // Sanctum akan otomatis attach session cookie
        await axios.delete(`/api/v1/ratings/${ratingId}`);
        await loadRatings();
        userRating.value = 0;
        userReview.value = '';
        alert('Rating berhasil dihapus');
    } catch (error: any) {
        console.error('Error deleting rating:', error);
        if (error.response?.status === 401) {
            alert('Anda harus login untuk menghapus rating');
        } else {
            alert('Gagal menghapus rating');
        }
    }
};

// Mark helpful
const markHelpful = async (ratingId: number) => {
    try {
        // Sanctum akan otomatis attach session cookie
        await axios.post(`/api/v1/ratings/${ratingId}/helpful`, {});
        await loadRatings();
    } catch (error) {
        console.error('Error marking rating as helpful:', error);
    }
};

onMounted(() => {
    loadRatings();
});

// Star rendering
const getStarClass = (index: number, ratingValue: number) => {
    const isFilled = index < ratingValue;
    return isFilled ? 'text-yellow-400' : 'text-gray-300';
};
</script>

<template>
    <div class="bg-white rounded-lg sm:rounded-2xl overflow-hidden shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-4 sm:px-6 md:px-8 py-6 sm:py-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 via-white to-gray-50">
            <div class="flex items-center gap-3">
                <span class="text-3xl">⭐</span>
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-900">Rating & Review</h3>
                    <p class="text-sm text-gray-600 mt-1">Bagikan pengalaman Anda tentang UMKM ini</p>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-6 md:p-8">
            <!-- Rating Stats -->
            <div v-if="stats" class="mb-8 pb-8 border-b border-gray-100">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Average Rating -->
                    <div class="text-center sm:text-left">
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-5xl font-bold text-gray-900">{{ averageStars.toFixed(1) }}</span>
                            <span class="text-2xl text-gray-500">/5</span>
                        </div>
                        <div class="flex gap-1 justify-center sm:justify-start mb-2">
                            <svg v-for="i in 5" :key="i" :class="getStarClass(i - 1, Math.round(averageStars))" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600">{{ totalCount }} rating dari pengguna</p>
                    </div>

                    <!-- Rating Distribution -->
                    <div class="space-y-2">
                        <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="flex items-center gap-2">
                            <span class="w-12 text-sm font-medium text-gray-600">{{ star }}⭐</span>
                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-yellow-400 to-yellow-500" :style="{ width: getRatingPercentage(star) + '%' }"></div>
                            </div>
                            <span class="w-12 text-right text-sm text-gray-600">{{ getRatingCount(star) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Form (Only for logged-in users) -->
            <div v-if="canEditRating" class="mb-8 pb-8 border-b border-gray-100">
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 p-4 sm:p-6 rounded-lg border border-blue-100">
                    <h4 class="text-lg font-bold text-gray-900 mb-4">{{ isEditingRating ? 'Perbarui Rating Anda' : 'Berikan Rating' }}</h4>
                    
                    <!-- Star Rating Selector -->
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-3">Berapa rating UMKM ini?</p>
                        <div class="flex gap-2">
                            <button 
                                v-for="star in 5" 
                                :key="star"
                                @click="userRating = star"
                                @mouseover="hoverRating = star"
                                @mouseleave="hoverRating = 0"
                                class="transition-all duration-200"
                            >
                                <svg 
                                    :class="hoverRating > 0 ? (star <= hoverRating ? 'text-yellow-400' : 'text-gray-300') : (star <= userRating ? 'text-yellow-400' : 'text-gray-300')"
                                    class="w-12 h-12 cursor-pointer" 
                                    fill="currentColor" 
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Review Text -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-900 mb-2">Ulasan (Opsional)</label>
                        <textarea 
                            v-model="userReview"
                            placeholder="Bagikan pengalaman Anda... (maksimal 1000 karakter)"
                            maxlength="1000"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all"
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">{{ userReview.length }}/1000</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex gap-3">
                        <button 
                            @click="submitRating"
                            :disabled="isSubmitting"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-bold rounded-lg hover:from-purple-700 hover:to-blue-700 transition-all duration-200 disabled:opacity-50"
                        >
                            {{ isSubmitting ? '⏳ Menyimpan...' : (isEditingRating ? '✏️ Perbarui Rating' : '📝 Kirim Rating') }}
                        </button>
                        <button 
                            v-if="isEditingRating"
                            @click="deleteRating(existingRating!.id)"
                            class="px-6 py-3 bg-red-100 text-red-700 font-bold rounded-lg hover:bg-red-200 transition-all duration-200"
                        >
                            🗑️ Hapus
                        </button>
                    </div>
                </div>
            </div>

            <!-- Login CTA -->
            <div v-else-if="!currentUser" class="mb-8 pb-8 border-b border-gray-100">
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 p-4 sm:p-6 rounded-lg border border-amber-200 text-center">
                    <p class="text-gray-900 font-medium mb-3">Masuk untuk memberikan rating dan ulasan</p>
                    <a href="/login/user" class="inline-block px-6 py-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-bold rounded-lg hover:from-purple-700 hover:to-blue-700 transition-all">
                        🔐 Masuk sebagai Pengguna
                    </a>
                </div>
            </div>

            <!-- Ratings List -->
            <div v-if="!isLoading && ratings.length > 0" class="space-y-4">
                <h4 class="text-lg font-bold text-gray-900 mb-6">{{ ratings.length }} Ulasan</h4>
                
                <div v-for="rating in ratings" :key="rating.id" class="border border-gray-200 rounded-lg p-4 sm:p-6 hover:border-gray-300 transition-all">
                    <!-- Reviewer Info -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <img 
                                v-if="rating.user.profile_photo"
                                :src="`/storage/${rating.user.profile_photo}`"
                                :alt="rating.user.name"
                                class="w-10 h-10 rounded-full object-cover"
                            />
                            <div v-else class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-blue-400 flex items-center justify-center text-white font-bold text-sm">
                                {{ rating.user.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ rating.user.name }}</p>
                                <p class="text-xs text-gray-500">{{ formatDate(rating.created_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Stars -->
                    <div class="flex gap-1 mb-3">
                        <svg v-for="i in 5" :key="i" :class="getStarClass(i - 1, rating.rating)" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>

                    <!-- Review Text -->
                    <p v-if="rating.review" class="text-gray-700 mb-3">{{ rating.review }}</p>

                    <!-- Helpful Button -->
                    <button 
                        @click="markHelpful(rating.id)"
                        class="text-sm text-gray-600 hover:text-purple-600 transition-colors flex items-center gap-1"
                    >
                        👍 Membantu ({{ rating.helpful_count }})
                    </button>
                </div>
            </div>

            <!-- No ratings -->
            <div v-if="!isLoading && ratings.length === 0" class="text-center py-8">
                <p class="text-gray-600">Belum ada rating. Jadilah yang pertama untuk memberikan rating!</p>
            </div>

            <!-- Loading -->
            <div v-if="isLoading" class="text-center py-8">
                <p class="text-gray-600">⏳ Memuat rating...</p>
            </div>
        </div>
    </div>
</template>
