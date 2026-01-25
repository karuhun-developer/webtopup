<script setup lang="ts">
import { show } from '@/actions/App/Http/Controllers/Main/ProductController';
import { Link } from '@inertiajs/vue3';
import InputDescription from './InputDescription.vue';

interface Props {
    image: string;
    title: string;
    description?: string;
    rating?: number;
    buttonText?: string;
}

const props = withDefaults(defineProps<Props>(), {
    rating: 5,
    buttonText: 'Detail',
});
</script>

<template>
    <div
        class="group relative overflow-hidden rounded-lg border border-border/50 bg-card shadow-md transition-all duration-300 hover:scale-105 hover:border-primary/50 hover:shadow-xl"
    >
        <Link
            :href="
                show({
                    product: title,
                }).url
            "
        >
            <!-- Product Image -->
            <div class="relative aspect-[3/4] overflow-hidden bg-muted">
                <img
                    :src="image"
                    :alt="title"
                    class="h-full w-full object-cover"
                />
                <!-- Overlay on hover -->
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
                ></div>
            </div>

            <!-- Product Info -->
            <div class="p-4">
                <!-- Title -->
                <h3
                    class="mb-2 line-clamp-2 text-sm font-semibold text-foreground"
                >
                    {{ title }}
                </h3>

                <!-- Star Rating -->
                <div class="mb-3 flex items-center gap-1">
                    <div v-for="i in 5" :key="i" class="text-yellow-400">
                        <svg
                            v-if="i <= rating"
                            class="h-3 w-3 fill-current"
                            viewBox="0 0 20 20"
                        >
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"
                            />
                        </svg>
                        <svg
                            v-else
                            class="h-3 w-3 fill-current text-muted"
                            viewBox="0 0 20 20"
                        >
                            <path
                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"
                            />
                        </svg>
                    </div>
                </div>
                <InputDescription>
                    {{ description }}
                </InputDescription>
            </div>
        </Link>
    </div>
</template>
