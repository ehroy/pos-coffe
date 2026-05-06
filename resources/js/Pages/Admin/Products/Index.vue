<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    products: Array,
    stats: Object,
    categories: Array,
});

const menuItems = [
    { name: 'dashboard', label: 'Dashboard', href: route('admin.dashboard'), route: 'admin.dashboard' },
    { name: 'products', label: 'Products', href: route('admin.products'), route: 'admin.products' },
    { name: 'categories', label: 'Categories', href: route('admin.categories'), route: 'admin.categories' },
    { name: 'inventory', label: 'Inventory', href: route('admin.inventory'), route: 'admin.inventory' },
];

const getCategoryBadgeColor = (type) => {
    const colors = {
        food: 'bg-orange-100 text-orange-800',
        drink: 'bg-blue-100 text-blue-800',
        other: 'bg-gray-100 text-gray-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Products Management" />

    <AppLayout title="Products Management" :menu-items="menuItems">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-coffee-800">Manage Products</h2>
                <p class="text-gray-600 mt-1">View and manage your menu items and products</p>
            </div>
            <button class="px-6 py-3 bg-coffee-500 text-white rounded-lg hover:bg-coffee-600 transition-colors font-semibold flex items-center gap-2">
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
                <p class="text-3xl font-bold text-coffee-800">{{ stats.total_products }}</p>
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
                <h3 class="text-lg font-semibold text-coffee-800">All Products</h3>
                <div class="flex gap-2">
                    <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">All Categories</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="product in products"
                        :key="product.id"
                        class="border-2 border-gray-200 rounded-lg overflow-hidden hover:border-coffee-500 transition-all hover:shadow-lg"
                    >
                        <!-- Product Image -->
                        <div class="bg-gray-100 h-48 flex items-center justify-center">
                            <svg class="w-20 h-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h4 class="text-lg font-bold text-coffee-800 mb-1">{{ product.name }}</h4>
                                    <span
                                        class="inline-block px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="getCategoryBadgeColor(product.category.type)"
                                    >
                                        {{ product.category.name }}
                                    </span>
                                </div>
                                <span
                                    class="px-2 py-1 text-xs font-semibold rounded-full"
                                    :class="product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                >
                                    {{ product.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ product.description }}</p>

                            <div class="mb-3">
                                <p class="text-xs text-gray-500 mb-1">Base Price</p>
                                <p class="text-xl font-bold text-coffee-800">
                                    Rp {{ product.base_price.toLocaleString('id-ID') }}
                                </p>
                            </div>

                            <!-- Variants -->
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 mb-2">Variants ({{ product.variants.length }})</p>
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="variant in product.variants.slice(0, 3)"
                                        :key="variant.id"
                                        class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded"
                                    >
                                        {{ variant.name }}
                                    </span>
                                    <span
                                        v-if="product.variants.length > 3"
                                        class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded"
                                    >
                                        +{{ product.variants.length - 3 }} more
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <button class="flex-1 px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm font-semibold">
                                    Edit
                                </button>
                                <button class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                                <button class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="products.length === 0" class="text-center py-12">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="text-gray-500 font-medium">No products found</p>
                    <p class="text-sm text-gray-400 mt-1">Start by adding your first product</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
