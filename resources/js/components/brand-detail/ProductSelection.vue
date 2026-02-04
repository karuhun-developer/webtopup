<script setup lang="ts">
import { formatCurrency } from '@/lib/utils';
import { PPOBProductDataItem } from '@/types/cms/ppob';

defineProps<{
    products: PPOBProductDataItem[];
    selectedProduct: number | null;
}>();

const emit = defineEmits<{
    'update:selectedProduct': [value: number];
}>();
</script>

<template>
    <section class="rounded-lg border border-border/50 bg-card p-6 shadow-sm">
        <h2
            class="mb-4 flex items-center gap-2 text-lg font-bold text-foreground"
        >
            <span
                class="flex h-7 w-7 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground"
                >2</span
            >
            Pilih Produk
        </h2>

        <div
            v-if="products && products.length > 0"
            class="grid grid-cols-2 gap-3 sm:grid-cols-3"
        >
            <button
                v-for="product in products"
                :key="product.id"
                class="rounded-lg border-2 p-4 text-left transition-all"
                :class="
                    selectedProduct === product.id
                        ? 'border-primary bg-primary/5'
                        : 'border-border/50 hover:border-primary/50'
                "
                @click="emit('update:selectedProduct', product.id)"
            >
                <div class="mb-2">
                    <img
                        v-if="product.image"
                        :src="product.image"
                        :alt="product.name"
                        class="h-12 w-12 object-contain"
                    />
                </div>
                <div class="text-sm font-semibold text-foreground">
                    {{ product.name }}
                </div>
                <div class="mt-2 text-sm font-bold text-primary">
                    {{ formatCurrency(product.sell_price) }}
                </div>
            </button>
        </div>
        <div v-else class="py-8 text-center text-sm text-muted-foreground">
            Tidak ada produk tersedia
        </div>
    </section>
</template>
