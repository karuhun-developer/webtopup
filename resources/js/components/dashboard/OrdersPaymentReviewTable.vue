<script setup lang="ts">
import { validatePaymentView } from '@/actions/App/Http/Controllers/Cms/Order/GiftOrderController';
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
import { ModalLink } from '@inertiaui/modal-vue';
import { CheckCircle } from 'lucide-vue-next';

defineProps<{
    orders: OrderDataItem[];
}>();

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount);
};
</script>

<template>
    <Card class="flex flex-col">
        <CardHeader>
            <CardTitle>Payment Review</CardTitle>
            <CardDescription>
                Manual transfer orders waiting for validation.
            </CardDescription>
        </CardHeader>
        <CardContent class="flex-1">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Reference</TableHead>
                        <TableHead>Amount</TableHead>
                        <TableHead class="text-right">Action</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="order in orders" :key="order.id">
                        <TableCell class="font-medium">
                            <div class="flex flex-col">
                                <span>{{ order.reference }}</span>
                                <span class="text-xs text-muted-foreground">
                                    {{ order.user?.name }}
                                </span>
                            </div>
                        </TableCell>
                        <TableCell>
                            <div class="flex flex-col">
                                <span class="font-medium">
                                    {{ formatCurrency(order.payment?.amount || 0) }}
                                </span>
                                <Badge variant="secondary" class="w-fit text-[10px] px-1 py-0 h-4">
                                    {{ order.brand?.name }}
                                </Badge>
                            </div>
                        </TableCell>
                        <TableCell class="text-right">
                            <ModalLink
                                :href="
                                    validatePaymentView({ order: order.reference })
                                        .url
                                "
                                slideover
                            >
                                <Button
                                    variant="default"
                                    size="sm"
                                    title="Validate Payment"
                                >
                                    <CheckCircle class="h-3 w-3 mr-1" />
                                    Validate
                                </Button>
                            </ModalLink>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="orders.length === 0">
                        <TableCell colspan="3" class="h-24 text-center">
                            No orders pending review.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>
