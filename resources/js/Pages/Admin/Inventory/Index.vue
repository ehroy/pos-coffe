<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useAdminMenu } from '@/Composables/useMenuItems';
import { ref } from 'vue';

const props = defineProps({
    ingredients: Array,
    stats: Object,
    lowStockItems: Array,
    recentMovements: Array,
});

const menuItems = useAdminMenu();

const getStockStatusColor = (status) => ({
    out_of_stock: 'bg-red-100 text-red-800',
    low_stock:    'bg-yellow-100 text-yellow-800',
    in_stock:     'bg-green-100 text-green-800',
}[status] ?? 'bg-gray-100 text-gray-800');

const getStockStatusLabel = (status) => ({
    out_of_stock: 'Habis',
    low_stock:    'Hampir Habis',
    in_stock:     'Tersedia',
}[status] ?? 'Unknown');

const getMovementTypeColor = (type) => ({
    in:         'bg-green-100 text-green-800',
    out:        'bg-red-100 text-red-800',
    adjustment: 'bg-blue-100 text-blue-800',
}[type] ?? 'bg-gray-100 text-gray-800');

const activeForm   = ref(null);
const activeModal  = ref(null);
const selectedIngredient = ref(null);

const ingredientForm = useForm({
    name: '', unit: 'pcs', current_stock: '', minimum_stock: '', cost_per_unit: '', is_active: true,
});

const stockInForm = useForm({ ingredient_id: props.ingredients[0]?.id ?? '', qty: '', note: '' });

const stockOutForm = useForm({ ingredient_id: '', qty: '', note: '' });

const adjustForm = useForm({ ingredient_id: '', qty: '', note: '' });

const openIngredientForm = () => { activeForm.value = 'ingredient'; };
const openStockForm = () => {
    if (!stockInForm.ingredient_id && props.ingredients.length > 0) {
        stockInForm.ingredient_id = props.ingredients[0].id;
    }
    activeForm.value = 'stock';
};
const closeForm = () => { activeForm.value = null; };

const openModal = (type, ingredient) => {
    activeModal.value = type;
    selectedIngredient.value = ingredient;
    if (type === 'out') {
        stockOutForm.ingredient_id = ingredient.id;
        stockOutForm.qty = '';
        stockOutForm.note = '';
    } else if (type === 'adjust') {
        adjustForm.ingredient_id = ingredient.id;
        adjustForm.qty = ingredient.current_stock;
        adjustForm.note = '';
    }
};
const closeModal = () => { activeModal.value = null; selectedIngredient.value = null; };

const submitIngredient = () => {
    ingredientForm.post(route('admin.inventory.ingredients.store'), {
        preserveScroll: true,
        onSuccess: () => { ingredientForm.reset('name', 'current_stock', 'minimum_stock', 'cost_per_unit'); ingredientForm.unit = 'pcs'; ingredientForm.is_active = true; closeForm(); },
    });
};

const submitStockIn = () => {
    stockInForm.post(route('admin.inventory.stock-in'), {
        preserveScroll: true,
        onSuccess: () => { stockInForm.reset('qty', 'note'); closeForm(); },
    });
};

const submitStockOut = () => {
    router.post(route('admin.inventory.stock-out'), stockOutForm.data(), {
        preserveScroll: true,
        onSuccess: closeModal,
        onError: (e) => { stockOutForm.setError(e); },
    });
};

const submitAdjust = () => {
    router.post(route('admin.inventory.adjustment'), adjustForm.data(), {
        preserveScroll: true,
        onSuccess: closeModal,
        onError: (e) => { adjustForm.setError(e); },
    });
};
</script>

