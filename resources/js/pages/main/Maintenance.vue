<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const setting = page.props.setting;
</script>

<template>
    <main
        class="mx-auto flex max-w-7xl flex-col items-center justify-center px-4 py-16 text-center"
    >
        <div class="mb-8 w-full max-w-md">
            <img
                v-if="setting?.maintenance_image"
                :src="setting?.maintenance_image"
                alt="Maintenance"
                class="mx-auto h-auto w-full max-w-[300px] object-contain"
            />
            <div v-else class="text-6xl text-primary">🛠️</div>
        </div>

        <h1 class="mb-4 text-3xl font-bold text-foreground">
            {{ setting?.maintenance_title || 'Sistem Sedang Maintenance' }}
        </h1>

        <div
            class="prose prose-sm dark:prose-invert mx-auto text-muted-foreground sm:prose-base"
            v-html="setting?.maintenance_description || '<p>Mohon maaf, saat ini sistem sedang dalam perbaikan rutin. Silakan kembali lagi nanti.</p>'"
        ></div>
    </main>
</template>
