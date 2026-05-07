<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    products: { type: Array, default: () => [] },
    tables: { type: Number, default: 0 },
    totalOrders: { type: Number, default: 0 },
});

const features = [
    {
        title: 'Crafted Coffee',
        description: 'Disajikan dari biji pilihan dengan profil rasa yang clean, balance, dan konsisten.',
    },
    {
        title: 'Warm Atmosphere',
        description: 'Ruang hangat dengan nuansa kayu, cahaya lembut, dan suasana yang tenang.',
    },
    {
        title: 'Signature Menu',
        description: 'Menu andalan yang dibuat untuk memberi pengalaman minum kopi yang berkesan.',
    },
];

const highlights = [
    { value: '100%', label: 'Fresh roast' },
    { value: '3', label: 'Coffee origin' },
    { value: '12', label: 'Signature drinks' },
];

const menuHighlights = [
    {
        name: 'House Espresso',
        category: 'Coffee',
        price: 'Rp 28.000',
    },
    {
        name: 'Velvet Latte',
        category: 'Best Seller',
        price: 'Rp 34.000',
    },
    {
        name: 'Butter Croissant',
        category: 'Snack',
        price: 'Rp 26.000',
    },
];

const profileStats = [
    { value: 'House roast', label: 'Roast profile' },
    { value: 'Soft light', label: 'Ambience' },
    { value: 'Latte art', label: 'Service detail' },
];
</script>

