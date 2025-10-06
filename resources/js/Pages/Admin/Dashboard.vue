<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

// TypeScript Interfaces
interface UmkmStats {
    total_umkm: number;
    active_umkm: number;
    inactive_umkm: number;
    total_users: number;
}

interface RecentUmkm {
    id: number;
    nama_umkm: string;
    kategori: string;
    alamat: string;
    status: string;
    created_at: string;
    user: {
        name: string;
        email: string;
    };
}

interface SearchResult {
    umkms: any[];
    users: any[];
    query?: string;
}

// Props Definition
const props = defineProps<{
    stats: UmkmStats;
    recent_umkm: RecentUmkm[];
}>();

// Reactive State
const searchQuery = ref('');
const searchResults = ref<SearchResult>({ umkms: [], users: [] });
const isSearching = ref(false);
const showSearchResults = ref(false);

// Search Functions
const performSearch = async (): Promise<void> => {
    if (!searchQuery.value.trim()) {
        showSearchResults.value = false;
        return;
    }

    isSearching.value = true;
    try {
        const response = await fetch(`/admin/search?q=${encodeURIComponent(searchQuery.value.trim())}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            searchResults.value = data;
            showSearchResults.value = true;
        } else {
            console.error('Search error:', response.statusText);
        }
    } catch (error) {
        console.error('Search error:', error);
    } finally {
        isSearching.value = false;
    }
};

const clearSearch = (): void => {
    searchQuery.value = '';
    searchResults.value = { umkms: [], users: [] };
    showSearchResults.value = false;
};

// Debounce Search
let searchTimeout: number;
const onSearchInput = (): void => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch();
    }, 500);
};

// Utility Functions
const calculatePercentage = (value: number, total: number): number => {
    return total > 0 ? Math.round((value / total) * 100) : 0;
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('id-ID');
};

const focusSearchInput = (): void => {
    searchQuery.value = '';
    const input = document.querySelector('input[type=text]') as HTMLInputElement;
    input?.focus();
};
</script>

<template>
    <Head title="Admin Dashboard" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="bg-gradient-to-r from-red-600 to-orange-600 -mx-4 -mt-4 px-4 pt-4 pb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-bold text-2xl text-white leading-tight">
                                Admin Dashboard
                            </h2>
                            <p class="text-red-100 text-sm">Kelola dan pantau sistem UMKM</p>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-8 bg-gradient-to-br from-gray-50 to-red-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Search Box -->
                <div class="mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">🔍 Pencarian Cepat</h3>
                            <button 
                                v-if="showSearchResults"
                                @click="clearSearch"
                                class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors"
                            >
                                Bersihkan
                            </button>
                        </div>
                        
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                v-model="searchQuery"
                                @input="onSearchInput"
                                type="text"
                                placeholder="Cari UMKM atau User (nama, email, kategori, alamat)..."
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent text-gray-900 transition-all"
                            />
                            <div v-if="isSearching" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="animate-spin h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Results -->
                <div v-if="showSearchResults" class="mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <h4 class="text-lg font-bold text-gray-900 mb-4">
                            📋 Hasil Pencarian: "{{ searchResults.query }}"
                        </h4>
                        
                        <!-- UMKM Results -->
                        <div v-if="searchResults.umkms && searchResults.umkms.length > 0" class="mb-6">
                            <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                🏪 UMKM ({{ searchResults.umkms.length }})
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div 
                                    v-for="umkm in searchResults.umkms" 
                                    :key="'umkm-' + umkm.id"
                                    class="border border-gray-200 rounded-xl p-4 hover:border-red-300 transition-colors"
                                >
                                    <div class="flex justify-between items-start mb-2">
                                        <h6 class="font-semibold text-gray-900">{{ umkm.nama_umkm }}</h6>
                                        <span :class="umkm.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                              class="px-2 py-1 text-xs rounded-full font-medium">
                                            {{ umkm.status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1">📍 {{ umkm.alamat }}</p>
                                    <p class="text-sm text-gray-600 mb-2">🏷️ {{ umkm.kategori }}</p>
                                    <p class="text-xs text-gray-500">👤 {{ umkm.user?.name }} ({{ umkm.user?.email }})</p>
                                </div>
                            </div>
                        </div>

                        <!-- Users Results -->
                        <div v-if="searchResults.users && searchResults.users.length > 0">
                            <h5 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                👥 Pengguna ({{ searchResults.users.length }})
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div 
                                    v-for="user in searchResults.users" 
                                    :key="'user-' + user.id"
                                    class="border border-gray-200 rounded-xl p-4 hover:border-red-300 transition-colors"
                                >
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h6 class="font-semibold text-gray-900">{{ user.name }}</h6>
                                            <p class="text-sm text-gray-600">{{ user.email }}</p>
                                            <p v-if="user.umkm" class="text-xs text-green-600 mt-1">
                                                🏪 Memiliki UMKM: {{ user.umkm.nama_umkm }}
                                            </p>
                                            <p v-else class="text-xs text-gray-500 mt-1">
                                                📝 Belum mendaftarkan UMKM
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- No Results -->
                        <div v-if="(!searchResults.umkms || searchResults.umkms.length === 0) && (!searchResults.users || searchResults.users.length === 0)" 
                             class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada hasil ditemukan</h4>
                            <p class="text-gray-600">Coba gunakan kata kunci yang berbeda</p>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards with Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-blue-100 hover:shadow-xl transition-shadow group">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-100 rounded-full group-hover:bg-blue-200 transition-colors">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-600">Total UMKM</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total_umkm }}</p>
                            </div>
                        </div>
                        <Link 
                            :href="route('admin.umkm')"
                            class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors"
                        >
                            Kelola UMKM →
                        </Link>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-green-100 hover:shadow-xl transition-shadow group">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-full group-hover:bg-green-200 transition-colors">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-600">UMKM Aktif</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.active_umkm }}</p>
                            </div>
                        </div>
                        <div class="mt-4 text-green-600 text-sm font-medium">
                            {{ calculatePercentage(stats.active_umkm, stats.total_umkm) }}% dari total
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-red-100 hover:shadow-xl transition-shadow group">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-100 rounded-full group-hover:bg-red-200 transition-colors">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-600">UMKM Nonaktif</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.inactive_umkm }}</p>
                            </div>
                        </div>
                        <div class="mt-4 text-red-600 text-sm font-medium">
                            Perlu perhatian admin
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-purple-100 hover:shadow-xl transition-shadow group">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-100 rounded-full group-hover:bg-purple-200 transition-colors">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-600">Total Pengguna</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total_users }}</p>
                            </div>
                        </div>
                        <Link 
                            :href="route('admin.password-reset-requests')"
                            class="mt-4 inline-flex items-center text-purple-600 hover:text-purple-800 text-sm font-medium transition-colors"
                        >
                            Reset Password →
                        </Link>
                    </div>
                </div>

                <!-- Quick Access Menu -->
                <div class="mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">⚡ Akses Cepat</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <Link 
                                :href="route('admin.umkm')"
                                class="flex flex-col items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors group"
                            >
                                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mb-2 group-hover:bg-blue-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700 text-center">Kelola UMKM</span>
                            </Link>

                            <Link 
                                :href="route('admin.password-reset-requests')"
                                class="flex flex-col items-center p-4 bg-green-50 hover:bg-green-100 rounded-xl transition-colors group"
                            >
                                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mb-2 group-hover:bg-green-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700 text-center">Reset Password</span>
                            </Link>

                            <Link 
                                :href="route('admin.reports')"
                                class="flex flex-col items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-xl transition-colors group"
                            >
                                <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mb-2 group-hover:bg-purple-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700 text-center">Laporan</span>
                            </Link>

                            <button 
                                @click="focusSearchInput"
                                class="flex flex-col items-center p-4 bg-red-50 hover:bg-red-100 rounded-xl transition-colors group"
                            >
                                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mb-2 group-hover:bg-red-600 transition-colors">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-700 text-center">Pencarian</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Recent UMKM -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">UMKM Terbaru</h3>
                        <Link
                            :href="route('admin.umkm')"
                            class="text-red-600 hover:text-red-800 font-medium text-sm transition-colors"
                        >
                            Lihat Semua →
                        </Link>
                    </div>

                    <div v-if="!recent_umkm || recent_umkm.length === 0" class="text-center py-12">
                        <div class="w-20 h-20 bg-gradient-to-br from-red-100 to-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Belum ada UMKM terdaftar</h4>
                        <p class="text-gray-600">Sistem siap menerima pendaftaran UMKM baru</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UMKM</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="umkm in recent_umkm" :key="umkm.id" class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ umkm.nama_umkm }}</div>
                                        <div class="text-sm text-gray-500">{{ umkm.alamat }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ umkm.user?.name }}</div>
                                        <div class="text-sm text-gray-500">{{ umkm.user?.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ umkm.kategori }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="umkm.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" 
                                              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            {{ umkm.status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(umkm.created_at) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>