<script setup lang="ts">
interface Props {
    message?: string;
}

defineProps<Props>();
</script>

<template>
    <p class="text-[0.8rem] text-muted-foreground break-words line-clamp-3">
        <slot>{{ message }}</slot>
    </p>
</template>
