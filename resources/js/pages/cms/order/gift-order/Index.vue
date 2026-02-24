<script setup lang="ts">
import {
    archiveAll,
    create,
    show,
    validatePaymentView,
} from '@/actions/App/Http/Controllers/Cms/Order/GiftOrderController';
import { archive } from '@/actions/App/Http/Controllers/Cms/Order/OrderController';
import Heading from '@/components/Heading.vue';
import OrderStatusFilter from '@/components/order/OrderStatusFilter.vue';
import ResourceTable from '@/components/ResourceTable.vue';
import { Button } from '@/components/ui/button';
import { useFilter } from '@/composables/useFilter';
import { usePermission } from '@/composables/usePermission';
import { useSwal } from '@/composables/useSwal';
import AppLayout from '@/layouts/AppLayout.vue';
import { PaginationItem, type BreadcrumbItem } from '@/types';
import { OrderDataItem } from '@/types/cms/main';
import { Head, Link, router } from '@inertiajs/vue3';
import { ModalLink } from '@inertiaui/modal-vue';
import dayjs from 'dayjs';
import { Archive, CheckCircle, Eye, Plus } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    data: PaginationItem<OrderDataItem>;
    orderBy?: string;
    order?: 'asc' | 'desc';
    search?: string;
    paginate?: number;
    resource: string;
    paymentStatusFilter?: string[];
    giftSendFilter?: string[];
}>();

const { confirm, toast } = useSwal();
const { hasPermission } = usePermission();
const { updateParams } = useFilter();

const title = 'Gift Orders';
const description = 'Manage and monitor all gift orders placed by customers.';

const columns = [
    { label: 'Reference', key: 'reference', sortable: true },
    { label: 'Customer', key: 'name', sortable: true },
    {
        label: 'Payment',
        key: 'payment_status',
        actualKey: 'gift_status',
        sortable: true,
    },
    { label: 'Gift Status', key: 'gift_status', sortable: false },
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
        title: title,
        href: '#',
    },
];

// Filter state
const paymentFilters = ref<string[]>(props.paymentStatusFilter || []);
const giftSendFilters = ref<string[]>(props.giftSendFilter || []);

// Watch for prop changes to update local state
watch(
    () => props.paymentStatusFilter,
    (newVal) => {
        paymentFilters.value = newVal || [];
    },
);

const selectedOrders = ref<(string | number)[]>([]);

const isArchivingAll = ref(false);
const handleArchiveAll = () => {
    confirm({
        title: 'Archive All',
        text: 'Are you sure you want to archive all Gift Orders? This action cannot be undone.',
        icon: 'warning',
        confirmButtonText: 'Yes, archive all!',
    }).then((result) => {
        if (result.isConfirmed) {
            isArchivingAll.value = true;
            router.post(
                archiveAll().url,
                {},
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        selectedOrders.value = [];
                        toast.fire({
                            icon: 'success',
                            title: 'All gift orders archived successfully.',
                        });
                    },
                    onFinish: () => {
                        isArchivingAll.value = false;
                    },
                },
            );
        }
    });
};

const bulkArchive = () => {
    if (selectedOrders.value.length === 0) return;

    confirm({
        title: 'Archive Selected',
        text: `Are you sure you want to archive ${selectedOrders.value.length} selected orders?`,
        icon: 'warning',
        confirmButtonText: 'Yes, archive them!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(
                archive().url,
                { ids: selectedOrders.value },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        selectedOrders.value = [];
                        toast.fire({
                            icon: 'success',
                            title: 'Selected orders archived successfully.',
                        });
                    },
                },
            );
        }
    });
};

watch(
    () => props.giftSendFilter,
    (newVal) => {
        giftSendFilters.value = newVal || [];
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

const handleGiftSendFiltersUpdate = (filters: string[]) => {
    giftSendFilters.value = filters;
    updateParams({
        gift_send: filters.length > 0 ? filters : undefined,
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
                <div class="flex items-center gap-2">
                    <Button
                        v-if="hasPermission('update' + resource)"
                        variant="secondary"
                        @click="handleArchiveAll"
                        :disabled="isArchivingAll"
                    >
                        Archive All
                    </Button>
                    <Button
                        v-if="
                            selectedOrders.length > 0 &&
                            hasPermission('update' + resource)
                        "
                        variant="secondary"
                        @click="bulkArchive"
                    >
                        <Archive class="h-4 w-4" />
                        Archive Selected ({{ selectedOrders.length }})
                    </Button>
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

            <!-- Filter Tabs -->
            <OrderStatusFilter
                :payment-filters="paymentFilters"
                :gift-send-filters="giftSendFilters"
                :show-topup-filter="false"
                :show-gift-send-filter="true"
                @update:payment-filters="handlePaymentFiltersUpdate"
                @update:gift-send-filters="handleGiftSendFiltersUpdate"
            />

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
                <template #gift_status="{ row }">
                    <div class="flex flex-col gap-2">
                        <span
                            class="inline-flex w-fit items-center rounded-md px-2.5 py-1 text-xs font-semibold"
                            :class="{
                                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400':
                                    row.submited.gift_send,
                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400':
                                    !row.submited.gift_send,
                            }"
                        >
                            {{ row.submited.gift_send ? 'Sent' : 'Not Sent' }}
                        </span>

                        <!-- Countdown -->
                        <div
                            v-if="
                                row.submited?.user_confirm_friend_timestamp &&
                                !row.submited.gift_send
                            "
                        >
                            <span
                                v-if="
                                    dayjs(
                                        row.submited
                                            ?.user_confirm_friend_timestamp,
                                    )
                                        .add(7, 'day')
                                        .diff(dayjs()) <= 0
                                "
                                class="inline-flex animate-pulse items-center rounded-md bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/30 dark:text-red-400"
                            >
                                READY TO SEND
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center rounded-md bg-orange-100 px-2.5 py-1.5 font-mono text-xs font-semibold text-orange-800 dark:bg-orange-900/30 dark:text-orange-400"
                            >
                                {{
                                    Math.floor(
                                        dayjs(
                                            row.submited
                                                ?.user_confirm_friend_timestamp,
                                        )
                                            .add(7, 'day')
                                            .diff(dayjs()) /
                                            (1000 * 60 * 60 * 24),
                                    )
                                }}d
                                {{
                                    Math.floor(
                                        (dayjs(
                                            row.submited
                                                ?.user_confirm_friend_timestamp,
                                        )
                                            .add(7, 'day')
                                            .diff(dayjs()) %
                                            (1000 * 60 * 60 * 24)) /
                                            (1000 * 60 * 60),
                                    )
                                }}h
                            </span>
                        </div>
                    </div>
                </template>
                <template #created_at="{ row }">
                    {{ dayjs(row.created_at).format('DD MMMM YYYY HH:mm:ss') }}
                </template>
                <template #expired_at="{ row }">
                    {{
                        dayjs(row.payment?.expired_at).format(
                            'DD MMMM YYYY HH:mm:ss',
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
                            :href="
                                validatePaymentView({ order: row.reference })
                                    .url
                            "
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
                        <Link
                            :href="show({ order: row.reference }).url"
                            v-if="hasPermission('view' + resource)"
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
