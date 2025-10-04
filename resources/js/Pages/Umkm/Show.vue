<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
    deskripsi?: string;
    kategori: string;
    alamat: string;
    no_telepon?: string;
    email?: string;
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
    user?: {
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
    <Head :title="`Detail ${umkm.nama_umkm}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detail UMKM: {{ umkm.nama_umkm }}
                </h2>
                <div class="flex space-x-4">
                    <Link
                        :href="route('umkm.edit', umkm.id)"
                        class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Edit
                    </Link>
                    <Link
                        :href="route('umkm.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Kembali
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Left Column - Image and Basic Info -->
                            <div>
                                <div class="mb-6">
                                    <div v-if="umkm.gambar" class="w-full h-64 overflow-hidden rounded-lg shadow-lg">
                                        <img 
                                            :src="`/storage/${umkm.gambar}`" 
                                            :alt="umkm.nama_umkm"
                                            class="w-full h-full object-cover"
                                        />
                                    </div>
                                    <div v-else class="w-full h-64 bg-gray-200 flex items-center justify-center rounded-lg">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Informasi Dasar</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Status:</span>
                                                <span 
                                                    :class="umkm.status === 'active' ? 'text-green-600' : 'text-red-600'"
                                                    class="font-semibold"
                                                >
                                                    {{ umkm.status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Kategori:</span>
                                                <span class="font-medium">{{ umkm.kategori }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Didaftarkan:</span>
                                                <span class="font-medium">{{ new Date(umkm.created_at).toLocaleDateString('id-ID') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="umkm.user">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Pemilik UMKM</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Nama:</span>
                                                <span class="font-medium">{{ umkm.user.name }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Email:</span>
                                                <span class="font-medium">{{ umkm.user.email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Details -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Detail UMKM</h3>
                                    
                                    <div v-if="umkm.deskripsi" class="mb-4">
                                        <h4 class="font-medium text-gray-700 mb-2">Deskripsi</h4>
                                        <p class="text-gray-600 leading-relaxed">{{ umkm.deskripsi }}</p>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <h4 class="font-medium text-gray-700 mb-2">Alamat</h4>
                                            <div class="flex items-start">
                                                <svg class="w-5 h-5 text-gray-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <p class="text-gray-600">{{ umkm.alamat }}</p>
                                            </div>
                                        </div>

                                        <div v-if="umkm.no_telepon">
                                            <h4 class="font-medium text-gray-700 mb-2">No. Telepon</h4>
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                                <a :href="`tel:${umkm.no_telepon}`" class="text-blue-600 hover:text-blue-800">{{ umkm.no_telepon }}</a>
                                            </div>
                                        </div>

                                        <div v-if="umkm.email">
                                            <h4 class="font-medium text-gray-700 mb-2">Email</h4>
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                <a :href="`mailto:${umkm.email}`" class="text-blue-600 hover:text-blue-800">{{ umkm.email }}</a>
                                            </div>
                                        </div>

                                        <div v-if="umkm.latitude && umkm.longitude">
                                            <h4 class="font-medium text-gray-700 mb-2">Koordinat</h4>
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                                </svg>
                                                <span class="text-gray-600">{{ umkm.latitude }}, {{ umkm.longitude }}</span>
                                            </div>
                                            <Link
                                                :href="route('mapping')"
                                                class="inline-flex items-center mt-2 text-sm text-blue-600 hover:text-blue-800"
                                            >
                                                Lihat di Peta
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </Link>
                                        </div>

                                        <!-- Social Media Links -->
                                        <div v-if="umkm.facebook || umkm.instagram || umkm.twitter || umkm.whatsapp || umkm.website">
                                            <h4 class="font-medium text-gray-700 mb-3">Media Sosial</h4>
                                            <div class="flex items-center space-x-3">
                                                <a v-if="umkm.facebook" :href="umkm.facebook" target="_blank" class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white hover:bg-blue-700 transition-colors duration-200" title="Facebook">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                    </svg>
                                                </a>
                                                <a v-if="umkm.instagram" :href="umkm.instagram" target="_blank" class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center text-white hover:from-purple-600 hover:to-pink-600 transition-all duration-200" title="Instagram">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.329-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.412-3.329c.881-.881 2.032-1.412 3.329-1.412s2.448.531 3.329 1.412c.881.881 1.412 2.032 1.412 3.329s-.531 2.448-1.412 3.329c-.881.807-2.032 1.297-3.329 1.297zm7.83-9.476h-1.78V6.524h1.78v.988zm-.612 5.027c0-1.09-.881-1.971-1.971-1.971s-1.971.881-1.971 1.971.881 1.971 1.971 1.971 1.971-.881 1.971-1.971z"/>
                                                    </svg>
                                                </a>
                                                <a v-if="umkm.twitter" :href="umkm.twitter" target="_blank" class="w-10 h-10 rounded-full bg-sky-500 flex items-center justify-center text-white hover:bg-sky-600 transition-colors duration-200" title="Twitter">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                                    </svg>
                                                </a>
                                                <a v-if="umkm.whatsapp" :href="umkm.whatsapp" target="_blank" class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white hover:bg-green-600 transition-colors duration-200" title="WhatsApp">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.570-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                                    </svg>
                                                </a>
                                                <a v-if="umkm.website" :href="umkm.website" target="_blank" class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center text-white hover:bg-gray-700 transition-colors duration-200" title="Website">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9m0 9c-5 0-9-4-9-9s4-9 9-9"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">Daftar Menu & Produk</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ umkm.menus.length }} menu tersedia</p>
                            </div>
                            <div class="flex space-x-3">
                                <Link 
                                    :href="route('umkm.menu.index', umkm.id)"
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    Kelola Menu
                                </Link>
                                <Link 
                                    :href="route('umkm.menu.create', umkm.id)"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition ease-in-out duration-150"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Tambah Menu
                                </Link>
                            </div>
                        </div>

                        <div v-if="umkm.menus.length === 0" class="text-center py-8">
                            <div class="text-4xl mb-4">🍽️</div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Menu</h4>
                            <p class="text-gray-500 mb-6">Tambahkan menu atau produk pertama untuk UMKM Anda</p>
                            <div class="flex justify-center space-x-3">
                                <Link 
                                    :href="route('umkm.menu.index', umkm.id)"
                                    class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors duration-200 font-medium"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    Kelola Menu
                                </Link>
                                <Link 
                                    :href="route('umkm.menu.create', umkm.id)"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 font-medium"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Tambah Menu Pertama
                                </Link>
                            </div>
                        </div>

                        <div v-else>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div v-for="menu in umkm.menus.slice(0, 6)" :key="menu.id" class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                                    <!-- Menu Image -->
                                    <div class="aspect-w-16 aspect-h-12 bg-gray-200">
                                        <img 
                                            v-if="menu.gambar_menu" 
                                            :src="`/storage/${menu.gambar_menu}`" 
                                            :alt="menu.nama_menu"
                                            class="w-full h-40 object-cover"
                                        >
                                        <div v-else class="w-full h-40 bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Menu Info -->
                                    <div class="p-4">
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="font-semibold text-gray-900 text-sm line-clamp-1">{{ menu.nama_menu }}</h4>
                                            <span :class="menu.tersedia ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ml-2 flex-shrink-0">
                                                {{ menu.tersedia ? 'Tersedia' : 'Habis' }}
                                            </span>
                                        </div>
                                        
                                        <p v-if="menu.kategori_menu" class="text-xs text-blue-600 font-medium mb-2">{{ menu.kategori_menu }}</p>
                                        
                                        <p v-if="menu.deskripsi" class="text-gray-600 text-xs mb-3 line-clamp-2">{{ menu.deskripsi }}</p>
                                        
                                        <div class="flex items-center justify-between">
                                            <span class="text-lg font-bold text-green-600">{{ formatCurrency(menu.harga) }}</span>
                                            <div class="flex space-x-2">
                                                <Link 
                                                    :href="route('umkm.menu.edit', [umkm.id, menu.id])"
                                                    class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                                    title="Edit Menu"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Show "Lihat Semua Menu" button if there are more than 6 menus -->
                            <div v-if="umkm.menus.length > 6" class="text-center mt-6">
                                <Link 
                                    :href="route('umkm.menu.index', umkm.id)"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    Lihat Semua Menu ({{ umkm.menus.length }})
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>