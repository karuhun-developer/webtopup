<script setup lang="ts">
import { show as showGift } from '@/actions/App/Http/Controllers/Cms/Order/GiftOrderController';
import { show as showTopupManual } from '@/actions/App/Http/Controllers/Cms/Order/ManualTopupOrderController';
import { Badge } from '@/components/ui/badge';
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

defineProps<{
    orders: OrderDataItem[];
}>();
</script>

<template>
    <Card class="flex flex-col">
        <CardHeader>
            <CardTitle>Not Processed</CardTitle>
            <CardDescription>
                New orders waiting tobe processed.
            </CardDescription>
        </CardHeader>
        <CardContent class="flex-1">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Reference</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead class="text-right">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="order in orders" :key="order.id">
                        <TableCell class="font-medium">
                            <div class="flex flex-col">
                                <span>{{ order.reference }}</span>
                                <span class="text-xs text-muted-foreground">
                                    {{ order.brand?.name }} -
                                    {{ order.product?.name }}
                                </span>
                            </div>
                        </TableCell>
                        <TableCell>
                            <Badge
                                :variant="
                                    order.payment_status === 2
                                        ? 'default'
                                        : 'secondary'
                                "
                                class="whitespace-nowrap"
                            >
                                {{
                                    order.payment_status === 2
                                        ? 'Paid'
                                        : order.payment_status === 0
                                          ? 'Pending'
                                          : 'Failed'
                                }}
                            </Badge>
                        </TableCell>
                        <TableCell class="text-right">
                            <Link
                                :href="
                                    (order.product?.provider === 'gift'
                                        ? showGift(order.reference)
                                        : showTopupManual(order.reference)
                                    ).url
                                "
                            >
                                <Button variant="outline" size="sm">
                                    Process
                                </Button>
                            </Link>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="orders.length === 0">
                        <TableCell colspan="3" class="h-24 text-center">
                            No pending orders.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>
