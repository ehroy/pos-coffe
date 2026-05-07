<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAdminMenu } from '@/Composables/useMenuItems';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    users: Array,
});

const menuItems = useAdminMenu();

const page = usePage();
const showForm = ref(false);
const isSubmitting = ref(false);
const form = ref({ name: '', email: '', password: '', role: 'cashier', is_active: true });

const roles = ['owner', 'admin', 'cashier', 'kitchen', 'warehouse'];
const flashSuccess = computed(() => page.props.flash?.success || '');

const submitUser = () => {
    isSubmitting.value = true;
    router.post(route('admin.users.store'), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            showForm.value = false;
            form.value = { name: '', email: '', password: '', role: 'cashier', is_active: true };
            isSubmitting.value = false;
        },
        onError: () => { isSubmitting.value = false; },
    });
};

const toggleActive = (user) => {
    router.patch(route('admin.users.toggle', user.id), {}, { preserveScroll: true });
};

const roleBadgeClass = (role) => {
    const map = {
        owner: 'bg-purple-100 text-purple-700',
        admin: 'bg-blue-100 text-blue-700',
        cashier: 'bg-green-100 text-green-700',
        kitchen: 'bg-orange-100 text-orange-700',
        warehouse: 'bg-yellow-100 text-yellow-700',
    };
    return map[role] || 'bg-gray-100 text-gray-700';
};
</script>

<template>
    <Head title="Manajemen User" />

    <AppLayout title="Manajemen User" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Manajemen User</h2>
                    <p class="mt-1 text-coffee-200">Kelola akun dan hak akses pengguna sistem.</p>
                </div>
                <button type="button" class="rounded-xl bg-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/30" @click="showForm = !showForm">
                    + Tambah User
                </button>
            </div>
        </div>

        <div v-if="flashSuccess" class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ flashSuccess }}
        </div>

        <div v-if="showForm" class="mb-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Tambah User Baru</h3>
            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
                    <input v-model="form.name" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="Nama lengkap" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                    <input v-model="form.email" type="email" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="email@example.com" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Password</label>
                    <input v-model="form.password" type="password" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="Min. 8 karakter" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Role</label>
                    <select v-model="form.role" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
                        <option v-for="role in roles" :key="role" :value="role" class="capitalize">{{ role }}</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded" />
                    <label for="is_active" class="text-sm text-gray-700">Aktif</label>
                </div>
            </div>
            <div class="mt-4 flex gap-3">
                <button type="button" class="rounded-xl bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50" :disabled="isSubmitting" @click="submitUser">
                    {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
                </button>
                <button type="button" class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50" @click="showForm = false">Batal</button>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Email</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Role</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" :key="user.id" class="border-t border-gray-100 hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ user.name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ user.email }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-medium capitalize" :class="roleBadgeClass(user.role)">{{ user.role }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-medium" :class="user.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">
                                {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button
                                type="button"
                                class="rounded-lg px-3 py-1 text-xs font-medium transition"
                                :class="user.is_active ? 'border border-red-200 text-red-600 hover:bg-red-50' : 'border border-green-200 text-green-600 hover:bg-green-50'"
                                @click="toggleActive(user)"
                            >
                                {{ user.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="users.length === 0">
                        <td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada user.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
