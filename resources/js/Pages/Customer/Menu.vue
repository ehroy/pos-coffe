<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { useCartStore } from '@/Stores/useCartStore';
import CategoryPicker from '@/Components/UI/CategoryPicker.vue';
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    table: Object,
    products: Array,
    categories: Array,
});

const cart = useCartStore();
const page = usePage();
const pricing = computed(() => page.props.pricing ?? {});
const taxLabel = computed(() => pricing.value.tax_enabled ? `Pajak (${pricing.value.tax_rate}%)` : 'Pajak');
const serviceChargeLabel = computed(() => pricing.value.service_charge_enabled ? `Service Charge (${pricing.value.service_charge_rate}%)` : 'Service Charge');

onMounted(() => {
    cart.setTable(props.table.qr_token, props.table.id, props.table.name);
});

const search = ref('');
const selectedCategory = ref('');

const formatCurrency = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));

const filteredProducts = computed(() => {
    const query = search.value.trim().toLowerCase();

    return props.products.filter((product) => {
        const matchesCategory = !selectedCategory.value || String(product.category_id) === String(selectedCategory.value);
        const matchesQuery = !query || [product.name, product.category?.name, ...(product.variants || []).map((variant) => variant.name)]
            .filter(Boolean)
            .some((value) => String(value).toLowerCase().includes(query));

        return matchesCategory && matchesQuery;
    });
});
</script>

<template>
    <Head :title="`Table ${table.code}`" />

    <div class="min-h-screen bg-slate-50 text-slate-800">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-xl">
                <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-sm text-[#e6d4bd]">Menu Meja QR</p>
                        <h1 class="text-3xl font-bold">{{ table.name }}</h1>
                        <p class="text-sm text-[#eadfce]">{{ table.code }} · Pilih menu favoritmu, lalu kirim pesanan ke dapur.</p>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-sm">
                        <div class="rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm">
                            <p class="text-[#e8dcc8]">Items</p>
                            <p class="text-xl font-bold">{{ cart.cartCount }}</p>
                        </div>
                        <div class="rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm">
                            <p class="text-[#e8dcc8]">Subtotal</p>
                            <p class="text-xl font-bold">Rp {{ formatCurrency(cart.subtotal) }}</p>
                        </div>
                        <div class="rounded-xl bg-white/10 px-4 py-3 backdrop-blur-sm">
                            <p class="text-[#e8dcc8]">Total</p>
                            <p class="text-xl font-bold">Rp {{ formatCurrency(cart.total) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-6 xl:grid-cols-[1.6fr_0.9fr]">
                <section class="rounded-2xl bg-white p-4 shadow-sm md:p-6">
                    <div class="mb-4 space-y-3">
                        <input v-model="search" type="text" placeholder="Cari menu favorit..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-brand focus:ring-1 focus:ring-brand/20" />
                        <CategoryPicker
                            v-model="selectedCategory"
                            :categories="categories"
                            all-label="Semua Menu"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 xl:grid-cols-3 xl:gap-4">
                        <article v-for="product in filteredProducts" :key="product.id" class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:border-brand/60 hover:shadow-md">
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

                                <div class="flex flex-wrap gap-2">
                                    <button v-if="!product.variants?.length" type="button" class="rounded-xl bg-brand px-3 py-2 text-xs text-white hover:bg-brand-dark sm:text-sm" @click="cart.addItem(product)">Tambah</button>
                                    <template v-else>
                                        <button
                                            v-for="variant in product.variants.slice(0, 3)"
                                            :key="variant.id"
                                            type="button"
                                            class="rounded-xl border border-brand/40 px-2.5 py-2 text-[11px] text-slate-800 hover:bg-slate-50 sm:px-3 sm:text-sm"
                                            @click="cart.addItem(product, variant)"
                                        >
                                            {{ variant.name }} · Rp {{ formatCurrency(variant.price) }}
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div v-if="filteredProducts.length === 0" class="rounded-2xl border border-dashed border-[#d8c7b3] py-10 text-center text-gray-500 bg-[#fffaf4]">
                        Menu tidak ditemukan.
                    </div>
                </section>

                <aside class="rounded-2xl bg-white p-4 shadow-sm md:p-6 border border-slate-200">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-800">Pesanan Anda</h3>
                            <p class="text-sm text-gray-500">{{ cart.cartCount }} item dipilih</p>
                        </div>
                        <a
                            v-if="cart.cartCount > 0"
                            :href="`/table/${table.qr_token}/cart`"
                            class="rounded-xl bg-brand/10 px-3 py-1.5 text-xs font-semibold text-brand hover:bg-[#eddfc8]"
                        >
                            Lihat Keranjang
                        </a>
                    </div>

                    <div v-if="cart.items.length === 0" class="mt-4 rounded-xl border border-dashed border-gray-300 py-10 text-center text-gray-500">
                        Belum ada menu dipilih.
                    </div>

                    <div v-else class="mt-4 space-y-3">
                        <div v-for="item in cart.items" :key="item.key" class="rounded-xl border border-gray-200 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium text-slate-800">{{ item.name }}</p>
                                    <p class="text-xs text-gray-500">{{ item.variant_name }}</p>
                                </div>
                                <button type="button" class="text-sm text-red-600" @click="cart.removeItem(item.key)">Hapus</button>
                            </div>

                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button type="button" class="h-8 w-8 rounded-lg border border-gray-300" @click="cart.updateQty(item.key, item.qty - 1)">-</button>
                                    <span class="w-8 text-center text-sm font-semibold">{{ item.qty }}</span>
                                    <button type="button" class="h-8 w-8 rounded-lg border border-gray-300" @click="cart.updateQty(item.key, item.qty + 1)">+</button>
                                </div>
                                <p class="text-sm font-semibold text-slate-800">Rp {{ formatCurrency(item.price * item.qty) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3 border-t border-gray-200 pt-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium">Rp {{ formatCurrency(cart.subtotal) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">{{ taxLabel }}</span>
                            <span class="font-medium">Rp {{ formatCurrency(cart.tax) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">{{ serviceChargeLabel }}</span>
                            <span class="font-medium">Rp {{ formatCurrency(cart.serviceCharge) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-lg font-bold text-slate-800">
                            <span>Total</span>
                            <span>Rp {{ formatCurrency(cart.total) }}</span>
                        </div>
                        <a
                            :href="`/table/${table.qr_token}/checkout`"
                            class="block w-full rounded-xl bg-brand px-4 py-3 text-center font-semibold text-white hover:bg-brand-dark"
                            :class="{ 'pointer-events-none opacity-50': cart.cartCount === 0 }"
                        >
                            Checkout ({{ cart.cartCount }} item)
                        </a>
                        <button type="button" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-700" @click="cart.clearCart">
                            Kosongkan Pesanan
                        </button>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</template>
