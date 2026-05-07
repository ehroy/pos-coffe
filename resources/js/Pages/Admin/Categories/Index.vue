<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { useAdminMenu } from '@/Composables/useMenuItems';
import { computed, ref } from 'vue';

const props = defineProps({
    categories: Array,
});

const menuItems = useAdminMenu();

const typeConfig = {
    food:  { label: 'Food',  color: 'bg-orange-100 text-orange-800', icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>` },
    drink: { label: 'Drink', color: 'bg-blue-100 text-blue-800',   icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zm4-7v3M12 1v3M8 1v3"/>` },
    other: { label: 'Other', color: 'bg-gray-100 text-gray-700',   icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>` },
};

const showCreateForm = ref(false);
const editingCategory = ref(null);
const deleteConfirm = ref(null);

const createForm = useForm({ name: '', type: 'food', is_active: true });

const editForm = useForm({ name: '', type: 'food', is_active: true });

const flashSuccess = computed(() => null);

const openEdit = (cat) => {
    editingCategory.value = cat;
    editForm.name      = cat.name;
    editForm.type      = cat.type;
    editForm.is_active = cat.is_active;
};

const closeEdit = () => {
    editingCategory.value = null;
    editForm.reset();
};

const submitCreate = () => {
    createForm.post(route('admin.categories.store'), {
        preserveScroll: true,
        onSuccess: () => { showCreateForm.value = false; createForm.reset(); },
    });
};

const submitEdit = () => {
    editForm.patch(route('admin.categories.update', editingCategory.value.id), {
        preserveScroll: true,
        onSuccess: closeEdit,
    });
};

const toggleActive = (cat) => {
    router.patch(route('admin.categories.toggle', cat.id), {}, { preserveScroll: true });
};

const confirmDelete = (cat) => { deleteConfirm.value = cat; };

const doDelete = () => {
    if (!deleteConfirm.value) return;
    router.delete(route('admin.categories.destroy', deleteConfirm.value.id), {
        preserveScroll: true,
        onFinish: () => { deleteConfirm.value = null; },
    });
};
</script>

<template>
    <Head title="Kategori" />

    <AppLayout title="Kategori" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Manajemen Kategori</h2>
                    <p class="mt-1 text-coffee-200">Kelola kategori produk menu.</p>
                </div>
                <button
                    type="button"
                    class="rounded-xl bg-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/30"
                    @click="showCreateForm = !showCreateForm"
                >
                    + Tambah Kategori
                </button>
            </div>
        </div>

        <div v-if="$page.props.flash?.success" class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $page.props.flash.error }}
        </div>

        <div v-if="showCreateForm" class="mb-6 rounded-xl border border-brand/30 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800">Tambah Kategori Baru</h3>
                <button class="text-sm text-gray-400 hover:text-gray-600" @click="showCreateForm = false">Tutup</button>
            </div>
            <form class="grid gap-3 sm:grid-cols-3" @submit.prevent="submitCreate">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
                    <input v-model="createForm.name" type="text" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" required />
                    <p v-if="createForm.errors.name" class="mt-1 text-xs text-red-600">{{ createForm.errors.name }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Tipe</label>
                    <select v-model="createForm.type" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
                        <option value="food">Food</option>
                        <option value="drink">Drink</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                    <select v-model="createForm.is_active" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
                        <option :value="true">Aktif</option>
                        <option :value="false">Nonaktif</option>
                    </select>
                </div>
                <div class="sm:col-span-3 flex justify-end gap-2">
                    <button type="button" class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50" @click="showCreateForm = false">Batal</button>
                    <button type="submit" class="rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50" :disabled="createForm.processing">Simpan</button>
                </div>
            </form>
        </div>

        <div class="mb-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm text-center">
                <p class="text-sm text-gray-500">Total</p>
                <p class="mt-1 text-2xl font-bold text-slate-800">{{ categories.length }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm text-center">
                <p class="text-sm text-gray-500">Food</p>
                <p class="mt-1 text-2xl font-bold text-orange-600">{{ categories.filter(c => c.type === 'food').length }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm text-center">
                <p class="text-sm text-gray-500">Drink</p>
                <p class="mt-1 text-2xl font-bold text-blue-600">{{ categories.filter(c => c.type === 'drink').length }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm text-center">
                <p class="text-sm text-gray-500">Aktif</p>
                <p class="mt-1 text-2xl font-bold text-green-600">{{ categories.filter(c => c.is_active).length }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="cat in categories"
                :key="cat.id"
                class="rounded-xl border bg-white p-5 shadow-sm transition"
                :class="cat.is_active ? 'border-slate-200' : 'border-gray-200 opacity-60'"
            >
                <div class="mb-3 flex items-start justify-between">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand/10">
                        <svg class="h-5 w-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="typeConfig[cat.type]?.icon ?? typeConfig.other.icon"></svg>
                    </div>
                    <span
                        class="rounded-full px-2 py-0.5 text-xs font-semibold"
                        :class="cat.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                    >
                        {{ cat.is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <h4 class="mb-1 font-bold text-slate-800">{{ cat.name }}</h4>
                <p class="mb-3 text-xs text-gray-400">{{ cat.slug }}</p>
                <span class="inline-block rounded-full px-2 py-0.5 text-xs font-semibold capitalize" :class="typeConfig[cat.type]?.color ?? typeConfig.other.color">
                    {{ typeConfig[cat.type]?.label ?? cat.type }}
                </span>

                <div class="mt-4 flex gap-2">
                    <button
                        type="button"
                        class="flex-1 rounded-lg bg-brand px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-dark"
                        @click="openEdit(cat)"
                    >
                        Edit
                    </button>
                    <button
                        type="button"
                        class="rounded-lg border px-3 py-1.5 text-xs font-medium transition"
                        :class="cat.is_active ? 'border-amber-200 text-amber-600 hover:bg-amber-50' : 'border-green-200 text-green-600 hover:bg-green-50'"
                        @click="toggleActive(cat)"
                    >
                        {{ cat.is_active ? 'Nonaktif' : 'Aktif' }}
                    </button>
                    <button
                        type="button"
                        class="rounded-lg border border-red-200 px-3 py-1.5 text-xs text-red-600 hover:bg-red-50"
                        @click="confirmDelete(cat)"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <div v-if="editingCategory" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="closeEdit">
                <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                    <h3 class="mb-4 font-bold text-slate-800">Edit Kategori — {{ editingCategory.name }}</h3>
                    <form class="space-y-3" @submit.prevent="submitEdit">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Nama</label>
                            <input v-model="editForm.name" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" required />
                            <p v-if="editForm.errors.name" class="mt-1 text-xs text-red-600">{{ editForm.errors.name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Tipe</label>
                            <select v-model="editForm.type" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
                                <option value="food">Food</option>
                                <option value="drink">Drink</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">Status</label>
                            <select v-model="editForm.is_active" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand">
                                <option :value="true">Aktif</option>
                                <option :value="false">Nonaktif</option>
                            </select>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="submit" class="flex-1 rounded-xl bg-brand py-2.5 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50" :disabled="editForm.processing">Simpan</button>
                            <button type="button" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm text-gray-600 hover:bg-gray-50" @click="closeEdit">Batal</button>
                        </div>
                    </form>
                </div>
            </div>

            <div v-if="deleteConfirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="deleteConfirm = null">
                <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
                    <h3 class="mb-2 font-bold text-slate-800">Hapus Kategori?</h3>
                    <p class="mb-5 text-sm text-gray-500">
                        Kategori <strong>{{ deleteConfirm.name }}</strong> akan dihapus permanen. Kategori yang masih memiliki produk tidak bisa dihapus.
                    </p>
                    <div class="flex gap-3">
                        <button type="button" class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white hover:bg-red-700" @click="doDelete">Hapus</button>
                        <button type="button" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm text-gray-600 hover:bg-gray-50" @click="deleteConfirm = null">Batal</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AppLayout>
</template>
