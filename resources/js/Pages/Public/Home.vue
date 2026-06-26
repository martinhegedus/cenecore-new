<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/Public/SeoHead.vue';

defineProps({
    hero: Object,
    services: Array,
    featuredProjects: Array,
    technologies: Array,
    testimonials: Array,
    articles: Array,
    faq: Array,
    cta: Object,
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
            <div class="page-shell grid gap-12 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <div class="space-y-8">
                    <div class="eyebrow">{{ hero.eyebrow }}</div>
                    <h1 class="headline max-w-4xl">{{ hero.headline }}</h1>
                    <p class="subhead">{{ hero.lead }}</p>
                    <div class="flex flex-wrap gap-3">
                        <Link :href="cta.quoteUrl" class="btn-primary">
                            {{ cta.quoteLabel }}
                        </Link>
                        <Link :href="cta.projectsUrl" class="btn-secondary">
                            {{ cta.projectsLabel }}
                        </Link>
                    </div>
                </div>

                <div class="panel p-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div v-for="item in [
                            { label: 'Laravel systems', value: 'Custom architecture' },
                            { label: 'Vue / Inertia', value: 'Productive frontend' },
                            { label: 'SEO', value: 'Technical visibility' },
                            { label: 'Integrations', value: 'Reliable automation' },
                        ]" :key="item.label" class="panel-soft p-4">
                            <div class="text-xs uppercase tracking-[0.22em] text-zinc-400">{{ item.label }}</div>
                            <div class="mt-3 text-lg font-semibold text-white">{{ item.value }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-shell">
            <div class="page-shell grid gap-6">
                <div class="eyebrow">Capabilities</div>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <article v-for="service in services" :key="service.id" class="panel-soft p-5">
                        <div class="text-xs uppercase tracking-[0.2em] text-zinc-400">Service</div>
                        <h2 class="mt-3 text-xl font-semibold text-white">{{ service.title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-zinc-300">{{ service.excerpt }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-shell">
            <div class="page-shell grid gap-6">
                <div class="eyebrow">Featured work</div>
                <div class="grid gap-4 lg:grid-cols-3">
                    <article v-for="project in featuredProjects" :key="project.id" class="panel p-6">
                        <div class="text-xs uppercase tracking-[0.2em] text-zinc-400">{{ project.client_name || 'Project' }}</div>
                        <h2 class="mt-4 text-2xl font-semibold text-white">{{ project.title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-zinc-300">{{ project.summary }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-shell">
            <div class="page-shell grid gap-6 lg:grid-cols-2">
                <div>
                    <div class="eyebrow">Technology ecosystem</div>
                    <p class="mt-4 max-w-xl text-lg leading-8 text-zinc-300">A technical operating system built around Laravel, Vue, Inertia, Filament, and modern delivery discipline.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div v-for="technology in technologies" :key="technology.id" class="panel-soft p-4">
                        <div class="text-sm font-semibold text-white">{{ technology.name }}</div>
                        <div class="mt-2 text-sm text-zinc-400">{{ technology.description }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-shell">
            <div class="page-shell grid gap-6 lg:grid-cols-2">
                <article v-for="testimonial in testimonials" :key="testimonial.id" class="panel p-6">
                    <p class="text-lg leading-8 text-zinc-100">“{{ testimonial.quote }}”</p>
                    <div class="mt-6 text-sm text-zinc-400">{{ testimonial.client_name }}</div>
                </article>
            </div>
        </section>

        <section class="section-shell">
            <div class="page-shell grid gap-6">
                <div class="eyebrow">Insights</div>
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <article v-for="article in articles" :key="article.id" class="panel-soft p-5">
                        <h2 class="text-xl font-semibold text-white">{{ article.title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-zinc-300">{{ article.excerpt }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section-shell">
            <div class="page-shell grid gap-6">
                <div class="eyebrow">FAQ</div>
                <div class="grid gap-3">
                    <details v-for="item in faq" :key="item.id" class="panel-soft p-5">
                        <summary class="cursor-pointer list-none text-base font-semibold text-white">{{ item.question }}</summary>
                        <p class="mt-3 text-sm leading-6 text-zinc-300">{{ item.answer }}</p>
                    </details>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
