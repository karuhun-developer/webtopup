<script setup lang="ts">
import { show } from '@/actions/App/Http/Controllers/Main/TransactionController';
import ResourceTable from '@/components/ResourceTable.vue';
import StatusBadge from '@/components/transaction/StatusBadge.vue';
import { formatCurrency } from '@/lib/utils';
import { PaginationItem } from '@/types';
import { OrderDataItem } from '@/types/cms/main';
import { Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { ref } from 'vue';

defineProps<{
    transactions: PaginationItem<OrderDataItem>;
}>();

const search = ref('');
const orderBy = ref('created_at');
const order = ref<'asc' | 'desc'>('desc');
const paginate = ref(10);

const columns = [
    { label: 'Order ID', key: 'reference', sortable: true },
    { label: 'Date', key: 'created_at', sortable: true },
    { label: 'Product', key: 'product', sortable: false },
    { label: 'Amount', key: 'total_amount', sortable: true },
    { label: 'Status', key: 'payment_status', sortable: false },
];
</script>

<template>
    <div class="rounded-lg border border-border/50 bg-card p-6 shadow-sm">
        <h2 class="mb-6 text-lg font-bold text-foreground">
            Transaction History
        </h2>

        <ResourceTable
            :data="transactions"
            :columns="columns"
            v-model:search="search"
            v-model:order-by="orderBy"
            v-model:order="order"
            v-model:paginate="paginate"
        >
            <template #reference="{ row }">
                <Link
                    :href="
                        show({
                            order: row.reference,
                        }).url
                    "
                    class="font-medium text-primary hover:underline"
                >
                    {{ row.reference }}
                </Link>
            </template>
            <template #created_at="{ row }">
                <div class="text-sm text-muted-foreground">
                    {{
                        dayjs(row.created_at)
                            .locale('id')
                            .format('DD MMM YYYY, HH:mm')
                    }}
                </div>
            </template>

            <template #product="{ row }">
                <div class="flex items-center gap-3">
                    <div>
                        <div class="font-medium text-foreground">
                            {{ row.product?.name }}
                        </div>
                        <div class="text-xs text-muted-foreground">
                            {{ row.brand?.name }}
                        </div>
                    </div>
                </div>
            </template>

            <template #total_amount="{ row }">
                <div class="text-right font-bold text-primary">
                    {{ formatCurrency(row.total_amount) }}
                </div>
            </template>

            <template #payment_status="{ row }">
                <StatusBadge :status="row.payment_status" type="payment" />
            </template>
        </ResourceTable>
    </div>
</template>
