<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useOwnerMenu } from '@/Composables/useMenuItems';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    recentOrders: Array,
    lowStockIngredients: Array,
});

const menuItems = useOwnerMenu();

const formatCurrency = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(v || 0));
const currency = (v) => new Intl.NumberFormat('id-ID').format(Number(v || 0));

const statusClass = (status) => ({
    pending:    'bg-yellow-100 text-yellow-800',
    accepted:   'bg-blue-100 text-blue-800',
    processing: 'bg-orange-100 text-orange-800',
    ready:      'bg-purple-100 text-purple-800',
    completed:  'bg-green-100 text-green-800',
    cancelled:  'bg-red-100 text-red-800',
}[status] ?? 'bg-gray-100 text-gray-700');
</script>

<template>
    <Head title="Owner Dashboard" />

    <AppLayout title="Owner Dashboard" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <h2 class="text-2xl font-bold">Selamat Datang, Owner</h2>
            <p class="mt-1 text-coffee-200">Ringkasan performa coffee shop hari ini.</p>
        </div>

        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Penjualan Hari Ini</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ formatCurrency(stats.total_sales_today) }}</p>
                <p class="mt-0.5 text-xs text-gray-400">Penjualan bulan ini: {{ formatCurrency(stats.monthly_sales) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Order Aktif</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ stats.active_orders }}</p>
                <p class="mt-0.5 text-xs text-gray-400">Sedang diproses</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Stok Hampir Habis</p>
                <p class="mt-1 text-2xl font-bold" :class="stats.low_stock_items > 0 ? 'text-red-600' : 'text-green-600'">
                    {{ stats.low_stock_items }}
                </p>
                <p class="mt-0.5 text-xs text-gray-400">Bahan baku</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Total User</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ stats.total_users }}</p>
                <p class="mt-0.5 text-xs text-green-600">{{ stats.active_users }} aktif</p>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <Link :href="route('owner.products')" class="group flex flex-col items-center rounded-xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-brand/40 hover:bg-brand/5">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-brand/15 group-hover:text-brand transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
                <span class="mt-2 text-sm font-medium text-slate-700">Produk</span>
            </Link>
            <Link :href="route('owner.inventory')" class="group flex flex-col items-center rounded-xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-brand/40 hover:bg-brand/5">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-brand/15 group-hover:text-brand transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </span>
                <span class="mt-2 text-sm font-medium text-slate-700">Inventory</span>
            </Link>
            <Link :href="route('owner.tables')" class="group flex flex-col items-center rounded-xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-brand/40 hover:bg-brand/5">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-brand/15 group-hover:text-brand transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </span>
                <span class="mt-2 text-sm font-medium text-slate-700">Meja</span>
            </Link>
            <Link :href="route('owner.users')" class="group flex flex-col items-center rounded-xl border border-slate-200 bg-white p-4 text-center shadow-sm transition hover:border-brand/40 hover:bg-brand/5">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-brand/15 group-hover:text-brand transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </span>
                <span class="mt-2 text-sm font-medium text-slate-700">Users</span>
            </Link>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Order Terbaru</h3>
                <div v-if="recentOrders?.length" class="space-y-2">
                    <div v-for="order in recentOrders" :key="order.id" class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm">
                        <div>
                            <p class="font-medium text-slate-800">{{ order.order_number }}</p>
                            <p class="text-xs text-gray-500">{{ order.table?.name || 'Kasir' }} · {{ order.items?.length }} item</p>
                        </div>
                        <div class="text-right">
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold" :class="statusClass(order.status)">{{ order.status }}</span>
                            <p class="mt-0.5 text-xs font-semibold text-slate-700">Rp {{ currency(order.total) }}</p>
                        </div>
                    </div>
                </div>
                <p v-else class="py-6 text-center text-sm text-gray-500">Belum ada order hari ini.</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Stok Hampir Habis</h3>
                    <Link :href="route('owner.inventory')" class="text-xs text-brand hover:underline">Lihat Semua</Link>
                </div>
                <div v-if="lowStockIngredients?.length" class="space-y-2">
                    <div v-for="item in lowStockIngredients" :key="item.id" class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm">
                        <span class="font-medium text-slate-800">{{ item.name }}</span>
                        <div class="text-right">
                            <p class="font-semibold text-red-600">{{ item.current_stock }} {{ item.unit }}</p>
                            <p class="text-xs text-gray-400">min: {{ item.minimum_stock }}</p>
                        </div>
                    </div>
                </div>
                <p v-else class="py-6 text-center text-sm text-green-600">Semua stok dalam kondisi baik.</p>
            </div>
        </div>
    </AppLayout>
</template>
