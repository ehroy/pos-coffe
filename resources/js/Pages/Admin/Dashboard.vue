<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAdminMenu } from '@/Composables/useMenuItems';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    bestSellers: Array,
});

const menuItems = useAdminMenu();

const formatCurrency = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value || 0));
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout title="Admin Dashboard" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <h2 class="text-2xl font-bold">Admin Dashboard</h2>
            <p class="mt-1 text-coffee-200">Kelola produk, inventory, laporan, dan pengaturan sistem.</p>
        </div>

        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Penjualan Hari Ini</p>
                <p class="mt-1 text-xl font-bold text-slate-800">{{ formatCurrency(stats.today_sales) }}</p>
                <p class="text-xs text-gray-400">Total transaksi paid</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Order Aktif</p>
                <p class="mt-1 text-xl font-bold text-slate-800">{{ stats.active_orders }}</p>
                <p class="text-xs text-gray-400">Pending / proses / ready</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Stok Hampir Habis</p>
                <p class="mt-1 text-xl font-bold" :class="stats.low_stock_count > 0 ? 'text-red-600' : 'text-green-600'">
                    {{ stats.low_stock_count }}
                </p>
                <p class="text-xs text-gray-400">Bahan baku</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Pengeluaran Bulan Ini</p>
                <p class="mt-1 text-xl font-bold text-slate-800">{{ formatCurrency(stats.expenses_this_month) }}</p>
                <p class="text-xs text-gray-400">Total expense</p>
            </div>
        </div>

        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Total Produk</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ stats.products_count }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Kategori</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ stats.categories_count }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Total Meja</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ stats.tables_count }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">User Aktif</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ stats.active_users }}</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Produk Terlaris Bulan Ini</h3>
                    <a :href="route('admin.reports')" class="text-xs text-brand hover:underline">Lihat Laporan</a>
                </div>
                <div v-if="bestSellers?.length" class="space-y-2">
                    <div
                        v-for="(item, index) in bestSellers"
                        :key="item.product_name"
                        class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2"
                    >
                        <div class="flex items-center gap-3">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand text-xs font-bold text-white">{{ index + 1 }}</span>
                            <span class="text-sm text-slate-800">{{ item.product_name }}</span>
                        </div>
                        <span class="text-sm font-semibold text-slate-800">{{ item.total_qty }} pcs</span>
                    </div>
                </div>
                <p v-else class="py-4 text-center text-sm text-gray-500">Belum ada data penjualan bulan ini.</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="mb-4 font-semibold text-slate-800">Akses Cepat</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a :href="route('admin.products')" class="group flex flex-col items-center rounded-xl border border-slate-200 p-4 text-center transition hover:border-brand/40 hover:bg-brand/5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-brand/15 group-hover:text-brand transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                        <span class="mt-2 text-sm font-medium text-slate-700">Produk</span>
                    </a>
                    <a :href="route('admin.inventory')" class="group flex flex-col items-center rounded-xl border border-slate-200 p-4 text-center transition hover:border-brand/40 hover:bg-brand/5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-brand/15 group-hover:text-brand transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </span>
                        <span class="mt-2 text-sm font-medium text-slate-700">Inventory</span>
                    </a>
                    <a :href="route('admin.reports')" class="group flex flex-col items-center rounded-xl border border-slate-200 p-4 text-center transition hover:border-brand/40 hover:bg-brand/5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-brand/15 group-hover:text-brand transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </span>
                        <span class="mt-2 text-sm font-medium text-slate-700">Laporan</span>
                    </a>
                    <a :href="route('admin.expenses')" class="group flex flex-col items-center rounded-xl border border-slate-200 p-4 text-center transition hover:border-brand/40 hover:bg-brand/5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-brand/15 group-hover:text-brand transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                        <span class="mt-2 text-sm font-medium text-slate-700">Pengeluaran</span>
                    </a>
                    <a :href="route('admin.tables')" class="group flex flex-col items-center rounded-xl border border-slate-200 p-4 text-center transition hover:border-brand/40 hover:bg-brand/5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-brand/15 group-hover:text-brand transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </span>
                        <span class="mt-2 text-sm font-medium text-slate-700">Meja</span>
                    </a>
                    <a :href="route('admin.users')" class="group flex flex-col items-center rounded-xl border border-slate-200 p-4 text-center transition hover:border-brand/40 hover:bg-brand/5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-500 group-hover:bg-brand/15 group-hover:text-brand transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </span>
                        <span class="mt-2 text-sm font-medium text-slate-700">Users</span>
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
