<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

// Modal state for delete confirmation
const showDeleteModal = ref(false);
const selectedUmkm = ref<{ href: string; name: string } | null>(null);
const isDeleting = ref(false);

// Modal state for toggle status confirmation
const showToggleModal = ref(false);
const selectedToggleUmkm = ref<{ id: number; name: string; status: string } | null>(null);
const isToggling = ref(false);

const handleDelete = (e: Event, href: string, umkmName: string) => {
    e.preventDefault();
    selectedUmkm.value = { href, name: umkmName };
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (!selectedUmkm.value) return;
    
    isDeleting.value = true;
    router.delete(selectedUmkm.value.href, {
        onSuccess: () => {
            showDeleteModal.value = false;
            selectedUmkm.value = null;
        },
        onError: () => {
            alert('Gagal menghapus UMKM. Silakan coba lagi.');
        },
        onFinish: () => {
            isDeleting.value = false;
        }
    });
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    selectedUmkm.value = null;
};

const handleUmkmClick = (umkmId: number, event: Event) => {
    // Prevent click when interacting with buttons or links
    const target = event.target as HTMLElement;
    if (target.closest('button') || target.closest('a') || target.closest('[role="button"]')) {
        return;
    }
    
    // Navigate to UMKM detail page
    router.visit(route('umkm.show', umkmId));
};

const confirmToggleStatus = (e: Event, umkm: any) => {
    e.stopPropagation();
    selectedToggleUmkm.value = {
        id: umkm.id,
        name: umkm.nama_umkm,
        status: umkm.status
    };
    showToggleModal.value = true;
};

const executeToggleStatus = () => {
    if (!selectedToggleUmkm.value) return;
    
    isToggling.value = true;
    router.patch(route('umkm.toggleStatus', selectedToggleUmkm.value.id), {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            showToggleModal.value = false;
            selectedToggleUmkm.value = null;
        },
        onError: () => {
            alert('Gagal mengubah status UMKM. Silakan coba lagi.');
        },
        onFinish: () => {
            isToggling.value = false;
        }
    });
};

const cancelToggleStatus = () => {
    showToggleModal.value = false;
    selectedToggleUmkm.value = null;
};

// View mode toggle
const viewMode = ref<'default' | 'list'>('default');
const openDropdown = ref<number | null>(null);

const toggleViewMode = () => {
    viewMode.value = viewMode.value === 'default' ? 'list' : 'default';
    openDropdown.value = null;
};

const toggleDropdown = (umkmId: number, event: Event) => {
    event.stopPropagation();
    openDropdown.value = openDropdown.value === umkmId ? null : umkmId;
};

