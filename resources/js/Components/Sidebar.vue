<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const emit = defineEmits(['close']);

const page = usePage();
const user = computed(() => page.props.auth?.user);

const props = defineProps({
    open: { type: Boolean, default: false },
    menuItems: { type: Array, default: () => [] },
});

const isActive = (item) => {
    try { return route().current(item.route); } catch { return false; }
};

const roleLabel = computed(() => {
    const map = {
        owner: 'Owner',
        admin: 'Admin',
        cashier: 'Kasir',
        kitchen: 'Kitchen',
        warehouse: 'Gudang',
    };
    return map[user.value?.role] ?? user.value?.role ?? '';
});

const roleColor = computed(() => {
    const map = {
        owner:     'bg-purple-500/20 text-purple-300',
        admin:     'bg-blue-500/20 text-blue-300',
        cashier:   'bg-emerald-500/20 text-emerald-300',
        kitchen:   'bg-orange-500/20 text-orange-300',
        warehouse: 'bg-yellow-500/20 text-yellow-300',
    };
    return map[user.value?.role] ?? 'bg-slate-500/20 text-slate-300';
});

const iconMap = {
    dashboard:      `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>`,
    products:       `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>`,
    categories:     `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>`,
    inventory:      `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>`,
    reports:        `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>`,
    expenses:       `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>`,
    settings:       `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0 .264 1.086 1.348 1.74 2.393 1.29 1.684-.726 3.422 1.012 2.696 2.696-.45 1.045.204 2.129 1.29 2.393 1.756.426 1.756 2.924 0 3.35-1.086.264-1.74 1.348-1.29 2.393.726 1.684-1.012 3.422-2.696 2.696-1.045-.45-2.129.204-2.393 1.29-.426 1.756-2.924 1.756-3.35 0-.264-1.086-1.348-1.74-2.393-1.29-1.684.726-3.422-1.012-2.696-2.696.45-1.045-.204-2.129-1.29-2.393-1.756-.426-1.756-2.924 0-3.35 1.086-.264 1.74-1.348 1.29-2.393-.726-1.684 1.012-3.422 2.696-2.696 1.045.45 2.129-.204 2.393-1.29Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0Z"/>`,
    tables:         `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>`,
    users:          `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>`,
    pos:            `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 7H6a2 2 0 00-2 2v9a2 2 0 002 2h9a2 2 0 002-2v-3M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2M9 7h6m-3 4v4m-2-2h4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 3h4a1 1 0 011 1v4"/>`,
    orders:         `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>`,
    'cash-session': `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>`,
    display:        `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>`,
    history:        `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
    warehouse:      `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>`,
    recipes:        `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>`,
};

const getIcon = (name) => iconMap[name] ?? iconMap.dashboard;

const loggingOut = ref(false);
const logout = () => {
    loggingOut.value = true;
    router.post(route('logout'));
};
</script>

<template>
    <aside
        class="fixed left-0 top-0 bottom-0 z-40 flex w-64 flex-col shadow-sidebar transition-transform duration-300 ease-in-out"
        :class="[
            open ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
        ]"
        style="background: #0f1117;"
    >
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="flex items-center justify-between px-5 py-5 border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand/20 ring-1 ring-brand/30">
                        <svg class="h-5 w-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zm4-7v3M12 1v3M8 1v3"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold tracking-wide text-white">Coffee POS</p>
                        <p class="text-[10px] text-slate-500 leading-none mt-0.5">Management System</p>
                    </div>
                </div>
                <button
                    type="button"
                    class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-500 hover:bg-white/5 hover:text-slate-300 md:hidden"
                    @click="$emit('close')"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
                <Link
                    v-for="item in menuItems"
                    :key="item.name"
                    :href="item.href"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-150"
                    :class="isActive(item)
                        ? 'bg-brand/15 text-brand ring-1 ring-brand/20'
                        : 'text-slate-400 hover:bg-white/5 hover:text-slate-200'"
                    @click="$emit('close')"
                >
                    <span
                        class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg transition-colors"
                        :class="isActive(item)
                            ? 'bg-brand/20 text-brand'
                            : 'bg-white/5 text-slate-500 group-hover:bg-white/8 group-hover:text-slate-300'"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="getIcon(item.name)"></svg>
                    </span>
                    <span class="truncate">{{ item.label }}</span>
                    <span
                        v-if="isActive(item)"
                        class="ml-auto h-1.5 w-1.5 rounded-full bg-brand flex-shrink-0"
                    ></span>
                </Link>
            </nav>

            <!-- User + Logout -->
            <div class="border-t border-white/5 p-3 space-y-1">
                <div class="flex items-center gap-3 rounded-xl px-3 py-2.5">
                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-brand/20 text-brand text-sm font-bold">
                        {{ user?.name?.charAt(0)?.toUpperCase() ?? 'U' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-200 truncate">{{ user?.name }}</p>
                        <span class="inline-block mt-0.5 rounded-md px-1.5 py-0.5 text-[10px] font-semibold leading-none" :class="roleColor">
                            {{ roleLabel }}
                        </span>
                    </div>
                </div>
                <button
                    type="button"
                    class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-500 hover:bg-white/5 hover:text-red-400 transition-colors"
                    :disabled="loggingOut"
                    @click="logout"
                >
                    <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-white/5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </span>
                    {{ loggingOut ? 'Keluar...' : 'Keluar' }}
                </button>
            </div>
        </div>
    </aside>
</template>
