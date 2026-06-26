<script setup>
defineProps({
    locales: {
        type: Array,
        required: true,
    },
    currentLocale: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="flex flex-wrap items-center gap-2">
        <a
            v-for="item in locales"
            :key="item.code"
            :href="item.switchUrl"
            class="rounded-full border px-3 py-1.5 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-sky-400"
            :class="item.code === currentLocale
                ? 'border-white/30 bg-white text-zinc-950'
                : 'border-white/10 bg-white/5 text-zinc-200 hover:bg-white/10'"
            :aria-current="item.code === currentLocale ? 'page' : undefined"
        >
            {{ item.label }}
        </a>
    </div>
</template>
