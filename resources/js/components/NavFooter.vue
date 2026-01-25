<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { toUrl } from '@/lib/utils';
import { type NavItem } from '@/types';
import Icon from './Icon.vue';

interface Props {
    items: NavItem[];
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <SidebarGroup
        :class="`group-data-[collapsible=icon]:p-0 ${$props.class || ''}`"
    >
        <SidebarGroupContent>
            <SidebarMenu>
                <SidebarMenuItem v-for="item in items" :key="item.name">
                    <SidebarMenuButton
                        class="text-sidebar-foreground hover:text-sidebar-foreground/80"
                        as-child
                    >
                        <a
                            :href="toUrl(item.url)"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <Icon :name="item.icon" v-if="item.icon" />
                            <span>{{ item.name }}</span>
                        </a>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
