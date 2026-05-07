<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import ReceiptModal from '@/Components/Cashier/ReceiptModal.vue';
import CategoryPicker from '@/Components/UI/CategoryPicker.vue';
import BarcodeScanner from '@/Components/BarcodeScanner.vue';
import { useCashierMenu } from '@/Composables/useMenuItems';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { calculatePricing } from '@/Services/pricing';

const props = defineProps({
    products: Array,
    categories: Array,
});

const menuItems = useCashierMenu();

const search = ref('');
const selectedCategory = ref('');
const discount = ref(0);
const showBarcodeScanner = ref(false);

const onBarcodeDetected = (code) => {
    showBarcodeScanner.value = false;
    search.value = code;
    const product = props.products.find(p =>
        p.variants?.some(v => v.barcode === code || v.sku === code)
    );
    if (product) {
        const variant = product.variants?.find(v => v.barcode === code || v.sku === code);
        addToCart(product, variant ?? null);
        search.value = '';
    }
};
const cart = ref([]);
const checkoutMessage = ref('');
const page = usePage();
const paymentDialogOpen = ref(false);
const paymentMode = ref('manual');
const paymentMethod = ref('cash');
const cashReceived = ref('');
const paymentSuccessOpen = ref(false);
const paymentSuccessTitle = ref('');
const paymentSuccessDetail = ref('');
const lastReceipt = ref(null);

const formatCurrency = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));

const filteredProducts = computed(() => {
    const query = search.value.trim().toLowerCase();

    return props.products.filter((product) => {
        const matchesCategory = !selectedCategory.value || String(product.category_id) === String(selectedCategory.value);
        const matchesQuery = !query || [
            product.name,
            product.category?.name,
            ...(product.variants || []).map((variant) => variant.name),
        ].filter(Boolean).some((value) => String(value).toLowerCase().includes(query));

        return matchesCategory && matchesQuery;
    });
});

const subtotal = computed(() => cart.value.reduce((total, item) => total + item.price * item.qty, 0));
const discountValue = computed(() => Math.min(Number(discount.value || 0), subtotal.value));
const pricing = computed(() => calculatePricing(subtotal.value, discountValue.value, page.props.pricing ?? {}));
const tax = computed(() => pricing.value.tax);
const serviceCharge = computed(() => pricing.value.serviceCharge);
const total = computed(() => pricing.value.total);
const cartCount = computed(() => cart.value.reduce((total, item) => total + item.qty, 0));
const flashSuccess = computed(() => page.props.flash?.success || '');

const addToCart = (product, variant = null) => {
    const key = `${product.id}-${variant?.id || 'base'}`;
    const existing = cart.value.find((item) => item.key === key);
    const price = Number(variant?.price ?? product.base_price);

    if (existing) {
        existing.qty += 1;
        return;
    }

    cart.value.push({
        key,
        product_id: product.id,
        variant_id: variant?.id || null,
        name: product.name,
        variant_name: variant?.name || 'Default',
        price,
        qty: 1,
    });
};

const increaseQty = (item) => item.qty += 1;

const decreaseQty = (item) => {
    if (item.qty > 1) {
        item.qty -= 1;
        return;
    }

    cart.value = cart.value.filter((current) => current.key !== item.key);
};

const removeItem = (item) => {
    cart.value = cart.value.filter((current) => current.key !== item.key);
};

const clearCart = () => {
    cart.value = [];
    discount.value = 0;
};

const openPaymentDialog = () => {
    if (cart.value.length === 0) {
        checkoutMessage.value = 'Cart masih kosong.';
        return;
    }

    paymentMode.value = 'manual';
    paymentMethod.value = 'cash';
    cashReceived.value = '';
    paymentDialogOpen.value = true;
    checkoutMessage.value = '';
};

const closePaymentDialog = () => {
    paymentDialogOpen.value = false;
};

const closeSuccessDialog = () => {
    paymentSuccessOpen.value = false;
    paymentSuccessTitle.value = '';
    paymentSuccessDetail.value = '';
    clearCart();
    discount.value = 0;
    cashReceived.value = '';
    paymentMode.value = 'manual';
    paymentMethod.value = 'cash';
    lastReceipt.value = null;
};

const changeAmount = computed(() => {
    const received = Number(cashReceived.value || 0);
    return Math.max(received - total.value, 0);
});

const receivedDisplay = computed(() => `Rp ${formatCurrency(cashReceived.value || 0)}`);

