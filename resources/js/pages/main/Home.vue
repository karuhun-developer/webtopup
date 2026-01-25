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

interface Category {
    id: number;
    name: string;
    icon: string;
    count: number;
}

interface Brand {
    id: number;
    name: string;
    logo: string;
    category: string;
}

interface Props {
    products?: Product[];
    categories?: Category[];
    brands?: Brand[];
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
    categories: () => [
        { id: 1, name: 'Games', icon: '🎮', count: 150 },
        { id: 2, name: 'Pulsa', icon: '📱', count: 12 },
        { id: 3, name: 'Data', icon: '📶', count: 24 },
        { id: 4, name: 'E-Wallet', icon: '💳', count: 8 },
        { id: 5, name: 'Voucher', icon: '🎫', count: 45 },
    ],
    brands: () => [
        { id: 1, name: 'Mobile Legends', logo: 'https://placehold.co/100x100/7C3AED/FFFFFF?text=ML', category: 'Games' },
        { id: 2, name: 'PUBG Mobile', logo: 'https://placehold.co/100x100/F59E0B/FFFFFF?text=PUBG', category: 'Games' },
        { id: 3, name: 'Free Fire', logo: 'https://placehold.co/100x100/DC2626/FFFFFF?text=FF', category: 'Games' },
        { id: 4, name: 'Genshin Impact', logo: 'https://placehold.co/100x100/8B5CF6/FFFFFF?text=GI', category: 'Games' },
        { id: 5, name: 'Telkomsel', logo: 'https://placehold.co/100x100/DC2626/FFFFFF?text=TSEL', category: 'Pulsa' },
        { id: 6, name: 'XL Axiata', logo: 'https://placehold.co/100x100/3B82F6/FFFFFF?text=XL', category: 'Pulsa' },
        { id: 7, name: 'Indosat', logo: 'https://placehold.co/100x100/F59E0B/FFFFFF?text=ISAT', category: 'Pulsa' },
        { id: 8, name: 'Tri', logo: 'https://placehold.co/100x100/EC4899/FFFFFF?text=TRI', category: 'Pulsa' },
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

            <!-- Categories Section -->
            <section class="mb-8">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-foreground">Kategori</h2>
                </div>
                <div class="flex gap-3 overflow-x-auto pb-2">
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        class="flex min-w-[140px] flex-col items-center gap-2 rounded-lg border border-border/50 bg-card p-4 transition-all hover:scale-105 hover:border-primary/50 hover:shadow-md"
                    >
                        <span class="text-3xl">{{ category.icon }}</span>
                        <span class="text-sm font-semibold text-foreground">{{ category.name }}</span>
                        <span class="text-xs text-muted-foreground">{{ category.count }} produk</span>
                    </button>
                </div>
            </section>

            <!-- Brands Section -->
            <section class="mb-8">
                <div class="mb-4">
                    <h2 class="text-xl font-bold text-foreground">Brand Populer</h2>
                </div>
                <div class="flex gap-3 overflow-x-auto pb-2">
                    <button
                        v-for="brand in brands"
                        :key="brand.id"
                        class="flex min-w-[100px] flex-col items-center gap-2 rounded-lg border border-border/50 bg-card p-3 transition-all hover:scale-105 hover:border-primary/50 hover:shadow-md"
                    >
                        <img
                            :src="brand.logo"
                            :alt="brand.name"
                            class="h-12 w-12 rounded-md object-cover"
                        />
                        <span class="text-center text-xs font-medium text-foreground">{{ brand.name }}</span>
                    </button>
                </div>
            </section>

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
