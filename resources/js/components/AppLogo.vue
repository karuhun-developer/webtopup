<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const name = page.props.setting?.title || page.props.name;
const logo = page.props.setting?.logo;
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md text-sidebar-primary-foreground"
    >
        <img
            v-if="logo"
            :src="logo"
            alt="Logo"
            class="size-8 rounded-md object-cover"
        />
        <div
            v-else
            class="flex items-center justify-center rounded-md bg-sidebar-primary"
        >
            <AppLogoIcon
                class="size-5 fill-current text-white dark:text-black"
            />
        </div>
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">
            {{ name }}
        </span>
    </div>
</template>
