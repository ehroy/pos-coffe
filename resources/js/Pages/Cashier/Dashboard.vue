<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useCashierMenu } from '@/Composables/useMenuItems';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    stats: Object,
    recentOrders: Array,
    pendingTableOrders: Array,
});

const polling = ref(null);
const pendingTableCount = computed(() => props.pendingTableOrders?.length ?? 0);

const currency = (value) => new Intl.NumberFormat('id-ID').format(Number(value ?? 0));

const statusLabel = (order) => {
    if (order.payment_status === 'paid') {
        return 'PAID';
    }

    return (order.status ?? 'pending').toUpperCase();
};

const statusClass = (order) => {
    if (order.payment_status === 'paid') {
        return 'bg-green-100 text-green-800';
    }

    switch (order.status) {
        case 'processing':
            return 'bg-blue-100 text-blue-800';
        case 'ready':
            return 'bg-emerald-100 text-emerald-800';
        case 'completed':
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-orange-100 text-orange-800';
    }
};

const approveOrder = (order) => {
    router.patch(route('cashier.orders.approve', order.id), {}, { preserveScroll: true });
};

const rejectOrder = (order) => {
    router.patch(route('cashier.orders.reject', order.id), {}, { preserveScroll: true });
};

const refreshDashboard = () => {
    router.reload({
        only: ['stats', 'recentOrders', 'pendingTableOrders'],
        preserveScroll: true,
        preserveState: true,
    });
};

onMounted(() => {
    polling.value = window.setInterval(refreshDashboard, 10000);
});

onBeforeUnmount(() => {
    if (polling.value) {
        window.clearInterval(polling.value);
    }
});

const menuItems = useCashierMenu();
</script>

