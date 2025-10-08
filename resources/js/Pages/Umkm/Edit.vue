<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

interface Umkm {
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
    facebook?: string;
    instagram?: string;
    twitter?: string;
    whatsapp?: string;
    website?: string;
}

const props = defineProps<{
    umkm: Umkm;
}>();

const form = useForm({
    nama_umkm: props.umkm.nama_umkm || '',
    deskripsi: props.umkm.deskripsi || '',
    kategori: props.umkm.kategori || '',
    alamat: props.umkm.alamat || '',
    no_telepon: props.umkm.no_telepon || '',
    email: props.umkm.email || '',
    latitude: props.umkm.latitude?.toString() || '',
    longitude: props.umkm.longitude?.toString() || '',
    gambar: null as File | null,
    facebook: props.umkm.facebook || '',
    instagram: props.umkm.instagram || '',
    twitter: props.umkm.twitter || '',
    whatsapp: props.umkm.whatsapp || '',
    website: props.umkm.website || '',
});

const mapContainer = ref<HTMLElement>();
const searchQuery = ref('');
const searchResults = ref<any[]>([]);
const isSearching = ref(false);
const showSearchResults = ref(false);
let map: any = null;
let currentLocationMarker: any = null;
let searchTimeout: any = null;

const submit = () => {
    // Always use POST with _method: PUT for Laravel method spoofing
    // This ensures consistent handling regardless of file upload
    form.transform((data) => ({
        ...data,
        _method: 'PUT'
    })).post(route('umkm.update', props.umkm.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        },
        onError: (errors) => {
            // Handle validation errors
            console.log('Validation errors:', errors);
        }
    });
};

const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.gambar = target.files[0];
    }
};

const getCurrentLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                form.latitude = lat.toString();
                form.longitude = lng.toString();
                
                if (map) {
                    map.setView([lat, lng], 15);
                    
                    if (currentLocationMarker) {
                        map.removeLayer(currentLocationMarker);
                    }
                    
                    const L = window.L;
                    currentLocationMarker = L.marker([lat, lng])
                        .addTo(map)
                        .bindPopup('Lokasi UMKM Anda')
                        .openPopup();
                }
            },
            (error) => {
                console.error('Error getting location:', error);
                alert('Tidak dapat mengambil lokasi. Pastikan izin lokasi sudah diberikan.');
            }
        );
    } else {
        alert('Geolocation tidak didukung oleh browser ini.');
    }
};

