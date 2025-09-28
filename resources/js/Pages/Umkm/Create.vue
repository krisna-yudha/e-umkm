<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const form = useForm({
    nama_umkm: '',
    deskripsi: '',
    kategori: '',
    alamat: '',
    no_telepon: '',
    email: '',
    latitude: '',
    longitude: '',
    gambar: null as File | null,
});

const mapContainer = ref<HTMLElement>();
const currentLocationMarker = ref<any>(null);
let map: any = null;

const submit = () => {
    form.post(route('umkm.store'), {
        forceFormData: true,
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
                    
                    if (currentLocationMarker.value) {
                        map.removeLayer(currentLocationMarker.value);
                    }
                    
                    // Dynamic import for L
                    import('leaflet').then(({ default: L }) => {
                        currentLocationMarker.value = L.marker([lat, lng]).addTo(map)
                            .bindPopup('Lokasi UMKM Anda')
                            .openPopup();
                    });
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

onMounted(async () => {
    // Import Leaflet dynamically
    const L = await import('leaflet');
    
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

    // Initialize map
    map = L.default.map(mapContainer.value!).setView([-6.2088, 106.8456], 11); // Jakarta coordinates

    // Add tile layer
    L.default.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add click event to map
    map.on('click', (e: any) => {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        form.latitude = lat.toString();
        form.longitude = lng.toString();
        
        if (currentLocationMarker.value) {
            map.removeLayer(currentLocationMarker.value);
        }
        
        currentLocationMarker.value = L.default.marker([lat, lng]).addTo(map)
            .bindPopup(`Lokasi UMKM: ${lat.toFixed(6)}, ${lng.toFixed(6)}`)
            .openPopup();
    });
});
</script>

<template>
    <Head title="Tambah UMKM" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tambah UMKM Baru
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit" enctype="multipart/form-data">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                            autofocus
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
                                        <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF hingga 2MB</p>
                                        <InputError class="mt-2" :message="form.errors.gambar" />
                                    </div>

                                    <div>
                                        <InputLabel value="Koordinat Lokasi" />
                                        <div class="mt-2 grid grid-cols-2 gap-4">
                                            <div>
                                                <InputLabel for="latitude" value="Latitude" />
                                                <TextInput
                                                    id="latitude"
                                                    type="number"
                                                    step="any"
                                                    class="mt-1 block w-full"
                                                    v-model="form.latitude"
                                                    placeholder="-6.2088"
                                                />
                                            </div>
                                            <div>
                                                <InputLabel for="longitude" value="Longitude" />
                                                <TextInput
                                                    id="longitude"
                                                    type="number"
                                                    step="any"
                                                    class="mt-1 block w-full"
                                                    v-model="form.longitude"
                                                    placeholder="106.8456"
                                                />
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            @click="getCurrentLocation"
                                            class="mt-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            Gunakan Lokasi Saat Ini
                                        </button>
                                        <InputError class="mt-2" :message="form.errors.latitude" />
                                        <InputError class="mt-2" :message="form.errors.longitude" />
                                    </div>
                                </div>
                            </div>

                            <!-- Map -->
                            <div class="mt-6">
                                <InputLabel value="Pilih Lokasi di Peta" />
                                <p class="text-sm text-gray-500 mb-2">Klik pada peta untuk menandai lokasi UMKM Anda</p>
                                <div 
                                    ref="mapContainer" 
                                    class="w-full h-64 rounded-lg shadow-inner border"
                                ></div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end mt-6 space-x-4">
                                <Link
                                    :href="route('umkm.index')"
                                    class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    Batal
                                </Link>

                                <PrimaryButton 
                                    class="ms-4" 
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    Simpan UMKM
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@import 'leaflet/dist/leaflet.css';
</style>