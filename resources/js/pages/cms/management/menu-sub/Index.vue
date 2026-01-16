<script setup lang="ts">
import { index as menusIndex } from '@/actions/App/Http/Controllers/Cms/Management/MenuController';
import {
    create,
    destroy,
    edit,
} from '@/actions/App/Http/Controllers/Cms/Management/MenuSubController';
import Heading from '@/components/Heading.vue';
import ResourceTable from '@/components/ResourceTable.vue';
import { Button } from '@/components/ui/button';
import { usePermission } from '@/composables/usePermission';
import { useSwal } from '@/composables/useSwal';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem, PaginationItem } from '@/types';
import { MenuDataItem, MenuSubDataItem } from '@/types/cms/management';
import { Head, router } from '@inertiajs/vue3';
import { ModalLink } from '@inertiaui/modal-vue';
import dayjs from 'dayjs';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';

const props = defineProps<{
    data: PaginationItem<MenuSubDataItem>;
    order: 'asc' | 'desc';
    orderBy: string;
    paginate: number;
    searchBySpecific: string;
    search: string;
    resource: string;
    menu: MenuDataItem;
}>();

const { confirm, toast } = useSwal();
const { hasPermission } = usePermission();

const title = props.menu.name + ' Sub Menu';
const description =
    'Manage ' + props.menu.name + ' sub menus within the application.';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Managements',
        href: '#',
    },
    {
        title: 'Menu Management',
        href: menusIndex().url,
    },
    {
        title: title,
        href: '#',
    },
];

const columns = [
    { label: 'Name', key: 'menu_subs.name', sortable: true },
    { label: 'URL', key: 'menu_subs.url', sortable: true },
    { label: 'Icon', key: 'menu_subs.icon', sortable: true },
    { label: 'Order', key: 'menu_subs.order', sortable: true },
    {
        label: 'Active Pattern',
        key: 'menu_subs.active_pattern',
        sortable: true,
    },
    { label: 'Status', key: 'menu_subs.status', sortable: true },
    { label: 'Created At', key: 'menu_subs.created_at', sortable: true },
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
                    :href="create({ menu: menu.id }).url"
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
                <template #menu_subs.name="{ row }">
                    {{ row.name }}
                </template>
                <template #menu_subs.url="{ row }">
                    {{ row.url }}
                </template>
                <template #menu_subs.icon="{ row }">
                    {{ row.icon ?? '-' }}
                </template>
                <template #menu_subs.order="{ row }">
                    {{ row.order }}
                </template>
                <template #menu_subs.active_pattern="{ row }">
                    {{ row.active_pattern }}
                </template>
                <template #menu_subs.status="{ row }">
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
                <template #menu_subs.created_at="{ row }">
                    {{
                        dayjs(row.created_at)
                            .locale('id')
                            .format('DD MMMM YYYY HH:mm:ss')
                    }}
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-center gap-2">
                        <ModalLink
                            :href="
                                edit({ menu: menu.id, sub_menu: row.id }).url
                            "
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
                                    title: 'Delete Menu Sub?',
                                    text: 'This action cannot be undone.',
                                    icon: 'warning',
                                    confirmButtonText: 'Yes, delete it!',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        router.delete(
                                            destroy({
                                                menu: menu.id,
                                                sub_menu: row.id,
                                            }).url,
                                            {
                                                preserveScroll: true,
                                                preserveState: true,
                                                onSuccess: () => {
                                                    toast.fire({
                                                        icon: 'success',
                                                        title: 'Sub Menu deleted.',
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
