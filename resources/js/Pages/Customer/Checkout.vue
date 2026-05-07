<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/useCartStore';
import { computed, onMounted, ref, watch } from 'vue';

const props = defineProps({
    table: Object,
    qrToken: String,
    onlinePaymentEnabled: {
        type: Boolean,
        default: true,
    },
});

const cart = useCartStore();
const page = usePage();
const pricing = computed(() => page.props.pricing ?? {});
const taxLabel = computed(() => pricing.value.tax_enabled ? `Pajak (${pricing.value.tax_rate}%)` : 'Pajak');
const serviceChargeLabel = computed(() => pricing.value.service_charge_enabled ? `Service Charge (${pricing.value.service_charge_rate}%)` : 'Service Charge');

onMounted(() => {
    cart.setTable(props.qrToken, props.table?.id, props.table?.name);
});

const customerName = ref('');
const note = ref('');
const paymentMethod = ref('cashier');
const isSubmitting = ref(false);
const errorMessage = ref('');

const formatCurrency = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));

const isEmpty = computed(() => cart.items.length === 0);
const paymentMethods = computed(() => (props.onlinePaymentEnabled ? ['cashier', 'qris'] : ['cashier']));

watch(paymentMethods, (methods) => {
    if (!methods.includes(paymentMethod.value)) {
        paymentMethod.value = methods[0] || 'cashier';
    }
}, { immediate: true });

const handleCheckout = async () => {
    if (isEmpty.value) {
        errorMessage.value = 'Keranjang masih kosong.';
        return;
    }

    isSubmitting.value = true;
    errorMessage.value = '';

    try {
        const order = await cart.checkout(customerName.value, note.value, paymentMethod.value);
        window.location.href = `/customer/orders/${order.id}`;
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Terjadi kesalahan. Silakan coba lagi.';
        isSubmitting.value = false;
    }
};
</script>

<template>
    <Head :title="`Checkout - ${table?.name || 'Meja'}`" />

    <div class="min-h-screen bg-slate-50 text-slate-800">
        <div class="mx-auto max-w-2xl px-4 py-6">
            <div class="mb-6 flex items-center gap-3">
                <a :href="`/table/${qrToken}/cart`" class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm text-brand">
                    &#8592;
                </a>
                <div>
                    <h1 class="text-xl font-bold">Checkout</h1>
                    <p class="text-sm text-gray-500">{{ table?.name }}</p>
                </div>
            </div>

            <div v-if="isEmpty" class="rounded-2xl border border-dashed border-[#d8c7b3] bg-white py-16 text-center text-gray-500">
                <p class="text-lg font-medium">Keranjang kosong</p>
                <p class="mt-1 text-sm">Kembali ke menu untuk memilih pesanan.</p>
                <a :href="`/table/${qrToken}`" class="mt-4 inline-block rounded-xl bg-brand px-6 py-3 text-sm font-semibold text-white hover:bg-brand-dark">
                    Lihat Menu
                </a>
            </div>

            <div v-else>
                <div v-if="errorMessage" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ errorMessage }}
                </div>

                <div class="space-y-4">
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <h2 class="mb-3 font-semibold text-slate-800">Informasi Tamu</h2>
                        <input
                            v-model="customerName"
                            type="text"
                            placeholder="Nama tamu (opsional)"
                            class="w-full rounded-xl border border-[#e7ddcf] bg-slate-50 px-4 py-3 outline-none transition focus:border-brand"
                        />
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <h2 class="mb-3 font-semibold text-slate-800">Catatan Pesanan</h2>
                        <textarea
                            v-model="note"
                            rows="3"
                            placeholder="Catatan untuk dapur (opsional)"
                            class="w-full rounded-xl border border-[#e7ddcf] bg-slate-50 px-4 py-3 outline-none transition focus:border-brand"
                        ></textarea>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <h2 class="mb-3 font-semibold text-slate-800">Metode Pembayaran</h2>
                        <div class="space-y-2">
                            <button
                                v-for="method in paymentMethods"
                                :key="method"
                                type="button"
                                class="w-full rounded-xl border p-4 text-left transition"
                                :class="paymentMethod === method ? 'border-brand bg-brand/10' : 'border-gray-200 hover:border-brand/40'"
                                @click="paymentMethod = method"
                            >
                                <div class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-slate-100">
                                        <svg v-if="method === 'cashier'" class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        <svg v-else class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    </span>
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ method === 'cashier' ? 'Bayar di Kasir' : 'QRIS' }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ method === 'cashier' ? 'Pesanan dikirim dulu, bayar saat di kasir.' : 'Scan QR untuk pembayaran.' }}
                                        </p>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <h2 class="mb-3 font-semibold text-slate-800">Ringkasan Pesanan</h2>
                        <div class="space-y-2">
                            <div
                                v-for="item in cart.items"
                                :key="item.key"
                                class="flex justify-between text-sm"
                            >
                                <span>{{ item.qty }}x {{ item.name }} <span v-if="item.variant_name" class="text-gray-500">{{ item.variant_name }}</span></span>
                                <span>Rp {{ formatCurrency(item.price * item.qty) }}</span>
                            </div>
                        </div>

                        <div class="mt-4 space-y-2 border-t border-gray-100 pt-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Subtotal</span>
                                <span>Rp {{ formatCurrency(cart.subtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">{{ taxLabel }}</span>
                                <span>Rp {{ formatCurrency(cart.tax) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">{{ serviceChargeLabel }}</span>
                                <span>Rp {{ formatCurrency(cart.serviceCharge) }}</span>
                            </div>
                            <div class="flex justify-between text-base font-bold text-slate-800">
                                <span>Total</span>
                                <span>Rp {{ formatCurrency(cart.total) }}</span>
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        class="w-full rounded-xl bg-brand px-4 py-3 font-semibold text-white hover:bg-brand-dark disabled:opacity-50"
                        :disabled="isSubmitting"
                        @click="handleCheckout"
                    >
                        {{ isSubmitting ? 'Mengirim...' : 'Kirim Pesanan' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
