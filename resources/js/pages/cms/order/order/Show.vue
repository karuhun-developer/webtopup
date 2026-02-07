<script setup lang="ts">
import { validatePayment } from '@/actions/App/Http/Controllers/Cms/Order/OrderController';
import StatusBadge from '@/components/transaction/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { useSwal } from '@/composables/useSwal';
import { formatCurrency } from '@/lib/utils';
import { OrderDataItem } from '@/types/cms/main';
import { useForm } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import dayjs from 'dayjs';
import { CheckCircle, Copy, XCircle } from 'lucide-vue-next';

const props = defineProps<{
    order: OrderDataItem;
}>();

const { toast, confirm } = useSwal();

const validationForm = useForm({
    status: 0,
});

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    toast.fire({
        icon: 'success',
        title: 'Berhasil disalin!',
    });
};

const approvePayment = async (close: () => void) => {
    const result = await confirm({
        title: 'Approve Payment?',
        text: 'Pesanan akan diproses setelah payment disetujui',
        icon: 'question',
        confirmButtonText: 'Ya, Approve',
        cancelButtonText: 'Batal',
    });

    if (result.isConfirmed) {
        validationForm.status = 2; // SETTLEMENT
        validationForm.submit(
            validatePayment({
                order: props.order.reference,
            }),
            {
                method: 'put',
                onSuccess: () => {
                    toast.fire({
                        icon: 'success',
                        title: 'Payment berhasil diapprove!',
                    });
                    close();
                },
            },
        );
    }
};

const rejectPayment = async (close: () => void) => {
    const result = await confirm({
        title: 'Reject Payment?',
        text: 'Yakin ingin menolak pembayaran ini?',
        icon: 'warning',
        confirmButtonText: 'Ya, Reject',
        cancelButtonText: 'Batal',
    });

    if (result.isConfirmed) {
        validationForm.status = -1; // DENY
        validationForm.submit(
            validatePayment({
                order: props.order.reference,
            }),
            {
                method: 'put',
                onSuccess: () => {
                    toast.fire({
                        icon: 'success',
                        title: 'Payment berhasil ditolak',
                    });
                    close();
                },
            },
        );
    }
};
</script>

