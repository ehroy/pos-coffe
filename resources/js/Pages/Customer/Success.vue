<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    order: Object,
});

const liveOrder = ref({ ...props.order });
const page = usePage();
const pricing = computed(() => page.props.pricing ?? {});
let timer = null;

const paymentMethod = props.order.payment?.method ?? props.order.payment_method ?? null;
const paymentLabelMap = { cash: 'Tunai', qris: 'QRIS', cashier: 'Bayar di Kasir' };
const paymentLabel = paymentLabelMap[paymentMethod] ?? 'Bayar di Kasir';
const isOnlinePayment = paymentMethod === 'qris';
const taxLabel = computed(() => pricing.value.tax_enabled ? `Pajak (${pricing.value.tax_rate}%)` : 'Pajak');
const serviceChargeLabel = computed(() => pricing.value.service_charge_enabled ? `Service Charge (${pricing.value.service_charge_rate}%)` : 'Service Charge');

const statusBadgeClass = (status) => ({
    pending:    'bg-yellow-100 text-yellow-800',
    accepted:   'bg-blue-100 text-blue-800',
    processing: 'bg-orange-100 text-orange-800',
    ready:      'bg-purple-100 text-purple-800',
    completed:  'bg-green-100 text-green-800',
    cancelled:  'bg-red-100 text-red-800',
}[status] ?? 'bg-gray-100 text-gray-700');

const refreshStatus = async () => {
    try {
        const response = await fetch(route('customer.orders.status', props.order.id));
        if (!response.ok) return;
        const data = await response.json();
        liveOrder.value = { ...liveOrder.value, ...data };
    } catch {
        // keep last known state
    }
};

onMounted(() => {
    timer = window.setInterval(refreshStatus, 5000);
    refreshStatus();
});

onBeforeUnmount(() => {
    if (timer) window.clearInterval(timer);
});
</script>

<template>
    <Head :title="`Order ${order.order_number}`" />

    <div class="min-h-screen bg-slate-50 px-4 py-8 text-slate-800 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl rounded-2xl bg-white p-6 shadow-xl">
            <div class="rounded-xl bg-coffee-900 p-6 text-white">
                <p class="text-sm text-coffee-300">Order Diterima</p>
                <h1 class="text-3xl font-bold">{{ order.order_number }}</h1>
                <p class="mt-1 text-coffee-200">
                    {{ order.table ? `Meja ${order.table.code}` : 'Walk-in' }}
                </p>
            </div>

            <div class="mt-6 space-y-4">
                <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                    <span class="text-gray-500">Status Order</span>
                    <span
                        class="rounded-full px-3 py-1 text-sm font-semibold"
                        :class="statusBadgeClass(liveOrder.status)"
                    >
                        {{ liveOrder.status }}
                    </span>
                </div>

                <div class="rounded-xl border border-gray-200 p-4">
                    <h2 class="font-semibold text-slate-800">Item Pesanan</h2>
                    <div class="mt-3 space-y-2">
                        <div
                            v-for="item in order.items"
                            :key="item.id"
                            class="flex justify-between text-sm"
                        >
                            <span>
                                {{ item.qty }}x {{ item.product_name }}
                                <span v-if="item.variant_name" class="text-gray-500">{{ item.variant_name }}</span>
                            </span>
                            <span>Rp {{ Number(item.subtotal).toLocaleString('id-ID') }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 p-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Pembayaran</span>
                        <span class="font-medium">{{ paymentLabel }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span>Rp {{ Number(order.subtotal).toLocaleString('id-ID') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ taxLabel }}</span>
                        <span>Rp {{ Number(order.tax).toLocaleString('id-ID') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ serviceChargeLabel }}</span>
                        <span>Rp {{ Number(order.service_charge || 0).toLocaleString('id-ID') }}</span>
                    </div>
                    <div class="mt-2 flex justify-between text-base font-bold">
                        <span>Total</span>
                        <span>Rp {{ Number(order.total).toLocaleString('id-ID') }}</span>
                    </div>
                </div>

                <div
                    class="rounded-xl p-4"
                    :class="liveOrder.status === 'completed' ? 'bg-green-50 text-green-800' : 'bg-blue-50 text-blue-800'"
                >
                    <p class="font-semibold">
                        {{ liveOrder.status === 'completed' ? 'Pesanan selesai!' : 'Order sedang diproses.' }}
                    </p>
                    <p class="mt-1 text-sm">Status: <strong>{{ liveOrder.status }}</strong></p>
                    <p v-if="isOnlinePayment && liveOrder.payment_status !== 'paid'" class="mt-2 text-sm text-amber-700">
                        Menunggu konfirmasi pembayaran online.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a
                        v-if="order.table?.qr_token"
                        :href="`/table/${order.table.qr_token}`"
                        class="rounded-xl bg-brand px-4 py-3 font-semibold text-white hover:bg-brand-dark"
                    >
                        Kembali ke Menu
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
