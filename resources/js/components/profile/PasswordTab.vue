<script setup lang="ts">
import PasswordController from '@/actions/App/Http/Controllers/Main/PasswordController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useSwal } from '@/composables/useSwal';
import { Form } from '@inertiajs/vue3';

const { toast } = useSwal();
</script>

<template>
    <div class="rounded-lg border border-border/50 bg-card p-6 shadow-sm">
        <h2 class="mb-6 text-lg font-bold text-foreground">
            Change Password
        </h2>

        <Form
            v-bind="PasswordController.update.form()"
            :options="{
                preserveScroll: true,
            }"
            :onSuccess="
                () =>
                    toast.fire({
                        icon: 'success',
                        title: 'Password updated.',
                    })
            "
            reset-on-success
            :reset-on-error="[
                'password',
                'password_confirmation',
                'current_password',
            ]"
            class="space-y-4"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="current_password">Current password</Label>
                <Input
                    id="current_password"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    placeholder="Current password"
                />
                <InputError :message="errors.current_password" />
            </div>

            <div class="grid gap-2">
                <Label for="password">New password</Label>
                <Input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    placeholder="New password"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirm password</Label>
                <Input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    placeholder="Confirm password"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save password</Button>
            </div>
        </Form>
    </div>
</template>
