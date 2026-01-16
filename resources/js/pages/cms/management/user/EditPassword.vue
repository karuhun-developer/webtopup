<script setup lang="ts">
import { updatePassword } from '@/actions/App/Http/Controllers/Cms/Management/UserController';
import InputDescription from '@/components/InputDescription.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useSwal } from '@/composables/useSwal';
import { UserDataItem } from '@/types/cms/management';
import { Form } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Save } from 'lucide-vue-next';

defineProps<{
    user: UserDataItem;
}>();

const { toast } = useSwal();
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Edit Password {{ user.name }}
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Edit the password for the user account.
            </p>

            <Form
                v-bind="updatePassword.form({ user: user.id })"
                class="mt-6 space-y-6"
                @success="
                    () => {
                        toast.fire({
                            icon: 'success',
                            title: 'Password updated.',
                        });
                        close();
                    }
                "
                v-slot="{ errors, processing }"
            >
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
                        <Save class="mr-2 h-4 w-4" />
                        Save Changes
                    </Button>
                </div>
            </Form>
        </div>
    </Modal>
</template>
