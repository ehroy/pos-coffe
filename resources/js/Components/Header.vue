<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

defineEmits(['toggle-sidebar']);

defineProps({
    title: { type: String, default: 'Dashboard' },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
</script>

<template>
    <header class="sticky top-0 z-30 flex h-16 items-center border-b border-slate-100 bg-white/95 backdrop-blur-sm px-4 md:px-6">
        <div class="flex w-full items-center justify-between gap-4">
            <!-- Left: hamburger + title -->
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors md:hidden"
                    @click="$emit('toggle-sidebar')"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-lg font-semibold text-slate-800 md:text-xl">{{ title }}</h1>
            </div>

            <!-- Right: actions -->
            <div class="flex items-center gap-2">
                <!-- Notification bell -->
                <button
                    type="button"
                    class="relative flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </button>

                <!-- Divider -->
                <div class="h-6 w-px bg-slate-200"></div>

                <!-- User chip -->
                <Link
                    :href="route('profile.edit')"
                    class="flex items-center gap-2.5 rounded-xl px-3 py-1.5 hover:bg-slate-100 transition-colors"
                >
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-coffee-800 text-xs font-bold text-brand">
                        {{ user?.name?.charAt(0)?.toUpperCase() ?? 'U' }}
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium text-slate-700 leading-none">{{ user?.name }}</p>
                        <p class="text-xs text-slate-400 capitalize leading-none mt-0.5">{{ user?.role }}</p>
                    </div>
                    <svg class="h-4 w-4 text-slate-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </Link>
            </div>
        </div>
    </header>
</template>
