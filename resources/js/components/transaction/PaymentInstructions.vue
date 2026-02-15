<script setup lang="ts">
import { update } from '@/actions/App/Http/Controllers/Main/TransactionController';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useSwal } from '@/composables/useSwal';
import { formatCurrency } from '@/lib/utils';
import { PaymentDataItem } from '@/types/cms/main';
import { useForm, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { Clock, Copy, Upload } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    payment: PaymentDataItem;
    orderReference: string;
    totalAmount: number;
}>();

const page = usePage();
const setting = page.props.setting;
const { toast } = useSwal();
const fileInput = ref<HTMLInputElement | null>(null);

const uploadForm = useForm({
    image: null as File | null,
});

const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    toast.fire({
        icon: 'success',
        title: 'Berhasil disalin!',
    });
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        uploadForm.image = target.files[0];
    }
};

const uploadProof = () => {
    if (!uploadForm.image) {
        toast.fire({
            icon: 'error',
            title: 'Pilih file terlebih dahulu',
        });
        return;
    }

    uploadForm.submit(
        update({
            reference: props.orderReference,
        }),
        {
            method: 'put',
            preserveScroll: true,
            onSuccess: () => {
                toast.fire({
                    icon: 'success',
                    title: 'Bukti transfer berhasil diupload!',
                });
            },
            onError: () => {
                toast.fire({
                    icon: 'error',
                    title: 'Gagal mengupload bukti transfer',
                });
            },
        },
    );
};
</script>

