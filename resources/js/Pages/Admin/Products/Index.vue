<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useAdminMenu } from '@/Composables/useMenuItems';
import CategoryPicker from '@/Components/UI/CategoryPicker.vue';
import { computed, ref } from 'vue';

const props = defineProps({
    products: Array,
    stats: Object,
    categories: Array,
});

const menuItems = useAdminMenu();

const getCategoryBadgeColor = (type) => {
    const colors = {
        food: 'bg-orange-100 text-orange-800',
        drink: 'bg-blue-100 text-blue-800',
        other: 'bg-gray-100 text-gray-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};

const showCreateForm = ref(false);
const showEditForm = ref(false);
const editingProduct = ref(null);
const variantModal = ref(null);
const editingVariant = ref(null);
const variantForm = ref({ name: '', price: '', sku: '', barcode: '', is_active: true });
const variantErrors = ref({});
const variantSubmitting = ref(false);

const openVariantModal = (product, variant = null) => {
    variantModal.value = product;
    editingVariant.value = variant;
    variantForm.value = variant
        ? { name: variant.name, price: variant.price, sku: variant.sku ?? '', barcode: variant.barcode ?? '', is_active: variant.is_active }
        : { name: '', price: '', sku: '', barcode: '', is_active: true };
    variantErrors.value = {};
};

const closeVariantModal = () => {
    variantModal.value = null;
    editingVariant.value = null;
    variantErrors.value = {};
};

const submitVariant = () => {
    variantSubmitting.value = true;
    variantErrors.value = {};
    const fd = new FormData();
    Object.entries(variantForm.value).forEach(([k, v]) => {
        if (v === null || v === undefined || v === '') return;
        fd.append(k, typeof v === 'boolean' ? (v ? '1' : '0') : v);
    });

    const url = editingVariant.value
        ? route('admin.products.variants.update', [variantModal.value.id, editingVariant.value.id])
        : route('admin.products.variants.store', variantModal.value.id);

    if (editingVariant.value) fd.append('_method', 'PATCH');

    router.post(url, fd, {
        preserveScroll: true,
        onSuccess: () => { variantSubmitting.value = false; closeVariantModal(); },
        onError: (e) => { variantErrors.value = e; variantSubmitting.value = false; },
    });
};

const deleteVariant = (product, variant) => {
    if (!confirm(`Hapus variant "${variant.name}"?`)) return;
    router.delete(route('admin.products.variants.destroy', [product.id, variant.id]), { preserveScroll: true });
};
const isSubmitting = ref(false);
const errors = ref({});

const createData = ref({
    category_id: props.categories?.[0]?.id ?? '',
    name: '',
    description: '',
    image: null,
    base_price: '',
    is_active: true,
    is_stock_tracked: false,
});

const editData = ref({
    category_id: '',
    name: '',
    description: '',
    image: null,
    base_price: '',
    is_active: true,
    is_stock_tracked: false,
});

const previewUrl = ref(null);
const editPreviewUrl = ref(null);

const filterCategory = ref('');

const filteredProducts = computed(() => {
    if (!filterCategory.value) return props.products;
    return props.products.filter(p => String(p.category_id) === String(filterCategory.value));
});

const openCreateForm = () => {
    if (!createData.value.category_id && props.categories.length > 0) {
        createData.value.category_id = props.categories[0].id;
    }
    errors.value = {};
    previewUrl.value = null;
    showCreateForm.value = true;
};

const closeCreateForm = () => {
    showCreateForm.value = false;
    createData.value = { category_id: props.categories?.[0]?.id ?? '', name: '', description: '', image: null, base_price: '', is_active: true, is_stock_tracked: false };
    previewUrl.value = null;
    errors.value = {};
};

const openEditForm = (product) => {
    editingProduct.value = product;
    editData.value = {
        category_id: product.category_id,
        name: product.name,
        description: product.description ?? '',
        image: null,
        base_price: product.base_price,
        is_active: !!product.is_active,
        is_stock_tracked: !!product.is_stock_tracked,
    };
    editPreviewUrl.value = null;
    errors.value = {};
    showEditForm.value = true;
};

const closeEditForm = () => {
    showEditForm.value = false;
    editingProduct.value = null;
    editData.value = { category_id: '', name: '', description: '', image: null, base_price: '', is_active: true, is_stock_tracked: false };
    editPreviewUrl.value = null;
    errors.value = {};
};

const onCreateImageChange = (e) => {
    const file = e.target.files?.[0] || null;
    createData.value.image = file;
    previewUrl.value = file ? URL.createObjectURL(file) : null;
};

const onEditImageChange = (e) => {
    const file = e.target.files?.[0] || null;
    editData.value.image = file;
    editPreviewUrl.value = file ? URL.createObjectURL(file) : null;
};

const buildFormData = (data, extra = {}) => {
    const fd = new FormData();
    Object.entries({ ...data, ...extra }).forEach(([key, val]) => {
        if (val === null || val === undefined) return;
        if (typeof val === 'boolean') {
            fd.append(key, val ? '1' : '0');
        } else {
            fd.append(key, val);
        }
    });
    return fd;
};

const submitCreateProduct = () => {
    isSubmitting.value = true;
    errors.value = {};
    const fd = buildFormData(createData.value);
    router.post(route('admin.products.store'), fd, {
        preserveScroll: true,
        onSuccess: () => { isSubmitting.value = false; closeCreateForm(); },
        onError: (e) => { errors.value = e; isSubmitting.value = false; },
    });
};

const submitEditProduct = () => {
    if (!editingProduct.value) return;
    isSubmitting.value = true;
    errors.value = {};
    const fd = buildFormData(editData.value, { _method: 'PATCH' });
    router.post(route('admin.products.update', editingProduct.value.id), fd, {
        preserveScroll: true,
        onSuccess: () => { isSubmitting.value = false; closeEditForm(); },
        onError: (e) => { errors.value = e; isSubmitting.value = false; },
    });
};
</script>

<template>
    <Head title="Products Management" />

    <AppLayout title="Products Management" :menu-items="menuItems">
        <div v-if="showCreateForm" class="mb-6 rounded-xl border border-brand/30 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Tambah Produk Baru</h3>
                    <p class="text-sm text-gray-500">Buat menu item baru untuk kasir dan customer.</p>
                </div>
                <button class="text-sm text-gray-500 hover:text-gray-800" @click="closeCreateForm">Tutup</button>
            </div>

            <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submitCreateProduct">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama Produk</label>
                    <input v-model="createData.name" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-brand focus:outline-none" required />
                    <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Kategori</label>
                    <CategoryPicker
                        v-model="createData.category_id"
                        :categories="categories"
                        :show-all="false"
                    />
                    <p v-if="errors.category_id" class="mt-1 text-xs text-red-600">{{ errors.category_id }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Harga Dasar (Rp)</label>
                    <input v-model="createData.base_price" type="number" min="0" step="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-brand focus:outline-none" required />
                    <p v-if="errors.base_price" class="mt-1 text-xs text-red-600">{{ errors.base_price }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Gambar Produk</label>
                    <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-brand/10 file:px-3 file:py-1 file:text-sm file:font-medium file:text-brand" @change="onCreateImageChange" />
                    <p v-if="errors.image" class="mt-1 text-xs text-red-600">{{ errors.image }}</p>
                    <div v-if="previewUrl" class="mt-2 overflow-hidden rounded-lg border border-gray-200">
                        <img :src="previewUrl" alt="Preview" class="h-32 w-full object-cover" />
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select v-model="createData.is_active" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-brand focus:outline-none">
                        <option :value="true">Aktif</option>
                        <option :value="false">Nonaktif</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea v-model="createData.description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-brand focus:outline-none"></textarea>
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-700 md:col-span-2">
                    <input v-model="createData.is_stock_tracked" type="checkbox" class="rounded border-gray-300" />
                    Lacak stok untuk produk ini
                </label>

                <div class="md:col-span-2 flex items-center justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50" @click="closeCreateForm">Batal</button>
                    <button type="submit" class="rounded-lg bg-brand px-4 py-2 text-white hover:bg-brand-dark disabled:opacity-50" :disabled="isSubmitting">
                        {{ isSubmitting ? 'Menyimpan...' : 'Simpan Produk' }}
                    </button>
                </div>
            </form>
        </div>

        <div v-if="showEditForm" class="mb-6 rounded-xl border border-brand/30 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Edit Produk</h3>
                    <p class="text-sm text-gray-500">Perbarui data produk dan ganti gambar jika diperlukan.</p>
                </div>
                <button class="text-sm text-gray-500 hover:text-gray-800" @click="closeEditForm">Tutup</button>
            </div>

            <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submitEditProduct">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama Produk</label>
                    <input v-model="editData.name" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-brand focus:outline-none" required />
                    <p v-if="errors.name" class="mt-1 text-xs text-red-600">{{ errors.name }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Kategori</label>
                    <CategoryPicker
                        v-model="editData.category_id"
                        :categories="categories"
                        :show-all="false"
                    />
                    <p v-if="errors.category_id" class="mt-1 text-xs text-red-600">{{ errors.category_id }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Harga Dasar (Rp)</label>
                    <input v-model="editData.base_price" type="number" min="0" step="1" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-brand focus:outline-none" required />
                    <p v-if="errors.base_price" class="mt-1 text-xs text-red-600">{{ errors.base_price }}</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Ganti Gambar</label>
                    <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-brand/10 file:px-3 file:py-1 file:text-sm file:font-medium file:text-brand" @change="onEditImageChange" />
                    <p v-if="errors.image" class="mt-1 text-xs text-red-600">{{ errors.image }}</p>
                    <div class="mt-2 overflow-hidden rounded-lg border border-gray-200">
                        <img
                            :src="editPreviewUrl || editingProduct?.image_url"
                            :alt="editingProduct?.name"
                            class="h-32 w-full object-cover"
                        />
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Biarkan kosong jika tidak ingin mengganti gambar.</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select v-model="editData.is_active" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-brand focus:outline-none">
                        <option :value="true">Aktif</option>
                        <option :value="false">Nonaktif</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea v-model="editData.description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-brand focus:outline-none"></textarea>
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-700 md:col-span-2">
                    <input v-model="editData.is_stock_tracked" type="checkbox" class="rounded border-gray-300" />
                    Lacak stok untuk produk ini
                </label>

                <div class="md:col-span-2 flex items-center justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50" @click="closeEditForm">Batal</button>
                    <button type="submit" class="rounded-lg bg-brand px-4 py-2 text-white hover:bg-brand-dark disabled:opacity-50" :disabled="isSubmitting">
                        {{ isSubmitting ? 'Menyimpan...' : 'Update Produk' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Manage Products</h2>
                <p class="text-gray-600 mt-1">View and manage your menu items and products</p>
            </div>
            <button class="px-6 py-3 bg-brand text-white rounded-lg hover:bg-brand-dark transition-colors font-semibold flex items-center gap-2" @click="openCreateForm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Product
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <p class="text-sm text-gray-600 mb-1">Total Products</p>
                <p class="text-3xl font-bold text-slate-800">{{ stats.total_products }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <p class="text-sm text-gray-600 mb-1">Active Products</p>
                <p class="text-3xl font-bold text-green-600">{{ stats.active_products }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <p class="text-sm text-gray-600 mb-1">Inactive Products</p>
                <p class="text-3xl font-bold text-red-600">{{ stats.inactive_products }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <p class="text-sm text-gray-600 mb-1">Total Variants</p>
                <p class="text-3xl font-bold text-blue-600">{{ stats.total_variants }}</p>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-800">Semua Produk ({{ filteredProducts.length }})</h3>
                <div class="flex gap-2">
                    <select v-model="filterCategory" class="px-4 py-2 border border-gray-300 rounded-lg text-sm outline-none focus:border-brand">
                        <option value="">Semua Kategori</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-3 md:grid-cols-2 lg:grid-cols-3 lg:gap-6">
                    <div
                        v-for="product in filteredProducts"
                        :key="product.id"
                        class="overflow-hidden rounded-xl border border-gray-200 bg-white transition-all hover:border-brand hover:shadow-lg"
                    >
                        <div class="aspect-[4/3] bg-gray-100">
                            <img :src="product.image_url" :alt="product.name" class="h-full w-full object-cover" loading="lazy" />
                        </div>

                        <div class="p-3 sm:p-4">
                            <div class="mb-2 flex items-start justify-between gap-2">
                                <div class="flex-1">
                                    <h4 class="mb-1 text-sm font-bold text-slate-800 sm:text-lg">{{ product.name }}</h4>
                                    <span
                                        class="inline-block rounded-full px-2 py-1 text-[10px] font-semibold sm:text-xs"
                                        :class="getCategoryBadgeColor(product.category?.type)"
                                    >
                                        {{ product.category?.name }}
                                    </span>
                                </div>
                                <span
                                    class="rounded-full px-2 py-1 text-[10px] font-semibold sm:text-xs"
                                    :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                >
                                    {{ product.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <p class="mb-3 line-clamp-2 text-xs text-gray-600 sm:text-sm">{{ product.description }}</p>

                            <div class="mb-3">
                                <p class="mb-1 text-xs text-gray-500">Base Price</p>
                                <p class="text-base font-bold text-slate-800 sm:text-xl">
                                    Rp {{ Number(product.base_price || 0).toLocaleString('id-ID') }}
                                </p>
                            </div>

                            <!-- Variants -->
                            <div class="mb-4">
                                <p class="mb-2 text-xs text-gray-500">Variants ({{ product.variants.length }})</p>
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="variant in product.variants.slice(0, 3)"
                                        :key="variant.id"
                                        class="rounded bg-gray-100 px-2 py-1 text-[10px] text-gray-700 sm:text-xs"
                                    >
                                        {{ variant.name }}
                                    </span>
                                    <span
                                        v-if="product.variants.length > 3"
                                        class="rounded bg-gray-100 px-2 py-1 text-[10px] text-gray-700 sm:text-xs"
                                    >
                                        +{{ product.variants.length - 3 }} more
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="button" class="flex-1 rounded-lg bg-brand px-3 py-2 text-sm font-semibold text-white hover:bg-brand-dark" @click="openEditForm(product)">
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50"
                                    @click="openVariantModal(product)"
                                    title="Kelola Variant"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                </button>
                                <button
                                    type="button"
                                    class="rounded-lg border border-red-200 px-3 py-2 text-red-600 hover:bg-red-50"
                                    @click="router.patch(route('admin.products.update', product.id), { is_active: !product.is_active }, { preserveScroll: true })"
                                    :title="product.is_active ? 'Nonaktifkan' : 'Aktifkan'"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path v-if="product.is_active" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredProducts.length === 0" class="text-center py-12">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="text-gray-500 font-medium">No products found</p>
                    <p class="text-sm text-gray-400 mt-1">Start by adding your first product</p>
                </div>
            </div>
        </div>
    </AppLayout>

    <Teleport to="body">
        <div v-if="variantModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" @click.self="closeVariantModal">
            <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <div>
                        <h3 class="font-bold text-slate-800">Kelola Variant</h3>
                        <p class="text-sm text-gray-500">{{ variantModal.name }}</p>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600" @click="closeVariantModal">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="max-h-64 overflow-y-auto px-6 py-3">
                    <div v-if="variantModal.variants?.length" class="space-y-2">
                        <div
                            v-for="v in variantModal.variants"
                            :key="v.id"
                            class="flex items-center justify-between rounded-xl border border-slate-200 px-3 py-2.5"
                        >
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ v.name }}</p>
                                <p class="text-xs text-gray-400">Rp {{ Number(v.price).toLocaleString('id-ID') }} · {{ v.sku || '-' }}</p>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" class="rounded-lg border border-brand/30 px-2.5 py-1 text-xs font-medium text-brand hover:bg-brand/5" @click="openVariantModal(variantModal, v)">Edit</button>
                                <button type="button" class="rounded-lg border border-red-200 px-2.5 py-1 text-xs text-red-600 hover:bg-red-50" @click="deleteVariant(variantModal, v)">Hapus</button>
                            </div>
                        </div>
                    </div>
                    <p v-else class="py-4 text-center text-sm text-gray-400">Belum ada variant. Tambahkan di bawah.</p>
                </div>

                <div class="border-t border-slate-100 px-6 py-4">
                    <h4 class="mb-3 text-sm font-semibold text-slate-700">
                        {{ editingVariant ? `Edit Variant: ${editingVariant.name}` : 'Tambah Variant Baru' }}
                    </h4>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Nama Variant</label>
                            <input v-model="variantForm.name" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="Contoh: Large, Hot, dll" />
                            <p v-if="variantErrors.name" class="mt-1 text-xs text-red-600">{{ variantErrors.name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Harga (Rp)</label>
                            <input v-model="variantForm.price" type="number" min="0" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="0" />
                            <p v-if="variantErrors.price" class="mt-1 text-xs text-red-600">{{ variantErrors.price }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">SKU (opsional)</label>
                            <input v-model="variantForm.sku" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="SKU-001" />
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-600">Barcode (opsional)</label>
                            <input v-model="variantForm.barcode" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="1234567890" />
                        </div>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <button
                            type="button"
                            class="flex-1 rounded-xl bg-brand py-2.5 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50"
                            :disabled="variantSubmitting"
                            @click="submitVariant"
                        >
                            {{ variantSubmitting ? 'Menyimpan...' : editingVariant ? 'Update Variant' : 'Tambah Variant' }}
                        </button>
                        <button v-if="editingVariant" type="button" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50" @click="editingVariant = null; variantForm = { name: '', price: '', sku: '', barcode: '', is_active: true }">Batal Edit</button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
