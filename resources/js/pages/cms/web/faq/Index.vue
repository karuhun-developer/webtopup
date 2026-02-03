<script setup lang="ts">
import {
    create,
    destroy,
    edit,
} from '@/actions/App/Http/Controllers/Cms/Web/FaqController';
import Heading from '@/components/Heading.vue';
import ResourceTable from '@/components/ResourceTable.vue';
import { Button } from '@/components/ui/button';
import { usePermission } from '@/composables/usePermission';
import { useSwal } from '@/composables/useSwal';
import AppLayout from '@/layouts/AppLayout.vue';
import { PaginationItem, type BreadcrumbItem } from '@/types';
import { FaqDataItem } from '@/types/cms/web';
import { Head, router } from '@inertiajs/vue3';
import { ModalLink } from '@inertiaui/modal-vue';
import dayjs from 'dayjs';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';

defineProps<{
    data: PaginationItem<FaqDataItem>;
    orderBy?: string;
    order?: 'asc' | 'desc';
    search?: string;
    paginate?: number;
    resource: string;
}>();

const { confirm, toast } = useSwal();
const { hasPermission } = usePermission();

const title = 'Faq';
const description =
    'Manage faqs to be displayed on the website homepage. You can create, edit, and delete faqs as needed.';

const columns = [
    { label: 'Question', key: 'question', sortable: true },
    { label: 'Answer', key: 'answer', sortable: true },
    { label: 'Order', key: 'order', sortable: true },
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
        title: 'Web',
        href: '#',
    },
    {
        title: title,
        href: '#',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="title" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <Heading :title="title" :description="description" />
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
            <ResourceTable
                :data="data"
                :columns="columns"
                :order-by="orderBy"
                :order="order"
                :search="search"
                :paginate="paginate"
            >
                <template #question="{ row }">
                    <span v-html="row.question"></span>
                </template>
                <template #answer="{ row }">
                    <span v-html="row.answer"></span>
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
                            :href="edit({ faq: row.id }).url"
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
                                    title: 'Delete Faq',
                                    text: 'This action cannot be undone.',
                                    icon: 'warning',
                                    confirmButtonText: 'Yes, delete it!',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        router.delete(
                                            destroy({ faq: row.id }).url,
                                            {
                                                preserveScroll: true,
                                                preserveState: true,
                                                onSuccess: () => {
                                                    toast.fire({
                                                        icon: 'success',
                                                        title: 'Faq deleted successfully.',
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
