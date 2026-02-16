<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
</script>

<template>
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
        <Card v-for="i in 5" :key="i">
            <CardHeader
                class="flex flex-row items-center justify-between space-y-0 pb-2"
            >
                <CardTitle class="text-sm font-medium">
                    <Skeleton class="h-4 w-[100px]" />
                </CardTitle>
                <Skeleton class="h-4 w-4" />
            </CardHeader>
            <CardContent>
                <div class="text-2xl font-bold">
                    <Skeleton class="h-8 w-[60px]" />
                </div>
                <p class="text-xs text-muted-foreground mt-2">
                    <Skeleton class="h-3 w-[120px]" />
                </p>
            </CardContent>
        </Card>
    </div>
</template>
