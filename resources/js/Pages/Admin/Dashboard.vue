<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: Object,
});

const menuItems = [
    {
        name: 'dashboard',
        label: 'Dashboard',
        href: route('admin.dashboard'),
        route: 'admin.dashboard',
    },
    {
        name: 'products',
        label: 'Products',
        href: route('admin.products'),
        route: 'admin.products',
    },
    {
        name: 'categories',
        label: 'Categories',
        href: route('admin.categories'),
        route: 'admin.categories',
    },
    {
        name: 'inventory',
        label: 'Inventory',
        href: route('admin.inventory'),
        route: 'admin.inventory',
    },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout title="Admin Dashboard" :menu-items="menuItems">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-8 text-white mb-6">
            <h2 class="text-3xl font-bold mb-2">Admin Dashboard 🛠️</h2>
            <p class="text-blue-100">Manage products, inventory, and system settings.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Products</p>
                        <p class="text-3xl font-bold text-coffee-800">{{ stats.products_count }}</p>
                        <p class="text-xs text-gray-500 mt-1">Phase 2</p>
                    </div>
                    <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Categories</p>
                        <p class="text-3xl font-bold text-coffee-800">{{ stats.categories_count }}</p>
                        <p class="text-xs text-green-600 mt-1">Active</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tables</p>
                        <p class="text-3xl font-bold text-coffee-800">{{ stats.tables_count }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total tables</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Active Users</p>
                        <p class="text-3xl font-bold text-coffee-800">{{ stats.active_users }}</p>
                        <p class="text-xs text-green-600 mt-1">Online</p>
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Product Management -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-coffee-800">Product Management</h3>
                    <button class="px-4 py-2 bg-coffee-500 text-white rounded-lg hover:bg-coffee-600 text-sm">
                        Add Product
                    </button>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-700">Total Products</span>
                        <span class="font-semibold text-coffee-800">{{ stats.products_count }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-700">Categories</span>
                        <span class="font-semibold text-coffee-800">{{ stats.categories_count }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-700">Out of Stock</span>
                        <span class="font-semibold text-red-600">0</span>
                    </div>
                </div>
            </div>

            <!-- Inventory Overview -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-coffee-800">Inventory Overview</h3>
                    <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">
                        Manage Stock
                    </button>
                </div>
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p>Inventory management</p>
                    <p class="text-sm">Coming in Phase 3</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
