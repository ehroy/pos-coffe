<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useCashierMenu } from '@/Composables/useMenuItems';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    order: Object,
});

const menuItems = useCashierMenu();
const page = usePage();
const pricing = computed(() => page.props.pricing ?? {});
const taxLabel = computed(() => pricing.value.tax_enabled ? `Tax (${pricing.value.tax_rate}%)` : 'Tax');
const serviceChargeLabel = computed(() => pricing.value.service_charge_enabled ? `Service Charge (${pricing.value.service_charge_rate}%)` : 'Service Charge');

const approveForm = useForm({});
const rejectForm = useForm({});
const confirmPaymentForm = useForm({});

const approveOrder = () => {
    approveForm.patch(route('cashier.orders.approve', props.order.id), {
        preserveScroll: true,
    });
};

const rejectOrder = () => {
    rejectForm.patch(route('cashier.orders.reject', props.order.id), {
        preserveScroll: true,
    });
};

const confirmOnlinePayment = () => {
    confirmPaymentForm.post(route('cashier.orders.confirm-payment', props.order.id), {
        preserveScroll: true,
    });
};

const currency = (value) => new Intl.NumberFormat('id-ID').format(Number(value ?? 0));

const stages = [
    { key: 'pending', label: 'Pending' },
    { key: 'accepted', label: 'Accepted' },
    { key: 'processing', label: 'Processing' },
    { key: 'ready', label: 'Ready' },
    { key: 'completed', label: 'Completed' },
];

const stageIndex = () => stages.findIndex((stage) => stage.key === props.order.status);

const logLabel = (status) => {
    const labels = {
        pending: 'Pending',
        accepted: 'Accepted',
        processing: 'Processing',
        ready: 'Ready',
        completed: 'Completed',
        cancelled: 'Cancelled',
    };

    return labels[status] || status;
};

const logAuthor = (log) => log.changer?.name || 'System';
</script>

