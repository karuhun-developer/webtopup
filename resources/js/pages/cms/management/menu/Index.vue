<script setup lang="ts">
import {
    create,
    destroy,
    edit,
} from '@/actions/App/Http/Controllers/Cms/Management/MenuController';
import { index as subMenuIndex } from '@/actions/App/Http/Controllers/Cms/Management/MenuSubController';
import Heading from '@/components/Heading.vue';
import ResourceTable from '@/components/ResourceTable.vue';
import { Button } from '@/components/ui/button';
import { usePermission } from '@/composables/usePermission';
import { useSwal } from '@/composables/useSwal';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, PaginationItem } from '@/types';
import { MenuDataItem } from '@/types/cms/management';
import { Head, Link, router } from '@inertiajs/vue3';
import { ModalLink } from '@inertiaui/modal-vue';
import dayjs from 'dayjs';
import { ListOrdered, Pencil, Plus, Trash2 } from 'lucide-vue-next';

defineProps<{
    data: PaginationItem<MenuDataItem>;
    order: 'asc' | 'desc';
    orderBy: string;
    paginate: number;
    searchBySpecific: string;
    search: string;
    resource: string;
}>();

const { confirm, toast } = useSwal();
const { hasPermission } = usePermission();

const title = 'Menu Management';
const description = 'Manage menus within the application.';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Managements',
        href: '#',
    },
    {
        title: title,
        href: '#',
    },
];

const columns = [
    { label: 'Role', key: 'roles.name', sortable: true },
    { label: 'Name', key: 'menus.name', sortable: true },
    { label: 'URL', key: 'menus.url', sortable: true },
    { label: 'Icon', key: 'menus.icon', sortable: true },
    { label: 'Order', key: 'menus.order', sortable: true },
    { label: 'Active Pattern', key: 'menus.active_pattern', sortable: true },
    { label: 'Status', key: 'menus.status', sortable: true },
    { label: 'Created At', key: 'menus.created_at', sortable: true },
    {
        label: 'Actions',
        key: 'actions',
        sortable: false,
        class: 'w-24 text-center',
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
                        <Plus />
                        Create
                    </Button>
                </ModalLink>
            </div>

            <ResourceTable
                :data="data"
                :columns="columns"
                :order="order"
                :orderBy="orderBy"
                :paginate="paginate"
                :searchBySpecific="searchBySpecific"
                :search="search"
                :hasActions="true"
            >
                <template #roles.name="{ row }">
                    {{ row.role_name }}
                </template>
                <template #menus.name="{ row }">
                    {{ row.name }}
                </template>
                <template #menus.url="{ row }">
                    {{ row.url }}
                </template>
                <template #menus.icon="{ row }">
                    {{ row.icon ?? '-' }}
                </template>
                <template #menus.order="{ row }">
                    {{ row.order }}
                </template>
                <template #menus.active_pattern="{ row }">
                    {{ row.active_pattern }}
                </template>
                <template #menus.status="{ row }">
                    <span
                        :class="{
                            'rounded-full px-2 py-1 text-sm font-medium': true,
                            'bg-green-100 text-green-800': row.status,
                            'bg-red-100 text-red-800': !row.status,
                        }"
                    >
                        {{ row.status ? 'Active' : 'Inactive' }}
                    </span>
                </template>
                <template #menus.created_at="{ row }">
                    {{
                        dayjs(row.created_at)
                            .locale('id')
                            .format('DD MMMM YYYY HH:mm:ss')
                    }}
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-center gap-2">
                        <ModalLink
                            :href="edit({ menu: row.id }).url"
                            slideover
                            v-if="hasPermission('update' + resource)"
                        >
                            <Button variant="ghost" size="icon">
                                <Pencil class="h-4 w-4" />
                            </Button>
                        </ModalLink>
                        <Link
                            :href="subMenuIndex({ menu: row.id }).url"
                            v-if="hasPermission('view' + resource)"
                        >
                            <Button
                                size="sm"
                                class="h-8 bg-yellow-500 px-2 text-black hover:bg-yellow-600"
                            >
                                <ListOrdered class="h-4 w-4" />
                                Sub Menu
                            </Button>
                        </Link>
                        <Button
                            variant="ghost"
                            size="icon"
                            v-if="hasPermission('delete' + resource)"
                            @click="
                                confirm({
                                    title: 'Delete Menu?',
                                    text: 'This action cannot be undone.',
                                    icon: 'warning',
                                    confirmButtonText: 'Yes, delete it!',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        router.delete(
                                            destroy({ menu: row.id }).url,
                                            {
                                                preserveScroll: true,
                                                preserveState: true,
                                                onSuccess: () => {
                                                    toast.fire({
                                                        icon: 'success',
                                                        title: 'Menu deleted.',
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
