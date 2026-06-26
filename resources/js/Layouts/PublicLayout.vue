<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-white text-gray-950">
        <header class="border-b border-gray-200">
            <div
                class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5"
            >
                <Link
                    href="/"
                    class="text-lg font-semibold tracking-tight text-gray-950"
                >
                    Cenecore
                </Link>

                <nav class="flex items-center gap-2 text-sm font-medium">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-gray-950"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-md px-3 py-2 text-gray-700 hover:bg-gray-100 hover:text-gray-950"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="route('register')"
                            class="rounded-md bg-gray-950 px-3 py-2 text-white hover:bg-gray-800"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>