watch(paymentMode, (value) => {
    if (value === 'automatic') {
        paymentMethod.value = 'qris';
    }
});

const confirmPayment = () => {
    if (paymentMode.value === 'manual' && paymentMethod.value === 'cash') {
        const received = Number(cashReceived.value || 0);

        if (received < total.value) {
            checkoutMessage.value = 'Uang cash kurang.';
            return;
        }

        submitOrder(received);
        return;
    }

    if (paymentMode.value === 'manual' && paymentMethod.value === 'qris') {
        submitOrder(total.value);
        return;
    }

    submitOrder(total.value);
};

const lastOrderId = ref(null);
const lastOrderNumber = ref('');

const submitOrder = (cashAmount) => {
    router.post(route('cashier.orders.store'), {
        items: cart.value.map((item) => ({
            product_id: item.product_id,
            variant_id: item.variant_id,
            product_name: item.name,
            variant_name: item.variant_name,
            qty: item.qty,
            price: item.price,
        })),
        payment_mode: paymentMode.value,
        payment_method: paymentMethod.value,
        cash_received: paymentMethod.value === 'cash' && paymentMode.value === 'manual' ? cashAmount : null,
        discount: discount.value,
    }, {
        preserveScroll: true,
        onSuccess: (page) => {
            paymentDialogOpen.value = false;
            lastReceipt.value = {
                total: total.value,
                change: changeAmount.value,
                amount: paymentMethod.value === 'cash' ? cashAmount : total.value,
                method: paymentMethod.value,
                mode: paymentMode.value,
                itemCount: cartCount.value,
            };
            lastOrderId.value = page.props.flash?.order_id ?? null;
            lastOrderNumber.value = page.props.flash?.order_number ?? '';
            paymentSuccessOpen.value = true;
            checkoutMessage.value = '';
        },
    });
};
</script>

