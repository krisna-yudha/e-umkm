<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref, computed } from 'vue';

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
        user: {
            name: string;
        };
    }>;
    auth?: {
        user?: {
            id: number;
            name: string;
            email: string;
            role: string;
        };
    };
}>();

// Dynamic home route based on user role
const homeRoute = computed(() => {
    if (props.auth?.user) {
        if (props.auth.user.role === 'admin') {
            return route('admin.dashboard');
        } else {
            return route('dashboard');
        }
    }
    return route('home');
});

const mapContainer = ref<HTMLElement>();
const searchQuery = ref('');
const searchResults = ref<any[]>([]);
const isSearching = ref(false);
const showSearchResults = ref(false);
let map: any = null;
let searchTimeout: any = null;
let userLocationMarker: any = null;

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
    
    // Update search query with selected location
    searchQuery.value = result.display_name;
    
    // Hide search results
    showSearchResults.value = false;
    
    // Move map to selected location with zoom
    if (map) {
        map.setView([lat, lng], 17);
    }
};

const getCurrentLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                if (map) {
                    // Zoom level yang lebih friendly - tidak terlalu dekat
                    map.setView([lat, lng], 16);
                    
                    // Remove existing user location marker
                    if (userLocationMarker) {
                        map.removeLayer(userLocationMarker);
                    }
                    
                    // Get address using reverse geocoding
                    let addressInfo = 'Mengambil informasi alamat...';
                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`
                        );
                        const data = await response.json();
                        
                        if (data && data.display_name) {
                            // Extract meaningful address parts
                            const address = data.address || {};
                            const parts = [];
                            
                            if (address.road) parts.push(address.road);
                            if (address.suburb || address.neighbourhood) parts.push(address.suburb || address.neighbourhood);
                            if (address.city || address.town || address.village) parts.push(address.city || address.town || address.village);
                            
                            addressInfo = parts.length > 0 ? parts.join(', ') : data.display_name.split(',').slice(0, 3).join(', ');
                        }
                    } catch (error) {
                        console.error('Error getting address:', error);
                        addressInfo = 'Alamat tidak dapat diambil';
                    }
                    
                    // Import Leaflet for marker creation
                    const L = await import('leaflet');
                    
                    // Create enhanced custom user location icon
                    const userIcon = L.default.divIcon({
                        className: 'custom-user-marker',
                        html: `
                            <div class="relative">
                                <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-600 rounded-full flex items-center justify-center shadow-2xl border-4 border-white animate-pulse-green">
                                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 4V6C15 7.66 13.66 9 12 9S9 7.66 9 6V4L3 7V9H21M12 10C14.67 10 17 11.33 17 13V16H7V13C7 11.33 9.33 10 12 10Z"/>
                                    </svg>
                                </div>
                                <div class="absolute -bottom-3 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-8 border-transparent border-t-emerald-600"></div>
                                <!-- Ripple effect -->
                                <div class="absolute inset-0 w-14 h-14 rounded-full border-4 border-green-400 animate-ping opacity-30"></div>
                            </div>
                        `,
                        iconSize: [56, 64],
                        iconAnchor: [28, 64],
                        popupAnchor: [0, -64]
                    });
                    
                    // Add user location marker
                    userLocationMarker = L.default.marker([lat, lng], { icon: userIcon }).addTo(map);
                    
                    // Enhanced popup with address and coordinates - Mini & Simple Design
                    const popupContent = `
                        <div class="bg-white rounded-lg overflow-hidden shadow-md border border-gray-200 max-w-60">
                            <!-- Compact Header -->
                            <div class="bg-emerald-500 px-2.5 py-2">
                                <div class="flex items-center">
                                    <div class="w-5 h-5 bg-white bg-opacity-25 rounded-full flex items-center justify-center mr-2">
                                        <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-white font-medium text-xs">📍 Lokasi Anda</h3>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Mini Content -->
                            <div class="p-2.5 space-y-2.5">
                                <!-- Address - Simplified -->
                                <div>
                                    <p class="text-gray-800 text-xs leading-snug">${addressInfo}</p>
                                </div>
                                
                                <!-- Coordinates - Minimal -->
                                <div class="bg-gray-50 rounded p-1.5">
                                    <div class="text-xs text-gray-600 space-y-0.5">
                                        <div class="flex justify-between">
                                            <span>Lat:</span>
                                            <span class="font-mono font-medium">${lat.toFixed(4)}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Lng:</span>
                                            <span class="font-mono font-medium">${lng.toFixed(4)}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Accuracy - Minimal -->
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500">Akurasi</span>
                                    <span class="text-emerald-600 font-medium">
                                        ${position.coords.accuracy ? Math.round(position.coords.accuracy) + 'm' : 'OK'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Add popup with enhanced styling
                    userLocationMarker.bindPopup(popupContent, {
                        maxWidth: 240,
                        minWidth: 200,
                        className: 'custom-user-popup'
                    }).openPopup();
                    
                    // Enhanced tooltip
                    userLocationMarker.bindTooltip('🎯 Lokasi Anda', {
                        permanent: false,
                        direction: 'top',
                        className: 'custom-user-tooltip'
                    });
                }
            },
            (error) => {
                console.error('Error getting location:', error);
                let errorMessage = 'Tidak dapat mengambil lokasi. ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Izin lokasi ditolak. Mohon berikan izin akses lokasi.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Informasi lokasi tidak tersedia.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Waktu permintaan lokasi habis.';
                        break;
                    default:
                        errorMessage += 'Terjadi kesalahan yang tidak diketahui.';
                        break;
                }
                alert(errorMessage);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 300000
            }
        );
    } else {
        alert('Geolocation tidak didukung oleh browser ini.');
    }
};

