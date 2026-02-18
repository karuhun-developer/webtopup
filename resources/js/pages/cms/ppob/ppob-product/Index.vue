<script setup lang="ts">
import {
    create,
    destroy,
    edit,
} from '@/actions/App/Http/Controllers/Cms/PPOB/PPOBProductController';
import Heading from '@/components/Heading.vue';
import ResourceTable from '@/components/ResourceTable.vue';
import { Button } from '@/components/ui/button';
import Label from '@/components/ui/label/Label.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useFilter } from '@/composables/useFilter';
import { usePermission } from '@/composables/usePermission';
import { useSwal } from '@/composables/useSwal';
import AppLayout from '@/layouts/AppLayout.vue';
import { formatCurrency } from '@/lib/utils';
import { PaginationItem, type BreadcrumbItem } from '@/types';
import {
    PPOBBrandDataItem,
    PPOBCategoryDataItem,
    PPOBProductDataItem,
} from '@/types/cms/ppob';
import { Head, router } from '@inertiajs/vue3';
import { ModalLink } from '@inertiaui/modal-vue';
import dayjs from 'dayjs';
import { Image, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    categories: PPOBCategoryDataItem[];
    brands: PPOBBrandDataItem[];
    filter_category_id?: number | null;
    filter_brand_id?: number | null;
    data: PaginationItem<PPOBProductDataItem>;
    orderBy?: string;
    order?: 'asc' | 'desc';
    search?: string;
    paginate?: number;
    resource: string;
}>();

const { confirm, toast } = useSwal();
const { hasPermission } = usePermission();

const title = 'PPOB Products';
const description =
    'Manage PPOB products available in the system, including their categories and brands.';

const columns = [
    { label: 'Category', key: 'category', sortable: false },
    { label: 'Brand', key: 'brand', sortable: false },
    { label: 'Name', key: 'name', sortable: true },
    { label: 'Sku', key: 'sku', sortable: true },
    { label: 'Buy Price', key: 'buy_price', sortable: true },
    { label: 'Sell Price', key: 'sell_price', sortable: true },
    { label: 'Image', key: 'image', sortable: false },
    { label: 'Status', key: 'status', sortable: true },
    { label: 'Created At', key: 'created_at', sortable: true },
    {
        label: 'Actions',
        key: 'actions',
        sortable: false,
        class: 'w-24 text-center',
    },
];

// Breadcrumbs
const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'PPOB',
        href: '#',
    },
    {
        title: title,
        href: '#',
    },
];

// Filter State
const filter_category_id = ref<number | null>(props.filter_category_id || null);
const filter_brand_id = ref<number | null>(props.filter_brand_id || null);

const { updateParams } = useFilter();

watch(filter_category_id, (newValue) => {
    const params: Record<string, any> = {};

    if (newValue !== null) {
        params.filter_category_id = newValue;
    } else {
        params.filter_category_id = null;
    }

    params.filter_brand_id = null; // Reset brand filter when category changes

    // Reset to page 1 when filter changes
    params.page = 1;

    updateParams(params);
});

watch(filter_brand_id, (newValue) => {
    const params: Record<string, any> = {};

    if (newValue !== null) {
        params.filter_brand_id = newValue;
    } else {
        params.filter_brand_id = null;
    }

    // Reset to page 1 when filter changes
    params.page = 1;

    updateParams(params);
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="title" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-wrap items-center justify-between">
                <Heading :title="title" :description="description" />
                <div class="flex items-center gap-2">
                    <ModalLink
                        :href="create().url"
                        slideover
                        v-if="hasPermission('create' + resource)"
                    >
                        <Button>
                            <Plus class="h-4 w-4" />
                            Create
                        </Button>
                    </ModalLink>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="flex flex-col">
                    <Label for="status" class="mb-3">Category Filter</Label>
                    <Select
                        name="filter_category_id"
                        v-model="filter_category_id"
                    >
                        <SelectTrigger
                            id="filter_category_id"
                            class="mt-1 w-full"
                        >
                            <SelectValue placeholder="Select a Category" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem :value="null">
                                -- All Categories --
                            </SelectItem>
                            <SelectItem
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="flex flex-col">
                    <Label for="status" class="mb-3">Brand Filter</Label>
                    <Select name="filter_brand_id" v-model="filter_brand_id">
                        <SelectTrigger id="filter_brand_id" class="mt-1 w-full">
                            <SelectValue placeholder="Select a Brand" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem :value="null">
                                -- All Brands --
                            </SelectItem>
                            <SelectItem
                                v-for="brand in brands"
                                :key="brand.id"
                                :value="brand.id"
                            >
                                {{ brand.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <ResourceTable
                :data="data"
                :columns="columns"
                :order-by="orderBy"
                :order="order"
                :search="search"
                :paginate="paginate"
            >
                <template #category="{ row }">
                    {{ row.brand?.category?.name }}
                </template>
                <template #brand="{ row }">
                    {{ row.brand?.name }}
                </template>
                <template #buy_price="{ row }">
                    {{ formatCurrency(row.buy_price) }}
                </template>
                <template #sell_price="{ row }">
                    {{ formatCurrency(row.sell_price) }}
                </template>
                <template #status="{ row }">
                    <span
                        :class="{
                            'rounded-full px-2 py-1 text-xs font-semibold': true,
                            'bg-green-100 text-green-800': row.status,
                            'bg-red-100 text-red-800': !row.status,
                        }"
                    >
                        {{ row.status ? 'Active' : 'Inactive' }}
                    </span>
                </template>
                <template #image="{ row }">
                    <img
                        v-if="row.image"
                        :src="row.image"
                        alt="Brand Image"
                        class="h-15 w-15 rounded object-cover"
                    />
                    <div
                        v-else
                        class="flex h-15 w-15 items-center justify-center rounded bg-muted"
                    >
                        <Image class="h-6 w-6" />
                    </div>
                </template>
                <template #created_at="{ row }">
                    {{ dayjs(row.created_at).format('DD MMMM YYYY HH:mm:ss') }}
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-center gap-2">
                        <ModalLink
                            :href="edit({ product: row.slug }).url"
                            slideover
                            v-if="hasPermission('update' + resource)"
                        >
                            <Button variant="ghost" size="icon">
                                <Pencil class="h-4 w-4" />
                            </Button>
                        </ModalLink>
                        <Button
                            variant="ghost"
                            size="icon"
                            v-if="hasPermission('delete' + resource)"
                            @click="
                                confirm({
                                    title: 'Delete PPOB Product',
                                    text: 'This action cannot be undone.',
                                    icon: 'warning',
                                    confirmButtonText: 'Yes, delete it!',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        router.delete(
                                            destroy({ product: row.slug }).url,
                                            {
                                                preserveScroll: true,
                                                preserveState: true,
                                                onSuccess: () => {
                                                    toast.fire({
                                                        icon: 'success',
                                                        title: 'PPOB Product deleted successfully.',
                                                    });
                                                },
                                            },
                                        );
                                    }
                                })
                            "
                        >
                            <Trash2 class="h-4 w-4 text-destructive" />
                        </Button>
                    </div>
                </template>
            </ResourceTable>
        </div>
    </AppLayout>
</template>