const searchLocation = async (query: string) => {
    if (!query.trim()) {
        searchResults.value = [];
        showSearchResults.value = false;
        return;
    }

    isSearching.value = true;
    
    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=id&addressdetails=1`
        );
        const data = await response.json();
        
        searchResults.value = data.filter((result: any) => result.lat && result.lon);
        showSearchResults.value = true;
    } catch (error) {
        console.error('Error searching location:', error);
        searchResults.value = [];
    } finally {
        isSearching.value = false;
    }
};

const onSearchInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const query = target.value;
    searchQuery.value = query;
    
    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // Set new timeout for debouncing
    searchTimeout = setTimeout(() => {
        searchLocation(query);
    }, 300);
};

const selectLocation = async (result: any) => {
    const lat = parseFloat(result.lat);
    const lng = parseFloat(result.lon);
    
    // Update form coordinates
    form.latitude = lat.toString();
    form.longitude = lng.toString();
    
    // Update search query with selected location
    searchQuery.value = result.display_name;
    
    // Hide search results
    showSearchResults.value = false;
    
    // Move map to selected location
    if (map) {
        map.setView([lat, lng], 15);
        
        // Remove existing marker
        if (currentLocationMarker) {
            map.removeLayer(currentLocationMarker);
        }
        
        // Add new marker
        const L = window.L;
        currentLocationMarker = L.marker([lat, lng])
            .addTo(map)
            .bindPopup(result.display_name)
            .openPopup();
    }
};

const setMarkerOnMap = (lat: number, lng: number, popupText: string) => {
    if (currentLocationMarker) {
        map.removeLayer(currentLocationMarker);
    }
    
    const L = window.L;
    currentLocationMarker = L.marker([lat, lng])
        .addTo(map)
        .bindPopup(popupText)
        .openPopup();
};

onMounted(async () => {
    try {
        const L = await import('leaflet');
        (window as any).L = L.default;
        
        // Fix for default markers
        const iconRetinaUrl = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png';
        const iconUrl = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png';
        const shadowUrl = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png';
        
        const DefaultIcon = L.default.icon({
            iconRetinaUrl,
            iconUrl,
            shadowUrl,
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        
        L.default.Marker.prototype.options.icon = DefaultIcon;

        // Initialize map with existing coordinates or default to Semarang
        const lat = props.umkm.latitude || -6.9664;
        const lng = props.umkm.longitude || 110.4204;
        
        map = L.default.map(mapContainer.value!).setView([lat, lng], 13);

        // Add tile layer
        L.default.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // If coordinates exist, get address and set search query
        if (props.umkm.latitude && props.umkm.longitude) {
            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${props.umkm.latitude}&lon=${props.umkm.longitude}&addressdetails=1`
                );
                const data = await response.json();
                if (data.display_name) {
                    searchQuery.value = data.display_name;
                }
            } catch (error) {
                console.error('Error getting address:', error);
                // Fallback to alamat field if available
                searchQuery.value = props.umkm.alamat || '';
            }

            currentLocationMarker = L.default.marker([props.umkm.latitude, props.umkm.longitude])
                .addTo(map)
                .bindPopup(props.umkm.nama_umkm)
                .openPopup();
        } else {
            // Set search query to alamat if no coordinates
            searchQuery.value = props.umkm.alamat || '';
        }

        // Add click handler for map
        map.on('click', async (e: any) => {
            const { lat, lng } = e.latlng;
            form.latitude = lat.toString();
            form.longitude = lng.toString();
            
            // Try to get address from coordinates (reverse geocoding)
            try {
                const response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`
                );
                const data = await response.json();
                if (data.display_name) {
                    searchQuery.value = data.display_name;
                }
            } catch (error) {
                console.error('Error getting address:', error);
            }
            
            setMarkerOnMap(lat, lng, `Lokasi UMKM: ${lat.toFixed(6)}, ${lng.toFixed(6)}`);
        });

        // Close search results when clicking outside
        document.addEventListener('click', (e) => {
            const target = e.target as HTMLElement;
            const searchContainer = document.getElementById('location-search-edit')?.closest('.relative');
            if (searchContainer && !searchContainer.contains(target)) {
                showSearchResults.value = false;
            }
        });
    } catch (error) {
        console.error('Error loading map:', error);
    }
});
</script>

<template>
    <Head title="Edit UMKM" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Edit UMKM
                </h2>
                <Link
                    :href="route('umkm.index')"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Kembali ke Daftar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Left Column -->
                                <div class="space-y-6">
                                    <div>
                                        <InputLabel for="nama_umkm" value="Nama UMKM *" />
                                        <TextInput
                                            id="nama_umkm"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="form.nama_umkm"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.nama_umkm" />
                                    </div>

                                    <div>
                                        <InputLabel for="kategori" value="Kategori *" />
                                        <select
                                            id="kategori"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            v-model="form.kategori"
                                            required
                                        >
                                            <option value="">Pilih Kategori</option>
                                            <option value="Makanan & Minuman">Makanan & Minuman</option>
                                            <option value="Fashion & Tekstil">Fashion & Tekstil</option>
                                            <option value="Kerajinan Tangan">Kerajinan Tangan</option>
                                            <option value="Elektronik">Elektronik</option>
                                            <option value="Jasa">Jasa</option>
                                            <option value="Pertanian">Pertanian</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.kategori" />
                                    </div>

                                    <div>
                                        <InputLabel for="deskripsi" value="Deskripsi" />
                                        <textarea
                                            id="deskripsi"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            rows="4"
                                            v-model="form.deskripsi"
                                            placeholder="Ceritakan tentang UMKM Anda..."
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.deskripsi" />
                                    </div>

                                    <div>
                                        <InputLabel for="alamat" value="Alamat *" />
                                        <textarea
                                            id="alamat"
                                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                            rows="3"
                                            v-model="form.alamat"
                                            required
                                            placeholder="Alamat lengkap UMKM"
                                        ></textarea>
                                        <InputError class="mt-2" :message="form.errors.alamat" />
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="space-y-6">
                                    <div>
                                        <InputLabel for="no_telepon" value="No. Telepon" />
                                        <TextInput
                                            id="no_telepon"
                                            type="tel"
                                            class="mt-1 block w-full"
                                            v-model="form.no_telepon"
                                            placeholder="08xxxxxxxxxx"
                                        />
                                        <InputError class="mt-2" :message="form.errors.no_telepon" />
                                    </div>

                                    <div>
                                        <InputLabel for="email" value="Email" />
                                        <TextInput
                                            id="email"
                                            type="email"
                                            class="mt-1 block w-full"
                                            v-model="form.email"
                                            placeholder="email@domain.com"
                                        />
                                        <InputError class="mt-2" :message="form.errors.email" />
                                    </div>

                                    <div>
                                        <InputLabel for="gambar" value="Gambar UMKM" />
                                        <input
                                            id="gambar"
                                            type="file"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                            accept="image/*"
                                            @change="handleImageChange"
                                        />
                                        <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF hingga 2MB. Kosongkan jika tidak ingin mengubah gambar.</p>
                                        <InputError class="mt-2" :message="form.errors.gambar" />
                                    </div>

                                    <!-- Social Media Section -->
                                    <div class="border-t border-gray-200 pt-6">
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">📱 Media Sosial & Kontak</h3>
                                        <div class="space-y-4">
                                            <div>
                                                <InputLabel for="whatsapp" value="WhatsApp" />
                                                <div class="mt-1 relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                                        </svg>
                                                    </div>
                                                    <TextInput
                                                        id="whatsapp"
                                                        type="text"
                                                        class="pl-10 block w-full"
                                                        v-model="form.whatsapp"
                                                        placeholder="628123456789"
                                                    />
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Format: 628123456789 (tanpa tanda +)</p>
                                                <InputError class="mt-2" :message="form.errors.whatsapp" />
                                            </div>

                                            <div>
                                                <InputLabel for="instagram" value="Instagram" />
                                                <div class="mt-1 relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <svg class="w-5 h-5 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                                        </svg>
                                                    </div>
                                                    <TextInput
                                                        id="instagram"
                                                        type="text"
                                                        class="pl-10 block w-full"
                                                        v-model="form.instagram"
                                                        placeholder="nama_umkm"
                                                    />
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Username Instagram tanpa @</p>
                                                <InputError class="mt-2" :message="form.errors.instagram" />
                                            </div>

                                            <div>
                                                <InputLabel for="facebook" value="Facebook" />
                                                <div class="mt-1 relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                        </svg>
                                                    </div>
                                                    <TextInput
                                                        id="facebook"
                                                        type="text"
                                                        class="pl-10 block w-full"
                                                        v-model="form.facebook"
                                                        placeholder="nama.umkm atau ID halaman Facebook"
                                                    />
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Username atau ID halaman Facebook</p>
                                                <InputError class="mt-2" :message="form.errors.facebook" />
                                            </div>

                                            <div>
                                                <InputLabel for="website" value="Website" />
                                                <div class="mt-1 relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9m0 9c-5 0-9-4-9-9s4-9 9-9"/>
                                                        </svg>
                                                    </div>
                                                    <TextInput
                                                        id="website"
                                                        type="url"
                                                        class="pl-10 block w-full"
                                                        v-model="form.website"
                                                        placeholder="https://website-umkm.com"
                                                    />
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">URL lengkap website UMKM Anda</p>
                                                <InputError class="mt-2" :message="form.errors.website" />
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <InputLabel value="Cari Lokasi UMKM" />
                                        <div class="mt-2 relative">
                                            <div class="relative">
                                                <TextInput
                                                    id="location-search-edit"
                                                    type="text"
                                                    class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    v-model="searchQuery"
                                                    @input="onSearchInput"
                                                    @focus="showSearchResults = searchResults.length > 0"
                                                    placeholder="Cari alamat, jalan, atau tempat (contoh: Jl. Pandanaran Semarang)"
                                                />
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                    </svg>
                                                </div>
                                                <div v-if="isSearching" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                                    <svg class="animate-spin h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            
                                            <!-- Search Results Dropdown -->
                                            <div v-if="showSearchResults && searchResults.length > 0" 
                                                 class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base overflow-auto focus:outline-none border border-gray-200">
                                                <div v-for="result in searchResults" 
                                                     :key="result.place_id"
                                                     @click="selectLocation(result)"
                                                     class="cursor-pointer select-none relative py-3 px-4 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100 last:border-b-0">
                                                    <div class="flex items-start space-x-3">
                                                        <svg class="h-5 w-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        </svg>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                                {{ result.display_name.split(',')[0] }}
                                                            </p>
                                                            <p class="text-xs text-gray-500 truncate">
                                                                {{ result.display_name }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3 flex space-x-3">
                                            <button
                                                type="button"
                                                @click="getCurrentLocation"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                Gunakan Lokasi Saat Ini
                                            </button>
                                        </div>
                                        
                                        <!-- Display selected coordinates (read-only) -->
                                        <div v-if="form.latitude && form.longitude" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="text-sm font-medium text-green-800">Lokasi dipilih:</span>
                                            </div>
                                            <p class="text-sm text-green-700 mt-1">
                                                Koordinat: {{ parseFloat(form.latitude).toFixed(6) }}, {{ parseFloat(form.longitude).toFixed(6) }}
                                            </p>
                                        </div>
                                        
                                        <InputError class="mt-2" :message="form.errors.latitude" />
                                        <InputError class="mt-2" :message="form.errors.longitude" />
                                    </div>
                                </div>
                            </div>

                            <!-- Map Section -->
                            <div class="mt-8">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">📍 Pilih Lokasi di Peta</h3>
                                <p class="text-sm text-gray-600 mb-4">Klik pada peta untuk menandai lokasi UMKM Anda</p>
                                <div ref="mapContainer" class="h-64 w-full rounded-lg border border-gray-300"></div>
                            </div>

                            <div class="flex items-center justify-between mt-8">
                                <Link
                                    :href="route('umkm.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    Batal
                                </Link>
                                
                                <PrimaryButton class="ms-4" :disabled="form.processing">
                                    <span v-if="form.processing">Menyimpan...</span>
                                    <span v-else>Perbarui UMKM</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
@import 'leaflet/dist/leaflet.css';
</style>