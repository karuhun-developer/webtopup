<script setup lang="ts">
import { show } from '@/actions/App/Http/Controllers/Main/BrandController';
import { PPOBBrandDataItem } from '@/types/cms/ppob';
import { Link } from '@inertiajs/vue3';
import { ShoppingBag } from 'lucide-vue-next';

const props = defineProps<{
    brand: PPOBBrandDataItem;
}>();
</script>

<template>
    <div
        class="group relative overflow-hidden rounded-lg border border-border/50 bg-card shadow-md transition-all duration-300 hover:scale-105 hover:border-primary/50 hover:shadow-xl"
    >
        <Link
            :href="
                show({
                    brand: brand.slug,
                }).url
            "
        >
            <!-- Brand Image -->
            <div class="relative aspect-[3/4] overflow-hidden bg-muted">
                <img
                    v-if="brand.image"
                    :src="brand.image"
                    :alt="brand.name"
                    class="h-full w-full object-cover"
                />
                <div
                    v-else
                    class="flex h-full w-full items-center justify-center text-muted-foreground"
                >
                    <ShoppingBag class="h-15 w-15 opacity-20" />
                </div>

                <!-- Overlay on hover -->
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                ></div>
            </div>

            <!-- Brand Info -->
            <div class="p-4">
                <!-- Name -->
                <h3
                    class="mb-2 line-clamp-2 text-sm font-semibold text-foreground"
                >
                    {{ brand.name }}
                </h3>
            </div>
        </Link>
    </div>
</template>
