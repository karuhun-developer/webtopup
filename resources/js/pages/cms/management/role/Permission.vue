<script setup lang="ts">
import { index as roleIndex } from '@/actions/App/Http/Controllers/Cms/Management/RoleController';
import {
    checkAllPermissions,
    checkPermissions,
    uncheckAllPermissions,
    uncheckPermissions,
} from '@/actions/App/Http/Controllers/Cms/Management/RolePermissionController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Skeleton } from '@/components/ui/skeleton';
import { useSwal } from '@/composables/useSwal';
import AppLayout from '@/layouts/AppLayout.vue';
import { BreadcrumbItem } from '@/types';
import { RoleDataItem } from '@/types/cms/management';
import { Deferred, Head, router } from '@inertiajs/vue3';
import { CheckSquare, XSquare } from 'lucide-vue-next';

const props = defineProps<{
    role: RoleDataItem;
    permissions?: Record<string, Record<string, boolean>>;
}>();

const { toast } = useSwal();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Managements',
        href: '#',
    },
    {
        title: 'Role Management',
        href: roleIndex().url,
    },
    {
        title: 'Permissions',
        href: '#',
    },
];

const handleCheckAll = () => {
    router.visit(checkAllPermissions({ role: props.role.id }).url, {
        method: 'put',
        preserveScroll: true,
        onSuccess: () => {
            toast.fire({
                icon: 'success',
                title: 'All permissions have been checked.',
            });
        },
    });
};

const handleUncheckAll = () => {
    router.visit(uncheckAllPermissions({ role: props.role.id }).url, {
        method: 'put',
        preserveScroll: true,
        onSuccess: () => {
            toast.fire({
                icon: 'success',
                title: 'All permissions have been unchecked.',
            });
        },
    });
};

const handlePermissionChange = (
    checked: boolean,
    model: string,
    permission: string,
) => {
    const action = checked ? uncheckPermissions : checkPermissions;
    router.visit(action({ role: props.role.id }).url, {
        method: 'put',
        data: {
            permission: permission + model,
        },
        preserveScroll: true,
        onSuccess: () => {
            toast.fire({
                icon: 'success',
                title: 'Permission has been updated.',
            });
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Role Permissions" />

        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <Heading
                    :title="`Permissions for ${role.name}`"
                    description="Manage permissions for this role."
                />
            </div>

            <Card>
                <CardHeader>
                    <div class="flex items-center justify-end gap-2">
                        <Button
                            variant="default"
                            size="sm"
                            @click="handleCheckAll"
                        >
                            <CheckSquare />
                            Check All
                        </Button>
                        <Button
                            variant="destructive"
                            size="sm"
                            @click="handleUncheckAll"
                        >
                            <XSquare />
                            Uncheck All
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <Deferred data="permissions">
                        <template #fallback>
                            <div class="space-y-6">
                                <div v-for="i in 3" :key="i" class="space-y-3">
                                    <Skeleton class="h-6 w-1/3" />
                                    <div class="grid grid-cols-4 gap-4">
                                        <Skeleton
                                            v-for="j in 4"
                                            :key="j"
                                            class="h-4 w-full"
                                        />
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div class="space-y-8">
                            <div
                                v-for="(perms, model) in permissions"
                                :key="model"
                            >
                                <h3
                                    class="mb-4 font-semibold text-gray-900 dark:text-gray-100"
                                >
                                    Resource: {{ model }}
                                </h3>
                                <div
                                    class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
                                >
                                    <div
                                        v-for="(isChecked, permission) in perms"
                                        :key="permission"
                                        class="flex items-center space-x-2"
                                    >
                                        <Checkbox
                                            :id="`${model}-${permission}`"
                                            :default-value="isChecked"
                                            @update:model-value="
                                                handlePermissionChange(
                                                    isChecked,
                                                    model,
                                                    permission,
                                                )
                                            "
                                        />
                                        <Label
                                            :for="`${model}-${permission}`"
                                            class="text-sm leading-none font-medium peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
                                        >
                                            {{ permission }}
                                        </Label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Deferred>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
