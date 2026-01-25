<script setup lang="ts">
import MainHeader from '@/components/MainHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Variant {
    id: number;
    name: string;
    bonus?: string;
    price: string;
}

interface PaymentMethod {
    id: string;
    name: string;
    logo?: string;
    fee: string;
}

interface Props {
    productName?: string;
    productImage?: string;
}

const props = withDefaults(defineProps<Props>(), {
    productName: 'Mobile Legends',
    productImage:
        'https://placehold.co/400x200/7C3AED/FFFFFF?text=Mobile+Legends',
});

const userId = ref('');
const zoneId = ref('');
const selectedVariant = ref<number | null>(null);
const selectedPayment = ref<string | null>(null);

const variants: Variant[] = [
    { id: 1, name: '5 Diamonds', price: 'Rp 1.500' },
    { id: 2, name: '12 Diamonds', bonus: '+1', price: 'Rp 3.500' },
    { id: 3, name: '28 Diamonds', bonus: '+2', price: 'Rp 7.500' },
    { id: 4, name: '56 Diamonds', bonus: '+4', price: 'Rp 14.500' },
    { id: 5, name: '85 Diamonds', bonus: '+7', price: 'Rp 21.500' },
    { id: 6, name: '170 Diamonds', bonus: '+17', price: 'Rp 43.000' },
    { id: 7, name: '284 Diamonds', bonus: '+28', price: 'Rp 71.500' },
    { id: 8, name: '568 Diamonds', bonus: '+57', price: 'Rp 143.000' },
];

const paymentMethods: PaymentMethod[] = [
    { id: 'dana', name: 'DANA', fee: 'Rp 500' },
    { id: 'ovo', name: 'OVO', fee: 'Rp 500' },
    { id: 'gopay', name: 'GoPay', fee: 'Rp 500' },
    { id: 'qris', name: 'QRIS', fee: 'Rp 0' },
    { id: 'bca', name: 'BCA Virtual Account', fee: 'Rp 4.000' },
    { id: 'bni', name: 'BNI Virtual Account', fee: 'Rp 4.000' },
    { id: 'mandiri', name: 'Mandiri Virtual Account', fee: 'Rp 4.000' },
];

const handleCheckout = () => {
    if (
        !userId.value ||
        !zoneId.value ||
        !selectedVariant.value ||
        !selectedPayment.value
    ) {
        alert('Please complete all fields');
        return;
    }
    alert('Checkout functionality will be implemented');
};
</script>

<template>
    <Head :title="`${productName} - Topup`" />

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <MainHeader :show-back-button="true" />

        <!-- Main Content -->
        <main class="mx-auto max-w-5xl px-4 py-8">
            <!-- Product Header -->
            <div class="mb-8 overflow-hidden rounded-xl bg-card shadow-sm">
                <img
                    :src="productImage"
                    :alt="productName"
                    class="h-48 w-full object-cover"
                />
                <div class="p-6">
                    <h1 class="text-2xl font-bold text-foreground">
                        {{ productName }}
                    </h1>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Top up diamonds instantly for {{ productName }}
                    </p>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Form Section -->
                <div class="lg:col-span-2">
                    <!-- Step 1: Account Data -->
                    <section
                        class="mb-6 rounded-lg border border-border/50 bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-lg font-bold text-foreground">
                            1. Masukkan Data Akun
                        </h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <Label for="userId">User ID</Label>
                                <Input
                                    id="userId"
                                    v-model="userId"
                                    placeholder="Enter User ID"
                                    class="mt-1"
                                />
                            </div>
                            <div>
                                <Label for="zoneId">Zone ID</Label>
                                <Input
                                    id="zoneId"
                                    v-model="zoneId"
                                    placeholder="Enter Zone ID"
                                    class="mt-1"
                                />
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-muted-foreground">
                            Untuk menemukan User ID dan Zone ID Anda, buka
                            profil dalam game
                        </p>
                    </section>

                    <!-- Step 2: Select Variant -->
                    <section
                        class="mb-6 rounded-lg border border-border/50 bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-lg font-bold text-foreground">
                            2. Pilih Nominal
                        </h2>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                            <button
                                v-for="variant in variants"
                                :key="variant.id"
                                class="rounded-lg border-2 p-4 text-left transition-all"
                                :class="
                                    selectedVariant === variant.id
                                        ? 'border-primary bg-primary/5'
                                        : 'border-border/50 hover:border-primary/50'
                                "
                                @click="selectedVariant = variant.id"
                            >
                                <div class="font-semibold text-foreground">
                                    {{ variant.name }}
                                </div>
                                <div
                                    v-if="variant.bonus"
                                    class="text-xs text-emerald-500"
                                >
                                    Bonus {{ variant.bonus }}
                                </div>
                                <div
                                    class="mt-2 text-sm font-bold text-primary"
                                >
                                    {{ variant.price }}
                                </div>
                            </button>
                        </div>
                    </section>

                    <!-- Step 3: Payment Method -->
                    <section
                        class="mb-6 rounded-lg border border-border/50 bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-lg font-bold text-foreground">
                            3. Pilih Metode Pembayaran
                        </h2>
                        <div class="space-y-2">
                            <button
                                v-for="method in paymentMethods"
                                :key="method.id"
                                class="flex w-full items-center justify-between rounded-lg border-2 p-4 transition-all"
                                :class="
                                    selectedPayment === method.id
                                        ? 'border-primary bg-primary/5'
                                        : 'border-border/50 hover:border-primary/50'
                                "
                                @click="selectedPayment = method.id"
                            >
                                <span class="font-medium text-foreground">{{
                                    method.name
                                }}</span>
                                <span class="text-sm text-muted-foreground"
                                    >Fee: {{ method.fee }}</span
                                >
                            </button>
                        </div>
                    </section>
                </div>

                <!-- Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div
                        class="sticky top-4 rounded-lg border border-border/50 bg-card p-6 shadow-sm"
                    >
                        <h3 class="mb-4 text-lg font-bold text-foreground">
                            Ringkasan Pesanan
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-muted-foreground"
                                    >Produk:</span
                                >
                                <span class="font-medium text-foreground">{{
                                    productName
                                }}</span>
                            </div>
                            <div
                                v-if="selectedVariant"
                                class="flex justify-between"
                            >
                                <span class="text-muted-foreground"
                                    >Nominal:</span
                                >
                                <span class="font-medium text-foreground">
                                    {{
                                        variants.find(
                                            (v) => v.id === selectedVariant,
                                        )?.name || '-'
                                    }}
                                </span>
                            </div>
                            <div
                                v-if="selectedPayment"
                                class="flex justify-between"
                            >
                                <span class="text-muted-foreground"
                                    >Pembayaran:</span
                                >
                                <span class="font-medium text-foreground">
                                    {{
                                        paymentMethods.find(
                                            (p) => p.id === selectedPayment,
                                        )?.name || '-'
                                    }}
                                </span>
                            </div>
                            <div class="border-t border-border/30 pt-3">
                                <div
                                    class="flex justify-between text-base font-bold"
                                >
                                    <span class="text-foreground">Total:</span>
                                    <span class="text-primary">
                                        {{
                                            selectedVariant
                                                ? variants.find(
                                                      (v) =>
                                                          v.id ===
                                                          selectedVariant,
                                                  )?.price
                                                : 'Rp 0'
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <Button
                            class="mt-6 w-full bg-primary hover:bg-primary/90"
                            @click="handleCheckout"
                        >
                            Beli Sekarang
                        </Button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
