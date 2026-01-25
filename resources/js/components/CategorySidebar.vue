<script setup lang="ts">
import { ref } from 'vue';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';

interface Category {
    id: string;
    label: string;
}

interface Props {
    categories?: Category[];
    platforms?: Category[];
}

const props = withDefaults(defineProps<Props>(), {
    categories: () => [
        { id: 'adventure', label: 'Adventure' },
        { id: 'moba', label: 'Moba Game' },
        { id: 'rpg', label: 'RPG' },
        { id: 'casual', label: 'Casual Game' },
        { id: 'strategy', label: 'Strategy' },
        { id: 'simulator', label: 'Simulator' },
        { id: 'sports', label: 'Sports Game' },
    ],
    platforms: () => [
        { id: 'smartphone', label: 'Smartphone' },
        { id: 'pc', label: 'PC' },
        { id: 'nintendo', label: 'Nintendo' },
        { id: 'ps4', label: 'Playstation 4' },
        { id: 'ps5', label: 'Playstation 5' },
        { id: 'xbox', label: 'X-Box' },
    ],
});

const selectedCategories = ref<string[]>([]);
const selectedPlatforms = ref<string[]>([]);
</script>

<template>
    <aside class="w-full rounded-lg border border-border/50 bg-card p-4 shadow-sm lg:w-64">
        <!-- Categories Section -->
        <div class="mb-6">
            <h3 class="mb-3 text-sm font-bold text-foreground">Categories</h3>
            <div class="space-y-2">
                <div
                    v-for="category in categories"
                    :key="category.id"
                    class="flex items-center gap-2"
                >
                    <Checkbox
                        :id="`category-${category.id}`"
                        v-model:checked="selectedCategories"
                        :value="category.id"
                    />
                    <Label
                        :for="`category-${category.id}`"
                        class="cursor-pointer text-sm font-normal text-muted-foreground hover:text-foreground"
                    >
                        {{ category.label }}
                    </Label>
                </div>
            </div>
        </div>

        <!-- Platforms Section -->
        <div>
            <h3 class="mb-3 text-sm font-bold text-foreground">Platforms</h3>
            <div class="space-y-2">
                <div
                    v-for="platform in platforms"
                    :key="platform.id"
                    class="flex items-center gap-2"
                >
                    <Checkbox
                        :id="`platform-${platform.id}`"
                        v-model:checked="selectedPlatforms"
                        :value="platform.id"
                    />
                    <Label
                        :for="`platform-${platform.id}`"
                        class="cursor-pointer text-sm font-normal text-muted-foreground hover:text-foreground"
                    >
                        {{ platform.label }}
                    </Label>
                </div>
            </div>
        </div>
    </aside>
</template>
