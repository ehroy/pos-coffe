<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useWarehouseMenu } from '@/Composables/useMenuItems';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    ingredients: Array,
    stats: Object,
});

const menuItems = useWarehouseMenu();
const page = usePage();

const flashSuccess = computed(() => page.props.flash?.success || '');
const flashError = computed(() => page.props.flash?.error || '');

const activeModal = ref(null);
const selectedIngredient = ref(null);
const isSubmitting = ref(false);

const form = ref({ ingredient_id: '', qty: '', note: '' });

const openModal = (type, ingredient = null) => {
    activeModal.value = type;
    selectedIngredient.value = ingredient;
    form.value = { ingredient_id: ingredient?.id ?? '', qty: '', note: '' };
};

const closeModal = () => {
    activeModal.value = null;
    selectedIngredient.value = null;
    form.value = { ingredient_id: '', qty: '', note: '' };
};

const submit = (routeName) => {
    isSubmitting.value = true;
    router.post(route(routeName), form.value, {
        preserveScroll: true,
        onSuccess: () => { isSubmitting.value = false; closeModal(); },
        onError: () => { isSubmitting.value = false; },
    });
};

const stockStatusClass = (item) => {
    if (Number(item.current_stock) <= 0) return 'text-red-600';
    if (Number(item.current_stock) <= Number(item.minimum_stock)) return 'text-amber-600';
    return 'text-green-600';
};

const stockStatusBg = (item) => {
    if (Number(item.current_stock) <= 0) return 'bg-red-50 border-red-200';
    if (Number(item.current_stock) <= Number(item.minimum_stock)) return 'bg-amber-50 border-amber-200';
    return 'bg-white border-slate-200';
};
</script>

<template>
    <Head title="Kelola Stok" />

    <AppLayout title="Kelola Stok" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Kelola Stok Bahan Baku</h2>
                    <p class="mt-1 text-coffee-200">Catat stok masuk, keluar, dan penyesuaian.</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="rounded-xl bg-green-500 px-4 py-2 text-sm font-semibold text-white hover:bg-green-600" @click="openModal('in')">
                        + Stok Masuk
                    </button>
                    <button type="button" class="rounded-xl bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600" @click="openModal('out')">
                        - Stok Keluar
                    </button>
                </div>
            </div>
        </div>

        <div v-if="flashSuccess" class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ flashSuccess }}</div>
        <div v-if="flashError" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ flashError }}</div>

        <div class="mb-4 grid grid-cols-3 gap-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm text-center">
                <p class="text-sm text-gray-500">Total Bahan</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ stats.total }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm text-center">
                <p class="text-sm text-gray-500">Hampir Habis</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ stats.low_stock }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm text-center">
                <p class="text-sm text-gray-500">Stok Habis</p>
                <p class="mt-1 text-2xl font-bold text-red-600">{{ stats.out }}</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="item in ingredients"
                :key="item.id"
                class="rounded-xl border p-4 shadow-sm"
                :class="stockStatusBg(item)"
            >
                <div class="mb-3 flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-slate-800">{{ item.name }}</p>
                        <p class="text-xs text-gray-400">Min: {{ item.minimum_stock }} {{ item.unit }}</p>
                    </div>
                    <p class="text-lg font-bold" :class="stockStatusClass(item)">
                        {{ item.current_stock }} <span class="text-sm font-normal">{{ item.unit }}</span>
                    </p>
                </div>
                <div class="flex gap-2">
                    <button type="button" class="flex-1 rounded-lg bg-green-500 py-1.5 text-xs font-semibold text-white hover:bg-green-600" @click="openModal('in', item)">
                        Masuk
                    </button>
                    <button type="button" class="flex-1 rounded-lg bg-red-500 py-1.5 text-xs font-semibold text-white hover:bg-red-600" @click="openModal('out', item)">
                        Keluar
                    </button>
                    <button type="button" class="flex-1 rounded-lg border border-slate-200 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50" @click="openModal('adjustment', item)">
                        Sesuaikan
                    </button>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <div v-if="activeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="closeModal">
                <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                    <h3 class="mb-4 text-lg font-bold text-slate-800">
                        {{ activeModal === 'in' ? 'Stok Masuk' : activeModal === 'out' ? 'Stok Keluar' : 'Penyesuaian Stok' }}
                        <span v-if="selectedIngredient" class="text-brand"> — {{ selectedIngredient.name }}</span>
                    </h3>

                    <div class="space-y-3">
                        <div v-if="!selectedIngredient">
                            <label class="mb-1 block text-sm font-medium text-gray-700">Bahan Baku</label>
                            <select v-model="form.ingredient_id" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
                                <option value="">Pilih bahan baku</option>
                                <option v-for="ing in ingredients" :key="ing.id" :value="ing.id">{{ ing.name }} ({{ ing.current_stock }} {{ ing.unit }})</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                {{ activeModal === 'adjustment' ? 'Stok Baru' : 'Jumlah' }}
                                <span v-if="selectedIngredient" class="text-gray-400">({{ selectedIngredient.unit }})</span>
                            </label>
                            <input v-model="form.qty" type="number" min="0" step="0.01" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="0" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Catatan (opsional)</label>
                            <input v-model="form.note" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="Keterangan..." />
                        </div>
                    </div>

                    <div class="mt-5 flex gap-3">
                        <button
                            type="button"
                            class="flex-1 rounded-xl py-2.5 text-sm font-semibold text-white disabled:opacity-50"
                            :class="activeModal === 'in' ? 'bg-green-500 hover:bg-green-600' : activeModal === 'out' ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'"
                            :disabled="isSubmitting"
                            @click="submit(activeModal === 'in' ? 'warehouse.stock-in' : activeModal === 'out' ? 'warehouse.stock-out' : 'warehouse.adjustment')"
                        >
                            {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                        <button type="button" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm text-gray-600 hover:bg-gray-50" @click="closeModal">Batal</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>
