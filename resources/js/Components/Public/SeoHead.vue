<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    seo: {
        type: Object,
        required: true,
    },
    localizedUrls: {
        type: Object,
        default: () => ({}),
    },
    locale: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head :title="seo.title">
        <meta name="description" :content="seo.description" />
        <link rel="canonical" :href="seo.canonical" />
        <meta name="robots" :content="seo.robots || 'index,follow'" />

        <meta property="og:title" :content="seo.ogTitle || seo.title" />
        <meta property="og:description" :content="seo.ogDescription || seo.description" />
        <meta property="og:type" :content="seo.ogType || 'website'" />
        <meta property="og:url" :content="seo.canonical" />
        <meta property="og:locale" :content="locale" />
        <meta property="twitter:card" content="summary_large_image" />

        <link
            v-for="(url, localizedLocale) in localizedUrls"
            :key="localizedLocale"
            rel="alternate"
            :hreflang="localizedLocale === 'cs' ? 'cs' : localizedLocale"
            :href="url"
        />
        <link rel="alternate" hreflang="x-default" :href="localizedUrls.sk || seo.canonical" />
    </Head>
</template>
