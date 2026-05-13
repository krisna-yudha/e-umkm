<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import RatingSection from '@/Components/RatingSection.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import axios from 'axios';

interface User {
    id: number;
    name: string;
    email: string;
    role?: string;
    user_type?: string;
}

interface UmkmMenu {
    id: number;
    nama_menu: string;
    deskripsi?: string;
    harga: number;
    kategori_menu?: string;
    gambar_menu?: string;
    tersedia: boolean;
}

interface Umkm {
    id: number;
    nama_umkm: string;
    deskripsi: string;
    kategori: string;
    alamat: string;
    no_telepon: string;
    email: string;
    latitude?: number;
    longitude?: number;
    gambar?: string;
    status: string;
    created_at: string;
    facebook?: string;
    instagram?: string;
    twitter?: string;
    whatsapp?: string;
    website?: string;
    operating_hours?: any;
    user: {
        name: string;
        email: string;
        profile_photo?: string;
    };
    menus: UmkmMenu[];
}

const props = defineProps<{
    umkm: Umkm;
    auth?: {
        user: User | null;
    };
}>();

// Get auth from Inertia shared data
const page = usePage();
const currentUser = computed(() => (props.auth?.user ?? page.props.auth?.user) as User | null);

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatOperatingHours = (operatingHours: any) => {
    if (!operatingHours) {
        return ['Senin - Sabtu: 08:00 - 17:00', 'Minggu: Tutup'];
    }

    const days = {
        monday: 'Senin',
        tuesday: 'Selasa', 
        wednesday: 'Rabu',
        thursday: 'Kamis',
        friday: 'Jumat',
        saturday: 'Sabtu',
        sunday: 'Minggu'
    };

    const scheduleLines: string[] = [];
    const groupedHours: { [key: string]: string[] } = {};
    
    // Group days by their operating hours
    Object.entries(operatingHours).forEach(([day, hours]: [string, any]) => {
        const dayName = days[day as keyof typeof days];
        
        // Handle both format: isOpen/is_open, openTime/open_time, etc.
        const isOpen = hours.isOpen !== undefined ? hours.isOpen : hours.is_open;
        const is24Hours = hours.is24Hours !== undefined ? hours.is24Hours : hours.is_24_hours;
        const openTime = hours.openTime !== undefined ? hours.openTime : hours.open_time;
        const closeTime = hours.closeTime !== undefined ? hours.closeTime : hours.close_time;
        
        if (!isOpen) {
            scheduleLines.push(`${dayName}: Tutup`);
        } else {
            const timeRange = is24Hours ? '24 Jam' : `${openTime} - ${closeTime}`;
            
            if (!groupedHours[timeRange]) {
                groupedHours[timeRange] = [];
            }
            groupedHours[timeRange].push(dayName);
        }
    });

    // Format grouped hours
    Object.entries(groupedHours).forEach(([timeRange, dayList]) => {
        if (dayList.length === 1) {
            scheduleLines.push(`${dayList[0]}: ${timeRange}`);
        } else {
            // Sort days in week order
            const dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            const sortedDays = dayList.sort((a, b) => dayOrder.indexOf(a) - dayOrder.indexOf(b));
            
            // Check for consecutive days
            const consecutiveGroups: string[][] = [];
            let currentGroup = [sortedDays[0]];
            
            for (let i = 1; i < sortedDays.length; i++) {
                const currentIndex = dayOrder.indexOf(sortedDays[i]);
                const prevIndex = dayOrder.indexOf(sortedDays[i-1]);
                
                if (currentIndex === prevIndex + 1) {
                    currentGroup.push(sortedDays[i]);
                } else {
                    consecutiveGroups.push(currentGroup);
                    currentGroup = [sortedDays[i]];
                }
            }
            consecutiveGroups.push(currentGroup);
            
            // Format consecutive groups
            consecutiveGroups.forEach(group => {
                if (group.length === 1) {
                    scheduleLines.push(`${group[0]}: ${timeRange}`);
                } else if (group.length >= 2) {
                    scheduleLines.push(`${group[0]} - ${group[group.length - 1]}: ${timeRange}`);
                }
            });
        }
    });

    return scheduleLines;
};

// Modal state for product details
const showDetailModal = ref(false);
const selectedMenuDetail = ref<UmkmMenu | null>(null);

// Wishlist state
const isWishlisted = ref(false);
const wishlistCount = ref(0);

