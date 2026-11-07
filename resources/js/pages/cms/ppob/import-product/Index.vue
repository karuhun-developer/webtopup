<script setup lang="ts">
import {
    index,
    sync,
} from '@/actions/App/Http/Controllers/Cms/PPOB/ImportProductController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useSwal } from '@/composables/useSwal';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatCurrency } from '@/lib/utils';
import { BreadcrumbItem } from '@/types';
import { ImportedCategoryGroup } from '@/types/cms/ppob';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ChevronRight, Save } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    products: ImportedCategoryGroup[];
    provider: string;
    error?: string | null;
}>();

const { toast } = useSwal();

const title = 'Import Products';
const description = 'Import katalog dari provider yang dipilih.';

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'PPOB', href: '#' },
    { title: title, href: '#' },
];

const openCategories = ref<string[]>([]);
const openBrands = ref<string[]>([]);

const isCategoryOpen = (name: string) => openCategories.value.includes(name);
const toggleCategoryOpen = (name: string) => {
    if (isCategoryOpen(name)) {
        openCategories.value = openCategories.value.filter((n) => n !== name);
    } else {
        openCategories.value.push(name);
    }
};

const isBrandOpen = (name: string) => openBrands.value.includes(name);
const toggleBrandOpen = (name: string) => {
    if (isBrandOpen(name)) {
        openBrands.value = openBrands.value.filter((n) => n !== name);
    } else {
        openBrands.value.push(name);
    }
};

// Submit - Import all products
const form = useForm({ provider: props.provider });

const submit = () => {
    form.post(sync().url, {
        onSuccess: () => {
            toast.fire({
                icon: 'success',
                title: 'Products imported successfully.',
            });
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="title" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <Heading :title="title" :description="description" />
                <Button
                    :disabled="form.processing || !!error || !products.length"
                    @click="submit"
                >
                    <Save class="mr-2 h-4 w-4" />
                    Import All Products
                </Button>
            </div>

            <div class="grid gap-3 sm:max-w-sm">
                <Select
                    :model-value="provider"
                    @update:model-value="
                        router.get(index.url(), { provider: String($event) })
                    "
                >
                    <SelectTrigger class="w-full"
                        ><SelectValue
                    /></SelectTrigger>
                    <SelectContent
                        ><SelectItem value="h2h">H2H Karuhun</SelectItem
                        ><SelectItem value="digiflazz"
                            >Digiflazz</SelectItem
                        ></SelectContent
                    >
                </Select>
                <InputError :message="error || form.errors.provider" />
            </div>
            <p
                v-if="!products.length && !error"
                class="text-sm text-muted-foreground"
            >
                Belum ada produk tersedia dari provider ini.
            </p>
            <div class="space-y-2">
                <Collapsible
                    v-for="category in products"
                    :key="category.name"
                    :open="openCategories.includes(category.name)"
                    @update:open="toggleCategoryOpen(category.name)"
                    class="rounded-lg border bg-card text-card-foreground shadow-sm"
                >
                    <div class="flex items-center gap-2 p-4">
                        <CollapsibleTrigger
                            class="rounded-md p-1 hover:bg-muted"
                        >
                            <ChevronRight
                                class="h-4 w-4 transition-transform duration-200"
                                :class="{
                                    'rotate-90': isCategoryOpen(category.name),
                                }"
                            />
                        </CollapsibleTrigger>
                        <span class="font-medium">{{ category.name }}</span>
                        <span class="ml-auto text-sm text-muted-foreground">
                            {{ category.brands.length }} Brands
                        </span>
                    </div>

                    <CollapsibleContent>
                        <div class="ml-6 space-y-2 border-l pr-4 pb-4 pl-4">
                            <Collapsible
                                v-for="brand in category.brands"
                                :key="brand.name"
                                :open="openBrands.includes(brand.name)"
                                @update:open="toggleBrandOpen(brand.name)"
                                class="rounded-lg border bg-card text-card-foreground shadow-sm"
                            >
                                <div class="flex items-center gap-2 p-3">
                                    <CollapsibleTrigger
                                        class="rounded-md p-1 hover:bg-muted"
                                    >
                                        <ChevronRight
                                            class="h-4 w-4 transition-transform duration-200"
                                            :class="{
                                                'rotate-90': isBrandOpen(
                                                    brand.name,
                                                ),
                                            }"
                                        />
                                    </CollapsibleTrigger>
                                    <span class="font-medium">{{
                                        brand.name
                                    }}</span>
                                    <span
                                        class="ml-auto text-sm text-muted-foreground"
                                    >
                                        {{ brand.products.length }} Products
                                    </span>
                                </div>

                                <CollapsibleContent>
                                    <div
                                        class="ml-6 grid grid-cols-1 gap-2 border-l pt-2 pl-4 md:grid-cols-2 lg:grid-cols-3"
                                    >
                                        <div
                                            v-for="product in brand.products"
                                            :key="product.sku"
                                            class="flex items-start gap-2 rounded-md border p-3"
                                        >
                                            <div class="grid flex-1 gap-1">
                                                <div
                                                    class="leading-none font-medium"
                                                >
                                                    {{ product.name }}
                                                </div>
                                                <div
                                                    class="text-xs text-muted-foreground"
                                                >
                                                    SKU:
                                                    {{ product.sku }}
                                                </div>
                                                <div
                                                    class="text-sm font-semibold"
                                                >
                                                    {{
                                                        formatCurrency(
                                                            product.price,
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </CollapsibleContent>
                            </Collapsible>
                        </div>
                    </CollapsibleContent>
                </Collapsible>
            </div>
        </div>
    </AppLayout>
</template>
