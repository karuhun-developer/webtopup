<script setup lang="ts">
import { update } from '@/actions/App/Http/Controllers/Cms/Management/RoleController';
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
    role: RoleDataItem;
}>();

const { toast } = useSwal();
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Edit Role
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Edit the role details.
            </p>

            <Form
                v-bind="update.form({ role: role.id })"
                class="mt-6 space-y-6"
                @success="
                    () => {
                        toast.fire({
                            icon: 'success',
                            title: 'Role updated.',
                        });
                        close();
                    }
                "
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <InputDescription>
                        The name of the role (e.g., 'admin', 'editor').
                    </InputDescription>
                    <Input
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        :default-value="role.name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="guard_name">Guard Name</Label>
                    <InputDescription>
                        The guard name for the role, usually 'web' or 'api'.
                    </InputDescription>
                    <Select name="guard_name" :default-value="role.guard_name">
                        <SelectTrigger id="guard_name" class="mt-1 w-full">
                            <SelectValue placeholder="Select a guard" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="web">web</SelectItem>
                            <SelectItem value="api">api</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.guard_name" />
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
