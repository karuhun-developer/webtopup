<script setup lang="ts">
import { Card } from '@/components/ui/card';
import { OrderDataItem } from '@/types/cms/main';
import dayjs from 'dayjs';

defineProps<{
    order: OrderDataItem;
}>();
</script>

<template>
    <Card class="sticky top-6 p-6">
        <h2 class="mb-4 text-lg font-semibold">Order Notifications</h2>

        <div
            v-if="order.notifications && order.notifications.length > 0"
            class="space-y-3"
        >
            <div
                v-for="notification in order.notifications"
                :key="notification.id"
                class="rounded-lg border p-4"
                :class="
                    notification.error
                        ? 'border-red-500/50 bg-red-500/10'
                        : 'border-green-500/50 bg-green-500/10'
                "
            >
                <div class="flex items-start justify-between">
                    <span
                        class="inline-block rounded px-2 py-1 text-xs font-semibold uppercase"
                        :class="
                            notification.error
                                ? 'bg-red-500/20 text-red-500'
                                : 'bg-green-500/20 text-green-500'
                        "
                    >
                        {{ notification.provider }}
                    </span>
                    <p class="text-xs text-muted-foreground">
                        {{ dayjs(notification.created_at).format('DD MMM YYYY, HH:mm') }}
                    </p>
                </div>
                <h4 class="mt-2 font-semibold">
                    {{ notification.title }}
                </h4>
                <p class="mt-1 whitespace-pre-wrap text-sm text-muted-foreground">
                    {{ notification.content }}
                </p>
                <p
                    v-if="notification.error"
                    class="mt-2 text-sm text-red-600"
                >
                    Error: {{ notification.error }}
                </p>
            </div>
        </div>

        <div
            v-else
            class="rounded-lg border border-dashed p-8 text-center"
        >
            <p class="text-sm text-muted-foreground">No notifications yet</p>
        </div>
    </Card>
</template>
