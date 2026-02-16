<script setup lang="ts">
import { show } from '@/actions/App/Http/Controllers/Cms/Order/GiftOrderController';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { OrderDataItem } from '@/types/cms/main';
import { Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';

defineProps<{
    orders: OrderDataItem[];
}>();
</script>

<template>
    <Card class="flex flex-col">
        <CardHeader>
            <CardTitle>In Progress (Waiting 7 Days)</CardTitle>
            <CardDescription>
                Orders where friend request was sent but waiting for 7 day delay.
            </CardDescription>
        </CardHeader>
        <CardContent class="flex-1">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Reference</TableHead>
                        <TableHead>Countdown</TableHead>
                        <TableHead class="text-right">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="order in orders" :key="order.id">
                        <TableCell class="font-medium">
                            <div class="flex flex-col">
                                <span>{{ order.reference }}</span>
                                <span class="text-xs text-muted-foreground">
                                    {{ order.submited?.account_id }}
                                    ({{ order.submited?.server_id }})
                                </span>
                            </div>
                        </TableCell>
                        <TableCell>
                            <div
                                v-if="
                                    order.submited?.user_confirm_friend_timestamp
                                "
                            >
                                <span
                                    v-if="
                                        dayjs(
                                            order.submited
                                                ?.user_confirm_friend_timestamp,
                                        )
                                            .add(7, 'day')
                                            .diff(dayjs()) <= 0
                                    "
                                    class="inline-flex animate-pulse items-center rounded-md bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-400"
                                >
                                    READY
                                </span>
                                <span
                                    v-else
                                    class="inline-flex items-center rounded-md bg-orange-100 px-2.5 py-1 text-xs font-medium text-orange-800 dark:bg-orange-900/30 dark:text-orange-400"
                                >
                                    {{
                                        Math.floor(
                                            dayjs(
                                                order.submited
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
                                                order.submited
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
                        </TableCell>
                        <TableCell class="text-right">
                            <Link :href="show({ order: order.reference }).url">
                                <Button variant="outline" size="sm">
                                    View
                                </Button>
                            </Link>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="orders.length === 0">
                        <TableCell colspan="3" class="h-24 text-center">
                            No waiting orders.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>
