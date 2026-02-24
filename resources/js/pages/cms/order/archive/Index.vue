<script setup lang="ts">
import {
    unarchive,
    unarchiveAll,
} from '@/actions/App/Http/Controllers/Cms/Order/ArchiveOrderController';
import { show as showGiftOrder } from '@/actions/App/Http/Controllers/Cms/Order/GiftOrderController';
import { show as showManualTopupOrder } from '@/actions/App/Http/Controllers/Cms/Order/ManualTopupOrderController';
import { show as showOrder } from '@/actions/App/Http/Controllers/Cms/Order/OrderController';
import Heading from '@/components/Heading.vue';
import ResourceTable from '@/components/ResourceTable.vue';
import { Button } from '@/components/ui/button';
import { usePermission } from '@/composables/usePermission';
import { useSwal } from '@/composables/useSwal';
import AppLayout from '@/layouts/AppLayout.vue';
import { PaginationItem, type BreadcrumbItem } from '@/types';
import { OrderDataItem } from '@/types/cms/main';
import { Head, Link, router } from '@inertiajs/vue3';
import { ModalLink } from '@inertiaui/modal-vue';
import dayjs from 'dayjs';
import { ArchiveRestore, Eye } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    data: PaginationItem<OrderDataItem>;
    orderBy?: string;
    order?: 'asc' | 'desc';
    search?: string;
    paginate?: number;
    resource: string;
}>();

const { confirm, toast } = useSwal();
const { hasPermission } = usePermission();

const title = 'Archived Orders';
const description = 'Manage and monitor all archived orders.';

const columns = [
    { label: 'Reference', key: 'reference', sortable: true },
    { label: 'Customer', key: 'name', sortable: true },
    { label: 'Provider', key: 'provider', sortable: false },
    { label: 'Payment', key: 'payment_status', sortable: true },
    { label: 'Created At', key: 'created_at', sortable: true },
    { label: 'Archived At', key: 'archive_at', sortable: true },
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
        title: title,
        href: '#',
    },
];

const selectedOrders = ref<(string | number)[]>([]);

