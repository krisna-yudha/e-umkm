<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{
    umkm: {
        id: number;
        nama_umkm: string;
        deskripsi: string;
        kategori: string;
        alamat: string;
        no_telepon: string;
        email: string;
        latitude: number;
        longitude: number;
        gambar: string;
        status: string;
        created_at: string;
        user: {
            name: string;
            email: string;
        };
    };
}>();
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

                                    <div>
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
                                                :href="route('public.mapping')"
                                                class="inline-flex items-center mt-2 text-sm text-blue-600 hover:text-blue-800"
                                            >
                                                Lihat di Peta
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>