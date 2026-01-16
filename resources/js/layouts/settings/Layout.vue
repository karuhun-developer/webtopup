<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { toUrl, urlIsActive } from '@/lib/utils';
import { edit as editAppearance } from '@/routes/appearance';
import { edit as editProfile } from '@/routes/profile';
import { show } from '@/routes/two-factor';
import { edit as editPassword } from '@/routes/user-password';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';

const sidebarNavItems: NavItem[] = [
    {
        name: 'Profile',
        url: editProfile(),
        active_pattern: '/settings/profile',
    },
    {
        name: 'Password',
        url: editPassword(),
        active_pattern: '/settings/password',
    },
    {
        name: 'Two-Factor Auth',
        url: show(),
        active_pattern: '/settings/two-factor',
    },
    {
        name: 'Appearance',
        url: editAppearance(),
        active_pattern: '/settings/appearance',
    },
];

const currentPath = typeof window !== undefined ? window.location.pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            title="Settings"
            description="Manage your profile and account settings"
        />

        <div class="flex flex-col lg:flex-row lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="toUrl(item.url)"
                        variant="ghost"
                        :class="[
                            'w-full justify-start',
                            {
                                'bg-muted': urlIsActive(
                                    item.active_pattern,
                                    currentPath,
                                ),
                            },
                        ]"
                        as-child
                    >
                        <Link :href="item.url">
                            <component :is="item.icon" class="h-4 w-4" />
                            {{ item.name }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 lg:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
