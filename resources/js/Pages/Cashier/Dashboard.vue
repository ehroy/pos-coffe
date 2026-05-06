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
        href: route('cashier.dashboard'),
        route: 'cashier.dashboard',
    },
    {
        name: 'pos',
        label: 'POS',
        href: '#',
        route: 'cashier.pos',
    },
    {
        name: 'orders',
        label: 'Orders',
        href: '#',
        route: 'cashier.orders',
    },
    {
        name: 'cash-session',
        label: 'Cash Session',
        href: '#',
        route: 'cashier.cash-session',
    },
];
</script>

<template>
    <Head title="Cashier Dashboard" />

    <AppLayout title="Cashier Dashboard" :menu-items="menuItems">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg shadow-lg p-8 text-white mb-6">
            <h2 class="text-3xl font-bold mb-2">Cashier Dashboard 💰</h2>
            <p class="text-green-100">Process orders and manage transactions efficiently.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Today's Sales</p>
                        <p class="text-3xl font-bold text-green-600">Rp {{ stats.today_sales.toLocaleString() }}</p>
                        <p class="text-xs text-gray-500 mt-1">Phase 4</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Today's Orders</p>
                        <p class="text-3xl font-bold text-blue-600">{{ stats.today_orders }}</p>
                        <p class="text-xs text-gray-500 mt-1">Transactions</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pending Orders</p>
                        <p class="text-3xl font-bold text-orange-600">{{ stats.pending_orders }}</p>
                        <p class="text-xs text-gray-500 mt-1">Awaiting payment</p>
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-coffee-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button class="flex flex-col items-center p-6 bg-gradient-to-br from-coffee-500 to-coffee-600 text-white rounded-lg hover:from-coffee-600 hover:to-coffee-700 transition-all shadow-lg hover:shadow-xl">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="font-semibold">Open POS</span>
                    <span class="text-xs mt-1 opacity-90">Start new order</span>
                </button>

                <button class="flex flex-col items-center p-6 border-2 border-gray-200 rounded-lg hover:border-coffee-500 hover:bg-coffee-50 transition-all group">
                    <div class="w-12 h-12 bg-coffee-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-coffee-500 transition-colors">
                        <svg class="w-6 h-6 text-coffee-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-coffee-800">View Orders</span>
                </button>

                <button class="flex flex-col items-center p-6 border-2 border-gray-200 rounded-lg hover:border-coffee-500 hover:bg-coffee-50 transition-all group">
                    <div class="w-12 h-12 bg-coffee-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-coffee-500 transition-colors">
                        <svg class="w-6 h-6 text-coffee-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-coffee-800">Cash Session</span>
                </button>

                <button class="flex flex-col items-center p-6 border-2 border-gray-200 rounded-lg hover:border-coffee-500 hover:bg-coffee-50 transition-all group">
                    <div class="w-12 h-12 bg-coffee-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-coffee-500 transition-colors">
                        <svg class="w-6 h-6 text-coffee-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-coffee-800">Reports</span>
                </button>
            </div>
        </div>

        <!-- POS Preview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Transactions -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-coffee-800 mb-4">Recent Transactions</h3>
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <p class="font-medium">No transactions yet</p>
                    <p class="text-sm mt-2">Start processing orders to see them here</p>
                    <button class="mt-4 px-6 py-2 bg-coffee-500 text-white rounded-lg hover:bg-coffee-600">
                        Open POS
                    </button>
                </div>
            </div>

            <!-- Cash Session Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-coffee-800 mb-4">Cash Session</h3>
                <div class="space-y-4">
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="font-semibold text-yellow-800">No Active Session</span>
                        </div>
                        <p class="text-sm text-yellow-700">Please open a cash session to start processing orders</p>
                    </div>
                    
                    <button class="w-full px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 font-semibold">
                        Open Cash Session
                    </button>

                    <div class="pt-4 border-t">
                        <p class="text-sm text-gray-600 mb-2">Session Info</p>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Opening Cash:</span>
                                <span class="font-semibold">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Current Cash:</span>
                                <span class="font-semibold">-</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Transactions:</span>
                                <span class="font-semibold">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