onMounted(async () => {
    try {
        // Import Leaflet dynamically
        const L = await import('leaflet');
        
        // Fix for default markers
        const iconRetinaUrl = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png';
        const iconUrl = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png';
        const shadowUrl = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png';
        
        const DefaultIcon = L.icon({
            iconRetinaUrl,
            iconUrl,
            shadowUrl,
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        
        L.Marker.prototype.options.icon = DefaultIcon;

        // Initialize map with comprehensive options for dragging
        // Set default view to Semarang, Central Java, Indonesia
        map = L.map(mapContainer.value!, {
            center: [-6.9664, 110.4204], // Semarang coordinates
            zoom: 15, // Closer zoom level for better detail
            zoomControl: true,
            dragging: true,
            touchZoom: true,
            doubleClickZoom: true,
            scrollWheelZoom: true,
            boxZoom: true,
            keyboard: true,
            attributionControl: true,
            closePopupOnClick: true,
            trackResize: true,
            inertia: true,
            inertiaDeceleration: 3000,
            inertiaMaxSpeed: 1500,
            worldCopyJump: false,
            maxBounds: undefined,
            minZoom: 2,
            maxZoom: 25
        });

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 25,
            minZoom: 3,
            tileSize: 256,
            zoomOffset: 0,
            detectRetina: true
        }).addTo(map);

        // Multiple attempts to ensure proper initialization
        setTimeout(() => {
            map.invalidateSize(true);
        }, 100);
        
        setTimeout(() => {
            map.invalidateSize(true);
            // Enable dragging explicitly
            if (map.dragging) {
                map.dragging.enable();
            }
        }, 500);
        
        // Re-enable dragging after a longer delay
        setTimeout(() => {
            map.invalidateSize(true);
            if (map.dragging) {
                map.dragging.enable();
            }
            // Map dragging enabled check
        }, 1000);

        // Function to create custom UMKM icon with profile image
        const createUmkmIcon = (umkm: any) => {
            const hasImage = umkm.gambar && umkm.gambar.trim() !== '';
            
            return L.divIcon({
                className: 'custom-umkm-marker',
                html: `
                    <div class="relative group transform transition-transform duration-200 hover:scale-110">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-xl border-3 border-white overflow-hidden ring-2 ring-blue-200 hover:ring-4 hover:ring-blue-300 transition-all duration-200">
                            ${hasImage ? 
                                `<img src="/storage/${umkm.gambar}" alt="${umkm.nama_umkm}" class="w-full h-full object-cover rounded-full transition-transform duration-200 group-hover:scale-110" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"/>` :
                                ''
                            }
                            <div class="${hasImage ? 'hidden' : 'flex'} w-full h-full items-center justify-center">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        </div>
                        <!-- Animated pointer -->
                        <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-8 border-transparent border-t-purple-600 drop-shadow-lg"></div>
                        
                        <!-- UMKM Name Label with improved styling -->
                        <div class="absolute -bottom-10 left-1/2 transform -translate-x-1/2 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-200 ease-in-out">
                            <div class="bg-white text-gray-800 text-xs font-semibold px-3 py-2 rounded-lg shadow-lg border border-gray-200 max-w-40 truncate relative">
                                ${umkm.nama_umkm}
                                <!-- Small arrow pointing up -->
                                <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-2 border-r-2 border-b-2 border-transparent border-b-white"></div>
                            </div>
                        </div>
                        
                        <!-- Pulse animation for active UMKM -->
                        <div class="absolute inset-0 w-14 h-14 rounded-full border-2 border-blue-400 animate-ping opacity-20"></div>
                    </div>
                `,
                iconSize: [56, 64],
                iconAnchor: [28, 64],
                popupAnchor: [0, -64]
            });
        };

        // Add markers for each UMKM
        props.umkms.forEach(umkm => {
            if (umkm.latitude && umkm.longitude) {
                const customIcon = createUmkmIcon(umkm);
                const marker = L.marker([umkm.latitude, umkm.longitude], { icon: customIcon }).addTo(map);
                
                // Add click event to marker for navigation
                marker.on('click', () => {
                    // Optional: You can add direct navigation here
                    // window.location.href = `/umkm/${umkm.id}`;
                });
                
                const popupContent = `
                    <div class="max-w-sm bg-white rounded-lg overflow-hidden shadow-lg">
                        <!-- Header dengan gambar atau icon -->
                        <div class="relative">
                            ${umkm.gambar ? 
                                `<img src="/storage/${umkm.gambar}" alt="${umkm.nama_umkm}" class="w-full h-32 object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />` :
                                ''
                            }
                            <div class="${umkm.gambar ? 'hidden' : 'block'} w-full h-32 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <!-- Kategori badge -->
                            <div class="absolute top-3 left-3">
                                <span class="bg-white text-blue-700 text-xs px-3 py-1.5 rounded-full font-bold shadow-lg border-2 border-blue-100">${umkm.kategori}</span>
                            </div>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-4">
                            <!-- UMKM Name & Owner -->
                            <div class="mb-3">
                                <h3 class="font-bold text-lg text-gray-900 mb-1 leading-tight">${umkm.nama_umkm}</h3>
                                <p class="text-gray-600 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    ${umkm.user.name}
                                </p>
                            </div>
                            
                            <!-- Description -->
                            ${umkm.deskripsi ? `<p class="text-gray-700 text-sm mb-3 leading-relaxed line-clamp-2">${umkm.deskripsi}</p>` : ''}
                            
                            <!-- Contact Info -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 text-gray-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="text-gray-700 text-sm leading-relaxed">${umkm.alamat}</span>
                                </div>
                                ${umkm.no_telepon ? `
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <a href="tel:${umkm.no_telepon}" class="text-green-600 hover:text-green-800 font-medium text-sm">${umkm.no_telepon}</a>
                                </div>
                                ` : ''}
                            </div>
                            
                            <!-- Action Button -->
                            <div class="pt-3 border-t border-gray-200">
                                <a href="/umkm-public/${umkm.id}" class="w-full inline-flex items-center justify-center px-4 py-3 bg-white text-blue-600 text-sm font-semibold rounded-lg border-2 border-blue-600 hover:bg-blue-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent, {
                    maxWidth: 320,
                    minWidth: 280,
                    className: 'custom-popup-enhanced'
                });
                
                // Add tooltip with UMKM name
                marker.bindTooltip(umkm.nama_umkm, {
                    permanent: false,
                    direction: 'top',
                    className: 'custom-tooltip'
                });
            }
        });

        // Set map view based on UMKM locations or default to Semarang
        if (props.umkms.length > 0) {
            const validUmkms = props.umkms.filter(umkm => umkm.latitude && umkm.longitude);
            
            if (validUmkms.length > 0) {
                // Check if UMKM are in Semarang area (rough bounds)
                const semarangUmkms = validUmkms.filter(umkm => 
                    umkm.latitude >= -7.1 && umkm.latitude <= -6.8 && 
                    umkm.longitude >= 110.2 && umkm.longitude <= 110.6
                );
                
                if (semarangUmkms.length > 0) {
                    // If there are UMKM in Semarang area, focus on them
                    if (semarangUmkms.length > 1) {
                        const group = new L.FeatureGroup(
                            semarangUmkms.map(umkm => L.marker([umkm.latitude, umkm.longitude]))
                        );
                        map.fitBounds(group.getBounds().pad(0.15));
                    } else {
                        // Single UMKM in Semarang - zoom level yang friendly
                        const umkm = semarangUmkms[0];
                        map.setView([umkm.latitude, umkm.longitude], 15);
                    }
                } else if (validUmkms.length > 1) {
                    // UMKM outside Semarang, show all
                    const group = new L.FeatureGroup(
                        validUmkms.map(umkm => L.marker([umkm.latitude, umkm.longitude]))
                    );
                    map.fitBounds(group.getBounds().pad(0.1));
                } else {
                    // Single UMKM outside Semarang - zoom level yang friendly
                    const umkm = validUmkms[0];
                    map.setView([umkm.latitude, umkm.longitude], 15);
                }
            } else {
                // No valid UMKM coordinates, stay focused on Semarang
                map.setView([-6.9664, 110.4204], 15);
            }
        } else {
            // No UMKM data, stay focused on Semarang
            map.setView([-6.9664, 110.4204], 15);
        }
        
        // Add event listeners to debug dragging
        map.on('dragstart', () => {
            // Map drag events
        });
        
        // Final check
        // Map initialized successfully
        
        // Close search results when clicking outside
        document.addEventListener('click', (e) => {
            const target = e.target as HTMLElement;
            const searchContainer = document.getElementById('location-search-mapping')?.closest('.relative');
            if (searchContainer && !searchContainer.contains(target)) {
                showSearchResults.value = false;
            }
        });
        
    } catch (error) {
        console.error('Error initializing map:', error);
    }
});
</script>

