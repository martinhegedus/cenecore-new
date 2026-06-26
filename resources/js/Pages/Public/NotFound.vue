<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/Public/SeoHead.vue';

defineProps({
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
                <div class="eyebrow">404</div>
                <h1 class="headline mt-4">Page not found</h1>
                <p class="subhead mt-6">The page you were looking for is unavailable or has moved.</p>
            </div>
        </section>
    </PublicLayout>
</template>
