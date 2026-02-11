<script setup lang="ts">
import { index } from '@/actions/App/Http/Controllers/Cms/Order/GiftOrderController';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { OrderDataItem } from '@/types/cms/main';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import DeliveryProgressCard from './components/DeliveryProgressCard.vue';
import OrderInformationCard from './components/OrderInformationCard.vue';
import OrderNotificationsCard from './components/OrderNotificationsCard.vue';

dayjs.extend(relativeTime);

const props = defineProps<{
    order: OrderDataItem;
    mlAccountNickname?: string;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Gift Orders', href: index().url },
    { title: props.order.reference, href: '#' },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" title="Gift Order Detail">
        <div class="container mx-auto py-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Left Column: Order Information -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Header -->
                    <div>
                        <h1 class="text-2xl font-bold">
                            Order {{ order.reference }}
                        </h1>
                        <p class="text-sm text-muted-foreground">
                            Created on
                            {{
                                dayjs(order.created_at).format(
                                    'DD MMMM YYYY, HH:mm',
                                )
                            }}
                        </p>
                    </div>

                    <!-- Order Information Card -->
                    <OrderInformationCard
                        :order="order"
                        :ml-account-nickname="mlAccountNickname"
                    />

                    <!-- Delivery Progress Card -->
                    <DeliveryProgressCard :order="order" />
                </div>

                <!-- Right Column: Order Notifications -->
                <div class="space-y-6 lg:col-span-1">
                    <OrderNotificationsCard :order="order" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
