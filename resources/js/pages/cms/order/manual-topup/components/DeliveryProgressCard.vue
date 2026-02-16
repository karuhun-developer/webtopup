<script setup lang="ts">
import {
    notify,
    save,
} from '@/actions/App/Http/Controllers/Cms/Order/ManualTopupOrderController';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { useSwal } from '@/composables/useSwal';
import { OrderDataItem } from '@/types/cms/main';
import { router, useForm } from '@inertiajs/vue3';
import {
    AlertTriangle,
    CheckCircle2,
    Gift,
    Send,
    Upload,
} from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    order: OrderDataItem;
}>();

const { toast } = useSwal();

const giftSendForm = useForm({
    gift_send: props.order.submited?.gift_send || false,
    gift_send_proof: null as File | null,
});

const giftProofPreview = ref<string | null>(null);

const handleGiftProofUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        giftSendForm.gift_send_proof = file;
        giftProofPreview.value = URL.createObjectURL(file);
    }

    giftSendForm.put(
        save({
            order: props.order.reference,
        }).url,
        {
            preserveScroll: true,
        },
    );
};

const toggleGiftSend = () => {
    giftSendForm.gift_send = !giftSendForm.gift_send;
    toast.fire({
        icon: 'success',
        title: giftSendForm.gift_send
            ? 'Gift delivery confirmed!'
            : 'Gift delivery unchecked',
    });

    giftSendForm.put(
        save({
            order: props.order.reference,
        }).url,
        {
            preserveScroll: true,
        },
    );
};

const sendGiftCompletionNotification = () => {
    router.put(
        notify({
            order: props.order.reference,
        }).url,
        {
            action: 'gift_send',
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.fire({
                    icon: 'success',
                    title: 'Gift completion notification sent!',
                });
            },
        },
    );
};
</script>

<template>
    <Card class="p-6">
        <h2 class="mb-6 flex items-center text-lg font-bold">
            <Gift class="mr-2 h-5 w-5" />
            Delivery Progress
        </h2>

        <div class="space-y-6">
            <!-- Delivery Proof -->
            <div class="rounded-lg border p-4">
                <Label class="mb-2 block text-sm font-semibold"
                    >Delivery Proof</Label
                >
                <input
                    type="file"
                    accept="image/*,video/*"
                    @change="handleGiftProofUpload"
                    class="hidden"
                    id="gift-proof-upload"
                />
                <label
                    for="gift-proof-upload"
                    class="flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed p-4 hover:border-primary"
                >
                    <Upload class="mr-2 h-4 w-4 text-muted-foreground" />
                    <span class="text-sm">
                        {{
                            giftProofPreview
                                ? 'Change File'
                                : 'Upload Image/Video'
                        }}
                    </span>
                </label>
                <img
                    v-if="giftProofPreview"
                    :src="giftProofPreview"
                    class="mt-2 w-full rounded-lg"
                />
                <img
                    v-else-if="order.submited?.gift_send_proof"
                    :src="order.submited?.gift_send_proof"
                    class="mt-2 w-full rounded-lg"
                />
            </div>

            <Separator />

            <!-- Send Gift -->
            <div
                class="rounded-lg border p-4"
                :class="
                    giftSendForm.gift_send
                        ? 'border-green-500/50 bg-green-500/10'
                        : 'border-border'
                "
            >
                <div class="mb-3 flex items-center gap-2">
                    <Gift class="h-5 w-5 text-muted-foreground" />
                    <span class="font-semibold">Send Gift</span>
                </div>
                <Button
                    @click="toggleGiftSend"
                    :variant="giftSendForm.gift_send ? 'default' : 'outline'"
                    class="w-full"
                >
                    {{
                        giftSendForm.gift_send
                            ? 'Gift Sent'
                            : 'Confirm Gift Sent'
                    }}
                </Button>

                <!-- Notify Gift Completion Button -->
                <Button
                    v-if="giftSendForm.gift_send"
                    @click="sendGiftCompletionNotification"
                    variant="outline"
                    size="sm"
                    class="mt-2 w-full"
                >
                    <Send class="mr-2 h-4 w-4" />
                    Notify: Gift Completion
                </Button>
            </div>

            <!-- Status Indicators -->
            <div class="space-y-3">
                <div
                    v-if="order.submited?.dispute"
                    class="rounded-lg border border-yellow-500/50 bg-yellow-500/10 p-4"
                >
                    <div class="flex items-center gap-2">
                        <AlertTriangle class="h-5 w-5 text-yellow-500" />
                        <span class="font-semibold text-yellow-500"
                            >Dispute Active</span
                        >
                    </div>
                </div>

                <div
                    v-if="order.submited?.done"
                    class="rounded-lg border border-green-500/50 bg-green-500/10 p-4"
                >
                    <div class="flex items-center gap-2">
                        <CheckCircle2 class="h-5 w-5 text-green-500" />
                        <span class="font-semibold text-green-500"
                            >Order Completed</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>
