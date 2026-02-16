<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/Cms/PPOB/PPOBBrandController';
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
import { PPOBCategoryDataItem } from '@/types/cms/ppob';
import { Form } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Plus, Save, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    categories: PPOBCategoryDataItem[];
}>();

const { toast } = useSwal();

// Description state
const description = ref<string>('');

// Settings type state
const settingsType = ref<string>('id');
// Server list state
const serverList = ref<string[]>([]);

// Add server function
const addServer = async () => {
    const result = prompt('Enter server name:');

    serverList.value.push(result ? result : 'New Server');
};

// Delete server function
const deleteServer = (index: number) => {
    serverList.value.splice(index, 1);
};
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <h2 class="text-lg font-medium">Create PPOB Brand</h2>

            <p class="mt-1 text-sm text-muted-foreground">
                Create a new PPOB brand by filling out the form below.
            </p>

            <Form
                v-bind="store.form()"
                class="mt-6 space-y-6"
                @success="
                    () => {
                        toast.fire({
                            icon: 'success',
                            title: 'PPOB Brand created successfully.',
                        });
                        close();
                    }
                "
                v-slot="{ errors, processing }"
            >
                <div class="grid grid-cols-1 gap-2 md:grid-cols-2 md:gap-6">
                    <div class="grid gap-2">
                        <Label for="p_p_o_b_category_id">Category</Label>
                        <InputDescription>
                            Select the category for this PPOB brand.
                        </InputDescription>
                        <Select name="p_p_o_b_category_id">
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
                        />
                        <InputError :message="errors.name" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="provider">Provider</Label>
                    <InputDescription>
                        Select the provider for this PPOB brand.
                    </InputDescription>
                    <Select name="provider" default-value="digiflazz">
                        <SelectTrigger id="provider" class="mt-1 w-full">
                            <SelectValue placeholder="Select a provider" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="digiflazz">
                                Digiflazz
                            </SelectItem>
                            <SelectItem value="gift"> Gift </SelectItem>
                            <SelectItem value="manual_topup">
                                Manual Topup
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.provider" />
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
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="banner">Banner</Label>
                    <InputDescription>
                        Upload the PPOB brand banner (Max 5MB).
                    </InputDescription>
                    <ImageUploadPreview
                        input-id="banner"
                        input-name="banner"
                        label=""
                        description="Upload your PPOB brand banner here."
                        accept="image/*"
                        :max-size="5"
                        preview-height="200px"
                        :errors="errors.banner"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="default_product_image"
                        >Default Product Image</Label
                    >
                    <InputDescription>
                        Upload the default product image for this PPOB brand
                        (Max 2MB).
                    </InputDescription>
                    <ImageUploadPreview
                        input-id="default_product_image"
                        input-name="default_product_image"
                        label=""
                        description="Upload your default product image here."
                        accept="image/*"
                        :max-size="2"
                        preview-height="200px"
                        :errors="errors.default_product_image"
                    />
                </div>

                <div class="grid grid-cols-1 gap-2 md:grid-cols-2 md:gap-6">
                    <div class="grid gap-2">
                        <Label for="featured">Featured</Label>
                        <InputDescription>
                            Is this brand featured?
                        </InputDescription>
                        <Select name="featured" default-value="0">
                            <SelectTrigger id="featured" class="mt-1 w-full">
                                <SelectValue
                                    placeholder="Is this brand featured?"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="1">Yes</SelectItem>
                                <SelectItem value="0">No</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.featured" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="order">Order</Label>
                        <InputDescription>
                            The order of the brand in listings.
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
                </div>

                <div class="grid gap-2">
                    <Label for="settings.type">Checkout Type</Label>
                    <InputDescription>
                        Select the type of PPOB brand.
                    </InputDescription>
                    <Select
                        name="settings[type]"
                        v-model="settingsType"
                        :default-value="settingsType"
                    >
                        <SelectTrigger id="settings.type" class="mt-1 w-full">
                            <SelectValue placeholder="Select a type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="id">ID</SelectItem>
                            <SelectItem value="id+server">ID+Server</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors['settings.type']" />
                </div>

                <div class="grid gap-2">
                    <Label for="settings.label_id">Label ID</Label>
                    <InputDescription>
                        The label ID associated with this PPOB brand.
                    </InputDescription>
                    <Input
                        id="settings.label_id"
                        name="settings[label_id]"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        default-value="ID"
                    />
                    <InputError :message="errors['settings.label_id']" />
                </div>

                <div class="grid gap-2" v-if="settingsType === 'id+server'">
                    <Label for="settings.label_server">Label Server</Label>
                    <InputDescription>
                        The label server associated with this PPOB brand.
                    </InputDescription>
                    <Input
                        id="settings.label_server"
                        name="settings[label_server]"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        default-value="Server"
                    />
                    <InputError :message="errors['settings.label_server']" />
                </div>

                <div class="grid gap-2" v-if="settingsType === 'id+server'">
                    <Label for="settings.server_list">Server List</Label>
                    <InputDescription>
                        Manage the list of servers for this PPOB brand.
                    </InputDescription>

                    <!-- Hidden input to submit server list -->
                    <Input
                        v-for="(server, index) in serverList"
                        :key="index"
                        name="settings[servers][]"
                        type="hidden"
                        :default-value="server"
                    />

                    <!-- Add Server Button -->
                    <div class="mb-2 flex justify-end">
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addServer"
                        >
                            <Plus class="mr-2 h-4 w-4" />
                            Add Server
                        </Button>
                    </div>

                    <!-- Server List Table -->
                    <div class="rounded-md border">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b bg-muted/50">
                                    <th
                                        class="px-4 py-3 text-left text-sm font-medium"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-sm font-medium"
                                    >
                                        Server Name
                                    </th>
                                    <th
                                        class="px-4 py-3 text-right text-sm font-medium"
                                    >
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-if="serverList.length === 0"
                                    class="border-b"
                                >
                                    <td
                                        colspan="3"
                                        class="px-4 py-8 text-center text-sm text-muted-foreground"
                                    >
                                        No servers added yet. Click "Add Server"
                                        to add one.
                                    </td>
                                </tr>
                                <tr
                                    v-for="(server, index) in serverList"
                                    :key="index"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="px-4 py-3 text-sm">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ server }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="sm"
                                            @click="deleteServer(index)"
                                        >
                                            <Trash2
                                                class="h-4 w-4 text-destructive"
                                            />
                                        </Button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <InputError :message="errors['settings.server_list']" />
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <InputDescription>
                        Select the status of the brand.
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
