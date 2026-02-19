<script setup lang="ts">
import { checkVoucher as checkVoucherRoute } from '@/actions/App/Http/Controllers/Main/TransactionController';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { formatCurrency } from '@/lib/utils';
import { PPOBProductDataItem } from '@/types/cms/ppob';
import axios from 'axios';
import { BadgeCheck, Loader2, TicketPercent } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import ContactCS from './ContactCS.vue';
import ReviewRating from './ReviewRating.vue';

interface ManualBank {
    id?: string;
    name?: string;
    account_number?: string;
    account_name?: string;
    img?: string;
}

interface PaymentMethod {
    id: string;
    name: string;
    fee: number;
    action: 'add' | 'multiply';
    img: string;
}

const props = defineProps<{
    brandName: string;
    selectedProduct: PPOBProductDataItem | null;
    selectedPayment: string | null;
    paymentType: 'manual' | 'automatic';
    totalAmount: number;
    manualBank: ManualBank;
    paymentMethods: PaymentMethod[];
    isLoading?: boolean;
}>();

const emit = defineEmits<{
    checkout: [];
    'voucher-applied': [code: string, discount: number];
    'voucher-removed': [];
}>();

const voucherCode = ref('');
const voucherError = ref<string | null>(null);
const discountAmount = ref(0);
const isCheckingVoucher = ref(false);
const appliedVoucherCode = ref<string | null>(null);

const checkVoucher = async () => {
    if (!voucherCode.value) return;

    if (!props.selectedProduct) {
        voucherError.value = 'Pilih produk terlebih dahulu';
        return;
    }

    isCheckingVoucher.value = true;
    voucherError.value = null;

    try {
        const response = await axios.post(checkVoucherRoute().url, {
            voucher_code: voucherCode.value,
            amount: props.selectedProduct.sell_price,
        });

        if (response.data.data.valid) {
            discountAmount.value = response.data.data.discount_amount;
            appliedVoucherCode.value = response.data.data.voucher_code;
            emit(
                'voucher-applied',
                response.data.data.voucher_code,
                response.data.data.discount_amount,
            );
        }
    } catch (error: any) {
        voucherError.value =
            error.response?.data?.message || 'Voucher tidak valid';
        discountAmount.value = 0;
        appliedVoucherCode.value = null;
        emit('voucher-removed');
    } finally {
        isCheckingVoucher.value = false;
    }
};

const removeVoucher = () => {
    voucherCode.value = '';
    discountAmount.value = 0;
    appliedVoucherCode.value = null;
    voucherError.value = null;
    emit('voucher-removed');
};

const getPaymentName = () => {
    if (!props.selectedPayment) return null;

    if (props.paymentType === 'manual') {
        return props.manualBank.name;
    } else {
        return props.paymentMethods.find((p) => p.id === props.selectedPayment)
            ?.name;
    }
};

// Reset voucher if product changes (optional, but safer due to min purchase checks)
watch(
    () => props.selectedProduct,
    () => {
        if (appliedVoucherCode.value) {
            // Re-check or remove? Better to remove to force re-validation or just re-validate silently?
            // For simplicity, let's remove and ask user to re-apply or keep it if we want to be fancy.
            // Let's remove to ensure validity with new price.
            removeVoucher();
        }
    },
);
</script>

<template>
    <div class="sticky top-4">
        <ReviewRating :rating="4.99" :totalReviews="10521" />

        <ContactCS />

        <!-- Voucher Section -->
        <div
            class="mt-5 rounded-lg border border-border/50 bg-card p-6 shadow-sm"
        >
            <h3 class="mb-4 text-lg font-bold text-foreground">Kode Voucher</h3>

            <div class="flex gap-2">
                <div class="relative w-full">
                    <div
                        class="absolute top-2.5 left-2.5 text-muted-foreground"
                    >
                        <TicketPercent class="h-4 w-4" />
                    </div>
                    <Input
                        v-model="voucherCode"
                        placeholder="Masukkan kode offer"
                        class="pl-9"
                        :disabled="!!appliedVoucherCode"
                        @keyup.enter="checkVoucher"
                    />
                </div>
                <Button
                    v-if="!appliedVoucherCode"
                    variant="secondary"
                    @click="checkVoucher"
                    :disabled="isCheckingVoucher || !voucherCode"
                >
                    <Loader2
                        v-if="isCheckingVoucher"
                        class="h-4 w-4 animate-spin"
                    />
                    <span v-else>Gunakan</span>
                </Button>
                <Button v-else variant="destructive" @click="removeVoucher">
                    Hapus
                </Button>
            </div>
            <p v-if="voucherError" class="mt-2 text-xs text-red-500">
                {{ voucherError }}
            </p>
            <p v-if="appliedVoucherCode" class="mt-2 text-xs text-green-500">
                Voucher berhasil digunakan! Hemat
                {{ formatCurrency(discountAmount) }}
            </p>
        </div>

        <div
            class="mt-5 rounded-lg border border-border/50 bg-card p-6 shadow-sm"
        >
            <h3 class="mb-4 text-lg font-bold text-foreground">
                Ringkasan Pesanan
            </h3>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Brand:</span>
                    <span class="font-medium text-foreground">
                        {{ brandName }}
                    </span>
                </div>

                <div v-if="selectedProduct" class="flex justify-between">
                    <span class="text-muted-foreground">Item:</span>
                    <span class="font-medium text-foreground">
                        {{ selectedProduct.name }}
                    </span>
                </div>

                <div v-if="selectedProduct" class="flex justify-between">
                    <span class="text-muted-foreground">Harga:</span>
                    <span class="font-medium text-foreground">
                        {{ formatCurrency(selectedProduct.sell_price) }}
                    </span>
                </div>

                <div
                    v-if="appliedVoucherCode"
                    class="flex justify-between text-green-500"
                >
                    <span class="font-medium">Diskon:</span>
                    <span class="font-medium">
                        -{{ formatCurrency(discountAmount) }}
                    </span>
                </div>

                <div v-if="selectedPayment" class="flex justify-between">
                    <span class="text-muted-foreground">Pembayaran:</span>
                    <span class="font-medium text-foreground">
                        {{ getPaymentName() }}
                    </span>
                </div>

                <div
                    v-if="
                        selectedPayment &&
                        paymentType === 'automatic' &&
                        selectedProduct
                    "
                    class="flex justify-between"
                >
                    <span class="text-muted-foreground">Fee:</span>
                    <span class="font-medium text-foreground">
                        {{
                            formatCurrency(
                                Math.max(
                                    0,
                                    totalAmount +
                                        discountAmount -
                                        selectedProduct.sell_price,
                                ),
                            )
                        }}
                    </span>
                </div>

                <div class="border-t border-border/30 pt-3">
                    <div class="flex justify-between text-base font-bold">
                        <span class="text-foreground">Total:</span>
                        <span class="text-primary">
                            {{ formatCurrency(totalAmount) }}
                        </span>
                    </div>
                </div>
            </div>

            <Button
                class="mt-6 w-full"
                @click="emit('checkout')"
                :disabled="isLoading"
            >
                <Loader2 v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
                <BadgeCheck v-else class="mr-2 h-4 w-4" />
                {{ isLoading ? 'Memproses...' : 'Beli Sekarang' }}
            </Button>
        </div>
    </div>
</template>
