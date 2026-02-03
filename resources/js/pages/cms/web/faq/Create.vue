<script setup lang="ts">
import { store } from '@/actions/App/Http/Controllers/Cms/Web/FaqController';
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
import { Form } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Save } from 'lucide-vue-next';
import { ref } from 'vue';

const { toast } = useSwal();

// Question & Answer state
const question = ref<string>('');
const answer = ref<string>('');
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <h2 class="text-lg font-medium">Create Faq</h2>

            <p class="mt-1 text-sm text-muted-foreground">
                Create a new Faq by filling out the form below.
            </p>

            <Form
                v-bind="store.form()"
                class="mt-6 space-y-6"
                @success="
                    () => {
                        toast.fire({
                            icon: 'success',
                            title: 'Faq created successfully.',
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
                    />
                    <QuilTextEditor
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
                    />
                    <QuilTextEditor
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
                    />
                    <InputError :message="errors.order" />
                </div>

                <div class="grid gap-2">
                    <Label for="status">Status</Label>
                    <InputDescription>
                        Select the status of the faq.
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
