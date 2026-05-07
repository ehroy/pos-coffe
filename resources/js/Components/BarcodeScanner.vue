<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

const emit = defineEmits(['detected', 'close']);

const videoRef = ref(null);
const isScanning = ref(false);
const error = ref('');
let codeReader = null;
let stream = null;

onMounted(async () => {
    try {
        const { BrowserMultiFormatReader } = await import('@zxing/browser');
        codeReader = new BrowserMultiFormatReader();

        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });

        if (videoRef.value) {
            videoRef.value.srcObject = stream;
            videoRef.value.play();
            isScanning.value = true;

            codeReader.decodeFromVideoElement(videoRef.value, (result, err) => {
                if (result) {
                    emit('detected', result.getText());
                }
            });
        }
    } catch (e) {
        error.value = 'Kamera tidak dapat diakses. Pastikan izin kamera sudah diberikan.';
    }
});

onBeforeUnmount(() => {
    if (stream) {
        stream.getTracks().forEach(t => t.stop());
    }
    if (codeReader) {
        try { codeReader.reset(); } catch {}
    }
});
</script>

<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80" @click.self="$emit('close')">
            <div class="w-full max-w-sm rounded-2xl bg-white overflow-hidden shadow-2xl">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-slate-800">Scan Barcode</h3>
                        <p class="text-xs text-gray-500">Arahkan kamera ke barcode produk</p>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-600" @click="$emit('close')">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="relative bg-black aspect-square">
                    <video ref="videoRef" class="h-full w-full object-cover" muted playsinline></video>

                    <div v-if="isScanning" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="relative h-48 w-48">
                            <div class="absolute top-0 left-0 h-8 w-8 border-t-4 border-l-4 border-brand rounded-tl-lg"></div>
                            <div class="absolute top-0 right-0 h-8 w-8 border-t-4 border-r-4 border-brand rounded-tr-lg"></div>
                            <div class="absolute bottom-0 left-0 h-8 w-8 border-b-4 border-l-4 border-brand rounded-bl-lg"></div>
                            <div class="absolute bottom-0 right-0 h-8 w-8 border-b-4 border-r-4 border-brand rounded-br-lg"></div>
                            <div class="absolute top-1/2 left-0 right-0 h-0.5 bg-brand/60 animate-pulse"></div>
                        </div>
                    </div>

                    <div v-if="error" class="absolute inset-0 flex items-center justify-center bg-black/70 p-4 text-center">
                        <div>
                            <svg class="mx-auto mb-2 h-10 w-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-sm text-white">{{ error }}</p>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-4 text-center">
                    <p class="text-sm text-gray-500">Scan otomatis saat barcode terdeteksi</p>
                    <button type="button" class="mt-3 w-full rounded-xl border border-gray-200 py-2.5 text-sm text-gray-600 hover:bg-gray-50" @click="$emit('close')">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>
