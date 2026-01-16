<script setup lang="ts">
import { ref, watch } from 'vue';

interface Props {
    inputId: string;
    inputName: string;
    label: string;
    description?: string;
    accept?: string;
    maxSize?: number; // in MB
    previewHeight?: string;
    errors?: string;
    initialPreview?: string;
}

const props = withDefaults(defineProps<Props>(), {
    accept: 'image/*',
    maxSize: 5,
    previewHeight: '300px',
    description: '',
    errors: '',
    initialPreview: '',
});

const fileInput = ref<HTMLInputElement | null>(null);
const preview = ref<string>(props.initialPreview);
const fileName = ref<string>('');
const isDragging = ref(false);

function handleFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        validateAndPreview(file);
    }
}

function validateAndPreview(file: File) {
    // Check file type
    if (!file.type.startsWith('image/')) {
        alert('Please select a valid image file');
        return;
    }

    // Check file size
    const maxBytes = props.maxSize * 1024 * 1024;
    if (file.size > maxBytes) {
        alert(`File size must be less than ${props.maxSize}MB`);
        return;
    }

    fileName.value = file.name;

    // Create preview
    const reader = new FileReader();
    reader.onload = (e) => {
        preview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
}

function handleDragOver(event: DragEvent) {
    event.preventDefault();
    isDragging.value = true;
}

function handleDragLeave() {
    isDragging.value = false;
}

function handleDrop(event: DragEvent) {
    event.preventDefault();
    isDragging.value = false;

    const file = event.dataTransfer?.files?.[0];
    if (file) {
        // Set the file to the input
        if (fileInput.value) {
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.value.files = dataTransfer.files;

            // Trigger change event
            const event = new Event('change', { bubbles: true });
            fileInput.value.dispatchEvent(event);
        }
    }
}

function triggerFileInput() {
    fileInput.value?.click();
}

function clearPreview() {
    preview.value = '';
    fileName.value = '';
    if (fileInput.value) {
        fileInput.value.value = '';
    }
}

watch(
    () => props.initialPreview,
    (newValue) => {
        if (newValue) {
            preview.value = newValue;
        }
    },
);
</script>

<template>
    <div class="grid gap-2">
        <label class="text-sm font-medium">{{ label }}</label>

        <p v-if="description" class="text-xs text-muted-foreground">
            {{ description }}
        </p>

        <!-- Preview Section -->
        <div
            v-if="preview"
            class="relative overflow-hidden rounded border border-border bg-muted"
        >
            <img
                :src="preview"
                :alt="label"
                class="w-full object-cover"
                :style="{ maxHeight: previewHeight }"
            />
            <div
                class="absolute inset-0 flex items-center justify-center gap-2 bg-black/0 transition-colors hover:bg-black/20"
            >
                <button
                    type="button"
                    @click="triggerFileInput"
                    class="rounded bg-primary px-3 py-1 text-sm text-primary-foreground transition-all hover:bg-primary/90"
                >
                    Change
                </button>
                <button
                    type="button"
                    @click="clearPreview"
                    class="rounded bg-destructive px-3 py-1 text-sm text-destructive-foreground transition-all hover:bg-destructive/90"
                >
                    Remove
                </button>
            </div>
            <div
                class="absolute right-2 bottom-2 left-2 truncate rounded bg-black/70 px-2 py-1 text-xs text-white"
            >
                {{ fileName }}
            </div>
        </div>

        <!-- Upload Area -->
        <div
            v-else
            :class="[
                'relative cursor-pointer rounded border-2 border-dashed p-8 text-center transition-colors',
                isDragging
                    ? 'border-primary bg-primary/5'
                    : 'border-muted-foreground/30 hover:border-primary/50',
            ]"
            @click="triggerFileInput"
            @dragover="handleDragOver"
            @dragleave="handleDragLeave"
            @drop="handleDrop"
        >
            <svg
                class="mx-auto h-12 w-12 text-muted-foreground"
                stroke="currentColor"
                fill="none"
                viewBox="0 0 48 48"
            >
                <path
                    d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20m-6-2l-3.172-3.172a2 2 0 00-2.828 0L28 12m0 0l-3.172-3.172a2 2 0 00-2.828 0L16 12"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
            <p class="mt-2 text-sm font-medium text-foreground">
                Drag and drop your image here, or click to select
            </p>
            <p class="mt-1 text-xs text-muted-foreground">
                Supports: {{ accept }} • Max size: {{ maxSize }}MB
            </p>
        </div>

        <!-- Hidden File Input -->
        <input
            ref="fileInput"
            :id="inputId"
            :name="inputName"
            type="file"
            :accept="accept"
            class="hidden"
            @change="handleFileChange"
        />

        <!-- Error Message -->
        <div v-if="errors" class="text-sm text-destructive">
            {{ errors }}
        </div>
    </div>
</template>
