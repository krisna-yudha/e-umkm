<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SimpleImageCompressor from '@/Components/SimpleImageCompressor.vue';
import OperatingHours from '@/Components/OperatingHours.vue';
import Notification from '@/Components/Notification.vue';
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
    facebook: '',
    instagram: '',
    twitter: '',
    whatsapp: '',
    website: '',
    operating_hours: {
        monday: { is_open: false, open_time: '09:00', close_time: '17:00' },
        tuesday: { is_open: false, open_time: '09:00', close_time: '17:00' },
        wednesday: { is_open: false, open_time: '09:00', close_time: '17:00' },
        thursday: { is_open: false, open_time: '09:00', close_time: '17:00' },
        friday: { is_open: false, open_time: '09:00', close_time: '17:00' },
        saturday: { is_open: false, open_time: '09:00', close_time: '17:00' },
        sunday: { is_open: false, open_time: '09:00', close_time: '17:00' }
    }
});

const mapContainer = ref<HTMLElement>();
const currentLocationMarker = ref<any>(null);
const searchQuery = ref('');
const searchResults = ref<any[]>([]);
const isSearching = ref(false);
const showSearchResults = ref(false);
const showImageCropper = ref(false);
const showNotification = ref(false);
const notificationType = ref<'success' | 'error' | 'warning'>('success');
const notificationMessage = ref('');
let map: any = null;
let searchTimeout: any = null;

const submit = () => {
    if (form.gambar && form.gambar instanceof File) {
        form.post(route('umkm.store'), {
            forceFormData: true,
            onSuccess: () => {
                showNotification.value = true;
                notificationType.value = 'success';
                notificationMessage.value = 'UMKM berhasil disimpan!';
            },
            onError: () => {
                showNotification.value = true;
                notificationType.value = 'error';
                notificationMessage.value = 'Terjadi kesalahan saat menyimpan UMKM';
            }
        });
    } else {
        form.post(route('umkm.store'), {
            onSuccess: () => {
                showNotification.value = true;
                notificationType.value = 'success';
                notificationMessage.value = 'UMKM berhasil disimpan!';
            },
            onError: () => {
                showNotification.value = true;
                notificationType.value = 'error';
                notificationMessage.value = 'Terjadi kesalahan saat menyimpan UMKM';
            }
        });
    }
};

const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.gambar = file;
        showImageCropper.value = true;
    }
};

const handleCroppedImage = (croppedFile: File) => {
    form.gambar = croppedFile;
    showImageCropper.value = false;
};

const handleCropperCancel = () => {
    showImageCropper.value = false;
    const fileInput = document.getElementById('gambar') as HTMLInputElement;
    if (fileInput) {
        fileInput.value = '';
    }
};

const handleCropperSuccess = (message: string) => {
    showNotification.value = true;
    notificationType.value = 'success';
    notificationMessage.value = message;
};

const handleCropperError = (message: string) => {
    showNotification.value = true;
    notificationType.value = 'error';
    notificationMessage.value = message;
};

const handleOperatingHoursChange = (hours: any) => {
    form.operating_hours = hours;
};

const closeNotification = () => {
    showNotification.value = false;
};

