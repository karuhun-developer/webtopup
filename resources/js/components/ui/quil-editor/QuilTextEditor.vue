<script setup lang="ts">
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps<{
    content?: string
}>();

const emit = defineEmits<{
    'update:content': [value: string]
}>();
</script>
<template>
    <QuillEditor
        toolbar="minimal"
        theme="snow"
        content-type="html"
        :content="content"
        @update:content="emit('update:content', $event)"
    />
</template>
