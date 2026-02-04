<script setup lang="ts">
import { formatCurrency } from '@/lib/utils';

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

defineProps<{
    paymentType: 'manual' | 'automatic';
    selectedPayment: string | null;
    manualBanks: ManualBank[];
    paymentMethods: PaymentMethod[];
}>();

const emit = defineEmits<{
    'update:paymentType': [value: 'manual' | 'automatic'];
    'update:selectedPayment': [value: string | null];
}>();

const handlePaymentTypeChange = (type: 'manual' | 'automatic') => {
    emit('update:paymentType', type);
    emit('update:selectedPayment', null);
};
</script>

<template>
    <section class="rounded-lg border border-border/50 bg-card p-6 shadow-sm">
        <h2
            class="mb-4 flex items-center gap-2 text-lg font-bold text-foreground"
        >
            <span
                class="flex h-7 w-7 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground"
                >4</span
            >
            Pilih Metode Pembayaran
        </h2>

        <!-- Payment Type Selector -->
        <div class="mb-4 flex gap-2">
            <button
                class="flex-1 rounded-lg border-2 px-4 py-2 text-sm font-medium transition-all"
                :class="
                    paymentType === 'automatic'
                        ? 'border-primary bg-primary/5 text-primary'
                        : 'border-border/50 text-muted-foreground hover:border-primary/50'
                "
                @click="handlePaymentTypeChange('automatic')"
            >
                Otomatis
            </button>
            <button
                class="flex-1 rounded-lg border-2 px-4 py-2 text-sm font-medium transition-all"
                :class="
                    paymentType === 'manual'
                        ? 'border-primary bg-primary/5 text-primary'
                        : 'border-border/50 text-muted-foreground hover:border-primary/50'
                "
                @click="handlePaymentTypeChange('manual')"
            >
                Manual (Transfer Bank)
            </button>
        </div>

        <!-- Manual Payment - Bank Accounts -->
        <div v-if="paymentType === 'manual'" class="space-y-2">
            <button
                v-for="bank in manualBanks"
                :key="bank.id"
                class="flex w-full items-center gap-4 rounded-lg border-2 p-4 transition-all"
                :class="
                    selectedPayment === bank.id
                        ? 'border-primary bg-primary/5'
                        : 'border-border/50 hover:border-primary/50'
                "
                @click="emit('update:selectedPayment', bank.id)"
            >
                <img
                    :src="bank.img"
                    :alt="bank.name"
                    class="h-8 w-12 object-contain"
                />
                <div class="flex-1 text-left">
                    <div class="font-medium text-foreground">
                        {{ bank.name }}
                    </div>
                    <div class="text-xs text-muted-foreground">
                        {{ bank.account_number }} - {{ bank.account_name }}
                    </div>
                </div>
                <span class="text-sm font-medium text-emerald-500">
                    Fee: Rp 0
                </span>
            </button>
        </div>

        <!-- Automatic Payment Methods -->
        <div v-else class="space-y-2">
            <button
                v-for="method in paymentMethods"
                :key="method.id"
                class="flex w-full items-center gap-4 rounded-lg border-2 p-4 transition-all"
                :class="
                    selectedPayment === method.id
                        ? 'border-primary bg-primary/5'
                        : 'border-border/50 hover:border-primary/50'
                "
                @click="emit('update:selectedPayment', method.id)"
            >
                <img
                    :src="method.img"
                    :alt="method.name"
                    class="h-8 w-12 object-contain"
                />
                <span class="flex-1 text-left font-medium text-foreground">
                    {{ method.name }}
                </span>
                <span class="text-sm text-muted-foreground">
                    Fee:
                    {{
                        method.action === 'multiply'
                            ? `${(method.fee * 100).toFixed(1)}%`
                            : formatCurrency(method.fee)
                    }}
                </span>
            </button>
        </div>
    </section>
</template>
