<script setup>
import { ref } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import Header from '@/Components/Header.vue';

defineProps({
    title: { type: String, default: 'Dashboard' },
    menuItems: { type: Array, default: () => [] },
});

const sidebarOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-slate-50 font-sans antialiased">
        <!-- Sidebar -->
        <Sidebar :menu-items="menuItems" :open="sidebarOpen" @close="sidebarOpen = false" />

        <!-- Overlay mobile -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="sidebarOpen"
                class="fixed inset-0 z-30 bg-black/50 backdrop-blur-sm md:hidden"
                @click="sidebarOpen = false"
            />
        </Transition>

        <!-- Main -->
        <div class="flex min-h-screen flex-col md:pl-64">
            <Header :title="title" @toggle-sidebar="sidebarOpen = true" />
            <main class="flex-1 p-4 md:p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
