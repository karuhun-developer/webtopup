<script setup lang="ts">
import { terms } from '@/actions/App/Http/Controllers/Main/ContentController';
import MainFooter from '@/components/MainFooter.vue';
import MainHeader from '@/components/MainHeader.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <Head>
        <title>Syarat & Ketentuan</title>
        <meta
            name="description"
            :content="`Syarat & Ketentuan ${page.props.setting.title}. Baca ketentuan penggunaan layanan kami untuk pengalaman transaksi yang aman dan nyaman.`"
        />
        <meta
            name="keywords"
            :content="`syarat dan ketentuan, terms and conditions, ${page.props.setting.title}, aturan penggunaan, perjanjian pengguna`"
        />
        <meta name="author" :content="page.props.setting.title" />
        <meta name="type" content="website" />
        <meta name="application-name" :content="page.props.setting.title" />
        <meta
            property="og:title"
            :content="`Syarat & Ketentuan - ${page.props.setting.title}`"
        />
        <meta
            property="og:description"
            :content="`Syarat & Ketentuan ${page.props.setting.title}. Baca ketentuan penggunaan layanan kami untuk pengalaman transaksi yang aman dan nyaman.`"
        />
        <meta property="og:url" :content="terms().url" />
        <meta property="og:image" content="/favicon.svg" />
        <meta
            property="twitter:title"
            :content="`Syarat & Ketentuan - ${page.props.setting.title}`"
        />
        <meta
            property="twitter:description"
            :content="`Syarat & Ketentuan ${page.props.setting.title}. Baca ketentuan penggunaan layanan kami untuk pengalaman transaksi yang aman dan nyaman.`"
        />
        <meta property="twitter:image" content="/favicon.svg" />
        <meta property="twitter:card" content="summary_large_image" />
        <meta property="twitter:site" :content="page.props.setting.title" />
        <meta property="shortcut icon" href="/favicon.svg" />
        <meta property="image" content="/favicon.svg" />
        <meta property="canonical" :content="terms().url" />
        <meta name="robots" content="index, follow" />
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
                    class="prose prose-sm max-w-none text-muted-foreground prose-headings:text-foreground prose-a:text-primary prose-strong:text-foreground"
                    v-html="page.props.setting.terms"
                />
            </div>
        </main>

        <!-- Footer -->
        <MainFooter />
    </div>
</template>