<template>
    <Head title="Peta UMKM" />

    <GuestLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Header Section -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">🗺️ Peta UMKM</h1>
                                <p class="text-blue-100">Temukan lokasi UMKM terdekat di sekitar Anda</p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <Link 
                                    :href="homeRoute"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-white/20 border border-white/30 rounded-xl font-semibold text-sm text-white tracking-wide hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-200"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    {{ auth?.user ? (auth.user.role === 'admin' ? 'Kembali ke Admin' : 'Kembali ke Dashboard') : 'Kembali ke Beranda' }}
                                </Link>
                                <Link 
                                    :href="route('public.umkm.list')"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-white text-blue-600 rounded-xl font-semibold text-sm tracking-wide hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 shadow-lg"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    Lihat Daftar
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Container -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div v-if="umkms.length === 0" class="text-center py-16">
                        <div class="mb-4">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada UMKM</h3>
                        <p class="text-gray-500 text-lg">Belum ada UMKM dengan lokasi yang terdaftar di peta.</p>
                    </div>

                    <div v-else>
                        <!-- Compact Search Bar -->
                        <div class="bg-white border-b border-gray-200 px-4 py-3">
                            <div class="flex items-center justify-between max-w-6xl mx-auto">
                                <!-- Search Input -->
                                <div class="flex-1 max-w-md relative">
                                    <div class="relative">
                                        <input
                                            id="location-search-mapping"
                                            type="text"
                                            class="block w-full pl-10 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white placeholder-gray-400"
                                            v-model="searchQuery"
                                            @input="onSearchInput"
                                            @focus="showSearchResults = searchResults.length > 0"
                                            placeholder="Cari lokasi..."
                                        />
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </div>
                                        <div v-if="isSearching" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg class="animate-spin h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Compact Search Results -->
                                    <div v-if="showSearchResults && searchResults.length > 0" 
                                         class="absolute z-20 mt-1 w-full bg-white shadow-lg max-h-60 rounded-lg py-1 text-sm overflow-auto border border-gray-200">
                                        <div v-for="result in searchResults" 
                                             :key="result.place_id"
                                             @click="selectLocation(result)"
                                             class="cursor-pointer select-none relative py-2 px-3 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-50 last:border-b-0">
                                            <div class="flex items-center space-x-2">
                                                <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                </svg>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        {{ result.display_name.split(',')[0] }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 truncate">
                                                        {{ result.display_name.split(',').slice(1, 3).join(',') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-3 ml-4">
                                    <button
                                        type="button"
                                        @click="getCurrentLocation"
                                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                                        title="Gunakan lokasi saat ini"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        <span class="ml-1 hidden sm:inline">Lokasi Saya</span>
                                    </button>
                                    
                                    <!-- UMKM Counter Badge -->
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-3 py-2 rounded-lg text-xs font-semibold shadow-sm">
                                        <span class="hidden sm:inline">📍 </span>{{ umkms.length }} UMKM
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Instructions -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-3">
                            <p class="text-sm text-blue-700 flex items-center">
                                <svg class="h-4 w-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Klik marker untuk detail UMKM atau gunakan pencarian untuk navigasi cepat.
                            </p>
                        </div>
                        
                        <!-- Map -->
                        <div class="relative map-wrapper">
                            <div 
                                ref="mapContainer" 
                                class="w-full h-[500px] md:h-[600px] map-container-custom"
                                style="position: relative; z-index: 1; cursor: grab;"
                            ></div>
                            
                            <!-- Map Loading Indicator -->
                            <div class="absolute inset-0 bg-gray-100 flex items-center justify-center" v-if="!map">
                                <div class="text-center">
                                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                                    <p class="text-gray-600">Memuat peta...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style>
@import 'leaflet/dist/leaflet.css';

/* Critical: Ensure map container is fully interactive */
.leaflet-container {
    cursor: grab !important;
    touch-action: none !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
    pointer-events: auto !important;
    position: relative !important;
    z-index: 1 !important;
}

.leaflet-container:active {
    cursor: grabbing !important;
}

/* Fix for dragging states */
.leaflet-dragging .leaflet-container {
    cursor: grabbing !important;
}

.leaflet-container .leaflet-map-pane {
    pointer-events: auto !important;
}

/* Ensure all layers are interactive */
.leaflet-pane {
    pointer-events: auto !important;
}

.leaflet-tile-pane {
    pointer-events: none !important;
}

.leaflet-objects-pane {
    pointer-events: auto !important;
}

/* Controls must be accessible */
.leaflet-control-container {
    pointer-events: auto !important;
    z-index: 1000 !important;
}

.leaflet-control {
    pointer-events: auto !important;
    z-index: 1001 !important;
}

/* Prevent any parent container from blocking interactions */
.leaflet-container * {
    pointer-events: auto !important;
}

/* Override any conflicting styles */
.bg-white .leaflet-container,
.rounded-2xl .leaflet-container,
.shadow-xl .leaflet-container {
    pointer-events: auto !important;
    touch-action: none !important;
}

/* Custom map wrapper */
.map-wrapper {
    position: relative !important;
    z-index: 1 !important;
    isolation: isolate !important;
}

.map-container-custom {
    position: relative !important;
    z-index: 1 !important;
    cursor: grab !important;
    touch-action: none !important;
    -webkit-user-select: none !important;
    user-select: none !important;
}

.map-container-custom:active {
    cursor: grabbing !important;
}

/* Force override any inherited styles */
.map-container-custom * {
    pointer-events: auto !important;
}

/* Ensure no overlay is blocking interactions */
.map-wrapper::before,
.map-wrapper::after {
    display: none !important;
}

/* Remove any potential blocking elements */
.relative .leaflet-container {
    position: relative !important;
    z-index: 1 !important;
}

/* Debug styles - remove after testing */
.leaflet-container[data-debug="true"] {
    border: 2px solid red !important;
    background-color: rgba(255, 0, 0, 0.1) !important;
}

.leaflet-control {
    pointer-events: auto !important;
}

/* Custom marker styling */
.custom-umkm-marker {
    background: transparent !important;
    border: none !important;
    pointer-events: auto !important;
}

/* User location marker styling */
.custom-user-marker {
    background: transparent !important;
    border: none !important;
    pointer-events: auto !important;
}

/* User location popup styling - Mini & Simple */
.custom-user-popup .leaflet-popup-content-wrapper {
    border-radius: 6px;
    padding: 0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    pointer-events: auto !important;
    background: white;
    border: 1px solid rgba(0, 0, 0, 0.08);
}

.custom-user-popup .leaflet-popup-content {
    margin: 0;
    padding: 0;
    pointer-events: auto !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 11px;
    line-height: 1.3;
}

.custom-user-popup .leaflet-popup-tip {
    background: #10b981;
    border: none;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

.custom-user-popup .leaflet-popup-close-button {
    top: 6px;
    right: 6px;
    width: 16px;
    height: 16px;
    font-size: 12px;
    color: #6b7280;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 50%;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    line-height: 1;
}

.custom-user-popup .leaflet-popup-close-button:hover {
    background: white;
    color: #374151;
    transform: scale(1.1);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

/* User location tooltip styling */
.custom-user-tooltip {
    background: rgba(16, 185, 129, 0.95) !important;
    border: none !important;
    border-radius: 8px !important;
    color: white !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    padding: 4px 8px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    pointer-events: none !important;
}

.custom-user-tooltip::before {
    border-top-color: rgba(16, 185, 129, 0.95) !important;
}

/* Enhanced UMKM marker styling with hover effects */
.custom-umkm-marker .group {
    cursor: pointer;
    transition: all 0.3s ease;
}

.custom-umkm-marker .group:hover {
    z-index: 1000;
}

.custom-umkm-marker img {
    transition: transform 0.3s ease-in-out;
}

.custom-umkm-marker:hover img {
    transform: scale(1.1);
}

/* UMKM name label styling with smooth animations */
.custom-umkm-marker .group:hover > div:nth-child(3) {
    opacity: 1;
    transform: translateX(-50%) translateY(0) scale(1);
    transition: all 0.3s ease-in-out;
}

/* Pulse animation for markers */
@keyframes marker-pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.2;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.1;
    }
}

.custom-umkm-marker .animate-ping {
    animation: marker-pulse 2s infinite;
}

/* Animation for user location marker */
@keyframes pulse-green {
    0%, 100% {
        transform: scale(1);
        opacity: 0.3;
    }
    50% {
        transform: scale(1.2);
        opacity: 0.1;
    }
}

/* Popup styling */
.custom-popup-enhanced .leaflet-popup-content-wrapper {
    border-radius: 12px;
    padding: 0;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    pointer-events: auto !important;
    background: white;
    overflow: hidden;
}

.custom-popup-enhanced .leaflet-popup-content {
    margin: 0;
    padding: 0;
    pointer-events: auto !important;
    max-height: 400px;
    overflow-y: auto;
}

.custom-popup-enhanced .leaflet-popup-tip {
    background: white;
    border: none;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.custom-popup-enhanced .leaflet-popup-close-button {
    pointer-events: auto !important;
    top: 8px;
    right: 8px;
    width: 24px;
    height: 24px;
    font-size: 16px;
    color: #6b7280;
    background: white;
    border-radius: 50%;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.custom-popup-enhanced .leaflet-popup-close-button:hover {
    background: #f3f4f6;
    color: #374151;
    transform: scale(1.1);
}

/* Line clamp utility */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.custom-popup .leaflet-popup-content-wrapper {
    border-radius: 12px;
    padding: 0;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    pointer-events: auto !important;
}

.custom-popup .leaflet-popup-content {
    margin: 0;
    padding: 0;
    pointer-events: auto !important;
}

.custom-popup .leaflet-popup-tip {
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    border: none;
}

.custom-popup .leaflet-popup-close-button {
    pointer-events: auto !important;
}

/* Tooltip styling */
.custom-tooltip {
    background: rgba(59, 130, 246, 0.95) !important;
    border: none !important;
    border-radius: 8px !important;
    color: white !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    padding: 4px 8px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    pointer-events: none !important;
}

.custom-tooltip::before {
    border-top-color: rgba(59, 130, 246, 0.95) !important;
}

/* Fix for map tiles loading */
.leaflet-tile-pane {
    pointer-events: none !important;
}

.leaflet-tile {
    pointer-events: none !important;
}

/* Semarang marker styling */
.custom-semarang-marker {
    background: transparent !important;
    border: none !important;
    pointer-events: auto !important;
}

.custom-tooltip-semarang {
    background: rgba(239, 68, 68, 0.95) !important;
    border: none !important;
    border-radius: 8px !important;
    color: white !important;
    font-weight: 600 !important;
    font-size: 12px !important;
    padding: 4px 8px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    pointer-events: none !important;
}

.custom-tooltip-semarang::before {
    border-top-color: rgba(239, 68, 68, 0.95) !important;
}

/* Fix for mobile touch events */
@media (max-width: 768px) {
    .leaflet-container {
        touch-action: pan-x pan-y !important;
    }
}
</style>