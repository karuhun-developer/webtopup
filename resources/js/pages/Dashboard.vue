<script setup lang="ts">
import DashboardTableSkeleton from '@/components/dashboard/DashboardTableSkeleton.vue';
import OrdersNotProcessedTable from '@/components/dashboard/OrdersNotProcessedTable.vue';
import OrdersPaymentReviewTable from '@/components/dashboard/OrdersPaymentReviewTable.vue';
import OrdersWaitingTable from '@/components/dashboard/OrdersWaitingTable.vue';
import StatsCards from '@/components/dashboard/StatsCards.vue';
import StatsCardsSkeleton from '@/components/dashboard/StatsCardsSkeleton.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes/cms';
import { OrderDataItem } from '@/types/cms/main';
import { Deferred, Head } from '@inertiajs/vue3';

interface DashboardStats {
    notProcessed: number;
    waiting: number;
    ready: number;
    gifted: number;
    revenue: number;
    paymentReview: number;
}

const props = defineProps<{
    stats: DashboardStats;
    ordersWaiting: OrderDataItem[];
    ordersNotProcessed: OrderDataItem[];
    ordersPaymentReview: OrderDataItem[];
}>();

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4">
            <!-- Stats Cards -->
            <Deferred data="stats">
                <template #fallback>
                    <StatsCardsSkeleton />
                </template>
                <StatsCards :stats="stats" />
            </Deferred>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <!-- Payment Review Table -->
                <Deferred data="ordersPaymentReview">
                    <template #fallback>
                        <DashboardTableSkeleton />
                    </template>
                    <OrdersPaymentReviewTable :orders="ordersPaymentReview" />
                </Deferred>

                <!-- Orders Waiting Table -->
                <Deferred data="ordersWaiting">
                    <template #fallback>
                        <DashboardTableSkeleton />
                    </template>
                    <OrdersWaitingTable :orders="ordersWaiting" />
                </Deferred>

                <!-- Orders Not Processed Table -->
                <Deferred data="ordersNotProcessed">
                    <template #fallback>
                        <DashboardTableSkeleton />
                    </template>
                    <OrdersNotProcessedTable :orders="ordersNotProcessed" />
                </Deferred>
            </div>
        </div>
    </AppLayout>
</template>
