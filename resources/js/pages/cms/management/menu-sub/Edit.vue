<script setup lang="ts">
import { update } from '@/actions/App/Http/Controllers/Cms/Management/MenuSubController';
import InputDescription from '@/components/InputDescription.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useSwal } from '@/composables/useSwal';
import { CommonStatusEnum } from '@/enums/global.enum';
import { MenuDataItem, MenuSubDataItem } from '@/types/cms/management';
import { Form } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Save } from 'lucide-vue-next';

defineProps<{
    subMenu: MenuSubDataItem;
    menu: MenuDataItem;
}>();

const { toast } = useSwal();
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="bg-white p-6 dark:bg-zinc-950 dark:text-white">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Edit Menu Sub
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Edit the sub-menu item details.
            </p>

            <Form
                v-bind="update.form({ menu: menu.id, sub_menu: subMenu.id })"
                class="mt-6 space-y-6"
                @success="
                    () => {
                        toast.fire({
                            icon: 'success',
                            title: 'Menu Sub updated.',
                        });
                        close();
                    }
                "
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-2">
                    <InputDescription>
                        The display name of the menu item.
                    </InputDescription>
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        :default-value="subMenu.name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="url">URL</Label>
                    <InputDescription>
                        The URL path the menu item points to.
                    </InputDescription>
                    <Input
                        id="url"
                        name="url"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        :default-value="subMenu.url"
                    />
                    <InputError :message="errors.url" />
                </div>

                <div class="grid gap-2">
                    <Label for="icon">Icon</Label>
                    <InputDescription>
                        The icon class for the menu item (e.g., 'XSquare').
                    </InputDescription>
                    <Input
                        id="icon"
                        name="icon"
                        type="text"
                        class="mt-1 block w-full"
                        :default-value="subMenu.icon"
                    />
                    <InputError :message="errors.icon" />
                </div>

                <div class="grid gap-2">
                    <Label for="order">Order</Label>
                    <InputDescription>
                        The display order of the menu item.
                    </InputDescription>
                    <Input
                        id="order"
                        name="order"
                        type="number"
                        class="mt-1 block w-full"
                        required
                        :default-value="subMenu.order"
                    />
                    <InputError :message="errors.order" />
                </div>

                <div class="grid gap-2">
                    <Label for="active_pattern">Active Pattern</Label>
                    <InputDescription>
                        The URL pattern to determine when the menu item is
                        active (e.g., '/cms/management/menu').
                    </InputDescription>
                    <Input
                        id="active_pattern"
                        name="active_pattern"
                        type="text"
                        class="mt-1 block w-full"
                        :default-value="subMenu.active_pattern"
                    />
                    <InputError :message="errors.active_pattern" />
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <InputDescription>
                        Status of the menu item (Active or Inactive).
                    </InputDescription>
                    <Select
                        name="status"
                        :default-value="Number(subMenu.status)"
                    >
                        <SelectTrigger id="status" class="mt-1 w-full">
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <template
                                v-for="commnStatus in CommonStatusEnum"
                                :key="commnStatus.value"
                            >
                                <SelectItem :value="commnStatus.value">
                                    {{ commnStatus.label }}
                                </SelectItem>
                            </template>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.status" />
                </div>

                <div class="flex justify-end gap-4">
                    <Button :disabled="processing" type="submit">
                        <Save class="mr-2 h-4 w-4" />
                        Save Changes
                    </Button>
                </div>
            </Form>
        </div>
    </Modal>
</template>
