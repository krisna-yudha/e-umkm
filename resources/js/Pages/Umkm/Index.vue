<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const handleDelete = (e: Event, href: string) => {
    e.preventDefault();
    if (window.confirm('Apakah Anda yakin ingin menghapus UMKM ini?')) {
        router.delete(href);
    }
};

const props = defineProps<{
    umkms: Array<{
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
        user: {
            name: string;
        };
    }>;
}>();
</script>

<template>
    <Head title="Kelola UMKM" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Kelola UMKM
                </h2>
                <Link
                    :href="route('umkm.create')"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Tambah UMKM
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div v-if="umkms.length === 0" class="text-center py-8">
                            <div class="mb-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada UMKM</h3>
                            <p class="text-gray-500 mb-4">Anda belum mendaftarkan UMKM apapun. Mulai dengan menambahkan UMKM pertama Anda.</p>
                            <Link
                                :href="route('umkm.create')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Tambah UMKM Pertama
                            </Link>
                        </div>

                        <div v-else>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div
                                    v-for="umkm in umkms"
                                    :key="umkm.id"
                                    class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden"
                                >
                                    <div v-if="umkm.gambar" class="h-48 overflow-hidden">
                                        <img 
                                            :src="`/storage/${umkm.gambar}`" 
                                            :alt="umkm.nama_umkm"
                                            class="w-full h-full object-cover"
                                        />
                                    </div>
                                    <div v-else class="h-48 bg-gray-200 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>

                                    <div class="p-6">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="text-xl font-semibold text-gray-900">{{ umkm.nama_umkm }}</h3>
                                            <span 
                                                :class="umkm.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            >
                                                {{ umkm.status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 mb-3">Kategori: {{ umkm.kategori }}</p>
                                        
                                        <div v-if="umkm.deskripsi" class="mb-4">
                                            <p class="text-gray-700 text-sm line-clamp-2">{{ umkm.deskripsi }}</p>
                                        </div>

                                        <div class="space-y-2 mb-4">
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                {{ umkm.alamat }}
                                            </div>

                                            <div v-if="umkm.latitude && umkm.longitude" class="flex items-center text-sm text-green-600">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                                </svg>
                                                Lokasi tersedia di peta
                                            </div>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            <div class="flex space-x-2">
                                                <Link
                                                    :href="route('umkm.show', umkm.id)"
                                                    class="text-indigo-600 hover:text-indigo-500"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </Link>
                                                <Link
                                                    :href="route('umkm.edit', umkm.id)"
                                                    class="text-yellow-600 hover:text-yellow-500"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </Link>
                                                <button
                                                    @click="handleDelete($event, route('umkm.destroy', umkm.id))"
                                                    class="text-red-600 hover:text-red-500"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
}
</style>