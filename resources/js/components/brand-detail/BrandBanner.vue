<script setup lang="ts">
import { PPOBBrandDataItem } from '@/types/cms/ppob';

defineProps<{
    brand: PPOBBrandDataItem;
}>();
</script>

<template>
    <div
        class="relative h-64 overflow-hidden bg-gradient-to-r from-primary/20 to-primary/5"
    >
        <img
            v-if="brand.banner"
            :src="brand.banner"
            :alt="brand.name"
            class="h-full w-full object-cover"
        />
        <div
            class="absolute inset-0 bg-gradient-to-t from-background/80 to-transparent"
        ></div>

        <!-- Brand Info Overlay -->
        <div class="absolute right-0 bottom-0 left-0 p-6">
            <div class="mx-auto max-w-5xl">
                <div class="flex items-end gap-4">
                    <!-- Brand Logo -->
                    <div
                        class="h-20 w-20 overflow-hidden rounded-lg border-4 border-background bg-card shadow-lg"
                    >
                        <img
                            v-if="brand.image"
                            :src="brand.image"
                            :alt="brand.name"
                            class="h-full w-full object-cover"
                        />
                    </div>

                    <!-- Brand Name & Features -->
                    <div class="flex-1">
                        <h1 class="mb-2 text-2xl font-bold text-foreground">
                            {{ brand.name }}
                        </h1>
                        <div class="flex flex-wrap gap-2">
                            <div
                                class="flex items-center gap-1 rounded-full bg-background/80 px-3 py-1 text-xs font-medium text-foreground"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-3 w-3"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                                Proses Cepat
                            </div>
                            <div
                                class="flex items-center gap-1 rounded-full bg-background/80 px-3 py-1 text-xs font-medium text-foreground"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-3 w-3"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
                                    />
                                </svg>
                                Layanan Chat 24/7
                            </div>
                            <div
                                class="flex items-center gap-1 rounded-full bg-background/80 px-3 py-1 text-xs font-medium text-foreground"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-3 w-3"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                                    />
                                </svg>
                                Pembayaran Aman
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
