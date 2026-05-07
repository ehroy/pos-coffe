<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAdminMenu } from '@/Composables/useMenuItems';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import axios from 'axios';

const menuItems = useAdminMenu();

const reportType = ref('daily');
const selectedDate = ref(new Date().toISOString().split('T')[0]);
const customFrom = ref('');
const customTo = ref('');
const isLoading = ref(false);
const report = ref(null);
const errorMessage = ref('');

const formatCurrency = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value || 0));

const fetchReport = async () => {
    isLoading.value = true;
    errorMessage.value = '';

    try {
        const params = { type: reportType.value, date: selectedDate.value };
        if (reportType.value === 'custom') {
            params.from = customFrom.value;
            params.to = customTo.value;
        }

        const response = await axios.get('/api/admin/reports/sales', { params });
        report.value = response.data;
    } catch (error) {
        errorMessage.value = error.response?.data?.message || 'Gagal memuat laporan.';
    } finally {
        isLoading.value = false;
    }
};

onMounted(fetchReport);
watch(reportType, fetchReport);
watch(selectedDate, fetchReport);

const profitColor = computed(() => {
    if (!report.value) return 'text-gray-700';
    return report.value.gross_profit >= 0 ? 'text-green-700' : 'text-red-700';
});

const exportParams = computed(() => {
    if (reportType.value === 'custom') {
        return `type=custom&from=${customFrom.value}&to=${customTo.value}`;
    }
    return `type=${reportType.value}&date=${selectedDate.value}`;
});

const exportPdfUrl = computed(() =>
    route('admin.export.sales.pdf') + '?' + exportParams.value
);

const exportCsvUrl = computed(() =>
    route('admin.export.sales.csv') + '?' + exportParams.value
);
</script>

<template>
    <Head title="Laporan Penjualan" />

    <AppLayout title="Laporan Penjualan" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">Laporan Penjualan</h2>
                    <p class="mt-1 text-coffee-200">Analisis performa penjualan dan keuangan.</p>
                </div>
                <div class="flex gap-2" v-if="report">
                    <a
                        :href="exportPdfUrl"
                        target="_blank"
                        class="flex items-center gap-1.5 rounded-xl bg-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/30"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        PDF
                    </a>
                    <a
                        :href="exportCsvUrl"
                        class="flex items-center gap-1.5 rounded-xl bg-white/20 px-4 py-2 text-sm font-semibold text-white hover:bg-white/30"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        CSV
                    </a>
                </div>
            </div>
        </div>

        <div class="mb-6 flex flex-wrap gap-3">
            <div class="flex gap-2">
                <button
                    v-for="type in ['daily', 'weekly', 'monthly', 'custom']"
                    :key="type"
                    type="button"
                    class="rounded-xl px-4 py-2 text-sm font-medium capitalize transition"
                    :class="reportType === type ? 'bg-brand text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
                    @click="reportType = type"
                >
                    {{ type === 'daily' ? 'Harian' : type === 'weekly' ? 'Mingguan' : type === 'monthly' ? 'Bulanan' : 'Custom' }}
                </button>
            </div>

            <input
                v-if="reportType !== 'custom'"
                v-model="selectedDate"
                type="date"
                class="rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand"
            />

            <template v-if="reportType === 'custom'">
                <input v-model="customFrom" type="date" class="rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" />
                <span class="self-center text-gray-500">s/d</span>
                <input v-model="customTo" type="date" class="rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" />
                <button type="button" class="rounded-xl bg-brand px-4 py-2 text-sm text-white hover:bg-brand-dark" @click="fetchReport">Tampilkan</button>
            </template>
        </div>

        <div v-if="errorMessage" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ errorMessage }}
        </div>

        <div v-if="isLoading" class="py-16 text-center text-gray-500">Memuat laporan...</div>

        <div v-else-if="report">
            <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Total Penjualan</p>
                    <p class="mt-1 text-xl font-bold text-slate-800">{{ formatCurrency(report.total_sales) }}</p>
                    <p class="text-xs text-gray-400">{{ report.total_orders }} transaksi</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Biaya Produk</p>
                    <p class="mt-1 text-xl font-bold text-slate-800">{{ formatCurrency(report.product_cost) }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Gross Profit</p>
                    <p class="mt-1 text-xl font-bold" :class="profitColor">{{ formatCurrency(report.gross_profit) }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm text-gray-500">Total Diskon</p>
                    <p class="mt-1 text-xl font-bold text-slate-800">{{ formatCurrency(report.total_discount) }}</p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h3 class="mb-4 font-semibold text-slate-800">Produk Terlaris</h3>
                    <div v-if="report.best_sellers?.length" class="space-y-3">
                        <div
                            v-for="(item, index) in report.best_sellers"
                            :key="item.product_id"
                            class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2"
                        >
                            <div class="flex items-center gap-3">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand text-xs font-bold text-white">{{ index + 1 }}</span>
                                <span class="text-sm font-medium text-slate-800">{{ item.product_name }}</span>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-800">{{ item.total_qty }} pcs</p>
                                <p class="text-xs text-gray-500">{{ formatCurrency(item.total_revenue) }}</p>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500">Belum ada data.</p>
                </div>

                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h3 class="mb-4 font-semibold text-slate-800">Penjualan per Hari</h3>
                    <div v-if="report.sales_by_day?.length" class="space-y-2">
                        <div
                            v-for="day in report.sales_by_day"
                            :key="day.date"
                            class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm"
                        >
                            <span class="text-gray-600">{{ day.date }}</span>
                            <div class="text-right">
                                <p class="font-semibold text-slate-800">{{ formatCurrency(day.total_sales) }}</p>
                                <p class="text-xs text-gray-500">{{ day.total_orders }} order</p>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-sm text-gray-500">Belum ada data.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
