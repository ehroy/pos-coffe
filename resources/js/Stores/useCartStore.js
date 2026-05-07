import { defineStore } from 'pinia';
import { computed, ref, watch } from 'vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';
import { calculatePricing } from '@/Services/pricing';

const STORAGE_KEY = 'coffee_pos_cart';

function loadFromStorage() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch {
        return null;
    }
}

function saveToStorage(data) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    } catch {}
}

export const useCartStore = defineStore('cart', () => {
    const page = usePage();
    const saved = loadFromStorage();

    const items     = ref(saved?.items     ?? []);
    const qrToken   = ref(saved?.qrToken   ?? null);
    const tableId   = ref(saved?.tableId   ?? null);
    const tableName = ref(saved?.tableName ?? null);

    const cartCount = computed(() => items.value.reduce((sum, item) => sum + item.qty, 0));

    const subtotal = computed(() =>
        items.value.reduce((sum, item) => sum + item.price * item.qty, 0)
    );

    const pricing = computed(() => calculatePricing(subtotal.value, 0, page.props.pricing ?? {}));

    const tax = computed(() => pricing.value.tax);

    const serviceCharge = computed(() => pricing.value.serviceCharge);

    const total = computed(() => pricing.value.total);

    watch(
        () => ({ items: items.value, qrToken: qrToken.value, tableId: tableId.value, tableName: tableName.value }),
        (val) => saveToStorage(val),
        { deep: true }
    );

    function setTable(token, id, name) {
        qrToken.value   = token;
        tableId.value   = id;
        tableName.value = name;
    }

    function addItem(product, variant = null) {
        const key      = `${product.id}-${variant?.id || 'base'}`;
        const existing = items.value.find((item) => item.key === key);
        const price    = Number(variant?.price ?? product.base_price);

        if (existing) {
            existing.qty += 1;
            return;
        }

        items.value.push({
            key,
            product_id:   product.id,
            variant_id:   variant?.id || null,
            name:         product.name,
            variant_name: variant?.name || null,
            price,
            qty: 1,
        });
    }

    function removeItem(key) {
        items.value = items.value.filter((item) => item.key !== key);
    }

    function updateQty(key, qty) {
        const item = items.value.find((i) => i.key === key);
        if (!item) return;

        if (qty <= 0) {
            removeItem(key);
            return;
        }

        item.qty = qty;
    }

    function clearCart() {
        items.value     = [];
        qrToken.value   = null;
        tableId.value   = null;
        tableName.value = null;
        try { localStorage.removeItem(STORAGE_KEY); } catch {}
    }

    async function checkout(customerName, note, paymentMethod) {
        if (!qrToken.value) {
            throw new Error('QR token tidak ditemukan. Silakan scan ulang QR meja.');
        }

        const payload = {
            customer_name:  customerName || null,
            note:           note || null,
            payment_method: paymentMethod,
            items: items.value.map((item) => ({
                product_id:   item.product_id,
                variant_id:   item.variant_id,
                product_name: item.name,
                variant_name: item.variant_name,
                qty:          item.qty,
                price:        item.price,
            })),
        };

        const response = await axios.post(
            `/api/customer/tables/${qrToken.value}/orders`,
            payload
        );

        clearCart();

        return response.data.order;
    }

    return {
        items,
        qrToken,
        tableId,
        tableName,
        cartCount,
        subtotal,
        tax,
        serviceCharge,
        total,
        pricing,
        setTable,
        addItem,
        removeItem,
        updateQty,
        clearCart,
        checkout,
    };
});
