<script setup lang="ts">
import { cn } from '@/lib/utils';
import { useScroll } from '@vueuse/core';
import { Check, ChevronsUpDown, Loader2 } from 'lucide-vue-next';
import {
    ComboboxAnchor,
    ComboboxContent,
    ComboboxEmpty,
    ComboboxGroup,
    ComboboxInput,
    ComboboxItem,
    ComboboxItemIndicator,
    ComboboxPortal,
    ComboboxRoot,
    ComboboxTrigger,
    ComboboxViewport,
    useForwardPropsEmits,
    type ComboboxRootEmits,
    type ComboboxRootProps,
} from 'reka-ui';
import { computed, ref, watch } from 'vue';

const props = defineProps<
    ComboboxRootProps & {
        options: any[];
        loading?: boolean;
        displayValue?: (option: any) => string;
        placeholder?: string;
        minCharacters?: number;
        searchTerm?: string;
        class?: string;
    }
>();

const emits = defineEmits<
    ComboboxRootEmits & {
        'update:searchTerm': [term: string];
        'loadMore': [];
    }
>();

const delegatedProps = computed(() => {
    const { searchTerm, modelValue, ...delegated } = props;
    return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);

// Local input value synchronization
const inputValue = ref(props.searchTerm || '');

// Sync local input when searchTerm prop changes (e.g. parent clears it)
watch(
    () => props.searchTerm,
    (newVal) => {
        if (newVal !== undefined && newVal !== inputValue.value) {
            inputValue.value = newVal;
        }
    }
);

// Sync local input when modelValue selected changes
watch(
    () => props.modelValue,
    (newVal) => {
        if (newVal && props.displayValue) {
            inputValue.value = props.displayValue(newVal);
            // Also notify parent about the term update to keep everything in sync
            emits('update:searchTerm', inputValue.value);
        }
    },
    { deep: true }
);

const onInputUpdate = (val: string) => {
    inputValue.value = val;
    emits('update:searchTerm', val);
};

const viewport = ref<HTMLElement | null>(null);

const { arrivedState } = useScroll(viewport);

watch(
    () => arrivedState.bottom,
    (isBottom) => {
        if (isBottom && !props.loading) {
            emits('loadMore');
        }
    },
);
</script>

<template>
    <ComboboxRoot
        v-bind="forwarded"
        class="relative w-full"
        :model-value="props.modelValue"
        :display-value="displayValue"
        :filter-function="() => true"
        :class="cn('flex flex-col', props.class)"
    >
        <ComboboxAnchor
            class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background data-[placeholder]:text-muted-foreground focus-within:ring-2 focus-within:ring-ring focus-within:ring-offset-2"
        >
            <ComboboxInput
                class="flex h-full w-full bg-transparent text-sm placeholder:text-muted-foreground focus:outline-none"
                :placeholder="placeholder"
                :model-value="inputValue"
                @update:model-value="onInputUpdate"
            />
            <ComboboxTrigger class="focus:outline-none">
                <ChevronsUpDown class="h-4 w-4 shrink-0 opacity-50" />
            </ComboboxTrigger>
        </ComboboxAnchor>

        <ComboboxPortal>
            <ComboboxContent
                class="z-50 min-w-[var(--reka-combobox-trigger-width)] overflow-hidden rounded-md border bg-popover text-popover-foreground shadow-md animate-in fade-in-0 zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2"
                :position="'popper'"
                :side-offset="4"
            >
                <ComboboxViewport
                    ref="viewport"
                    class="max-h-[200px] w-full p-1"
                >
                    <ComboboxEmpty class="py-6 text-center text-sm">
                        <span
                            v-if="
                                minCharacters &&
                                (searchTerm?.length || 0) < minCharacters
                            "
                        >
                            Type at least {{ minCharacters }} characters to
                            search...
                        </span>
                        <span
                            v-else-if="loading && options.length === 0"
                            class="flex items-center justify-center gap-2"
                        >
                            <Loader2 class="h-4 w-4 animate-spin" />
                            Loading...
                        </span>
                        <span v-else>No results found.</span>
                    </ComboboxEmpty>

                    <ComboboxGroup>
                        <ComboboxItem
                            v-for="option in options"
                            :key="option.id || option.value"
                            :value="option"
                            class="relative flex cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none data-[highlighted]:bg-accent data-[highlighted]:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50"
                        >
                            <span
                                class="absolute left-2 flex h-3.5 w-3.5 items-center justify-center"
                            >
                                <ComboboxItemIndicator>
                                    <Check class="h-4 w-4" />
                                </ComboboxItemIndicator>
                            </span>
                            <span class="pl-8">{{
                                displayValue ? displayValue(option) : option.name
                            }}</span>
                        </ComboboxItem>
                        <div
                            v-if="loading && options.length > 0"
                            class="flex justify-center p-2"
                        >
                            <Loader2 class="h-4 w-4 animate-spin" />
                        </div>
                    </ComboboxGroup>
                </ComboboxViewport>
            </ComboboxContent>
        </ComboboxPortal>
    </ComboboxRoot>
</template>
