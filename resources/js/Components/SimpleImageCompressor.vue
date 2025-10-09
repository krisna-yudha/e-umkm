<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Kompres Gambar</h3>
                <button 
                    @click="closeModal" 
                    class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="mb-4">
                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                    <span>Ukuran asli: {{ originalSize }}</span>
                    <span>Target maksimal: 2MB</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div 
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                        :style="{ width: compressionProgress + '%' }"
                    ></div>
                </div>
            </div>

            <!-- Image Preview -->
            <div v-if="imageSrc" class="mb-4">
                <div class="border-2 border-gray-300 rounded-lg p-4 text-center">
                    <img 
                        :src="imageSrc" 
                        class="max-w-full max-h-80 mx-auto rounded"
                        style="object-fit: contain;"
                    />
                </div>
            </div>

            <!-- Simple Controls -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Kualitas:</label>
                    <input 
                        v-model="quality" 
                        type="range" 
                        min="0.1" 
                        max="1" 
                        step="0.1"
                        class="flex-1"
                    >
                    <span class="text-sm text-gray-600 w-12">{{ Math.round(quality * 100) }}%</span>
                </div>
                
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Ukuran:</label>
                    <select v-model="targetSize" class="flex-1 text-sm border border-gray-300 rounded px-2 py-1">
                        <option value="original">Ukuran Asli</option>
                        <option value="1200">1200px</option>
                        <option value="1024">1024px</option>
                        <option value="800">800px</option>
                        <option value="600">600px</option>
                    </select>
                </div>
            </div>

            <!-- Show estimated file size -->
            <div v-if="estimatedSize" class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong>Perkiraan ukuran hasil:</strong> {{ estimatedSize }}
                </p>
            </div>

            <div class="flex justify-end space-x-3">
                <button 
                    @click="closeModal"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-200"
                >
                    Batal
                </button>
                <button 
                    @click="processImage"
                    :disabled="isProcessing || !imageFile"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-md transition-colors duration-200"
                >
                    <span v-if="isProcessing">Memproses...</span>
                    <span v-else>Kompres & Simpan</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import imageCompression from 'browser-image-compression';

interface Props {
    show: boolean;
    imageFile: File | null;
    maxSize?: number; // in MB
}

const props = withDefaults(defineProps<Props>(), {
    maxSize: 2
});

const emit = defineEmits<{
    close: [];
    cropped: [file: File];
    error: [message: string];
    success: [message: string];
}>();

const imageSrc = ref('');
const quality = ref(0.8);
const targetSize = ref('1024');
const isProcessing = ref(false);
const originalSize = ref('');
const compressionProgress = ref(0);

const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const estimatedSize = computed(() => {
    if (!props.imageFile) return '';
    
    // Rough estimation based on quality and target size
    const currentSize = props.imageFile.size;
    const qualityFactor = quality.value;
    const sizeFactor = targetSize.value === 'original' ? 1 : 
                      parseInt(targetSize.value) <= 800 ? 0.4 : 
                      parseInt(targetSize.value) <= 1024 ? 0.6 : 0.8;
    
    const estimated = currentSize * qualityFactor * sizeFactor;
    return formatFileSize(estimated);
});

const closeModal = () => {
    emit('close');
};

const processImage = async () => {
    if (!props.imageFile) {
        emit('error', 'Tidak ada file gambar yang dipilih');
        return;
    }
    
    isProcessing.value = true;
    compressionProgress.value = 10;

    try {
        // Create compression options
        const options: any = {
            maxSizeMB: props.maxSize,
            useWebWorker: true,
            fileType: 'image/jpeg',
            initialQuality: quality.value
        };

        // Add maxWidthOrHeight only if not original
        if (targetSize.value !== 'original') {
            options.maxWidthOrHeight = parseInt(targetSize.value);
        }

        compressionProgress.value = 30;

        const compressedFile = await imageCompression(props.imageFile, options);
        
        compressionProgress.value = 80;

        // Create new file with original name but compressed content
        const processedFile = new File([compressedFile], props.imageFile.name, {
            type: 'image/jpeg',
            lastModified: Date.now()
        });

        compressionProgress.value = 100;

        emit('success', `Gambar berhasil dikompres dari ${originalSize.value} menjadi ${formatFileSize(processedFile.size)}!`);
        emit('cropped', processedFile);
        closeModal();

    } catch (error) {
        console.error('Error processing image:', error);
        emit('error', 'Terjadi kesalahan saat memproses gambar. Silakan coba lagi.');
    } finally {
        isProcessing.value = false;
        compressionProgress.value = 0;
    }
};

watch(() => props.show, (newVal) => {
    if (newVal && props.imageFile) {
        try {
            const reader = new FileReader();
            reader.onload = (e) => {
                imageSrc.value = e.target?.result as string;
            };
            reader.onerror = () => {
                emit('error', 'Gagal membaca file gambar');
            };
            reader.readAsDataURL(props.imageFile);
            originalSize.value = formatFileSize(props.imageFile.size);
        } catch (error) {
            emit('error', 'Gagal memproses file gambar');
        }
    }
});

watch(() => props.imageFile, (newFile) => {
    if (newFile && props.show) {
        try {
            const reader = new FileReader();
            reader.onload = (e) => {
                imageSrc.value = e.target?.result as string;
            };
            reader.onerror = () => {
                emit('error', 'Gagal membaca file gambar');
            };
            reader.readAsDataURL(newFile);
            originalSize.value = formatFileSize(newFile.size);
        } catch (error) {
            emit('error', 'Gagal memproses file gambar');
        }
    }
});
</script>

<style scoped>
/* Simple styles */
</style>