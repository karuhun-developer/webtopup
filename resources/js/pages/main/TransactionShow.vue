<script setup lang="ts">
import MainFooter from '@/components/MainFooter.vue';
import MainHeader from '@/components/MainHeader.vue';
import OrderDetails from '@/components/transaction/OrderDetails.vue';
import PaymentInstructions from '@/components/transaction/PaymentInstructions.vue';
import { OrderDataItem } from '@/types/cms/main';
import { Head } from '@inertiajs/vue3';

defineProps<{
    order: OrderDataItem;
}>();
</script>

<template>
    <Head title="Detail Transaksi" />

    <div class="flex min-h-screen flex-col bg-background">
        <!-- Header -->
        <MainHeader :show-back-button="true" />

        <!-- Main Content -->
        <main class="flex-1">
            <div class="mx-auto max-w-5xl px-4 py-8">
                <!-- Page Title -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-foreground">
                        Detail Transaksi
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Informasi lengkap pesanan Anda
                    </p>
                </div>

                <!-- Content Grid -->
                <div class="grid gap-6 lg:grid-cols-2">
                    <!-- Left Column - Order Details -->
                    <div>
                        <OrderDetails :order="order" />
                    </div>

                    <!-- Right Column - Payment Instructions -->
                    <div>
                        <PaymentInstructions
                            v-if="order.payment"
                            :payment="order.payment"
                            :order-reference="order.reference"
                            :total-amount="order.total_amount"
                        />
                    </div>
                </div>

                <!-- Help Section -->
                <div
                    class="mt-6 rounded-lg border border-border/50 bg-card p-4"
                >
                    <p class="text-sm text-muted-foreground">
                        💬 Butuh bantuan? Hubungi CS kami di
                        <a
                            href="https://wa.me/6281234567890"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-primary hover:underline"
                        >
                            WhatsApp
                        </a>
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <MainFooter />
    </div>
</template>
