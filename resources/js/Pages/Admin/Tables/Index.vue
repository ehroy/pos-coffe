<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAdminMenu } from '@/Composables/useMenuItems';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    tables: Array,
});

const menuItems = useAdminMenu();

const page = usePage();
const showForm = ref(false);
const form = ref({ name: '', code: '', is_active: true });
const isSubmitting = ref(false);

const flashSuccess = computed(() => page.props.flash?.success || '');

const submitTable = () => {
    isSubmitting.value = true;
    router.post(route('admin.tables.store'), form.value, {
        preserveScroll: true,
        onSuccess: () => {
            showForm.value = false;
            form.value = { name: '', code: '', is_active: true };
            isSubmitting.value = false;
        },
        onError: () => { isSubmitting.value = false; },
    });
};

const toggleActive = (table) => {
    router.patch(route('admin.tables.toggle', table.id), {}, { preserveScroll: true });
};

const getQrUrl = (table) => `/table/${table.qr_token}`;
</script>

<template>
    <Head title="Manajemen Meja" />

    <AppLayout title="Manajemen Meja" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Manajemen Meja</h2>
                    <p class="mt-1 text-coffee-200">Kelola meja dan QR code untuk order pelanggan.</p>
                </div>
                <button type="button" class="rounded-xl bg-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/30" @click="showForm = !showForm">
                    + Tambah Meja
                </button>
            </div>
        </div>

        <div v-if="flashSuccess" class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ flashSuccess }}
        </div>

        <div v-if="showForm" class="mb-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Tambah Meja Baru</h3>
            <div class="grid gap-3 sm:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Nama Meja</label>
                    <input v-model="form.name" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="Meja 1" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Kode (opsional)</label>
                    <input v-model="form.code" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="T001" />
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="form.is_active" type="checkbox" class="rounded" />
                        Aktif
                    </label>
                </div>
            </div>
            <div class="mt-4 flex gap-3">
                <button type="button" class="rounded-xl bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50" :disabled="isSubmitting" @click="submitTable">
                    {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
                </button>
                <button type="button" class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50" @click="showForm = false">Batal</button>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <div
                v-for="table in tables"
                :key="table.id"
                class="rounded-xl border bg-white p-4 shadow-sm transition"
                :class="table.is_active ? 'border-slate-200' : 'border-gray-200 opacity-60'"
            >
                <div class="mb-3 flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-slate-800">{{ table.name }}</p>
                        <p class="text-xs text-gray-500">{{ table.code }}</p>
                    </div>
                    <span
                        class="rounded-full px-2 py-1 text-xs font-medium"
                        :class="table.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                    >
                        {{ table.is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                <div class="mb-3 rounded-xl bg-slate-50 p-2 text-center">
                    <img
                        :src="route('admin.qr.show', table.id)"
                        :alt="`QR ${table.name}`"
                        class="mx-auto h-24 w-24"
                        loading="lazy"
                    />
                    <p class="mt-1 break-all text-xs text-brand">{{ getQrUrl(table) }}</p>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <a
                        :href="getQrUrl(table)"
                        target="_blank"
                        class="rounded-xl border border-brand/40 px-3 py-1.5 text-center text-xs font-medium text-brand hover:bg-brand/10"
                    >
                        Buka Menu
                    </a>
                    <a
                        :href="route('admin.qr.pdf', table.id)"
                        target="_blank"
                        class="rounded-xl border border-brand/40 px-3 py-1.5 text-center text-xs font-medium text-brand hover:bg-brand/10"
                    >
                        Download QR
                    </a>
                    <button
                        type="button"
                        class="col-span-2 rounded-xl border px-3 py-1.5 text-xs font-medium transition"
                        :class="table.is_active ? 'border-red-200 text-red-600 hover:bg-red-50' : 'border-green-200 text-green-600 hover:bg-green-50'"
                        @click="toggleActive(table)"
                    >
                        {{ table.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </div>
            </div>
        </div>

        <div v-if="tables.length === 0" class="rounded-2xl border border-dashed border-[#d8c7b3] py-16 text-center text-gray-500">
            Belum ada meja. Tambahkan meja pertama.
        </div>

        <div v-if="tables.length > 0" class="mt-4 text-right">
            <a
                :href="route('admin.qr.bulk')"
                target="_blank"
                class="inline-flex items-center gap-2 rounded-xl bg-[#2d2419] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#3a2f22]"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download Semua QR (PDF)
            </a>
        </div>
    </AppLayout>
</template>
