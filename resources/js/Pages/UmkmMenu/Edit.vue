<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
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
    menu: UmkmMenu;
}>();

const form = useForm({
    nama_menu: props.menu.nama_menu,
    deskripsi: props.menu.deskripsi || '',
    harga: props.menu.harga.toString(),
    kategori_menu: props.menu.kategori_menu || '',
    gambar_menu: null as File | null,
    tersedia: props.menu.tersedia
});

const fileInput = ref<HTMLInputElement>();

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.gambar_menu = target.files[0];
    }
};

const submit = () => {
    // Use forceFormData only when there's a file upload
    if (form.gambar_menu && form.gambar_menu instanceof File) {
        form.put(route('umkm.menu.update', [props.umkm.id, props.menu.id]), {
            forceFormData: true
        });
    } else {
        // For regular updates without file, use normal PUT
        form.put(route('umkm.menu.update', [props.umkm.id, props.menu.id]));
    }
};
</script>

<template>
    <Head :title="`Edit Menu - ${menu.nama_menu}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link 
                    :href="route('umkm.menu.index', umkm.id)"
                    class="text-gray-500 hover:text-gray-700 transition-colors duration-200"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </Link>
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Edit Menu - {{ menu.nama_menu }}
                    </h2>
                    <p class="text-sm text-gray-600">Perbarui informasi produk/menu Anda</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl border border-gray-100">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Nama Menu -->
                        <div>
                            <label for="nama_menu" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Menu/Produk *
                            </label>
                            <input 
                                id="nama_menu"
                                v-model="form.nama_menu"
                                type="text" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                                placeholder="Contoh: Nasi Gudeg, Batik Tulis, dll"
                                required
                            >
                            <div v-if="form.errors.nama_menu" class="mt-1 text-sm text-red-600">
                                {{ form.errors.nama_menu }}
                            </div>
                        </div>

                        <!-- Kategori Menu -->
                        <div>
                            <label for="kategori_menu" class="block text-sm font-medium text-gray-700 mb-2">
                                Kategori
                            </label>
                            <input 
                                id="kategori_menu"
                                v-model="form.kategori_menu"
                                type="text" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                                placeholder="Contoh: Makanan, Minuman, Kerajinan, dll"
                            >
                            <div v-if="form.errors.kategori_menu" class="mt-1 text-sm text-red-600">
                                {{ form.errors.kategori_menu }}
                            </div>
                        </div>

                        <!-- Harga -->
                        <div>
                            <label for="harga" class="block text-sm font-medium text-gray-700 mb-2">
                                Harga *
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                <input 
                                    id="harga"
                                    v-model="form.harga"
                                    type="number" 
                                    min="0"
                                    step="1000"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200"
                                    placeholder="0"
                                    required
                                >
                            </div>
                            <div v-if="form.errors.harga" class="mt-1 text-sm text-red-600">
                                {{ form.errors.harga }}
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi
                            </label>
                            <textarea 
                                id="deskripsi"
                                v-model="form.deskripsi"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-purple-500 focus:border-purple-500 transition-colors duration-200 resize-none"
                                placeholder="Jelaskan detail produk/menu Anda..."
                            ></textarea>
                            <div v-if="form.errors.deskripsi" class="mt-1 text-sm text-red-600">
                                {{ form.errors.deskripsi }}
                            </div>
                        </div>

                        <!-- Current Image Display -->
                        <div v-if="menu.gambar_menu" class="space-y-4">
                            <label class="block text-sm font-medium text-gray-700">
                                Foto Saat Ini
                            </label>
                            <div class="w-48 h-48 rounded-lg overflow-hidden border border-gray-300">
                                <img 
                                    :src="`/storage/${menu.gambar_menu}`" 
                                    :alt="menu.nama_menu"
                                    class="w-full h-full object-cover"
                                >
                            </div>
                        </div>

                        <!-- Upload Gambar Baru -->
                        <div>
                            <label for="gambar_menu" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ menu.gambar_menu ? 'Ganti Foto Produk' : 'Upload Foto Produk' }}
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-400 transition-colors duration-200">
                                <input 
                                    ref="fileInput"
                                    type="file" 
                                    accept="image/*"
                                    @change="handleFileUpload"
                                    class="hidden"
                                >
                                <div v-if="!form.gambar_menu" @click="fileInput?.click()" class="cursor-pointer">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span class="font-medium text-purple-600">Klik untuk upload</span> foto baru
                                    </p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 2MB</p>
                                </div>
                                <div v-else class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ form.gambar_menu.name }}</span>
                                    <button 
                                        type="button"
                                        @click="form.gambar_menu = null"
                                        class="text-red-500 hover:text-red-700"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                            <div v-if="form.errors.gambar_menu" class="mt-1 text-sm text-red-600">
                                {{ form.errors.gambar_menu }}
                            </div>
                        </div>

                        <!-- Status Ketersediaan -->
                        <div>
                            <div class="flex items-center">
                                <input 
                                    id="tersedia"
                                    v-model="form.tersedia"
                                    type="checkbox"
                                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                                >
                                <label for="tersedia" class="ml-2 block text-sm text-gray-700">
                                    Produk tersedia untuk dijual
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                            <Link 
                                :href="route('umkm.menu.index', umkm.id)"
                                class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200 font-medium"
                            >
                                Batal
                            </Link>
                            <button 
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-200 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Menyimpan...</span>
                                <span v-else>Update Menu</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>