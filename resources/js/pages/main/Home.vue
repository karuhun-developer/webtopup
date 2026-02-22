<script setup lang="ts">
import { index } from '@/actions/App/Http/Controllers/Main/HomeController';
import BrandCard from '@/components/BrandCard.vue';
import HeroBanner from '@/components/HeroBanner.vue';
import MainFooter from '@/components/MainFooter.vue';
import MainHeader from '@/components/MainHeader.vue';
import { PaginationItem } from '@/types';
import { PPOBBrandDataItem, PPOBCategoryDataItem } from '@/types/cms/ppob';
import { SliderDataItem } from '@/types/cms/web';
import { Head, InfiniteScroll, Link, usePage } from '@inertiajs/vue3';

defineProps<{
    sliders: SliderDataItem[];
    brands: PaginationItem<PPOBBrandDataItem>;
    featured_brands: PPOBBrandDataItem[];
    categories: PPOBCategoryDataItem[];
}>();

const page = usePage();
const setting = page.props.setting;
const appUrl = page.props.app_url;
</script>

<template>
    <Head>
        <title>{{ setting?.title }}</title>
        <component :is="'script'" type="application/ld+json">
            {{
                JSON.stringify({
                    '@context': 'https://schema.org',
                    '@type': 'WebSite',
                    name: setting?.title,
                    url: index().url,
                    potentialAction: {
                        '@type': 'SearchAction',
                        target: {
                            '@type': 'EntryPoint',
                            urlTemplate: `${index().url}?search={search_term_string}`,
                        },
                        'query-input': 'required name=search_term_string',
                    },
                })
            }}
        </component>
    </Head>

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <MainHeader />

        <!-- Main Content -->
        <main class="mx-auto max-w-7xl px-4 py-8">
            <!-- Hero Banner -->
            <HeroBanner
                class="mb-8"
                :slides="sliders"
                :autoplay="true"
                :interval="50000"
            />

            <!-- Categories Section -->
            <section class="mb-8">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-foreground">Kategori</h2>
                </div>
                <div class="flex gap-3 overflow-x-auto pb-2">
                    <Link
                        v-for="category in categories"
                        :href="index().url + '?category=' + category.slug"
                        :key="category.id"
                        preserve-scroll
                    >
                        <button
                            class="flex min-w-[140px] flex-col items-center gap-2 rounded-lg border border-border/50 bg-card p-4 transition-all hover:scale-105 hover:border-primary/50 hover:shadow-md"
                        >
                            <img
                                v-if="category.image"
                                :src="category.image"
                                alt="category.name"
                                class="h-12 w-12 object-contain"
                            />
                            <span class="text-3xl" v-else> 🎮 </span>
                            <span class="text-sm font-semibold text-foreground">
                                {{ category.name }}
                            </span>
                            <span class="text-xs text-muted-foreground">
                                {{ category.active_brands_count }} produk
                            </span>
                        </button>
                    </Link>
                </div>
            </section>

            <!-- Featured Brands Section -->
            <section class="mb-8" v-if="featured_brands.length > 0">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-foreground">
                        Produk Unggulan
                    </h2>
                </div>
                <div
                    class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6"
                >
                    <BrandCard
                        v-for="brand in featured_brands"
                        :key="brand.id"
                        :brand="brand"
                    />
                </div>
            </section>

            <!-- Brands Section -->
            <section>
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-foreground">
                        Produk Populer
                    </h2>
                </div>

                <InfiniteScroll data="brands">
                    <div
                        class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6"
                    >
                        <BrandCard
                            v-for="brand in brands?.data"
                            :key="brand.id"
                            :brand="brand"
                        />
                    </div>
                </InfiniteScroll>
            </section>
        </main>

        <!-- Footer -->
        <MainFooter />
    </div>
</template>