<template>
    <Head title="Inventory Management" />

    <AppLayout title="Inventory Management" :menu-items="menuItems">
        <div v-if="activeForm" class="mb-6 rounded-xl border border-brand/30 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">
                        {{ activeForm === 'ingredient' ? 'Add Ingredient' : 'Stock In' }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ activeForm === 'ingredient' ? 'Create a new raw material.' : 'Add stock to existing ingredient.' }}
                    </p>
                </div>
                <button class="text-sm text-gray-500 hover:text-gray-800" @click="closeForm">Close</button>
            </div>

            <form v-if="activeForm === 'ingredient'" class="grid gap-4 md:grid-cols-2" @submit.prevent="submitIngredient">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                    <input v-model="ingredientForm.name" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2" required />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Unit</label>
                    <select v-model="ingredientForm.unit" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="gram">gram</option>
                        <option value="ml">ml</option>
                        <option value="pcs">pcs</option>
                        <option value="kg">kg</option>
                        <option value="liter">liter</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Current Stock</label>
                    <input v-model="ingredientForm.current_stock" type="number" min="0" step="0.01" class="w-full rounded-lg border border-gray-300 px-3 py-2" required />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Minimum Stock</label>
                    <input v-model="ingredientForm.minimum_stock" type="number" min="0" step="0.01" class="w-full rounded-lg border border-gray-300 px-3 py-2" required />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Cost/Unit</label>
                    <input v-model="ingredientForm.cost_per_unit" type="number" min="0" step="1" class="w-full rounded-lg border border-gray-300 px-3 py-2" required />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select v-model="ingredientForm.is_active" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option :value="true">Active</option>
                        <option :value="false">Inactive</option>
                    </select>
                </div>
                <div class="md:col-span-2 flex justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700" @click="closeForm">Cancel</button>
                    <button type="submit" class="rounded-lg bg-brand px-4 py-2 text-white hover:bg-brand-dark" :disabled="ingredientForm.processing">Save Ingredient</button>
                </div>
            </form>

            <form v-else class="grid gap-4 md:grid-cols-2" @submit.prevent="submitStockIn">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Ingredient</label>
                    <select v-model="stockInForm.ingredient_id" class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
                        <option value="" disabled>Select ingredient</option>
                        <option v-for="item in ingredients" :key="item.id" :value="item.id">{{ item.name }}</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Qty</label>
                    <input v-model="stockInForm.qty" type="number" min="0.01" step="0.01" class="w-full rounded-lg border border-gray-300 px-3 py-2" required />
                </div>
                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Note</label>
                    <textarea v-model="stockInForm.note" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2"></textarea>
                </div>
                <div class="md:col-span-2 flex justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700" @click="closeForm">Cancel</button>
                    <button type="submit" class="rounded-lg bg-brand px-4 py-2 text-white hover:bg-brand-dark" :disabled="stockInForm.processing">Save Stock In</button>
                </div>
            </form>
        </div>

        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Inventory Management</h2>
                <p class="text-gray-600 mt-1">Manage ingredients, stock levels, and recipes</p>
            </div>
            <div class="flex gap-2">
                <button class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-semibold flex items-center gap-2" @click="openStockForm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Stock In
                </button>
                <button class="px-6 py-3 bg-brand text-white rounded-lg hover:bg-brand-dark transition-colors font-semibold flex items-center gap-2" @click="openIngredientForm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Ingredient
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <p class="text-sm text-gray-600 mb-1">Total Ingredients</p>
                <p class="text-3xl font-bold text-slate-800">{{ stats.total_ingredients }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <p class="text-sm text-gray-600 mb-1">Low Stock Items</p>
                <p class="text-3xl font-bold text-yellow-600">{{ stats.low_stock_items }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <p class="text-sm text-gray-600 mb-1">Out of Stock</p>
                <p class="text-3xl font-bold text-red-600">{{ stats.out_of_stock }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <p class="text-sm text-gray-600 mb-1">Total Value</p>
                <p class="text-3xl font-bold text-green-600">Rp {{ Math.round(stats.total_value).toLocaleString('id-ID') }}</p>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div v-if="lowStockItems.length > 0" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 font-semibold">
                        Low Stock Alert: {{ lowStockItems.length }} items need restocking
                    </p>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li v-for="item in lowStockItems" :key="item.id">
                                {{ item.name }}: {{ item.current_stock }} {{ item.unit }} (Min: {{ item.minimum_stock }})
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Ingredients List -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-slate-800">All Ingredients</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ingredient</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost/Unit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="ingredient in ingredients" :key="ingredient.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ ingredient.name }}</div>
                                    <div class="text-xs text-gray-500">{{ ingredient.unit }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold" :class="ingredient.is_low_stock ? 'text-red-600' : 'text-gray-900'">
                                        {{ ingredient.current_stock }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ingredient.minimum_stock }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ Number(ingredient.cost_per_unit || 0).toLocaleString('id-ID') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="getStockStatusColor(ingredient.stock_status)"
                                    >
                                        {{ getStockStatusLabel(ingredient.stock_status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button
                                        type="button"
                                        class="mr-2 rounded-lg bg-green-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-600"
                                        @click="openModal('in', ingredient)"
                                    >
                                        + Masuk
                                    </button>
                                    <button
                                        type="button"
                                        class="mr-2 rounded-lg bg-red-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-600"
                                        @click="openModal('out', ingredient)"
                                    >
                                        - Keluar
                                    </button>
                                    <button
                                        type="button"
                                        class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50"
                                        @click="openModal('adjust', ingredient)"
                                    >
                                        Sesuaikan
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Movements -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-slate-800">Recent Movements</h3>
                </div>
                <div class="p-6">
                    <div v-if="recentMovements.length > 0" class="space-y-4">
                        <div
                            v-for="movement in recentMovements"
                            :key="movement.id"
                            class="border-l-4 pl-4 py-2"
                            :class="movement.type === 'in' ? 'border-green-500' : movement.type === 'out' ? 'border-red-500' : 'border-blue-500'"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full uppercase"
                                    :class="getMovementTypeColor(movement.type)"
                                >
                                    {{ movement.type }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ new Date(movement.created_at).toLocaleDateString('id-ID') }}
                                </span>
                            </div>
                            <p class="text-sm font-medium text-gray-900">{{ movement.ingredient.name }}</p>
                            <p class="text-xs text-gray-600">
                                Qty: {{ movement.qty }} {{ movement.ingredient.unit }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ movement.before_stock }} → {{ movement.after_stock }}
                            </p>
                            <p v-if="movement.note" class="text-xs text-gray-500 mt-1">{{ movement.note }}</p>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        <p class="text-sm">No stock movements yet</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <Teleport to="body">
        <div v-if="activeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="closeModal">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <h3 class="mb-4 font-bold text-slate-800">
                    {{ activeModal === 'in' ? 'Stok Masuk' : activeModal === 'out' ? 'Stok Keluar' : 'Penyesuaian Stok' }}
                    <span v-if="selectedIngredient" class="text-brand"> — {{ selectedIngredient.name }}</span>
                </h3>

                <div class="space-y-3">
                    <div v-if="activeModal === 'in'">
                        <label class="mb-1 block text-sm font-medium text-gray-700">Bahan Baku</label>
                        <select v-model="stockInForm.ingredient_id" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
                            <option v-for="ing in ingredients" :key="ing.id" :value="ing.id">{{ ing.name }} ({{ ing.current_stock }} {{ ing.unit }})</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            {{ activeModal === 'adjust' ? 'Stok Baru' : 'Jumlah' }}
                            <span v-if="selectedIngredient" class="text-gray-400">({{ selectedIngredient.unit }})</span>
                        </label>
                        <input
                            v-if="activeModal === 'in'"
                            v-model="stockInForm.qty"
                            type="number" min="0.01" step="0.01"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand"
                            placeholder="0"
                        />
                        <input
                            v-else-if="activeModal === 'out'"
                            v-model="stockOutForm.qty"
                            type="number" min="0.01" step="0.01"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand"
                            placeholder="0"
                        />
                        <input
                            v-else
                            v-model="adjustForm.qty"
                            type="number" min="0" step="0.01"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand"
                        />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Catatan (opsional)</label>
                        <input
                            v-if="activeModal === 'in'" v-model="stockInForm.note"
                            type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="Keterangan..."
                        />
                        <input
                            v-else-if="activeModal === 'out'" v-model="stockOutForm.note"
                            type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="Keterangan..."
                        />
                        <input
                            v-else v-model="adjustForm.note"
                            type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="Keterangan..."
                        />
                    </div>
                </div>

                <div class="mt-5 flex gap-3">
                    <button
                        type="button"
                        class="flex-1 rounded-xl py-2.5 text-sm font-semibold text-white"
                        :class="activeModal === 'in' ? 'bg-green-500 hover:bg-green-600' : activeModal === 'out' ? 'bg-red-500 hover:bg-red-600' : 'bg-blue-500 hover:bg-blue-600'"
                        @click="activeModal === 'in' ? submitStockIn() : activeModal === 'out' ? submitStockOut() : submitAdjust()"
                    >
                        Simpan
                    </button>
                    <button type="button" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm text-gray-600 hover:bg-gray-50" @click="closeModal">Batal</button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
