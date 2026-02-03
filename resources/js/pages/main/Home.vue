<script setup lang="ts">
import { index } from '@/actions/App/Http/Controllers/Main/HomeController';
import HeroBanner from '@/components/HeroBanner.vue';
import MainHeader from '@/components/MainHeader.vue';
import ProductCard from '@/components/ProductCard.vue';
import { PaginationItem } from '@/types';
import { PPOBBrandDataItem, PPOBCategoryDataItem } from '@/types/cms/ppob';
import { Head, InfiniteScroll, Link } from '@inertiajs/vue3';

defineProps<{
    products?: PaginationItem<PPOBBrandDataItem>;
    categories?: PPOBCategoryDataItem[];
}>();
</script>

<template>
    <Head title="Home - GameStore" />

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <MainHeader :show-search="true" :show-auth-buttons="true" />

        <!-- Main Content -->
        <main class="mx-auto max-w-7xl px-4 py-8">
            <!-- Hero Banner -->
            <HeroBanner class="mb-8" />

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
                                {{ category.brands_count }} produk
                            </span>
                        </button>
                    </Link>
                </div>
            </section>

            <!-- Products Section -->
            <section>
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-foreground">
                        Produk Populer
                    </h2>
                </div>

                <InfiniteScroll data="products">
                    <div
                        class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6"
                    >
                        <ProductCard
                            v-for="product in products?.data"
                            :key="product.id"
                            :product="product"
                        />
                    </div>
                </InfiniteScroll>
            </section>
        </main>
    </div>
</template>
