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
import QuilTextEditor from '@/components/ui/quil-editor/QuilTextEditor.vue';
import { type SettingValue } from '@/types/cms/setting';
import { ref, watch } from 'vue';

const props = defineProps<{
    setting?: SettingValue;
    errors: Record<string, string>;
}>();

const maintenanceDescriptionContent = ref(props.setting?.maintenance_description || '');

watch(
    () => props.setting?.maintenance_description,
    (newVal) => {
        if (newVal) maintenanceDescriptionContent.value = newVal;
    },
);
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div class="grid gap-2">
                <Label for="maintenance_status">Maintenance Status</Label>
                <Select
                    name="value[maintenance_status]"
                    :default-value="setting?.maintenance_status || 'inactive'"
                >
                    <SelectTrigger
                        id="maintenance_status"
                        class="mt-1 w-full"
                    >
                        <SelectValue placeholder="Select maintenance status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="active">Active</SelectItem>
                        <SelectItem value="inactive">Inactive</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="errors['value.maintenance_status']" />
            </div>

            <div class="grid gap-2">
                <Label for="maintenance_title">Maintenance Title</Label>
                <Input
                    id="maintenance_title"
                    name="value[maintenance_title]"
                    :default-value="setting?.maintenance_title"
                    placeholder="e.g., Under Maintenance"
                />
                <InputError :message="errors['value.maintenance_title']" />
            </div>
        </div>

        <div class="grid gap-2">
            <Label for="maintenance_description">Maintenance Description</Label>
            <QuilTextEditor
                :content="maintenanceDescriptionContent"
                @update:content="maintenanceDescriptionContent = $event"
            />
            <input
                type="hidden"
                name="value[maintenance_description]"
                :value="maintenanceDescriptionContent"
            />
            <InputError :message="errors['value.maintenance_description']" />
        </div>

        <ImageUploadPreview
            input-id="maintenance_image"
            input-name="value[maintenance_image]"
            label="Maintenance Image"
            description="Upload maintenance image (PNG, JPG, or SVG max 2MB)"
            :max-size="2"
            preview-height="150px"
            :initial-preview="setting?.maintenance_image || ''"
            :errors="errors['value.maintenance_image']"
        />
    </div>
</template>
