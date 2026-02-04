<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { useSwal } from '@/composables/useSwal';
import { formatCurrency } from '@/lib/utils';
import { useForm } from '@inertiajs/vue3';
import { Copy, Upload } from 'lucide-vue-next';
import { ref } from 'vue';

interface Payment {
    driver: string;
    payment_type: string;
    channel: string;
    account_number: string;
    expired_at: string;
}

const props = defineProps<{
    payment: Payment;
    orderReference: string;
    totalAmount: number;
}>();

const { toast } = useSwal();
const fileInput = ref<HTMLInputElement | null>(null);

const uploadForm = useForm({
    proof: null as File | null,
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
        uploadForm.proof = target.files[0];
    }
};

const uploadProof = () => {
    if (!uploadForm.proof) {
        toast.fire({
            icon: 'error',
            title: 'Pilih file terlebih dahulu',
        });
        return;
    }

    // TODO: Implement upload endpoint
    toast.fire({
        icon: 'info',
        title: 'Upload functionality coming soon',
    });
};
</script>

<template>
    <div class="rounded-lg border border-border/50 bg-card p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-bold text-foreground">
            Instruksi Pembayaran
        </h3>

        <!-- Midtrans Bank Transfer -->
        <div
            v-if="payment.driver === 'midtrans' && payment.payment_type === 'bank_transfer'"
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

            <div class="text-xs text-muted-foreground">
                <p class="mb-1">⏰ Bayar sebelum:</p>
                <p class="font-medium">
                    {{ new Date(payment.expired_at).toLocaleString('id-ID') }}
                </p>
            </div>
        </div>

        <!-- Midtrans QRIS -->
        <div
            v-else-if="payment.driver === 'midtrans' && payment.payment_type === 'qris'"
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

            <div class="text-xs text-muted-foreground">
                <p class="mb-1">⏰ Bayar sebelum:</p>
                <p class="font-medium">
                    {{ new Date(payment.expired_at).toLocaleString('id-ID') }}
                </p>
            </div>

            <div class="rounded-lg bg-blue-500/10 p-3 text-xs text-blue-600">
                <p class="font-medium">Cara Bayar:</p>
                <ol class="ml-4 mt-2 list-decimal space-y-1">
                    <li>Buka aplikasi e-wallet atau mobile banking</li>
                    <li>Pilih menu Scan QR</li>
                    <li>Scan QR Code di atas</li>
                    <li>Konfirmasi pembayaran</li>
                </ol>
            </div>
        </div>

        <!-- Manual Payment -->
        <div v-else-if="payment.driver === 'manual'" class="space-y-4">
            <div class="rounded-lg bg-muted/50 p-4">
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
                            PT GameStore Indonesia
                        </p>
                    </div>
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
                        {{ uploadForm.proof ? uploadForm.proof.name : 'Pilih File' }}
                    </Button>
                    <Button
                        :disabled="!uploadForm.proof"
                        @click="uploadProof"
                    >
                        Upload
                    </Button>
                </div>
                <p class="text-xs text-muted-foreground">
                    Format: JPG, PNG. Max 2MB
                </p>
            </div>

            <div class="rounded-lg bg-yellow-500/10 p-3 text-xs text-yellow-600">
                <p class="font-medium">⚠️ Penting:</p>
                <p class="mt-1">
                    Setelah transfer, upload bukti transfer untuk mempercepat
                    proses verifikasi.
                </p>
            </div>
        </div>
    </div>
</template>
