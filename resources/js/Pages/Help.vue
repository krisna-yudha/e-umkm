<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Help & Support" />

    <AuthenticatedLayout>
        <template #header>
            <div class="bg-gradient-to-r from-indigo-600 via-blue-600 to-cyan-600 px-6 py-8 rounded-lg shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-2">
                            🚀 Help & Support
                        </h2>
                        <p class="text-blue-100 text-lg">
                            Panduan lengkap dan bantuan menggunakan platform E-UMKM
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gradient-to-br from-gray-50 to-indigo-50 min-h-screen">
            <div class="mx-auto max-w-6xl space-y-10 sm:px-6 lg:px-8">
                
                <!-- Quick Links -->
                <div class="group transform hover:scale-[1.02] transition-all duration-300">
                    <div class="overflow-hidden bg-white shadow-xl sm:rounded-2xl border-0 ring-1 ring-blue-200/50">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-8 py-6">
                            <div class="flex items-center space-x-4">
                                <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                                    <span class="text-2xl">🚀</span>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">
                                        Quick Start Guide
                                    </h3>
                                    <p class="text-blue-100 text-sm">
                                        Panduan cepat untuk memulai menggunakan platform
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8 bg-gradient-to-br from-white to-blue-50">
                            <div class="grid gap-6 md:grid-cols-2">
                                <a :href="route('user.profile')" class="group/card block p-6 bg-white border-2 border-blue-100 rounded-xl hover:border-blue-300 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center group-hover/card:scale-110 transition-transform duration-300">
                                                <span class="text-xl text-white">👤</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-lg font-semibold text-gray-900 group-hover/card:text-blue-600 transition-colors">
                                                Dashboard Profile
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                Kelola UMKM dan produk Anda dengan mudah
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                
                                <a :href="route('profile.edit')" class="group/card block p-6 bg-white border-2 border-emerald-100 rounded-xl hover:border-emerald-300 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center group-hover/card:scale-110 transition-transform duration-300">
                                                <span class="text-xl text-white">⚙️</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-lg font-semibold text-gray-900 group-hover/card:text-emerald-600 transition-colors">
                                                Edit Profile
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                Update informasi dan foto profile Anda
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="group transform hover:scale-[1.02] transition-all duration-300">
                    <div class="overflow-hidden bg-white shadow-xl sm:rounded-2xl border-0 ring-1 ring-green-200/50">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-8 py-6">
                            <div class="flex items-center space-x-4">
                                <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                                    <span class="text-2xl">❓</span>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">
                                        Frequently Asked Questions
                                    </h3>
                                    <p class="text-emerald-100 text-sm">
                                        Jawaban untuk pertanyaan yang sering diajukan
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8 bg-gradient-to-br from-white to-green-50 space-y-8">
                            
                            <div class="bg-white p-6 rounded-xl border-l-4 border-blue-500 shadow-sm hover:shadow-md transition-shadow">
                                <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-blue-600 font-bold">1</span>
                                    </span>
                                    Bagaimana cara mendaftarkan UMKM saya?
                                </h4>
                                <div class="ml-11 space-y-2 text-gray-600">
                                    <p class="flex items-center"><span class="text-blue-500 mr-2">•</span> Masuk ke Dashboard Profile</p>
                                    <p class="flex items-center"><span class="text-blue-500 mr-2">•</span> Klik tombol "Daftar UMKM Baru"</p>
                                    <p class="flex items-center"><span class="text-blue-500 mr-2">•</span> Isi formulir pendaftaran lengkap</p>
                                    <p class="flex items-center"><span class="text-blue-500 mr-2">•</span> Upload gambar dan informasi produk</p>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-xl border-l-4 border-emerald-500 shadow-sm hover:shadow-md transition-shadow">
                                <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-emerald-600 font-bold">2</span>
                                    </span>
                                    Bagaimana cara menambahkan produk/menu?
                                </h4>
                                <div class="ml-11 space-y-2 text-gray-600">
                                    <p class="flex items-center"><span class="text-emerald-500 mr-2">•</span> Buka detail UMKM Anda</p>
                                    <p class="flex items-center"><span class="text-emerald-500 mr-2">•</span> Klik tab "Menu/Produk"</p>
                                    <p class="flex items-center"><span class="text-emerald-500 mr-2">•</span> Klik "Tambah Menu Baru"</p>
                                    <p class="flex items-center"><span class="text-emerald-500 mr-2">•</span> Isi informasi produk dan harga</p>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-xl border-l-4 border-amber-500 shadow-sm hover:shadow-md transition-shadow">
                                <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-amber-600 font-bold">3</span>
                                    </span>
                                    Bagaimana cara mengupdate foto profile?
                                </h4>
                                <div class="ml-11 space-y-2 text-gray-600">
                                    <p class="flex items-center"><span class="text-amber-500 mr-2">•</span> Klik nama Anda di pojok kanan atas</p>
                                    <p class="flex items-center"><span class="text-amber-500 mr-2">•</span> Pilih "Edit Profile" dari dropdown</p>
                                    <p class="flex items-center"><span class="text-amber-500 mr-2">•</span> Klik "Pilih Foto" di bagian foto profile</p>
                                    <p class="flex items-center"><span class="text-amber-500 mr-2">•</span> Pilih gambar dan simpan perubahan</p>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-xl border-l-4 border-purple-500 shadow-sm hover:shadow-md transition-shadow">
                                <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                                    <span class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-purple-600 font-bold">4</span>
                                    </span>
                                    Apakah data UMKM saya aman?
                                </h4>
                                <div class="ml-11 text-gray-600">
                                    <p class="leading-relaxed">
                                        Ya, semua data UMKM dilindungi dengan sistem keamanan berlapis dan enkripsi tingkat enterprise. 
                                        Hanya Anda yang dapat mengakses dan mengedit informasi UMKM Anda. Kami menjamin privasi dan keamanan data sesuai standar internasional.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Support -->
                <div class="group transform hover:scale-[1.02] transition-all duration-300">
                    <div class="overflow-hidden bg-white shadow-xl sm:rounded-2xl border-0 ring-1 ring-red-200/50">
                        <div class="bg-gradient-to-r from-red-500 to-pink-600 px-8 py-6">
                            <div class="flex items-center space-x-4">
                                <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                                    <span class="text-2xl">📞</span>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">
                                        Butuh Bantuan Lebih Lanjut?
                                    </h3>
                                    <p class="text-red-100 text-sm">
                                        Tim support profesional kami siap membantu Anda 24/7
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8 bg-gradient-to-br from-white to-red-50">
                            <div class="grid gap-8 md:grid-cols-3">
                                <div class="text-center group/contact">
                                    <div class="mx-auto h-16 w-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center group-hover/contact:scale-110 transition-transform duration-300 shadow-lg">
                                        <span class="text-2xl">📧</span>
                                    </div>
                                    <h4 class="mt-4 text-lg font-semibold text-gray-900">Email Support</h4>
                                    <p class="mt-2 text-blue-600 font-medium">support@e-umkm.com</p>
                                    <p class="mt-1 text-sm text-gray-500">Response dalam 24 jam</p>
                                </div>
                                
                                <div class="text-center group/contact">
                                    <div class="mx-auto h-16 w-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center group-hover/contact:scale-110 transition-transform duration-300 shadow-lg">
                                        <span class="text-2xl">💬</span>
                                    </div>
                                    <h4 class="mt-4 text-lg font-semibold text-gray-900">WhatsApp</h4>
                                    <p class="mt-2 text-green-600 font-medium">+62 812-3456-7890</p>
                                    <p class="mt-1 text-sm text-gray-500">Chat langsung dengan tim</p>
                                </div>
                                
                                <div class="text-center group/contact">
                                    <div class="mx-auto h-16 w-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center group-hover/contact:scale-110 transition-transform duration-300 shadow-lg">
                                        <span class="text-2xl">🕐</span>
                                    </div>
                                    <h4 class="mt-4 text-lg font-semibold text-gray-900">Jam Operasional</h4>
                                    <p class="mt-2 text-purple-600 font-medium">Senin - Jumat</p>
                                    <p class="mt-1 text-sm text-gray-500">08:00 - 17:00 WIB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tips & Tricks -->
                <div class="group transform hover:scale-[1.02] transition-all duration-300">
                    <div class="overflow-hidden bg-white shadow-xl sm:rounded-2xl border-0 ring-1 ring-yellow-200/50">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-8 py-6">
                            <div class="flex items-center space-x-4">
                                <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                                    <span class="text-2xl">💡</span>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">
                                        Tips & Tricks Pro
                                    </h3>
                                    <p class="text-amber-100 text-sm">
                                        Rahasia untuk memaksimalkan potensi bisnis UMKM Anda
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8 bg-gradient-to-br from-white to-amber-50">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="flex items-start p-4 bg-white rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
                                    <span class="flex-shrink-0 h-8 w-8 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-green-600 text-sm font-bold">✓</span>
                                    </span>
                                    <span class="text-gray-700 leading-relaxed">
                                        Upload foto produk dengan kualitas tinggi dan pencahayaan yang baik
                                    </span>
                                </div>
                                
                                <div class="flex items-start p-4 bg-white rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
                                    <span class="flex-shrink-0 h-8 w-8 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-green-600 text-sm font-bold">✓</span>
                                    </span>
                                    <span class="text-gray-700 leading-relaxed">
                                        Tulis deskripsi produk yang lengkap dan menarik untuk meningkatkan penjualan
                                    </span>
                                </div>
                                
                                <div class="flex items-start p-4 bg-white rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
                                    <span class="flex-shrink-0 h-8 w-8 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-green-600 text-sm font-bold">✓</span>
                                    </span>
                                    <span class="text-gray-700 leading-relaxed">
                                        Update informasi kontak dan lokasi secara berkala untuk akurasi
                                    </span>
                                </div>
                                
                                <div class="flex items-start p-4 bg-white rounded-lg border border-gray-100 hover:shadow-md transition-shadow">
                                    <span class="flex-shrink-0 h-8 w-8 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-green-600 text-sm font-bold">✓</span>
                                    </span>
                                    <span class="text-gray-700 leading-relaxed">
                                        Manfaatkan fitur mapping untuk memudahkan pelanggan menemukan lokasi
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>