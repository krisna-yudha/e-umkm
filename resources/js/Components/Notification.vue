<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" class="fixed top-4 right-4 z-50 w-full max-w-sm">
            <div :class="notificationClasses" class="rounded-lg p-4 shadow-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <!-- Success Icon -->
                        <svg v-if="type === 'success'" class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <!-- Error Icon -->
                        <svg v-if="type === 'error'" class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <!-- Warning Icon -->
                        <svg v-if="type === 'warning'" class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p :class="textClasses" class="text-sm font-medium">
                            {{ message }}
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="close" :class="buttonClasses" class="rounded-md inline-flex focus:outline-none focus:ring-2 focus:ring-offset-2">
                            <span class="sr-only">Tutup</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup lang="ts">
import { computed, onMounted } from 'vue';

interface Props {
    show: boolean;
    type: 'success' | 'error' | 'warning';
    message: string;
    duration?: number; // in milliseconds
}

const props = withDefaults(defineProps<Props>(), {
    duration: 5000
});

const emit = defineEmits<{
    close: [];
}>();

const notificationClasses = computed(() => {
    const baseClasses = 'border-l-4';
    switch (props.type) {
        case 'success':
            return `${baseClasses} bg-green-50 border-green-400`;
        case 'error':
            return `${baseClasses} bg-red-50 border-red-400`;
        case 'warning':
            return `${baseClasses} bg-yellow-50 border-yellow-400`;
        default:
            return `${baseClasses} bg-blue-50 border-blue-400`;
    }
});

const textClasses = computed(() => {
    switch (props.type) {
        case 'success':
            return 'text-green-700';
        case 'error':
            return 'text-red-700';
        case 'warning':
            return 'text-yellow-700';
        default:
            return 'text-blue-700';
    }
});

const buttonClasses = computed(() => {
    switch (props.type) {
        case 'success':
            return 'text-green-400 hover:text-green-600 focus:ring-green-500';
        case 'error':
            return 'text-red-400 hover:text-red-600 focus:ring-red-500';
        case 'warning':
            return 'text-yellow-400 hover:text-yellow-600 focus:ring-yellow-500';
        default:
            return 'text-blue-400 hover:text-blue-600 focus:ring-blue-500';
    }
});

const close = () => {
    emit('close');
};

onMounted(() => {
    if (props.duration > 0) {
        setTimeout(() => {
            close();
        }, props.duration);
    }
});
</script>