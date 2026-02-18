<script setup lang="ts">
import { Card } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { OrderDataItem } from '@/types/cms/main';
import dayjs from 'dayjs';
import {
    AlertTriangle,
    CheckCircle2,
    Clock,
    Gift,
    UserPlus,
} from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    order: OrderDataItem;
}>();

// Helper to check if it's a gift order based on data presence
const isGiftOrder = computed(() => {
    return props.order.product?.provider === 'gift';
});

// Helper to check if it's a manual topup based on data presence or type
const isManualTopup = computed(() => {
    // Adjust logic if there is a specific type for manual topup,
    // otherwise we can infer from the presence of manual topup specific fields if needed
    // or just rely on the fact that if it's not gift order but has delivery proof, it might be relevant.
    // For now, let's assume if it has 'gift_send_proof' it's relevant for both.
    return true;
});

// Logic for Gift Order
const userConfirmFriendAt = computed(() => {
    return props.order.submited?.user_confirm_friend_timestamp
        ? dayjs(props.order.submited.user_confirm_friend_timestamp)
        : null;
});

const countdownEndDate = computed(() => {
    return userConfirmFriendAt.value?.add(7, 'day') || null;
});

const countdownRemaining = computed(() => {
    if (!countdownEndDate.value) return null;
    const now = dayjs();
    const diff = countdownEndDate.value.diff(now);
    if (diff <= 0) return 'READY TO SEND';

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

    return `${days}d ${hours}h ${minutes}m`;
});

// Check specific flags
const adminAddedFriend = computed(() => props.order.submited?.admin_add_friend);
const userConfirmedFriend = computed(
    () => props.order.submited?.user_confirm_friend,
);
const giftSent = computed(() => props.order.submited?.gift_send);
</script>

