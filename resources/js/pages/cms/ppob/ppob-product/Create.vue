<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/Cms/PPOB/PPOBProductController';
import ImageUploadPreview from '@/components/ImageUploadPreview.vue';
import InputDescription from '@/components/InputDescription.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import MoneyInput from '@/components/ui/money-input/MoneyInput.vue';
import QuilTextEditor from '@/components/ui/quil-editor/QuilTextEditor.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import usePPOBQuery from '@/composables/query/usePPOBQuery';
import { useSwal } from '@/composables/useSwal';
import { PPOBCategoryDataItem } from '@/types/cms/ppob';
import { Form } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Save } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

defineProps<{
    categories: PPOBCategoryDataItem[];
}>();

const { toast } = useSwal();

const { fetchBrands } = usePPOBQuery();

// Selected category state
const selectedCategory = ref<number>();
const selectedBrand = ref<number | null>();

// Fetch brands
const {
    data: brandsData,
    isError: isBrandsError,
    isFetching: isBrandsFetching,
} = fetchBrands(
    computed(() =>
        selectedCategory.value ? Number(selectedCategory.value) : 0,
    ),
    5 * 60 * 1000,
);

// Description state
const description = ref<string>('');

// Reset selectedBrand when selectedCategory changes
watch(selectedCategory, () => {
    selectedBrand.value = null;
});
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <h2 class="text-lg font-medium">Create PPOB Product</h2>

            <p class="mt-1 text-sm text-muted-foreground">
                Create a new PPOB product by filling out the form below.
            </p>

            <Form
                v-bind="store.form()"
                class="mt-6 space-y-6"
                @success="
                    () => {
                        toast.fire({
                            icon: 'success',
                            title: 'PPOB Product created successfully.',
                        });
                        close();
                    }
                "
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-2">
                    <Label for="p_p_o_b_category_id">Category</Label>
                    <InputDescription>
                        Select the category for this PPOB product.
                    </InputDescription>
                    <Select
                        name="p_p_o_b_category_id"
                        v-model="selectedCategory"
                        :disabled="isBrandsFetching"
                    >
                        <SelectTrigger
                            id="p_p_o_b_category_id"
                            class="mt-1 w-full"
                        >
                            <SelectValue placeholder="Select a category" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="category in categories"
                                :key="category.id"
                                :value="String(category.id)"
                            >
                                {{ category.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.p_p_o_b_category_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="brand_id">Brand</Label>
                    <InputDescription>
                        Select the brand for this PPOB product.
                    </InputDescription>
                    <Select name="p_p_o_b_brand_id" v-model="selectedBrand">
                        <SelectTrigger
                            id="p_p_o_b_brand_id"
                            class="mt-1 w-full"
                        >
                            <SelectValue placeholder="Select a brand" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="brand in brandsData || []"
                                :key="brand.id"
                                :value="String(brand.id)"
                            >
                                {{ brand.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.p_p_o_b_brand_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <InputDescription>
                        The name of the PPOB product.
                    </InputDescription>
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
                    <Label for="sku">SKU</Label>
                    <InputDescription>
                        The Stock Keeping Unit identifier for the product.
                    </InputDescription>
                    <Input
                        id="sku"
                        name="sku"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError :message="errors.sku" />
                </div>

                <div class="grid gap-2">
                    <Label for="buy_price">Buy Price</Label>
                    <InputDescription>
                        The buying price of the PPOB product.
                    </InputDescription>
                    <MoneyInput
                        id="buy_price"
                        name="buy_price"
                        :default-value="1000"
                    />
                    <InputError :message="errors.buy_price" />
                </div>

                <div class="grid gap-2">
                    <Label for="sell_price">Sell Price</Label>
                    <InputDescription>
                        The selling price of the PPOB product.
                    </InputDescription>
                    <MoneyInput
                        id="sell_price"
                        name="sell_price"
                        :default-value="1200"
                    />
                    <InputError :message="errors.sell_price" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <InputDescription>
                        A brief description of the PPOB product.
                    </InputDescription>
                    <Input
                        id="description"
                        name="description"
                        type="hidden"
                        :value="description"
                    />
                    <QuilTextEditor
                        @update:content="
                            (value) => {
                                description = value;
                            }
                        "
                    />
                    <InputError :message="errors.description" />
                </div>

                <div class="grid gap-2">
                    <Label for="logo">Image</Label>
                    <InputDescription>
                        Upload the PPOB product image (Max 5MB).
                    </InputDescription>
                    <ImageUploadPreview
                        input-id="image"
                        input-name="image"
                        label=""
                        description="Upload your PPOB product image here."
                        accept="image/*"
                        :max-size="5"
                        preview-height="200px"
                        :errors="errors.image"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <InputDescription>
                        Select the status of the product.
                    </InputDescription>
                    <Select name="status" default-value="1">
                        <SelectTrigger id="status" class="mt-1 w-full">
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
                        <Save />
                        Save Changes
                    </Button>
                </div>
            </Form>
        </div>
    </Modal>
</template>
