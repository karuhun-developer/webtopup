<script setup lang="ts">
import HeroBanner from '@/components/HeroBanner.vue';
import MainHeader from '@/components/MainHeader.vue';
import ProductCard from '@/components/ProductCard.vue';
import { Head } from '@inertiajs/vue3';

interface Product {
    id: number;
    title: string;
    image: string;
    rating: number;
}

interface Props {
    products?: Product[];
}

const props = withDefaults(defineProps<Props>(), {
    products: () => [
        {
            id: 1,
            title: 'Mobile Legends',
            image: 'https://placehold.co/300x400/7C3AED/FFFFFF?text=Mobile+Legends',
            rating: 5,
        },
        {
            id: 2,
            title: 'Free Fire',
            image: 'https://placehold.co/300x400/DC2626/FFFFFF?text=Free+Fire',
            rating: 5,
        },
        {
            id: 3,
            title: 'Genshin Impact',
            image: 'https://placehold.co/300x400/F59E0B/FFFFFF?text=Genshin+Impact',
            rating: 5,
        },
        {
            id: 4,
            title: 'Honkai Star Rail',
            image: 'https://placehold.co/300x400/EC4899/FFFFFF?text=Honkai+Star+Rail',
            rating: 5,
        },
        {
            id: 5,
            title: 'PUBG Mobile',
            image: 'https://placehold.co/300x400/10B981/FFFFFF?text=PUBG+Mobile',
            rating: 5,
        },
        {
            id: 6,
            title: 'Call of Duty Mobile',
            image: 'https://placehold.co/300x400/3B82F6/FFFFFF?text=Call+of+Duty',
            rating: 5,
        },
        {
            id: 7,
            title: 'Arena of Valor',
            image: 'https://placehold.co/300x400/8B5CF6/FFFFFF?text=Arena+of+Valor',
            rating: 5,
        },
        {
            id: 8,
            title: 'Valorant',
            image: 'https://placehold.co/300x400/EF4444/FFFFFF?text=Valorant',
            rating: 5,
        },
        {
            id: 9,
            title: 'Apex Legends',
            image: 'https://placehold.co/300x400/F97316/FFFFFF?text=Apex+Legends',
            rating: 5,
        },
        {
            id: 10,
            title: 'League of Legends',
            image: 'https://placehold.co/300x400/06B6D4/FFFFFF?text=League+of+Legends',
            rating: 5,
        },
        {
            id: 11,
            title: 'Roblox',
            image: 'https://placehold.co/300x400/A855F7/FFFFFF?text=Roblox',
            rating: 5,
        },
        {
            id: 12,
            title: 'Minecraft',
            image: 'https://placehold.co/300x400/22C55E/FFFFFF?text=Minecraft',
            rating: 5,
        },
    ],
});
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

            <!-- Products Section -->
            <section>
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-foreground">
                        Produk Populer
                    </h2>
                </div>

                <div
                    class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6"
                >
                    <ProductCard
                        v-for="product in products"
                        :key="product.id"
                        :image="product.image"
                        :title="product.title"
                        description="qwepkqopwejkoiqwjeioqweiojqwioejqiowjeoiqwe"
                        :rating="product.rating"
                    />
                </div>
            </section>
        </main>
    </div>
</template>
