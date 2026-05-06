<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
});

const menuItems = [
    {
        name: 'dashboard',
        label: 'Dashboard',
        href: route('owner.dashboard'),
        route: 'owner.dashboard',
    },
    {
        name: 'products',
        label: 'Products',
        href: route('owner.products'),
        route: 'owner.products',
    },
    {
        name: 'inventory',
        label: 'Inventory',
        href: route('owner.inventory'),
        route: 'owner.inventory',
    },
    {
        name: 'tables',
        label: 'Tables',
        href: route('owner.tables'),
        route: 'owner.tables',
    },
    {
        name: 'users',
        label: 'Users',
        href: route('owner.users'),
        route: 'owner.users',
    },
];
</script>

<template>
    <Head title="Owner Dashboard" />

    <AppLayout title="Owner Dashboard" :menu-items="menuItems">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-coffee-600 to-coffee-800 rounded-lg shadow-lg p-8 text-white mb-6">
            <h2 class="text-3xl font-bold mb-2">Welcome Back, Owner! ☕</h2>
            <p class="text-coffee-100">Here's an overview of your coffee shop management system.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Users</p>
                        <p class="text-3xl font-bold text-coffee-800">{{ stats.total_users }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <span class="font-semibold">{{ stats.active_users }}</span> active
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Active Tables -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tables</p>
                        <p class="text-3xl font-bold text-coffee-800">{{ stats.total_tables }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <span class="font-semibold">{{ stats.active_tables }}</span> active
                        </p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Categories</p>
                        <p class="text-3xl font-bold text-coffee-800">{{ stats.total_categories }}</p>
                        <p class="text-xs text-gray-500 mt-1">Product categories</p>
                    </div>
                    <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Sales Today -->
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Sales Today</p>
                        <p class="text-3xl font-bold text-coffee-800">Rp 0</p>
                        <p class="text-xs text-gray-500 mt-1">Coming in Phase 4</p>
                    </div>
                    <div class="w-14 h-14 bg-gold/20 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-coffee-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button class="flex flex-col items-center p-6 border-2 border-gray-200 rounded-lg hover:border-coffee-500 hover:bg-coffee-50 transition-all group">
                    <div class="w-12 h-12 bg-coffee-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-coffee-500 transition-colors">
                        <svg class="w-6 h-6 text-coffee-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-coffee-800">Add Product</span>
                </button>

                <button class="flex flex-col items-center p-6 border-2 border-gray-200 rounded-lg hover:border-coffee-500 hover:bg-coffee-50 transition-all group">
                    <div class="w-12 h-12 bg-coffee-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-coffee-500 transition-colors">
                        <svg class="w-6 h-6 text-coffee-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-coffee-800">View Reports</span>
                </button>

                <button class="flex flex-col items-center p-6 border-2 border-gray-200 rounded-lg hover:border-coffee-500 hover:bg-coffee-50 transition-all group">
                    <div class="w-12 h-12 bg-coffee-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-coffee-500 transition-colors">
                        <svg class="w-6 h-6 text-coffee-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-coffee-800">Manage Users</span>
                </button>

                <button class="flex flex-col items-center p-6 border-2 border-gray-200 rounded-lg hover:border-coffee-500 hover:bg-coffee-50 transition-all group">
                    <div class="w-12 h-12 bg-coffee-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-coffee-500 transition-colors">
                        <svg class="w-6 h-6 text-coffee-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-coffee-800">Settings</span>
                </button>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-coffee-800 mb-4">Recent Orders</h3>
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p>No orders yet</p>
                    <p class="text-sm">Orders will appear here in Phase 4</p>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-coffee-800 mb-4">Low Stock Alert</h3>
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p>All stock levels are good</p>
                    <p class="text-sm">Low stock alerts will appear here in Phase 3</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
