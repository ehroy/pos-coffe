<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useOwnerMenu } from '@/Composables/useMenuItems';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    users: Array,
});

const menuItems = useOwnerMenu();

const getRoleBadgeColor = (role) => {
    const colors = {
        owner: 'bg-purple-100 text-purple-800',
        admin: 'bg-blue-100 text-blue-800',
        cashier: 'bg-green-100 text-green-800',
        kitchen: 'bg-orange-100 text-orange-800',
        warehouse: 'bg-gray-100 text-gray-800',
    };
    return colors[role] || 'bg-gray-100 text-gray-800';
};

const showCreateForm = ref(false);

const createForm = useForm({
    name: '',
    email: '',
    password: '',
    role: 'cashier',
    is_active: true,
});

const openCreateForm = () => {
    showCreateForm.value = true;
};

const closeCreateForm = () => {
    showCreateForm.value = false;
    createForm.reset('name', 'email', 'password');
    createForm.role = 'cashier';
    createForm.is_active = true;
};

const submitCreateUser = () => {
    createForm.post(route('owner.users.store'), {
        preserveScroll: true,
        onSuccess: closeCreateForm,
    });
};
</script>

<template>
    <Head title="Users Management" />

    <AppLayout title="Users Management" :menu-items="menuItems">
        <div v-if="showCreateForm" class="mb-6 rounded-xl border border-brand/30 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">Add New User</h3>
                    <p class="text-sm text-gray-500">Create account for cashier, kitchen, warehouse, admin, or owner.</p>
                </div>
                <button class="text-sm text-gray-500 hover:text-gray-800" @click="closeCreateForm">Close</button>
            </div>

            <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submitCreateUser">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                    <input v-model="createForm.name" type="text" class="w-full rounded-lg border border-gray-300 px-3 py-2" required />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                    <input v-model="createForm.email" type="email" class="w-full rounded-lg border border-gray-300 px-3 py-2" required />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                    <input v-model="createForm.password" type="password" class="w-full rounded-lg border border-gray-300 px-3 py-2" required />
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Role</label>
                    <select v-model="createForm.role" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="owner">Owner</option>
                        <option value="admin">Admin</option>
                        <option value="cashier">Cashier</option>
                        <option value="kitchen">Kitchen</option>
                        <option value="warehouse">Warehouse</option>
                    </select>
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
                    <button type="submit" class="rounded-lg bg-brand px-4 py-2 text-white hover:bg-brand-dark" :disabled="createForm.processing">Save User</button>
                </div>
            </form>
        </div>

        <!-- Header Actions -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Manage Users</h2>
                <p class="text-gray-600 mt-1">View and manage system users and their roles</p>
            </div>
            <button class="px-6 py-3 bg-brand text-white rounded-lg hover:bg-brand-dark transition-colors font-semibold" @click="openCreateForm">
                + Add New User
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Total Users</p>
                <p class="text-3xl font-bold text-slate-800">{{ users.length }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Active Users</p>
                <p class="text-3xl font-bold text-green-600">{{ users.filter(u => u.is_active).length }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Inactive Users</p>
                <p class="text-3xl font-bold text-red-600">{{ users.filter(u => !u.is_active).length }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600 mb-1">Roles</p>
                <p class="text-3xl font-bold text-blue-600">5</p>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-slate-800">All Users</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-brand rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ user.email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                    :class="getRoleBadgeColor(user.role)"
                                >
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                >
                                    {{ user.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button
                                    type="button"
                                    class="rounded-lg px-3 py-1 text-xs font-medium transition"
                                    :class="user.is_active ? 'border border-red-200 text-red-600 hover:bg-red-50' : 'border border-green-200 text-green-600 hover:bg-green-50'"
                                    @click="router.patch(route('owner.users.toggle', user.id), {}, { preserveScroll: true })"
                                >
                                    {{ user.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
