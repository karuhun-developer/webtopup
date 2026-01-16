<script setup lang="ts">
import { cn } from '@/lib/utils'
import { ref, watch } from 'vue'

const props = withDefaults(
    defineProps<{
        modelValue?: number | string | null
        defaultValue?: number | string
        currency?: string
        locale?: string
        class?: string
    }>(),
    {
        currency: 'IDR',
        locale: 'id-ID',
    },
)

const emits = defineEmits<{
    (e: 'update:modelValue', payload: number | null): void
}>()

const inputRef = ref<HTMLInputElement | null>(null)
const internalValue = ref('')

// Create formatter dynamically based on props
const getFormatter = () => {
    return new Intl.NumberFormat(props.locale, {
        currency: props.currency,
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    })
}

const formatValue = (val: number | string | null | undefined) => {
    if (val === null || val === '' || val === undefined) return ''
    const num = typeof val === 'string' ? parseFloat(val) : val
    if (isNaN(num)) return ''
    return getFormatter().format(num)
}

// Watch for external changes
watch(
    () => props.modelValue,
    (newVal) => {
        // If the user is currently typing (input is focused), we generally avoid
        // overwriting the internal value to prevent cursor jumping,
        // UNLESS the new value is drastically different (e.g. set from outside).
        // However, for a simple money input, enforcing format on type is common.
        // We will try to sync if values mismatch.

        // Handle default value logic
        let valueToUse = newVal
        if (valueToUse === undefined) {
            valueToUse = props.defaultValue
        }

        const formatted = formatValue(valueToUse)
        const currentDigits = internalValue.value.replace(/\D/g, '')
        const newDigits = formatted.replace(/\D/g, '')

        if (currentDigits !== newDigits) {
            internalValue.value = formatted
        }
    },
    { immediate: true },
)

const onInput = (e: Event) => {
    const target = e.target as HTMLInputElement
    const value = target.value

    // Strip non-digits
    const digits = value.replace(/\D/g, '')

    if (!digits) {
        internalValue.value = ''
        emits('update:modelValue', null)
        return
    }

    const num = parseInt(digits, 10)
    const formatted = getFormatter().format(num)

    // Update display value
    // Note: Replacing value moves cursor to end.
    // For 'Rp ' prefix inputs, this is generally acceptable behavior for basic implementations.
    internalValue.value = formatted
    emits('update:modelValue', num)
}
</script>

<template>
    <input
        ref="inputRef"
        :value="internalValue"
        @input="onInput"
        :class="
            cn(
                'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
                'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
                props.class,
            )
        "
    />
</template>
