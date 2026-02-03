<script setup lang="ts">
import { update } from '@/actions/App/Http/Controllers/Cms/Web/FaqController';
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
import { FaqDataItem } from '@/types/cms/web';
import { Form } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Save } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    faq: FaqDataItem;
}>();

const { toast } = useSwal();

// Question & Answer state
const question = ref<string>(props.faq.question);
const answer = ref<string>(props.faq.answer);
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <h2 class="text-lg font-medium">Edit Faq</h2>

            <p class="mt-1 text-sm text-muted-foreground">
                Edit the Faq details by modifying the form below.
            </p>

            <Form
                v-bind="update.form({ faq: faq.id })"
                class="mt-6 space-y-6"
                @success="
                    () => {
                        toast.fire({
                            icon: 'success',
                            title: 'Faq updated successfully.',
                        });
                        close();
                    }
                "
                v-slot="{ errors, processing }"
            >
                <div class="grid gap-2">
                    <Label for="question">Question</Label>
                    <InputDescription>
                        The question of the Faq.
                    </InputDescription>
                    <Input
                        id="question"
                        name="question"
                        type="hidden"
                        :value="question"
                        s
                        :default-value="faq.question"
                    />
                    <QuilTextEditor
                        :content="question"
                        @update:content="
                            (value) => {
                                question = value;
                            }
                        "
                    />
                    <InputError :message="errors.question" />
                </div>

                <div class="grid gap-2">
                    <Label for="answer">Answer</Label>
                    <InputDescription>
                        The answer of the Faq.
                    </InputDescription>
                    <Input
                        id="answer"
                        name="answer"
                        type="hidden"
                        :value="answer"
                        :default-value="faq.answer"
                    />
                    <QuilTextEditor
                        :content="answer"
                        @update:content="
                            (value) => {
                                answer = value;
                            }
                        "
                    />
                    <InputError :message="errors.answer" />
                </div>

                <div class="grid gap-2">
                    <Label for="order">Order</Label>
                    <InputDescription> The order of the Faq. </InputDescription>
                    <Input
                        id="order"
                        name="order"
                        type="number"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        :default-value="faq.order"
                    />
                    <InputError :message="errors.order" />
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <InputDescription>
                        Select the status of the faq.
                    </InputDescription>
                    <Select name="status" :default-value="String(faq.status)">
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
