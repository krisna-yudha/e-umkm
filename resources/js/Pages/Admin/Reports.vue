<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        📊 Reports & Analytics
                    </h2>
                    <p class="text-gray-600 mt-1">Analisis komprehensif aktivitas UMKM dengan visualisasi data</p>
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

        <div class="py-8 bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total UMKM</p>
                                <p class="text-2xl font-bold text-gray-900">{{ totalUmkm }}</p>
                                <p class="text-xs text-gray-500">Terdaftar di sistem</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">UMKM Aktif</p>
                                <p class="text-2xl font-bold text-gray-900">{{ activeUmkm }}</p>
                                <p class="text-xs text-gray-500">{{ activePercentage }}% dari total</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Kategori</p>
                                <p class="text-2xl font-bold text-gray-900">{{ totalCategories }}</p>
                                <p class="text-xs text-gray-500">Jenis usaha berbeda</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Monthly Registration Chart -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">📈 Tren Pendaftaran Bulanan</h3>
                            <p class="text-gray-600 text-sm">Grafik pendaftaran UMKM 12 bulan terakhir</p>
                        </div>
                        
                        <!-- Monthly Bar Chart -->
                        <div v-if="monthly_stats.length > 0" class="space-y-4">
                            <div v-for="stat in monthly_stats" :key="`${stat.year}-${stat.month}`" class="group">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700">{{ getMonthName(stat.month.toString().padStart(2, '0')) }} {{ stat.year }}</span>
                                    <span class="text-sm font-bold text-blue-600">{{ stat.count }} UMKM</span>
                                </div>
                                <div class="bg-gray-200 rounded-full h-6 relative overflow-hidden">
                                    <div 
                                        class="bg-gradient-to-r from-blue-500 to-blue-600 h-6 rounded-full transition-all duration-700 ease-out group-hover:from-blue-600 group-hover:to-blue-700"
                                        :style="{ width: (stat.count / Math.max(...monthly_stats.map(s => s.count)) * 100) + '%' }"
                                    >
                                        <div class="absolute inset-0 bg-white bg-opacity-20 animate-pulse"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="h-48 flex items-center justify-center bg-gray-50 rounded-lg">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 012-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">Belum ada data pendaftaran</p>
                                <p class="text-gray-400 text-sm">Data akan muncul setelah ada pendaftaran UMKM</p>
                            </div>
                        </div>
                    </div>

                    <!-- Status Distribution Chart -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">� Distribusi Status UMKM</h3>
                            <p class="text-gray-600 text-sm">Perbandingan status aktif dan non-aktif</p>
                        </div>
                        
                        <!-- Status Cards -->
                        <div v-if="status_stats.length > 0" class="space-y-4">
                            <div v-for="stat in status_stats" :key="stat.status" 
                                 class="relative overflow-hidden rounded-xl border-2 transition-all duration-300 hover:shadow-lg"
                                 :class="stat.status === 'active' ? 'bg-green-50 border-green-200 hover:border-green-300' : 'bg-red-50 border-red-200 hover:border-red-300'">
                                <div class="p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center">
                                            <div :class="[
                                                'w-4 h-4 rounded-full mr-3',
                                                stat.status === 'active' ? 'bg-green-500' : 'bg-red-500'
                                            ]"></div>
                                            <span class="font-semibold text-gray-800">{{ stat.status === 'active' ? '✅ Aktif' : '❌ Tidak Aktif' }}</span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-gray-900">{{ stat.count }}</div>
                                            <div class="text-sm text-gray-600">{{ Math.round((stat.count / totalUmkm) * 100) }}%</div>
                                        </div>
                                    </div>
                                    <!-- Progress bar -->
                                    <div class="bg-white bg-opacity-60 rounded-full h-2">
                                        <div 
                                            :class="stat.status === 'active' ? 'bg-green-500' : 'bg-red-500'"
                                            class="h-2 rounded-full transition-all duration-700"
                                            :style="{ width: Math.round((stat.count / totalUmkm) * 100) + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="h-48 flex items-center justify-center bg-gray-50 rounded-lg">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 font-medium">Belum ada data status</p>
                                <p class="text-gray-400 text-sm">Data status akan muncul setelah ada UMKM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Distribution Chart -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 mb-8">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">📊 Distribusi Kategori UMKM</h3>
                        <p class="text-gray-600 text-sm">Jumlah UMKM berdasarkan kategori usaha</p>
                    </div>
                    
                    <!-- Category Bar Chart -->
                    <div v-if="category_stats.length > 0" class="space-y-6">
                        <div v-for="(stat, index) in category_stats" :key="stat.kategori" class="group">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div 
                                        class="w-4 h-4 rounded-full mr-3"
                                        :class="[
                                            index % 6 === 0 ? 'bg-blue-500' : 
                                            index % 6 === 1 ? 'bg-green-500' :
                                            index % 6 === 2 ? 'bg-yellow-500' :
                                            index % 6 === 3 ? 'bg-purple-500' :
                                            index % 6 === 4 ? 'bg-pink-500' :
                                            'bg-indigo-500'
                                        ]"
                                    ></div>
                                    <span class="font-semibold text-gray-800">{{ stat.kategori }}</span>
                                </div>
                                <span class="text-lg font-bold text-gray-900">{{ stat.count }} UMKM</span>
                            </div>
                            <div class="bg-gray-200 rounded-full h-8 relative overflow-hidden">
                                <div 
                                    class="h-8 rounded-full transition-all duration-700 ease-out relative"
                                    :class="[
                                        index % 6 === 0 ? 'bg-gradient-to-r from-blue-500 to-blue-600 group-hover:from-blue-600 group-hover:to-blue-700' : 
                                        index % 6 === 1 ? 'bg-gradient-to-r from-green-500 to-green-600 group-hover:from-green-600 group-hover:to-green-700' :
                                        index % 6 === 2 ? 'bg-gradient-to-r from-yellow-500 to-yellow-600 group-hover:from-yellow-600 group-hover:to-yellow-700' :
                                        index % 6 === 3 ? 'bg-gradient-to-r from-purple-500 to-purple-600 group-hover:from-purple-600 group-hover:to-purple-700' :
                                        index % 6 === 4 ? 'bg-gradient-to-r from-pink-500 to-pink-600 group-hover:from-pink-600 group-hover:to-pink-700' :
                                        'bg-gradient-to-r from-indigo-500 to-indigo-600 group-hover:from-indigo-600 group-hover:to-indigo-700'
                                    ]"
                                    :style="{ width: (stat.count / Math.max(...category_stats.map(s => s.count)) * 100) + '%' }"
                                >
                                    <div class="absolute inset-0 bg-white bg-opacity-20 animate-pulse"></div>
                                    <div class="absolute right-2 top-1/2 transform -translate-y-1/2">
                                        <span class="text-white text-sm font-bold">{{ Math.round((stat.count / totalUmkm) * 100) }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="h-48 flex items-center justify-center bg-gray-50 rounded-lg">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">Belum ada data kategori</p>
                            <p class="text-gray-400 text-sm">Kategori akan muncul setelah UMKM dibuat</p>
                        </div>
                    </div>
                </div>

                <!-- Detailed Statistics Tables -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Category Statistics Table -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">📋 Detail Kategori</h3>
                        <div v-if="category_stats && category_stats.length > 0" class="space-y-3 max-h-80 overflow-y-auto">
                            <div v-for="stat in category_stats" :key="stat.kategori" 
                                 class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-3 bg-blue-500"></div>
                                    <span class="text-gray-700 font-medium">{{ stat.kategori }}</span>
                                </div>
                                <span class="text-lg font-bold text-blue-600">{{ stat.count }}</span>
                            </div>
                        </div>
                        <div v-else class="text-center text-gray-500 py-8">
                            Belum ada data kategori
                        </div>
                    </div>

                    <!-- Status Statistics Table -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">🔄 Detail Status</h3>
                        <div v-if="status_stats && status_stats.length > 0" class="space-y-4">
                            <div v-for="stat in status_stats" :key="stat.status" 
                                 class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div :class="[
                                        'w-4 h-4 rounded-full mr-3',
                                        stat.status === 'active' ? 'bg-green-500' : 'bg-red-500'
                                    ]"></div>
                                    <span class="text-gray-700 font-medium capitalize">{{ 
                                        stat.status === 'active' ? '✅ Aktif' : '❌ Tidak Aktif' 
                                    }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl font-bold text-gray-900">{{ stat.count }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ Math.round((stat.count / totalUmkm) * 100) }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center text-gray-500 py-8">
                            Belum ada data status
                        </div>
                    </div>
                </div>

                <!-- Monthly Registration Statistics Table -->
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">📅 Pendaftaran Bulanan (12 Bulan Terakhir)</h3>
                    <div v-if="monthly_stats && monthly_stats.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Pendaftaran</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visualisasi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="stat in monthly_stats" :key="`${stat.year}-${stat.month}`" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ getMonthName(stat.month.toString().padStart(2, '0')) }} {{ stat.year }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ stat.count }} UMKM
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-1">
                                                <div class="bg-gray-200 rounded-full h-2">
                                                    <div 
                                                        class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full transition-all duration-500" 
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

                <!-- Download Report Section -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <div class="text-center">
                        <div class="mb-6">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">📄 Ekspor Laporan</h3>
                            <p class="text-gray-600">Download laporan lengkap dalam format yang sesuai kebutuhan Anda</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <button 
                                class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition-all duration-200 shadow-lg hover:shadow-xl"
                                @click="downloadReport('excel')"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download Excel
                            </button>
                            <button 
                                class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition-all duration-200 shadow-lg hover:shadow-xl"
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
import BarChart from '@/Components/Charts/BarChart.vue'
import PieChart from '@/Components/Charts/PieChart.vue'
import LineChart from '@/Components/Charts/LineChart.vue'
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

const activePercentage = computed(() => {
    return totalUmkm.value > 0 ? Math.round((activeUmkm.value / totalUmkm.value) * 100) : 0
})

const totalCategories = computed(() => {
    return props.category_stats.length
})

// Chart data preparations
const monthlyChartData = computed(() => {
    const sortedStats = [...props.monthly_stats].reverse() // Show oldest to newest
    return {
        labels: sortedStats.map(stat => `${getMonthName(stat.month.toString().padStart(2, '0'))} ${stat.year}`),
        datasets: [{
            label: 'Pendaftaran UMKM',
            data: sortedStats.map(stat => stat.count),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    }
})

const statusChartData = computed(() => {
    return {
        labels: props.status_stats.map(stat => stat.status === 'active' ? 'Aktif' : 'Tidak Aktif'),
        datasets: [{
            label: 'Jumlah UMKM',
            data: props.status_stats.map(stat => stat.count),
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderColor: [
                'rgb(34, 197, 94)',
                'rgb(239, 68, 68)'
            ],
            borderWidth: 2
        }]
    }
})

const categoryChartData = computed(() => {
    // Generate different colors for each category
    const colors = [
        'rgba(59, 130, 246, 0.8)',
        'rgba(16, 185, 129, 0.8)',
        'rgba(245, 158, 11, 0.8)',
        'rgba(239, 68, 68, 0.8)',
        'rgba(139, 92, 246, 0.8)',
        'rgba(236, 72, 153, 0.8)',
        'rgba(14, 165, 233, 0.8)',
        'rgba(34, 197, 94, 0.8)'
    ]
    
    return {
        labels: props.category_stats.map(stat => stat.kategori),
        datasets: [{
            label: 'Jumlah UMKM',
            data: props.category_stats.map(stat => stat.count),
            backgroundColor: props.category_stats.map((_, index) => colors[index % colors.length]),
            borderColor: props.category_stats.map((_, index) => colors[index % colors.length].replace('0.8', '1')),
            borderWidth: 2
        }]
    }
})

const getMonthName = (month: string): string => {
    const months = [
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
    ]
    return months[parseInt(month) - 1] || ''
}

const downloadReport = (type: 'excel' | 'pdf') => {
    // Implementation for downloading reports
    // Download report functionality
    // Here you would typically make an API call to generate and download the report
}
</script>