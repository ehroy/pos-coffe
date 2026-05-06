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
        href: route('kitchen.dashboard'),
        route: 'kitchen.dashboard',
    },
    {
        name: 'orders',
        label: 'Active Orders',
        href: '#',
        route: 'kitchen.orders',
    },
    {
        name: 'history',
        label: 'Order History',
        href: '#',
        route: 'kitchen.history',
    },
];
</script>

<template>
    <Head title="Kitchen Dashboard" />

    <AppLayout title="Kitchen Dashboard" :menu-items="menuItems">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-lg shadow-lg p-8 text-white mb-6">
            <h2 class="text-3xl font-bold mb-2">Kitchen Dashboard 👨‍🍳</h2>
            <p class="text-orange-100">View and manage incoming orders in real-time.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pending Orders</p>
                        <p class="text-3xl font-bold text-orange-600">{{ stats.pending_orders }}</p>
                        <p class="text-xs text-gray-500 mt-1">Waiting to cook</p>
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Processing</p>
                        <p class="text-3xl font-bold text-blue-600">{{ stats.processing_orders }}</p>
                        <p class="text-xs text-gray-500 mt-1">Currently cooking</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Completed Today</p>
                        <p class="text-3xl font-bold text-green-600">{{ stats.completed_today }}</p>
                        <p class="text-xs text-gray-500 mt-1">Orders done</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Orders Display -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-coffee-800">Active Orders</h3>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm">
                        Pending (0)
                    </button>
                    <button class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">
                        Processing (0)
                    </button>
                    <button class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm">
                        Ready (0)
                    </button>
                </div>
            </div>

            <!-- Empty State -->
            <div class="text-center py-16 text-gray-500">
                <svg class="w-24 h-24 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <p class="text-xl font-medium mb-2">No Active Orders</p>
                <p class="text-sm">Orders will appear here in real-time when customers place them</p>
                <p class="text-xs text-gray-400 mt-4">Feature available in Phase 6 (Realtime Orders)</p>
            </div>
        </div>

        <!-- Order Cards Preview (Example) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Example Order Card 1 -->
            <div class="bg-white rounded-lg shadow-lg border-l-4 border-orange-500 p-6 opacity-50">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-xs font-semibold text-gray-500">ORDER #001</span>
                        <p class="text-sm text-gray-600 mt-1">Table 5</p>
                    </div>
                    <span class="px-3 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">
                        PENDING
                    </span>
                </div>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span>2x Espresso</span>
                        <span class="text-gray-500">Hot</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>1x Cappuccino</span>
                        <span class="text-gray-500">Large</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm font-semibold">
                        Start Cooking
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-3">Example order (Phase 6)</p>
            </div>

            <!-- Example Order Card 2 -->
            <div class="bg-white rounded-lg shadow-lg border-l-4 border-blue-500 p-6 opacity-50">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-xs font-semibold text-gray-500">ORDER #002</span>
                        <p class="text-sm text-gray-600 mt-1">Table 3</p>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                        PROCESSING
                    </span>
                </div>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span>1x Latte</span>
                        <span class="text-gray-500">Iced</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>1x French Fries</span>
                        <span class="text-gray-500">Regular</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="flex-1 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm font-semibold">
                        Mark Ready
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-3">Example order (Phase 6)</p>
            </div>

            <!-- Example Order Card 3 -->
            <div class="bg-white rounded-lg shadow-lg border-l-4 border-green-500 p-6 opacity-50">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <span class="text-xs font-semibold text-gray-500">ORDER #003</span>
                        <p class="text-sm text-gray-600 mt-1">Table 8</p>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                        READY
                    </span>
                </div>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span>1x Americano</span>
                        <span class="text-gray-500">Hot</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span>2x Croissant</span>
                        <span class="text-gray-500">-</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg text-sm font-semibold cursor-not-allowed">
                        Waiting Pickup
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-3">Example order (Phase 6)</p>
            </div>
        </div>
    </AppLayout>
</template>
