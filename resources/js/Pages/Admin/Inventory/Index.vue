<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    ingredients: Array,
    stats: Object,
    lowStockItems: Array,
    recentMovements: Array,
});

const menuItems = [
    { name: 'dashboard', label: 'Dashboard', href: route('admin.dashboard'), route: 'admin.dashboard' },
    { name: 'products', label: 'Products', href: route('admin.products'), route: 'admin.products' },
    { name: 'categories', label: 'Categories', href: route('admin.categories'), route: 'admin.categories' },
    { name: 'inventory', label: 'Inventory', href: route('admin.inventory'), route: 'admin.inventory' },
];

const getStockStatusColor = (status) => {
    const colors = {
        out_of_stock: 'bg-red-100 text-red-800',
        low_stock: 'bg-yellow-100 text-yellow-800',
        in_stock: 'bg-green-100 text-green-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStockStatusLabel = (status) => {
    const labels = {
        out_of_stock: 'Out of Stock',
        low_stock: 'Low Stock',
        in_stock: 'In Stock',
    };
    return labels[status] || 'Unknown';
};

const getMovementTypeColor = (type) => {
    const colors = {
        in: 'bg-green-100 text-green-800',
        out: 'bg-red-100 text-red-800',
        adjustment: 'bg-blue-100 text-blue-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Inventory Management" />

    <AppLayout title="Inventory Management" :menu-items="menuItems">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-coffee-800">Inventory Management</h2>
                <p class="text-gray-600 mt-1">Manage ingredients, stock levels, and recipes</p>
            </div>
            <div class="flex gap-2">
                <button class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Stock In
                </button>
                <button class="px-6 py-3 bg-coffee-500 text-white rounded-lg hover:bg-coffee-600 transition-colors font-semibold flex items-center gap-2">
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
                <p class="text-3xl font-bold text-coffee-800">{{ stats.total_ingredients }}</p>
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
                    <h3 class="text-lg font-semibold text-coffee-800">All Ingredients</h3>
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
                                    Rp {{ ingredient.cost_per_unit.toLocaleString('id-ID') }}
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
                                    <button class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                    <button class="text-green-600 hover:text-green-900">Stock In</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Movements -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-coffee-800">Recent Movements</h3>
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
</template>
