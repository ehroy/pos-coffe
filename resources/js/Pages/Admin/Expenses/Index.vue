<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useAdminMenu } from '@/Composables/useMenuItems';
import ExpenseCategoryPicker from '@/Components/UI/ExpenseCategoryPicker.vue';
import { onMounted, ref } from 'vue';
import axios from 'axios';

const menuItems = useAdminMenu();

const expenses = ref([]);
const summary = ref({ total: 0, by_category: {} });
const isLoading = ref(false);
const showForm = ref(false);
const isSubmitting = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const filterFrom = ref(new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0]);
const filterTo = ref(new Date().toISOString().split('T')[0]);

const form = ref({
    title: '',
    category: '',
    amount: '',
    expense_date: new Date().toISOString().split('T')[0],
    note: '',
});



const formatCurrency = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value || 0));

const fetchExpenses = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/admin/reports/expenses', {
            params: { from: filterFrom.value, to: filterTo.value },
        });
        expenses.value = response.data.items || [];
        summary.value = { total: response.data.total, by_category: response.data.by_category };
    } catch (error) {
        errorMessage.value = 'Gagal memuat data pengeluaran.';
    } finally {
        isLoading.value = false;
    }
};

const submitExpense = async () => {
    isSubmitting.value = true;
    errorMessage.value = '';

    try {
        await axios.post(route('admin.expenses.store'), form.value);
        successMessage.value = 'Pengeluaran berhasil disimpan.';
        showForm.value = false;
        form.value = { title: '', category: '', amount: '', expense_date: new Date().toISOString().split('T')[0], note: '' };
        fetchExpenses();
    } catch (error) {
        errorMessage.value = error.response?.data?.message || Object.values(error.response?.data?.errors || {})[0]?.[0] || 'Terjadi kesalahan.';
    } finally {
        isSubmitting.value = false;
    }
};

onMounted(fetchExpenses);
</script>

<template>
    <Head title="Pengeluaran" />

    <AppLayout title="Pengeluaran" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Pengeluaran Operasional</h2>
                    <p class="mt-1 text-coffee-200">Catat dan pantau semua pengeluaran bisnis.</p>
                </div>
                <div class="flex gap-2">
                    <a
                        :href="route('admin.export.expenses.csv') + `?from=${filterFrom}&to=${filterTo}`"
                        class="rounded-xl bg-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/30"
                    >
                        Export CSV
                    </a>
                    <button
                        type="button"
                        class="rounded-xl bg-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/30"
                        @click="showForm = !showForm"
                    >
                        + Tambah
                    </button>
                </div>
            </div>
        </div>

        <div v-if="successMessage" class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ successMessage }}
        </div>

        <div v-if="showForm" class="mb-6 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="mb-4 font-semibold text-slate-800">Tambah Pengeluaran</h3>

            <div v-if="errorMessage" class="mb-3 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ errorMessage }}
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Judul</label>
                    <input v-model="form.title" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="Nama pengeluaran" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Kategori</label>
                    <ExpenseCategoryPicker v-model="form.category" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                    <input v-model="form.amount" type="number" min="0" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="0" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Tanggal</label>
                    <input v-model="form.expense_date" type="date" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" />
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-medium text-gray-700">Catatan (opsional)</label>
                    <textarea v-model="form.note" rows="2" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="Keterangan tambahan"></textarea>
                </div>
            </div>

            <div class="mt-4 flex gap-3">
                <button type="button" class="rounded-xl bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50" :disabled="isSubmitting" @click="submitExpense">
                    {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
                </button>
                <button type="button" class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50" @click="showForm = false">Batal</button>
            </div>
        </div>

        <div class="mb-4 flex flex-wrap items-center gap-3">
            <input v-model="filterFrom" type="date" class="rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" />
            <span class="text-gray-500">s/d</span>
            <input v-model="filterTo" type="date" class="rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" />
            <button type="button" class="rounded-xl bg-brand px-4 py-2 text-sm text-white hover:bg-brand-dark" @click="fetchExpenses">Filter</button>

            <div class="ml-auto rounded-xl bg-brand/10 px-4 py-2 text-sm">
                <span class="text-gray-500">Total: </span>
                <span class="font-bold text-slate-800">{{ formatCurrency(summary.total) }}</span>
            </div>
        </div>

        <div v-if="isLoading" class="py-10 text-center text-gray-500">Memuat data...</div>

        <div v-else class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Judul</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Kategori</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="expense in expenses" :key="expense.id" class="border-t border-gray-100 hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-800">{{ expense.title }}</p>
                            <p v-if="expense.note" class="text-xs text-gray-500">{{ expense.note }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 rounded-full bg-brand/10 px-2.5 py-1 text-xs font-medium text-brand capitalize">
                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="expense.category === 'utilities'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    <path v-else-if="expense.category === 'salary'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path v-else-if="expense.category === 'rent'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    <path v-else-if="expense.category === 'supplies'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    <path v-else-if="expense.category === 'equipment'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>
                                </svg>
                                {{ expense.category }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ expense.expense_date }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-slate-800">{{ formatCurrency(expense.amount) }}</td>
                    </tr>
                    <tr v-if="expenses.length === 0">
                        <td colspan="4" class="px-4 py-10 text-center text-gray-500">Belum ada data pengeluaran.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
