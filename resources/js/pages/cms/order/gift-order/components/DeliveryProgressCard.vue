<script setup lang="ts">
import {
    notify,
    save,
} from '@/actions/App/Http/Controllers/Cms/Order/GiftOrderController';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { useSwal } from '@/composables/useSwal';
import { OrderDataItem } from '@/types/cms/main';
import { router, useForm } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import {
    AlertTriangle,
    CheckCircle2,
    Clock,
    Gift,
    Send,
    Upload,
    UserPlus,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    order: OrderDataItem;
}>();

const { toast } = useSwal();

const adminIGNForm = useForm({
    admin_account_ign: props.order.submited?.admin_account_ign || '',
});

const adminAddFriendForm = useForm({
    admin_add_friend: props.order.submited?.admin_add_friend || false,
    admin_add_friend_timestamp:
        props.order.submited?.admin_add_friend_timestamp ||
        dayjs().format('YYYY-MM-DDTHH:mm'),
    admin_add_friend_proof: null as File | null,
});

const userAddFriendForm = useForm({
    user_confirm_friend: props.order.submited?.user_confirm_friend || false,
    user_confirm_friend_timestamp:
        props.order.submited?.user_confirm_friend_timestamp || null,
    user_confirm_friend_proof: null as File | null,
});

const giftSendForm = useForm({
    gift_send: props.order.submited?.gift_send || false,
    gift_send_proof: null as File | null,
});

const adminProofPreview = ref<string | null>(null);
const userProofPreview = ref<string | null>(null);
const giftProofPreview = ref<string | null>(null);

// Calculate countdown from admin_add_friend_at
const adminAddFriendAt = computed(() => {
    return props.order.submited?.admin_add_friend_at
        ? dayjs(props.order.submited.admin_add_friend_at)
        : null;
});

const countdownEndDate = computed(() => {
    return adminAddFriendAt.value?.add(7, 'day') || null;
});

const countdownRemaining = computed(() => {
    if (!countdownEndDate.value) return null;
    const now = dayjs();
    const diff = countdownEndDate.value.diff(now);
    if (diff <= 0) return 'Expired';

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

    return `${days}d ${hours}h ${minutes}m`;
});

const saveAdminIGN = () => {
    toast.fire({
        icon: 'success',
        title: 'Admin IGN saved!',
    });

    adminIGNForm.put(
        save({
            order: props.order.reference,
        }).url,
        {
            preserveScroll: true,
        },
    );
};

const sendFriendRequestNotification = () => {
    router.put(
        notify({
            order: props.order.reference,
        }).url,
        {
            action: 'admin_add_friend',
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.fire({
                    icon: 'success',
                    title: 'Friend request notification sent!',
                });
            },
        },
    );
};

const sendWaitNotification = () => {
    router.put(
        notify({
            order: props.order.reference,
        }).url,
        {
            action: 'user_confirm_friend',
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.fire({
                    icon: 'success',
                    title: 'Wait 7 days notification sent!',
                });
            },
        },
    );
};

const handleAdminProofUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        adminAddFriendForm.admin_add_friend_proof = file;
        adminProofPreview.value = URL.createObjectURL(file);
    }

    adminAddFriendForm.put(
        save({
            order: props.order.reference,
        }).url,
        {
            preserveScroll: true,
        },
    );
};

const handleUserProofUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        userAddFriendForm.user_confirm_friend_proof = file;
        userProofPreview.value = URL.createObjectURL(file);
    }

    userAddFriendForm.put(
        save({
            order: props.order.reference,
        }).url,
        {
            preserveScroll: true,
        },
    );
};

const handleGiftProofUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        giftSendForm.gift_send_proof = file;
        giftProofPreview.value = URL.createObjectURL(file);
    }

    giftSendForm.put(
        save({
            order: props.order.reference,
        }).url,
        {
            preserveScroll: true,
        },
    );
};

