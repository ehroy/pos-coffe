<script setup>
const props = defineProps({
    modelValue: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const expenseCategories = [
    { value: 'utilities',  label: 'Utilitas',    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z"/>`, color: 'text-yellow-700 bg-yellow-50 border-yellow-200', active: 'bg-yellow-500 text-white border-yellow-500' },
    { value: 'salary',     label: 'Gaji',         icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>`, color: 'text-blue-700 bg-blue-50 border-blue-200', active: 'bg-blue-500 text-white border-blue-500' },
    { value: 'rent',       label: 'Sewa',         icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>`, color: 'text-purple-700 bg-purple-50 border-purple-200', active: 'bg-purple-500 text-white border-purple-500' },
    { value: 'supplies',   label: 'Bahan Baku',   icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>`, color: 'text-green-700 bg-green-50 border-green-200', active: 'bg-green-500 text-white border-green-500' },
    { value: 'equipment',  label: 'Peralatan',    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>`, color: 'text-slate-700 bg-slate-50 border-slate-200', active: 'bg-slate-600 text-white border-slate-600' },
    { value: 'marketing',  label: 'Marketing',    icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>`, color: 'text-pink-700 bg-pink-50 border-pink-200', active: 'bg-pink-500 text-white border-pink-500' },
    { value: 'other',      label: 'Lainnya',      icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>`, color: 'text-gray-600 bg-gray-50 border-gray-200', active: 'bg-gray-500 text-white border-gray-500' },
];

const isActive = (val) => props.modelValue === val;
const select = (val) => emit('update:modelValue', val);
</script>

<template>
    <div class="flex flex-wrap gap-2">
        <button
            v-for="cat in expenseCategories"
            :key="cat.value"
            type="button"
            class="flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium transition-all"
            :class="isActive(cat.value)
                ? cat.active + ' shadow-sm'
                : cat.color + ' hover:opacity-80'"
            @click="select(cat.value)"
        >
            <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="cat.icon"></svg>
            </span>
            <span>{{ cat.label }}</span>
        </button>
    </div>
</template>
