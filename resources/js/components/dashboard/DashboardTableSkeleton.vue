<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Skeleton } from '@/components/ui/skeleton';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
</script>

<template>
    <Card class="flex flex-col">
        <CardHeader>
            <CardTitle><Skeleton class="h-6 w-[200px]" /></CardTitle>
            <div class="text-sm text-muted-foreground">
                <Skeleton class="h-4 w-[300px]" />
            </div>
        </CardHeader>
        <CardContent class="flex-1">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead><Skeleton class="h-4 w-[80px]" /></TableHead>
                        <TableHead><Skeleton class="h-4 w-[100px]" /></TableHead>
                        <TableHead class="text-right"
                            ><Skeleton class="ml-auto h-4 w-[60px]"
                        /></TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="i in 5" :key="i">
                        <TableCell class="font-medium">
                            <div class="flex flex-col gap-2">
                                <Skeleton class="h-4 w-[120px]" />
                                <Skeleton class="h-3 w-[150px]" />
                            </div>
                        </TableCell>
                        <TableCell>
                            <Skeleton class="h-6 w-[80px]" />
                        </TableCell>
                        <TableCell class="text-right">
                            <Skeleton class="ml-auto h-9 w-[60px]" />
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </CardContent>
    </Card>
</template>
