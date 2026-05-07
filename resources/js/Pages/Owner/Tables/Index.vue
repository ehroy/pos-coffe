<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useOwnerMenu } from '@/Composables/useMenuItems';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    tables: Array,
});

const menuItems = useOwnerMenu();

const showCreateForm = ref(false);
const copiedTableId = ref(null);

const createForm = useForm({
    code: '',
    name: '',
    is_active: true,
});

const openCreateForm = () => {
    showCreateForm.value = true;
};

const closeCreateForm = () => {
    showCreateForm.value = false;
    createForm.reset('code', 'name');
    createForm.is_active = true;
};

const submitCreateTable = () => {
    createForm.post(route('owner.tables.store'), {
        preserveScroll: true,
        onSuccess: closeCreateForm,
    });
};

const copyQrUrl = async (table) => {
    try {
        await navigator.clipboard.writeText(table.qr_url);
        copiedTableId.value = table.id;
        window.setTimeout(() => {
            if (copiedTableId.value === table.id) {
                copiedTableId.value = null;
            }
        }, 1500);
    } catch (error) {
        copiedTableId.value = null;
    }
};
</script>

<template>
    <Head title="Tables Management" />

    <AppLayout title="Tables Management" :menu-items="menuItems">
        <div v-if="showCreateForm" class="mb-6 rounded-xl border border-brand/30 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Add New Table</h3>
                    <p class="text-sm text-gray-500">Create table code and QR token.</p>
                </div>
                <button class="text-sm text-gray-500 hover:text-gray-800" @click="closeCreateForm">Close</button>
            </div>

            <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submitCreateTable">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Table Code</label>
                    <input v-model="createForm.code" type="text" placeholder="Optional, auto generated if empty" class="w-full rounded-lg border border-gray-300 px-3 py-2" />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Table Name</label>
                    <input v-model="createForm.name" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2" required />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select v-model="createForm.is_active" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option :value="true">Active</option>
                        <option :value="false">Inactive</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex items-center justify-end gap-3">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700" @click="closeCreateForm">Cancel</button>
                    <button type="submit" class="rounded-lg bg-brand px-4 py-2 text-white hover:bg-brand-dark" :disabled="createForm.processing">Save Table</button>
                </div>
            </form>
        </div>

        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Manage Tables</h2>
                <p class="text-gray-600 mt-1">View and manage restaurant tables with QR codes</p>
            </div>
            <button class="px-6 py-3 bg-brand text-white rounded-lg hover:bg-brand-dark transition-colors font-semibold" @click="openCreateForm">
                + Add New Table
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Total Tables</p>
                <p class="text-3xl font-bold text-slate-800">{{ tables.length }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Active Tables</p>
                <p class="text-3xl font-bold text-green-600">{{ tables.filter(t => t.is_active).length }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Inactive Tables</p>
                <p class="text-3xl font-bold text-red-600">{{ tables.filter(t => !t.is_active).length }}</p>
            </div>
        </div>

        <!-- Tables Grid -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-slate-800">All Tables</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        v-for="table in tables"
                        :key="table.id"
                        class="border-2 rounded-lg p-4 hover:border-brand transition-colors"
                        :class="table.is_active ? 'border-gray-200' : 'border-red-200 bg-red-50'"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs text-gray-500">{{ table.code }}</p>
                                <p class="text-lg font-bold text-slate-800">{{ table.name }}</p>
                            </div>
                            <span
                                class="px-2 py-1 text-xs font-semibold rounded-full"
                                :class="table.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                            >
                                {{ table.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="bg-gray-100 rounded-lg p-4 mb-3">
                            <p class="mb-2 text-xs font-semibold uppercase text-gray-500">QR Menu Link</p>
                            <a :href="table.qr_url" class="break-all text-sm font-medium text-brand-dark hover:underline" target="_blank" rel="noreferrer">
                                {{ table.qr_url }}
                            </a>
                        </div>

                        <div class="space-y-2">
                            <a :href="table.qr_url" target="_blank" rel="noreferrer" class="block w-full rounded-lg bg-blue-500 px-3 py-2 text-center text-sm text-white hover:bg-blue-600">
                                Buka Menu
                            </a>
                            <button type="button" class="w-full px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm" @click="copyQrUrl(table)">
                                {{ copiedTableId === table.id ? 'Copied' : 'Copy QR Link' }}
                            </button>
                            <button
                                type="button"
                                class="w-full rounded-lg px-3 py-2 text-sm font-medium transition"
                                :class="table.is_active ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100'"
                                @click="router.patch(route('owner.tables.toggle', table.id), {}, { preserveScroll: true })"
                            >
                                {{ table.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </div>

                        <p class="text-xs text-gray-500 mt-3 truncate">Token: {{ table.qr_token.substring(0, 12) }}...</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
