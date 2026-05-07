<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useKitchenMenu } from '@/Composables/useMenuItems';
import { Head, router } from '@inertiajs/vue3';
import { useOrderStore } from '@/Stores/useOrderStore';
import { onMounted, onUnmounted } from 'vue';

const props = defineProps({
    orders: Array,
});

const menuItems = useKitchenMenu();

const orderStore = useOrderStore();

const statusConfig = {
    accepted: { label: 'Baru', class: 'bg-purple-100 text-purple-800', next: 'processing', nextLabel: 'Mulai Proses' },
    processing: { label: 'Diproses', class: 'bg-blue-100 text-blue-800', next: 'ready', nextLabel: 'Siap Disajikan' },
    ready: { label: 'Siap', class: 'bg-green-100 text-green-800', next: 'completed', nextLabel: 'Selesai' },
};

const updateStatus = (order, status) => {
    router.patch(route('kitchen.orders.update', order.id), { status }, {
        preserveScroll: true,
        onSuccess: () => {
            orderStore.updateOrderStatus(order.id, status);
        },
    });
};

let echoChannel = null;

onMounted(() => {
    orderStore.setOrders(props.orders);

    if (window.Echo) {
        echoChannel = window.Echo.channel('kitchen');

        echoChannel.listen('.OrderCreated', (data) => {
            if (['accepted', 'processing', 'ready'].includes(data.status)) {
                orderStore.addOrUpdateOrder(data);
            }
        });

        echoChannel.listen('.OrderUpdated', (data) => {
            orderStore.addOrUpdateOrder(data);
            if (['completed', 'cancelled'].includes(data.status)) {
                orderStore.removeOrder(data.id);
            }
        });
    }
});

onUnmounted(() => {
    if (echoChannel) {
        window.Echo?.leave('kitchen');
    }
});

const currency = (value) => new Intl.NumberFormat('id-ID').format(Number(value ?? 0));

const timeSince = (dateStr) => {
    const diff = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000);
    if (diff < 60) return `${diff}s`;
    if (diff < 3600) return `${Math.floor(diff / 60)}m`;
    return `${Math.floor(diff / 3600)}j`;
};
</script>

<template>
    <Head title="Kitchen Display" />

    <AppLayout title="Kitchen Display" :menu-items="menuItems">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Kitchen Display</h2>
                <p class="text-sm text-gray-500">{{ orderStore.activeOrders.length }} order aktif · Realtime</p>
            </div>
            <div class="flex items-center gap-2 rounded-xl bg-green-50 px-3 py-2 text-xs font-medium text-green-700">
                <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                Live
            </div>
        </div>

        <div v-if="orderStore.activeOrders.length === 0" class="rounded-2xl border border-dashed border-gray-300 bg-white py-16 text-center text-gray-500">
            <p class="text-lg font-medium">Tidak ada order aktif</p>
            <p class="mt-1 text-sm">Order baru akan muncul otomatis di sini.</p>
        </div>

        <div v-else class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="order in orderStore.activeOrders"
                :key="order.id"
                class="rounded-xl border bg-white p-5 shadow-sm transition"
                :class="{
                    'border-purple-200': order.status === 'accepted',
                    'border-blue-200': order.status === 'processing',
                    'border-green-200': order.status === 'ready',
                }"
            >
                <div class="mb-3 flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400">{{ order.order_number }}</p>
                        <h3 class="text-lg font-bold text-slate-800">{{ order.table?.name || 'Kasir' }}</h3>
                        <p v-if="order.customer_name" class="text-xs text-gray-500">{{ order.customer_name }}</p>
                    </div>
                    <div class="text-right">
                        <span
                            class="rounded-full px-3 py-1 text-xs font-semibold"
                            :class="statusConfig[order.status]?.class || 'bg-gray-100 text-gray-700'"
                        >
                            {{ statusConfig[order.status]?.label || order.status }}
                        </span>
                        <p class="mt-1 text-xs text-gray-400">{{ timeSince(order.created_at) }} lalu</p>
                    </div>
                </div>

                <div class="mb-3 space-y-1.5 rounded-xl bg-slate-50 p-3">
                    <div
                        v-for="item in (order.kitchen_items || order.items)"
                        :key="item.id"
                        class="flex items-center justify-between text-sm"
                    >
                        <span class="font-medium text-slate-800">{{ item.qty }}x {{ item.product_name }}</span>
                        <span v-if="item.variant_name" class="text-xs text-gray-500">{{ item.variant_name }}</span>
                    </div>
                    <div v-if="order.items?.some(i => i.note)" class="mt-2 border-t border-gray-200 pt-2">
                        <p v-for="item in order.items?.filter(i => i.note)" :key="item.id" class="text-xs text-amber-700">
                            Note ({{ item.product_name }}): {{ item.note }}
                        </p>
                    </div>
                </div>

                <div v-if="statusConfig[order.status]" class="flex gap-2">
                    <button
                        type="button"
                        class="flex-1 rounded-xl py-2 text-sm font-semibold text-white transition"
                        :class="{
                            'bg-blue-500 hover:bg-blue-600': order.status === 'accepted',
                            'bg-green-500 hover:bg-green-600': order.status === 'processing',
                            'bg-gray-800 hover:bg-gray-900': order.status === 'ready',
                        }"
                        @click="updateStatus(order, statusConfig[order.status].next)"
                    >
                        {{ statusConfig[order.status].nextLabel }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
