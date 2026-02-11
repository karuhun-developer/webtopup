<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { type SettingValue } from '@/types/cms/setting';

defineProps<{
    setting?: SettingValue;
    errors: Record<string, string>;
}>();
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="template_checkout">Checkout Template</Label>
                <Textarea
                    id="template_checkout"
                    name="value[template_checkout]"
                    :default-value="setting?.template_checkout"
                    placeholder="WhatsApp template for checkout"
                    rows="8"
                />
                <InputError :message="errors['value.template_checkout']" />
                <p class="text-xs text-muted-foreground">
                    Available variables: {app_name}, {order_id}, {product},
                    {total}, {link}, {cs_link}
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="template_payment_confirmation"
                    >Payment Confirmation Template</Label
                >
                <Textarea
                    id="template_payment_confirmation"
                    name="value[template_payment_confirmation]"
                    :default-value="setting?.template_payment_confirmation"
                    placeholder="WhatsApp template for payment confirmation"
                    rows="8"
                />
                <InputError
                    :message="errors['value.template_payment_confirmation']"
                />
                <p class="text-xs text-muted-foreground">
                    Available variables: {customer_name}, {order_id},
                    {app_name}, {link}, {cs_link}
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="template_payment_rejected"
                    >Payment Rejected Template</Label
                >
                <Textarea
                    id="template_payment_rejected"
                    name="value[template_payment_rejected]"
                    :default-value="setting?.template_payment_rejected"
                    placeholder="WhatsApp template for payment rejection"
                    rows="8"
                />
                <InputError
                    :message="errors['value.template_payment_rejected']"
                />
                <p class="text-xs text-muted-foreground">
                    Available variables: {customer_name}, {order_id}, {link},
                    {cs_link}
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="template_order_completed"
                    >Order Completed Template</Label
                >
                <Textarea
                    id="template_order_completed"
                    name="value[template_order_completed]"
                    :default-value="setting?.template_order_completed"
                    placeholder="WhatsApp template for completed orders"
                    rows="8"
                />
                <InputError
                    :message="errors['value.template_order_completed']"
                />
                <p class="text-xs text-muted-foreground">
                    Available variables: {customer_name}, {order_id},
                    {app_name}, {link}, {cs_link}
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="template_order_failed"
                    >Order Failed Template</Label
                >
                <Textarea
                    id="template_order_failed"
                    name="value[template_order_failed]"
                    :default-value="setting?.template_order_failed"
                    placeholder="WhatsApp template for failed orders"
                    rows="8"
                />
                <InputError :message="errors['value.template_order_failed']" />
                <p class="text-xs text-muted-foreground">
                    Available variables: {customer_name}, {order_id}, {link},
                    {cs_link}
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="template_gift_order_admin_friend_request"
                    >Gift Order - Admin Friend Request Template</Label
                >
                <Textarea
                    id="template_gift_order_admin_friend_request"
                    name="value[template_gift_order_admin_friend_request]"
                    :default-value="setting?.template_gift_order_admin_friend_request"
                    placeholder="WhatsApp template for gift order admin friend request"
                    rows="8"
                />
                <InputError
                    :message="errors['value.template_gift_order_admin_friend_request']"
                />
                <p class="text-xs text-muted-foreground">
                    Available variables: {customer_name}, {order_id},
                    {cs_link}
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="template_gift_order_user_friend_confirmation"
                    >Gift Order - User Friend Confirmation Template</Label
                >
                <Textarea
                    id="template_gift_order_user_friend_confirmation"
                    name="value[template_gift_order_user_friend_confirmation]"
                    :default-value="setting?.template_gift_order_user_friend_confirmation"
                    placeholder="WhatsApp template for gift order user friend confirmation"
                    rows="8"
                />
                <InputError
                    :message="errors['value.template_gift_order_user_friend_confirmation']"
                />
                <p class="text-xs text-muted-foreground">
                    Available variables: {customer_name}, {order_id},
                    {app_name}, {cs_link}
                </p>
            </div>

            <div class="grid gap-2">
                <Label for="template_gift_order_completion"
                    >Gift Order - Completion Template</Label
                >
                <Textarea
                    id="template_gift_order_completion"
                    name="value[template_gift_order_completion]"
                    :default-value="setting?.template_gift_order_completion"
                    placeholder="WhatsApp template for gift order completion"
                    rows="8"
                />
                <InputError
                    :message="errors['value.template_gift_order_completion']"
                />
                <p class="text-xs text-muted-foreground">
                    Available variables: {customer_name}, {order_id},
                    {app_name}, {cs_link}
                </p>
            </div>
        </div>
    </div>
</template>
