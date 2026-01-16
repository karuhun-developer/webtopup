<script setup lang="ts">
import {
    create,
    destroy,
    edit,
    editPassword,
} from '@/actions/App/Http/Controllers/Cms/Management/UserController';
import Heading from '@/components/Heading.vue';
import ResourceTable from '@/components/ResourceTable.vue';
import { Button } from '@/components/ui/button';
import { usePermission } from '@/composables/usePermission';
import { useSwal } from '@/composables/useSwal';
import AppLayout from '@/layouts/AppLayout.vue';
import { PaginationItem, type BreadcrumbItem } from '@/types';
import { UserDataItem } from '@/types/cms/management';
import { Head, router } from '@inertiajs/vue3';
import { ModalLink } from '@inertiaui/modal-vue';
import dayjs from 'dayjs';
import { KeyRound, Pencil, Plus, Trash2 } from 'lucide-vue-next';

defineProps<{
    data: PaginationItem<UserDataItem>;
    orderBy?: string;
    order?: 'asc' | 'desc';
    search?: string;
    paginate?: number;
    resource: string;
}>();

const { confirm, toast } = useSwal();
const { hasPermission } = usePermission();

const title = 'User Management';
const description = 'Manage users in the application.';

const columns = [
    { label: 'Role', key: 'roles.name', sortable: true },
    { label: 'Name', key: 'users.name', sortable: true },
    { label: 'Email', key: 'users.email', sortable: true },
    { label: 'Phone', key: 'users.phone', sortable: true },
    {
        label: 'Email Verified At',
        key: 'users.email_verified_at',
        sortable: true,
    },
    { label: 'Created At', key: 'users.created_at', sortable: true },
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
        title: 'Managements',
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
                <template #roles.name="{ row }">
                    {{ row.role_name }}
                </template>
                <template #users.name="{ row }">
                    {{ row.name }}
                </template>
                <template #users.email="{ row }">
                    {{ row.email }}
                </template>
                <template #users.phone="{ row }">
                    {{ row.phone ?? '-' }}
                </template>
                <template #users.email_verified_at="{ row }">
                    <span
                        class="rounded-full bg-green-100 px-2 py-1 text-sm font-medium text-green-800"
                        v-if="row.email_verified_at"
                    >
                        Verified
                    </span>
                    <span
                        class="rounded-full bg-red-100 px-2 py-1 text-sm font-medium text-red-800"
                        v-else
                    >
                        Not Verified
                    </span>
                </template>
                <template #users.created_at="{ row }">
                    {{ dayjs(row.created_at).format('DD MMMM YYYY H:m:s') }}
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-center gap-2">
                        <ModalLink
                            :href="edit({ user: row.id }).url"
                            slideover
                            v-if="hasPermission('update' + resource)"
                        >
                            <Button variant="ghost" size="icon">
                                <Pencil class="h-4 w-4" />
                            </Button>
                        </ModalLink>
                        <ModalLink
                            :href="editPassword({ user: row.id }).url"
                            slideover
                            v-if="hasPermission('update' + resource)"
                        >
                            <Button
                                size="sm"
                                class="h-8 bg-yellow-500 px-2 text-black hover:bg-yellow-600"
                            >
                                <KeyRound class="h-4 w-4" />
                                Edit Password
                            </Button>
                        </ModalLink>
                        <Button
                            variant="ghost"
                            size="icon"
                            v-if="hasPermission('delete' + resource)"
                            @click="
                                confirm({
                                    title: 'Delete User?',
                                    text: 'This action cannot be undone.',
                                    icon: 'warning',
                                    confirmButtonText: 'Yes, delete it!',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        router.delete(
                                            destroy({ user: row.id }).url,
                                            {
                                                preserveScroll: true,
                                                preserveState: true,
                                                onSuccess: () => {
                                                    toast.fire({
                                                        icon: 'success',
                                                        title: 'User deleted successfully.',
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