const bulkUnarchive = () => {
    if (selectedOrders.value.length === 0) return;

    confirm({
        title: 'Unarchive Selected',
        text: `Are you sure you want to unarchive ${selectedOrders.value.length} selected orders?`,
        icon: 'warning',
        confirmButtonText: 'Yes, unarchive them!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(
                unarchive().url,
                { ids: selectedOrders.value },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        selectedOrders.value = [];
                        toast.fire({
                            icon: 'success',
                            title: 'Selected orders unarchived successfully.',
                        });
                    },
                },
            );
        }
    });
};
const isUnarchivingAll = ref(false);
const handleUnarchiveAll = () => {
    confirm({
        title: 'Unarchive All',
        text: 'Are you sure you want to unarchive ALL orders?',
        icon: 'warning',
        confirmButtonText: 'Yes, unarchive all!',
    }).then((result) => {
        if (result.isConfirmed) {
            isUnarchivingAll.value = true;
            router.post(
                unarchiveAll().url,
                {},
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        selectedOrders.value = [];
                        toast.fire({
                            icon: 'success',
                            title: 'All orders unarchived successfully.',
                        });
                    },
                    onFinish: () => {
                        isUnarchivingAll.value = false;
                    },
                },
            );
        }
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="title" />
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <Heading :title="title" :description="description" />
                <div class="flex items-center gap-2">
                    <Button
                        v-if="hasPermission('update' + resource)"
                        variant="secondary"
                        @click="handleUnarchiveAll"
                        :disabled="isUnarchivingAll"
                    >
                        Unarchive All
                    </Button>
                    <Button
                        v-if="
                            selectedOrders.length > 0 &&
                            hasPermission('update' + resource)
                        "
                        variant="secondary"
                        @click="bulkUnarchive"
                    >
                        <ArchiveRestore class="h-4 w-4" />
                        Unarchive Selected ({{ selectedOrders.length }})
                    </Button>
                </div>
            </div>

            <ResourceTable
                :data="data"
                :columns="columns"
                :order-by="orderBy"
                :order="order"
                :search="search"
                :paginate="paginate"
                selectable
                key-field="id"
                :selected="selectedOrders"
                @update:selected="selectedOrders = $event"
            >
                <template #name="{ row }">
                    <div class="flex flex-col">
                        <span class="font-medium">{{ row.name }}</span>
                        <span class="text-sm text-muted-foreground">
                            {{ row.phone }} | {{ row.email || '-' }}
                        </span>
                    </div>
                </template>
                <template #provider="{ row }">
                    <span
                        class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold"
                        :class="{
                            'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400':
                                row.product?.provider === 'digiflazz',
                            'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400':
                                row.product?.provider === 'gift',
                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400':
                                row.product?.provider === 'manual_topup',
                            'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400':
                                !row.product?.provider,
                        }"
                    >
                        {{ row.product?.provider || 'N/A' }}
                    </span>
                </template>
                <template #payment_status="{ row }">
                    <div class="flex flex-col gap-2">
                        <!-- Payment Driver Badge -->
                        <span
                            class="inline-flex w-fit items-center rounded-md px-2.5 py-1 text-xs font-semibold tracking-wide uppercase"
                            :class="{
                                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400':
                                    row.payment?.driver === 'midtrans',
                                'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400':
                                    row.payment?.driver === 'manual',
                                'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400':
                                    !row.payment?.driver,
                            }"
                        >
                            {{ row.payment?.driver || 'N/A' }}
                        </span>

                        <!-- Payment Status Badge -->
                        <span
                            class="inline-flex w-fit items-center rounded-md px-2.5 py-1 text-xs font-semibold"
                            :class="{
                                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400':
                                    row.payment_status === 2 ||
                                    row.payment_status === 1,
                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400':
                                    row.payment_status === 0,
                                'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400':
                                    row.payment_status < 0,
                            }"
                        >
                            {{
                                row.payment_status === 2 ||
                                row.payment_status === 1
                                    ? 'Successful'
                                    : row.payment_status === 0
                                      ? 'Pending'
                                      : 'Failed'
                            }}
                        </span>
                    </div>
                </template>
                <template #topup_status="{ row }">
                    <span
                        v-if="row.brand?.provider !== 'gift'"
                        class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold"
                        :class="{
                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400':
                                row.topup_status === 2,
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400':
                                row.topup_status === 0,
                            'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400':
                                row.topup_status === 1,
                            'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400':
                                row.topup_status === 3,
                        }"
                    >
                        {{
                            row.topup_status === 2
                                ? 'Success'
                                : row.topup_status === 0
                                  ? 'Pending'
                                  : row.topup_status === 1
                                    ? 'On Progress'
                                    : 'Failed'
                        }}
                    </span>
                    <span
                        v-else
                        class="inline-flex w-fit items-center rounded-md px-2.5 py-1 text-xs font-semibold"
                        :class="{
                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400':
                                row.submited?.gift_send,
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400':
                                !row.submited?.gift_send,
                        }"
                    >
                        {{ row.submited?.gift_send ? 'Sent' : 'Not Sent' }}
                    </span>
                </template>
                <template #created_at="{ row }">
                    {{ dayjs(row.created_at).format('DD MMMM YYYY HH:mm:ss') }}
                </template>
                <template #archive_at="{ row }">
                    {{ dayjs(row.archive_at).format('DD MMMM YYYY HH:mm:ss') }}
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-center gap-2">
                        <!-- View Order Details (Digiflazz) -->
                        <ModalLink
                            :href="showOrder({ order: row.reference }).url"
                            slideover
                            v-if="
                                row.product?.provider === 'digiflazz' &&
                                hasPermission('view' + resource)
                            "
                        >
                            <Button
                                variant="ghost"
                                size="icon"
                                title="View Details"
                            >
                                <Eye class="h-4 w-4" />
                            </Button>
                        </ModalLink>

                        <!-- View Order Details (Gift) -->
                        <Link
                            :href="showGiftOrder({ order: row.reference }).url"
                            v-else-if="
                                row.product?.provider === 'gift' &&
                                hasPermission('view' + resource)
                            "
                        >
                            <Button
                                variant="ghost"
                                size="icon"
                                title="View Details"
                            >
                                <Eye class="h-4 w-4" />
                            </Button>
                        </Link>

                        <!-- View Order Details (Manual Topup) -->
                        <Link
                            :href="
                                showManualTopupOrder({ order: row.reference })
                                    .url
                            "
                            v-else-if="
                                row.product?.provider === 'manual_topup' &&
                                hasPermission('view' + resource)
                            "
                        >
                            <Button
                                variant="ghost"
                                size="icon"
                                title="View Details"
                            >
                                <Eye class="h-4 w-4" />
                            </Button>
                        </Link>
                    </div>
                </template>
            </ResourceTable>
        </div>
    </AppLayout>
</template>
