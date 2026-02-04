<script setup lang="ts">
import { Star } from 'lucide-vue-next';

defineProps<{
    rating: number;
    totalReviews: number;
}>();
</script>

<template>
    <div class="rounded-lg border border-border/50 bg-card p-6 shadow-sm">
        <h3 class="mb-3 text-sm font-semibold text-foreground">
            Ulasan dan rating
        </h3>

        <div class="flex items-center gap-3">
            <div class="text-4xl font-bold text-foreground">
                {{ rating.toFixed(2) }}
            </div>
            <div class="flex-1">
                <div class="mb-1 flex gap-1">
                    <Star
                        v-for="i in 5"
                        :key="i"
                        class="h-5 w-5 fill-yellow-400 text-yellow-400"
                    />
                </div>
                <p class="text-xs text-muted-foreground">
                    Berdasarkan total {{ totalReviews.toLocaleString('id-ID') }}
                    rating
                </p>
            </div>
        </div>
    </div>
</template>
