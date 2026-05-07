<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    total: {
        type: Number,
        required: true,
    },
    isOpen: {
        type: Boolean,
        default: false,
    },
    isSubmitting: {
        type: Boolean,
        default: false,
    },
    errorMessage: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['confirm', 'close']);

const paymentMode = ref('manual');
const paymentMethod = ref('cash');
const cashReceived = ref('');

const formatCurrency = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));

const changeAmount = computed(() => {
    const received = Number(cashReceived.value || 0);
    return Math.max(received - props.total, 0);
});

const isCashShort = computed(() => {
    if (paymentMethod.value !== 'cash' || paymentMode.value !== 'manual') return false;
    return Number(cashReceived.value || 0) < props.total;
});

const handleConfirm = () => {
    if (isCashShort.value) return;

    emit('confirm', {
        payment_mode: paymentMode.value,
        payment_method: paymentMethod.value,
        cash_received: paymentMethod.value === 'cash' && paymentMode.value === 'manual'
            ? Number(cashReceived.value)
            : props.total,
    });
};

const handleClose = () => {
    paymentMode.value = 'manual';
    paymentMethod.value = 'cash';
    cashReceived.value = '';
    emit('close');
};

const quickAmounts = computed(() => {
    const t = props.total;
    const round = (n) => Math.ceil(n / 1000) * 1000;
    return [round(t), round(t) + 5000, round(t) + 10000, round(t) + 20000].filter((v, i, arr) => arr.indexOf(v) === i);
});
</script>

<template>
    <Teleport to="body">
        <div v-if="isOpen" class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 sm:items-center" @click.self="handleClose">
            <div class="w-full max-w-md rounded-t-3xl bg-white p-6 shadow-2xl sm:rounded-3xl">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-[#2d2419]">Proses Pembayaran</h2>
                    <button type="button" class="text-gray-400 hover:text-gray-600" @click="handleClose">&#10005;</button>
                </div>

                <div v-if="errorMessage" class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ errorMessage }}
                </div>

                <div class="mb-4 rounded-2xl bg-[#f5f0e8] px-4 py-3">
                    <p class="text-sm text-gray-500">Total Pembayaran</p>
                    <p class="text-2xl font-bold text-[#2d2419]">Rp {{ formatCurrency(total) }}</p>
                </div>

                <div class="mb-4">
                    <p class="mb-2 text-sm font-semibold text-[#2d2419]">Mode Pembayaran</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            type="button"
                            class="rounded-2xl border p-3 text-sm font-medium transition"
                            :class="paymentMode === 'manual' ? 'border-[#8b6f47] bg-[#f5f0e8] text-[#8b6f47]' : 'border-gray-200 text-gray-600'"
                            @click="paymentMode = 'manual'; paymentMethod = 'cash'"
                        >
                            Manual
                        </button>
                        <button
                            type="button"
                            class="rounded-2xl border p-3 text-sm font-medium transition"
                            :class="paymentMode === 'automatic' ? 'border-[#8b6f47] bg-[#f5f0e8] text-[#8b6f47]' : 'border-gray-200 text-gray-600'"
                            @click="paymentMode = 'automatic'; paymentMethod = 'qris'"
                        >
                            Gateway
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <p class="mb-2 text-sm font-semibold text-[#2d2419]">Metode</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            v-for="method in (paymentMode === 'manual' ? ['cash', 'qris', 'transfer', 'ewallet'] : ['qris'])"
                            :key="method"
                            type="button"
                            class="rounded-2xl border p-3 text-sm font-medium capitalize transition"
                            :class="paymentMethod === method ? 'border-[#8b6f47] bg-[#f5f0e8] text-[#8b6f47]' : 'border-gray-200 text-gray-600'"
                            @click="paymentMethod = method"
                        >
                            {{ method.toUpperCase() }}
                        </button>
                    </div>
                </div>

                <div v-if="paymentMethod === 'cash' && paymentMode === 'manual'" class="mb-4">
                    <p class="mb-2 text-sm font-semibold text-[#2d2419]">Uang Diterima</p>
                    <input
                        v-model="cashReceived"
                        type="number"
                        placeholder="0"
                        class="w-full rounded-2xl border border-[#e7ddcf] bg-[#fbf7f1] px-4 py-3 text-lg font-bold outline-none transition focus:border-[#8b6f47]"
                    />
                    <div class="mt-2 flex flex-wrap gap-2">
                        <button
                            v-for="amount in quickAmounts"
                            :key="amount"
                            type="button"
                            class="rounded-xl border border-[#d4c0a0] px-3 py-1.5 text-xs font-medium text-[#2d2419] hover:bg-[#f5f0e8]"
                            @click="cashReceived = amount"
                        >
                            Rp {{ formatCurrency(amount) }}
                        </button>
                    </div>
                    <div v-if="Number(cashReceived) > 0" class="mt-3 flex justify-between rounded-2xl bg-green-50 px-4 py-3 text-sm">
                        <span class="text-gray-500">Kembalian</span>
                        <span class="font-bold text-green-700">Rp {{ formatCurrency(changeAmount) }}</span>
                    </div>
                </div>

                <button
                    type="button"
                    class="w-full rounded-2xl bg-[#8b6f47] px-4 py-3 font-semibold text-white hover:bg-[#6b5538] disabled:opacity-50"
                    :disabled="isCashShort || isSubmitting"
                    @click="handleConfirm"
                >
                    {{ isSubmitting ? 'Memproses...' : 'Konfirmasi Pembayaran' }}
                </button>
            </div>
        </div>
    </Teleport>
</template>
