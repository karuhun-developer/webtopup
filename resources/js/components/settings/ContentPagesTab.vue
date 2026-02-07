<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import QuilTextEditor from '@/components/ui/quil-editor/QuilTextEditor.vue';
import { type SettingValue } from '@/types/cms/setting';
import { ref, watch } from 'vue';

const props = defineProps<{
    setting?: SettingValue;
    errors: Record<string, string>;
}>();

// Reactive content for Quill editors
const privacyPolicyContent = ref(props.setting?.privacy_policy || '');
const termsContent = ref(props.setting?.terms || '');

// Watch for prop changes
watch(
    () => props.setting?.privacy_policy,
    (newVal) => {
        if (newVal) privacyPolicyContent.value = newVal;
    },
);

watch(
    () => props.setting?.terms,
    (newVal) => {
        if (newVal) termsContent.value = newVal;
    },
);
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="privacy_policy">Privacy Policy</Label>
                <QuilTextEditor
                    :content="privacyPolicyContent"
                    @update:content="privacyPolicyContent = $event"
                />
                <input
                    type="hidden"
                    name="value[privacy_policy]"
                    :value="privacyPolicyContent"
                />
                <InputError :message="errors['value.privacy_policy']" />
            </div>

            <div class="grid gap-2">
                <Label for="terms">Terms & Conditions</Label>
                <QuilTextEditor
                    :content="termsContent"
                    @update:content="termsContent = $event"
                />
                <input
                    type="hidden"
                    name="value[terms]"
                    :value="termsContent"
                />
                <InputError :message="errors['value.terms']" />
            </div>
        </div>
    </div>
</template>
