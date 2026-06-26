<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/Public/SeoHead.vue';

defineProps({
    title: String,
    headline: String,
    content: Array,
    locale: String,
    supportedLocales: Array,
    localizedUrls: Object,
    navigation: Array,
    seo: Object,
});
</script>

<template>
    <SeoHead :seo="seo" :locale="locale" :localized-urls="localizedUrls" />
    <PublicLayout :navigation="navigation" :locale="locale" :supported-locales="supportedLocales">
        <section class="section-shell">
            <div class="page-shell">
                <div class="max-w-3xl">
                    <div class="eyebrow">{{ title }}</div>
                    <h1 class="headline mt-4">{{ headline }}</h1>
                    <div class="mt-8 space-y-4 text-zinc-300 leading-7">
                        <p v-for="(paragraph, index) in content" :key="index">{{ paragraph }}</p>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
