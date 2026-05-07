<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useOwnerMenu } from '@/Composables/useMenuItems';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({}),
    },
});

const menuItems = useOwnerMenu();

const form = useForm({
    customer_online_payment_enabled: !!props.settings.customer_online_payment_enabled,
    payment_gateway_provider: props.settings.payment_gateway_provider ?? 'midtrans',
    payment_gateway_client_key: props.settings.payment_gateway_client_key ?? '',
    payment_gateway_secret_key: props.settings.payment_gateway_secret_key ?? '',
    payment_gateway_merchant_id: props.settings.payment_gateway_merchant_id ?? '',
    printer_name: props.settings.printer_name ?? '',
    printer_ip: props.settings.printer_ip ?? '',
    printer_port: props.settings.printer_port ?? '9100',
    tax_enabled: !!props.settings.tax_enabled,
    tax_rate: props.settings.tax_rate ?? '11',
    service_charge_enabled: !!props.settings.service_charge_enabled,
    service_charge_rate: props.settings.service_charge_rate ?? '0',
    integration_enabled: !!props.settings.integration_enabled,
    integration_webhook_url: props.settings.integration_webhook_url ?? '',
    integration_api_key: props.settings.integration_api_key ?? '',
    integration_secret_key: props.settings.integration_secret_key ?? '',
});

const submit = () => {
    form.patch(route('owner.settings.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Owner Settings" />

    <AppLayout title="Owner Settings" :menu-items="menuItems">
        <div class="mb-6 rounded-2xl bg-gradient-to-br from-coffee-900 via-coffee-800 to-coffee-700 p-6 text-white shadow-lg">
            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/80">Owner Settings</p>
            <h1 class="mt-2 text-2xl font-bold">Payment Gateway & Integration</h1>
            <p class="mt-2 max-w-2xl text-sm text-coffee-100/80">
                Kelola QRIS customer, payment gateway, dan key integrasi dalam satu tempat.
            </p>
        </div>

        <form class="grid gap-6 lg:grid-cols-2" @submit.prevent="submit">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Customer Payment</h2>
                        <p class="mt-1 text-sm text-gray-500">Kontrol metode pembayaran customer di checkout meja.</p>
                    </div>
                    <label class="inline-flex cursor-pointer items-center gap-3 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">
                        <input v-model="form.customer_online_payment_enabled" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-brand" />
                        QRIS aktif
                    </label>
                </div>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Payment Gateway Provider</label>
                        <select v-model="form.payment_gateway_provider" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-brand">
                            <option value="midtrans">Midtrans</option>
                            <option value="xendit">Xendit</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Merchant ID</label>
                        <input v-model="form.payment_gateway_merchant_id" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-brand" placeholder="MID-xxxx" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Client Key</label>
                        <input v-model="form.payment_gateway_client_key" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-brand" placeholder="Client key" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Secret Key</label>
                        <input v-model="form.payment_gateway_secret_key" type="password" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-brand" placeholder="Secret key" />
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Printer Setting</h2>
                        <p class="mt-1 text-sm text-gray-500">Konfigurasi printer thermal untuk struk.</p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Printer Name</label>
                        <input v-model="form.printer_name" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-brand" placeholder="EPSON TM-T82" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Printer IP</label>
                            <input v-model="form.printer_ip" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-brand" placeholder="192.168.1.50" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Port</label>
                            <input v-model="form.printer_port" type="number" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-brand" placeholder="9100" />
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Tax & Service</h2>
                        <p class="mt-1 text-sm text-gray-500">Atur pajak dan service charge untuk transaksi.</p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800">Tax Enabled</p>
                            <p class="text-xs text-gray-500">Aktifkan perhitungan pajak otomatis</p>
                        </div>
                        <input v-model="form.tax_enabled" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-brand" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Tax Rate (%)</label>
                        <input v-model="form.tax_rate" type="number" step="0.01" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-brand" placeholder="11" />
                    </div>

                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-slate-800">Service Charge Enabled</p>
                            <p class="text-xs text-gray-500">Tambahkan biaya layanan pada transaksi</p>
                        </div>
                        <input v-model="form.service_charge_enabled" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-brand" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Service Charge Rate (%)</label>
                        <input v-model="form.service_charge_rate" type="number" step="0.01" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-brand" placeholder="5" />
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">Integration Keys</h2>
                        <p class="mt-1 text-sm text-gray-500">Webhook dan API key untuk integrasi external.</p>
                    </div>
                    <label class="inline-flex cursor-pointer items-center gap-3 rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">
                        <input v-model="form.integration_enabled" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brand focus:ring-brand" />
                        Integration aktif
                    </label>
                </div>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Webhook URL</label>
                        <input v-model="form.integration_webhook_url" type="url" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-brand" placeholder="https://example.com/webhook" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">API Key</label>
                        <input v-model="form.integration_api_key" type="text" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-brand" placeholder="API key" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Secret Key</label>
                        <input v-model="form.integration_secret_key" type="password" class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none focus:border-brand" placeholder="Secret key" />
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <p class="font-semibold">Catatan</p>
                    <p class="mt-1 leading-6">Simpan nilai sensitif hanya jika environment Anda siap. Bisa diganti kapan saja dari halaman ini.</p>
                </div>
            </section>

            <div class="lg:col-span-2 flex justify-end">
                <button
                    type="submit"
                    class="rounded-full bg-brand px-6 py-3 font-semibold text-white hover:bg-brand-dark disabled:opacity-50"
                    :disabled="form.processing"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                </button>
            </div>
        </form>
    </AppLayout>
</template>
