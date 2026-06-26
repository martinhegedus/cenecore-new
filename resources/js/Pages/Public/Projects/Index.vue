<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/Public/SeoHead.vue';

defineProps({
    projects: Array,
    featuredProject: Object,
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
                <div class="eyebrow">Projects</div>
                <h1 class="headline mt-4">Selected case studies.</h1>
                <div class="mt-10 grid gap-4 lg:grid-cols-2">
                    <article v-for="project in projects" :key="project.id" class="panel-soft p-5">
                        <h2 class="text-xl font-semibold text-white">{{ project.title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-zinc-300">{{ project.summary }}</p>
                    </article>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
