<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ref, watch } from 'vue';

interface FilterOption {
    label: string;
    value: string | number;
}

const props = withDefaults(
    defineProps<{
        paymentFilters?: string[];
        topupFilters?: string[];
        giftSendFilters?: string[];
        showTopupFilter?: boolean;
        showPaymentFilter?: boolean;
        showGiftSendFilter?: boolean;
    }>(),
    {
        showTopupFilter: true,
        showPaymentFilter: true,
        showGiftSendFilter: false,
    },
);

const emit = defineEmits<{
    'update:paymentFilters': [value: string[]];
    'update:topupFilters': [value: string[]];
    'update:giftSendFilters': [value: string[]];
}>();

const localPaymentFilters = ref<string[]>(props.paymentFilters || []);
const localTopupFilters = ref<string[]>((props.topupFilters || []).map(String));
const localGiftSendFilters = ref<string[]>((props.giftSendFilters || []).map(String));

// Watch for prop changes
watch(
    () => props.paymentFilters,
    (newVal) => {
        localPaymentFilters.value = newVal || [];
    },
);

watch(
    () => props.topupFilters,
    (newVal) => {
        localTopupFilters.value = (newVal || []).map(String);
    },
);

watch(
    () => props.giftSendFilters,
    (newVal) => {
        localGiftSendFilters.value = (newVal || []).map(String);
    },
);

const paymentStatusOptions: FilterOption[] = [
    { label: 'Pending', value: 'pending' },
    { label: 'Settlement', value: 'settlement' },
    { label: 'Failed', value: 'failed' },
];

const topupStatusOptions: FilterOption[] = [
    { label: 'Pending', value: 0 },
    { label: 'On Progress', value: 1 },
    { label: 'Success', value: 2 },
    { label: 'Failed', value: 3 },
];

const giftSendStatusOptions: FilterOption[] = [
    { label: 'Belum Selesai', value: 0 },
    { label: 'Selesai', value: 1 },
];

const togglePaymentFilter = (value: string) => {
    const index = localPaymentFilters.value.indexOf(value);
    if (index > -1) {
        localPaymentFilters.value.splice(index, 1);
    } else {
        localPaymentFilters.value.push(value);
    }
    emit('update:paymentFilters', localPaymentFilters.value);
};

const toggleTopupFilter = (value: number) => {
    const valueStr = String(value);
    const index = localTopupFilters.value.indexOf(valueStr);
    if (index > -1) {
        localTopupFilters.value.splice(index, 1);
    } else {
        localTopupFilters.value.push(valueStr);
    }
    emit('update:topupFilters', localTopupFilters.value);
};

const toggleGiftSendFilter = (value: number) => {
    const valueStr = String(value);
    const index = localGiftSendFilters.value.indexOf(valueStr);
    if (index > -1) {
        localGiftSendFilters.value.splice(index, 1);
    } else {
        localGiftSendFilters.value.push(valueStr);
    }
    emit('update:giftSendFilters', localGiftSendFilters.value);
};
</script>

<template>
    <div class="space-y-3">
        <!-- Payment Status Filters -->
        <div class="flex flex-col gap-2" v-if="showPaymentFilter">
            <span class="text-sm font-medium text-foreground"
                >Payment Status</span
            >
            <div class="flex flex-wrap gap-2">
                <Button
                    v-for="option in paymentStatusOptions"
                    :key="option.value"
                    variant="outline"
                    size="sm"
                    :class="{
                        'bg-primary text-primary-foreground hover:bg-primary/90':
                            localPaymentFilters.includes(String(option.value)),
                    }"
                    @click="togglePaymentFilter(String(option.value))"
                >
                    {{ option.label }}
                </Button>
            </div>
        </div>

        <!-- Topup Status Filters -->
        <div class="flex flex-col gap-2" v-if="showTopupFilter">
            <span class="text-sm font-medium text-foreground"
                >Topup Status</span
            >
            <div class="flex flex-wrap gap-2">
                <Button
                    v-for="option in topupStatusOptions"
                    :key="option.value"
                    variant="outline"
                    size="sm"
                    :class="{
                        'bg-primary text-primary-foreground hover:bg-primary/90':
                            localTopupFilters.includes(String(option.value)),
                    }"
                    @click="toggleTopupFilter(Number(option.value))"
                >
                    {{ option.label }}
                </Button>
            </div>
        </div>

        <!-- Gift Send Status Filters -->
        <div class="flex flex-col gap-2" v-if="showGiftSendFilter">
            <span class="text-sm font-medium text-foreground"
                >Gift Send Status</span
            >
            <div class="flex flex-wrap gap-2">
                <Button
                    v-for="option in giftSendStatusOptions"
                    :key="option.value"
                    variant="outline"
                    size="sm"
                    :class="{
                        'bg-primary text-primary-foreground hover:bg-primary/90':
                            localGiftSendFilters.includes(String(option.value)),
                    }"
                    @click="toggleGiftSendFilter(Number(option.value))"
                >
                    {{ option.label }}
                </Button>
            </div>
        </div>
    </div>
</template>
