<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);

const props = defineProps({
    menuItems: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <aside class="w-64 bg-coffee-800 text-cream min-h-screen flex flex-col fixed left-0 top-0 bottom-0 z-10">
        <!-- Logo -->
        <div class="p-6 border-b border-coffee-700">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-coffee-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-cream" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M2 21h19v-3H2v3zM20 8H4V6h16v2zm0-5H4c-1.1 0-2 .9-2 2v3c0 1.1.9 2 2 2h1v1c0 2.76 2.24 5 5 5h4c2.76 0 5-2.24 5-5v-1h1c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-cream">Coffee POS</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <Link
                v-for="item in menuItems"
                :key="item.name"
                :href="item.href"
                :class="[
                    'flex items-center gap-3 px-4 py-3 rounded-lg transition-colors',
                    route().current(item.route)
                        ? 'bg-coffee-600 text-white'
                        : 'text-cream hover:bg-coffee-700'
                ]"
            >
                <component :is="item.icon" v-if="item.icon" class="w-5 h-5" />
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span>{{ item.label }}</span>
            </Link>
        </nav>

        <!-- User Info -->
        <div class="p-4 border-t border-coffee-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-coffee-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-semibold">{{ user?.name?.charAt(0) || 'U' }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ user?.name }}</p>
                    <p class="text-xs text-coffee-300 capitalize">{{ user?.role }}</p>
                </div>
            </div>
        </div>
    </aside>
</template>
