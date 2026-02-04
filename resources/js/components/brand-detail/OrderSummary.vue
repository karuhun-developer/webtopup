<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { formatCurrency } from '@/lib/utils';
import { PPOBProductDataItem } from '@/types/cms/ppob';
import { BadgeCheck } from 'lucide-vue-next';
import ContactCS from './ContactCS.vue';
import ReviewRating from './ReviewRating.vue';

interface ManualBank {
    id: string;
    name: string;
    account_number: string;
    account_name: string;
    img: string;
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
    manualBanks: ManualBank[];
    paymentMethods: PaymentMethod[];
}>();

const emit = defineEmits<{
    checkout: [];
}>();

const getPaymentName = () => {
    if (!props.selectedPayment) return null;

    if (props.paymentType === 'manual') {
        return props.manualBanks.find((p) => p.id === props.selectedPayment)
            ?.name;
    } else {
        return props.paymentMethods.find((p) => p.id === props.selectedPayment)
            ?.name;
    }
};
</script>

<template>
    <div class="sticky top-4">
        <ReviewRating :rating="4.99" :totalReviews="10521" />

        <ContactCS />

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
                                totalAmount - selectedProduct.sell_price,
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

            <Button class="mt-6 w-full" @click="emit('checkout')">
                <BadgeCheck class="mr-2 h-4 w-4" />
                Beli Sekarang
            </Button>
        </div>
    </div>
</template>
