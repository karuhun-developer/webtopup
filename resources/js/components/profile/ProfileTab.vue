<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Main/ProfileController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useSwal } from '@/composables/useSwal';
import { send } from '@/routes/verification';
import { Form, Link, usePage } from '@inertiajs/vue3';

defineProps<{
    mustVerifyEmail: boolean;
    status?: string;
}>();

const page = usePage();
const user = page.props.auth.user;

const { toast } = useSwal();
</script>

<template>
    <div class="rounded-lg border border-border/50 bg-card p-6 shadow-sm">
        <h2 class="mb-6 text-lg font-bold text-foreground">Update Profile</h2>

        <Form
            v-bind="ProfileController.update.form()"
            :onSuccess="
                () =>
                    toast.fire({
                        icon: 'success',
                        title: 'Profile updated.',
                    })
            "
            class="space-y-4"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    name="name"
                    :default-value="user.name"
                    required
                    autocomplete="name"
                    placeholder="Full name"
                />
                <InputError :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    :default-value="user.email"
                    required
                    autocomplete="username"
                    placeholder="Email address"
                />
                <InputError :message="errors.email" />
            </div>

            <div v-if="mustVerifyEmail && !user.email_verified_at">
                <p class="-mt-2 text-sm text-muted-foreground">
                    Your email address is unverified.
                    <Link
                        :href="send()"
                        as="button"
                        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                    >
                        Click here to resend the verification email.
                    </Link>
                </p>

                <div
                    v-if="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="grid gap-2">
                <Label for="phone">Phone number</Label>
                <Input
                    id="phone"
                    type="number"
                    name="phone"
                    :default-value="user.phone"
                    required
                    autocomplete="phone"
                    placeholder="Phone number"
                />
                <InputError :message="errors.phone" />
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing">Save</Button>
            </div>
        </Form>
    </div>
</template>
