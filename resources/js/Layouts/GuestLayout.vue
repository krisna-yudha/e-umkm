<script setup lang="ts">
import { computed } from 'vue';

// Check if current page is home page
const isHomePage = computed(() => {
    if (typeof window !== 'undefined') {
        const path = window.location.pathname;
        return path === '/' || path === '' || path === '/welcome';
    }
    return false;
});

// Check if current page is auth page (login/register)
const isAuthPage = computed(() => {
    if (typeof window !== 'undefined') {
        const path = window.location.pathname;
        return path.includes('/login') || path.includes('/register') || path.includes('/password');
    }
    return false;
});
</script>

<template>
    <!-- Fullscreen layout for home page -->
    <div v-if="isHomePage" class="w-full h-full">
        <slot />
    </div>
    
    <!-- Fullscreen layout for auth pages -->
    <div v-else-if="isAuthPage" class="w-full h-full">
        <slot />
    </div>
    
    <!-- Default layout for other pages -->
    <div v-else class="w-full h-full">
        <slot />
    </div>
</template>
