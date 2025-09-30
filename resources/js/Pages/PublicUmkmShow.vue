<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

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
    user: {
        name: string;
        email: string;
    };
    menus: UmkmMenu[];
}

const props = defineProps<{
    umkm: Umkm;
}>();

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
                
                <div class="max-w-7xl mx-auto px-4 py-12 relative">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center space-x-4">
                            <Link 
                                :href="route('public.umkm.list')"
                                class="bg-white bg-opacity-20 backdrop-blur-sm text-white hover:bg-opacity-30 transition-all duration-200 p-2 rounded-lg border border-white border-opacity-30"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </Link>
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-2">{{ umkm.nama_umkm }}</h1>
                                <p class="text-white text-opacity-80">Detail informasi UMKM</p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <span :class="umkm.status === 'active' ? 'bg-green-500 bg-opacity-20 text-green-100 border-green-300' : 'bg-red-500 bg-opacity-20 text-red-100 border-red-300'" class="px-4 py-2 rounded-lg text-sm font-medium border border-opacity-30">
                                {{ umkm.status === 'active' ? 'UMKM Aktif' : 'UMKM Non-aktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="max-w-7xl mx-auto px-4 pb-12 relative -mt-8">
                <!-- UMKM Info Card -->
                <div class="bg-white bg-opacity-95 backdrop-blur-lg overflow-hidden shadow-2xl rounded-3xl border border-white border-opacity-50 mb-8">
                    <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-8 py-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-white mb-2">{{ umkm.nama_umkm }}</h2>
                                <div class="flex items-center space-x-4 text-purple-100">
                                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-medium">{{ umkm.kategori }}</span>
                                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-medium">
                                        {{ umkm.menus.length }} Menu
                                    </span>
                                </div>
                            </div>
                            <div v-if="umkm.gambar" class="w-24 h-24 rounded-xl overflow-hidden border-2 border-white border-opacity-30">
                                <img 
                                    :src="`/storage/${umkm.gambar}`" 
                                    :alt="umkm.nama_umkm"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Left Column - Basic Information -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
                                    <div class="space-y-3">
                                        <div class="flex items-start">
                                            <span class="w-4 h-4 text-gray-400 mt-1 mr-3">👤</span>
                                            <div>
                                                <p class="text-sm text-gray-500">Pemilik</p>
                                                <p class="font-medium text-gray-900">{{ umkm.user.name }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="w-4 h-4 text-gray-400 mt-1 mr-3">📍</span>
                                            <div>
                                                <p class="text-sm text-gray-500">Alamat</p>
                                                <p class="font-medium text-gray-900">{{ umkm.alamat }}</p>
                                            </div>
                                        </div>
                                        <div v-if="umkm.deskripsi" class="flex items-start">
                                            <span class="w-4 h-4 text-gray-400 mt-1 mr-3">📝</span>
                                            <div>
                                                <p class="text-sm text-gray-500">Deskripsi</p>
                                                <p class="font-medium text-gray-900">{{ umkm.deskripsi }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <span class="w-4 h-4 text-gray-400 mt-1 mr-3">📅</span>
                                            <div>
                                                <p class="text-sm text-gray-500">Terdaftar sejak</p>
                                                <p class="font-medium text-gray-900">{{ formatDate(umkm.created_at) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Contact Information -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kontak</h3>
                                    <div class="space-y-3">
                                        <div v-if="umkm.no_telepon" class="flex items-center">
                                            <span class="w-4 h-4 text-gray-400 mr-3">📞</span>
                                            <a :href="`tel:${umkm.no_telepon}`" class="font-medium text-green-600 hover:text-green-800 transition-colors">
                                                {{ umkm.no_telepon }}
                                            </a>
                                        </div>
                                        <div v-if="umkm.email" class="flex items-center">
                                            <span class="w-4 h-4 text-gray-400 mr-3">✉️</span>
                                            <a :href="`mailto:${umkm.email}`" class="font-medium text-blue-600 hover:text-blue-800 transition-colors">
                                                {{ umkm.email }}
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Social Media -->
                                <div v-if="umkm.facebook || umkm.instagram || umkm.twitter || umkm.whatsapp || umkm.website">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Media Sosial</h3>
                                    <div class="flex flex-wrap gap-3">
                                        <a v-if="umkm.facebook" 
                                           :href="`https://facebook.com/${umkm.facebook}`" 
                                           target="_blank" 
                                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                            Facebook
                                        </a>
                                        <a v-if="umkm.instagram" 
                                           :href="`https://instagram.com/${umkm.instagram}`" 
                                           target="_blank" 
                                           class="bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 transition-colors text-sm font-medium">
                                            Instagram
                                        </a>
                                        <a v-if="umkm.twitter" 
                                           :href="`https://twitter.com/${umkm.twitter}`" 
                                           target="_blank" 
                                           class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700 transition-colors text-sm font-medium">
                                            Twitter
                                        </a>
                                        <a v-if="umkm.whatsapp" 
                                           :href="`https://wa.me/${umkm.whatsapp}`" 
                                           target="_blank" 
                                           class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                                            WhatsApp
                                        </a>
                                        <a v-if="umkm.website" 
                                           :href="umkm.website" 
                                           target="_blank" 
                                           class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium">
                                            Website
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Section -->
                <div class="bg-white bg-opacity-95 backdrop-blur-lg overflow-hidden shadow-2xl rounded-3xl border border-white border-opacity-50">
                    <div class="px-8 py-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">Produk</h2>
                                <p class="text-sm text-gray-600 mt-1">{{ umkm.menus.length }}  tersedia</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Hubungi pemilik untuk pemesanan</p>
                                <div class="flex space-x-2 mt-2">
                                    <a v-if="umkm.no_telepon" 
                                       :href="`tel:${umkm.no_telepon}`" 
                                       class="bg-green-600 text-white px-3 py-1 rounded-md text-xs hover:bg-green-700 transition-colors">
                                        📞 Telepon
                                    </a>
                                    <a v-if="umkm.whatsapp" 
                                       :href="`https://wa.me/${umkm.whatsapp}`" 
                                       target="_blank"
                                       class="bg-green-500 text-white px-3 py-1 rounded-md text-xs hover:bg-green-600 transition-colors">
                                        💬 WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div v-if="umkm.menus.length === 0" class="text-center py-12">
                            <div class="text-6xl mb-4">🍽️</div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Menu</h3>
                            <p class="text-gray-500">UMKM ini belum menambahkan menu atau produk</p>
                        </div>

                        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <div v-for="menu in umkm.menus" :key="menu.id" class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                <!-- Menu Image -->
                                <div class="aspect-w-16 aspect-h-12 bg-gray-200">
                                    <img 
                                        v-if="menu.gambar_menu" 
                                        :src="`/storage/${menu.gambar_menu}`" 
                                        :alt="menu.nama_menu"
                                        class="w-full h-32 object-cover"
                                    >
                                    <div v-else class="w-full h-32 bg-gradient-to-br from-purple-100 to-blue-100 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Menu Info -->
                                <div class="p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-semibold text-gray-900 text-sm truncate flex-1">{{ menu.nama_menu }}</h4>
                                        <span :class="menu.tersedia ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-2">
                                            {{ menu.tersedia ? 'Tersedia' : 'Habis' }}
                                        </span>
                                    </div>
                                    
                                    <p v-if="menu.kategori_menu" class="text-xs text-gray-500 mb-2">{{ menu.kategori_menu }}</p>
                                    
                                    <p v-if="menu.deskripsi" class="text-gray-600 text-xs mb-3 line-clamp-2">{{ menu.deskripsi }}</p>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-bold text-purple-600">{{ formatCurrency(menu.harga) }}</span>
                                        <div class="text-xs text-gray-500">
                                            {{ menu.tersedia ? '✅ Tersedia' : '❌ Habis' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div v-if="umkm.menus.length > 0" class="mt-8 pt-6 border-t border-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-blue-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ umkm.menus.length }}</div>
                                    <div class="text-sm text-blue-800">Total Menu</div>
                                </div>
                                <div class="bg-green-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ umkm.menus.filter(menu => menu.tersedia).length }}</div>
                                    <div class="text-sm text-green-800">Menu Tersedia</div>
                                </div>
                                <div class="bg-yellow-50 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-yellow-600">{{ new Set(umkm.menus.map(menu => menu.kategori_menu).filter(Boolean)).size }}</div>
                                    <div class="text-sm text-yellow-800">Kategori Menu</div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-6">
                                <p class="text-sm text-gray-600 mb-4">
                                    Tertarik dengan menu ini? Hubungi pemilik UMKM untuk pemesanan
                                </p>
                                <div class="flex justify-center space-x-3">
                                    <a v-if="umkm.no_telepon" 
                                       :href="`tel:${umkm.no_telepon}`" 
                                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        Hubungi via Telepon
                                    </a>
                                    <a v-if="umkm.whatsapp" 
                                       :href="`https://wa.me/${umkm.whatsapp}`" 
                                       target="_blank"
                                       class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 font-medium text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.570-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                        </svg>
                                        Chat WhatsApp
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
</style>