<template>
    <Head title="POS" />

    <AppLayout title="POS" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-r from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Point of Sale</h2>
                    <p class="mt-1 text-coffee-200">Search produk, tambah ke cart, dan proses transaksi.</p>
                </div>
                <div class="grid grid-cols-3 gap-3 text-sm">
                    <div class="rounded-xl bg-white/10 px-4 py-3">
                        <p class="text-coffee-200">Products</p>
                        <p class="text-xl font-bold">{{ products.length }}</p>
                    </div>
                    <div class="rounded-xl bg-white/10 px-4 py-3">
                        <p class="text-coffee-200">Cart</p>
                        <p class="text-xl font-bold">{{ cartCount }}</p>
                    </div>
                    <div class="rounded-xl bg-white/10 px-4 py-3">
                        <p class="text-coffee-200">Total</p>
                        <p class="text-xl font-bold">Rp {{ formatCurrency(total) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="flashSuccess || checkoutMessage" class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
            {{ flashSuccess || checkoutMessage }}
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.6fr_0.9fr]">
            <section class="rounded-xl bg-white p-4 shadow-sm md:p-6">
                <div class="mb-4 space-y-3">
                    <div class="flex gap-2">
                        <input v-model="search" type="text" placeholder="Cari produk, kategori, variant, barcode..." class="flex-1 rounded-xl border border-gray-200 px-4 py-3 outline-none focus:border-brand focus:ring-1 focus:ring-brand/20" />
                        <button
                            type="button"
                            class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl border border-gray-200 text-slate-500 hover:border-brand hover:bg-brand/5 hover:text-brand transition-colors"
                            title="Scan Barcode"
                            @click="showBarcodeScanner = true"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </button>
                    </div>
                    <CategoryPicker
                        v-model="selectedCategory"
                        :categories="categories"
                        all-label="Semua"
                    />
                </div>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 xl:grid-cols-3 xl:gap-4">
                    <article v-for="product in filteredProducts" :key="product.id" class="overflow-hidden rounded-xl border border-gray-200 bg-white hover:border-coffee-400 hover:shadow-sm">
                        <div class="aspect-[4/3] bg-gray-100">
                            <img :src="product.image_url" :alt="product.name" class="h-full w-full object-cover" loading="lazy" />
                        </div>

                        <div class="p-3 sm:p-4">
                            <div class="mb-2 flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-800 sm:text-base">{{ product.name }}</h3>
                                    <p class="text-[11px] text-gray-500 sm:text-xs">{{ product.category?.name }}</p>
                                </div>
                                <span class="rounded-full bg-brand/10 px-2 py-1 text-[10px] font-semibold text-brand-dark sm:text-xs">Rp {{ formatCurrency(product.base_price) }}</span>
                            </div>

                            <p class="mb-3 line-clamp-2 text-xs text-gray-600 sm:text-sm">{{ product.description }}</p>

                            <div class="mb-4 flex flex-wrap gap-2">
                            <button v-if="!product.variants?.length" type="button" class="rounded-lg bg-brand px-3 py-2 text-xs text-white hover:bg-brand-dark sm:text-sm" @click="addToCart(product)">
                                Add to Cart
                            </button>
                            <template v-else>
                                <button
                                    v-for="variant in product.variants.slice(0, 3)"
                                    :key="variant.id"
                                    type="button"
                                    class="rounded-lg border border-brand/40 px-2.5 py-2 text-[11px] text-slate-800 hover:bg-brand/5 sm:px-3 sm:text-sm"
                                    @click="addToCart(product, variant)"
                                >
                                    {{ variant.name }} · Rp {{ formatCurrency(variant.price) }}
                                </button>
                            </template>
                        </div>
                        </div>
                    </article>
                </div>

                <div v-if="filteredProducts.length === 0" class="rounded-xl border border-dashed border-gray-300 py-10 text-center text-gray-500">
                    Tidak ada produk yang cocok.
                </div>
            </section>

            <aside class="rounded-xl bg-white p-4 shadow-sm md:p-6">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800">Cart</h3>
                        <p class="text-sm text-gray-500">{{ cartCount }} item</p>
                    </div>
                    <button class="text-sm text-red-600 hover:text-red-700" type="button" @click="clearCart">Clear</button>
                </div>

                <div v-if="cart.length === 0" class="rounded-xl border border-dashed border-gray-300 py-10 text-center text-gray-500">
                    Cart masih kosong.
                </div>

                <div v-else class="space-y-3">
                    <div v-for="item in cart" :key="item.key" class="rounded-xl border border-gray-200 p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-slate-800">{{ item.name }}</p>
                                <p class="text-xs text-gray-500">{{ item.variant_name }}</p>
                            </div>
                            <button type="button" class="text-sm text-red-600" @click="removeItem(item)">Remove</button>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <button type="button" class="h-8 w-8 rounded-lg border border-gray-300" @click="decreaseQty(item)">-</button>
                                <span class="w-8 text-center text-sm font-semibold">{{ item.qty }}</span>
                                <button type="button" class="h-8 w-8 rounded-lg border border-gray-300" @click="increaseQty(item)">+</button>
                            </div>
                            <p class="text-sm font-semibold text-slate-800">Rp {{ formatCurrency(item.price * item.qty) }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 space-y-3 border-t border-gray-200 pt-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium">Rp {{ formatCurrency(subtotal) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-gray-500">Discount</span>
                        <input v-model="discount" type="number" min="0" step="1" class="w-32 rounded-lg border border-gray-300 px-3 py-2 text-right" />
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">{{ page.props.pricing?.tax_enabled ? `Tax (${page.props.pricing.tax_rate}%)` : 'Tax' }}</span>
                        <span class="font-medium">Rp {{ formatCurrency(tax) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">{{ page.props.pricing?.service_charge_enabled ? `Service Charge (${page.props.pricing.service_charge_rate}%)` : 'Service Charge' }}</span>
                        <span class="font-medium">Rp {{ formatCurrency(serviceCharge) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-lg font-bold text-slate-800">
                        <span>Total</span>
                        <span>Rp {{ formatCurrency(total) }}</span>
                    </div>
                    <button type="button" class="w-full rounded-xl bg-brand px-4 py-3 font-semibold text-white hover:bg-brand-dark" @click="openPaymentDialog">
                        Process Payment
                    </button>
                </div>
            </aside>
        </div>

        <Modal :show="paymentDialogOpen" @close="closePaymentDialog">
            <div class="max-w-3xl overflow-hidden rounded-xl bg-white shadow-2xl">
                <div class="bg-gradient-to-r from-coffee-700 to-coffee-900 px-6 py-5 text-white">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-coffee-200">Cashier Payment</p>
                            <h3 class="mt-1 text-2xl font-bold">Finalize Payment</h3>
                            <p class="mt-1 text-sm text-coffee-200">Manual cash, manual QRIS, atau automatic gateway.</p>
                        </div>
                        <button type="button" class="rounded-full bg-white/10 px-3 py-2 text-sm font-medium text-white hover:bg-white/20" @click="closePaymentDialog">Close</button>
                    </div>
                </div>

                <div class="grid gap-0 lg:grid-cols-[1.05fr_0.95fr]">
                    <div class="space-y-5 p-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Payment Mode</label>
                                <select v-model="paymentMode" class="w-full rounded-xl border border-gray-300 px-4 py-3">
                                    <option value="manual">Manual</option>
                                    <option value="automatic">Automatic Gateway</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Payment Method</label>
                                <select v-model="paymentMethod" class="w-full rounded-xl border border-gray-300 px-4 py-3" :disabled="paymentMode === 'automatic'">
                                    <option value="cash">Cash</option>
                                    <option value="qris">QRIS</option>
                                </select>
                                <p v-if="paymentMode === 'automatic'" class="mt-1 text-xs text-amber-600">
                                    Automatic mode diarahkan ke gateway QRIS.
                                </p>
                            </div>
                        </div>

                        <div v-if="paymentMode === 'manual' && paymentMethod === 'cash'" class="rounded-xl border border-brand/30 bg-slate-50 p-4">
                            <div class="mb-3 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Cash Received</p>
                                    <p class="text-xs text-gray-500">Masukkan nominal uang yang diterima.</p>
                                </div>
                                <span class="rounded-full bg-brand/10 px-3 py-1 text-xs font-semibold text-brand-dark">Rp</span>
                            </div>

                            <div class="flex items-center overflow-hidden rounded-xl border border-gray-300 bg-white">
                                <span class="border-r border-gray-300 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-700">Rp</span>
                                <input v-model="cashReceived" type="number" min="0" step="1" class="w-full px-4 py-3 outline-none" placeholder="Masukkan nominal" />
                            </div>

                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-xl bg-white px-4 py-3 shadow-sm">
                                    <p class="text-xs uppercase tracking-wide text-gray-500">Diterima</p>
                                    <p class="mt-1 text-lg font-bold text-slate-800">{{ receivedDisplay }}</p>
                                </div>
                                <div class="rounded-xl bg-green-50 px-4 py-3 shadow-sm">
                                    <p class="text-xs uppercase tracking-wide text-green-700">Kembalian</p>
                                    <p class="mt-1 text-lg font-bold text-green-800">Rp {{ formatCurrency(changeAmount) }}</p>
                                </div>
                            </div>

                            <p class="mt-3 text-xs text-gray-500">Pastikan nominal benar sebelum klik confirm.</p>
                        </div>

                        <div v-else class="rounded-xl border border-dashed border-brand/30 bg-brand/5 p-4 text-sm text-slate-800">
                            <p v-if="paymentMode === 'manual'">
                                QRIS manual: customer bayar lalu cashier konfirmasi pembayaran.
                            </p>
                            <p v-else>
                                Automatic gateway: status akan pending sampai gateway aktif.
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 bg-gray-50 p-6 lg:border-l lg:border-t-0">
                        <p class="text-sm font-semibold text-slate-800">Summary</p>
                        <div class="mt-4 space-y-3 rounded-xl bg-white p-4 shadow-sm">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-medium">Rp {{ formatCurrency(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Discount</span>
                                <span class="font-medium">Rp {{ formatCurrency(discountValue) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tax</span>
                                <span class="font-medium">Rp {{ formatCurrency(tax) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Service Charge</span>
                                <span class="font-medium">Rp {{ formatCurrency(serviceCharge) }}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex items-center justify-between text-lg font-bold text-slate-800">
                                    <span>Total</span>
                                    <span>Rp {{ formatCurrency(total) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" class="rounded-xl border border-gray-300 px-4 py-3 text-gray-700" @click="closePaymentDialog">Cancel</button>
                            <button type="button" class="rounded-xl bg-brand px-4 py-3 font-semibold text-white hover:bg-brand-dark" @click="confirmPayment">
                                Confirm Payment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <ReceiptModal
            :is-open="paymentSuccessOpen"
            :receipt="lastReceipt"
            :order-id="lastOrderId"
            :order-number="lastOrderNumber"
            @close="closeSuccessDialog"
            @new-order="closeSuccessDialog"
        />

        <BarcodeScanner
            v-if="showBarcodeScanner"
            @detected="onBarcodeDetected"
            @close="showBarcodeScanner = false"
        />
    </AppLayout>
</template>
