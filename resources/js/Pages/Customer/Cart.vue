<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/useCartStore';
import { computed, onMounted } from 'vue';

const props = defineProps({
    table: Object,
    qrToken: String,
});

const cart = useCartStore();
const page = usePage();
const pricing = computed(() => page.props.pricing ?? {});
const taxLabel = computed(() => pricing.value.tax_enabled ? `Pajak (${pricing.value.tax_rate}%)` : 'Pajak');
const serviceChargeLabel = computed(() => pricing.value.service_charge_enabled ? `Service Charge (${pricing.value.service_charge_rate}%)` : 'Service Charge');

onMounted(() => {
    cart.setTable(props.qrToken, props.table?.id, props.table?.name);
});

const formatCurrency = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));

const isEmpty = computed(() => cart.items.length === 0);
</script>

<template>
    <Head :title="`Keranjang - ${table?.name || 'Meja'}`" />

    <div class="min-h-screen bg-slate-50 text-slate-800">
        <div class="mx-auto max-w-2xl px-4 py-6">
            <div class="mb-6 flex items-center gap-3">
                <a :href="`/table/${qrToken}`" class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm text-brand">
                    &#8592;
                </a>
                <div>
                    <h1 class="text-xl font-bold">Keranjang Pesanan</h1>
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
                <div class="space-y-3">
                    <div
                        v-for="item in cart.items"
                        :key="item.key"
                        class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-800">{{ item.name }}</p>
                                <p v-if="item.variant_name" class="text-xs text-gray-500">{{ item.variant_name }}</p>
                                <p class="mt-1 text-sm text-brand">Rp {{ formatCurrency(item.price) }}</p>
                            </div>
                            <button
                                type="button"
                                class="text-xs text-red-500 hover:text-red-700"
                                @click="cart.removeItem(item.key)"
                            >
                                Hapus
                            </button>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    class="flex h-8 w-8 items-center justify-center rounded-xl border border-gray-300 text-lg font-bold hover:bg-gray-50"
                                    @click="cart.updateQty(item.key, item.qty - 1)"
                                >
                                    -
                                </button>
                                <span class="w-8 text-center font-semibold">{{ item.qty }}</span>
                                <button
                                    type="button"
                                    class="flex h-8 w-8 items-center justify-center rounded-xl border border-gray-300 text-lg font-bold hover:bg-gray-50"
                                    @click="cart.updateQty(item.key, item.qty + 1)"
                                >
                                    +
                                </button>
                            </div>
                            <p class="font-semibold text-slate-800">Rp {{ formatCurrency(item.price * item.qty) }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 rounded-xl border border-slate-200 bg-white p-4">
                    <div class="space-y-2 text-sm">
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
                        <div class="flex justify-between border-t border-gray-100 pt-2 text-base font-bold text-slate-800">
                            <span>Total</span>
                            <span>Rp {{ formatCurrency(cart.total) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex gap-3">
                    <a
                        :href="`/table/${qrToken}`"
                        class="flex-1 rounded-xl border border-gray-300 px-4 py-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50"
                    >
                        Tambah Menu
                    </a>
                    <a
                        :href="`/table/${qrToken}/checkout`"
                        class="flex-1 rounded-xl bg-brand px-4 py-3 text-center text-sm font-semibold text-white hover:bg-brand-dark"
                    >
                        Lanjut Checkout
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>
