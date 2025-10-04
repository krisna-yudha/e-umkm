<script setup lang="ts">
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const showingUserMenu = ref(false);

const closeUserMenu = () => {
    setTimeout(() => {
        showingUserMenu.value = false;
    }, 150);
};
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="border-b border-gray-100 bg-white"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <!-- Dashboard navigation removed for cleaner interface -->
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Modern User Menu -->
                            <div class="relative">
                                <button
                                    @click="showingUserMenu = !showingUserMenu"
                                    @blur="closeUserMenu"
                                    class="flex items-center space-x-3 px-4 py-2 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                >
                                    <!-- Profile Photo -->
                                    <div class="relative">
                                        <img
                                            v-if="($page.props.auth.user as any).profile_photo"
                                            :src="`/storage/${($page.props.auth.user as any).profile_photo}`"
                                            :alt="$page.props.auth.user.name"
                                            class="h-10 w-10 rounded-full object-cover border-2 border-white shadow-sm"
                                        />
                                        <div
                                            v-else
                                            class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center border-2 border-white shadow-sm"
                                        >
                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </div>
                                        <!-- Online indicator -->
                                        <div class="absolute -bottom-0.5 -right-0.5 h-3 w-3 bg-green-400 border-2 border-white rounded-full"></div>
                                    </div>
                                    
                                    <div class="text-left">
                                        <p class="text-sm font-semibold text-gray-900">{{ $page.props.auth.user.name }}</p>
                                        <p class="text-xs text-gray-500">{{ ($page.props.auth.user as any).role === 'admin' ? 'Administrator' : 'Pelaku UMKM' }}</p>
                                    </div>

                                    <svg
                                        class="h-4 w-4 text-gray-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': showingUserMenu }"
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>

                                <!-- Modern Dropdown Menu -->
                                <Transition
                                    enter-active-class="transition ease-out duration-200"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-150"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95"
                                >
                                    <div
                                        v-show="showingUserMenu"
                                        class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50"
                                    >
                                        <!-- User Info Header -->
                                        <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                                            <div class="flex items-center space-x-3">
                                                <img
                                                    v-if="($page.props.auth.user as any).profile_photo"
                                                    :src="`/storage/${($page.props.auth.user as any).profile_photo}`"
                                                    :alt="$page.props.auth.user.name"
                                                    class="h-12 w-12 rounded-full object-cover border-2 border-white/20"
                                                />
                                                <div
                                                    v-else
                                                    class="h-12 w-12 rounded-full bg-white/20 flex items-center justify-center border-2 border-white/20"
                                                >
                                                    <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h3 class="font-semibold text-lg">{{ $page.props.auth.user.name }}</h3>
                                                    <p class="text-blue-100 text-sm">{{ $page.props.auth.user.email }}</p>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white mt-1">
                                                        {{ ($page.props.auth.user as any).role === 'admin' ? '👑 Administrator' : '🏢 Pelaku UMKM' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Menu Items -->
                                        <div class="py-2">
                                            <!-- Admin Menu -->
                                            <template v-if="($page.props.auth.user as any).role === 'admin'">
                                                <div class="px-4 py-2">
                                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Admin Panel</p>
                                                </div>
                                                <Link
                                                    :href="route('admin.dashboard')"
                                                    class="flex items-center px-6 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150"
                                                    @click="showingUserMenu = false"
                                                >
                                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                        <span class="text-blue-600">🏛️</span>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">Admin Dashboard</p>
                                                        <p class="text-xs text-gray-500">Kelola sistem secara keseluruhan</p>
                                                    </div>
                                                </Link>
                                                <Link
                                                    :href="route('admin.umkm')"
                                                    class="flex items-center px-6 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150"
                                                    @click="showingUserMenu = false"
                                                >
                                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                                        <span class="text-green-600">🏢</span>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">Kelola UMKM</p>
                                                        <p class="text-xs text-gray-500">Moderasi dan persetujuan UMKM</p>
                                                    </div>
                                                </Link>
                                                <Link
                                                    :href="route('admin.reports')"
                                                    class="flex items-center px-6 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150"
                                                    @click="showingUserMenu = false"
                                                >
                                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                                        <span class="text-purple-600">📊</span>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">Laporan</p>
                                                        <p class="text-xs text-gray-500">Analytics dan statistik</p>
                                                    </div>
                                                </Link>
                                                <Link
                                                    :href="route('admin.password-reset-requests')"
                                                    class="flex items-center px-6 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-150"
                                                    @click="showingUserMenu = false"
                                                >
                                                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                                        <span class="text-red-600">🔐</span>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">Reset Password</p>
                                                        <p class="text-xs text-gray-500">Kelola permintaan reset password</p>
                                                    </div>
                                                </Link>
                                                <div class="border-t border-gray-100 my-2"></div>
                                            </template>

                                            <!-- User Menu (only for non-admin users) -->
                                            <template v-else>
                                                <div class="px-4 py-2">
                                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Akun Saya</p>
                                                </div>
                                                <Link
                                                    :href="route('user.profile')"
                                                    class="flex items-center px-6 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors duration-150"
                                                    @click="showingUserMenu = false"
                                                >
                                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                                        <span class="text-gray-600">👤</span>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">Dashboard Profil</p>
                                                        <p class="text-xs text-gray-500">Lihat profil dan UMKM</p>
                                                    </div>
                                                </Link>
                                                <Link
                                                    :href="route('profile.edit')"
                                                    class="flex items-center px-6 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors duration-150"
                                                    @click="showingUserMenu = false"
                                                >
                                                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                                        <span class="text-orange-600">⚙️</span>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium">Edit Profil</p>
                                                        <p class="text-xs text-gray-500">Ubah informasi akun</p>
                                                    </div>
                                                </Link>
                                            </template>
                                            <!-- Common Menu Items for all users -->
                                            <Link
                                                :href="route('help')"
                                                class="flex items-center px-6 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors duration-150"
                                                @click="showingUserMenu = false"
                                            >
                                                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                                    <span class="text-yellow-600">❓</span>
                                                </div>
                                                <div>
                                                    <p class="font-medium">Bantuan</p>
                                                    <p class="text-xs text-gray-500">Pusat bantuan dan FAQ</p>
                                                </div>
                                            </Link>

                                            <div class="border-t border-gray-100 my-2"></div>
                                            
                                            <Link
                                                :href="route('logout')"
                                                method="post"
                                                as="button"
                                                class="w-full flex items-center px-6 py-3 text-sm text-red-700 hover:bg-red-50 transition-colors duration-150"
                                                @click="showingUserMenu = false"
                                            >
                                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                                    <span class="text-red-600">🚪</span>
                                                </div>
                                                <div>
                                                    <p class="font-medium">Keluar</p>
                                                    <p class="text-xs text-red-500">Logout dari akun</p>
                                                </div>
                                            </Link>
                                        </div>
                                    </div>
                                </Transition>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <!-- Dashboard navigation removed for cleaner interface -->
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('user.profile')">
                                👤 Dashboard Profil
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('profile.edit')">
                                ⚙️ Edit Profil
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                🚪 Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
