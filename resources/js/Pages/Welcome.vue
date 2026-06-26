<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '../Layouts/PublicLayout.vue';
</script>

<template>
    <Head title="Home" />

    <PublicLayout>
        <section class="mx-auto grid max-w-6xl gap-10 px-6 py-20 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:py-28">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-amber-700">
                    Phase 1 foundation
                </p>
                <h1 class="mt-4 max-w-3xl text-4xl font-semibold tracking-tight text-gray-950 sm:text-5xl">
                    Cenecore is ready for the application buildout.
                </h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-gray-600">
                    The public site now renders through Inertia and Vue, with
                    Breeze authentication and a Filament admin panel available
                    for the next phase.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <Link
                        :href="route('login')"
                        class="rounded-md bg-gray-950 px-5 py-3 text-sm font-semibold text-white hover:bg-gray-800"
                    >
                        Log in
                    </Link>
                    <a
                        href="/admin"
                        class="rounded-md border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-800 hover:bg-gray-50"
                    >
                        Admin panel
                    </a>
                </div>
            </div>

            <div class="border-l border-gray-200 pl-6">
                <dl class="space-y-6">
                    <div>
                        <dt class="text-sm font-semibold text-gray-950">
                            Frontend
                        </dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-600">
                            Vue 3, Inertia, Vite, and Tailwind are wired for
                            public pages and authenticated screens.
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-semibold text-gray-950">
                            Authentication
                        </dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-600">
                            Breeze provides registration, login, password reset,
                            dashboard, and profile management.
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-semibold text-gray-950">
                            Administration
                        </dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-600">
                            Filament is installed at <span class="font-mono">/admin</span>
                            with the default panel dashboard.
                        </dd>
                    </div>
                </dl>
            </div>
        </section>
    </PublicLayout>
</template>
