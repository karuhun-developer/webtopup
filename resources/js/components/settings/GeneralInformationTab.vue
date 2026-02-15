<script setup lang="ts">
import ImageUploadPreview from '@/components/ImageUploadPreview.vue';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
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
        <div class="grid gap-6 md:grid-cols-2">
            <div class="grid gap-2">
                <Label for="title">Website Title</Label>
                <Input
                    id="title"
                    name="value[title]"
                    :default-value="setting?.title"
                    placeholder="Enter website title"
                />
                <InputError :message="errors['value.title']" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email Address</Label>
                <Input
                    id="email"
                    type="email"
                    name="value[email]"
                    :default-value="setting?.email"
                    placeholder="contact@example.com"
                />
                <InputError :message="errors['value.email']" />
            </div>

            <div class="grid gap-2">
                <Label for="phone">Phone Number</Label>
                <Input
                    id="phone"
                    name="value[phone]"
                    :default-value="setting?.phone"
                    placeholder="+62 xxx xxxx xxxx"
                />
                <InputError :message="errors['value.phone']" />
            </div>

            <div class="grid gap-2">
                <Label for="cs">Customer Service</Label>
                <Input
                    id="cs"
                    name="value[cs]"
                    :default-value="setting?.cs"
                    placeholder="WhatsApp number or contact"
                />
                <InputError :message="errors['value.cs']" />
            </div>
        </div>

        <div class="grid gap-2">
            <Label for="footer_description">Footer Description</Label>
            <Textarea
                id="footer_description"
                name="value[footer_description]"
                :default-value="setting?.footer_description"
                placeholder="Enter footer description"
                rows="3"
            />
            <InputError :message="errors['value.footer_description']" />
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            <ImageUploadPreview
                input-id="logo"
                input-name="value[logo]"
                label="Website Logo"
                description="Upload website logo"
                :max-size="2"
                preview-height="100px"
                :initial-preview="setting?.logo || ''"
                :errors="errors['value.logo']"
            />

            <ImageUploadPreview
                input-id="icon"
                input-name="value[icon]"
                label="App Icon"
                description="Upload app icon (square)"
                :max-size="2"
                preview-height="100px"
                :initial-preview="setting?.icon || ''"
                :errors="errors['value.icon']"
            />

            <ImageUploadPreview
                input-id="favicon"
                input-name="value[favicon]"
                label="Favicon"
                description="Upload favicon"
                :max-size="1"
                preview-height="100px"
                :initial-preview="setting?.favicon || ''"
                :errors="errors['value.favicon']"
            />
        </div>
    </div>
</template>
