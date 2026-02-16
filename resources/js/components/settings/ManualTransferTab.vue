<script setup lang="ts">
import ImageUploadPreview from '@/components/ImageUploadPreview.vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { type SettingValue } from '@/types/cms/setting';

defineProps<{
    setting?: SettingValue;
    errors: Record<string, string>;
}>();
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-6 md:grid-cols-3">
            <div class="grid gap-2">
                <Label for="manual_transfer_type">Type</Label>
                <Select
                    name="value[manual_transfer_type]"
                    :default-value="setting?.manual_transfer_type"
                >
                    <SelectTrigger
                        id="manual_transfer_type"
                        class="mt-1 w-full"
                    >
                        <SelectValue placeholder="Select type" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="rekening">Rekening</SelectItem>
                        <SelectItem value="qris">QRIS</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors['value.manual_transfer_type']" />
            </div>

            <div class="grid gap-2">
                <Label for="manual_transfer_bank">Bank Name</Label>
                <Input
                    id="manual_transfer_bank"
                    name="value[manual_transfer_bank]"
                    :default-value="setting?.manual_transfer_bank"
                    placeholder="e.g., Bank BCA, Bank Mandiri"
                />
                <InputError :message="errors['value.manual_transfer_bank']" />
            </div>

            <div class="grid gap-2">
                <Label for="manual_transfer_account_name">Account Name</Label>
                <Input
                    id="manual_transfer_account_name"
                    name="value[manual_transfer_account_name]"
                    :default-value="setting?.manual_transfer_account_name"
                    placeholder="Account holder name"
                />
                <InputError
                    :message="errors['value.manual_transfer_account_name']"
                />
            </div>

            <div class="grid gap-2">
                <Label for="manual_transfer_account_number"
                    >Account Number</Label
                >
                <Input
                    id="manual_transfer_account_number"
                    name="value[manual_transfer_account_number]"
                    :default-value="setting?.manual_transfer_account_number"
                    placeholder="Account number"
                />
                <InputError
                    :message="errors['value.manual_transfer_account_number']"
                />
            </div>
        </div>

        <!-- Bank Logo Upload -->
        <ImageUploadPreview
            input-id="manual_transfer_bank_logo"
            input-name="value[manual_transfer_bank_logo]"
            label="Bank Logo"
            description="Upload bank logo (PNG or JPG, max 2MB)"
            :max-size="2"
            preview-height="150px"
            :initial-preview="setting?.manual_transfer_bank_logo || ''"
            :errors="errors['value.manual_transfer_bank_logo']"
        />
    </div>
</template>
