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
        <link
            rel="icon"
            type="image/svg+xml"
            :href="setting?.favicon || '/favicon.svg'"
        />
        <meta
            name="description"
            :content="`Syarat & Ketentuan ${setting?.title}. Baca ketentuan penggunaan layanan kami untuk pengalaman transaksi yang aman dan nyaman.`"
        />
        <meta
            name="keywords"
            :content="`syarat dan ketentuan, terms and conditions, ${setting?.title}, aturan penggunaan, perjanjian pengguna`"
        />
        <meta name="author" :content="setting?.title" />
        <meta name="application-name" :content="setting?.title" />
        <meta
            property="og:title"
            :content="`Syarat & Ketentuan - ${setting?.title}`"
        />
        <meta
            property="og:description"
            :content="`Syarat & Ketentuan ${setting?.title}. Baca ketentuan penggunaan layanan kami untuk pengalaman transaksi yang aman dan nyaman.`"
        />
        <meta property="og:url" :content="terms().url" />
        <meta
            property="og:image"
            :content="`${appUrl}${setting?.favicon || '/favicon.svg'}`"
        />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta
            property="twitter:title"
            :content="`Syarat & Ketentuan - ${setting?.title}`"
        />
        <meta
            property="twitter:description"
            :content="`Syarat & Ketentuan ${setting?.title}. Baca ketentuan penggunaan layanan kami untuk pengalaman transaksi yang aman dan nyaman.`"
        />
        <meta
            property="twitter:image"
            :content="`${appUrl}${setting?.favicon || '/favicon.svg'}`"
        />
        <meta property="twitter:card" content="summary_large_image" />
        <meta property="twitter:site" :content="setting?.title" />
        <meta
            property="image"
            :content="`${appUrl}${setting?.favicon || '/favicon.svg'}`"
        />
        <meta property="canonical" :content="terms().url" />
        <meta name="robots" content="index, follow" />
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
