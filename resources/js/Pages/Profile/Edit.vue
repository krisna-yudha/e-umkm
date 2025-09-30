<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail?: boolean;
    status?: string;
}>();
</script>

<template>
    <Head title="Edit Profile" />

    <AuthenticatedLayout>
        <template #header>
            <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 px-6 py-8 rounded-lg shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-white mb-2">
                            ✨ Edit Profile
                        </h2>
                        <p class="text-blue-100 text-lg">
                            Kelola informasi profile dan pengaturan keamanan akun Anda
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12 bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
            <div class="mx-auto max-w-5xl space-y-8 sm:px-6 lg:px-8">
                <!-- Profile Information Card -->
                <div class="group transform hover:scale-[1.02] transition-all duration-300">
                    <div class="overflow-hidden bg-white shadow-xl sm:rounded-2xl border-0 ring-1 ring-gray-200/50">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-8 py-6">
                            <div class="flex items-center space-x-4">
                                <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">
                                        👤 Informasi Profile
                                    </h3>
                                    <p class="text-emerald-100 text-sm">
                                        Perbarui foto profile, nama, dan alamat email Anda
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8 bg-gradient-to-br from-white to-gray-50">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                                class="max-w-2xl"
                            />
                        </div>
                    </div>
                </div>

                <!-- Password Update Card -->
                <div class="group transform hover:scale-[1.02] transition-all duration-300">
                    <div class="overflow-hidden bg-white shadow-xl sm:rounded-2xl border-0 ring-1 ring-gray-200/50">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-8 py-6">
                            <div class="flex items-center space-x-4">
                                <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">
                                        🔐 Keamanan Password
                                    </h3>
                                    <p class="text-amber-100 text-sm">
                                        Pastikan akun Anda menggunakan password yang kuat dan aman
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8 bg-gradient-to-br from-white to-gray-50">
                            <UpdatePasswordForm class="max-w-xl" />
                        </div>
                    </div>
                </div>

                <!-- Delete Account Card -->
                <div class="group transform hover:scale-[1.02] transition-all duration-300">
                    <div class="overflow-hidden bg-white shadow-xl sm:rounded-2xl border-0 ring-1 ring-red-200/50">
                        <div class="bg-gradient-to-r from-red-500 to-pink-600 px-8 py-6">
                            <div class="flex items-center space-x-4">
                                <div class="bg-white/20 backdrop-blur-sm rounded-full p-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">
                                        ⚠️ Zona Berbahaya
                                    </h3>
                                    <p class="text-red-100 text-sm">
                                        Setelah akun dihapus, semua data akan hilang permanen dan tidak dapat dikembalikan
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8 bg-gradient-to-br from-white to-red-50">
                            <DeleteUserForm class="max-w-xl" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