const toggleAdminAddFriend = () => {
    adminAddFriendForm.admin_add_friend = !adminAddFriendForm.admin_add_friend;
    toast.fire({
        icon: 'success',
        title: adminAddFriendForm.admin_add_friend
            ? 'Admin friend request confirmed!'
            : 'Admin friend request unchecked',
    });

    adminAddFriendForm.put(
        save({
            order: props.order.reference,
        }).url,
        {
            preserveScroll: true,
        },
    );
};

const toggleUserAddFriend = () => {
    userAddFriendForm.user_confirm_friend =
        !userAddFriendForm.user_confirm_friend;
    toast.fire({
        icon: 'success',
        title: userAddFriendForm.user_confirm_friend
            ? 'User acceptance confirmed!'
            : 'User acceptance unchecked',
    });

    userAddFriendForm.put(
        save({
            order: props.order.reference,
        }).url,
        {
            preserveScroll: true,
        },
    );
};

const toggleGiftSend = () => {
    giftSendForm.gift_send = !giftSendForm.gift_send;
    toast.fire({
        icon: 'success',
        title: giftSendForm.gift_send
            ? 'Gift delivery confirmed!'
            : 'Gift delivery unchecked',
    });

    giftSendForm.put(
        save({
            order: props.order.reference,
        }).url,
        {
            preserveScroll: true,
        },
    );
};

const sendGiftCompletionNotification = () => {
    router.put(
        notify({
            order: props.order.reference,
        }).url,
        {
            action: 'gift_send',
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                toast.fire({
                    icon: 'success',
                    title: 'Gift completion notification sent!',
                });
            },
        },
    );
};
</script>

