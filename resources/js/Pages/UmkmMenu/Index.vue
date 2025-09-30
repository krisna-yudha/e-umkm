<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

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
    kategori: string;
}

const props = defineProps<{
    umkm: Umkm;
    menus: UmkmMenu[];
}>();

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

const deleteMenu = (menuId: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus menu ini?')) {
        router.delete(route('umkm.menu.destroy', [props.umkm.id, menuId]));
    }
};
</script>

<template>
    <Head :title="`Daftar Menu - ${umkm.nama_umkm}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link 
                        :href="route('umkm.show', umkm.id)"
                        class="text-gray-500 hover:text-gray-700 transition-colors duration-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-xl font-semibold leading-tight text-gray-800">
                            Daftar Menu - {{ umkm.nama_umkm }}
                        </h2>
                        <p class="text-sm text-gray-600">Kelola produk/menu untuk UMKM Anda</p>
                    </div>
                </div>
                <Link 
                    :href="route('umkm.menu.create', umkm.id)"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white tracking-wide hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-purple-500/25"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Menu
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="menus.length === 0" class="text-center py-12">
                    <div class="mx-auto max-w-md">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h9a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada menu</h3>
                        <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan menu/produk pertama Anda.</p>
                        <div class="mt-6">
                            <Link 
                                :href="route('umkm.menu.create', umkm.id)"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white tracking-wide hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah Menu Pertama
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div v-for="menu in menus" :key="menu.id" class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:scale-105">
                        <!-- Menu Image -->
                        <div class="aspect-w-16 aspect-h-12 bg-gray-200">
                            <img 
                                v-if="menu.gambar_menu" 
                                :src="`/storage/${menu.gambar_menu}`" 
                                :alt="menu.nama_menu"
                                class="w-full h-48 object-cover"
                            >
                            <div v-else class="w-full h-48 bg-gradient-to-br from-purple-100 to-blue-100 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>

                        <!-- Menu Info -->
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="font-semibold text-gray-900 text-lg truncate flex-1">{{ menu.nama_menu }}</h3>
                                <div class="flex items-center space-x-1 ml-2">
                                    <span :class="menu.tersedia ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                        {{ menu.tersedia ? 'Tersedia' : 'Habis' }}
                                    </span>
                                </div>
                            </div>
                            
                            <p v-if="menu.kategori_menu" class="text-sm text-gray-500 mb-2">{{ menu.kategori_menu }}</p>
                            
                            <p v-if="menu.deskripsi" class="text-gray-600 text-sm mb-3 line-clamp-2">{{ menu.deskripsi }}</p>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-purple-600">{{ formatCurrency(menu.harga) }}</span>
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
                                    <button 
                                        @click="deleteMenu(menu.id)"
                                        class="text-red-600 hover:text-red-800 transition-colors duration-200"
                                        title="Hapus Menu"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>