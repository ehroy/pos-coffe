<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useCashierMenu } from '@/Composables/useMenuItems';
import PaymentModal from '@/Components/Cashier/PaymentModal.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    order: Object,
});

const menuItems = useCashierMenu();
const page = usePage();
const pricing = computed(() => page.props.pricing ?? {});
const taxLabel = computed(() => pricing.value.tax_enabled ? `Pajak (${pricing.value.tax_rate}%)` : 'Pajak');
const serviceChargeLabel = computed(() => pricing.value.service_charge_enabled ? `Service Charge (${pricing.value.service_charge_rate}%)` : 'Service Charge');

const paymentModalOpen = ref(false);
const isSubmitting = ref(false);
const errorMessage = ref('');
const paymentSuccess = ref(null);

const formatCurrency = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));

const canPay = computed(() => {
    if (!props.order) return false;
    return props.order.payment_status !== 'paid' && props.order.status !== 'cancelled';
});

const handlePaymentConfirm = (paymentData) => {
    isSubmitting.value = true;
    errorMessage.value = '';

    const payload = {
        method:        paymentData.payment_method,
        amount:        paymentData.payment_method === 'cash'
            ? (paymentData.cash_received ?? props.order.total)
            : props.order.total,
        cash_received: paymentData.cash_received ?? null,
    };

    router.post(route('cashier.orders.pay', props.order.id), payload, {
        preserveScroll: true,
        onSuccess: (page) => {
            paymentModalOpen.value = false;
            const flash = page.props.flash ?? {};
            paymentSuccess.value = {
                method: payload.method,
                amount: payload.amount,
                change: flash.change ?? Math.max(payload.amount - props.order.total, 0),
            };
            isSubmitting.value = false;
        },
        onError: (errors) => {
            errorMessage.value = Object.values(errors)[0] || 'Terjadi kesalahan.';
            isSubmitting.value = false;
        },
    });
};
</script>

<template>
    <Head :title="`Payment - ${order?.order_number || ''}`" />

    <AppLayout :title="`Payment ${order?.order_number}`" :menu-items="menuItems">
        <div class="mx-auto max-w-2xl">
            <div class="mb-6 flex items-center gap-3">
                <a :href="route('cashier.orders')" class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm text-brand">
                    &#8592;
                </a>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Pembayaran Order</h1>
                    <p class="text-sm text-gray-500">{{ order?.order_number }}</p>
                </div>
            </div>

            <div v-if="paymentSuccess" class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4">
                <p class="font-semibold text-green-800">Pembayaran Berhasil</p>
                <p class="text-sm text-green-700">Metode: {{ paymentSuccess.method.toUpperCase() }}</p>
                <p v-if="paymentSuccess.method === 'cash'" class="text-sm text-green-700">
                    Kembalian: Rp {{ formatCurrency(paymentSuccess.change) }}
                </p>
            </div>

            <div v-if="order" class="space-y-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="font-semibold text-slate-800">Detail Order</h2>
                        <span
                            class="rounded-full px-3 py-1 text-xs font-semibold"
                            :class="{
                                'bg-yellow-100 text-yellow-800': order.status === 'pending',
                                'bg-blue-100 text-blue-800': order.status === 'accepted',
                                'bg-orange-100 text-orange-800': order.status === 'processing',
                                'bg-purple-100 text-purple-800': order.status === 'ready',
                                'bg-green-100 text-green-800': order.status === 'completed',
                                'bg-red-100 text-red-800': order.status === 'cancelled',
                            }"
                        >
                            {{ order.status }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        <div v-for="item in order.items" :key="item.id" class="flex justify-between text-sm">
                            <span>{{ item.qty }}x {{ item.product_name }} <span v-if="item.variant_name" class="text-gray-500">{{ item.variant_name }}</span></span>
                            <span>Rp {{ formatCurrency(item.subtotal) }}</span>
                        </div>
                    </div>

                    <div class="mt-4 space-y-2 border-t border-gray-100 pt-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span>Rp {{ formatCurrency(order.subtotal) }}</span>
                        </div>
                        <div v-if="Number(order.discount) > 0" class="flex justify-between">
                            <span class="text-gray-500">Diskon</span>
                            <span class="text-green-600">- Rp {{ formatCurrency(order.discount) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ taxLabel }}</span>
                            <span>Rp {{ formatCurrency(order.tax) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">{{ serviceChargeLabel }}</span>
                            <span>Rp {{ formatCurrency(order.service_charge || 0) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-slate-800">
                            <span>Total</span>
                            <span>Rp {{ formatCurrency(order.total) }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Status Pembayaran</span>
                        <span
                            class="rounded-full px-3 py-1 text-xs font-semibold"
                            :class="{
                                'bg-yellow-100 text-yellow-800': order.payment_status === 'unpaid',
                                'bg-green-100 text-green-800': order.payment_status === 'paid',
                                'bg-red-100 text-red-800': order.payment_status === 'refunded',
                            }"
                        >
                            {{ order.payment_status }}
                        </span>
                    </div>
                    <div v-if="order.payment" class="mt-2 flex justify-between text-sm">
                        <span class="text-gray-500">Metode</span>
                        <span class="font-medium uppercase">{{ order.payment.method }}</span>
                    </div>
                </div>

                <button
                    v-if="canPay"
                    type="button"
                    class="w-full rounded-xl bg-brand px-4 py-3 font-semibold text-white hover:bg-brand-dark"
                    @click="paymentModalOpen = true"
                >
                    Proses Pembayaran
                </button>

                <div v-else-if="order.payment_status === 'paid'" class="rounded-xl bg-green-50 px-4 py-3 text-center text-sm font-semibold text-green-700">
                    Order sudah dibayar
                </div>
            </div>
        </div>

        <PaymentModal
            :is-open="paymentModalOpen"
            :total="Number(order?.total || 0)"
            :is-submitting="isSubmitting"
            :error-message="errorMessage"
            @confirm="handlePaymentConfirm"
            @close="paymentModalOpen = false"
        />
    </AppLayout>
</template>
