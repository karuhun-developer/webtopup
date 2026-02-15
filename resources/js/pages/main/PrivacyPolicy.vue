<script setup lang="ts">
import { privacyPolicy } from '@/actions/App/Http/Controllers/Main/ContentController';
import MainFooter from '@/components/MainFooter.vue';
import MainHeader from '@/components/MainHeader.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();
const setting = page.props.setting;
</script>

<template>
    <Head>
        <title>Kebijakan Privasi</title>
        <link
            rel="icon"
            type="image/svg+xml"
            :href="setting?.favicon || '/favicon.svg'"
        />
        <meta
            name="description"
            :content="`Kebijakan Privasi ${setting?.title}. Pelajari bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan layanan kami.`"
        />
        <meta
            name="keywords"
            :content="`kebijakan privasi, privacy policy, ${setting?.title}, perlindungan data, keamanan informasi`"
        />
        <meta name="author" :content="setting?.title" />
        <meta name="application-name" :content="setting?.title" />
        <meta
            property="og:title"
            :content="`Kebijakan Privasi - ${setting?.title}`"
        />
        <meta
            property="og:description"
            :content="`Kebijakan Privasi ${setting?.title}. Pelajari bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan layanan kami.`"
        />
        <meta property="og:url" :content="privacyPolicy().url" />
        <meta
            property="og:image"
            :content="setting?.favicon || '/favicon.svg'"
        />
        <meta
            property="twitter:title"
            :content="`Kebijakan Privasi - ${setting?.title}`"
        />
        <meta
            property="twitter:description"
            :content="`Kebijakan Privasi ${setting?.title}. Pelajari bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan layanan kami.`"
        />
        <meta
            property="twitter:image"
            :content="setting?.favicon || '/favicon.svg'"
        />
        <meta property="twitter:card" content="summary_large_image" />
        <meta property="twitter:site" :content="setting?.title" />
        <meta property="image" :content="setting?.favicon || '/favicon.svg'" />
        <meta property="canonical" :content="privacyPolicy().url" />
        <meta name="robots" content="index, follow" />
        <component :is="'script'" type="application/ld+json">
            {{
                JSON.stringify({
                    '@context': 'https://schema.org',
                    '@type': 'WebPage',
                    name: 'Kebijakan Privasi',
                    description: `Kebijakan Privasi ${setting?.title}. Pelajari bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan layanan kami.`,
                    url: privacyPolicy().url,
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
                    Kebijakan Privasi
                </h1>

                <div
                    class="prose prose-sm prose-headings:text-foreground prose-a:text-primary prose-strong:text-foreground max-w-none text-muted-foreground"
                    v-html="setting?.privacy_policy"
                />
            </div>
        </main>

        <!-- Footer -->
        <MainFooter />
    </div>
</template>
