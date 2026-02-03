<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/Cms/Web/SliderController';
import ImageUploadPreview from '@/components/ImageUploadPreview.vue';
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
import { Form } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Save } from 'lucide-vue-next';

const { toast } = useSwal();
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <h2 class="text-lg font-medium">Create Slider</h2>

            <p class="mt-1 text-sm text-muted-foreground">
                Create a new Slider by filling out the form below.
            </p>

            <Form
                v-bind="store.form()"
                class="mt-6 space-y-6"
                @success="
                    () => {
                        toast.fire({
                            icon: 'success',
                            title: 'Slider created successfully.',
                        });
                        close();
                    }
                "
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-2">
                    <Label for="title">Title</Label>
                    <InputDescription>
                        The title of the Slider.
                    </InputDescription>
                    <Input
                        id="title"
                        name="title"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                    />
                    <InputError :message="errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="order">Order</Label>
                    <InputDescription>
                        The order of the Slider.
                    </InputDescription>
                    <Input
                        id="order"
                        name="order"
                        type="number"
                        class="mt-1 block w-full"
                        required
                        autofocus
                    />
                    <InputError :message="errors.order" />
                </div>

                <div class="grid gap-2">
                    <Label for="link">Link</Label>
                    <InputDescription>
                        The link of the Slider.
                    </InputDescription>
                    <Input
                        id="link"
                        name="link"
                        type="text"
                        class="mt-1 block w-full"
                        autofocus
                    />
                    <InputError :message="errors.link" />
                </div>

                <div class="grid gap-2">
                    <Label for="logo">Image</Label>
                    <InputDescription>
                        Upload the Slider image (Max 5MB).
                    </InputDescription>
                    <ImageUploadPreview
                        input-id="image"
                        input-name="image"
                        label=""
                        description="Upload your Slider image here."
                        accept="image/*"
                        :max-size="5"
                        preview-height="200px"
                        :errors="errors.image"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <InputDescription>
                        Select the status of the slider.
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
