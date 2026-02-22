<script setup lang="ts">
import { terms } from '@/actions/App/Http/Controllers/Main/ContentController';
import MainFooter from '@/components/MainFooter.vue';
import MainHeader from '@/components/MainHeader.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
const setting = page.props.setting;
const appUrl = page.props.app_url;
</script>

<template>
    <Head>
        <title>Syarat & Ketentuan</title>
        <component :is="'script'" type="application/ld+json">
            {{
                JSON.stringify({
                    '@context': 'https://schema.org',
                    '@type': 'WebPage',
                    name: 'Syarat & Ketentuan',
                    description: `Syarat & Ketentuan ${setting?.title}. Baca ketentuan penggunaan layanan kami untuk pengalaman transaksi yang aman dan nyaman.`,
                    url: terms().url,
                })
            }}
        </component>
    </Head>

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <MainHeader :show-search="false" />

        <!-- Main Content -->
        <main class="mx-auto max-w-4xl px-4 py-8">
            <div class="rounded-lg border border-border/50 bg-card p-6 md:p-8">
                <h1 class="mb-6 text-2xl font-bold text-foreground md:text-3xl">
                    Syarat & Ketentuan
                </h1>

                <div
                    class="prose prose-sm prose-headings:text-foreground prose-a:text-primary prose-strong:text-foreground max-w-none text-muted-foreground"
                    v-html="setting?.terms"
                />
            </div>
        </main>

        <!-- Footer -->
        <MainFooter />
    </div>
</template>
