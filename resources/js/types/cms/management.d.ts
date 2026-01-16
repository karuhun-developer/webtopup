export interface MenuDataItem {
    id: number;
    role_id: number;
    role_name?: string;
    name: string;
    url: string;
    icon: string;
    order: number;
    active_pattern: string;
    status: boolean;
    created_at: string;
    updated_at: string;
    role?: RoleDataItem;
}

export interface MenuSubDataItem {
    id: number;
    role_id: number;
    menu_id: number;
    name: string;
    url: string;
    icon: string;
    order: number;
    active_pattern: string;
    status: boolean;
    created_at: string;
    updated_at: string;
    role?: RoleDataItem;
    menu?: MenuDataItem;
}

export interface PermissionDataItem {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
}

export interface RoleDataItem {
    id: number;
    name: string;
    guard_name: string;
    created_at: string;
    updated_at: string;
}

export interface UserDataItem {
    id: number;
    role_name?: string;
    name: string;
    email: string;
    phone?: string;
    email_verified_at?: string;
    created_at: string;
    updated_at: string;
    roles?: RoleDataItem[];
}
