<script setup lang="ts">
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { urlIsActive } from '@/lib/utils';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronRight } from 'lucide-vue-next';
import Icon from './Icon.vue';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();
const { state } = useSidebar();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.name">
                <template
                    v-if="
                        state === 'collapsed' &&
                        item.sub_menu &&
                        item.sub_menu.length > 0
                    "
                >
                    <SidebarMenuItem>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <SidebarMenuButton
                                    :tooltip="item.name"
                                    :is-active="
                                        item.isActive ||
                                        urlIsActive(
                                            item.active_pattern,
                                            page.url,
                                        )
                                    "
                                >
                                    <Icon :name="item.icon" v-if="item.icon" />
                                    <span>{{ item.name }}</span>
                                    <ChevronRight class="ml-auto" />
                                </SidebarMenuButton>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent side="right" align="start">
                                <DropdownMenuLabel>{{
                                    item.name
                                }}</DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem
                                    v-for="subItem in item.sub_menu"
                                    :key="subItem.name"
                                    as-child
                                >
                                    <Link
                                        :href="subItem.url"
                                        :class="{
                                            'bg-accent': urlIsActive(
                                                subItem.active_pattern,
                                                page.url,
                                            ),
                                        }"
                                    >
                                        {{ subItem.name }}
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </SidebarMenuItem>
                </template>

                <Collapsible
                    v-else-if="item.sub_menu && item.sub_menu.length > 0"
                    as-child
                    :default-open="
                        item.isActive ||
                        item.sub_menu.some((subItem) =>
                            urlIsActive(subItem.active_pattern, page.url),
                        )
                    "
                    class="group/collapsible"
                >
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child>
                            <SidebarMenuButton :tooltip="item.name">
                                <Icon :name="item.icon" v-if="item.icon" />
                                <span>{{ item.name }}</span>
                                <ChevronRight
                                    class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                                />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub>
                                <SidebarMenuSubItem
                                    v-for="subItem in item.sub_menu"
                                    :key="subItem.name"
                                >
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="
                                            urlIsActive(
                                                subItem.active_pattern,
                                                page.url,
                                            )
                                        "
                                    >
                                        <Link :href="subItem.url">
                                            <span>{{ subItem.name }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <SidebarMenuItem v-else>
                    <SidebarMenuButton
                        as-child
                        :is-active="urlIsActive(item.active_pattern, page.url)"
                        :tooltip="item.name"
                    >
                        <Link :href="item.url">
                            <Icon :name="item.icon" v-if="item.icon" />
                            <span>{{ item.name }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
