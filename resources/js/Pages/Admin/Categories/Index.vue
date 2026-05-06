<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    categories: Array,
});

const menuItems = [
    { name: 'dashboard', label: 'Dashboard', href: route('admin.dashboard'), route: 'admin.dashboard' },
    { name: 'products', label: 'Products', href: route('admin.products'), route: 'admin.products' },
    { name: 'categories', label: 'Categories', href: route('admin.categories'), route: 'admin.categories' },
    { name: 'inventory', label: 'Inventory', href: route('admin.inventory'), route: 'admin.inventory' },
];

const getTypeBadgeColor = (type) => {
    const colors = {
        food: 'bg-orange-100 text-orange-800',
        drink: 'bg-blue-100 text-blue-800',
        other: 'bg-gray-100 text-gray-800',
    };
    return colors[type] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Categories Management" />

    <AppLayout title="Categories Management" :menu-items="menuItems">
        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-coffee-800">Manage Categories</h2>
                <p class="text-gray-600 mt-1">Organize your products into categories</p>
            </div>
            <button class="px-6 py-3 bg-coffee-500 text-white rounded-lg hover:bg-coffee-600 transition-colors font-semibold">
                + Add New Category
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Total Categories</p>
                <p class="text-3xl font-bold text-coffee-800">{{ categories.length }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Food Categories</p>
                <p class="text-3xl font-bold text-orange-600">{{ categories.filter(c => c.type === 'food').length }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Drink Categories</p>
                <p class="text-3xl font-bold text-blue-600">{{ categories.filter(c => c.type === 'drink').length }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Active Categories</p>
                <p class="text-3xl font-bold text-green-600">{{ categories.filter(c => c.is_active).length }}</p>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-coffee-800">All Categories</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        v-for="category in categories"
                        :key="category.id"
                        class="border-2 border-gray-200 rounded-lg p-6 hover:border-coffee-500 transition-colors"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-coffee-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-coffee-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                :class="category.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                            >
                                {{ category.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <h4 class="text-lg font-bold text-coffee-800 mb-2">{{ category.name }}</h4>
                        <p class="text-sm text-gray-500 mb-3">{{ category.slug }}</p>

                        <span
                            class="inline-block px-3 py-1 text-xs font-semibold rounded-full capitalize mb-4"
                            :class="getTypeBadgeColor(category.type)"
                        >
                            {{ category.type }}
                        </span>

                        <div class="flex gap-2 mt-4">
                            <button class="flex-1 px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">
                                Edit
                            </button>
                            <button class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