<template>
    <Modal v-slot="{ close }">
        <div class="p-6">
            <!-- Header -->
            <div class="mb-6 border-b pb-4">
                <h2 class="text-xl font-bold">Order {{ order.reference }}</h2>
                <p class="text-sm text-muted-foreground">
                    {{ dayjs(order.created_at).format('DD MMMM YYYY, HH:mm') }}
                </p>
            </div>

            <div class="space-y-6">
                <!-- Product & Customer Info -->
                <div class="rounded-lg border bg-muted/30 p-4">
                    <div class="mb-4 flex items-start gap-4">
                        <img
                            v-if="order?.product?.image"
                            :src="order.product.image"
                            :alt="order.product.name"
                            class="h-20 w-20 rounded-lg object-cover"
                        />
                        <div class="flex-1">
                            <p class="text-xs text-muted-foreground uppercase">
                                {{ order?.brand?.name }}
                            </p>
                            <h3 class="text-lg font-bold">
                                {{ order?.product?.name }}
                            </h3>
                            <p class="mt-1 text-xl font-bold text-primary">
                                {{ formatCurrency(order.total_amount) }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 border-t pt-4">
                        <div>
                            <p class="text-xs text-muted-foreground">
                                ID Akun
                                {{ order.submited.server_id ? '/ Server' : '' }}
                            </p>
                            <p class="font-mono font-medium">
                                {{ order.submited.account_id }}
                                {{
                                    order.submited.server_id
                                        ? `/ ${order.submited.server_id}`
                                        : ''
                                }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Customer
                            </p>
                            <p class="font-medium">{{ order.name }}</p>
                            <p class="text-sm text-muted-foreground">
                                {{ order.phone }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div v-if="order.payment" class="space-y-4">
                    <h3 class="font-semibold">Payment Details</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-lg border bg-card p-3">
                            <p class="mb-1 text-xs text-muted-foreground">
                                Metode
                            </p>
                            <p class="font-semibold uppercase">
                                {{ order.payment.driver }}
                                {{
                                    order.payment.payment_type
                                        ? `- ${order.payment.payment_type}`
                                        : ''
                                }}
                            </p>
                        </div>
                        <div class="rounded-lg border bg-card p-3">
                            <p class="mb-1 text-xs text-muted-foreground">
                                {{
                                    order.payment.driver === 'manual'
                                        ? 'Bank'
                                        : 'Channel'
                                }}
                            </p>
                            <p class="font-semibold uppercase">
                                {{ order.payment.channel }}
                            </p>
                        </div>
                    </div>

                    <div class="rounded-lg border bg-card p-3">
                        <!-- QRIS QR Code -->
                        <div
                            v-if="
                                order.payment.driver === 'midtrans' &&
                                order.payment.payment_type === 'qris'
                            "
                            class="flex justify-center"
                        >
                            <img
                                :src="order.payment.account_number"
                                alt="QR Code"
                                class="h-48 w-48 rounded-lg"
                            />
                        </div>

                        <!-- Regular Account Number -->
                        <div v-else class="flex items-center justify-between">
                            <div>
                                <p class="mb-1 text-xs text-muted-foreground">
                                    {{
                                        order.payment.driver === 'manual'
                                            ? 'Nomor Rekening'
                                            : 'Account Number'
                                    }}
                                </p>
                                <p class="font-mono text-lg font-bold">
                                    {{ order.payment.account_number }}
                                </p>
                                <p
                                    v-if="order.payment.account_code"
                                    class="mt-1 text-sm text-muted-foreground"
                                >
                                    a/n {{ order.payment.account_code }}
                                </p>
                            </div>
                            <Button
                                variant="outline"
                                size="icon"
                                @click="
                                    copyToClipboard(
                                        order.payment.account_number,
                                    )
                                "
                            >
                                <Copy class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>

                    <!-- Status & Expiry -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-lg border bg-card p-3">
                            <p class="mb-2 text-xs text-muted-foreground">
                                Status
                            </p>
                            <div class="flex gap-2">
                                <StatusBadge
                                    :status="order.payment_status"
                                    type="payment"
                                />
                                <StatusBadge
                                    :status="order.topup_status"
                                    type="topup"
                                />
                            </div>
                        </div>
                        <div
                            v-if="order.payment.expired_at"
                            class="rounded-lg border p-3"
                            :class="
                                order.payment.paid_at
                                    ? 'border-green-500/50 bg-green-500/10'
                                    : 'border-yellow-500/50 bg-yellow-500/10'
                            "
                        >
                            <p class="mb-1 text-xs font-bold uppercase">
                                {{
                                    order.payment.paid_at
                                        ? 'Dibayar'
                                        : 'Batas Waktu'
                                }}
                            </p>
                            <p class="font-mono text-sm font-bold">
                                {{
                                    dayjs(
                                        order.payment.paid_at ||
                                            order.payment.expired_at,
                                    )
                                        .locale('id')
                                        .format('DD MMM, HH:mm')
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- Payment Proof -->
                    <div
                        v-if="
                            order.payment.driver === 'manual' &&
                            order.payment.image
                        "
                        class="space-y-2"
                    >
                        <p class="text-sm font-medium">Bukti Transfer</p>
                        <img
                            :src="order.payment.image"
                            alt="Bukti Transfer"
                            class="w-full rounded-lg border object-cover"
                        />
                    </div>

                    <!-- Validation Buttons -->
                    <div
                        v-if="
                            !order.payment.paid_at && order.payment_status === 0
                        "
                        class="flex gap-3 border-t pt-4"
                    >
                        <Button
                            variant="default"
                            class="flex-1"
                            size="lg"
                            @click="approvePayment(close)"
                            :disabled="validationForm.processing"
                        >
                            <CheckCircle class="mr-2 h-5 w-5" />
                            Approve
                        </Button>
                        <Button
                            variant="destructive"
                            class="flex-1"
                            size="lg"
                            @click="rejectPayment(close)"
                            :disabled="validationForm.processing"
                        >
                            <XCircle class="mr-2 h-5 w-5" />
                            Reject
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>
