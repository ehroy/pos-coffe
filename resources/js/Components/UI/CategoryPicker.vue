<script setup>
const props = defineProps({
    modelValue: { type: [String, Number], default: '' },
    categories: { type: Array, required: true },
    allLabel: { type: String, default: 'Semua' },
    showAll: { type: Boolean, default: true },
    size: { type: String, default: 'md' },
});

const emit = defineEmits(['update:modelValue']);

const categoryIconMap = {
    coffee:     { icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zm4-7v3M12 1v3M8 1v3"/>`, color: 'text-amber-700 bg-amber-50 border-amber-200', active: 'bg-amber-600 text-white border-amber-600' },
    'non-coffee': { icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15m-6.3-12.082A24.3 24.3 0 0112 3c-.83 0-1.647.047-2.45.138M12 21a9 9 0 100-18 9 9 0 000 18z"/>`, color: 'text-teal-700 bg-teal-50 border-teal-200', active: 'bg-teal-600 text-white border-teal-600' },
    food:       { icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>`, color: 'text-orange-700 bg-orange-50 border-orange-200', active: 'bg-orange-600 text-white border-orange-600' },
    snack:      { icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21l-3.5-3.5M3 21l3.5-3.5M12 12a3 3 0 100-6 3 3 0 000 6z"/>`, color: 'text-yellow-700 bg-yellow-50 border-yellow-200', active: 'bg-yellow-600 text-white border-yellow-600' },
    drink:      { icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15m-6.3-12.082A24.3 24.3 0 0112 3c-.83 0-1.647.047-2.45.138M12 21a9 9 0 100-18 9 9 0 000 18z"/>`, color: 'text-blue-700 bg-blue-50 border-blue-200', active: 'bg-blue-600 text-white border-blue-600' },
    other:      { icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/>`, color: 'text-gray-600 bg-gray-50 border-gray-200', active: 'bg-gray-600 text-white border-gray-600' },
};

const allIcon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>`;

const getConfig = (category) => {
    const slug = category.slug ?? category.name?.toLowerCase().replace(/\s+/g, '-');
    const type = category.type ?? '';
    return categoryIconMap[slug] ?? categoryIconMap[type] ?? categoryIconMap.other;
};

const isActive = (id) => String(props.modelValue) === String(id);
const isAllActive = () => props.modelValue === '' || props.modelValue === null || props.modelValue === undefined;

const select = (id) => emit('update:modelValue', id);
</script>

<template>
    <div class="flex flex-wrap gap-2">
        <button
            v-if="showAll"
            type="button"
            class="flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium transition-all"
            :class="isAllActive()
                ? 'bg-slate-800 text-white border-slate-800 shadow-sm'
                : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300 hover:bg-slate-50'"
            @click="select('')"
        >
            <span class="flex h-5 w-5 items-center justify-center">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="allIcon"></svg>
            </span>
            {{ allLabel }}
        </button>

        <button
            v-for="cat in categories"
            :key="cat.id ?? cat"
            type="button"
            class="flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium transition-all"
            :class="isActive(cat.id ?? cat)
                ? getConfig(cat).active + ' shadow-sm'
                : getConfig(cat).color + ' hover:opacity-80'"
            @click="select(cat.id ?? cat)"
        >
            <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="getConfig(cat).icon"></svg>
            </span>
            <span>{{ cat.name ?? cat }}</span>
        </button>
    </div>
</template>