<template>
    <Card class="p-6">
        <h2 class="mb-6 flex items-center text-lg font-bold">
            <Gift class="mr-2 h-5 w-5" />
            Delivery Progress
        </h2>

        <div class="space-y-6">
            <!-- Admin IGN -->
            <div>
                <Label class="mb-2 block">Admin Account IGN</Label>
                <div class="flex gap-2">
                    <Input
                        v-model="adminIGNForm.admin_account_ign"
                        placeholder="Enter admin IGN"
                        class="flex-1"
                        @change="saveAdminIGN"
                    />
                    <Button
                        @click="saveAdminIGN"
                        size="sm"
                        :disabled="!adminIGNForm.admin_account_ign"
                    >
                        Save
                    </Button>
                </div>
            </div>

            <Separator />

            <!-- Friendship Verification -->
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="font-semibold">Friendship Verification</h3>
                    <CheckCircle2
                        v-if="
                            adminAddFriendForm.admin_add_friend &&
                            userAddFriendForm.user_confirm_friend
                        "
                        class="h-5 w-5 text-green-500"
                    />
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Admin Sent Friend Request -->
                    <div
                        class="rounded-lg border p-4"
                        :class="
                            adminAddFriendForm.admin_add_friend
                                ? 'border-green-500/50 bg-green-500/10'
                                : 'border-border'
                        "
                    >
                        <div class="mb-3 flex items-center gap-2">
                            <UserPlus class="h-4 w-4 text-muted-foreground" />
                            <span class="text-sm font-semibold"
                                >Admin Sent Request</span
                            >
                        </div>

                        <div class="mb-3">
                            <Label
                                class="mb-1 block text-xs text-muted-foreground"
                                >Request Sent At</Label
                            >
                            <Input
                                type="datetime-local"
                                v-model="
                                    adminAddFriendForm.admin_add_friend_timestamp
                                "
                                class="w-full"
                            />
                        </div>

                        <div class="mb-3">
                            <Label
                                class="mb-1 block text-xs text-muted-foreground"
                                >Upload Proof</Label
                            >
                            <input
                                type="file"
                                accept="image/*"
                                @change="handleAdminProofUpload"
                                class="hidden"
                                id="admin-proof-upload"
                            />
                            <label
                                for="admin-proof-upload"
                                class="flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed p-3 hover:border-primary"
                            >
                                <Upload
                                    class="mr-2 h-4 w-4 text-muted-foreground"
                                />
                                <span class="text-xs">
                                    {{
                                        adminProofPreview
                                            ? 'Change Screenshot'
                                            : 'Upload Screenshot'
                                    }}
                                </span>
                            </label>
                            <img
                                v-if="adminProofPreview"
                                :src="adminProofPreview"
                                class="mt-2 w-full rounded-lg"
                            />
                            <img
                                v-else-if="
                                    order.submited?.admin_add_friend_proof
                                "
                                :src="order.submited?.admin_add_friend_proof"
                                class="mt-2 w-full rounded-lg"
                            />
                        </div>

                        <Button
                            @click="toggleAdminAddFriend"
                            :variant="
                                adminAddFriendForm.admin_add_friend
                                    ? 'default'
                                    : 'outline'
                            "
                            size="sm"
                            class="w-full"
                        >
                            {{
                                adminAddFriendForm.admin_add_friend
                                    ? 'Request Sent'
                                    : 'Mark as Sent'
                            }}
                        </Button>
                    </div>

                    <!-- User Accepted Friend Request -->
                    <div
                        class="rounded-lg border p-4"
                        :class="
                            userAddFriendForm.user_confirm_friend
                                ? 'border-green-500/50 bg-green-500/10'
                                : 'border-border'
                        "
                    >
                        <div class="mb-3 flex items-center gap-2">
                            <CheckCircle2
                                class="h-4 w-4 text-muted-foreground"
                            />
                            <span class="text-sm font-semibold"
                                >User Accepted Request</span
                            >
                        </div>

                        <div class="mb-3">
                            <Label
                                class="mb-1 block text-xs text-muted-foreground"
                                >Accepted At</Label
                            >
                            <Input
                                type="datetime-local"
                                v-model="
                                    userAddFriendForm.user_confirm_friend_timestamp
                                "
                                class="w-full"
                            />
                        </div>

                        <div class="mb-3">
                            <Label
                                class="mb-1 block text-xs text-muted-foreground"
                                >Upload Proof</Label
                            >
                            <input
                                type="file"
                                accept="image/*"
                                @change="handleUserProofUpload"
                                class="hidden"
                                id="user-proof-upload"
                            />
                            <label
                                for="user-proof-upload"
                                class="flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed p-3 hover:border-primary"
                            >
                                <Upload
                                    class="mr-2 h-4 w-4 text-muted-foreground"
                                />
                                <span class="text-xs">
                                    {{
                                        userProofPreview
                                            ? 'Change Screenshot'
                                            : 'Upload Screenshot'
                                    }}
                                </span>
                            </label>
                            <img
                                v-if="userProofPreview"
                                :src="userProofPreview"
                                class="mt-2 w-full rounded-lg"
                            />
                            <img
                                v-else-if="
                                    order.submited?.user_confirm_friend_proof
                                "
                                :src="order.submited?.user_confirm_friend_proof"
                                class="mt-2 w-full rounded-lg"
                            />
                        </div>

                        <Button
                            @click="toggleUserAddFriend"
                            :variant="
                                userAddFriendForm.user_confirm_friend
                                    ? 'default'
                                    : 'outline'
                            "
                            size="sm"
                            class="w-full"
                        >
                            {{
                                userAddFriendForm.user_confirm_friend
                                    ? 'Accepted'
                                    : 'Mark as Accepted'
                            }}
                        </Button>
                    </div>
                </div>

                <div class="mt-3 grid gap-2 md:grid-cols-2">
                    <Button
                        @click="sendFriendRequestNotification"
                        variant="outline"
                        size="sm"
                    >
                        <Send class="mr-2 h-4 w-4" />
                        Notify: Friend Request
                    </Button>
                    <Button
                        @click="sendWaitNotification"
                        variant="outline"
                        size="sm"
                    >
                        <Clock class="mr-2 h-4 w-4" />
                        Notify: Wait 7 Days
                    </Button>
                </div>
            </div>

            <Separator />

            <!-- Countdown Timer -->
            <div
                v-if="countdownEndDate"
                class="rounded-lg border p-4"
                :class="
                    countdownRemaining === 'Expired'
                        ? 'border-red-500/50 bg-red-500/10'
                        : 'border-blue-500/50 bg-blue-500/10'
                "
            >
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <Clock class="h-5 w-5" />
                        <span class="font-semibold">Restriction Countdown</span>
                    </div>
                    <span
                        class="font-mono text-lg font-bold"
                        :class="
                            countdownRemaining === 'Expired'
                                ? 'text-red-500'
                                : 'text-blue-500'
                        "
                    >
                        {{ countdownRemaining }}
                    </span>
                </div>
                <div class="mt-2 text-xs text-muted-foreground">
                    <div class="flex justify-between">
                        <span
                            >Started:
                            {{
                                adminAddFriendAt?.format('MMM DD, HH:mm A')
                            }}</span
                        >
                        <span
                            >Ends:
                            {{
                                countdownEndDate.format('MMM DD, HH:mm A')
                            }}</span
                        >
                    </div>
                </div>
            </div>

            <Separator />

            <!-- Final Gift Delivery Proof -->
            <div class="rounded-lg border p-4">
                <Label class="mb-2 block text-sm font-semibold"
                    >Final Gift Delivery Proof</Label
                >
                <input
                    type="file"
                    accept="image/*,video/*"
                    @change="handleGiftProofUpload"
                    class="hidden"
                    id="gift-proof-upload"
                />
                <label
                    for="gift-proof-upload"
                    class="flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed p-4 hover:border-primary"
                >
                    <Upload class="mr-2 h-4 w-4 text-muted-foreground" />
                    <span class="text-sm">
                        {{
                            giftProofPreview
                                ? 'Change File'
                                : 'Upload Image/Video'
                        }}
                    </span>
                </label>
                <img
                    v-if="giftProofPreview"
                    :src="giftProofPreview"
                    class="mt-2 w-full rounded-lg"
                />
                <img
                    v-else-if="order.submited?.gift_send_proof"
                    :src="order.submited?.gift_send_proof"
                    class="mt-2 w-full rounded-lg"
                />
            </div>

            <Separator />

            <!-- Send Gift -->
            <div
                class="rounded-lg border p-4"
                :class="
                    giftSendForm.gift_send
                        ? 'border-green-500/50 bg-green-500/10'
                        : 'border-border'
                "
            >
                <div class="mb-3 flex items-center gap-2">
                    <Gift class="h-5 w-5 text-muted-foreground" />
                    <span class="font-semibold">Send Gift</span>
                </div>
                <Button
                    @click="toggleGiftSend"
                    :variant="giftSendForm.gift_send ? 'default' : 'outline'"
                    class="w-full"
                >
                    {{
                        giftSendForm.gift_send
                            ? 'Gift Sent'
                            : 'Confirm Gift Sent'
                    }}
                </Button>

                <!-- Notify Gift Completion Button -->
                <Button
                    v-if="giftSendForm.gift_send"
                    @click="sendGiftCompletionNotification"
                    variant="outline"
                    size="sm"
                    class="mt-2 w-full"
                >
                    <Send class="mr-2 h-4 w-4" />
                    Notify: Gift Completion
                </Button>
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
                            >Dispute Active</span
                        >
                    </div>
                </div>

                <div
                    v-if="order.submited?.done"
                    class="rounded-lg border border-green-500/50 bg-green-500/10 p-4"
                >
                    <div class="flex items-center gap-2">
                        <CheckCircle2 class="h-5 w-5 text-green-500" />
                        <span class="font-semibold text-green-500"
                            >Order Completed</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>