<template>
    <Head :title="`Order ${order.order_number}`" />

    <AppLayout title="Order Detail" :menu-items="menuItems">
        <div class="flex flex-col gap-4 rounded-xl bg-white p-6 shadow-sm lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm text-gray-500">Order {{ order.order_number }}</p>
                <h1 class="text-2xl font-bold text-slate-800">{{ order.source === 'qr_table' ? 'Table Order' : 'Cashier Order' }}</h1>
                <p class="mt-1 text-gray-600">{{ order.table ? `Table ${order.table.code}` : 'No table' }} · {{ order.payment_method || '-' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-700">{{ order.status }}</span>
                <span class="rounded-full bg-brand/10 px-3 py-1 text-sm text-brand-dark">{{ order.payment_status }}</span>
                <span class="rounded-full bg-blue-100 px-3 py-1 text-sm text-blue-700">{{ order.source }}</span>
            </div>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1.4fr_0.9fr]">
            <section class="rounded-xl bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-800">Items</h2>
                    <span class="text-sm text-gray-500">{{ order.items.length }} item</span>
                </div>

                <div class="space-y-3">
                    <div v-for="item in order.items" :key="item.id" class="flex items-start justify-between rounded-xl border border-gray-200 p-4">
                        <div>
                            <p class="font-medium text-slate-800">{{ item.product_name }}</p>
                            <p class="text-sm text-gray-500">{{ item.variant_name || 'Default' }} · Qty {{ item.qty }}</p>
                            <p v-if="item.note" class="mt-1 text-sm text-gray-600">Note: {{ item.note }}</p>
                        </div>
                        <p class="font-semibold text-slate-800">Rp {{ currency(item.subtotal) }}</p>
                    </div>
                </div>
            </section>

            <aside class="rounded-xl bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-slate-800">Summary</h2>
                <div class="mt-4 rounded-xl border border-gray-200 p-4">
                    <p class="text-sm font-semibold text-slate-800">Status Timeline</p>
                    <div class="mt-3 space-y-2">
                        <div
                            v-for="(stage, index) in stages"
                            :key="stage.key"
                            class="flex items-center gap-3 text-sm"
                        >
                            <div
                                class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold"
                                :class="index <= stageIndex() ? 'bg-brand text-white' : 'bg-gray-200 text-gray-500'"
                            >
                                {{ index + 1 }}
                            </div>
                            <span :class="index <= stageIndex() ? 'text-slate-800 font-medium' : 'text-gray-500'">{{ stage.label }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 rounded-xl border border-gray-200 p-4">
                    <p class="text-sm font-semibold text-slate-800">Status History</p>
                    <div class="mt-3 space-y-3">
                        <div v-for="log in order.status_logs || []" :key="log.id" class="flex gap-3">
                            <div class="mt-1 h-2.5 w-2.5 rounded-full bg-brand"></div>
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ logLabel(log.status) }}</p>
                                <p class="text-xs text-gray-500">{{ log.note || '-' }}</p>
                                <p class="text-xs text-gray-400">By {{ logAuthor(log) }}</p>
                            </div>
                        </div>
                        <div v-if="(order.status_logs || []).length === 0" class="text-sm text-gray-500">
                            Belum ada riwayat status.
                        </div>
                    </div>
                </div>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>Rp {{ currency(order.subtotal) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Discount</span><span>Rp {{ currency(order.discount) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">{{ taxLabel }}</span><span>Rp {{ currency(order.tax) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">{{ serviceChargeLabel }}</span><span>Rp {{ currency(order.service_charge || 0) }}</span></div>
                    <div class="flex justify-between border-t border-gray-200 pt-3 text-base font-bold"><span>Total</span><span>Rp {{ currency(order.total) }}</span></div>
                </div>

                <div class="mt-6 rounded-xl bg-amber-50 p-4 text-amber-900">
                    <p class="font-semibold">Approval</p>
                    <p class="text-sm">Order meja harus di-approve oleh cashier/admin sebelum diproses lebih lanjut.</p>
                </div>

                <div v-if="order.source === 'qr_table' && order.status === 'pending'" class="mt-4 grid gap-3">
                    <button
                        v-if="order.payment_method === 'qris'"
                        type="button"
                        class="w-full rounded-xl bg-emerald-600 px-4 py-3 font-semibold text-white hover:bg-emerald-700"
                        :disabled="confirmPaymentForm.processing"
                        @click="confirmOnlinePayment"
                    >
                        Konfirmasi Payment QRIS
                    </button>
                    <button
                        v-if="order.payment_method !== 'qris'"
                        type="button"
                        class="w-full rounded-xl bg-brand px-4 py-3 font-semibold text-white hover:bg-brand-dark"
                        :disabled="approveForm.processing"
                        @click="approveOrder"
                    >
                        Approve Order
                    </button>
                    <button
                        type="button"
                        class="w-full rounded-xl border border-red-200 bg-red-50 px-4 py-3 font-semibold text-red-700 hover:bg-red-100"
                        :disabled="rejectForm.processing"
                        @click="rejectOrder"
                    >
                        Reject Order
                    </button>
                </div>

                <div v-else-if="order.payment_status !== 'paid' && order.status !== 'cancelled'" class="mt-4 space-y-2">
                    <Link
                        :href="route('cashier.orders.payment', order.id)"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand px-4 py-3 font-semibold text-white hover:bg-brand-dark"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Proses Pembayaran
                    </Link>
                </div>

                <div v-else-if="order.payment_status === 'paid'" class="mt-4 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Order sudah dibayar
                </div>

                <div v-else class="mt-4 rounded-xl border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-500">
                    Order dibatalkan.
                </div>

                <Link :href="route('cashier.orders')" class="mt-3 inline-flex w-full justify-center rounded-xl border border-gray-300 px-4 py-3 text-gray-700 hover:bg-gray-50">
                    Back to Orders
                </Link>
            </aside>
        </div>
    </AppLayout>
</template>
