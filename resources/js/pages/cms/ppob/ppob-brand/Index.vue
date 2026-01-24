<script setup lang="ts">
import {
    create,
    destroy,
    edit,
    index,
} from '@/actions/App/Http/Controllers/Cms/PPOB/PPOBBrandController';
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
import { usePermission } from '@/composables/usePermission';
import { useSwal } from '@/composables/useSwal';
import AppLayout from '@/layouts/AppLayout.vue';
import { PaginationItem, type BreadcrumbItem } from '@/types';
import { PPOBBrandDataItem, PPOBCategoryDataItem } from '@/types/cms/ppob';
import { Head, router } from '@inertiajs/vue3';
import { ModalLink } from '@inertiaui/modal-vue';
import dayjs from 'dayjs';
import { Image, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';

defineProps<{
    categories: PPOBCategoryDataItem[];
    data: PaginationItem<PPOBBrandDataItem>;
    orderBy?: string;
    order?: 'asc' | 'desc';
    search?: string;
    paginate?: number;
    resource: string;
}>();

const { confirm, toast } = useSwal();
const { hasPermission } = usePermission();

const title = 'PPOB Brands';
const description =
    'Manage brands for PPOB services including creation, editing, and deletion.';

const columns = [
    { label: 'Category', key: 'category', sortable: false },
    { label: 'Name', key: 'name', sortable: true },
    { label: 'Description', key: 'description', sortable: true },
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
const filter_category_id = ref<number | null>(null);

watch(filter_category_id, (newValue) => {
    const query: Record<string, any> = {};
    if (newValue) {
        query.filter_category_id = newValue;
    }
    router.get(index().url, query, {
        preserveState: true,
        replace: true,
    });
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
                    {{ row.category?.name }}
                </template>
                <template #description="{ row }">
                    <span v-html="row.description"></span>
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
                <template #created_at="{ row }">
                    {{ dayjs(row.created_at).format('DD MMMM YYYY H:m:s') }}
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-center gap-2">
                        <ModalLink
                            :href="edit({ brand: row.slug }).url"
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
                                    title: 'Delete PPOB Brand',
                                    text: 'This action cannot be undone.',
                                    icon: 'warning',
                                    confirmButtonText: 'Yes, delete it!',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        router.delete(
                                            destroy({ brand: row.slug }).url,
                                            {
                                                preserveScroll: true,
                                                preserveState: true,
                                                onSuccess: () => {
                                                    toast.fire({
                                                        icon: 'success',
                                                        title: 'PPOB Brand deleted successfully.',
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
