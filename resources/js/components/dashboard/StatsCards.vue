<script setup lang="ts">
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { AlertCircle, CheckCircle, Gift, Timer } from 'lucide-vue-next';
import { formatCurrency } from '@/lib/utils';

interface DashboardStats {
    notProcessed: number;
    waiting: number;
    ready: number;
    gifted: number;
    revenue: number;
}

const props = defineProps<{
    stats: DashboardStats;
}>();

</script>

<template>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
        <Card>
            <CardHeader
                class="flex flex-row items-center justify-between space-y-0 pb-2"
            >
                <CardTitle class="text-sm font-medium">
                    Total Revenue
                </CardTitle>
                <span class="text-muted-foreground font-bold">Rp</span>
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">
                    {{ formatCurrency(stats.revenue) }}
                </div>
                <p class="text-xs text-muted-foreground">
                    Total from paid orders
                </p>
            </CardContent>
        </Card>
        <Card>
            <CardHeader
                class="flex flex-row items-center justify-between space-y-0 pb-2"
            >
                <CardTitle class="text-sm font-medium">
                    Not Processed
                </CardTitle>
                <AlertCircle class="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">
                    {{ stats.notProcessed }}
                </div>
                <p class="text-xs text-muted-foreground">
                    Orders waiting for friend request
                </p>
            </CardContent>
        </Card>
        <Card>
            <CardHeader
                class="flex flex-row items-center justify-between space-y-0 pb-2"
            >
                <CardTitle class="text-sm font-medium">
                    Waiting Limit
                </CardTitle>
                <Timer class="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">
                    {{ stats.waiting }}
                </div>
                <p class="text-xs text-muted-foreground">
                    In 7-day countdown period
                </p>
            </CardContent>
        </Card>
        <Card>
            <CardHeader
                class="flex flex-row items-center justify-between space-y-0 pb-2"
            >
                <CardTitle class="text-sm font-medium">
                    Ready to Gift
                </CardTitle>
                <Gift class="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold text-green-600">
                    {{ stats.ready }}
                </div>
                <p class="text-xs text-muted-foreground">
                    Passed 7 days, ready to send
                </p>
            </CardContent>
        </Card>
        <Card>
            <CardHeader
                class="flex flex-row items-center justify-between space-y-0 pb-2"
            >
                <CardTitle class="text-sm font-medium">
                    Gifted
                </CardTitle>
                <CheckCircle class="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">{{ stats.gifted }}</div>
                <p class="text-xs text-muted-foreground">
                    Successfully completed orders
                </p>
            </CardContent>
        </Card>
    </div>
</template>