<template>
    <Head title="Coffee POS" />

    <div class="min-h-screen bg-[#0f0a07] text-stone-100">
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(191,133,79,0.28),_transparent_32%),radial-gradient(circle_at_bottom_right,_rgba(255,241,220,0.08),_transparent_30%)]"></div>
            <div class="absolute left-0 top-0 h-72 w-72 rounded-full bg-amber-400/10 blur-3xl"></div>
            <div class="absolute right-0 top-28 h-80 w-80 rounded-full bg-orange-300/10 blur-3xl"></div>

            <div class="relative mx-auto max-w-7xl px-6 pb-16 pt-6 lg:px-8">
                <header class="flex items-center justify-between rounded-full border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-xl">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-amber-200 to-orange-400 text-[#2a160d] shadow-lg shadow-amber-500/20">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12v6a4 4 0 01-4 4H9a3 3 0 01-3-3V7Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9h1.25a2.25 2.25 0 010 4.5H18" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 21h14" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Coffee Shop</p>
                            <p class="text-lg font-semibold text-white">Coffee POS</p>
                        </div>
                    </div>

                    <nav class="hidden items-center gap-6 text-sm text-stone-300 md:flex">
                        <a href="#fitur" class="transition hover:text-white">Fitur</a>
                        <a href="#menu" class="transition hover:text-white">Menu</a>
                        <a href="#statistik" class="transition hover:text-white">Statistik</a>
                    </nav>
                </header>

                <section class="grid gap-12 py-16 lg:grid-cols-[1.15fr_0.85fr] lg:items-center lg:py-24">
                    <div class="max-w-3xl">
                        <p class="inline-flex items-center gap-2 rounded-full border border-amber-200/20 bg-amber-200/10 px-4 py-2 text-sm text-amber-100">
                            <span class="h-2 w-2 rounded-full bg-amber-300"></span>
                            Elegant profile for a modern coffee shop
                        </p>
                        <h1 class="mt-6 text-4xl font-semibold leading-tight text-white md:text-6xl">
                            Coffee shop profile yang hangat, modern, dan berkarakter.
                        </h1>
                        <p class="mt-6 max-w-2xl text-base leading-8 text-stone-300 md:text-lg">
                            Kami meracik suasana yang tenang, aroma kopi yang kuat, dan sajian yang elegan untuk menemani pagi, siang, hingga malam.
                        </p>

                        <div class="mt-8 inline-flex items-center rounded-full border border-amber-200/15 bg-white/5 px-5 py-3 text-sm text-stone-200">
                            Nuansa brown, cream, dan gold untuk kesan premium yang lembut.
                        </div>

                            <div id="statistik" class="mt-10 grid gap-4 sm:grid-cols-3">
                                <div
                                    v-for="item in profileStats"
                                    :key="item.label"
                                    class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur"
                                >
                                    <p class="text-2xl font-semibold text-white">{{ item.value }}</p>
                                    <p class="mt-1 text-sm text-stone-400">{{ item.label }}</p>
                                </div>
                            </div>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-0 -rotate-6 rounded-[2rem] bg-gradient-to-br from-amber-300/20 to-orange-500/10 blur-2xl"></div>
                        <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-[#1a120d]/90 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-stone-400">Brand story</p>
                                    <p class="mt-1 text-2xl font-semibold text-white">Coffee House</p>
                                </div>
                                <div class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-medium text-emerald-300">
                                    Signature
                                </div>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-4">
                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="text-sm text-stone-400">Roast profile</p>
                                    <p class="mt-2 text-3xl font-semibold text-white">House blend</p>
                                </div>
                                <div class="rounded-2xl bg-white/5 p-4">
                                    <p class="text-sm text-stone-400">Ambience</p>
                                    <p class="mt-2 text-3xl font-semibold text-white">Cozy</p>
                                </div>
                            </div>

                            <div class="mt-6 space-y-3">
                                <div class="rounded-2xl border border-white/5 bg-[#241812] p-4">
                                    <p class="text-sm text-amber-100/80">Origin</p>
                                    <p class="mt-1 font-medium text-white">Single origin, house blend, dan seasonal roast</p>
                                </div>
                                <div class="rounded-2xl border border-white/5 bg-[#241812] p-4">
                                    <p class="text-sm text-amber-100/80">Mood</p>
                                    <p class="mt-1 font-medium text-white">Slow morning, soft jazz, dan ruang yang nyaman</p>
                                </div>
                            </div>

                            <div class="mt-6 rounded-[1.5rem] border border-amber-200/10 bg-gradient-to-br from-amber-200/10 to-transparent p-5">
                                <p class="text-sm uppercase tracking-[0.3em] text-amber-200/70">Coffee note</p>
                                <p class="mt-2 text-2xl font-semibold text-white">Velvet, nutty, caramel</p>
                                <p class="mt-1 text-sm text-stone-400">Aroma lembut dengan aftertaste manis yang bersih dan nyaman.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="fitur" class="py-8 lg:py-14">
                    <div class="max-w-2xl">
                        <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Coffee profile</p>
                        <h2 class="mt-3 text-3xl font-semibold text-white">Karakter brand yang terasa dari rasa sampai suasana.</h2>
                    </div>

                    <div class="mt-8 grid gap-5 lg:grid-cols-3">
                        <article
                            v-for="feature in features"
                            :key="feature.title"
                            class="rounded-[1.75rem] border border-white/10 bg-white/5 p-6 backdrop-blur transition hover:-translate-y-1 hover:border-amber-200/20"
                        >
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-200/10 text-amber-100">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h10M4 17h14" />
                                </svg>
                            </div>
                            <h3 class="mt-5 text-xl font-semibold text-white">{{ feature.title }}</h3>
                            <p class="mt-3 leading-7 text-stone-300">{{ feature.description }}</p>
                        </article>
                    </div>
                </section>

                <section id="menu" class="grid gap-8 py-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-start lg:py-14">
                    <div>
                        <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Signature menu</p>
                        <h2 class="mt-3 text-3xl font-semibold text-white">Pilihan menu yang sederhana, elegan, dan mudah disukai.</h2>
                        <p class="mt-4 max-w-xl leading-7 text-stone-300">
                            Setiap menu dibuat untuk memperkuat identitas coffee shop yang bersih, berkelas, dan nyaman dikunjungi.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <article
                            v-for="item in menuHighlights"
                            :key="item.name"
                            class="rounded-[1.6rem] border border-white/10 bg-[#1a120d] p-5 shadow-lg shadow-black/20"
                        >
                            <div class="aspect-square rounded-[1.2rem] bg-gradient-to-br from-amber-200/30 via-orange-300/10 to-transparent"></div>
                            <p class="mt-4 text-sm text-amber-100/70">{{ item.category }}</p>
                            <h3 class="mt-1 text-lg font-semibold text-white">{{ item.name }}</h3>
                            <p class="mt-2 text-sm text-stone-400">{{ item.price }}</p>
                        </article>
                    </div>
                </section>

                <section class="pb-10 pt-8 lg:pb-16">
                    <div class="rounded-[2rem] border border-amber-200/10 bg-gradient-to-r from-amber-200/10 via-white/5 to-orange-300/10 p-8 text-center backdrop-blur">
                        <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Coffee brand experience</p>
                        <h2 class="mt-3 text-3xl font-semibold text-white">Tempat singgah yang tenang untuk menikmati kopi berkualitas.</h2>
                        <p class="mx-auto mt-4 max-w-2xl text-stone-300">
                            Landing page ini fokus pada identitas coffee shop, bukan penjelasan aplikasi, agar tampil sebagai profil brand yang utuh.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
