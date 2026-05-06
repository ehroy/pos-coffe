<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    tables: Array,
});

const menuItems = [
    { name: 'dashboard', label: 'Dashboard', href: route('owner.dashboard'), route: 'owner.dashboard' },
    { name: 'products', label: 'Products', href: route('owner.products'), route: 'owner.products' },
    { name: 'inventory', label: 'Inventory', href: route('owner.inventory'), route: 'owner.inventory' },
    { name: 'tables', label: 'Tables', href: route('owner.tables'), route: 'owner.tables' },
    { name: 'users', label: 'Users', href: route('owner.users'), route: 'owner.users' },
];
</script>

<template>
    <Head title="Tables Management" />

    <AppLayout title="Tables Management" :menu-items="menuItems">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-coffee-800">Manage Tables</h2>
                <p class="text-gray-600 mt-1">View and manage restaurant tables with QR codes</p>
            </div>
            <button class="px-6 py-3 bg-coffee-500 text-white rounded-lg hover:bg-coffee-600 transition-colors font-semibold">
                + Add New Table
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Total Tables</p>
                <p class="text-3xl font-bold text-coffee-800">{{ tables.length }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Active Tables</p>
                <p class="text-3xl font-bold text-green-600">{{ tables.filter(t => t.is_active).length }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Inactive Tables</p>
                <p class="text-3xl font-bold text-red-600">{{ tables.filter(t => !t.is_active).length }}</p>
            </div>
        </div>

        <!-- Tables Grid -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-coffee-800">All Tables</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        v-for="table in tables"
                        :key="table.id"
                        class="border-2 rounded-lg p-4 hover:border-coffee-500 transition-colors"
                        :class="table.is_active ? 'border-gray-200' : 'border-red-200 bg-red-50'"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs text-gray-500">{{ table.code }}</p>
                                <p class="text-lg font-bold text-coffee-800">{{ table.name }}</p>
                            </div>
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                :class="table.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                            >
                                {{ table.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <!-- QR Code Placeholder -->
                        <div class="bg-gray-100 rounded-lg p-4 mb-3 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>

                        <div class="space-y-2">
                            <button class="w-full px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">
                                View QR Code
                            </button>
                            <button class="w-full px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                                Edit
                            </button>
                        </div>

                        <p class="text-xs text-gray-500 mt-3 truncate">Token: {{ table.qr_token.substring(0, 12) }}...</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
