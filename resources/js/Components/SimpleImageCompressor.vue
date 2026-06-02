<template>
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">🗜️ Kompres Gambar dengan TinyIMG</h3>
                <button 
                    @click="closeModal" 
                    class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Info Box -->
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    ✨ <strong>TinyIMG API</strong> akan mengompres gambar dengan kualitas terbaik
                </p>
            </div>

            <!-- Warning for Large Files -->
            <div v-if="isLargeFile" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800">
                    ⚠️ <strong>File Besar Terdeteksi!</strong> Gambar ini akan di-pre-compress secara lokal sebelum diproses oleh TinyIMG untuk hasil optimal.
                </p>
            </div>

            <!-- Progress Bar -->
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

            <!-- Compression Controls -->
            <div v-if="!isProcessing" class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-2">
                            Kualitas: {{ Math.round(quality * 100) }}%
                        </label>
                        <input 
                            v-model.number="quality" 
                            type="range" 
                            min="0.5" 
                            max="1" 
                            step="0.05"
                            class="w-full"
                        />
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-2">
                            Ukuran Target
                        </label>
                        <select v-model="targetSize" class="w-full text-sm border border-gray-300 rounded px-2 py-1">
                            <option value="original">Ukuran Asli</option>
                            <option value="1200">1200px (Recommended)</option>
                            <option value="1024">1024px</option>
                            <option value="800">800px</option>
                            <option value="600">600px</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-800">
                    <strong>❌ Error:</strong> {{ errorMessage }}
                </p>
            </div>

            <!-- Action Buttons -->
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
                    <span v-if="isProcessing">⏳ Memproses dengan TinyIMG...</span>
                    <span v-else>🗜️ Kompres & Simpan</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import axios from 'axios';

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
const isProcessing = ref(false);
const originalSize = ref('');
const compressionProgress = ref(0);
const errorMessage = ref('');
const quality = ref(0.8);
const targetSize = ref('original');
const isLargeFile = ref(false);

const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const closeModal = () => {
    emit('close');
};

// Pre-compress image locally using canvas for large files
const preCompressLocally = async (file: File): Promise<File> => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                
                // Calculate new dimensions
                let width = img.width;
                let height = img.height;
                
                if (targetSize.value !== 'original') {
                    const maxDim = parseInt(targetSize.value);
                    const scale = Math.min(maxDim / width, maxDim / height, 1);
                    width = Math.round(width * scale);
                    height = Math.round(height * scale);
                }
                
                canvas.width = width;
                canvas.height = height;
                
                const ctx = canvas.getContext('2d');
                if (!ctx) {
                    reject(new Error('Failed to get canvas context'));
                    return;
                }
                
                ctx.drawImage(img, 0, 0, width, height);
                
                canvas.toBlob(
                    (blob) => {
                        if (!blob) {
                            reject(new Error('Failed to create blob'));
                            return;
                        }
                        const newFile = new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        resolve(newFile);
                    },
                    'image/jpeg',
                    quality.value
                );
            };
            img.onerror = () => reject(new Error('Failed to load image'));
            img.src = e.target?.result as string;
        };
        reader.onerror = () => reject(new Error('Failed to read file'));
        reader.readAsDataURL(file);
    });
};

const processImage = async () => {
    if (!props.imageFile) {
        emit('error', 'Tidak ada file gambar yang dipilih');
        return;
    }
    
    isProcessing.value = true;
    compressionProgress.value = 10;
    errorMessage.value = '';

    try {
        let fileToCompress = props.imageFile;
        
        // Pre-compress locally if file > 2MB
        if (props.imageFile.size > 2097152) {
            compressionProgress.value = 15;
            console.log('Pre-compressing large file locally...');
            fileToCompress = await preCompressLocally(props.imageFile);
            compressionProgress.value = 30;
        }

        const formData = new FormData();
        formData.append('image', fileToCompress);

        compressionProgress.value = 40;

        // Call backend endpoint to compress with TinyIMG
        const response = await axios.post('/api/compress-image', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
            timeout: 30000, // 30 second timeout
            onUploadProgress: (progressEvent: any) => {
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                compressionProgress.value = Math.min(40 + (percentCompleted * 0.45), 85);
            }
        });

        compressionProgress.value = 90;

        if (response.data.success && response.data.compressedImage) {
            // Convert base64 back to File
            const base64Data = response.data.compressedImage.includes(',') 
                ? response.data.compressedImage.split(',')[1] 
                : response.data.compressedImage;
            
            const binaryString = atob(base64Data);
            const bytes = new Uint8Array(binaryString.length);
            for (let i = 0; i < binaryString.length; i++) {
                bytes[i] = binaryString.charCodeAt(i);
            }
            
            const processedFile = new File([bytes], props.imageFile.name, {
                type: 'image/jpeg',
                lastModified: Date.now()
            });

            compressionProgress.value = 100;

            const savedSize = formatFileSize(processedFile.size);
            const compressionRate = ((1 - processedFile.size / props.imageFile.size) * 100).toFixed(1);

            emit('success', `✅ Gambar berhasil dikompres dari ${originalSize.value} menjadi ${savedSize} (${compressionRate}% lebih kecil)!`);
            emit('cropped', processedFile);
            
            // Reset form
            quality.value = 0.8;
            targetSize.value = 'original';
            
            closeModal();
        } else {
            throw new Error(response.data.message || 'Kompresi gagal');
        }

    } catch (error: any) {
        console.error('Error processing image:', error);
        
        let errorMsg = 'Terjadi kesalahan saat memproses gambar';
        
        if (error.response?.data?.message) {
            errorMsg = error.response.data.message;
        } else if (error.message === 'Network Error') {
            errorMsg = 'Koneksi jaringan bermasalah. Silakan periksa koneksi internet Anda.';
        } else if (error.code === 'ECONNABORTED') {
            errorMsg = 'Proses kompresi timeout. File terlalu besar atau koneksi lambat.';
        } else if (error.message) {
            errorMsg = error.message;
        }
        
        errorMessage.value = errorMsg;
        emit('error', errorMsg);
    } finally {
        isProcessing.value = false;
        compressionProgress.value = 0;
    }
};

watch(() => props.show, (newVal) => {
    if (newVal && props.imageFile) {
        try {
            isLargeFile.value = props.imageFile.size > 2097152;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                imageSrc.value = e.target?.result as string;
            };
            reader.onerror = () => {
                emit('error', 'Gagal membaca file gambar');
            };
            reader.readAsDataURL(props.imageFile);
            originalSize.value = formatFileSize(props.imageFile.size);
            errorMessage.value = '';
        } catch (error) {
            emit('error', 'Gagal memproses file gambar');
        }
    }
});

watch(() => props.imageFile, (newFile) => {
    if (newFile && props.show) {
        try {
            isLargeFile.value = newFile.size > 2097152;
            
            const reader = new FileReader();
            reader.onload = (e) => {
                imageSrc.value = e.target?.result as string;
            };
            reader.onerror = () => {
                emit('error', 'Gagal membaca file gambar');
            };
            reader.readAsDataURL(newFile);
            originalSize.value = formatFileSize(newFile.size);
            errorMessage.value = '';
        } catch (error) {
            emit('error', 'Gagal memproses file gambar');
        }
    }
});
</script>

<style scoped>
/* Simple styles */
</style>