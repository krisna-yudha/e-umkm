<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

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

// Track loading state for individual menus
const loadingMenus = ref<Set<number>>(new Set());
const deletingMenus = ref<Set<number>>(new Set());

// Modal state for delete confirmation
const showDeleteModal = ref(false);
const selectedMenu = ref<{ id: number; name: string } | null>(null);

// Modal state for product detail
const showDetailModal = ref(false);
const selectedMenuDetail = ref<UmkmMenu | null>(null);

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
};

const showMenuDetail = (menu: UmkmMenu, event: Event) => {
    // Prevent if clicking on toggle or delete button
    const target = event.target as HTMLElement;
    if (target.closest('label') || target.closest('input') || target.closest('button')) {
        return;
    }
    
    selectedMenuDetail.value = menu;
    showDetailModal.value = true;
};

const closeDetailModal = () => {
    showDetailModal.value = false;
    selectedMenuDetail.value = null;
};

const deleteMenu = (menuId: number, menuName: string) => {
    selectedMenu.value = { id: menuId, name: menuName };
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (!selectedMenu.value) return;
    
    const menuId = selectedMenu.value.id;
    deletingMenus.value.add(menuId);
    
    router.delete(route('umkm.menu.destroy', [props.umkm.id, menuId]), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            // Menu will be removed from the list automatically by Inertia
            showDeleteModal.value = false;
            selectedMenu.value = null;
        },
        onError: (errors) => {
            console.error('Error deleting menu:', errors);
            alert('Gagal menghapus menu. Silakan coba lagi.');
        },
        onFinish: () => {
            deletingMenus.value.delete(menuId);
        }
    });
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    selectedMenu.value = null;
};

const toggleMenuStatus = (menuId: number, isAvailable: boolean) => {
    // Add to loading state
    loadingMenus.value.add(menuId);
    
    router.patch(route('umkm.menu.update', [props.umkm.id, menuId]), {
        tersedia: isAvailable
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Show success feedback (you can remove this if not needed)
            // console.log('Status menu berhasil diperbarui');
        },
        onError: (errors) => {
            console.error('Error updating menu status:', errors);
            // Optionally show error message to user
        },
        onFinish: () => {
            // Remove from loading state when finished
            loadingMenus.value.delete(menuId);
        }
    });
};

