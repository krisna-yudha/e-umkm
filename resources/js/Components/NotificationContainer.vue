<script setup lang="ts">
import { useNotification } from '@/Composables/useNotification';

const { notifications, removeNotification } = useNotification();

const getNotificationStyles = (type: string) => {
    const styles: Record<string, string> = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-500 text-white',
        warning: 'bg-yellow-500 text-white'
    };
    return styles[type] || styles.info;
};

const getNotificationIcon = (type: string) => {
    const icons: Record<string, string> = {
        success: '✅',
        error: '❌',
        info: 'ℹ️',
        warning: '⚠️'
    };
    return icons[type] || '•';
};
</script>

<template>
    <div class="fixed top-4 right-4 z-50 space-y-3 pointer-events-none">
        <transition-group name="notification" tag="div">
            <div
                v-for="notification in notifications"
                :key="notification.id"
                :class="getNotificationStyles(notification.type)"
                class="px-4 sm:px-6 py-3 sm:py-4 rounded-lg shadow-2xl flex items-start gap-3 max-w-sm animate-in slide-in-from-right pointer-events-auto"
            >
                <span class="text-xl flex-shrink-0 mt-0.5">{{ getNotificationIcon(notification.type) }}</span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm sm:text-base font-medium break-words">{{ notification.message }}</p>
                </div>
                <button
                    @click="removeNotification(notification.id)"
                    class="flex-shrink-0 ml-2 hover:opacity-75 transition-opacity"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </transition-group>
    </div>
</template>

<style scoped>
.notification-enter-active,
.notification-leave-active {
    transition: all 0.3s ease;
}

.notification-enter-from {
    opacity: 0;
    transform: translateX(30px);
}

.notification-leave-to {
    opacity: 0;
    transform: translateX(30px);
}

.animate-in {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>
