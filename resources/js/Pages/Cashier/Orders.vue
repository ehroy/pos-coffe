<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useCashierMenu } from '@/Composables/useMenuItems';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    orders: Array,
    filters: Object,
});

const liveOrders = ref([...(props.orders || [])]);

const filterTabs = [
    { label: 'Semua', value: 'all' },
    { label: 'Order Meja', value: 'qr_table' },
    { label: 'Order Kasir', value: 'cashier' },
    { label: 'QRIS Pending', value: 'qris_pending' },
];

const filterHref = (value) => {
    if (value === 'all') return route('cashier.orders');
    if (value === 'qris_pending') return route('cashier.orders', { queue: value });
    return route('cashier.orders', { source: value });
};

const sourceLabel = (order) => (order.source === 'qr_table' ? 'Order Meja' : 'Order Kasir');
const sourceClass = (order) => order.source === 'qr_table' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700';

const approvalLabel = (order) => {
    if (order.source !== 'qr_table') return null;
    if (order.status === 'pending') return 'Perlu Approval';
    if (order.status === 'accepted') return 'Sudah Approved';
    if (order.status === 'cancelled') return 'Ditolak';
    return 'Diproses';
};

const queueLabel = (order) => {
    if (order.source === 'qr_table' && order.payment_method === 'qris' && order.payment_status === 'unpaid' && order.status === 'pending') {
        return 'QRIS Pending';
    }
    return null;
};

const menuItems = useCashierMenu();

const confirmPaymentForm = useForm({});
const confirmingOrderId = ref(null);
const newOrderAlert = ref(null);

const confirmOnlinePayment = (order) => {
    confirmingOrderId.value = order.id;
    confirmPaymentForm.post(route('cashier.orders.confirm-payment', order.id), {
        preserveScroll: true,
        onFinish: () => { confirmingOrderId.value = null; },
    });
};

let echoChannel = null;

onMounted(() => {
    if (window.Echo) {
        echoChannel = window.Echo.channel('cashier');

        echoChannel.listen('.OrderCreated', (data) => {
            const exists = liveOrders.value.find((o) => o.id === data.order_id);
            if (!exists) {
                newOrderAlert.value = `Order baru masuk: ${data.order_number}`;
                setTimeout(() => { newOrderAlert.value = null; }, 5000);
            }
        });

        echoChannel.listen('.OrderUpdated', (data) => {
            const index = liveOrders.value.findIndex((o) => o.id === data.order_id);
            if (index !== -1) {
                liveOrders.value[index] = { ...liveOrders.value[index], status: data.status, payment_status: data.payment_status };
            }
        });

        echoChannel.listen('.PaymentCompleted', (data) => {
            const order = liveOrders.value.find((o) => o.id === data.order_id);
            if (order) {
                order.payment_status = 'paid';
            }
        });
    }
});

onUnmounted(() => {
    if (echoChannel) window.Echo?.leave('cashier');
});
</script>

<template>
    <Head title="Orders" />

    <AppLayout title="Orders" :menu-items="menuItems">
        <div v-if="newOrderAlert" class="mb-4 flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-800">
            <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            {{ newOrderAlert }}
        </div>

        <div v-if="$page.props.flash?.success" class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            {{ $page.props.flash.success }}
        </div>

        <div class="flex flex-col gap-3 rounded-xl bg-white p-6 shadow-sm md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Orders</h2>
                <p class="text-gray-500">Daftar order cashier yang akan diproses.</p>
            </div>
            <Link :href="route('cashier.pos')" class="rounded-lg bg-brand px-4 py-2 text-white hover:bg-brand-dark">Buka POS</Link>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <Link
                v-for="tab in filterTabs"
                :key="tab.value"
                :href="filterHref(tab.value)"
                class="rounded-full px-4 py-2 text-sm font-semibold transition"
                :class="(tab.value === 'qris_pending' ? filters?.queue === 'qris_pending' : filters?.source === tab.value) || (!filters?.source && !filters?.queue && tab.value === 'all') ? 'bg-brand text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
            >
                {{ tab.label }}
            </Link>
        </div>

        <div class="mt-6 overflow-hidden rounded-xl bg-white shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-slate-800">Recent Orders</h3>
            </div>

            <div v-if="liveOrders.length === 0" class="p-10 text-center text-gray-500">
                Belum ada order.
            </div>

            <div v-else class="divide-y divide-gray-200">
                <div
                    v-for="order in liveOrders"
                    :key="order.id"
                    class="flex flex-col gap-3 border-l-4 px-6 py-4 md:flex-row md:items-center md:justify-between"
                    :class="order.source === 'qr_table' && order.status === 'pending' ? 'border-amber-500 bg-amber-50/40' : 'border-transparent'"
                >
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-slate-800">{{ order.order_number }}</p>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold" :class="sourceClass(order)">
                                {{ sourceLabel(order) }}
                            </span>
                            <span v-if="approvalLabel(order)" class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-200">
                                {{ approvalLabel(order) }}
                            </span>
                            <span v-if="queueLabel(order)" class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200">
                                {{ queueLabel(order) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500">
                            {{ order.items?.length }} item · Rp {{ Number(order.total).toLocaleString('id-ID') }} · {{ order.source === 'qr_table' ? `Table ${order.table?.code || '-'}` : 'Cashier' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span class="rounded-full px-3 py-1 font-semibold" :class="order.payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            {{ order.payment_status }}
                        </span>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-gray-700">{{ order.status }}</span>
                        <span class="rounded-full bg-brand/10 px-3 py-1 text-brand-dark">{{ order.payment?.method || '-' }}</span>
                        <button
                            v-if="order.source === 'qr_table' && order.payment_method === 'qris' && order.payment_status !== 'paid'"
                            type="button"
                            class="rounded-full bg-emerald-600 px-3 py-1 text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="confirmPaymentForm.processing && confirmingOrderId === order.id"
                            @click="confirmOnlinePayment(order)"
                        >
                            {{ confirmPaymentForm.processing && confirmingOrderId === order.id ? 'Memproses...' : 'Konfirmasi QRIS' }}
                        </button>
                        <Link :href="route('cashier.orders.show', order.id)" class="rounded-full bg-blue-100 px-3 py-1 text-blue-800 hover:bg-blue-200">Detail</Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
