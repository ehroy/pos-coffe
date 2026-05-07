<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useKitchenMenu } from '@/Composables/useMenuItems';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    orders: Array,
});

const menuItems = useKitchenMenu();

const statusClass = (status) => ({
    accepted:   'bg-purple-100 text-purple-800',
    processing: 'bg-blue-100 text-blue-800',
    ready:      'bg-green-100 text-green-800',
}[status] ?? 'bg-gray-100 text-gray-700');

const readyCount = computed(() => props.orders?.filter(o => o.status === 'ready').length ?? 0);
</script>

<template>
    <Head title="Kitchen Dashboard" />

    <AppLayout title="Kitchen Dashboard" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <h2 class="text-2xl font-bold">Kitchen Dashboard</h2>
            <p class="mt-1 text-coffee-200">Pantau dan kelola order masuk secara realtime.</p>
        </div>

        <div class="mb-6 grid grid-cols-3 gap-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Antrian Baru</p>
                <p class="mt-1 text-3xl font-bold text-orange-600">{{ stats.pending_orders }}</p>
                <p class="mt-0.5 text-xs text-gray-400">Menunggu diproses</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Sedang Diproses</p>
                <p class="mt-1 text-3xl font-bold text-blue-600">{{ stats.processing_orders }}</p>
                <p class="mt-0.5 text-xs text-gray-400">Sedang dimasak</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-gray-500">Selesai Hari Ini</p>
                <p class="mt-1 text-3xl font-bold text-green-600">{{ stats.completed_today }}</p>
                <p class="mt-0.5 text-xs text-gray-400">Order completed</p>
            </div>
        </div>

        <div class="mb-6 flex gap-3">
            <Link :href="route('kitchen.display')" class="flex items-center gap-2 rounded-xl bg-coffee-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-coffee-900">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Buka Kitchen Display
            </Link>
            <Link :href="route('kitchen.orders')" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                Active Orders ({{ stats.pending_orders + stats.processing_orders }})
            </Link>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800">Order Aktif Saat Ini</h3>
                <div class="flex items-center gap-2 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"></span>
                    Live
                </div>
            </div>

            <div v-if="orders?.length" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="order in orders"
                    :key="order.id"
                    class="rounded-xl border p-4"
                    :class="{
                        'border-purple-200 bg-purple-50/30': order.status === 'accepted',
                        'border-blue-200 bg-blue-50/30': order.status === 'processing',
                        'border-green-200 bg-green-50/30': order.status === 'ready',
                    }"
                >
                    <div class="mb-3 flex items-start justify-between">
                        <div>
                            <p class="text-xs text-gray-400">{{ order.order_number }}</p>
                            <p class="font-semibold text-slate-800">{{ order.table?.name ?? 'Kasir' }}</p>
                        </div>
                        <span class="rounded-full px-2 py-0.5 text-xs font-semibold" :class="statusClass(order.status)">
                            {{ order.status }}
                        </span>
                    </div>
                    <div class="mb-3 space-y-1 text-sm">
                        <div v-for="item in order.items?.slice(0, 3)" :key="item.id" class="flex justify-between">
                            <span class="text-slate-700">{{ item.qty }}x {{ item.product_name }}</span>
                            <span class="text-gray-400">{{ item.variant_name || '' }}</span>
                        </div>
                        <p v-if="order.items?.length > 3" class="text-xs text-gray-400">+{{ order.items.length - 3 }} item lainnya</p>
                    </div>
                    <Link :href="route('kitchen.orders')" class="block w-full rounded-lg bg-coffee-800 py-2 text-center text-xs font-semibold text-white hover:bg-coffee-900">
                        Kelola Order
                    </Link>
                </div>
            </div>

            <div v-else class="py-12 text-center text-gray-500">
                <svg class="mx-auto mb-3 h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <p class="font-medium">Tidak ada order aktif</p>
                <p class="mt-1 text-sm">Order baru akan muncul di sini secara otomatis.</p>
            </div>
        </div>
    </AppLayout>
</template>
