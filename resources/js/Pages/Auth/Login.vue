<script setup>
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login — Coffee POS" />

    <div class="flex min-h-screen bg-coffee-950">
        <div class="hidden w-1/2 flex-col justify-between bg-gradient-to-br from-coffee-900 via-coffee-800 to-coffee-700 p-12 lg:flex">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand/20 ring-1 ring-brand/30">
                    <svg class="h-6 w-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zm4-7v3M12 1v3M8 1v3"/>
                    </svg>
                </div>
                <span class="text-lg font-bold text-white">Coffee POS</span>
            </div>

            <div>
                <h1 class="text-4xl font-bold leading-tight text-white">
                    Sistem Manajemen<br />Coffee Shop Modern
                </h1>
                <p class="mt-4 text-lg text-coffee-300">
                    POS kasir, order QR meja, kitchen display, inventory, dan laporan keuangan dalam satu platform.
                </p>

                <div class="mt-10 grid grid-cols-2 gap-4">
                    <div class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10">
                        <p class="text-2xl font-bold text-brand">POS</p>
                        <p class="mt-1 text-sm text-coffee-300">Kasir & pembayaran</p>
                    </div>
                    <div class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10">
                        <p class="text-2xl font-bold text-brand">QR</p>
                        <p class="mt-1 text-sm text-coffee-300">Order per meja</p>
                    </div>
                    <div class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10">
                        <p class="text-2xl font-bold text-brand">Kitchen</p>
                        <p class="mt-1 text-sm text-coffee-300">Display realtime</p>
                    </div>
                    <div class="rounded-xl bg-white/5 p-4 ring-1 ring-white/10">
                        <p class="text-2xl font-bold text-brand">Laporan</p>
                        <p class="mt-1 text-sm text-coffee-300">Keuangan & stok</p>
                    </div>
                </div>
            </div>

            <p class="text-sm text-coffee-500">&copy; {{ new Date().getFullYear() }} Coffee POS. All rights reserved.</p>
        </div>

        <div class="flex flex-1 items-center justify-center p-6 lg:p-12" style="background: #0f1117;">
            <div class="w-full max-w-md">
                <div class="mb-8 lg:hidden flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand/20 ring-1 ring-brand/30">
                        <svg class="h-5 w-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zm4-7v3M12 1v3M8 1v3"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-white">Coffee POS</span>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-white">Selamat Datang</h2>
                    <p class="mt-1 text-slate-400">Masuk ke akun Anda untuk melanjutkan.</p>
                </div>

                <div v-if="status" class="mb-4 rounded-xl border border-green-500/20 bg-green-500/10 px-4 py-3 text-sm text-green-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-300">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="username"
                            required
                            autofocus
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 outline-none transition focus:border-brand focus:ring-1 focus:ring-brand"
                            placeholder="email@example.com"
                        />
                        <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-400">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-300">Password</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 outline-none transition focus:border-brand focus:ring-1 focus:ring-brand"
                            placeholder="••••••••"
                        />
                        <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-400">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-slate-400">
                            <input v-model="form.remember" type="checkbox" class="rounded border-white/20 bg-white/5 text-brand focus:ring-brand" />
                            Ingat saya
                        </label>
                        <a v-if="canResetPassword" :href="route('password.request')" class="text-sm text-brand hover:text-brand-light">
                            Lupa password?
                        </a>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-brand py-3 font-semibold text-white transition hover:bg-brand-dark disabled:opacity-50"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Masuk...' : 'Masuk' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