<template>
    <Card class="p-6">
        <h2 class="mb-6 flex items-center text-lg font-bold">
            <Gift class="mr-2 h-5 w-5" />
            Status Pengiriman
        </h2>

        <div class="space-y-6">
            <!-- GIFT ORDER SPECIFIC UI -->
            <template v-if="isGiftOrder">
                <!-- Admin IGN -->
                <div v-if="order.submited?.admin_account_ign">
                    <Label class="mb-2 block text-muted-foreground"
                        >IGN Akun Admin</Label
                    >
                    <div class="font-medium">
                        {{ order.submited.admin_account_ign }}
                    </div>
                </div>

                <Separator v-if="order.submited?.admin_account_ign" />

                <!-- Friendship Verification Status -->
                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="font-semibold">Verifikasi Pertemanan</h3>
                        <CheckCircle2
                            v-if="adminAddedFriend && userConfirmedFriend"
                            class="h-5 w-5 text-green-500"
                        />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <!-- Admin Sent Request -->
                        <div
                            class="rounded-lg border p-4"
                            :class="
                                adminAddedFriend
                                    ? 'border-green-500/50 bg-green-500/10'
                                    : 'border-border'
                            "
                        >
                            <div class="flex items-center gap-2">
                                <UserPlus
                                    class="h-4 w-4"
                                    :class="
                                        adminAddedFriend
                                            ? 'text-green-600'
                                            : 'text-muted-foreground'
                                    "
                                />
                                <span class="text-sm font-semibold"
                                    >Admin Mengirim Permintaan</span
                                >
                            </div>
                            <div
                                v-if="
                                    order.submited?.admin_add_friend_timestamp
                                "
                                class="mt-2 text-xs text-muted-foreground"
                            >
                                {{
                                    dayjs(
                                        order.submited
                                            .admin_add_friend_timestamp,
                                    ).format('DD MMM YYYY, HH:mm')
                                }}
                            </div>
                            <div
                                v-if="order.submited?.admin_add_friend_proof"
                                class="mt-3"
                            >
                                <Label
                                    class="mb-1 block text-xs text-muted-foreground"
                                    >Bukti</Label
                                >
                                <img
                                    :src="order.submited.admin_add_friend_proof"
                                    class="w-full rounded-lg"
                                    alt="Bukti Admin"
                                />
                            </div>
                        </div>

                        <!-- User Accepted Request -->
                        <div
                            class="rounded-lg border p-4"
                            :class="
                                userConfirmedFriend
                                    ? 'border-green-500/50 bg-green-500/10'
                                    : 'border-border'
                            "
                        >
                            <div class="flex items-center gap-2">
                                <CheckCircle2
                                    class="h-4 w-4"
                                    :class="
                                        userConfirmedFriend
                                            ? 'text-green-600'
                                            : 'text-muted-foreground'
                                    "
                                />
                                <span class="text-sm font-semibold"
                                    >User Menerima Permintaan</span
                                >
                            </div>
                            <div
                                v-if="
                                    order.submited
                                        ?.user_confirm_friend_timestamp
                                "
                                class="mt-2 text-xs text-muted-foreground"
                            >
                                {{
                                    dayjs(
                                        order.submited
                                            .user_confirm_friend_timestamp,
                                    ).format('DD MMM YYYY, HH:mm')
                                }}
                            </div>
                            <div
                                v-if="order.submited?.user_confirm_friend_proof"
                                class="mt-3"
                            >
                                <Label
                                    class="mb-1 block text-xs text-muted-foreground"
                                    >Bukti</Label
                                >
                                <img
                                    :src="
                                        order.submited.user_confirm_friend_proof
                                    "
                                    class="w-full rounded-lg"
                                    alt="Bukti User"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <Separator />

                <!-- Countdown Timer -->
                <div
                    v-if="countdownEndDate && !giftSent"
                    class="rounded-lg border p-4"
                    :class="
                        countdownRemaining === 'READY TO SEND'
                            ? 'border-green-500/50 bg-green-500/10'
                            : 'border-orange-500/50 bg-orange-500/10'
                    "
                >
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Clock class="h-5 w-5" />
                            <span class="font-semibold"
                                >Hitung Mundur Pembatasan</span
                            >
                        </div>
                        <span
                            class="font-mono text-lg font-bold"
                            :class="
                                countdownRemaining === 'READY TO SEND'
                                    ? 'text-green-600'
                                    : 'text-orange-600'
                            "
                        >
                            {{ countdownRemaining }}
                        </span>
                    </div>
                    <div class="mt-2 text-xs text-muted-foreground">
                        <div class="flex justify-between">
                            <span
                                >Dimulai:
                                {{
                                    userConfirmFriendAt?.format('DD MMM, HH:mm')
                                }}</span
                            >
                            <span
                                >Berakhir:
                                {{
                                    countdownEndDate.format('DD MMM, HH:mm')
                                }}</span
                            >
                        </div>
                    </div>
                    <div
                        v-if="countdownRemaining === 'READY TO SEND'"
                        class="mt-3 text-center"
                    >
                        <p class="text-sm font-bold text-green-600">
                            Menunggu admin mengirimkan hadiah.
                        </p>
                    </div>
                </div>
                <Separator v-if="countdownEndDate && !giftSent" />
            </template>

            <!-- COMMON / MANUAL TOPUP UI (Delivery Proof & Status) -->

            <!-- Final Delivery Proof -->
            <div v-if="order.submited?.gift_send_proof">
                <Label class="mb-2 block text-sm font-semibold"
                    >Bukti Pengiriman</Label
                >
                <img
                    :src="order.submited.gift_send_proof"
                    class="w-full rounded-lg"
                    alt="Bukti Pengiriman"
                />
            </div>

            <!-- Status Indicators -->
            <div class="space-y-3">
                <div
                    v-if="order.submited?.dispute"
                    class="rounded-lg border border-yellow-500/50 bg-yellow-500/10 p-4"
                >
                    <div class="flex items-center gap-2">
                        <AlertTriangle class="h-5 w-5 text-yellow-500" />
                        <span class="font-semibold text-yellow-500"
                            >Pesanan Bermasalah</span
                        >
                    </div>
                    <p class="mt-1 text-sm text-yellow-600">
                        Ada masalah dengan pesanan Anda. Silakan hubungi layanan
                        pelanggan.
                    </p>
                </div>

                <div
                    v-if="giftSent || order.submited?.done"
                    class="rounded-lg border border-green-500/50 bg-green-500/10 p-4"
                >
                    <div class="flex items-center gap-2">
                        <CheckCircle2 class="h-5 w-5 text-green-500" />
                        <span class="font-semibold text-green-500">
                            {{
                                isGiftOrder
                                    ? 'Hadiah Terkirim & Selesai'
                                    : 'Pesanan Selesai'
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>
