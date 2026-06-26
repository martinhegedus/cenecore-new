<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/Public/SeoHead.vue';

defineProps({
    posts: Array,
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
                <div class="eyebrow">Insights</div>
                <h1 class="headline mt-4">Technical notes, process, and product thinking.</h1>
                <div class="mt-10 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <article v-for="post in posts" :key="post.id" class="panel-soft p-5">
                        <h2 class="text-xl font-semibold text-white">{{ post.title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-zinc-300">{{ post.excerpt }}</p>
                    </article>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