<template>
    <div class="rounded-lg border border-border/50 bg-card p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-bold text-foreground">
            Instruksi Pembayaran
        </h3>

        <!-- Midtrans Bank Transfer -->
        <div
            v-if="
                payment.driver === 'midtrans' &&
                payment.payment_type === 'bank_transfer'
            "
            class="space-y-4"
        >
            <div class="rounded-lg bg-muted/50 p-4">
                <p class="mb-2 text-sm text-muted-foreground">
                    Transfer ke Virtual Account:
                </p>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-muted-foreground">
                            {{ payment.channel.toUpperCase() }}
                        </p>
                        <p class="text-2xl font-bold text-foreground">
                            {{ payment.account_number }}
                        </p>
                    </div>
                    <Button
                        variant="outline"
                        size="sm"
                        @click="copyToClipboard(payment.account_number)"
                    >
                        <Copy class="h-4 w-4" />
                    </Button>
                </div>
            </div>

            <div class="rounded-lg bg-muted/50 p-4">
                <p class="mb-2 text-sm text-muted-foreground">Total Bayar:</p>
                <p class="text-2xl font-bold text-primary">
                    {{ formatCurrency(totalAmount) }}
                </p>
            </div>

            <div
                class="flex items-center gap-4 rounded-xl border border-yellow-500/50 bg-yellow-500/10 p-5 text-yellow-600 shadow-[0_0_15px_rgba(234,179,8,0.1)]"
                v-if="payment?.paid_at === null"
            >
                <div
                    class="flex h-12 w-12 animate-pulse items-center justify-center rounded-full bg-yellow-500/20"
                >
                    <Clock class="h-6 w-6 text-yellow-600" />
                </div>

                <div>
                    <p
                        class="text-xs font-bold tracking-wider uppercase opacity-80"
                    >
                        Batas Waktu Pembayaran
                    </p>
                    <p class="text-xl font-black tabular-nums">
                        {{
                            dayjs(payment.expired_at)
                                .locale('id')
                                .format('DD MMM YYYY, HH:mm')
                        }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Midtrans QRIS -->
        <div
            v-else-if="
                payment.driver === 'midtrans' && payment.payment_type === 'qris'
            "
            class="space-y-4"
        >
            <div class="flex justify-center">
                <img
                    :src="payment.account_number"
                    alt="QR Code"
                    class="h-64 w-64 rounded-lg border border-border"
                />
            </div>

            <div class="rounded-lg bg-muted/50 p-4">
                <p class="mb-2 text-sm text-muted-foreground">Total Bayar:</p>
                <p class="text-2xl font-bold text-primary">
                    {{ formatCurrency(totalAmount) }}
                </p>
            </div>

            <div
                class="flex items-center gap-4 rounded-xl border border-yellow-500/50 bg-yellow-500/10 p-5 text-yellow-600 shadow-[0_0_15px_rgba(234,179,8,0.1)]"
                v-if="payment?.paid_at === null"
            >
                <div
                    class="flex h-12 w-12 animate-pulse items-center justify-center rounded-full bg-yellow-500/20"
                >
                    <Clock class="h-6 w-6 text-yellow-600" />
                </div>

                <div>
                    <p
                        class="text-xs font-bold tracking-wider uppercase opacity-80"
                    >
                        Batas Waktu Pembayaran
                    </p>
                    <p class="text-xl font-black tabular-nums">
                        {{
                            dayjs(payment.expired_at)
                                .locale('id')
                                .format('DD MMM YYYY, HH:mm')
                        }}
                    </p>
                </div>
            </div>

            <div class="rounded-lg bg-blue-500/10 p-3 text-xs text-blue-600">
                <p class="font-medium">Cara Bayar:</p>
                <ol class="mt-2 ml-4 list-decimal space-y-1">
                    <li>Buka aplikasi e-wallet atau mobile banking</li>
                    <li>Pilih menu Scan QR</li>
                    <li>Scan QR Code di atas</li>
                    <li>Konfirmasi pembayaran</li>
                </ol>
            </div>
        </div>

        <!-- Manual Payment -->
        <div v-else-if="payment.driver === 'manual'" class="space-y-4">
            <div
                class="rounded-lg bg-muted/50 p-4"
                v-if="setting.manual_transfer_type === 'rekening'"
            >
                <p class="mb-2 text-sm text-muted-foreground">
                    Transfer ke Rekening:
                </p>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-muted-foreground">Bank:</p>
                        <p class="text-lg font-bold text-foreground">
                            {{ payment.channel.toUpperCase() }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-muted-foreground">
                                Nomor Rekening:
                            </p>
                            <p class="text-xl font-bold text-foreground">
                                {{ payment.account_number }}
                            </p>
                        </div>
                        <Button
                            variant="outline"
                            size="sm"
                            @click="copyToClipboard(payment.account_number)"
                        >
                            <Copy class="h-4 w-4" />
                        </Button>
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">Atas Nama:</p>
                        <p class="font-medium text-foreground">
                            {{ payment.account_code }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="rounded-lg bg-muted/50 p-4"
                v-else-if="setting.manual_transfer_type === 'qris'"
            >
                <p class="mb-2 text-sm text-muted-foreground">
                    Scan QR Code untuk Bayar:
                </p>
                <div>
                    <p class="text-xs text-muted-foreground">Atas Nama:</p>
                    <p class="font-medium text-foreground">
                        {{ payment.account_code }}
                    </p>
                </div>
                <div class="flex justify-center">
                    <img
                        :src="setting.manual_transfer_bank_logo"
                        alt="QR Code"
                        class="h-70 w-64 rounded-lg border border-border"
                    />
                </div>
            </div>

            <div class="rounded-lg bg-muted/50 p-4">
                <p class="mb-2 text-sm text-muted-foreground">Total Bayar:</p>
                <p class="text-2xl font-bold text-primary">
                    {{ formatCurrency(totalAmount) }}
                </p>
            </div>

            <!-- Upload Proof -->
            <div class="space-y-2">
                <Label>Upload Bukti Transfer</Label>
                <div class="flex gap-2">
                    <input
                        ref="fileInput"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handleFileChange"
                    />
                    <Button
                        variant="outline"
                        class="flex-1"
                        @click="fileInput?.click()"
                    >
                        <Upload class="mr-2 h-4 w-4" />
                        {{
                            uploadForm.image
                                ? uploadForm.image.name
                                : 'Pilih File'
                        }}
                    </Button>
                    <Button :disabled="!uploadForm.image" @click="uploadProof">
                        Upload
                    </Button>
                </div>
                <p class="text-xs text-muted-foreground">
                    Format: JPG, PNG. Max 2MB
                </p>
            </div>

            <div v-if="payment.image" class="space-y-1">
                <p class="text-sm text-muted-foreground">
                    Bukti transfer yang sudah diupload:
                </p>
                <img
                    :src="payment.image"
                    alt="Bukti Transfer"
                    class="h-48 w-full rounded-lg border border-border object-cover"
                />
            </div>

            <div
                class="rounded-lg bg-yellow-500/10 p-3 text-xs text-yellow-600"
            >
                <p class="font-medium">⚠️ Penting:</p>
                <p class="mt-1">
                    Setelah transfer, upload bukti transfer untuk mempercepat
                    proses verifikasi.
                </p>
            </div>
        </div>
    </div>
</template>
