<script setup lang="ts">
import {
    create,
    show,
} from '@/actions/App/Http/Controllers/Cms/Order/OrderController';
import Heading from '@/components/Heading.vue';
import OrderStatusFilter from '@/components/order/OrderStatusFilter.vue';
import ResourceTable from '@/components/ResourceTable.vue';
import { Button } from '@/components/ui/button';
import { useFilter } from '@/composables/useFilter';
import { usePermission } from '@/composables/usePermission';
import AppLayout from '@/layouts/AppLayout.vue';
import { PaginationItem, type BreadcrumbItem } from '@/types';
import { OrderDataItem } from '@/types/cms/main';
import { Head } from '@inertiajs/vue3';
import { ModalLink } from '@inertiaui/modal-vue';
import dayjs from 'dayjs';
import { CheckCircle, Eye, Plus } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    data: PaginationItem<OrderDataItem>;
    orderBy?: string;
    order?: 'asc' | 'desc';
    search?: string;
    paginate?: number;
    resource: string;
    paymentStatusFilter?: string[];
    topupStatusFilter?: string[];
}>();

const { hasPermission } = usePermission();
const { updateParams } = useFilter();

const title = 'PPOB Categories';
const description =
    'Manage categories for PPOB services including creation, editing, and deletion.';

const columns = [
    { label: 'Reference', key: 'reference', sortable: true },
    { label: 'Customer', key: 'name', sortable: true },
    { label: 'Payment', key: 'payment_status', sortable: true },
    { label: 'Topup', key: 'topup_status', sortable: true },
    { label: 'Created At', key: 'created_at', sortable: true },
    { label: 'Expired At', key: 'expired_at', sortable: false },
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

// Filter state
const paymentFilters = ref<string[]>(props.paymentStatusFilter || []);
const topupFilters = ref<string[]>((props.topupStatusFilter || []).map(String));

// Watch for prop changes to update local state
watch(
    () => props.paymentStatusFilter,
    (newVal) => {
        paymentFilters.value = newVal || [];
    },
);

watch(
    () => props.topupStatusFilter,
    (newVal) => {
        topupFilters.value = (newVal || []).map(String);
    },
);

// Handle filter updates from component
const handlePaymentFiltersUpdate = (filters: string[]) => {
    paymentFilters.value = filters;
    updateParams({
        payment_status: filters.length > 0 ? filters : undefined,
        page: 1,
    });
};

const handleTopupFiltersUpdate = (filters: string[]) => {
    topupFilters.value = filters;
    updateParams({
        topup_status: filters.length > 0 ? filters : undefined,
        page: 1,
    });
};
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

            <!-- Filter Tabs -->
            <OrderStatusFilter
                :payment-filters="paymentFilters"
                :topup-filters="topupFilters"
                @update:payment-filters="handlePaymentFiltersUpdate"
                @update:topup-filters="handleTopupFiltersUpdate"
            />

            <ResourceTable
                :data="data"
                :columns="columns"
                :order-by="orderBy"
                :order="order"
                :search="search"
                :paginate="paginate"
            >
                <template #name="{ row }">
                    <div class="flex flex-col">
                        <span class="font-medium">{{ row.name }}</span>
                        <span class="text-sm text-muted-foreground">
                            {{ row.phone }} | {{ row.email || '-' }}
                        </span>
                    </div>
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
                </template>
                <template #created_at="{ row }">
                    {{ dayjs(row.created_at).format('DD MMMM YYYY H:m:s') }}
                </template>
                <template #expired_at="{ row }">
                    {{
                        dayjs(row.payment?.expired_at).format(
                            'DD MMMM YYYY H:m:s',
                        )
                    }}
                </template>
                <template #actions="{ row }">
                    <div class="flex items-center justify-center gap-2">
                        <!-- Validate Payment Button (Manual Transfer + Pending + Has Image) -->
                        <ModalLink
                            v-if="
                                row.payment_status === 0 &&
                                hasPermission('update' + resource)
                            "
                            :href="show({ order: row.reference }).url"
                            slideover
                        >
                            <Button
                                variant="default"
                                size="icon"
                                title="Validate Payment"
                            >
                                <CheckCircle class="h-4 w-4" />
                            </Button>
                        </ModalLink>

                        <!-- View Order Details -->
                        <ModalLink
                            :href="show({ order: row.reference }).url"
                            slideover
                            v-if="hasPermission('view' + resource)"
                        >
                            <Button
                                variant="ghost"
                                size="icon"
                                title="View Details"
                            >
                                <Eye class="h-4 w-4" />
                            </Button>
                        </ModalLink>
                    </div>
                </template>
            </ResourceTable>
        </div>
    </AppLayout>
</template>
