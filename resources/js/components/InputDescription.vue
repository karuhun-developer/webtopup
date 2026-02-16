<script setup lang="ts">
interface Props {
    message?: string;
}

defineProps<Props>();
</script>

<template>
    <p class="line-clamp-3 text-[0.8rem] break-words text-muted-foreground">
        <slot>{{ message }}</slot>
    </p>
</template>
