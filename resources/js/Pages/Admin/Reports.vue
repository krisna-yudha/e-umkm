<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Reports & Analytics
                    </h2>
                    <p class="text-gray-600 mt-1">Analisis komprehensif aktivitas UMKM</p>
                </div>
                <div class="flex space-x-3">
                    <Link
                        :href="route('admin.dashboard')"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Dashboard
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 bg-gradient-to-br from-gray-50 to-red-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total UMKM</p>
                                <p class="text-2xl font-bold text-gray-900">{{ totalUmkm }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">UMKM Aktif</p>
                                <p class="text-2xl font-bold text-gray-900">{{ activeUmkm }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 012-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Kategori</p>
                                <p class="text-2xl font-bold text-gray-900">{{ totalCategories }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Statistics -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Statistik Kategori</h3>
                    <div v-if="category_stats && category_stats.length > 0" class="space-y-4 max-h-80 overflow-y-auto">
                        <div v-for="stat in category_stats" :key="stat.kategori" class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full mr-3 bg-blue-100"></div>
                                <span class="text-gray-700 font-medium">{{ stat.kategori }}</span>
                            </div>
                            <span class="text-lg font-bold text-gray-900">{{ stat.count }}</span>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500">
                        Belum ada data kategori
                    </div>
                </div>

                <!-- Status Statistics -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Statistik Status UMKM</h3>
                    <div v-if="status_stats && status_stats.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-for="stat in status_stats" :key="stat.status" class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center">
                                <div :class="[
                                    'w-4 h-4 rounded-full mr-3',
                                    stat.status === 'active' ? 'bg-green-500' : 'bg-gray-400'
                                ]"></div>
                                <span class="text-gray-700 font-medium capitalize">{{ 
                                    stat.status === 'active' ? 'Aktif' : 'Tidak Aktif' 
                                }}</span>
                            </div>
                            <span class="text-xl font-bold text-gray-900">{{ stat.count }}</span>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500">
                        Belum ada data status
                    </div>
                </div>

                <!-- Monthly Registration Statistics -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Statistik Pendaftaran Bulanan (12 Bulan Terakhir)</h3>
                    <div v-if="monthly_stats && monthly_stats.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pendaftaran</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="stat in monthly_stats" :key="`${stat.year}-${stat.month}`" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ getMonthName(stat.month.toString().padStart(2, '0')) }} {{ stat.year }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ stat.count }} UMKM
                                                </span>
                                            </div>
                                            <div class="ml-4 flex-1">
                                                <div class="bg-gray-200 rounded-full h-2">
                                                    <div 
                                                        class="bg-blue-600 h-2 rounded-full" 
                                                        :style="{ width: (stat.count / Math.max(...monthly_stats.map((s: any) => s.count)) * 100) + '%' }"
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else class="text-center text-gray-500 py-8">
                        Belum ada data pendaftaran
                    </div>
                </div>

                <!-- Download Report Button -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Ekspor Laporan</h3>
                        <p class="text-gray-600 mb-6">Download laporan lengkap dalam format yang sesuai kebutuhan Anda</p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <button 
                                class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-all duration-200 shadow-lg"
                                @click="downloadReport('excel')"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download Excel
                            </button>
                            <button 
                                class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition-all duration-200 shadow-lg"
                                @click="downloadReport('pdf')"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                Download PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { computed } from 'vue'

interface MonthlyStats {
    year: number;
    month: number;
    count: number;
}

interface CategoryStats {
    kategori: string;
    count: number;
}

interface StatusStats {
    status: string;
    count: number;
}

interface Props {
    monthly_stats: MonthlyStats[];
    category_stats: CategoryStats[];
    status_stats: StatusStats[];
}

const props = defineProps<Props>()

const totalUmkm = computed(() => {
    return props.category_stats.reduce((total, stat) => total + stat.count, 0)
})

const activeUmkm = computed(() => {
    const activeStat = props.status_stats.find(stat => stat.status === 'active')
    return activeStat ? activeStat.count : 0
})

const totalCategories = computed(() => {
    return props.category_stats.length
})

const getMonthName = (month: string): string => {
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ]
    return months[parseInt(month) - 1] || ''
}

const downloadReport = (type: 'excel' | 'pdf') => {
    // Implementation for downloading reports
    console.log(`Downloading ${type} report`)
}
</script>