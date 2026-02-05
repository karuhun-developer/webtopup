<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { formatCurrency } from '@/lib/utils';
import { ref } from 'vue';

defineProps<{
    balance: number;
}>();

const depositAmount = ref('');
const selectedPayment = ref<string | null>(null);

const paymentMethods = [
    { id: 'dana', name: 'DANA', fee: 500 },
    { id: 'ovo', name: 'OVO', fee: 500 },
    { id: 'gopay', name: 'GoPay', fee: 500 },
    { id: 'va_bca', name: 'BCA Virtual Account', fee: 4000 },
    { id: 'va_mandiri', name: 'Mandiri Virtual Account', fee: 4000 },
];

const handleDeposit = () => {
    if (!depositAmount.value) {
        alert('Please enter deposit amount');
        return;
    }
    if (!selectedPayment.value) {
        alert('Please select payment method');
        return;
    }
    alert('Deposit functionality will be implemented');
};
</script>

<template>
    <div class="rounded-lg border border-border/50 bg-card p-6 shadow-sm">
        <h2 class="mb-6 text-lg font-bold text-foreground">
            Deposit Balance
        </h2>

        <!-- Current Balance -->
        <div
            class="mb-6 rounded-lg bg-gradient-to-r from-primary to-primary/80 p-6 text-white"
        >
            <p class="mb-2 text-sm opacity-90">Current Balance</p>
            <p class="text-3xl font-bold">{{ formatCurrency(balance) }}</p>
        </div>

        <!-- Deposit Form -->
        <form class="space-y-4" @submit.prevent="handleDeposit">
            <div>
                <Label for="depositAmount">Deposit Amount</Label>
                <Input
                    id="depositAmount"
                    v-model="depositAmount"
                    type="number"
                    placeholder="Enter amount (e.g., 100000)"
                    class="mt-1"
                />
            </div>

            <div>
                <Label>Payment Method</Label>
                <div class="mt-2 space-y-2">
                    <button
                        v-for="method in paymentMethods"
                        :key="method.id"
                        type="button"
                        class="flex w-full items-center justify-between rounded-lg border-2 p-4 transition-all"
                        :class="
                            selectedPayment === method.id
                                ? 'border-primary bg-primary/5'
                                : 'border-border/50 hover:border-primary'
                        "
                        @click="selectedPayment = method.id"
                    >
                        <span class="font-medium text-foreground">
                            {{ method.name }}
                        </span>
                        <span class="text-sm text-muted-foreground">
                            Fee: {{ formatCurrency(method.fee) }}
                        </span>
                    </button>
                </div>
            </div>

            <Button
                type="submit"
                class="w-full bg-primary hover:bg-primary/90"
            >
                Deposit Now
            </Button>
        </form>
    </div>
</template>
