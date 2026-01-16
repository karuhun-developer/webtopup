<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/Cms/Management/UserController';
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
import { RoleDataItem } from '@/types/cms/management';
import { Form } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Save } from 'lucide-vue-next';

defineProps<{
    roles: RoleDataItem[];
}>();

const { toast } = useSwal();
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Create User
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Create a new user for the application.
            </p>

            <Form
                v-bind="store.form()"
                class="mt-6 space-y-6"
                @success="
                    () => {
                        toast.fire({
                            icon: 'success',
                            title: 'User created.',
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
                    <Select name="role">
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
                    />
                    <InputError :message="errors.phone" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <InputDescription>
                        Password for the user account.
                    </InputDescription>
                    <Input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="new-password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm Password</Label>
                    <InputDescription>
                        Confirm the password for the user account.
                    </InputDescription>
                    <Input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="new-password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <div class="flex justify-end gap-4">
                    <Button :disabled="processing" type="submit">
                        <Save />
                        Save Changes
                    </Button>
                </div>
            </Form>
        </div>
    </Modal>
</template>
