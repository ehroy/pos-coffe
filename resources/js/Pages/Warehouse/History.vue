<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useWarehouseMenu } from '@/Composables/useMenuItems';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    movements: Object,
});

const menuItems = useWarehouseMenu();

const typeClass = (type) => ({
    in:         'bg-green-100 text-green-700',
    out:        'bg-red-100 text-red-700',
    adjustment: 'bg-blue-100 text-blue-700',
}[type] ?? 'bg-gray-100 text-gray-700');

const typeLabel = (type) => ({ in: 'Masuk', out: 'Keluar', adjustment: 'Penyesuaian' }[type] ?? type);

const formatDate = (d) => new Date(d).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
</script>

<template>
    <Head title="Riwayat Stok" />

    <AppLayout title="Riwayat Stok" :menu-items="menuItems">
        <div class="mb-6 rounded-xl bg-gradient-to-br from-coffee-900 to-coffee-700 p-6 text-white shadow-lg">
            <h2 class="text-2xl font-bold">Riwayat Pergerakan Stok</h2>
            <p class="mt-1 text-coffee-200">Semua catatan stok masuk, keluar, dan penyesuaian.</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Bahan Baku</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Tipe</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Qty</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Sebelum</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600">Sesudah</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Oleh</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="m in movements.data" :key="m.id" class="border-t border-gray-100 hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-slate-800">{{ m.ingredient?.name }}</p>
                            <p v-if="m.note" class="text-xs text-gray-400">{{ m.note }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold" :class="typeClass(m.type)">{{ typeLabel(m.type) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold" :class="m.type === 'in' ? 'text-green-600' : m.type === 'out' ? 'text-red-600' : 'text-blue-600'">
                            {{ m.type === 'in' ? '+' : m.type === 'out' ? '-' : '~' }}{{ m.qty }} {{ m.ingredient?.unit }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500">{{ m.before_stock }}</td>
                        <td class="px-4 py-3 text-right font-medium text-slate-800">{{ m.after_stock }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ m.creator?.name || 'System' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400">{{ formatDate(m.created_at) }}</td>
                    </tr>
                    <tr v-if="!movements.data?.length">
                        <td colspan="7" class="px-4 py-10 text-center text-gray-500">Belum ada riwayat pergerakan stok.</td>
                    </tr>
                </tbody>
            </table>

            <div v-if="movements.last_page > 1" class="flex items-center justify-between border-t border-gray-100 px-4 py-3">
                <p class="text-sm text-gray-500">{{ movements.from }}–{{ movements.to }} dari {{ movements.total }}</p>
                <div class="flex gap-2">
                    <Link v-if="movements.prev_page_url" :href="movements.prev_page_url" class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm hover:bg-gray-50">Sebelumnya</Link>
                    <Link v-if="movements.next_page_url" :href="movements.next_page_url" class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm hover:bg-gray-50">Berikutnya</Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
