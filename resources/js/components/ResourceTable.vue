<script setup lang="ts" generic="T">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useFilter } from '@/composables/useFilter';
import { PaginationItem } from '@/types';
import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import {
    ChevronDown,
    ChevronUp,
    ChevronsUpDown,
    Search,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Column {
    label: string;
    key: string;
    sortable?: boolean;
    class?: string;
}

const props = withDefaults(
    defineProps<{
        data: PaginationItem<T>;
        columns: Column[];
        orderBy?: string;
        order?: 'asc' | 'desc';
        search?: string;
        paginate?: number;
    }>(),
    {
        orderBy: 'id',
        order: 'desc',
        search: '',
        paginate: 10,
    },
);

const { updateParams } = useFilter();

const emit = defineEmits<{
    'update:search': [value: string];
    'update:order': [value: 'asc' | 'desc'];
    'update:orderBy': [value: string];
    'update:paginate': [value: number];
}>();

const localSearch = ref(props.search);

const handleSearch = useDebounceFn((value: string) => {
    emit('update:search', value);
    updateParams({ search: value, page: 1 }); // Reset to page 1 on search
}, 300);

watch(
    () => props.search,
    (val) => {
        localSearch.value = val;
    },
);

const handleSort = (key: string) => {
    let newOrder: 'asc' | 'desc' = 'asc';
    if (props.orderBy === key) {
        newOrder = props.order === 'asc' ? 'desc' : 'asc';
    }
    emit('update:orderBy', key);
    emit('update:order', newOrder);
    updateParams({ orderBy: key, order: newOrder });
};

const handlePaginate = (value: number) => {
    emit('update:paginate', value);
    updateParams({ paginate: value, page: 1 });
};

// Helper for pagination links
const onPageClick = (url: string | null) => {
    if (url) {
        router.get(url, {}, { preserveState: true, preserveScroll: true });
    }
};
</script>

<template>
    <div class="space-y-4">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4">
            <div class="relative max-w-sm flex-1">
                <Search
                    class="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground"
                />
                <Input
                    v-model="localSearch"
                    placeholder="Search..."
                    class="pl-9"
                    @input="
                        handleSearch(($event.target as HTMLInputElement).value)
                    "
                />
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-muted-foreground">Rows per page</span>
                <Select
                    :model-value="String(paginate)"
                    @update:model-value="(v) => handlePaginate(Number(v))"
                >
                    <SelectTrigger class="h-9 w-16">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="10">10</SelectItem>
                        <SelectItem value="25">25</SelectItem>
                        <SelectItem value="50">50</SelectItem>
                        <SelectItem value="100">100</SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead
                            v-for="col in columns"
                            :key="col.key"
                            :class="[
                                col.class,
                                {
                                    'cursor-pointer hover:text-foreground':
                                        col.sortable,
                                },
                            ]"
                            @click="col.sortable && handleSort(col.key)"
                        >
                            <div class="flex items-center gap-1">
                                {{ col.label }}
                                <span v-if="col.sortable" class="ml-1">
                                    <ChevronsUpDown
                                        v-if="orderBy !== col.key"
                                        class="h-3 w-3"
                                    />
                                    <ChevronUp
                                        v-else-if="order === 'asc'"
                                        class="h-3 w-3"
                                    />
                                    <ChevronDown v-else class="h-3 w-3" />
                                </span>
                            </div>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="data.data.length > 0">
                        <TableRow
                            v-for="row in data.data"
                            :key="(row as any).id"
                        >
                            <TableCell
                                v-for="col in columns"
                                :key="col.key"
                                :class="col.class"
                            >
                                <slot :name="col.key" :row="row">
                                    {{ (row as any)[col.key] }}
                                </slot>
                            </TableCell>
                        </TableRow>
                    </template>
                    <TableRow v-else>
                        <TableCell
                            :colspan="columns.length"
                            class="h-24 text-center"
                        >
                            No results found.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between space-x-2 py-4">
            <div class="text-sm text-muted-foreground">
                Showing {{ data.from ?? 0 }} to {{ data.to ?? 0 }} of
                {{ data.total }} results
            </div>
            <div class="flex items-center space-x-2">
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!data.prev_page_url"
                    @click="onPageClick(data.prev_page_url)"
                >
                    Previous
                </Button>
                <div class="hidden gap-1 md:flex">
                    <template
                        v-for="(link, i) in data.links.slice(1, -1)"
                        :key="i"
                    >
                        <Button
                            v-if="link.url || link.label === '...'"
                            :variant="link.active ? 'default' : 'outline'"
                            size="sm"
                            :disabled="!link.url"
                            @click="onPageClick(link.url)"
                        >
                            <span v-html="link.label"></span>
                        </Button>
                    </template>
                </div>
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!data.next_page_url"
                    @click="onPageClick(data.next_page_url)"
                >
                    Next
                </Button>
            </div>
        </div>
    </div>
</template>
