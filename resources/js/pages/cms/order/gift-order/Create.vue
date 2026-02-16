<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/Cms/Order/GiftOrderController';
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
import { formatCurrency } from '@/lib/utils';
import { PPOBProductDataItem } from '@/types/cms/ppob';
import { useForm } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Save } from 'lucide-vue-next';
import { computed, watch } from 'vue';

const props = defineProps<{
    products: PPOBProductDataItem[];
}>();

const { toast } = useSwal();

// Form Data
const form = useForm({
    p_p_o_b_brand_id: '',
    product_id: '',
    account_id: '',
    server_id: '',
    email: '',
    name: '',
    phone: '',
    payment_type: 'manual',
    payment_method: 'manual',
    submited: {} as Record<string, any>,
});

const selectedProduct = computed(() => {
    return props.products.find((p) => p.id === Number(form.product_id));
});

// Update brand_id when product is selected
watch(
    () => form.product_id,
    (newVal) => {
        if (newVal && selectedProduct.value) {
            form.p_p_o_b_brand_id = String(
                selectedProduct.value.p_p_o_b_brand_id,
            );
            // Reset account fields
            form.account_id = '';
            form.server_id = '';
        }
    },
);

const inputType = computed(
    () => selectedProduct.value?.brand?.settings?.type || 'id',
);
const labelId = computed(
    () => selectedProduct.value?.brand?.settings?.label_id || 'ID',
);
const labelServer = computed(
    () => selectedProduct.value?.brand?.settings?.label_server || 'Server',
);
const serverOptions = computed(
    () => selectedProduct.value?.brand?.settings?.servers || [],
);

const submit = (close: () => void) => {
    form.post(store().url, {
        onSuccess: () => {
            toast.fire({
                icon: 'success',
                title: 'Order created successfully.',
            });
            close();
        },
    });
};
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <h2 class="text-lg font-medium">Create Gift Order</h2>

            <p class="mt-1 text-sm text-muted-foreground">
                Create a new manual gift order.
            </p>

            <form @submit.prevent="submit(close)" class="mt-6 space-y-6">
                <!-- Product Selection -->
                <div class="grid gap-2">
                    <Label for="product">Product</Label>
                    <Select v-model="form.product_id" name="product_id">
                        <SelectTrigger id="product" class="w-full">
                            <SelectValue placeholder="Select a product" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="product in products"
                                :key="product.id"
                                :value="String(product.id)"
                            >
                                {{ product.brand?.name }} -
                                {{ product.name }} ({{
                                    formatCurrency(product.sell_price)
                                }})
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.product_id" />
                </div>

                <!-- Account Data -->
                <div class="grid grid-cols-2 gap-4" v-if="selectedProduct">
                    <div class="col-span-2 md:col-span-1">
                        <Label for="account_id">{{ labelId }}</Label>
                        <Input
                            id="account_id"
                            name="account_id"
                            v-model="form.account_id"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Enter User ID"
                            required
                        />
                        <InputError :message="form.errors.account_id" />
                    </div>

                    <div
                        class="col-span-2 md:col-span-1"
                        v-if="inputType === 'id+server'"
                    >
                        <Label for="server_id">{{ labelServer }}</Label>
                        <Input
                            v-if="serverOptions.length === 0"
                            id="server_id"
                            name="server_id"
                            v-model="form.server_id"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Enter Server ID"
                            required
                        />
                        <Select
                            v-else
                            v-model="form.server_id"
                            name="server_id"
                        >
                            <SelectTrigger id="server_id" class="mt-1 w-full">
                                <SelectValue placeholder="Select Server" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="server in serverOptions"
                                    :key="server.id"
                                    :value="server.id"
                                >
                                    {{ server.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.server_id" />
                    </div>
                </div>

                <!-- User Data -->
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="name">Customer Name</Label>
                        <Input
                            id="name"
                            name="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Admin Order"
                            required
                        />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="phone">Phone Number</Label>
                        <Input
                            id="phone"
                            name="phone"
                            v-model="form.phone"
                            type="tel"
                            class="mt-1 block w-full"
                            placeholder="628..."
                            required
                        />
                        <InputError :message="form.errors.phone" />
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <Button :disabled="form.processing" type="submit">
                        <Save class="mr-2 h-4 w-4" />
                        Create Order
                    </Button>
                </div>
            </form>
        </div>
    </Modal>
</template>