const closeDropdown = () => {
    openDropdown.value = null;
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
        whatsapp?: string;
        instagram?: string;
        facebook?: string;
        twitter?: string;
        website?: string;
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
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 -mx-4 -mt-4 px-3 sm:px-4 pt-3 sm:pt-4 pb-5 sm:pb-6">
                        <div class="flex flex-col gap-2 sm:gap-3">
                    <!-- Title and Button Row -->
                    <div class="flex flex-row items-center justify-between gap-3">
                        <h2 class="font-bold text-xl sm:text-2xl md:text-3xl text-white leading-tight">
                            🏢 Kelola UMKM
                        </h2>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <Link
                                :href="route('umkm.create')"
                                class="inline-flex items-center justify-center sm:justify-start px-5 sm:px-6 md:px-7 py-3 sm:py-3 md:py-4 bg-white text-blue-600 border border-transparent rounded-lg sm:rounded-xl font-semibold text-xs sm:text-sm hover:bg-blue-50 hover:shadow-xl focus:bg-blue-50 active:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-600 transition-all duration-200 shadow-lg hover:-translate-y-0.5 whitespace-nowrap"
                            >
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah UMKM
                            </Link>
                        </div>
                    </div>
                    <!-- Subtitle Row -->
                    <p class="text-blue-100 text-xs sm:text-sm">Kelola UMKM Anda dengan mudah</p>
                </div>
            </div>
        </template>

        <div class="py-4 sm:py-6 md:py-8 bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
            <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:shadow-lg rounded-xl sm:rounded-2xl border border-gray-200">
                    <div class="p-4 sm:p-6 md:p-8 text-gray-900">
                        <div v-if="umkms.length === 0" class="text-center py-8 sm:py-12 md:py-16">
                            <div class="mb-6 sm:mb-8">
                                <div class="mx-auto w-20 sm:w-24 md:w-28 h-20 sm:h-24 md:h-28 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mb-4 sm:mb-6">
                                    <svg class="w-10 sm:w-12 md:w-14 h-10 sm:h-12 md:h-14 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-2 sm:mb-4">🏢 Belum ada UMKM</h3>
                            <p class="text-xs sm:text-sm text-gray-600 mb-6 sm:mb-8 max-w-md mx-auto leading-relaxed">Anda belum mendaftarkan UMKM apapun. Mulai perjalanan bisnis Anda dengan menambahkan UMKM pertama!</p>
                            <Link
                                :href="route('umkm.create')"
                                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl"
                            >
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah UMKM Pertama
                            </Link>
                        </div>

                        <div v-else>
                            <div class="mb-4 sm:mb-6 flex items-center justify-between">
                                <h3 class="text-base sm:text-lg md:text-xl font-semibold text-gray-900">📋 Daftar UMKM Anda</h3>
                                <!-- View Toggle Button - Mobile Only -->
                                <button
                                    v-if="umkms && umkms.length > 0"
                                    @click="toggleViewMode"
                                    class="sm:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-all duration-200"
                                    :title="viewMode === 'default' ? 'Tampilan List' : 'Tampilan Default'"
                                >
                                    <svg v-if="viewMode === 'default'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                    </svg>
                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                                    </svg>
                                </button>
                            </div>
                            <!-- DEFAULT VIEW -->
                            <div v-if="viewMode === 'default'" class="space-y-3 sm:space-y-4 md:space-y-5">
                                <div
                                    v-for="umkm in umkms"
                                    :key="umkm.id"
                                    @click="handleUmkmClick(umkm.id, $event)"
                                    class="group bg-white border border-gray-200 rounded-lg sm:rounded-xl shadow-sm overflow-hidden hover:shadow-lg hover:border-blue-300 transition-all duration-300 cursor-pointer relative"
                                >
                                    <!-- Status Badge positioned at top-right of card -->
                                    <div class="absolute top-2 right-2 sm:top-3 sm:right-3 z-10">
                                        <span 
                                            :class="{
                                                'bg-green-100 text-green-800 border-green-200': umkm.status === 'active',
                                                'bg-red-100 text-red-800 border-red-200': umkm.status === 'inactive',
                                                'bg-yellow-100 text-yellow-800 border-yellow-200': umkm.status === 'pending'
                                            }"
                                            class="inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 text-xs font-semibold rounded-full border shadow-sm whitespace-nowrap"
                                        >
                                            <div :class="{
                                                'bg-green-400': umkm.status === 'active',
                                                'bg-red-400': umkm.status === 'inactive',
                                                'bg-yellow-400': umkm.status === 'pending'
                                            }" class="w-2 h-2 rounded-full mr-1"></div>
                                            {{ umkm.status === 'active' ? 'Aktif' : umkm.status === 'inactive' ? 'Nonaktif' : 'Pending' }}
                                        </span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row sm:items-center p-3 sm:p-4 md:p-5 gap-3 sm:gap-4">
                                        <!-- Image Section -->
                                        <div class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 flex-shrink-0 mx-auto sm:mx-0">
                                            <div v-if="umkm.gambar" class="h-full w-full overflow-hidden rounded-xl shadow-md">
                                                <img 
                                                    :src="`/storage/${umkm.gambar}`" 
                                                    :alt="umkm.nama_umkm"
                                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                                />
                                            </div>
                                            <div v-else class="relative h-full w-full bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-md">
                                                <svg class="w-8 h-8 sm:w-9 md:w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Content Section -->
                                        <div class="flex-1 min-w-0 text-center sm:text-left">
                                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-2 sm:mb-3">
                                                <div class="mb-1 sm:mb-0">
                                                    <h3 class="text-sm sm:text-base md:text-lg font-bold text-gray-900 truncate mb-1 group-hover:text-blue-600 transition-colors duration-200">{{ umkm.nama_umkm }}</h3>
                                                </div>
                                            </div>
                                            
                                            <div class="flex flex-col space-y-1.5 sm:space-y-2 mb-3 sm:mb-0">
                                                <div class="flex items-center justify-center sm:justify-start text-xs sm:text-sm text-gray-600">
                                                    <div class="flex items-center bg-blue-50 px-2.5 sm:px-3 py-1 rounded-lg text-xs sm:text-sm">
                                                        <svg class="w-4 sm:w-5 h-4 sm:h-5 mr-2 sm:mr-3 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                                        </svg>
                                                        <span class="font-medium text-blue-700 text-xs sm:text-sm">{{ umkm.kategori || 'Belum dikategorikan' }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center justify-center sm:justify-start text-xs sm:text-sm text-gray-600">
                                                    <svg class="w-4 sm:w-5 h-4 sm:h-5 mr-2 sm:mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span class="truncate text-center sm:text-left text-xs sm:text-sm">{{ umkm.alamat || 'Alamat belum diisi' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex-shrink-0 flex justify-center mt-3 sm:mt-0" @click.stop>
                                            <div class="flex items-center space-x-2 sm:space-x-3">
                                                <!-- Edit Button -->
                                                <Link
                                                    :href="route('umkm.edit', umkm.id)"
                                                    class="inline-flex items-center justify-center px-4 sm:px-5 md:px-6 py-2 sm:py-2.5 md:py-3 bg-yellow-500 text-white font-semibold text-xs sm:text-sm rounded-lg hover:bg-yellow-600 hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-all duration-200 shadow-md whitespace-nowrap"
                                                    title="Edit UMKM"
                                                >
                                                    Edit
                                                </Link>

                                                <!-- Toggle Status Button -->
                                                <button
                                                    @click="confirmToggleStatus($event, umkm)"
                                                    :class="umkm.status === 'active' ? 'bg-orange-500 hover:bg-orange-600 focus:ring-orange-500' : 'bg-green-500 hover:bg-green-600 focus:ring-green-500'"
                                                    class="inline-flex items-center justify-center px-4 sm:px-5 md:px-6 py-2 sm:py-2.5 md:py-3 text-white font-semibold text-xs sm:text-sm rounded-lg hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-all duration-200 shadow-md whitespace-nowrap"
                                                    :title="umkm.status === 'active' ? 'Nonaktifkan UMKM' : 'Aktifkan UMKM'"
                                                >
                                                    {{ umkm.status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>

                                                <!-- Delete Button -->
                                                <button
                                                    @click="handleDelete($event, route('umkm.destroy', umkm.id), umkm.nama_umkm)"
                                                    class="inline-flex items-center justify-center px-4 sm:px-5 md:px-6 py-2 sm:py-2.5 md:py-3 bg-red-500 text-white font-semibold text-xs sm:text-sm rounded-lg hover:bg-red-600 hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-md whitespace-nowrap"
                                                    title="Hapus UMKM"
                                                >
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- LIST VIEW -->
                            <div v-else class="space-y-4">
                                <div
                                    v-for="umkm in umkms"
                                    :key="umkm.id"
                                    class="bg-white border border-gray-200 rounded-lg shadow-sm"
                                    :class="{'pb-32': openDropdown === umkm.id}"
                                >
                                    <div class="flex items-center justify-between p-3 gap-2">
                                        <!-- Image & Info -->
                                        <div class="flex items-center gap-2 flex-1 min-w-0 cursor-pointer" @click="handleUmkmClick(umkm.id, $event)">
                                            <!-- Small Image -->
                                            <div class="w-12 h-12 flex-shrink-0">
                                                <div v-if="umkm.gambar" class="h-full w-full overflow-hidden rounded-lg">
                                                    <img 
                                                        :src="`/storage/${umkm.gambar}`" 
                                                        :alt="umkm.nama_umkm"
                                                        class="w-full h-full object-cover"
                                                    />
                                                </div>
                                                <div v-else class="h-full w-full bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <!-- Text Info -->
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-sm font-bold text-gray-900 truncate">{{ umkm.nama_umkm }}</h3>
                                                <p class="text-xs text-gray-600 truncate">{{ umkm.alamat || 'Alamat belum diisi' }}</p>
                                            </div>
                                        </div>

                                        <!-- Dropdown Menu -->
                                        <div class="relative flex-shrink-0">
                                            <button
                                                @click="toggleDropdown(umkm.id, $event)"
                                                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200"
                                                :class="{'bg-gray-100': openDropdown === umkm.id}"
                                            >
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                                </svg>
                                            </button>

                                            <!-- Dropdown Items -->
                                            <div
                                                v-if="openDropdown === umkm.id"
                                                class="absolute right-0 top-8 bg-white border border-gray-200 rounded-lg shadow-2xl z-50 overflow-hidden min-w-max"
                                            >
                                                <!-- Edit -->
                                                <Link
                                                    :href="route('umkm.edit', umkm.id)"
                                                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-yellow-50 border-b border-gray-100 text-yellow-600 transition-colors duration-200 whitespace-nowrap text-sm"
                                                    title="Edit"
                                                    @click="closeDropdown"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    <span>Edit</span>
                                                </Link>

                                                <!-- Toggle Status -->
                                                <button
                                                    @click="confirmToggleStatus($event, umkm)"
                                                    :class="umkm.status === 'active' ? 'text-orange-600 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50'"
                                                    class="w-full flex items-center gap-3 px-4 py-2.5 border-b border-gray-100 transition-colors duration-200 text-sm"
                                                    :title="umkm.status === 'active' ? 'Nonaktifkan' : 'Aktifkan'"
                                                    @click.stop
                                                >
                                                    <svg v-if="umkm.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>{{ umkm.status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                                                </button>

                                                <!-- Delete -->
                                                <button
                                                    @click="handleDelete($event, route('umkm.destroy', umkm.id), umkm.nama_umkm)"
                                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 transition-colors duration-200 text-sm"
                                                    title="Hapus"
                                                    @click.stop
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    <span>Hapus</span>
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

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-3 sm:px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="cancelDelete"></div>

                <!-- Modal content -->
                <div class="inline-block align-bottom bg-white rounded-lg sm:rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 sm:px-6 pt-4 sm:pt-5 pb-4 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Hapus UMKM
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Apakah Anda yakin ingin menghapus UMKM <strong>{{ selectedUmkm?.name }}</strong>? 
                                        Tindakan ini tidak dapat dibatalkan dan semua data terkait akan hilang.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:flex sm:flex-row-reverse gap-2 sm:gap-0">
                        <button 
                            type="button" 
                            @click="confirmDelete"
                            :disabled="isDeleting"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 sm:py-2.5 bg-red-600 text-base font-medium text-white hover:bg-red-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 hover:-translate-y-0.5 transition-all duration-200 disabled:cursor-not-allowed"
                        >
                            <svg v-if="isDeleting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ isDeleting ? 'Menghapus...' : 'Hapus' }}
                        </button>
                        <button 
                            type="button" 
                            @click="cancelDelete"
                            :disabled="isDeleting"
                            class="order-first sm:order-last mt-3 w-full sm:mt-0 inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 sm:py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 hover:-translate-y-0.5 transition-all duration-200"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toggle Status Confirmation Modal -->
        <div v-if="showToggleModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-3 sm:px-4 py-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="cancelToggleStatus"></div>
                <div class="relative bg-white rounded-lg sm:rounded-xl text-left overflow-hidden shadow-xl transform transition-all max-w-lg w-full mx-auto">
                    <div class="bg-white px-4 sm:px-6 pt-4 sm:pt-5 pb-4 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div :class="selectedToggleUmkm?.status === 'active' ? 'bg-red-100' : 'bg-green-100'" class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                <svg v-if="selectedToggleUmkm?.status === 'active'" class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <svg v-else class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    {{ selectedToggleUmkm?.status === 'active' ? 'Nonaktifkan UMKM' : 'Aktifkan UMKM' }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        {{ selectedToggleUmkm?.status === 'active' 
                                            ? 'Yakin ingin menonaktifkan UMKM ini?' 
                                            : 'Yakin ingin mengaktifkan UMKM ini?' 
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 sm:px-6 py-3 sm:flex sm:flex-row-reverse gap-2 sm:gap-0">
                        <button 
                            type="button" 
                            @click="executeToggleStatus"
                            :disabled="isToggling"
                            :class="selectedToggleUmkm?.status === 'active' 
                                ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' 
                                : 'bg-green-600 hover:bg-green-700 focus:ring-green-500'"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 sm:py-2.5 text-base font-medium text-white hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 hover:-translate-y-0.5 transition-all duration-200 disabled:cursor-not-allowed"
                        >
                            <svg v-if="isToggling" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ isToggling ? 'Memproses...' : 'Ya' }}
                        </button>
                        <button 
                            type="button" 
                            @click="cancelToggleStatus"
                            :disabled="isToggling"
                            class="order-first sm:order-last mt-3 w-full sm:mt-0 inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 sm:py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 hover:-translate-y-0.5 transition-all duration-200"
                        >
                            Batal
                        </button>
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