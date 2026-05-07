<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useWarehouseMenu } from '@/Composables/useMenuItems';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Object,
    lowStockItems: Array,
    recentMovements: Array,
});

const menuItems = useWarehouseMenu();

const formatCurrency = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(v || 0));

const movementTypeClass = (type) => ({
    in:         'bg-green-100 text-green-700',
    out:        'bg-red-100 text-red-700',
    adjustment: 'bg-blue-100 text-blue-700',
}[type] ?? 'bg-gray-100 text-gray-700');

const movementTypeLabel = (type) => ({ in: 'Masuk', out: 'Keluar', adjustment: 'Penyesuaian' }[type] ?? type);
</script>

<template>
    <Head title="Warehouse Dashboard" />

    <AppLayout title="Warehouse Dashboard" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <h2 class="text-2xl font-bold">Warehouse Dashboard</h2>
            <p class="mt-1 text-coffee-200">Pantau stok bahan baku dan pergerakan inventory.</p>
        </div>

        <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Total Bahan</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ stats.total_ingredients }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Stok Hampir Habis</p>
                <p class="mt-1 text-2xl font-bold" :class="stats.low_stock > 0 ? 'text-amber-600' : 'text-green-600'">{{ stats.low_stock }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Stok Habis</p>
                <p class="mt-1 text-2xl font-bold" :class="stats.out_of_stock > 0 ? 'text-red-600' : 'text-green-600'">{{ stats.out_of_stock }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Pergerakan Hari Ini</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ stats.movements_today }}</p>
            </div>
        </div>

        <div class="mb-4 flex gap-3">
            <Link :href="route('warehouse.inventory')" class="flex items-center gap-2 rounded-xl bg-coffee-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-coffee-900">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Kelola Stok
            </Link>
            <Link :href="route('warehouse.history')" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Riwayat Pergerakan
            </Link>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Stok Hampir Habis</h3>
                    <Link :href="route('warehouse.inventory')" class="text-xs text-brand hover:underline">Lihat Semua</Link>
                </div>
                <div v-if="lowStockItems?.length" class="space-y-2">
                    <div v-for="item in lowStockItems" :key="item.id" class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ item.name }}</p>
                            <p class="text-xs text-gray-400">Min: {{ item.minimum_stock }} {{ item.unit }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold" :class="Number(item.current_stock) <= 0 ? 'text-red-600' : 'text-amber-600'">
                                {{ item.current_stock }} {{ item.unit }}
                            </p>
                        </div>
                    </div>
                </div>
                <p v-else class="py-6 text-center text-sm text-green-600">Semua stok dalam kondisi baik.</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800">Pergerakan Terbaru</h3>
                    <Link :href="route('warehouse.history')" class="text-xs text-brand hover:underline">Lihat Semua</Link>
                </div>
                <div v-if="recentMovements?.length" class="space-y-2">
                    <div v-for="m in recentMovements" :key="m.id" class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm">
                        <div>
                            <p class="font-medium text-slate-800">{{ m.ingredient?.name }}</p>
                            <p class="text-xs text-gray-400">{{ m.creator?.name || 'System' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold" :class="movementTypeClass(m.type)">
                                {{ movementTypeLabel(m.type) }}
                            </span>
                            <p class="mt-0.5 text-xs font-semibold text-slate-700">{{ m.qty }} {{ m.ingredient?.unit }}</p>
                        </div>
                    </div>
                </div>
                <p v-else class="py-6 text-center text-sm text-gray-500">Belum ada pergerakan stok.</p>
            </div>
        </div>
    </AppLayout>
</template>
