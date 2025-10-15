<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-medium text-gray-700">⏰ Jam Operasional</h4>
            <button 
                type="button"
                @click="toggleAllDays"
                class="text-xs text-blue-600 hover:text-blue-800 font-medium"
            >
                {{ allDaysOpen ? 'Tutup Semua' : 'Buka Semua' }}
            </button>
        </div>
        
        <div class="space-y-3">
            <div 
                v-for="(day, index) in operatingHours" 
                :key="day.day"
                class="p-3 bg-gray-50 rounded-lg border border-gray-200"
            >
                <!-- Mobile Layout (Stacked) -->
                <div class="block sm:hidden space-y-3">
                    <!-- Day and Toggle Row -->
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-medium text-gray-700">
                            {{ day.label }}
                        </label>
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    v-model="day.isOpen" 
                                    @change="updateOperatingHours"
                                    class="sr-only peer"
                                >
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Time Inputs Row (Mobile) -->
                    <div v-if="day.isOpen" class="space-y-2">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-600 w-12">Buka:</span>
                            <input 
                                type="time" 
                                v-model="day.openTime"
                                @change="updateOperatingHours"
                                class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-600 w-12">Tutup:</span>
                            <input 
                                type="time" 
                                v-model="day.closeTime"
                                @change="updateOperatingHours"
                                class="flex-1 text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <div class="flex items-center">
                            <label class="flex items-center cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    v-model="day.is24Hours"
                                    @change="toggle24Hours(index)"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                                >
                                <span class="ml-2 text-xs text-gray-600">24 Jam</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Closed Label (Mobile) -->
                    <div v-else>
                        <span class="text-sm text-gray-500 italic">Tutup</span>
                    </div>
                </div>

                <!-- Desktop Layout (Horizontal) -->
                <div class="hidden sm:flex items-center space-x-3">
                    <!-- Day Label -->
                    <div class="w-16 flex-shrink-0">
                        <label class="text-sm font-medium text-gray-700">
                            {{ day.label }}
                        </label>
                    </div>

                    <!-- Is Open Toggle -->
                    <div class="flex items-center flex-shrink-0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                v-model="day.isOpen" 
                                @change="updateOperatingHours"
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <!-- Time Inputs -->
                    <div v-if="day.isOpen" class="flex items-center space-x-2 flex-1 min-w-0">
                        <input 
                            type="time" 
                            v-model="day.openTime"
                            @change="updateOperatingHours"
                            class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-20"
                        >
                        <span class="text-gray-500 text-sm flex-shrink-0">-</span>
                        <input 
                            type="time" 
                            v-model="day.closeTime"
                            @change="updateOperatingHours"
                            class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-20"
                        >
                    </div>

                    <!-- Closed Label -->
                    <div v-else class="flex-1 min-w-0">
                        <span class="text-sm text-gray-500 italic">Tutup</span>
                    </div>

                    <!-- 24 Hours Toggle -->
                    <div v-if="day.isOpen" class="flex items-center flex-shrink-0">
                        <label class="flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                v-model="day.is24Hours"
                                @change="toggle24Hours(index)"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
                            >
                            <span class="ml-2 text-xs text-gray-600 whitespace-nowrap">24 Jam</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="pt-2">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <button 
                    type="button"
                    @click="setWeekdayHours"
                    class="px-3 py-2 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 text-center"
                >
                    Senin-Jumat 08:00-17:00
                </button>
                <button 
                    type="button"
                    @click="setWeekendHours"
                    class="px-3 py-2 text-xs bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors duration-200 text-center"
                >
                    Sabtu-Minggu 09:00-15:00
                </button>
                <button 
                    type="button"
                    @click="set24HoursAll"
                    class="px-3 py-2 text-xs bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors duration-200 text-center"
                >
                    24 Jam Setiap Hari
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface OperatingDay {
    day: string;
    label: string;
    isOpen: boolean;
    openTime: string;
    closeTime: string;
    is24Hours: boolean;
}

interface Props {
    modelValue?: Record<string, any> | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:modelValue': [value: Record<string, any>];
}>();

const operatingHours = ref<OperatingDay[]>([
    { day: 'monday', label: 'Senin', isOpen: false, openTime: '08:00', closeTime: '17:00', is24Hours: false },
    { day: 'tuesday', label: 'Selasa', isOpen: false, openTime: '08:00', closeTime: '17:00', is24Hours: false },
    { day: 'wednesday', label: 'Rabu', isOpen: false, openTime: '08:00', closeTime: '17:00', is24Hours: false },
    { day: 'thursday', label: 'Kamis', isOpen: false, openTime: '08:00', closeTime: '17:00', is24Hours: false },
    { day: 'friday', label: 'Jumat', isOpen: false, openTime: '08:00', closeTime: '17:00', is24Hours: false },
    { day: 'saturday', label: 'Sabtu', isOpen: false, openTime: '08:00', closeTime: '17:00', is24Hours: false },
    { day: 'sunday', label: 'Minggu', isOpen: false, openTime: '08:00', closeTime: '17:00', is24Hours: false },
]);

const allDaysOpen = computed(() => {
    return operatingHours.value.every(day => day.isOpen);
});

const initializeFromProps = () => {
    if (props.modelValue) {
        operatingHours.value.forEach(day => {
            const savedDay = props.modelValue![day.day];
            if (savedDay) {
                day.isOpen = savedDay.isOpen || false;
                day.openTime = savedDay.openTime || '08:00';
                day.closeTime = savedDay.closeTime || '17:00';
                day.is24Hours = savedDay.is24Hours || false;
            }
        });
    }
};

const updateOperatingHours = () => {
    const result: Record<string, any> = {};
    operatingHours.value.forEach(day => {
        result[day.day] = {
            isOpen: day.isOpen,
            openTime: day.isOpen ? day.openTime : null,
            closeTime: day.isOpen ? day.closeTime : null,
            is24Hours: day.isOpen ? day.is24Hours : false
        };
    });
    emit('update:modelValue', result);
};

const toggleAllDays = () => {
    const shouldOpen = !allDaysOpen.value;
    operatingHours.value.forEach(day => {
        day.isOpen = shouldOpen;
    });
    updateOperatingHours();
};

const toggle24Hours = (index: number) => {
    const day = operatingHours.value[index];
    if (day.is24Hours) {
        day.openTime = '00:00';
        day.closeTime = '23:59';
    } else {
        day.openTime = '08:00';
        day.closeTime = '17:00';
    }
    updateOperatingHours();
};

const setWeekdayHours = () => {
    operatingHours.value.slice(0, 5).forEach(day => {
        day.isOpen = true;
        day.openTime = '08:00';
        day.closeTime = '17:00';
        day.is24Hours = false;
    });
    updateOperatingHours();
};

const setWeekendHours = () => {
    operatingHours.value.slice(5).forEach(day => {
        day.isOpen = true;
        day.openTime = '09:00';
        day.closeTime = '15:00';
        day.is24Hours = false;
    });
    updateOperatingHours();
};

const set24HoursAll = () => {
    operatingHours.value.forEach(day => {
        day.isOpen = true;
        day.openTime = '00:00';
        day.closeTime = '23:59';
        day.is24Hours = true;
    });
    updateOperatingHours();
};

// Initialize on mount
initializeFromProps();

// Watch for props changes
watch(() => props.modelValue, () => {
    initializeFromProps();
}, { deep: true });
</script>

<style scoped>
/* Custom toggle switch styling */
input[type="checkbox"]:checked + div {
    background-color: #3b82f6;
}

input[type="checkbox"]:checked + div:after {
    transform: translateX(100%);
    border-color: white;
}
</style>