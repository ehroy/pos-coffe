<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAdminMenu } from '@/Composables/useMenuItems';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps({
    customerOnlinePaymentEnabled: Boolean,
});

const menuItems = useAdminMenu();

const toggleCustomerOnlinePayment = () => {
    router.patch(route('admin.settings.customer-online-payment'), {
        enabled: !props.customerOnlinePaymentEnabled,
    }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Settings" />

    <AppLayout title="Settings" :menu-items="menuItems">
        <div class="mb-6 rounded-2xl bg-gradient-to-br from-coffee-900 via-coffee-800 to-coffee-700 p-6 text-white shadow-lg">
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/80">System Settings</p>
            <h1 class="mt-2 text-2xl font-bold">QRIS Customer Payment</h1>
            <p class="mt-2 max-w-2xl text-sm text-coffee-100/80">
                Atur apakah customer boleh memilih pembayaran QRIS saat checkout meja.
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-semibold text-slate-800">Customer QRIS Payment</h2>
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold"
                                :class="customerOnlinePaymentEnabled ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                            >
                                {{ customerOnlinePaymentEnabled ? 'Active' : 'Disabled' }}
                            </span>
                        </div>
                        <p class="mt-2 max-w-xl text-sm leading-6 text-gray-500">
                            {{ customerOnlinePaymentEnabled ? 'Customer akan melihat opsi Bayar di Kasir dan QRIS di halaman checkout.' : 'Customer hanya akan melihat opsi Bayar di Kasir di halaman checkout.' }}
                        </p>
                    </div>

                    <button
                        type="button"
                        class="inline-flex items-center rounded-full px-4 py-2.5 text-sm font-semibold transition"
                        :class="customerOnlinePaymentEnabled ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-slate-200 text-slate-700 hover:bg-slate-300'"
                        @click="toggleCustomerOnlinePayment"
                    >
                        {{ customerOnlinePaymentEnabled ? 'Matikan QRIS' : 'Aktifkan QRIS' }}
                    </button>
                </div>

                <div class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-700">Dampak perubahan</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-600">
                        <li>• Checkout customer mengikuti status setting ini secara langsung.</li>
                        <li>• Admin bisa mengubah kapan saja tanpa edit file `.env`.</li>
                        <li>• Saat nonaktif, alur pembayaran tetap aman dengan opsi kasir.</li>
                    </ul>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-slate-800">Current Status</p>
                <div class="mt-4 rounded-2xl p-5" :class="customerOnlinePaymentEnabled ? 'bg-emerald-50' : 'bg-slate-50'">
                    <p class="text-sm text-gray-500">QRIS Customer</p>
                    <p class="mt-1 text-2xl font-bold" :class="customerOnlinePaymentEnabled ? 'text-emerald-700' : 'text-slate-700'">
                        {{ customerOnlinePaymentEnabled ? 'Enabled' : 'Disabled' }}
                    </p>
                    <p class="mt-2 text-sm text-gray-600">
                        {{ customerOnlinePaymentEnabled ? 'Menu checkout menampilkan Bayar di Kasir dan QRIS.' : 'Menu checkout menampilkan hanya Bayar di Kasir.' }}
                    </p>
                </div>

                <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <p class="font-semibold">Tip</p>
                    <p class="mt-1 leading-6">Gunakan QRIS saat ingin customer membayar langsung dari meja.</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
