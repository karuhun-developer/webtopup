import { InertiaLinkProps } from '@inertiajs/vue3';

interface PaginationItem<T> {
    data: T[];
    current_page: number;
    first_page_url: string | null;
    from: number;
    last_page: number;
    last_page_url: string | null;
    links: Array<{
        url: string | null;
        page: number | null;
        label: string;
        active: boolean;
    }>;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface Auth {
    user: User;
    menus?: NavItem[];
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    name: string;
    url: string | InertiaLinkProps['href'];
    icon?: string;
    isActive?: boolean;
    active_pattern?: string[] | string;
    sub_menu?: NavItem[];
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    permissions?: string[];
    roles?: string[];
}

export type BreadcrumbItemType = BreadcrumbItem;