<template>
    <Head title="Cashier Dashboard" />

    <AppLayout title="Cashier Dashboard" :menu-items="menuItems">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg shadow-lg p-8 text-white mb-6">
            <h2 class="text-3xl font-bold mb-2">Cashier Dashboard</h2>
            <p class="text-green-100">Process orders and manage transactions efficiently.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Today's Sales</p>
                        <p class="text-3xl font-bold text-green-600">Rp {{ currency(props.stats.today_sales) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Cash + non-cash paid</p>
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
                        <p class="text-xs text-gray-500 mt-1">Orders created today</p>
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

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Table Pending</p>
                        <p class="text-3xl font-bold text-amber-600">{{ stats.table_orders_pending }}</p>
                        <p class="text-xs text-gray-500 mt-1">Need cashier approval</p>
                    </div>
                    <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Table Approved</p>
                        <p class="text-3xl font-bold text-emerald-600">{{ stats.table_orders_accepted }}</p>
                        <p class="text-xs text-gray-500 mt-1">Ready for kitchen</p>
                    </div>
                    <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">QRIS Pending</p>
                        <p class="text-3xl font-bold text-emerald-700">{{ stats.qris_pending_orders }}</p>
                        <p class="text-xs text-gray-500 mt-1">Need payment confirmation</p>
                    </div>
                    <div class="w-14 h-14 bg-emerald-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6 rounded-xl bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Pending Table Orders</h3>
                    <p class="text-sm text-gray-500">Order meja yang perlu approval sekarang.</p>
                </div>
                <Link :href="route('cashier.orders', { source: 'qr_table' })" class="rounded-lg border border-brand/30 px-4 py-2 text-sm font-semibold text-brand-dark hover:bg-brand/5">
                    View All
                </Link>
            </div>

            <div class="mb-4 flex flex-wrap gap-2">
                <Link :href="route('cashier.orders', { queue: 'qris_pending' })" class="rounded-full bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    QRIS Pending ({{ props.stats.qris_pending_orders }})
                </Link>
                <Link :href="route('cashier.orders', { source: 'qr_table' })" class="rounded-full border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    All Table Orders
                </Link>
            </div>

            <div v-if="pendingTableCount" class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                <strong>{{ pendingTableCount }}</strong> order meja baru menunggu approval.
            </div>

            <div v-if="props.pendingTableOrders?.length" class="grid gap-3 md:grid-cols-3">
                <div v-for="order in props.pendingTableOrders" :key="order.id" class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-semibold text-slate-800">{{ order.order_number }}</p>
                            <p class="text-sm text-gray-600">{{ order.table?.name || 'Table' }}</p>
                        </div>
                        <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-200">Pending</span>
                    </div>
                    <p class="mt-3 text-sm text-gray-700">{{ order.items?.length ?? 0 }} item · Rp {{ currency(order.total) }}</p>
                    <div class="mt-4 flex gap-2">
                        <Link :href="route('cashier.orders.show', order.id)" class="inline-flex rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-dark">
                            Review
                        </Link>
                        <button type="button" class="inline-flex rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100" @click="rejectOrder(order)">
                            Reject
                        </button>
                        <button type="button" class="inline-flex rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100" @click="approveOrder(order)">
                            Approve
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="rounded-xl border border-dashed border-gray-300 py-8 text-center text-sm text-gray-500">
                Tidak ada order meja yang menunggu approval.
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-slate-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <Link :href="route('cashier.pos')" class="flex flex-col items-center rounded-lg bg-gradient-to-br from-coffee-500 to-coffee-600 p-6 text-white shadow-lg transition-all hover:from-coffee-600 hover:to-coffee-700 hover:shadow-xl">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="font-semibold">Open POS</span>
                    <span class="text-xs mt-1 opacity-90">Start new order</span>
                </Link>

                <Link :href="route('cashier.orders')" class="flex flex-col items-center rounded-lg border-2 border-gray-200 p-6 transition-all hover:border-brand hover:bg-brand/5 group">
                    <div class="w-12 h-12 bg-brand/10 rounded-full flex items-center justify-center mb-3 group-hover:bg-brand transition-colors">
                        <svg class="w-6 h-6 text-coffee-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-slate-800">View Orders</span>
                </Link>

                <Link :href="route('cashier.cash-session')" class="flex flex-col items-center rounded-lg border-2 border-gray-200 p-6 transition-all hover:border-brand hover:bg-brand/5 group">
                    <div class="w-12 h-12 bg-brand/10 rounded-full flex items-center justify-center mb-3 group-hover:bg-brand transition-colors">
                        <svg class="w-6 h-6 text-coffee-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-slate-800">Cash Session</span>
                </Link>

                <Link :href="route('cashier.cash-session')" class="flex flex-col items-center rounded-lg border-2 border-gray-200 p-6 transition-all hover:border-brand hover:bg-brand/5 group">
                    <div class="w-12 h-12 bg-brand/10 rounded-full flex items-center justify-center mb-3 group-hover:bg-brand transition-colors">
                        <svg class="w-6 h-6 text-coffee-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-700 group-hover:text-slate-800">Sesi Kas</span>
                </Link>
            </div>
        </div>

        <!-- POS Preview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Transactions -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Recent Transactions</h3>
                <div v-if="props.recentOrders?.length" class="space-y-3">
                    <div
                        v-for="order in props.recentOrders"
                        :key="order.id"
                        class="flex items-center justify-between rounded-lg border border-gray-200 p-4"
                    >
                        <div>
                            <p class="font-semibold text-gray-900">{{ order.order_number }}</p>
                            <p class="text-sm text-gray-500">{{ order.source === 'qr_table' ? 'QR Table' : 'Cashier' }} · {{ order.items?.length ?? 0 }} items</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold" :class="statusClass(order)">
                                {{ statusLabel(order) }}
                            </span>
                            <p class="mt-1 text-sm font-semibold text-gray-900">Rp {{ currency(order.total) }}</p>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-12 text-gray-500">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <p class="font-medium">No transactions yet</p>
                    <p class="text-sm mt-2">Start processing orders to see them here</p>
                    <Link :href="route('cashier.pos')" class="mt-4 inline-flex rounded-lg bg-brand px-6 py-2 text-white hover:bg-brand-dark">
                        Open POS
                    </Link>
                </div>
            </div>

            <!-- Cash Session Status -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Sesi Kas</h3>

                <div v-if="stats.has_active_session" class="space-y-3">
                    <div class="rounded-xl bg-green-50 border border-green-200 p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="font-semibold text-green-800">Sesi Aktif</span>
                        </div>
                        <div class="space-y-1 text-sm text-green-700">
                            <div class="flex justify-between">
                                <span>Kas Awal</span>
                                <span class="font-semibold">Rp {{ currency(stats.session_opening_cash) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Dibuka</span>
                                <span>{{ stats.session_opened_at ? new Date(stats.session_opened_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '-' }}</span>
                            </div>
                        </div>
                    </div>
                    <Link :href="route('cashier.cash-session')" class="block w-full rounded-xl border border-slate-200 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Kelola Sesi Kas
                    </Link>
                </div>

                <div v-else class="space-y-3">
                    <div class="rounded-xl bg-amber-50 border border-amber-200 p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="font-semibold text-amber-800">Sesi Belum Dibuka</span>
                        </div>
                        <p class="text-sm text-amber-700">Buka sesi kas sebelum memulai transaksi.</p>
                    </div>
                    <Link :href="route('cashier.cash-session')" class="block w-full rounded-xl bg-green-500 py-2.5 text-center text-sm font-semibold text-white hover:bg-green-600">
                        Buka Sesi Kas
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
