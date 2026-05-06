<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import Header from '@/Components/Header.vue';

defineProps({
    title: {
        type: String,
        default: 'Dashboard'
    },
    menuItems: {
        type: Array,
        default: () => []
    }
});
</script>

<template>
    <div class="flex min-h-screen bg-gray-50">
        <!-- Sidebar -->
        <Sidebar :menu-items="menuItems" />
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col ml-64">
            <!-- Header -->
            <Header :title="title" />
            
            <!-- Page Content -->
            <main class="flex-1 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
