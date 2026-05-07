<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useKitchenMenu } from '@/Composables/useMenuItems';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    orders: Object,
    filters: Object,
});

const menuItems = useKitchenMenu();
const currency = (v) => new Intl.NumberFormat('id-ID').format(Number(v ?? 0));

const search = ref(props.filters?.search ?? '');
const date = ref(props.filters?.date ?? '');

let searchTimer = null;
watch([search, date], () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(route('kitchen.history'), { search: search.value, date: date.value }, { preserveState: true, replace: true });
    }, 400);
});

const statusClass = (status) => ({
    completed: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-700',
}[status] ?? 'bg-gray-100 text-gray-700');
</script>

<template>
    <Head title="Riwayat Order" />

    <AppLayout title="Riwayat Order" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <h2 class="text-2xl font-bold">Riwayat Order</h2>
            <p class="mt-1 text-coffee-200">Order selesai dan dibatalkan.</p>
        </div>

        <div class="mb-4 flex flex-wrap gap-3">
            <input
                v-model="search"
                type="text"
                placeholder="Cari nomor order..."
                class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-brand"
            />
            <input
                v-model="date"
                type="date"
                class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-brand"
            />
        </div>

        <div v-if="!orders.data?.length" class="rounded-xl border border-dashed border-gray-300 bg-white py-12 text-center text-gray-500">
            Belum ada riwayat order.
        </div>

        <div v-else>
            <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
                <div v-for="order in orders.data" :key="order.id" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="mb-3 flex items-start justify-between">
                        <div>
                            <p class="text-xs text-gray-400">{{ order.order_number }}</p>
                            <p class="font-semibold text-slate-800">{{ order.table?.name || 'Kasir' }}</p>
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
                        <p v-if="order.items?.length > 3" class="text-xs text-gray-400">+{{ order.items.length - 3 }} lainnya</p>
                    </div>

                    <div class="border-t border-gray-100 pt-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Pembayaran</span>
                            <span class="uppercase">{{ order.payment?.method || '-' }}</span>
                        </div>
                        <div class="flex justify-between font-semibold">
                            <span class="text-gray-500">Total</span>
                            <span class="text-slate-800">Rp {{ currency(order.total) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="orders.last_page > 1" class="mt-4 flex items-center justify-between">
                <p class="text-sm text-gray-500">{{ orders.from }}–{{ orders.to }} dari {{ orders.total }} order</p>
                <div class="flex gap-2">
                    <Link v-if="orders.prev_page_url" :href="orders.prev_page_url" class="rounded-xl border border-slate-200 px-4 py-2 text-sm hover:bg-slate-50">Sebelumnya</Link>
                    <Link v-if="orders.next_page_url" :href="orders.next_page_url" class="rounded-xl border border-slate-200 px-4 py-2 text-sm hover:bg-slate-50">Berikutnya</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
