<script setup>
import { Link } from '@inertiajs/vue3';
import LocaleSwitcher from '@/Components/Public/LocaleSwitcher.vue';

defineProps({
    navigation: {
        type: Array,
        default: () => [],
    },
    locale: {
        type: String,
        default: 'sk',
    },
    supportedLocales: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="min-h-screen bg-transparent text-white">
        <a href="#content" class="sr-only focus:not-sr-only">Skip to content</a>
        <header class="sticky top-0 z-40 border-b border-white/10 bg-[#0a0a0e]/80 backdrop-blur-xl">
            <div class="page-shell flex items-center justify-between py-4">
                <Link href="/" class="flex items-center gap-3 text-sm font-semibold tracking-[0.25em] text-white uppercase">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/10 bg-white/5 font-display text-base tracking-normal">C</span>
                    Cenecore
                </Link>

                <nav class="hidden items-center gap-6 lg:flex">
                    <Link v-for="item in navigation" :key="item.url" :href="item.url" class="text-sm text-zinc-300 transition hover:text-white">
                        {{ item.label }}
                    </Link>
                </nav>

                <div class="hidden lg:block">
                    <LocaleSwitcher :locales="supportedLocales" :current-locale="locale" />
                </div>
            </div>
        </header>

        <main id="content">
            <slot />
        </main>
    </div>
</template>
