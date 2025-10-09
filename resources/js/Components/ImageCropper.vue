<template>
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Crop & Kompres Gambar</h3>
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

            <div class="cropper-container mb-4" style="max-height: 400px;">
                <vue-cropper
                    ref="cropper"
                    :src="imageSrc"
                    :aspect-ratio="aspectRatio"
                    :auto-crop-area="0.8"
                    :view-mode="1"
                    :drag-mode="'crop'"
                    :background="true"
                    :rotatable="true"
                    :scalable="true"
                    :zoomable="true"
                    :crop-box-movable="true"
                    :crop-box-resizable="true"
                    class="w-full"
                    style="max-height: 400px;"
                />
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Rasio:</label>
                    <select 
                        v-model="aspectRatio" 
                        @change="updateAspectRatio"
                        class="text-sm border border-gray-300 rounded px-2 py-1"
                    >
                        <option :value="16/9">16:9 (Landscape)</option>
                        <option :value="4/3">4:3 (Standard)</option>
                        <option :value="1">1:1 (Square)</option>
                        <option :value="3/4">3:4 (Portrait)</option>
                        <option :value="0">Free</option>
                    </select>
                </div>

                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Kualitas:</label>
                    <input 
                        v-model="quality" 
                        type="range" 
                        min="0.1" 
                        max="1" 
                        step="0.1"
                        class="w-20"
                    >
                    <span class="text-sm text-gray-600">{{ Math.round(quality * 100) }}%</span>
                </div>
            </div>

            <div class="flex items-center justify-center space-x-2 mb-4">
                <button 
                    @click="rotateCropper(-90)"
                    class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded transition-colors duration-200"
                    title="Rotate Left"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2h-2a2 2 0 01-2-2V6a2 2 0 012-2h2m0 0L16 4"/>
                    </svg>
                </button>
                <button 
                    @click="rotateCropper(90)"
                    class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded transition-colors duration-200"
                    title="Rotate Right"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12a8 8 0 11-16 0 8 8 0 0116 0zm-8-4v4l2-2"/>
                    </svg>
                </button>
                <button 
                    @click="flipHorizontal"
                    class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded transition-colors duration-200"
                    title="Flip Horizontal"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </button>
                <button 
                    @click="resetCropper"
                    class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 rounded transition-colors duration-200"
                    title="Reset"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>

            <div class="flex justify-end space-x-3">
                <button 
                    @click="closeModal"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-200"
                >
                    Batal
                </button>
                <button 
                    @click="cropAndCompress"
                    :disabled="isProcessing"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-md transition-colors duration-200"
                >
                    <span v-if="isProcessing">Memproses...</span>
                    <span v-else>Crop & Kompres</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick } from 'vue';
import VueCropper from 'vue-cropperjs';
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

const showModal = ref(false);
const cropper = ref<any>(null);
const imageSrc = ref('');
const aspectRatio = ref(16/9);
const quality = ref(0.8);
const isProcessing = ref(false);
const originalSize = ref('');
const compressionProgress = ref(0);
const scaleX = ref(1);

const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const updateAspectRatio = () => {
    if (cropper.value) {
        cropper.value.setAspectRatio(aspectRatio.value);
    }
};

const rotateCropper = (degrees: number) => {
    if (cropper.value) {
        cropper.value.rotate(degrees);
    }
};

const flipHorizontal = () => {
    scaleX.value = -scaleX.value;
    if (cropper.value) {
        cropper.value.scaleX(scaleX.value);
    }
};

const resetCropper = () => {
    if (cropper.value) {
        cropper.value.reset();
        scaleX.value = 1;
    }
};

const closeModal = () => {
    showModal.value = false;
    emit('close');
};

const cropAndCompress = async () => {
    if (!cropper.value) {
        emit('error', 'Cropper belum siap. Silakan coba lagi.');
        return;
    }
    
    isProcessing.value = true;
    compressionProgress.value = 0;

    try {
        // Get cropped canvas
        const canvas = cropper.value.getCroppedCanvas({
            width: 1200, // Max width
            height: 1200, // Max height for square, will be adjusted based on aspect ratio
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        if (!canvas) {
            throw new Error('Gagal membuat canvas dari crop area');
        }

        // Convert canvas to blob
        const blob: Blob | null = await new Promise((resolve) => {
            canvas.toBlob(resolve, 'image/jpeg', quality.value);
        });

        if (!blob) {
            throw new Error('Gagal mengkonversi canvas menjadi blob');
        }

        compressionProgress.value = 30;

        // Convert blob to file
        let file = new File([blob], props.imageFile?.name || 'cropped-image.jpg', {
            type: 'image/jpeg',
            lastModified: Date.now()
        });

        compressionProgress.value = 50;

        // Compress if file size exceeds maxSize
        const maxSizeInBytes = props.maxSize * 1024 * 1024; // Convert MB to bytes
        
        if (file.size > maxSizeInBytes) {
            const options = {
                maxSizeMB: props.maxSize,
                maxWidthOrHeight: 1920,
                useWebWorker: true,
                fileType: 'image/jpeg',
                initialQuality: quality.value
            };

            file = await imageCompression(file, options);
            compressionProgress.value = 90;
        }

        compressionProgress.value = 100;

        // Emit the processed file
        emit('success', 'Gambar berhasil dikompres dan di-crop!');
        emit('cropped', file);
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
    showModal.value = newVal;
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
    if (newFile && showModal.value) {
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
.cropper-container {
    background: #f3f4f6;
    border-radius: 8px;
    overflow: hidden;
}

:deep(.cropper-container) {
    background: #f3f4f6;
}

:deep(.cropper-view-box) {
    outline: 2px solid #3b82f6;
}

:deep(.cropper-face) {
    background-color: rgba(59, 130, 246, 0.1);
}
</style>