<script setup>
const props = defineProps({
    isOpen: { type: Boolean, default: false },
    receipt: { type: Object, default: null },
    orderId: { type: Number, default: null },
    orderNumber: { type: String, default: '' },
});

const emit = defineEmits(['close', 'new-order']);

const formatCurrency = (value) =>
    'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value || 0));

const printReceipt = () => {
    if (!props.orderId) {
        printFallback();
        return;
    }
    const url = route('cashier.orders.receipt', props.orderId);
    const win = window.open(url, '_blank', 'width=420,height=700,scrollbars=yes');
    if (win) {
        win.addEventListener('load', () => {
            setTimeout(() => win.print(), 500);
        });
    }
};

const downloadPdf = () => {
    if (!props.orderId) return;
    window.open(route('cashier.orders.receipt.pdf', props.orderId), '_blank');
};

const printFallback = () => {
    if (!props.receipt) return;
    const win = window.open('', '_blank', 'width=420,height=700');
    if (!win) return;
    win.document.write(`
        <!DOCTYPE html><html><head>
        <title>Receipt ${props.orderNumber}</title>
        <style>
          body { font-family: monospace; font-size: 12px; padding: 16px; width: 80mm; }
          .center { text-align: center; }
          .row { display: flex; justify-content: space-between; margin: 2px 0; }
          .divider { border-top: 1px dashed #000; margin: 6px 0; }
          .bold { font-weight: bold; }
          h2 { font-size: 16px; }
        </style>
        </head><body>
        <div class="center"><h2>COFFEE POS</h2></div>
        <div class="divider"></div>
        <div class="row"><span>Order</span><span class="bold">${props.orderNumber}</span></div>
        <div class="row"><span>Items</span><span>${props.receipt.itemCount}</span></div>
        <div class="divider"></div>
        <div class="row"><span>Total</span><span class="bold">${formatCurrency(props.receipt.total)}</span></div>
        ${props.receipt.method === 'cash' ? `
        <div class="row"><span>Bayar</span><span>${formatCurrency(props.receipt.amount)}</span></div>
        <div class="row bold"><span>Kembali</span><span>${formatCurrency(props.receipt.change)}</span></div>
        ` : ''}
        <div class="divider"></div>
        <div class="center"><p>Terima kasih!</p></div>
        </body></html>
    `);
    win.document.close();
    win.focus();
    setTimeout(() => win.print(), 300);
};
</script>

<template>
    <Teleport to="body">
        <div
            v-if="isOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"
            @click.self="emit('close')"
        >
            <div class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl">
                <div class="mb-4 text-center">
                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-[#2d2419]">Pembayaran Berhasil</h2>
                    <p v-if="orderNumber" class="mt-1 text-sm text-gray-500">{{ orderNumber }}</p>
                </div>

                <div v-if="receipt" class="mb-5 space-y-2 rounded-2xl bg-[#faf8f5] p-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Metode</span>
                        <span class="font-semibold uppercase">{{ receipt.method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total</span>
                        <span class="font-bold text-[#2d2419]">{{ formatCurrency(receipt.total) }}</span>
                    </div>
                    <template v-if="receipt.method === 'cash'">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Bayar</span>
                            <span>{{ formatCurrency(receipt.amount) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-2">
                            <span class="font-semibold text-[#2d2419]">Kembalian</span>
                            <span class="text-xl font-bold text-green-600">{{ formatCurrency(receipt.change) }}</span>
                        </div>
                    </template>
                </div>

                <div class="space-y-2">
                    <button
                        type="button"
                        class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#2d2419] px-4 py-3 font-semibold text-white hover:bg-[#3a2f22]"
                        @click="printReceipt"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Struk
                    </button>
                    <button
                        v-if="orderId"
                        type="button"
                        class="flex w-full items-center justify-center gap-2 rounded-2xl border border-[#d4c0a0] px-4 py-3 text-sm font-semibold text-[#8b6f47] hover:bg-[#f5f0e8]"
                        @click="downloadPdf"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download PDF
                    </button>
                    <button
                        type="button"
                        class="w-full rounded-2xl bg-[#8b6f47] px-4 py-3 font-semibold text-white hover:bg-[#6b5538]"
                        @click="emit('new-order')"
                    >
                        Order Baru
                    </button>
                    <button
                        type="button"
                        class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-sm text-gray-600 hover:bg-gray-50"
                        @click="emit('close')"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