const handleToggleChange = async (menuId: number, isChecked: boolean) => {
    const menu = props.menus.find(m => m.id === menuId);
    if (!menu) return;
    
    // If the status is the same, don't make API call
    if (menu.tersedia === isChecked) return;
    
    try {
        loadingMenus.value.add(menuId);
        
        await router.patch(route('umkm.menu.update', [props.umkm.id, menuId]), {
            tersedia: isChecked
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                // Update the menu status in the local state
                menu.tersedia = isChecked;
            },
            onError: () => {
                // Reset the toggle on error
                const checkbox = document.querySelector(`input[data-menu-id="${menuId}"]`) as HTMLInputElement;
                if (checkbox) {
                    checkbox.checked = menu.tersedia;
                }
            }
        });
    } catch (error) {
        console.error('Error updating menu status:', error);
    } finally {
        loadingMenus.value.delete(menuId);
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

        <div class="py-6 sm:py-8 lg:py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div v-if="menus.length === 0" class="text-center py-8 sm:py-12">
                    <div class="mx-auto max-w-md px-4">
                        <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h9a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <h3 class="mt-2 text-sm sm:text-base font-medium text-gray-900">Belum ada menu</h3>
                        <p class="mt-1 text-xs sm:text-sm text-gray-500">Mulai dengan menambahkan menu/produk pertama Anda.</p>
                        <div class="mt-4 sm:mt-6">
                            <Link 
                                :href="route('umkm.menu.create', umkm.id)"
                                class="inline-flex items-center px-3 py-2 sm:px-4 sm:py-2 bg-gradient-to-r from-purple-600 to-blue-600 border border-transparent rounded-lg font-semibold text-xs sm:text-sm text-white tracking-wide hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200"
                            >
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Tambah Menu Pertama
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Header List -->
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between text-sm font-medium text-gray-700">
                            <span>Menu</span>
                            <span>Status</span>
                        </div>
                    </div>
                    
                    <!-- List Items -->
                    <div class="divide-y divide-gray-100">
                        <div v-for="menu in menus" :key="menu.id" 
                             @click="showMenuDetail(menu, $event)"
                             class="px-4 py-4 hover:bg-gray-50 transition-colors duration-200 cursor-pointer active:bg-gray-100 touch-manipulation">
                            <div class="flex items-center justify-between">
                                <!-- Menu Info -->
                                <div class="flex items-center space-x-4 flex-1">
                                    <!-- Menu Image -->
                                    <div class="flex-shrink-0">
                                        <img 
                                            v-if="menu.gambar_menu" 
                                            :src="`/storage/${menu.gambar_menu}`" 
                                            :alt="menu.nama_menu"
                                            class="w-12 h-12 sm:w-16 sm:h-16 object-cover rounded-lg"
                                        >
                                        <div v-else class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-purple-100 to-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Menu Details -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm sm:text-base font-semibold text-gray-900 truncate">{{ menu.nama_menu }}</h3>
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-4 mt-1">
                                            <p v-if="menu.kategori_menu" class="text-xs sm:text-sm text-gray-500">{{ menu.kategori_menu }}</p>
                                            <span class="text-sm sm:text-base font-bold text-purple-600">{{ formatCurrency(menu.harga) }}</span>
                                        </div>
                                        <p v-if="menu.deskripsi" class="text-xs sm:text-sm text-gray-600 mt-1 line-clamp-1">{{ menu.deskripsi }}</p>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex flex-col items-end space-y-2 flex-shrink-0 ml-4"
                                     @click.stop>
                                    <!-- Status Toggle -->
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            :checked="menu.tersedia" 
                                            :disabled="loadingMenus.has(menu.id)"
                                            :data-menu-id="menu.id"
                                            @change="(event) => handleToggleChange(menu.id, (event.target as HTMLInputElement).checked)"
                                            class="sr-only peer"
                                        >
                                        <div :class="[
                                            'relative w-10 h-6 rounded-full peer transition-all',
                                            loadingMenus.has(menu.id) 
                                                ? 'bg-gray-300 cursor-not-allowed' 
                                                : 'bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-purple-300 peer-checked:bg-green-500 hover:peer-checked:bg-green-600',
                                            'peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all'
                                        ]">
                                            <!-- Loading spinner -->
                                            <div v-if="loadingMenus.has(menu.id)" class="absolute inset-0 flex items-center justify-center">
                                                <svg class="w-3 h-3 animate-spin text-gray-500" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </label>
                                    <span :class="[
                                        'text-xs font-medium transition-colors',
                                        loadingMenus.has(menu.id) 
                                            ? 'text-gray-400' 
                                            : menu.tersedia ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        {{ loadingMenus.has(menu.id) ? 'Menyimpan...' : menu.tersedia ? 'Tersedia' : 'Habis' }}
                                    </span>
                                    
                                    <!-- Delete Button -->
                                    <button
                                        @click="deleteMenu(menu.id, menu.nama_menu)"
                                        :disabled="deletingMenus.has(menu.id) || loadingMenus.has(menu.id)"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-md transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                        title="Hapus menu"
                                    >
                                        <svg v-if="deletingMenus.has(menu.id)" class="w-3 h-3 animate-spin mr-1" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <svg v-else class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        {{ deletingMenus.has(menu.id) ? 'Menghapus...' : 'Hapus' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="cancelDelete"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Hapus Menu
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Apakah Anda yakin ingin menghapus menu <span class="font-semibold">"{{ selectedMenu?.name }}"</span>? Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            type="button"
                            @click="confirmDelete"
                            :disabled="!selectedMenu || deletingMenus.has(selectedMenu.id)"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg v-if="selectedMenu && deletingMenus.has(selectedMenu.id)" class="w-4 h-4 animate-spin mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ selectedMenu && deletingMenus.has(selectedMenu.id) ? 'Menghapus...' : 'Hapus' }}
                        </button>
                        <button
                            type="button"
                            @click="cancelDelete"
                            :disabled="selectedMenu ? deletingMenus.has(selectedMenu.id) : false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Detail Modal -->
        <div v-if="showDetailModal && selectedMenuDetail" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="product-modal" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-black bg-opacity-60 transition-opacity backdrop-blur-sm" @click="closeDetailModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <!-- Header with close button -->
                    <div class="absolute top-4 right-4 z-10">
                        <button @click="closeDetailModal" class="bg-white/80 backdrop-blur-sm rounded-full p-2 text-gray-400 hover:text-gray-600 hover:bg-white transition-all duration-200 shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Product Image -->
                    <div class="relative h-64 sm:h-80 bg-gradient-to-br from-purple-100 to-blue-100 overflow-hidden">
                        <img 
                            v-if="selectedMenuDetail.gambar_menu" 
                            :src="`/storage/${selectedMenuDetail.gambar_menu}`" 
                            :alt="selectedMenuDetail.nama_menu"
                            class="w-full h-full object-cover"
                        >
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        
                        <!-- Status badge -->
                        <div class="absolute top-4 left-4">
                            <span :class="[
                                'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                selectedMenuDetail.tersedia 
                                    ? 'bg-green-100 text-green-800 border border-green-200' 
                                    : 'bg-red-100 text-red-800 border border-red-200'
                            ]">
                                <div :class="[
                                    'w-2 h-2 rounded-full mr-2',
                                    selectedMenuDetail.tersedia ? 'bg-green-400' : 'bg-red-400'
                                ]"></div>
                                {{ selectedMenuDetail.tersedia ? 'Tersedia' : 'Habis' }}
                            </span>
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="p-6">
                        <!-- Category -->
                        <div v-if="selectedMenuDetail.kategori_menu" class="mb-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ selectedMenuDetail.kategori_menu }}
                            </span>
                        </div>

                        <!-- Product Name -->
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ selectedMenuDetail.nama_menu }}</h2>
                        
                        <!-- Price -->
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-purple-600">{{ formatCurrency(selectedMenuDetail.harga) }}</span>
                        </div>

                        <!-- Description -->
                        <div v-if="selectedMenuDetail.deskripsi" class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Deskripsi</h3>
                            <p class="text-gray-600 leading-relaxed">{{ selectedMenuDetail.deskripsi }}</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-3 pt-4 border-t border-gray-100">
                            <Link 
                                :href="route('umkm.menu.edit', [umkm.id, selectedMenuDetail.id])"
                                class="flex-1 bg-gradient-to-r from-purple-600 to-blue-600 text-white text-center py-3 px-4 rounded-xl font-semibold hover:from-purple-700 hover:to-blue-700 transition-all duration-200 shadow-lg hover:shadow-purple-500/25"
                            >
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Produk
                            </Link>
                            <button 
                                @click="deleteMenu(selectedMenuDetail.id, selectedMenuDetail.nama_menu); closeDetailModal()"
                                class="px-4 py-3 border border-red-300 text-red-600 rounded-xl font-semibold hover:bg-red-50 hover:border-red-400 transition-all duration-200"
                            >
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
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