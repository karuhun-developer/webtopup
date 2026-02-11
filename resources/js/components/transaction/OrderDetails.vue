<script setup lang="ts">
import StatusBadge from '@/components/transaction/StatusBadge.vue';
import { formatCurrency } from '@/lib/utils';
import { OrderDataItem } from '@/types/cms/main';

defineProps<{
    order: OrderDataItem;
}>();
</script>

<template>
    <div class="rounded-lg border border-border/50 bg-card p-6 shadow-sm">
        <h3 class="mb-4 text-lg font-bold text-foreground">Detail Pesanan</h3>

        <div class="space-y-4">
            <!-- Order Reference -->
            <div>
                <p class="text-xs text-muted-foreground">Nomor Pesanan</p>
                <p class="font-mono text-sm font-medium text-foreground">
                    {{ order.reference }}
                </p>
            </div>

            <!-- Product Info -->
            <div class="flex gap-3 rounded-lg bg-muted/50 p-3">
                <img
                    v-if="order?.product?.image"
                    :src="order.product.image"
                    :alt="order.product.name"
                    class="h-16 w-16 rounded-lg object-cover"
                />
                <div class="flex-1">
                    <p class="text-xs text-muted-foreground">
                        {{ order?.brand?.name }}
                    </p>
                    <p class="font-medium text-foreground">
                        {{ order?.product?.name }}
                    </p>
                    <p class="mt-1 text-sm font-bold text-primary">
                        {{ formatCurrency(order.amount) }}
                    </p>
                </div>
            </div>

            <!-- Account Details -->
            <div class="space-y-2 rounded-lg bg-muted/50 p-3">
                <div>
                    <p class="text-xs text-muted-foreground">ID Akun</p>
                    <p class="font-medium text-foreground">
                        {{ order.submited.account_id }}
                    </p>
                </div>
                <div v-if="order.submited.server_id">
                    <p class="text-xs text-muted-foreground">Server</p>
                    <p class="font-medium text-foreground">
                        {{ order.submited.server_id }}
                    </p>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="space-y-2">
                <div>
                    <p class="text-xs text-muted-foreground">Nama</p>
                    <p class="text-sm text-foreground">{{ order.name }}</p>
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">No. WhatsApp</p>
                    <p class="text-sm text-foreground">{{ order.phone }}</p>
                </div>
                <div v-if="order.email">
                    <p class="text-xs text-muted-foreground">Email</p>
                    <p class="text-sm text-foreground">{{ order.email }}</p>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="space-y-2 border-t border-border/50 pt-3">
                <div class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Harga Produk</span>
                    <span class="text-foreground">
                        {{ formatCurrency(order.amount) }}
                    </span>
                </div>
                <div v-if="order.fee > 0" class="flex justify-between text-sm">
                    <span class="text-muted-foreground">Biaya Admin</span>
                    <span class="text-foreground">
                        {{ formatCurrency(order.fee) }}
                    </span>
                </div>
                <div
                    class="flex justify-between border-t border-border/50 pt-2"
                >
                    <span class="font-medium text-foreground">Total</span>
                    <span class="text-lg font-bold text-primary">
                        {{ formatCurrency(order.total_amount) }}
                    </span>
                </div>
            </div>

            <!-- Status -->
            <div class="space-y-2 border-t border-border/50 pt-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted-foreground">
                        Status Pembayaran
                    </span>
                    <StatusBadge
                        :status="order.payment_status"
                        type="payment"
                    />
                </div>
                <div
                    class="flex items-center justify-between"
                    v-if="order.brand?.provider === 'digiflazz'"
                >
                    <span class="text-sm text-muted-foreground">
                        Status Top Up
                    </span>
                    <StatusBadge :status="order.topup_status" type="topup" />
                </div>
            </div>
        </div>
    </div>
</template>
