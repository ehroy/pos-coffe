<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'Dashboard'
    }
});
</script>

<template>
    <header class="bg-white border-b border-gray-200 px-6 py-4 sticky top-0 z-5">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-coffee-800">{{ title }}</h1>
            
            <div class="flex items-center gap-4">
                <!-- Notifications -->
                <button class="p-2 hover:bg-gray-100 rounded-lg relative">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <!-- Profile -->
                <Link :href="route('profile.edit')" class="flex items-center gap-2 hover:bg-gray-100 px-3 py-2 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </Link>

                <!-- Logout -->
                <Link :href="route('logout')" method="post" as="button" class="px-4 py-2 bg-coffee-500 text-white rounded-lg hover:bg-coffee-600 transition-colors">
                    Logout
                </Link>
            </div>
        </div>
    </header>
</template>
