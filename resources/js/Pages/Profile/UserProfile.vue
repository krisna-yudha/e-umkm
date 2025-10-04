<template>
    <AuthenticatedLayout>
        <div class="py-8 bg-gradient-to-br from-gray-50 to-purple-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Profile Stats Section -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <!-- Profile Card -->
                    <div class="md:col-span-1 bg-white rounded-2xl shadow-lg p-6 border border-purple-100">
                        <div class="text-center">
                            <!-- Profile Photo -->
                            <div class="relative mx-auto mb-4">
                                <img
                                    v-if="$page.props.auth.user.profile_photo"
                                    :src="`/storage/${$page.props.auth.user.profile_photo}`"
                                    :alt="$page.props.auth.user.name"
                                    class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg ring-4 ring-purple-100 mx-auto"
                                />
                                <div
                                    v-else
                                    class="w-24 h-24 bg-gradient-to-br from-purple-400 to-blue-500 rounded-full flex items-center justify-center mx-auto border-4 border-white shadow-lg ring-4 ring-purple-100"
                                >
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $page.props.auth.user.name }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ $page.props.auth.user.email }}</p>
                            <div class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Pelaku UMKM
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Section -->
                    <div class="md:col-span-3">
                        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border border-gray-200">
                            <h3 class="text-lg md:text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Aksi Cepat
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                                <!-- Tambah UMKM -->
                                <Link
                                    :href="route('umkm.create')"
                                    class="group flex flex-col items-center p-4 md:p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200 hover:from-blue-100 hover:to-blue-200 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-1"
                                >
                                    <div class="p-3 bg-blue-600 rounded-full mb-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm md:text-base font-semibold text-gray-800 text-center">Tambah UMKM</span>
                                    <span class="text-xs text-gray-600 text-center mt-1">Daftarkan usaha baru</span>
                                </Link>

                                <!-- Edit Profile -->
                                <Link
                                    :href="route('profile.edit')"
                                    class="group flex flex-col items-center p-4 md:p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200 hover:from-green-100 hover:to-green-200 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-1"
                                >
                                    <div class="p-3 bg-green-600 rounded-full mb-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm md:text-base font-semibold text-gray-800 text-center">Edit Profile</span>
                                    <span class="text-xs text-gray-600 text-center mt-1">Perbarui informasi</span>
                                </Link>

                                <!-- Lihat Peta -->
                                <Link
                                    :href="route('mapping')"
                                    class="group flex flex-col items-center p-4 md:p-6 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200 hover:from-purple-100 hover:to-purple-200 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-1"
                                >
                                    <div class="p-3 bg-purple-600 rounded-full mb-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm md:text-base font-semibold text-gray-800 text-center">Lihat Peta</span>
                                    <span class="text-xs text-gray-600 text-center mt-1">Jelajahi lokasi UMKM</span>
                                </Link>

                                <!-- Bantuan
                                <Link
                                    :href="route('help')"
                                    class="group flex flex-col items-center p-4 md:p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl border border-orange-200 hover:from-orange-100 hover:to-orange-200 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-1"
                                >
                                    <div class="p-3 bg-orange-600 rounded-full mb-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm md:text-base font-semibold text-gray-800 text-center">Bantuan</span>
                                    <span class="text-xs text-gray-600 text-center mt-1">Pusat bantuan</span>
                                </Link> -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent UMKM Section -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">📍 UMKM Terbaru</h3>
                        <Link
                            :href="route('umkm.index')"
                            class="text-purple-600 hover:text-purple-800 font-medium text-sm"
                        >
                            Lihat Semua →
                        </Link>
                    </div>

                    <div v-if="!umkms || umkms.length === 0" class="text-center py-12">
                        <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Belum ada UMKM</h4>
                        <p class="text-gray-600 mb-6">Mulai dengan mendaftarkan UMKM pertama Anda!</p>
                        <Link
                            :href="route('umkm.create')"
                            class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 transition-colors"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tambah UMKM
                        </Link>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="umkm in umkms.slice(0, 6)"
                            :key="umkm.id"
                            class="group bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 hover:border-purple-300"
                        >
                            <div class="flex items-start justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">
                                    {{ umkm.nama_umkm }}
                                </h4>
                                <span 
                                    :class="{
                                        'bg-green-100 text-green-800': umkm.status === 'active',
                                        'bg-red-100 text-red-800': umkm.status === 'inactive',
                                        'bg-yellow-100 text-yellow-800': umkm.status === 'pending'
                                    }"
                                    class="px-2 py-1 text-xs font-medium rounded-full"
                                >
                                    {{ umkm.status === 'active' ? 'Aktif' : umkm.status === 'inactive' ? 'Nonaktif' : 'Pending' }}
                                </span>
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span>{{ umkm.kategori || 'Tidak ada kategori' }}</span>
                                </div>
                                
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    <span class="truncate">{{ umkm.alamat || 'Alamat tidak tersedia' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <Link
                                    :href="route('umkm.show', umkm.id)"
                                    class="text-purple-600 hover:text-purple-800 font-medium text-sm"
                                >
                                    Lihat Detail →
                                </Link>
                                <div v-if="umkm.latitude && umkm.longitude" class="flex items-center text-xs text-purple-600">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m-6 3l6-3"/>
                                    </svg>
                                    Di Peta
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
    umkms: {
        type: Array,
        default: () => []
    }
})
</script>