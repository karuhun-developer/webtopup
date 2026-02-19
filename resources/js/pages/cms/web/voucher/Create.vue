<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/Cms/Web/VoucherController';
import InputDescription from '@/components/InputDescription.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import MoneyInput from '@/components/ui/money-input/MoneyInput.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { useSwal } from '@/composables/useSwal';
import { Form } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Save } from 'lucide-vue-next';
import { ref } from 'vue';

const { toast } = useSwal();

const type = ref<string>('FIXED_AMOUNT');
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <h2 class="text-lg font-medium">Create Voucher</h2>

            <p class="mt-1 text-sm text-muted-foreground">
                Create a new Voucher by filling out the form below.
            </p>

            <Form
                v-bind="store.form()"
                class="mt-6 space-y-6"
                @success="
                    () => {
                        toast.fire({
                            icon: 'success',
                            title: 'Voucher created successfully.',
                        });
                        close();
                    }
                "
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-2">
                    <Label for="code">Code</Label>
                    <InputDescription
                        >The unique code for the voucher.</InputDescription
                    >
                    <Input
                        id="code"
                        name="code"
                        placeholder="SUMMER2024"
                        required
                        autofocus
                    />
                    <InputError :message="errors.code" />
                </div>

                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <InputDescription
                        >The display name of the voucher.</InputDescription
                    >
                    <Input
                        id="name"
                        name="name"
                        placeholder="Summer Sale Voucher"
                        required
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description (Optional)</Label>
                    <InputDescription
                        >A brief description of the voucher.</InputDescription
                    >
                    <Textarea
                        id="description"
                        name="description"
                        placeholder="Get 20% off for all items."
                    />
                    <InputError :message="errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="type">Type</Label>
                    <InputDescription
                        >Select the discount type.</InputDescription
                    >
                    <!-- We use a hidden input for the actual form submission -->
                    <input type="hidden" name="type" :value="type" />
                    <Select
                        :default-value="type"
                        @update:model-value="(val) => (type = val)"
                    >
                        <SelectTrigger id="type" class="w-full">
                            <SelectValue placeholder="Select a type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="FIXED_AMOUNT"
                                >Fixed Amount</SelectItem
                            >
                            <SelectItem value="PERCENTAGE"
                                >Percentage</SelectItem
                            >
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.type" />
                </div>

                <div v-if="type === 'FIXED_AMOUNT'" class="grid gap-2">
                    <Label for="fixed_amount">Fixed Amount</Label>
                    <InputDescription
                        >The fixed discount amount.</InputDescription
                    >
                    <MoneyInput
                        id="fixed_amount"
                        name="fixed_amount"
                        :default-value="10000"
                    />
                    <InputError :message="errors.fixed_amount" />
                </div>

                <div v-if="type === 'PERCENTAGE'" class="grid gap-2">
                    <Label for="percentage">Percentage</Label>
                    <InputDescription
                        >The discount percentage (0-100).</InputDescription
                    >
                    <Input
                        id="percentage"
                        name="percentage"
                        type="number"
                        step="0.01"
                        placeholder="20"
                    />
                    <InputError :message="errors.percentage" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="start_date">Start Date (Optional)</Label>
                        <Input
                            id="start_date"
                            name="start_date"
                            type="datetime-local"
                        />
                        <InputError :message="errors.start_date" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="end_date">End Date (Optional)</Label>
                        <Input
                            id="end_date"
                            name="end_date"
                            type="datetime-local"
                        />
                        <InputError :message="errors.end_date" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label for="min_purchase_amount"
                            >Min Purchase Amount (Optional)</Label
                        >
                        <InputDescription
                            >Minimum purchase to use.</InputDescription
                        >
                        <MoneyInput
                            id="min_purchase_amount"
                            name="min_purchase_amount"
                            :default-value="0"
                        />
                        <InputError :message="errors.min_purchase_amount" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="usage_limit">Usage Limit (Optional)</Label>
                        <InputDescription
                            >Total usage limit (0 for
                            unlimited).</InputDescription
                        >
                        <MoneyInput
                            id="usage_limit"
                            name="usage_limit"
                            :default-value="0"
                        />
                        <InputError :message="errors.usage_limit" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <InputDescription
                        >Select the status of the voucher.</InputDescription
                    >
                    <Select name="status" default-value="1">
                        <SelectTrigger id="status" class="w-full">
                            <SelectValue placeholder="Select a status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="1">Active</SelectItem>
                            <SelectItem value="0">Inactive</SelectItem>
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