// Function to show menu detail modal
const showMenuDetail = (menu: UmkmMenu) => {
    selectedMenuDetail.value = menu;
    showDetailModal.value = true;
};

// Function to close detail modal
const closeDetailModal = () => {
    showDetailModal.value = false;
    selectedMenuDetail.value = null;
};

// Wishlist functions
const toggleWishlist = async () => {
    if (!currentUser.value) {
        window.location.href = '/login/user';
        return;
    }
    
    if (isWishlisted.value) {
        await removeFromWishlist();
    } else {
        await addToWishlist();
    }
};

const addToWishlist = async () => {
    if (!currentUser.value) {
        window.location.href = '/login/user';
        return;
    }
    
    try {
        console.log('Adding to wishlist, UMKM ID:', props.umkm.id);
        const response = await axios.post(`/api/v1/umkms/${props.umkm.id}/wishlist`);
        
        console.log('Wishlist add response:', response.data);
        isWishlisted.value = true;
        alert('✅ Berhasil ditambahkan ke wishlist!');
    } catch (error: any) {
        console.error('Error adding to wishlist:', error);
        if (error.response?.status === 401) {
            alert('❌ Sesi Anda telah berakhir. Silakan login kembali.');
            window.location.href = '/login/user';
        } else if (error.response?.data?.message) {
            alert('❌ Error: ' + error.response.data.message);
        } else {
            alert('❌ Gagal menambahkan ke wishlist. Silakan coba lagi.');
        }
    }
};

const removeFromWishlist = async () => {
    try {
        console.log('Removing from wishlist, UMKM ID:', props.umkm.id);
        const response = await axios.delete(`/api/v1/umkms/${props.umkm.id}/wishlist`);
        
        console.log('Wishlist remove response:', response.data);
        isWishlisted.value = false;
        alert('✅ Berhasil dihapus dari wishlist!');
    } catch (error: any) {
        console.error('Error removing from wishlist:', error);
        if (error.response?.status === 401) {
            alert('❌ Sesi Anda telah berakhir. Silakan login kembali.');
            window.location.href = '/login/user';
        } else if (error.response?.data?.message) {
            alert('❌ Error: ' + error.response.data.message);
        } else {
            alert('❌ Gagal menghapus dari wishlist. Silakan coba lagi.');
        }
    }
};

const checkWishlistStatus = async () => {
    if (!currentUser.value) {
        isWishlisted.value = false;
        return;
    }
    
    try {
        console.log('Checking wishlist status for UMKM:', props.umkm.id);
        // Call axios (dengan session-based auth)
        const response = await axios.get(`/api/v1/umkms/${props.umkm.id}/wishlist/check`);
        
        isWishlisted.value = response.data.wishlisted || false;
        console.log('Wishlist status:', isWishlisted.value);
    } catch (error: any) {
        console.error('Error checking wishlist status:', error);
        // Jika error, asumsikan tidak di-wishlist
        isWishlisted.value = false;
    }
};

const saveToWishlist = async () => {
    if (!currentUser.value) {
        window.location.href = '/login/user';
        return;
    }
    
    await addToWishlist();
};

// Check wishlist status on component mount
onMounted(() => {
    checkWishlistStatus();
});
</script>

