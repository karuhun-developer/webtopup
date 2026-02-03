<script setup lang="ts">
import { update } from '@/actions/App/Http/Controllers/Cms/PPOB/PPOBBrandController';
import ImageUploadPreview from '@/components/ImageUploadPreview.vue';
import InputDescription from '@/components/InputDescription.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import QuilTextEditor from '@/components/ui/quil-editor/QuilTextEditor.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useSwal } from '@/composables/useSwal';
import { PPOBBrandDataItem, PPOBCategoryDataItem } from '@/types/cms/ppob';
import { Form } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Save } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    categories: PPOBCategoryDataItem[];
    brand: PPOBBrandDataItem;
}>();

const { toast } = useSwal();

// Description state
const description = ref<string>(props.brand.description || '');
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <h2 class="text-lg font-medium">Edit PPOB Brand</h2>

            <p class="mt-1 text-sm text-muted-foreground">
                Edit the PPOB Brand details by modifying the form below.
            </p>

            <Form
                v-bind="update.form({ brand: brand.slug })"
                class="mt-6 space-y-6"
                @success="
                    () => {
                        toast.fire({
                            icon: 'success',
                            title: 'PPOB Brand updated successfully.',
                        });
                        close();
                    }
                "
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-2">
                    <Label for="p_p_o_b_category_id">Category</Label>
                    <InputDescription>
                        Select the category for this PPOB brand.
                    </InputDescription>
                    <Select
                        name="p_p_o_b_category_id"
                        :default-value="String(brand.p_p_o_b_category_id)"
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
                    <Label for="name">Name</Label>
                    <InputDescription>
                        The name of the PPOB brand.
                    </InputDescription>
                    <Input
                        id="name"
                        name="name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        :default-value="brand.name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <InputDescription>
                        A brief description of the PPOB brand.
                    </InputDescription>
                    <Input
                        id="description"
                        name="description"
                        type="hidden"
                        :value="description"
                    />
                    <QuilTextEditor
                        :content="description"
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
                        Upload the PPOB brand image (Max 5MB).
                    </InputDescription>
                    <ImageUploadPreview
                        input-id="image"
                        input-name="image"
                        label=""
                        description="Upload your PPOB brand image here."
                        accept="image/*"
                        :max-size="5"
                        preview-height="200px"
                        :errors="errors.image"
                        :initial-preview="brand.image"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <InputDescription>
                        Select the status of the brand.
                    </InputDescription>
                    <Select name="status" :default-value="String(brand.status)">
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
                        <Save class="mr-2 h-4 w-4" />
                        Save Changes
                    </Button>
                </div>
            </Form>
        </div>
    </Modal>
</template>
