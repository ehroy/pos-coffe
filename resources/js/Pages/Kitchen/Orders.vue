<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useKitchenMenu } from '@/Composables/useMenuItems';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    orders: Array,
});

const liveOrders = ref([...(props.orders || [])]);

const menuItems = useKitchenMenu();

const updateStatus = (order, status) => {
    router.patch(route('kitchen.orders.update', order.id), { status }, {
        preserveScroll: true,
        onSuccess: () => {
            const index = liveOrders.value.findIndex((o) => o.id === order.id);
            if (index !== -1) {
                liveOrders.value[index].status = status;
                if (['completed', 'cancelled'].includes(status)) {
                    liveOrders.value.splice(index, 1);
                }
            }
        },
    });
};

const currency = (value) => new Intl.NumberFormat('id-ID').format(Number(value ?? 0));

let echoChannel = null;

onMounted(() => {
    if (window.Echo) {
        echoChannel = window.Echo.channel('kitchen');

        echoChannel.listen('.OrderCreated', (data) => {
            if (['accepted', 'processing', 'ready'].includes(data.status)) {
                const exists = liveOrders.value.find((o) => o.id === data.order_id);
                if (!exists) {
                    liveOrders.value.unshift({
                        id: data.order_id,
                        order_number: data.order_number,
                        source: data.source,
                        status: data.status,
                        payment_status: data.payment_status,
                        table_id: data.table_id,
                        items: [],
                        table: null,
                    });
                }
            }
        });

        echoChannel.listen('.OrderUpdated', (data) => {
            const index = liveOrders.value.findIndex((o) => o.id === data.order_id);
            if (index !== -1) {
                liveOrders.value[index].status = data.status;
                if (['completed', 'cancelled'].includes(data.status)) {
                    liveOrders.value.splice(index, 1);
                }
            }
        });
    }
});

onUnmounted(() => {
    if (echoChannel) window.Echo?.leave('kitchen');
});
</script>

<template>
    <Head title="Kitchen Orders" />

    <AppLayout title="Kitchen Orders" :menu-items="menuItems">
        <div v-if="$page.props.flash?.success" class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            {{ $page.props.flash.success }}
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Active Orders</h2>
                    <p class="mt-1 text-gray-500">{{ liveOrders.length }} order aktif · Realtime</p>
                </div>
                <div class="flex items-center gap-2 rounded-xl bg-green-50 px-3 py-2 text-xs font-medium text-green-700">
                    <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                    Live
                </div>
            </div>
        </div>

        <div v-if="liveOrders.length === 0" class="mt-6 rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-500">
            Tidak ada order aktif.
        </div>

        <div v-else class="mt-6 grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
            <div v-for="order in liveOrders" :key="order.id" class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500">{{ order.order_number }}</p>
                        <h3 class="text-lg font-semibold text-slate-800">{{ order.table?.name || order.source }}</h3>
                        <p class="text-xs text-gray-500">{{ order.payment_method || '-' }} · {{ order.payment_status }}</p>
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="order.status === 'accepted' ? 'bg-purple-100 text-purple-800' : order.status === 'processing' ? 'bg-blue-100 text-blue-800' : order.status === 'ready' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'">{{ order.status }}</span>
                </div>

                <div class="mt-3 rounded-xl bg-gray-50 p-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Customer</span><span>{{ order.customer_name || '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Kitchen Items</span><span>{{ (order.kitchen_items || order.items)?.length || 0 }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Total</span><span>Rp {{ currency(order.total) }}</span></div>
                </div>

                <div v-if="order.items?.length" class="mt-4 space-y-2 text-sm">
                    <div v-for="item in (order.kitchen_items || order.items)" :key="item.id" class="flex justify-between">
                        <span>{{ item.qty }}x {{ item.product_name }}</span>
                        <span class="text-gray-500">{{ item.variant_name }}</span>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button class="rounded-lg bg-blue-500 px-3 py-2 text-sm text-white hover:bg-blue-600" @click="updateStatus(order, 'processing')">Processing</button>
                    <button class="rounded-lg bg-green-500 px-3 py-2 text-sm text-white hover:bg-green-600" @click="updateStatus(order, 'ready')">Ready</button>
                    <button class="rounded-lg bg-gray-800 px-3 py-2 text-sm text-white hover:bg-gray-900" @click="updateStatus(order, 'completed')">Completed</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