<template>
    <Head :title="`${umkm.nama_umkm} - Detail UMKM`" />

    <GuestLayout>
        <!-- Background dengan gradient yang konsisten -->
        <div class="min-h-screen" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <!-- Header Section -->
            <div class="text-white relative overflow-hidden">
                <!-- Background pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.15) 1px, transparent 0); background-size: 20px 20px;"></div>
                </div>
                
                <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-6 sm:py-10 md:py-12 relative">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                        <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                            <Link 
                                :href="route('public.umkm.list')"
                                class="bg-white bg-opacity-20 backdrop-blur-sm text-white hover:bg-opacity-30 transition-all duration-200 p-2.5 rounded-lg border border-white border-opacity-30 flex-shrink-0"
                            >
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </Link>
                            <div class="min-w-0">
                                <h1 class="text-lg sm:text-2xl md:text-3xl lg:text-4xl font-bold text-white mb-0.5 sm:mb-1 line-clamp-2">{{ umkm.nama_umkm }}</h1>
                                <p class="text-xs sm:text-sm text-white text-opacity-90">Detail informasi UMKM</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <span :class="umkm.status === 'active' ? 'bg-green-500 bg-opacity-20 text-green-100 border-green-300' : 'bg-red-500 bg-opacity-20 text-red-100 border-red-300'" class="inline-block px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold border border-opacity-30 whitespace-nowrap">
                                {{ umkm.status === 'active' ? '✅ Aktif' : '❌ Non-aktif' }}
                            </span>
                            <button 
                                @click="toggleWishlist"
                                :class="isWishlisted ? 'bg-red-500 text-white' : 'bg-white bg-opacity-20 text-white hover:bg-opacity-30'"
                                class="ml-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold border border-white border-opacity-30 transition-all duration-200"
                            >
                                {{ isWishlisted ? '❤️ Wishlist' : '🤍 Wishlist' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 pb-8 sm:pb-12 lg:pb-16 relative -mt-6 sm:-mt-8 md:-mt-10">
                <!-- UMKM Info Card (Hidden for Restaurant/Food UMKM) -->
                <div v-if="!umkm.kategori.toLowerCase().includes('makanan') && !umkm.kategori.toLowerCase().includes('minuman') && !umkm.kategori.toLowerCase().includes('restoran')" class="bg-white bg-opacity-95 backdrop-blur-lg overflow-hidden shadow-2xl rounded-2xl md:rounded-3xl border border-white border-opacity-50 mb-6 sm:mb-8 lg:mb-10">
                    <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-4 sm:px-6 md:px-8 py-6 sm:py-8 md:py-10">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <h2 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-white mb-2 sm:mb-3 line-clamp-2">{{ umkm.nama_umkm }}</h2>
                                <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                                    <div class="flex items-center bg-white bg-opacity-20 px-3 py-1.5 rounded-full hover:bg-opacity-30 transition-all">
                                        <span class="text-xs sm:text-sm font-semibold text-white">📁 {{ umkm.kategori }}</span>
                                    </div>
                                </div>
                            </div>
                            <div v-if="umkm.gambar" class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-xl overflow-hidden border-3 border-white border-opacity-40 flex-shrink-0 shadow-lg">
                                <img 
                                    :src="`/storage/${umkm.gambar}`" 
                                    :alt="umkm.nama_umkm"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 md:p-8 lg:p-12 xl:p-16">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-10 lg:gap-16 xl:gap-20">
                            <!-- Left Column - Basic Information -->
                            <div class="space-y-6 sm:space-y-8 lg:space-y-10">
                                <div>
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 lg:mb-8 flex items-center gap-2"><span class="text-2xl">ℹ️</span> Informasi Dasar</h3>
                                    <div class="space-y-4 sm:space-y-5 lg:space-y-6">
                                        <!-- Owner Info -->
                                        <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg border border-blue-100">
                                            <div class="flex-shrink-0">
                                                <img v-if="umkm.user.profile_photo" 
                                                     :src="`/storage/${umkm.user.profile_photo}`" 
                                                     :alt="umkm.user.name"
                                                     class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border-2 border-white shadow-sm"
                                                />
                                                <div v-else 
                                                     class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center border-2 border-white shadow-sm">
                                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs sm:text-sm text-gray-500 font-medium uppercase tracking-wide">Pemilik</p>
                                                <p class="font-bold text-gray-900 text-sm sm:text-base break-words">{{ umkm.user.name }}</p>
                                                <p class="text-xs text-purple-600 font-semibold mt-0.5">👑 Pelaku UMKM</p>
                                            </div>
                                        </div>

                                        <!-- Alamat -->
                                        <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-red-50 rounded-lg flex items-center justify-center text-lg">📍</div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs sm:text-sm text-gray-500 font-medium uppercase tracking-wide">Alamat</p>
                                                <p class="font-medium text-gray-900 text-sm sm:text-base break-words">{{ umkm.alamat }}</p>
                                            </div>
                                        </div>

                                        <!-- Deskripsi -->
                                        <div v-if="umkm.deskripsi" class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-yellow-50 rounded-lg flex items-center justify-center text-lg">📝</div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs sm:text-sm text-gray-500 font-medium uppercase tracking-wide">Deskripsi</p>
                                                <p class="font-medium text-gray-900 text-sm sm:text-base break-words">{{ umkm.deskripsi }}</p>
                                            </div>
                                        </div>

                                        <!-- Terdaftar Sejak -->
                                        <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-green-50 rounded-lg flex items-center justify-center text-lg">📅</div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs sm:text-sm text-gray-500 font-medium uppercase tracking-wide">Terdaftar sejak</p>
                                                <p class="font-medium text-gray-900 text-sm sm:text-base">{{ formatDate(umkm.created_at) }}</p>
                                            </div>
                                        </div>

                                        <!-- Jam Operasional -->
                                        <div class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-purple-50 rounded-lg flex items-center justify-center text-lg">🕒</div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs sm:text-sm text-gray-500 font-medium uppercase tracking-wide">Jam Operasional</p>
                                                <div v-if="formatOperatingHours(umkm.operating_hours).length > 0" class="space-y-0.5 mt-1">
                                                    <p v-for="schedule in formatOperatingHours(umkm.operating_hours)" :key="schedule" class="font-medium text-gray-900 text-sm sm:text-base">{{ schedule }}</p>
                                                </div>
                                                <div v-else>
                                                    <p class="font-medium text-gray-900 text-sm sm:text-base">Senin - Sabtu: 08:00 - 17:00</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">Minggu: Tutup</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Contact Information -->
                            <div class="space-y-6 sm:space-y-8 lg:space-y-10">
                                <div>
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 lg:mb-8 flex items-center gap-2"><span class="text-2xl">📞</span> Informasi Kontak</h3>
                                    <div class="space-y-4 sm:space-y-5 lg:space-y-6">
                                        <!-- Telepon -->
                                        <div v-if="umkm.no_telepon" class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-green-50 rounded-lg flex items-center justify-center text-lg">📱</div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs sm:text-sm text-gray-500 font-medium uppercase tracking-wide">Telepon</p>
                                                <a :href="`tel:${umkm.no_telepon}`" class="font-bold text-green-600 hover:text-green-800 transition-colors text-sm sm:text-base break-all">
                                                    {{ umkm.no_telepon }}
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div v-if="umkm.email" class="flex gap-3 sm:gap-4 p-3 sm:p-4 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 bg-blue-50 rounded-lg flex items-center justify-center text-lg">✉️</div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs sm:text-sm text-gray-500 font-medium uppercase tracking-wide">Email</p>
                                                <a :href="`mailto:${umkm.email}`" class="font-bold text-blue-600 hover:text-blue-800 transition-colors text-sm sm:text-base break-all">
                                                    {{ umkm.email }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Social Media -->
                                <div v-if="umkm.facebook || umkm.instagram || umkm.twitter || umkm.whatsapp || umkm.website" class="mt-6 sm:mt-8 lg:mt-10 pt-6 sm:pt-8 lg:pt-10 border-t border-gray-200">
                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6 lg:mb-8 flex items-center gap-2"><span class="text-2xl">🌐</span> Media Sosial</h3>
                                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-3 sm:gap-4">
                                        <a v-if="umkm.facebook" 
                                           :href="`https://facebook.com/${umkm.facebook}`" 
                                           target="_blank" 
                                           class="bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-xs sm:text-sm font-medium text-center">
                                            Facebook
                                        </a>
                                        <a v-if="umkm.instagram" 
                                           :href="`https://instagram.com/${umkm.instagram}`" 
                                           target="_blank" 
                                           class="bg-pink-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-pink-700 transition-colors text-xs sm:text-sm font-medium text-center">
                                            Instagram
                                        </a>
                                        <a v-if="umkm.twitter" 
                                           :href="`https://twitter.com/${umkm.twitter}`" 
                                           target="_blank" 
                                           class="bg-sky-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-sky-700 transition-colors text-xs sm:text-sm font-medium text-center">
                                            Twitter
                                        </a>
                                        <a v-if="umkm.whatsapp" 
                                           :href="`https://wa.me/${umkm.whatsapp}`" 
                                           target="_blank" 
                                           class="bg-green-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-xs sm:text-sm font-medium text-center">
                                            WhatsApp
                                        </a>
                                        <a v-if="umkm.website" 
                                           :href="umkm.website" 
                                           target="_blank" 
                                           class="bg-gray-600 text-white px-3 sm:px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-xs sm:text-sm font-medium text-center">
                                            Website
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Section -->
                <div class="bg-white bg-opacity-95 backdrop-blur-lg overflow-hidden shadow-2xl rounded-2xl md:rounded-3xl border border-white border-opacity-50">
                    <div class="px-3 sm:px-6 md:px-8 lg:px-12 xl:px-16 py-4 sm:py-8 md:py-10 lg:py-12 border-b border-gray-100 bg-gradient-to-r from-gray-50 via-white to-gray-50">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 lg:gap-6">
                            <div class="flex-1">
                                <h2 class="text-base sm:text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 flex items-center gap-2 sm:gap-3">
                                    <span class="text-2xl sm:text-3xl lg:text-4xl animate-bounce" style="animation-delay: 0s">🍽️</span>
                                    <span class="bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">Menu</span>
                                </h2>
                                <p class="text-xs sm:text-base text-gray-600 mt-1.5 sm:mt-2 lg:mt-3 ml-8 sm:ml-12 font-medium">{{ umkm.menus.length }} produk</p>
                            </div>
                            <!-- <div class="sm:text-right">
                                <div class="bg-gradient-to-r from-purple-50 to-blue-50 px-3 sm:px-4 py-2 rounded-lg">
                                    <p class="text-xs sm:text-sm font-medium text-gray-700">💬 Hubungi untuk pemesanan</p>
                                    <div class="flex space-x-2 mt-2">
                                    <a v-if="umkm.no_telepon" 
                                       :href="`tel:${umkm.no_telepon}`" 
                                       class="bg-green-600 text-white px-2 sm:px-3 py-1 rounded-md text-xs hover:bg-green-700 transition-colors">
                                        📞 Telepon
                                    </a>
                                    <a v-if="umkm.whatsapp" 
                                       :href="`https://wa.me/${umkm.whatsapp}`" 
                                       target="_blank"
                                       class="bg-green-500 text-white px-2 sm:px-3 py-1 rounded-md text-xs hover:bg-green-600 transition-colors">
                                        💬 WhatsApp
                                    </a>
                                </div>
                                </div>
                            </div> -->
                        </div>
                    </div>

                    <div class="p-3 sm:p-6 md:p-8 lg:p-12 xl:p-16">
                        <div v-if="umkm.menus.length === 0" class="text-center py-8 sm:py-16 md:py-20 lg:py-24">
                            <div class="text-4xl sm:text-7xl lg:text-8xl mb-3 sm:mb-6 lg:mb-8 animate-bounce inline-block">🍽️</div>
                            <h3 class="text-base sm:text-xl md:text-2xl lg:text-3xl font-semibold text-gray-900 mb-1.5 lg:mb-4">Belum Ada Menu</h3>
                            <p class="text-xs sm:text-base lg:text-lg text-gray-500 max-w-md mx-auto font-medium px-3">UMKM ini segera menambahkan produk</p>
                            <div class="mt-4 sm:mt-6 lg:mt-8">
                                <div class="inline-block px-4 py-2 sm:px-6 sm:py-3 bg-gradient-to-r from-purple-100 to-blue-100 rounded-lg border border-purple-200 text-xs sm:text-sm lg:text-base font-medium text-purple-700">
                                    ⏳ Menu sedang disiapkan
                                </div>
                            </div>
                        </div>

                        <div v-else class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-5 md:gap-6 lg:gap-8">
                            <div 
                                v-for="menu in umkm.menus" 
                                :key="menu.id" 
                                @click="showMenuDetail(menu)"
                                class="group bg-white rounded-lg sm:rounded-2xl overflow-hidden cursor-pointer transform transition-all duration-300 hover:sm:scale-105 flex flex-col shadow-md sm:shadow-lg hover:sm:shadow-2xl border border-gray-200 hover:sm:border-transparent active:scale-95 sm:active:scale-100"
                                style="background: linear-gradient(135deg, rgba(255,255,255,0.98) 0%, rgba(249,250,251,0.98) 100%)"
                            >
                                <!-- Menu Image -->
                                <div class="relative bg-gradient-to-br from-purple-100 to-blue-100 overflow-hidden w-full aspect-square sm:aspect-square">
                                    <img 
                                        v-if="menu.gambar_menu" 
                                        :src="`/storage/${menu.gambar_menu}`" 
                                        :alt="menu.nama_menu"
                                        class="w-full h-full object-cover group-hover:sm:scale-110 transition-transform duration-500"
                                    >
                                    <div v-else class="w-full h-full bg-gradient-to-br from-purple-100 via-blue-50 to-indigo-100 flex items-center justify-center">
                                        <svg class="w-10 h-10 sm:w-14 sm:h-14 text-purple-300 group-hover:sm:text-purple-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <!-- Status Badge - Mobile Optimized -->
                                    <div class="absolute inset-0 flex items-end justify-between p-2 sm:p-3 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 sm:opacity-0 group-hover:sm:opacity-40 transition-opacity duration-300">
                                    </div>
                                    <div class="absolute top-2 right-2 sm:top-3 sm:right-3 z-10">
                                        <span :class="menu.tersedia ? 'bg-gradient-to-r from-green-400 to-green-600 text-white shadow-md' : 'bg-gradient-to-r from-red-400 to-red-600 text-white shadow-md'" class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 rounded-full text-xs font-bold backdrop-blur-sm border border-white border-opacity-40">
                                            {{ menu.tersedia ? '✅' : '❌' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Menu Info - Mobile Optimized -->
                                <div class="p-3 sm:p-5 md:p-6 lg:p-7 flex flex-col flex-1">
                                    <h4 class="font-bold text-gray-900 text-xs sm:text-base lg:text-lg line-clamp-2 mb-1.5 sm:mb-2 lg:mb-3 group-hover:sm:text-purple-600 transition-colors duration-300">{{ menu.nama_menu }}</h4>
                                    
                                    <p v-if="menu.kategori_menu" class="text-xs text-purple-600 font-semibold mb-1.5 sm:mb-2 lg:mb-3 uppercase tracking-wide hidden sm:block">{{ menu.kategori_menu }}</p>
                                    
                                    <p v-if="menu.deskripsi" class="text-gray-600 text-xs sm:text-sm mb-2 sm:mb-3 lg:mb-4 line-clamp-2 flex-1 hidden sm:block">{{ menu.deskripsi }}</p>
                                    
                                    <div class="flex flex-col gap-2 sm:gap-3">
                                        <!-- Price - More Prominent on Mobile -->
                                        <div class="w-full">
                                            <p class="text-xs text-gray-500 font-medium mb-1 hidden sm:block">Harga</p>
                                            <span class="text-sm sm:text-base lg:text-lg font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent block">{{ formatCurrency(menu.harga) }}</span>
                                        </div>
                                        
                                        <!-- Status Text Mobile -->
                                        <div class="text-center sm:text-left pt-2 sm:pt-3 border-t border-gray-100">
                                            <div class="text-xs font-semibold px-3 py-1.5 rounded-full inline-block" :class="menu.tersedia ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'">
                                                {{ menu.tersedia ? '✓ Tersedia' : '✗ Habis' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Call to Action untuk mendukung UMKM -->
                        <div v-if="umkm.menus.length > 0 && (!umkm.kategori.toLowerCase().includes('makanan') && !umkm.kategori.toLowerCase().includes('minuman') && !umkm.kategori.toLowerCase().includes('restoran'))" class="mt-10 sm:mt-12 md:mt-16 lg:mt-20 pt-8 sm:pt-10 lg:pt-12 border-t border-gray-100">
                            <div class="text-center">
                                <div class="relative overflow-hidden rounded-xl sm:rounded-2xl lg:rounded-3xl p-6 sm:p-8 md:p-10 lg:p-14 border-2 border-transparent shadow-lg lg:shadow-2xl group hover:shadow-2xl transition-all duration-300"
                                     style="background: linear-gradient(135deg, rgba(191, 219, 254, 0.4) 0%, rgba(196, 181, 253, 0.4) 50%, rgba(251, 191, 223, 0.4) 100%); border-image: linear-gradient(135deg, #667eea, #764ba2, #f093fb) 1">
                                    <!-- Animated background -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-blue-50 via-purple-50 to-pink-50 opacity-50 group-hover:opacity-80 transition-opacity duration-300"></div>
                                    <div class="relative z-10">
                                        <h3 class="text-lg sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 mb-2 sm:mb-4 lg:mb-6 flex items-center justify-center gap-2">
                                            <span class="text-xl sm:text-3xl lg:text-4xl animate-pulse">🌟</span>
                                            <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent hidden sm:inline">Dukung UMKM Lokal</span>
                                            <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent sm:hidden">Dukung UMKM</span>
                                        </h3>
                                        <p class="text-xs sm:text-base lg:text-lg text-gray-700 max-w-2xl mx-auto leading-relaxed font-medium">
                                            Belanja di UMKM lokal untuk mendukung ekonomi sekitar. Setiap pembelian Anda membantu!
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Kontak Options
                                <div class="flex justify-center space-x-3">
                                    <a v-if="umkm.no_telepon" 
                                       :href="`tel:${umkm.no_telepon}`" 
                                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium text-sm shadow-md">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        Telepon
                                    </a>
                                    <a v-if="umkm.whatsapp" 
                                       :href="`https://wa.me/${umkm.whatsapp}`" 
                                       target="_blank"
                                       class="inline-flex items-center px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 font-medium text-sm shadow-md">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.570-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                        </svg>
                                        WhatsApp
                                    </a>
                                </div> -->
                            </div>
                        </div>

                        <!-- Rating Section (For Restaurant/Food UMKM) -->
                        <div v-if="umkm.kategori.toLowerCase().includes('makanan') || umkm.kategori.toLowerCase().includes('minuman') || umkm.kategori.toLowerCase().includes('restoran')" class="mt-10 sm:mt-12 md:mt-16 lg:mt-20">
                            <RatingSection 
                                :umkm="umkm"
                                :current-user="currentUser"
                            />
                        </div>
                    </div>
                </div>

                <!-- Rating Section (For Other UMKM) -->
                <div v-if="!umkm.kategori.toLowerCase().includes('makanan') && !umkm.kategori.toLowerCase().includes('minuman') && !umkm.kategori.toLowerCase().includes('restoran')" class="mt-6 sm:mt-8 lg:mt-10">
                    <RatingSection 
                        :umkm="umkm"
                        :current-user="currentUser"
                    />
                </div>
            </div>
        </div>

        <!-- Product Detail Modal -->
        <transition name="fade">
            <div v-if="showDetailModal && selectedMenuDetail" class="fixed inset-0 z-50 overflow-y-auto" @click="closeDetailModal">
                <div class="flex items-center justify-center min-h-screen px-4 py-6 sm:p-0">
                    <!-- Backdrop -->
                    <transition name="fade">
                        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
                    </transition>

                    <!-- Modal Panel -->
                    <div @click.stop class="relative bg-white rounded-xl sm:rounded-2xl overflow-hidden shadow-2xl transform transition-all my-8 w-full max-w-md sm:max-w-lg md:max-w-xl">
                        <!-- Modal Header with Image -->
                        <div class="relative overflow-hidden bg-gray-200">
                            <img 
                                v-if="selectedMenuDetail.gambar_menu" 
                                :src="`/storage/${selectedMenuDetail.gambar_menu}`" 
                                :alt="selectedMenuDetail.nama_menu"
                                class="w-full h-56 sm:h-72 md:h-80 object-cover"
                            >
                            <div v-else class="w-full h-56 sm:h-72 md:h-80 bg-gradient-to-br from-purple-100 to-blue-100 flex items-center justify-center">
                                <svg class="w-16 h-16 sm:w-20 sm:h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            
                            <!-- Close Button -->
                            <button 
                                @click="closeDetailModal"
                                class="absolute top-3 right-3 sm:top-4 sm:right-4 bg-white bg-opacity-95 hover:bg-opacity-100 text-gray-600 hover:text-gray-800 rounded-full p-2 sm:p-3 transition-all duration-200 shadow-lg hover:shadow-xl"
                            >
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>

                            <!-- Status Badge -->
                            <div class="absolute top-3 left-3 sm:top-4 sm:left-4">
                                <span :class="selectedMenuDetail.tersedia ? 'bg-green-500 text-white' : 'bg-red-500 text-white'" class="inline-flex items-center px-3 py-1.5 rounded-full text-xs sm:text-sm font-bold shadow-lg">
                                    {{ selectedMenuDetail.tersedia ? '✅ Tersedia' : '❌ Habis' }}
                                </span>
                            </div>
                        </div>

                        <!-- Modal Content -->
                        <div class="bg-white px-3 sm:px-6 md:px-8 lg:px-10 py-5 sm:py-8 md:py-10 lg:py-12">
                            <!-- Product Title & Category -->
                            <div class="mb-3 sm:mb-6 lg:mb-8">
                                <h3 class="text-lg sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 leading-tight mb-2 sm:mb-3 lg:mb-4">{{ selectedMenuDetail.nama_menu }}</h3>
                                <div v-if="selectedMenuDetail.kategori_menu">
                                    <span class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-1.5 rounded-full text-xs sm:text-sm font-semibold bg-purple-100 text-purple-800 uppercase">
                                        {{ selectedMenuDetail.kategori_menu }}
                                    </span>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="mb-4 sm:mb-8 lg:mb-10">
                                <p class="text-xs sm:text-sm lg:text-base text-gray-500 mb-1 sm:mb-2 lg:mb-3 font-semibold uppercase">Harga</p>
                                <div class="text-2xl sm:text-4xl lg:text-5xl font-bold text-purple-600">{{ formatCurrency(selectedMenuDetail.harga) }}</div>
                            </div>

                            <!-- Description -->
                            <div v-if="selectedMenuDetail.deskripsi" class="mb-4 sm:mb-8 lg:mb-10">
                                <h4 class="text-xs sm:text-base lg:text-lg font-bold text-gray-800 mb-2 sm:mb-3 lg:mb-4">📝 Deskripsi</h4>
                                <p class="text-gray-700 leading-relaxed text-xs sm:text-base lg:text-lg">{{ selectedMenuDetail.deskripsi }}</p>
                            </div>

                            <!-- Contact Actions -->
                            <div v-if="umkm.whatsapp" class="flex flex-col space-y-2 sm:space-y-3 lg:space-y-4">
                                <p class="text-xs sm:text-sm lg:text-base text-gray-500 text-center font-semibold uppercase">💬 Pesan Sekarang</p>
                                <a :href="`https://wa.me/${umkm.whatsapp}?text=Halo, saya tertarik dengan produk ${selectedMenuDetail.nama_menu} dari ${umkm.nama_umkm}`" 
                                   target="_blank"
                                   class="w-full inline-flex items-center justify-center px-3 sm:px-6 lg:px-8 py-2 sm:py-4 lg:py-5 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 font-bold text-xs sm:text-base lg:text-lg shadow-md hover:shadow-lg">
                                    <svg class="w-4 h-4 sm:w-6 sm:h-6 mr-1.5 sm:mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.570-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                    </svg>
                                    Hubungi via WhatsApp
                                </a>
                            </div>
                            <div v-else-if="umkm.no_telepon" class="flex flex-col space-y-2 sm:space-y-3 lg:space-y-4">
                                <p class="text-xs sm:text-sm lg:text-base text-gray-500 text-center font-semibold uppercase">📞 Hubungi Sekarang</p>
                                <a :href="`tel:${umkm.no_telepon}`" 
                                   class="w-full inline-flex items-center justify-center px-3 sm:px-6 lg:px-8 py-2 sm:py-4 lg:py-5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-bold text-xs sm:text-base lg:text-lg shadow-md hover:shadow-lg">
                                    <svg class="w-4 h-4 sm:w-6 sm:h-6 mr-1.5 sm:mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    Telepon
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
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

/* Fade transition for modal */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Bounce animation for menu icon */
@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
    }
}

.animate-bounce {
    animation: bounce 2s infinite;
}

/* Pulse animation for star */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Card glow effect on hover */
@keyframes cardGlow {
    0% {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
    50% {
        box-shadow: 0 20px 40px -10px rgba(102, 126, 234, 0.2), 0 0 20px rgba(118, 75, 162, 0.15);
    }
    100% {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
}

/* Enhanced text gradient effect */
@keyframes textGradientShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.text-gradient-animated {
    background: linear-gradient(270deg, #667eea, #764ba2, #f093fb, #667eea);
    background-size: 300% 300%;
    animation: textGradientShift 3s ease infinite;
}

/* Android WebView optimizations */
@media screen and (max-width: 640px) {
    /* Ensure proper touch targets */
    button, a {
        min-height: 44px;
        min-width: 44px;
    }
    
    /* Prevent text from being too small */
    body {
        -webkit-text-size-adjust: 100%;
    }
    
    /* Smooth scrolling for mobile */
    * {
        -webkit-overflow-scrolling: touch;
    }
    
    /* Better spacing on mobile */
    h1, h2, h3 {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
}

/* Tablet optimizations */
@media screen and (min-width: 640px) and (max-width: 1024px) {
    .grid {
        gap: 1.5rem;
    }
}

/* Desktop optimizations */
@media screen and (min-width: 1024px) {
    /* Smoother transitions on desktop */
    * {
        transition-duration: 300ms;
    }
}

/* Image hover effect */
img {
    transition: transform 0.3s ease;
}

/* Card hover effects */
.group {
    transition: all 0.3s ease;
}

/* Ensure modal is responsive */
@media screen and (max-width: 640px) {
    .fixed {
        position: fixed;
        max-width: 100vw;
    }
}
</style>