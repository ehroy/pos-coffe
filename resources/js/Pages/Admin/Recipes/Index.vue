<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAdminMenu } from '@/Composables/useMenuItems';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    products: Array,
    ingredients: Array,
});

const menuItems = useAdminMenu();

const selectedProduct = ref(null);
const recipeForm = ref({ ingredient_id: '', variant_id: '', qty_used: '' });
const recipeErrors = ref({});
const isSubmitting = ref(false);

const selectProduct = (product) => {
    selectedProduct.value = product;
    recipeForm.value = { ingredient_id: '', variant_id: '', qty_used: '' };
    recipeErrors.value = {};
};

const submitRecipe = () => {
    if (!selectedProduct.value) return;
    isSubmitting.value = true;
    recipeErrors.value = {};

    router.post(route('admin.recipes.store', selectedProduct.value.id), recipeForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            isSubmitting.value = false;
            recipeForm.value = { ingredient_id: '', variant_id: '', qty_used: '' };
        },
        onError: (e) => { recipeErrors.value = e; isSubmitting.value = false; },
    });
};

const deleteRecipe = (product, recipe) => {
    if (!confirm(`Hapus resep "${recipe.ingredient?.name}"?`)) return;
    router.delete(route('admin.recipes.destroy', [product.id, recipe.id]), { preserveScroll: true });
};

const getIngredientName = (id) => props.ingredients.find(i => i.id === id)?.name ?? '-';

const formatQty = (qty, ingredientId) => {
    const ing = props.ingredients.find(i => i.id === ingredientId);
    return `${qty} ${ing?.unit ?? ''}`;
};
</script>

<template>
    <Head title="Manajemen Resep" />

    <AppLayout title="Manajemen Resep" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <h2 class="text-2xl font-bold">Manajemen Resep</h2>
            <p class="mt-1 text-coffee-200">Atur bahan baku yang digunakan per produk untuk kalkulasi stok otomatis.</p>
        </div>

        <div v-if="$page.props.flash?.success" class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">{{ $page.props.flash.success }}</div>
        <div v-if="$page.props.flash?.error" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $page.props.flash.error }}</div>

        <div class="grid gap-6 lg:grid-cols-[320px_1fr]">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="border-b border-slate-100 px-4 py-3">
                    <h3 class="font-semibold text-slate-800">Pilih Produk</h3>
                </div>
                <div class="max-h-[600px] overflow-y-auto">
                    <button
                        v-for="product in products"
                        :key="product.id"
                        type="button"
                        class="flex w-full items-center gap-3 border-b border-slate-50 px-4 py-3 text-left transition hover:bg-slate-50"
                        :class="selectedProduct?.id === product.id ? 'bg-brand/5 border-l-2 border-l-brand' : ''"
                        @click="selectProduct(product)"
                    >
                        <div class="flex-1 min-w-0">
                            <p class="truncate text-sm font-medium text-slate-800">{{ product.name }}</p>
                            <p class="text-xs text-gray-400">{{ product.category?.name }} · {{ product.recipes?.length ?? 0 }} bahan</p>
                        </div>
                        <span
                            class="flex-shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold"
                            :class="product.recipes?.length ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                        >
                            {{ product.recipes?.length ?? 0 }}
                        </span>
                    </button>
                </div>
            </div>

            <div>
                <div v-if="!selectedProduct" class="flex h-64 items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white text-center text-gray-400">
                    <div>
                        <svg class="mx-auto mb-2 h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm">Pilih produk di sebelah kiri untuk mengelola resepnya.</p>
                    </div>
                </div>

                <div v-else class="space-y-4">
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-slate-800">{{ selectedProduct.name }}</h3>
                                <p class="text-sm text-gray-500">{{ selectedProduct.category?.name }}</p>
                            </div>
                        </div>

                        <div v-if="selectedProduct.recipes?.length" class="mb-4 space-y-2">
                            <div
                                v-for="recipe in selectedProduct.recipes"
                                :key="recipe.id"
                                class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2.5"
                            >
                                <div>
                                    <p class="text-sm font-medium text-slate-800">{{ recipe.ingredient?.name }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ formatQty(recipe.qty_used, recipe.ingredient_id) }}
                                        <span v-if="recipe.variant_id" class="ml-1 text-brand">· {{ selectedProduct.variants?.find(v => v.id === recipe.variant_id)?.name }}</span>
                                        <span v-else class="ml-1 text-gray-400">· Semua variant</span>
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="rounded-lg border border-red-200 px-2.5 py-1 text-xs text-red-600 hover:bg-red-50"
                                    @click="deleteRecipe(selectedProduct, recipe)"
                                >Hapus</button>
                            </div>
                        </div>
                        <p v-else class="mb-4 rounded-xl bg-amber-50 px-3 py-2 text-sm text-amber-700">
                            Belum ada resep. Stok tidak akan dikurangi saat order selesai.
                        </p>

                        <div class="border-t border-slate-100 pt-4">
                            <h4 class="mb-3 text-sm font-semibold text-slate-700">Tambah Bahan Baku</h4>
                            <div class="grid gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-600">Bahan Baku</label>
                                    <select v-model="recipeForm.ingredient_id" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
                                        <option value="">Pilih bahan...</option>
                                        <option v-for="ing in ingredients" :key="ing.id" :value="ing.id">{{ ing.name }} ({{ ing.unit }})</option>
                                    </select>
                                    <p v-if="recipeErrors.ingredient_id" class="mt-1 text-xs text-red-600">{{ recipeErrors.ingredient_id }}</p>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-600">Variant (opsional)</label>
                                    <select v-model="recipeForm.variant_id" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
                                        <option value="">Semua variant</option>
                                        <option v-for="v in selectedProduct.variants" :key="v.id" :value="v.id">{{ v.name }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1 block text-xs font-medium text-gray-600">Qty Digunakan</label>
                                    <input v-model="recipeForm.qty_used" type="number" min="0.001" step="0.001" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="0" />
                                    <p v-if="recipeErrors.qty_used" class="mt-1 text-xs text-red-600">{{ recipeErrors.qty_used }}</p>
                                </div>
                            </div>
                            <button
                                type="button"
                                class="mt-3 rounded-xl bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50"
                                :disabled="isSubmitting || !recipeForm.ingredient_id || !recipeForm.qty_used"
                                @click="submitRecipe"
                            >
                                {{ isSubmitting ? 'Menyimpan...' : '+ Tambah Bahan' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
