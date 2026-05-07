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
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-bold text-lg sm:text-2xl text-gray-900 leading-tight line-clamp-2 flex-1">
                    📋 Detail UMKM: {{ umkm.nama_umkm }}
                </h2>
                <div class="flex flex-row gap-2 sm:gap-3 w-full sm:w-auto justify-center sm:justify-end">
                    <!-- Edit Button -->
                    <Link
                        :href="route('umkm.edit', umkm.id)"
                        class="inline-flex items-center justify-center px-5 sm:px-6 md:px-7 py-2.5 sm:py-3 md:py-3.5 bg-yellow-500 text-white border border-transparent rounded-lg sm:rounded-xl font-semibold text-xs sm:text-sm hover:bg-yellow-600 hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-200 shadow-md whitespace-nowrap"
                    >
                        Edit
                    </Link>

                    <!-- Back Button -->
                    <Link
                        :href="route('umkm.index')"
                        class="inline-flex items-center justify-center px-5 sm:px-6 md:px-7 py-2.5 sm:py-3 md:py-3.5 bg-gray-500 text-white border border-transparent rounded-lg sm:rounded-xl font-semibold text-xs sm:text-sm hover:bg-gray-600 hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-md whitespace-nowrap"
                    >
                        Kembali
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-3 sm:py-8 md:py-12 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-5xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-lg rounded-lg sm:rounded-2xl border border-gray-100">
                    <div class="p-4 sm:p-6 md:p-8 lg:p-10">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 md:gap-6 lg:gap-8">
                            <!-- Left Column - Image and Basic Info -->
                            <div>
                                <div class="mb-5 sm:mb-6">
                                    <div v-if="umkm.gambar" class="w-full h-40 sm:h-48 md:h-60 lg:h-72 overflow-hidden rounded-xl shadow-xl border border-gray-200 hover:shadow-2xl transition-shadow duration-300">
                                        <img 
                                            :src="`/storage/${umkm.gambar}`" 
                                            :alt="umkm.nama_umkm"
                                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                        />
                                    </div>
                                    <div v-else class="w-full h-40 sm:h-48 md:h-60 lg:h-72 bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center rounded-xl border border-gray-200">
                                        <svg class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="space-y-3 sm:space-y-4">
                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-200 rounded-xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="p-2 bg-blue-600 rounded-lg">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-sm sm:text-base font-bold text-gray-900">📋 Informasi Dasar</h3>
                                        </div>
                                        <div class="space-y-1 text-xs">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 font-medium">Status:</span>
                                                <span 
                                                    :class="umkm.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                                    class="font-bold px-2 py-1 rounded-full text-xs"
                                                >
                                                    {{ umkm.status === 'active' ? '✓ Aktif' : '✗ Tidak Aktif' }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 font-medium">Kategori:</span>
                                                <span class="font-semibold text-right truncate ml-2 text-blue-600">{{ umkm.kategori }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 font-medium">Didaftarkan:</span>
                                                <span class="font-medium">{{ new Date(umkm.created_at).toLocaleDateString('id-ID') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="umkm.user" class="bg-gradient-to-br from-purple-50 to-purple-100/50 border border-purple-200 rounded-xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="p-2 bg-purple-600 rounded-lg">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-sm sm:text-base font-bold text-gray-900">👤 Pemilik UMKM</h3>
                                        </div>
                                        <div class="space-y-1 text-xs">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 font-medium">Nama:</span>
                                                <span class="font-semibold truncate ml-2 text-purple-600">{{ umkm.user.name }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600 font-medium">Email:</span>
                                                <span class="font-medium text-xs truncate ml-2">{{ umkm.user.email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Details -->
                            <div class="space-y-4 sm:space-y-6">
                                <div>
                                    <div class="flex items-center gap-2 mb-3 pb-2 border-b-2 border-gradient-to-r from-blue-200 to-purple-200">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                        </svg>
                                        <h3 class="text-sm sm:text-lg font-bold text-gray-900">Detail UMKM</h3>
                                    </div>
                                    
                                    <div v-if="umkm.deskripsi" class="mb-2 sm:mb-3">
                                        <h4 class="font-medium text-gray-700 mb-0.5 sm:mb-1 text-xs sm:text-sm">Deskripsi</h4>
                                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed line-clamp-3">{{ umkm.deskripsi }}</p>
                                    </div>

                                    <div class="space-y-2 sm:space-y-3">
                                        <div class="bg-gradient-to-br from-green-50 to-green-100/50 border border-green-200 rounded-xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="p-2 bg-green-600 rounded-lg">
                                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                                    </svg>
                                                </div>
                                                <h4 class="font-bold text-gray-900 text-sm sm:text-base">📍 Lokasi & Kontak</h4>
                                            </div>
                                            <div class="space-y-3">
                                                <div>
                                                    <div class="flex items-start gap-1.5 mb-1.5">
                                                        <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        </svg>
                                                        <div>
                                                            <p class="font-bold text-gray-900 text-xs sm:text-sm">Alamat</p>
                                                            <p class="text-xs sm:text-sm text-gray-700 mt-0.5">{{ umkm.alamat }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div v-if="umkm.no_telepon" class="flex items-start gap-1.5">
                                                    <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                    <div>
                                                        <p class="font-bold text-gray-900 text-xs sm:text-sm">No. Telepon</p>
                                                        <a :href="`tel:${umkm.no_telepon}`" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 hover:underline transition-all duration-200 break-all mt-0.5 inline-block">{{ umkm.no_telepon }}</a>
                                                    </div>
                                                </div>

                                                <div v-if="umkm.email" class="flex items-start gap-1.5">
                                                    <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                    <div>
                                                        <p class="font-bold text-gray-900 text-xs sm:text-sm">Email</p>
                                                        <a :href="`mailto:${umkm.email}`" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 hover:underline transition-all duration-200 truncate mt-0.5 inline-block">{{ umkm.email }}</a>
                                                    </div>
                                                </div>

                                                <div v-if="umkm.latitude && umkm.longitude" class="flex items-start gap-1.5">
                                                    <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                                    </svg>
                                                    <div>
                                                        <p class="font-bold text-gray-900 text-xs sm:text-sm">Koordinat</p>
                                                        <p class="text-xs sm:text-sm text-gray-700 mt-0.5">{{ umkm.latitude }}, {{ umkm.longitude }}</p>
                                                        <Link
                                                            :href="route('mapping')"
                                                            class="inline-flex items-center mt-1.5 text-xs text-blue-600 hover:text-blue-800 hover:underline transition-all duration-200 font-medium"
                                                        >
                                                            Lihat di Peta
                                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                            </svg>
                                                        </Link>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Social Media Links -->
                                        <div v-if="umkm.facebook || umkm.instagram || umkm.twitter || umkm.whatsapp || umkm.website" class="bg-gradient-to-br from-pink-50 to-pink-100/50 border border-pink-200 rounded-xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow duration-300">
                                            <div class="flex items-center gap-2 mb-4">
                                                <div class="p-2 bg-pink-600 rounded-lg">
                                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                                    </svg>
                                                </div>
                                                <h4 class="font-bold text-gray-900 text-sm sm:text-base">🌐 Media Sosial & Website</h4>
                                            </div>
                                            <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                                                <a v-if="umkm.facebook" :href="umkm.facebook" target="_blank" class="group w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white hover:bg-blue-700 transition-all duration-300 hover:shadow-2xl hover:scale-125 hover:-translate-y-2 shadow-lg" title="Facebook">
                                                    <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                    </svg>
                                                </a>
                                                <a v-if="umkm.instagram" :href="umkm.instagram" target="_blank" class="group w-12 h-12 rounded-full bg-gradient-to-r from-pink-500 via-purple-500 to-rose-500 flex items-center justify-center text-white hover:from-pink-600 hover:via-purple-600 hover:to-rose-600 transition-all duration-300 hover:shadow-2xl hover:scale-125 hover:-translate-y-2 shadow-lg" title="Instagram">
                                                    <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.329-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.412-3.329c.881-.881 2.032-1.412 3.329-1.412s2.448.531 3.329 1.412c.881.881 1.412 2.032 1.412 3.329s-.531 2.448-1.412 3.329c-.881.807-2.032 1.297-3.329 1.297zm7.83-9.476h-1.78V6.524h1.78v.988zm-.612 5.027c0-1.09-.881-1.971-1.971-1.971s-1.971.881-1.971 1.971.881 1.971 1.971 1.971 1.971-.881 1.971-1.971z"/>
                                                    </svg>
                                                </a>
                                                <a v-if="umkm.twitter" :href="umkm.twitter" target="_blank" class="group w-12 h-12 rounded-full bg-sky-500 flex items-center justify-center text-white hover:bg-sky-600 transition-all duration-300 hover:shadow-2xl hover:scale-125 hover:-translate-y-2 shadow-lg" title="Twitter/X">
                                                    <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                                    </svg>
                                                </a>
                                                <a v-if="umkm.whatsapp" :href="umkm.whatsapp" target="_blank" class="group w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white hover:bg-green-600 transition-all duration-300 hover:shadow-2xl hover:scale-125 hover:-translate-y-2 shadow-lg" title="WhatsApp">
                                                    <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.570-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                                    </svg>
                                                </a>
                                                <a v-if="umkm.website" :href="umkm.website" target="_blank" class="group w-12 h-12 rounded-full bg-indigo-600 flex items-center justify-center text-white hover:bg-indigo-700 transition-all duration-300 hover:shadow-2xl hover:scale-125 hover:-translate-y-2 shadow-lg" title="Website">
                                                    <svg class="w-6 h-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="bg-white overflow-hidden shadow-lg rounded-lg sm:rounded-2xl border border-gray-100 mt-4 sm:mt-8 md:mt-12">
                    <div class="p-4 sm:p-6 md:p-8 lg:p-10">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4 mb-4 sm:mb-6 md:mb-8">
                            <div>
                                <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-900">📍 Menu & Produk</h3>
                                <p class="text-xs sm:text-sm text-gray-600 mt-0.5 sm:mt-1">{{ umkm.menus.length }} item tersedia</p>
                            </div>
                            <div class="grid grid-cols-2 gap-1.5 sm:grid-cols-2 sm:gap-2.5 md:gap-3 lg:gap-4">
                                <Link 
                                    :href="route('umkm.menu.index', umkm.id)"
                                    class="inline-flex items-center justify-center px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 md:py-2.5 bg-gray-600 border border-transparent rounded-md font-semibold text-xs sm:text-sm text-white hover:bg-gray-700 transition-all duration-200 hover:shadow-lg hover:scale-110 hover:-translate-y-1 shadow-md group"
                                >
                                    <svg class="w-4 h-4 sm:mr-2 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    <span class="text-xs">Kelola</span>
                                </Link>
                                <Link 
                                    :href="route('umkm.menu.create', umkm.id)"
                                    class="inline-flex items-center justify-center px-2 sm:px-3 md:px-4 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs sm:text-sm text-white hover:bg-blue-700 transition-all duration-200 hover:shadow-lg hover:scale-110 hover:-translate-y-1 shadow-md group"
                                >
                                    <svg class="w-4 h-4 sm:mr-2 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <span class="text-xs">Tambah</span>
                                </Link>
                            </div>
                        </div>

                        <div v-if="umkm.menus.length === 0" class="text-center py-8">
                            <div class="text-4xl mb-4">🍽️</div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Menu</h4>
                            <p class="text-gray-500">Menu akan ditampilkan di sini setelah ditambahkan</p>
                        </div>

                        <div v-else>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-5 lg:gap-6">
                                <div v-for="menu in umkm.menus.slice(0, 6)" :key="menu.id" class="bg-gray-50 rounded-lg sm:rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg hover:border-purple-200 transition-all duration-300">
                                    <!-- Menu Image -->
                                    <div class="w-full aspect-video bg-gray-200">
                                        <img 
                                            v-if="menu.gambar_menu" 
                                            :src="`/storage/${menu.gambar_menu}`" 
                                            :alt="menu.nama_menu"
                                            class="w-full h-full object-cover"
                                        >
                                        <div v-else class="w-full h-full bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Menu Info -->
                                    <div class="p-3 sm:p-4 md:p-5 space-y-2 sm:space-y-3">
                                        <div class="flex items-start justify-between mb-1.5 gap-2">
                                            <h4 class="font-semibold text-gray-900 text-sm sm:text-base line-clamp-1">{{ menu.nama_menu }}</h4>
                                            <span :class="menu.tersedia ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-xs font-medium flex-shrink-0 whitespace-nowrap">
                                                {{ menu.tersedia ? 'Tersedia' : 'Habis' }}
                                            </span>
                                        </div>
                                        
                                        <p v-if="menu.kategori_menu" class="text-xs sm:text-xs text-blue-600 font-medium mb-2 sm:mb-2.5">{{ menu.kategori_menu }}</p>
                                        
                                        <p v-if="menu.deskripsi" class="text-gray-600 text-xs sm:text-xs mb-2 sm:mb-3 line-clamp-2">{{ menu.deskripsi }}</p>
                                        
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-base sm:text-lg md:text-xl font-bold text-green-600">{{ formatCurrency(menu.harga) }}</span>
                                            <div class="flex space-x-1 sm:space-x-2">
                                                <Link 
                                                    :href="route('umkm.menu.edit', [umkm.id, menu.id])"
                                                    class="inline-flex items-center justify-center w-8 h-8 sm:w-9 sm:h-9 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 hover:shadow-lg hover:scale-125 hover:-translate-y-1 shadow-md group"
                                                    title="Edit Menu"
                                                >
                                                    <svg class="w-4 h-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Show "Lihat Semua Menu" button if there are more than 6 menus -->
                            <div v-if="umkm.menus.length > 6" class="text-center mt-4 sm:mt-6">
                                <Link 
                                    :href="route('umkm.menu.index', umkm.id)"
                                    class="inline-flex items-center px-4 sm:px-6 py-2 sm:py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 font-medium text-sm sm:text-base shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                                >
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    <span class="hidden sm:inline">Lihat Semua Menu ({{ umkm.menus.length }})</span>
                                    <span class="sm:hidden">Semua Menu</span>
                                    <svg class="w-4 h-4 sm:ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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