const validatePhoneInput = (event: Event, field: 'no_telepon' | 'whatsapp') => {
    const target = event.target as HTMLInputElement;
    let value = target.value.replace(/\D/g, '');
    
    if (value.length > 13) {
        value = value.slice(0, 13);
    }
    
    target.value = value;
    form[field] = value;
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
                    
                    import('leaflet').then(({ default: L }) => {
                        currentLocationMarker.value = L.marker([lat, lng], { draggable: true }).addTo(map)
                            .bindPopup('Lokasi UMKM Anda')
                            .openPopup();
                        
                        currentLocationMarker.value.on('dragend', function(e: any) {
                            const position = e.target.getLatLng();
                            form.latitude = position.lat.toString();
                            form.longitude = position.lng.toString();
                            
                            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.lat}&lon=${position.lng}&addressdetails=1`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.display_name) {
                                        searchQuery.value = data.display_name;
                                        form.alamat = data.display_name;
                                    }
                                })
                                .catch(error => console.error('Error getting address:', error));
                        });
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
    
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    searchTimeout = setTimeout(() => {
        searchLocation(query);
    }, 300);
};

const selectLocation = async (result: any) => {
    const lat = parseFloat(result.lat);
    const lng = parseFloat(result.lon);
    
    form.latitude = lat.toString();
    form.longitude = lng.toString();
    
    searchQuery.value = result.display_name;
    form.alamat = result.display_name;
    
    showSearchResults.value = false;
    
    if (map) {
        map.setView([lat, lng], 15);
        
        if (currentLocationMarker.value) {
            map.removeLayer(currentLocationMarker.value);
        }
        
        const L = await import('leaflet');
        currentLocationMarker.value = L.default.marker([lat, lng], { draggable: true }).addTo(map)
            .bindPopup(result.display_name)
            .openPopup();
        
        currentLocationMarker.value.on('dragend', function(e: any) {
            const position = e.target.getLatLng();
            form.latitude = position.lat.toString();
            form.longitude = position.lng.toString();
            
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.lat}&lon=${position.lng}&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data.display_name) {
                        searchQuery.value = data.display_name;
                        form.alamat = data.display_name;
                    }
                })
                .catch(error => console.error('Error getting address:', error));
        });
    }
};

const setMarkerOnMap = (lat: number, lng: number, popupText: string) => {
    import('leaflet').then(({ default: L }) => {
        if (currentLocationMarker.value) {
            map.removeLayer(currentLocationMarker.value);
        }
        
        currentLocationMarker.value = L.marker([lat, lng], { draggable: true }).addTo(map)
            .bindPopup(popupText)
            .openPopup();
        
        currentLocationMarker.value.on('dragend', function(e: any) {
            const position = e.target.getLatLng();
            form.latitude = position.lat.toString();
            form.longitude = position.lng.toString();
            
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.lat}&lon=${position.lng}&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data.display_name) {
                        searchQuery.value = data.display_name;
                        form.alamat = data.display_name;
                    }
                })
                .catch(error => console.error('Error getting address:', error));
        });
    });
};

onMounted(async () => {
    const L = await import('leaflet');
    
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

    map = L.default.map(mapContainer.value!).setView([-6.9664, 110.4204], 13);

    L.default.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    map.on('click', async (e: any) => {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        form.latitude = lat.toString();
        form.longitude = lng.toString();
        
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

    document.addEventListener('click', (e) => {
        const target = e.target as HTMLElement;
        const searchContainer = document.getElementById('location-search')?.closest('.relative');
        if (searchContainer && !searchContainer.contains(target)) {
            showSearchResults.value = false;
        }
    });
});
</script>

<template>
    <Head title="Tambah UMKM" />

    <AuthenticatedLayout>
        <template #header>
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg p-6 shadow-lg">
                <div class="flex items-center space-x-4">
                    <div class="bg-white bg-opacity-20 p-3 rounded-full">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">Tambah UMKM Baru</h2>
                        <p class="text-blue-100 mt-1">Daftarkan usaha mikro, kecil, dan menengah Anda</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-8 py-6 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">Formulir Pendaftaran UMKM</h3>
                                <p class="text-gray-600 mt-1">Lengkapi informasi di bawah ini dengan data yang akurat</p>
                            </div>
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <span class="flex items-center space-x-1">
                                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                    <span>Wajib diisi</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-8">
                        <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-8">
                            <!-- Section: Informasi Dasar -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="bg-blue-500 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar UMKM</h3>
                                        <p class="text-sm text-gray-600">Data utama tentang usaha Anda</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Left Column -->
                                    <div class="space-y-6">
                                        <div class="group">
                                            <InputLabel for="nama_umkm" class="flex items-center space-x-2">
                                                <span>Nama UMKM</span>
                                                <span class="text-red-500">*</span>
                                                <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">Wajib</span>
                                            </InputLabel>
                                            <TextInput
                                                id="nama_umkm"
                                                type="text"
                                                class="mt-2 block w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition-all duration-200 group-hover:border-blue-300"
                                                v-model="form.nama_umkm"
                                                required
                                                autofocus
                                                placeholder="Masukkan nama UMKM Anda"
                                            />
                                            <InputError class="mt-2" :message="form.errors.nama_umkm" />
                                        </div>

                                        <div class="group">
                                            <InputLabel for="kategori" class="flex items-center space-x-2">
                                                <span>Kategori Usaha</span>
                                                <span class="text-red-500">*</span>
                                                <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">Wajib</span>
                                            </InputLabel>
                                            <select
                                                id="kategori"
                                                class="mt-2 block w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition-all duration-200 group-hover:border-blue-300"
                                                v-model="form.kategori"
                                                required
                                            >
                                                <option value="">-- Pilih Kategori Usaha --</option>
                                                <option value="Makanan & Minuman">🍽️ Makanan & Minuman</option>
                                                <option value="Fashion & Tekstil">👗 Fashion & Tekstil</option>
                                                <option value="Kerajinan Tangan">🎨 Kerajinan Tangan</option>
                                                <option value="Elektronik">💻 Elektronik</option>
                                                <option value="Jasa">🔧 Jasa</option>
                                                <option value="Pertanian">🌾 Pertanian</option>
                                                <option value="Lainnya">📦 Lainnya</option>
                                            </select>
                                            <InputError class="mt-2" :message="form.errors.kategori" />
                                        </div>

                                        <div class="group">
                                            <InputLabel for="alamat" class="flex items-center space-x-2">
                                                <span>Alamat Lengkap</span>
                                                <span class="text-red-500">*</span>
                                                <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">Wajib</span>
                                            </InputLabel>
                                            <textarea
                                                id="alamat"
                                                class="mt-2 block w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition-all duration-200 group-hover:border-blue-300"
                                                rows="4"
                                                v-model="form.alamat"
                                                required
                                                placeholder="Masukkan alamat lengkap UMKM Anda, termasuk RT/RW, kelurahan, kecamatan, kota"
                                            ></textarea>
                                            <InputError class="mt-2" :message="form.errors.alamat" />
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="space-y-6">
                                        <div class="group">
                                            <InputLabel for="deskripsi" value="Deskripsi Usaha" />
                                            <textarea
                                                id="deskripsi"
                                                class="mt-2 block w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition-all duration-200 group-hover:border-blue-300"
                                                rows="6"
                                                v-model="form.deskripsi"
                                                placeholder="Ceritakan tentang UMKM Anda, produk/jasa yang ditawarkan, keunggulan, dan target pasar..."
                                            ></textarea>
                                            <p class="text-xs text-gray-500 mt-1">Deskripsi yang menarik akan membantu pelanggan mengenal usaha Anda</p>
                                            <InputError class="mt-2" :message="form.errors.deskripsi" />
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div class="group">
                                                <InputLabel for="no_telepon" value="No. Telepon" />
                                                <TextInput
                                                    id="no_telepon"
                                                    type="tel"
                                                    class="mt-2 block w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition-all duration-200 group-hover:border-blue-300"
                                                    v-model="form.no_telepon"
                                                    placeholder="08123456789"
                                                    maxlength="13"
                                                    @input="validatePhoneInput($event, 'no_telepon')"
                                                />
                                                <p class="mt-1 text-xs text-gray-500">Maksimal 13 digit angka</p>
                                                <InputError class="mt-2" :message="form.errors.no_telepon" />
                                            </div>

                                            <div class="group">
                                                <InputLabel for="email" value="Email" />
                                                <TextInput
                                                    id="email"
                                                    type="email"
                                                    class="mt-2 block w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition-all duration-200 group-hover:border-blue-300"
                                                    v-model="form.email"
                                                    placeholder="email@domain.com"
                                                />
                                                <InputError class="mt-2" :message="form.errors.email" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Media & Gambar -->
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-200">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="bg-purple-500 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Foto & Media Sosial</h3>
                                        <p class="text-sm text-gray-600">Upload foto dan hubungkan media sosial Anda</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Gambar UMKM -->
                                    <div class="group">
                                        <InputLabel for="gambar" class="flex items-center space-x-2">
                                            <span>Foto UMKM</span>
                                            <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">Opsional</span>
                                        </InputLabel>
                                        <div class="mt-2 border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-400 transition-colors duration-200 group-hover:bg-purple-25">
                                            <input
                                                id="gambar"
                                                type="file"
                                                class="hidden"
                                                accept="image/*"
                                                @change="handleImageChange"
                                            />
                                            <label for="gambar" class="cursor-pointer block">
                                                <div class="flex flex-col items-center space-y-3">
                                                    <div class="bg-purple-100 p-3 rounded-full">
                                                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">Klik untuk upload foto</p>
                                                        <p class="text-xs text-gray-500">PNG, JPG, GIF hingga 2MB</p>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        
                                        <div class="mt-3 space-y-2">
                                            <div class="flex items-center space-x-2 text-xs text-purple-600 bg-purple-50 p-2 rounded-lg">
                                                <span>✂️</span>
                                                <span>Fitur otomatis: Kompres & resize gambar</span>
                                            </div>
                                            <div class="flex items-center space-x-2 text-xs text-purple-600 bg-purple-50 p-2 rounded-lg">
                                                <span>📏</span>
                                                <span>Kualitas gambar akan dioptimalkan</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Preview selected image -->
                                        <div v-if="form.gambar" class="mt-4 p-4 bg-green-50 border-2 border-green-200 rounded-xl">
                                            <div class="flex items-center space-x-3">
                                                <div class="bg-green-100 p-2 rounded-lg">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-green-800 truncate">{{ form.gambar.name }}</p>
                                                    <p class="text-xs text-green-600">Size: {{ (form.gambar.size / (1024 * 1024)).toFixed(2) }} MB</p>
                                                </div>
                                                <button
                                                    type="button"
                                                    @click="showImageCropper = true"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-purple-700 bg-purple-100 hover:bg-purple-200 transition-colors duration-200"
                                                >
                                                    📏 Kompres
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <InputError class="mt-2" :message="form.errors.gambar" />
                                    </div>

                                    <!-- Social Media Section -->
                                    <div class="space-y-4">
                                        <div>
                                            <h4 class="text-md font-medium text-gray-900 mb-3 flex items-center space-x-2">
                                                <span>📱</span>
                                                <span>Media Sosial & Kontak</span>
                                            </h4>
                                        </div>
                                        
                                        <div class="space-y-4">
                                            <div class="group">
                                                <InputLabel for="whatsapp" value="WhatsApp" />
                                                <div class="mt-2 relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-green-500 text-lg">📱</span>
                                                    </div>
                                                    <TextInput
                                                        id="whatsapp"
                                                        type="tel"
                                                        class="pl-12 block w-full border-2 border-gray-200 focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-all duration-200 group-hover:border-green-300"
                                                        v-model="form.whatsapp"
                                                        placeholder="628123456789"
                                                        maxlength="13"
                                                        @input="validatePhoneInput($event, 'whatsapp')"
                                                    />
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Format: 628123456789 (tanpa +), maksimal 13 digit</p>
                                                <InputError class="mt-2" :message="form.errors.whatsapp" />
                                            </div>

                                            <div class="group">
                                                <InputLabel for="instagram" value="Instagram" />
                                                <div class="mt-2 relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-pink-500 text-lg">📷</span>
                                                    </div>
                                                    <TextInput
                                                        id="instagram"
                                                        type="text"
                                                        class="pl-12 block w-full border-2 border-gray-200 focus:border-pink-500 focus:ring-pink-500 rounded-xl shadow-sm transition-all duration-200 group-hover:border-pink-300"
                                                        v-model="form.instagram"
                                                        placeholder="username_anda"
                                                    />
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Username Instagram tanpa @</p>
                                                <InputError class="mt-2" :message="form.errors.instagram" />
                                            </div>

                                            <div class="group">
                                                <InputLabel for="facebook" value="Facebook" />
                                                <div class="mt-2 relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-blue-600 text-lg">👥</span>
                                                    </div>
                                                    <TextInput
                                                        id="facebook"
                                                        type="text"
                                                        class="pl-12 block w-full border-2 border-gray-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition-all duration-200 group-hover:border-blue-300"
                                                        v-model="form.facebook"
                                                        placeholder="namahalaman"
                                                    />
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Username atau ID halaman Facebook</p>
                                                <InputError class="mt-2" :message="form.errors.facebook" />
                                            </div>

                                            <div class="group">
                                                <InputLabel for="website" value="Website" />
                                                <div class="mt-2 relative">
                                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                        <span class="text-indigo-600 text-lg">🌐</span>
                                                    </div>
                                                    <TextInput
                                                        id="website"
                                                        type="url"
                                                        class="pl-12 block w-full border-2 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all duration-200 group-hover:border-indigo-300"
                                                        v-model="form.website"
                                                        placeholder="https://website-anda.com"
                                                    />
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">URL lengkap website UMKM Anda</p>
                                                <InputError class="mt-2" :message="form.errors.website" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Jam Operasional -->
                            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-xl p-6 border border-orange-200">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="bg-orange-500 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Jam Operasional</h3>
                                        <p class="text-sm text-gray-600">Atur jadwal buka tutup UMKM Anda</p>
                                    </div>
                                </div>
                                <OperatingHours 
                                    :modelValue="form.operating_hours"
                                    @update:modelValue="handleOperatingHoursChange"
                                />
                                <InputError class="mt-2" :message="form.errors.operating_hours" />
                            </div>

                            <!-- Section: Lokasi & Peta -->
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="bg-green-500 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Lokasi UMKM</h3>
                                        <p class="text-sm text-gray-600">Tentukan lokasi UMKM di peta</p>
                                    </div>
                                </div>

                                <div class="space-y-6">
                                    <!-- Location Search -->
                                    <div class="group">
                                        <InputLabel value="Cari Lokasi" />
                                        <div class="mt-2 relative">
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                </div>
                                                <TextInput
                                                    id="location-search"
                                                    type="text"
                                                    class="pl-10 block w-full border-2 border-gray-200 focus:border-green-500 focus:ring-green-500 rounded-xl shadow-sm transition-all duration-200 group-hover:border-green-300"
                                                    v-model="searchQuery"
                                                    @input="onSearchInput"
                                                    placeholder="Cari alamat, jalan, atau tempat (contoh: Jl. Pandanaran Semarang)"
                                                />
                                                <div v-if="isSearching" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                                    <svg class="animate-spin h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            
                                            <!-- Search Results Dropdown -->
                                            <div v-if="showSearchResults && searchResults.length > 0" 
                                                 class="absolute z-10 mt-1 w-full bg-white shadow-xl rounded-xl border border-gray-200 max-h-60 overflow-auto">
                                                <div 
                                                    v-for="result in searchResults" 
                                                    :key="result.place_id"
                                                    @click="selectLocation(result)"
                                                    class="px-4 py-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors duration-150"
                                                >
                                                    <div class="flex items-start space-x-3">
                                                        <div class="bg-green-100 p-1 rounded-full mt-1">
                                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900 truncate">{{ result.display_name }}</p>
                                                            <p class="text-xs text-gray-500">{{ result.type || 'Lokasi' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-3 flex space-x-3">
                                            <button
                                                type="button"
                                                @click="getCurrentLocation"
                                                class="inline-flex items-center px-4 py-2 border-2 border-green-300 shadow-sm text-sm leading-4 font-medium rounded-xl text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200"
                                            >
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-10 0a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2"></path>
                                                </svg>
                                                📍 Gunakan Lokasi Saat Ini
                                            </button>
                                        </div>
                                        
                                        <!-- Display selected coordinates -->
                                        <div v-if="form.latitude && form.longitude" class="mt-4 p-4 bg-green-50 border-2 border-green-200 rounded-xl">
                                            <div class="flex items-center space-x-3">
                                                <div class="bg-green-100 p-2 rounded-lg">
                                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-green-800">Koordinat Terpilih</p>
                                                    <p class="text-xs text-green-600">
                                                        Latitude: {{ form.latitude }}, Longitude: {{ form.longitude }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <InputError class="mt-2" :message="form.errors.latitude" />
                                        <InputError class="mt-2" :message="form.errors.longitude" />
                                    </div>

                                    <!-- Map -->
                                    <div>
                                        <InputLabel value="Peta Lokasi" />
                                        <p class="text-sm text-gray-500 mb-3">Klik pada peta untuk menandai lokasi UMKM Anda</p>
                                        <div 
                                            ref="mapContainer" 
                                            class="w-full h-72 rounded-xl shadow-inner border-2 border-gray-200 overflow-hidden"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Section -->
                            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                                <Link
                                    :href="route('umkm.index')"
                                    class="inline-flex items-center px-6 py-3 bg-gray-200 border border-gray-300 rounded-xl font-semibold text-sm text-gray-700 uppercase tracking-wider hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 shadow-sm"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Batal
                                </Link>

                                <PrimaryButton 
                                    class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider hover:from-blue-700 hover:to-purple-700 focus:from-blue-700 focus:to-purple-700 active:from-blue-800 active:to-purple-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5" 
                                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <svg v-if="!form.processing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <svg v-else class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span v-if="form.processing">Menyimpan...</span>
                                    <span v-else">💾 Simpan UMKM</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Compressor Modal -->
        <SimpleImageCompressor
            :show="showImageCropper"
            :imageFile="form.gambar"
            @cropped="handleCroppedImage"
            @close="handleCropperCancel"
            @success="handleCropperSuccess"
            @error="handleCropperError"
        />

        <!-- Notification -->
        <Notification
            :show="showNotification"
            :type="notificationType"
            :message="notificationMessage"
            @close="closeNotification"
        />
    </AuthenticatedLayout>
</template>

<style>
@import 'leaflet/dist/leaflet.css';
</style>