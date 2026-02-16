<script setup lang="ts">
import { Card } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { formatCurrency } from '@/lib/utils';
import { OrderDataItem } from '@/types/cms/main';

defineProps<{
    order: OrderDataItem;
    mlAccountNickname?: string;
}>();
</script>

<template>
    <Card class="p-6">
        <h2 class="mb-4 text-lg font-semibold">Order Information</h2>

        <div class="mb-4 flex items-start gap-4">
            <img
                v-if="order?.product?.image"
                :src="order.product.image"
                :alt="order.product.name"
                class="h-24 w-24 rounded-lg object-cover"
            />
            <div class="flex-1">
                <p class="text-xs text-muted-foreground uppercase">
                    {{ order?.brand?.name }}
                </p>
                <h3 class="text-xl font-bold">
                    {{ order?.product?.name }}
                </h3>
                <p class="mt-2 text-2xl font-bold text-primary">
                    {{ formatCurrency(order.total_amount) }}
                </p>
            </div>
        </div>

        <Separator class="my-4" />

        <div class="grid grid-cols-2 gap-4">
            <div>
                <Label class="text-muted-foreground">Account ID / Server</Label>
                <p class="font-mono font-medium">
                    {{ order.submited?.account_id || '-' }}
                    {{
                        order.submited?.server_id
                            ? `/ ${order.submited.server_id}`
                            : ''
                    }}
                </p>
                <p
                    v-if="mlAccountNickname"
                    class="mt-1 text-sm font-semibold text-primary"
                >
                    {{ mlAccountNickname }}
                </p>
            </div>
            <div>
                <Label class="text-muted-foreground">Customer</Label>
                <p class="font-medium">{{ order.name }}</p>
                <p class="text-sm text-muted-foreground">
                    {{ order.phone }}
                </p>
            </div>
        </div>
    </Card>
</template>
