<script setup lang="ts">
interface Props {
    message?: string;
}

defineProps<Props>();
</script>

<template>
    <p class="text-[0.8rem] text-muted-foreground">
        <slot>{{ message }}</slot>
    </p>
</template>
