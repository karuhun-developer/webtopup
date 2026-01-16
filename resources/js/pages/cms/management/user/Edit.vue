<script setup lang="ts">
import { update } from '@/actions/App/Http/Controllers/Cms/Management/UserController';
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
import { RoleDataItem, UserDataItem } from '@/types/cms/management';
import { Form } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Save } from 'lucide-vue-next';

defineProps<{
    user: UserDataItem;
    roles: RoleDataItem[];
}>();

const { toast } = useSwal();
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Edit {{ user.name }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Edit the user details.
            </p>

            <Form
                v-bind="update.form({ user: user.id })"
                class="mt-6 space-y-6"
                @success="
                    () => {
                        toast.fire({
                            icon: 'success',
                            title: 'User updated.',
                        });
                        close();
                    }
                "
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-2">
                    <Label for="role">Role</Label>
                    <InputDescription>
                        The name of the role (e.g., 'admin', 'editor').
                    </InputDescription>
                    <Select name="role" :default-value="user.role_name">
                        <SelectTrigger id="role" class="mt-1 w-full">
                            <SelectValue placeholder="Select a role" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="role in roles"
                                :key="role.id"
                                :value="role.name"
                            >
                                {{ role.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.role" />
                </div>

                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <InputDescription> Username of the user. </InputDescription>
                    <Input
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        :default-value="user.name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="guard_name">Email</Label>
                    <InputDescription>
                        Email address of the user.
                    </InputDescription>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                        :default-value="user.email"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Phone</Label>
                    <InputDescription>
                        Phone number of the user.
                    </InputDescription>
                    <Input
                        id="phone"
                        name="phone"
                        type="text"
                        class="mt-1 block w-full"
                        :default-value="user.phone"
                    />
                    <InputError :message="errors.phone" />
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
