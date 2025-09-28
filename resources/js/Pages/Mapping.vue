<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

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
}>();

const mapContainer = ref<HTMLElement>();
let map: any = null;

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
        map = L.map(mapContainer.value!, {
            center: [-6.2088, 106.8456],
            zoom: 11,
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
            maxZoom: 20
        });

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 20,
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
            console.log('Map dragging enabled:', map.dragging.enabled());
        }, 1000);

        // Create custom icon for UMKM
        const umkmIcon = L.divIcon({
            className: 'custom-umkm-marker',
            html: `
                <div class="relative">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg border-2 border-white">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-2 border-r-2 border-t-4 border-transparent border-t-purple-600"></div>
                </div>
            `,
            iconSize: [32, 40],
            iconAnchor: [16, 40],
            popupAnchor: [0, -40]
        });

        // Add markers for each UMKM
        props.umkms.forEach(umkm => {
            if (umkm.latitude && umkm.longitude) {
                const marker = L.marker([umkm.latitude, umkm.longitude], { icon: umkmIcon }).addTo(map);
                
                const popupContent = `
                    <div class="max-w-sm">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-3 rounded-t-lg">
                            <h3 class="font-bold text-lg mb-1">${umkm.nama_umkm}</h3>
                            <p class="text-blue-100 text-sm">👤 ${umkm.user.name}</p>
                        </div>
                        <div class="bg-white p-4 rounded-b-lg shadow-lg">
                            <div class="flex items-center mb-2">
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">${umkm.kategori}</span>
                            </div>
                            ${umkm.deskripsi ? `<p class="text-gray-700 text-sm mb-3 leading-relaxed">${umkm.deskripsi}</p>` : ''}
                            <div class="space-y-2">
                                <div class="flex items-start">
                                    <svg class="w-4 h-4 text-gray-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-sm text-gray-600">${umkm.alamat}</p>
                                </div>
                                ${umkm.no_telepon ? `
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <a href="tel:${umkm.no_telepon}" class="text-sm text-blue-600 hover:text-blue-800">${umkm.no_telepon}</a>
                                </div>
                                ` : ''}
                                ${umkm.email ? `
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <a href="mailto:${umkm.email}" class="text-sm text-blue-600 hover:text-blue-800">${umkm.email}</a>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
                
                marker.bindPopup(popupContent, {
                    maxWidth: 300,
                    className: 'custom-popup'
                });
                
                // Add tooltip with UMKM name
                marker.bindTooltip(umkm.nama_umkm, {
                    permanent: false,
                    direction: 'top',
                    className: 'custom-tooltip'
                });
            }
        });

        // Fit map to show all markers
        if (props.umkms.length > 0) {
            const validUmkms = props.umkms.filter(umkm => umkm.latitude && umkm.longitude);
            
            if (validUmkms.length > 0) {
                const group = new L.FeatureGroup(
                    validUmkms.map(umkm => L.marker([umkm.latitude, umkm.longitude]))
                );
                
                if (group.getLayers().length > 1) {
                    map.fitBounds(group.getBounds().pad(0.1));
                } else if (group.getLayers().length === 1) {
                    const umkm = validUmkms[0];
                    map.setView([umkm.latitude, umkm.longitude], 15);
                }
            }
        }
        
        // Add event listeners to debug dragging
        map.on('dragstart', () => {
            console.log('Map drag started');
        });
        
        map.on('drag', () => {
            console.log('Map dragging...');
        });
        
        map.on('dragend', () => {
            console.log('Map drag ended');
        });
        
        map.on('mousedown', () => {
            console.log('Mouse down on map');
        });
        
        // Final check
        console.log('Map initialized with dragging:', map.dragging ? map.dragging.enabled() : 'dragging object not found');
        
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
                                    :href="route('home')"
                                    class="inline-flex items-center justify-center px-6 py-3 bg-white/20 border border-white/30 rounded-xl font-semibold text-sm text-white tracking-wide hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-200"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                    Kembali ke Beranda
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
                        <!-- Map Info Bar -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm text-green-700">
                                        <span class="font-semibold">{{ umkms.length }} UMKM</span> ditampilkan di peta. 
                                        Klik pada marker biru untuk melihat detail UMKM, atau arahkan kursor untuk melihat nama UMKM.
                                    </p>
                                </div>
                            </div>
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

/* Popup styling */
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

/* Fix for mobile touch events */
@media (max-width: 768px) {
    .leaflet-container {
        touch-action: pan-x pan-y !important;
    }
}
</style>