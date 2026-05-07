<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useCashierMenu } from '@/Composables/useMenuItems';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    session: Object,
    recentSessions: Array,
});

const menuItems = useCashierMenu();

const openingCash = ref('');
const closingCash = ref('');
const isSubmitting = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const formatCurrency = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value || 0));

const hasActiveSession = computed(() => props.session && props.session.status === 'open');

const openSession = () => {
    if (!openingCash.value || Number(openingCash.value) < 0) {
        errorMessage.value = 'Masukkan jumlah kas awal yang valid.';
        return;
    }

    isSubmitting.value = true;
    errorMessage.value = '';

    router.post(route('cashier.cash-session.open'), { opening_cash: openingCash.value }, {
        preserveScroll: true,
        onSuccess: () => {
            openingCash.value = '';
            isSubmitting.value = false;
        },
        onError: (errors) => {
            errorMessage.value = Object.values(errors)[0] || 'Terjadi kesalahan.';
            isSubmitting.value = false;
        },
    });
};

const closeSession = () => {
    if (!closingCash.value || Number(closingCash.value) < 0) {
        errorMessage.value = 'Masukkan jumlah kas akhir yang valid.';
        return;
    }

    isSubmitting.value = true;
    errorMessage.value = '';

    router.post(route('cashier.cash-session.close'), { closing_cash: closingCash.value }, {
        preserveScroll: true,
        onSuccess: () => {
            closingCash.value = '';
            isSubmitting.value = false;
        },
        onError: (errors) => {
            errorMessage.value = Object.values(errors)[0] || 'Terjadi kesalahan.';
            isSubmitting.value = false;
        },
    });
};

const differenceClass = (diff) => {
    if (diff === null || diff === undefined) return 'text-gray-700';
    return Number(diff) >= 0 ? 'text-green-700' : 'text-red-700';
};

const formatDiff = (diff) => {
    if (diff === null || diff === undefined) return '-';
    const n = Number(diff);
    return (n >= 0 ? '+' : '') + 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(n));
};
</script>

<template>
    <Head title="Cash Session" />

    <AppLayout title="Cash Session" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <h2 class="text-2xl font-bold">Sesi Kas</h2>
            <p class="mt-1 text-coffee-200">Buka dan tutup sesi kas harian kasir.</p>
        </div>

        <div v-if="$page.props.flash?.success" class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ $page.props.flash.success }}
        </div>

        <div v-if="$page.props.flash?.error || errorMessage" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $page.props.flash?.error || errorMessage }}
        </div>

        <div v-if="hasActiveSession" class="mb-6 rounded-xl border border-green-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center gap-3">
                <span class="h-3 w-3 rounded-full bg-green-500 animate-pulse"></span>
                <h3 class="text-lg font-semibold text-slate-800">Sesi Kas Aktif</h3>
            </div>

            <div class="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-xs text-gray-500">Kas Awal</p>
                    <p class="mt-1 font-bold text-slate-800">{{ formatCurrency(session.opening_cash) }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-xs text-gray-500">Dibuka</p>
                    <p class="mt-1 text-sm font-medium text-slate-800">{{ new Date(session.opened_at).toLocaleString('id-ID') }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-xs text-gray-500">Status</p>
                    <p class="mt-1 font-bold text-green-600">Aktif</p>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4">
                <p class="mb-2 text-sm font-semibold text-slate-800">Tutup Sesi Kas</p>
                <div class="flex gap-3">
                    <input
                        v-model="closingCash"
                        type="number"
                        min="0"
                        placeholder="Jumlah kas akhir (Rp)"
                        class="flex-1 rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand"
                    />
                    <button
                        type="button"
                        class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50"
                        :disabled="isSubmitting"
                        @click="closeSession"
                    >
                        {{ isSubmitting ? 'Menutup...' : 'Tutup Sesi' }}
                    </button>
                </div>
            </div>
        </div>

        <div v-else class="mb-6 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-slate-800">Buka Sesi Kas Baru</h3>
            <p class="mb-4 text-sm text-gray-500">Masukkan jumlah uang kas yang ada di laci kasir saat ini.</p>

            <div class="flex gap-3">
                <input
                    v-model="openingCash"
                    type="number"
                    min="0"
                    placeholder="Jumlah kas awal (Rp)"
                    class="flex-1 rounded-xl border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand"
                />
                <button
                    type="button"
                    class="rounded-xl bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-dark disabled:opacity-50"
                    :disabled="isSubmitting"
                    @click="openSession"
                >
                    {{ isSubmitting ? 'Membuka...' : 'Buka Sesi' }}
                </button>
            </div>
        </div>

        <div v-if="recentSessions?.length" class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-gray-100 px-4 py-3">
                <h3 class="font-semibold text-slate-800">Riwayat Sesi Kas</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Dibuka</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Ditutup</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Kas Awal</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Kas Akhir</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Selisih</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="s in recentSessions" :key="s.id" class="border-t border-gray-100 hover:bg-slate-50">
                        <td class="px-4 py-3 text-gray-600">{{ new Date(s.opened_at).toLocaleString('id-ID') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ s.closed_at ? new Date(s.closed_at).toLocaleString('id-ID') : '-' }}</td>
                        <td class="px-4 py-3 text-right">{{ formatCurrency(s.opening_cash) }}</td>
                        <td class="px-4 py-3 text-right">{{ s.closing_cash ? formatCurrency(s.closing_cash) : '-' }}</td>
                        <td class="px-4 py-3 text-right font-semibold" :class="differenceClass(s.difference)">
                            {{ formatDiff(s.difference) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="rounded-full px-2 py-1 text-xs font-medium" :class="s.status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'">
                                {{ s.status === 'open' ? 'Aktif' : 'Ditutup' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
