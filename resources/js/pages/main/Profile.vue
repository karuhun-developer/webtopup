<script setup lang="ts">
import MainHeader from '@/components/MainHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

type Tab = 'profile' | 'transactions' | 'deposit';

interface Transaction {
    id: number;
    date: string;
    product: string;
    amount: string;
    status: 'success' | 'pending' | 'failed';
}

const activeTab = ref<Tab>('profile');

// Profile form data
const profileForm = ref({
    name: 'John Doe',
    email: 'john@example.com',
    whatsapp: '+62812345678',
    currentPassword: '',
    newPassword: '',
    confirmPassword: '',
});

// Mock transactions
const transactions = ref<Transaction[]>([
    {
        id: 1,
        date: '29-01-2026 20:05:16',
        product: 'Mobile Legends - 284 Diamonds',
        amount: 'Rp 71.500',
        status: 'success',
    },
    {
        id: 2,
        date: '28-01-2026 15:30:22',
        product: 'Free Fire - 100 Diamonds',
        amount: 'Rp 15.000',
        status: 'success',
    },
    {
        id: 3,
        date: '27-01-2026 10:15:45',
        product: 'PUBG Mobile - 600 UC',
        amount: 'Rp 85.000',
        status: 'pending',
    },
]);

// Deposit data
const balance = ref('Rp 150.000');
const depositAmount = ref('');

const handleUpdateProfile = () => {
    alert('Profile update functionality will be implemented');
};

const handleDeposit = () => {
    if (!depositAmount.value) {
        alert('Please enter deposit amount');
        return;
    }
    alert('Deposit functionality will be implemented');
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'success':
            return 'bg-emerald-500';
        case 'pending':
            return 'bg-yellow-500';
        case 'failed':
            return 'bg-red-500';
        default:
            return 'bg-gray-500';
    }
};
</script>

<template>
    <Head title="Profile" />

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <MainHeader :show-search="true" />

        <!-- Main Content -->
        <main class="mx-auto max-w-5xl px-4 py-8">
            <h1 class="mb-6 text-2xl font-bold text-foreground">My Profile</h1>

            <div class="grid gap-6 lg:grid-cols-4">
                <!-- Sidebar Tabs -->
                <div class="lg:col-span-1">
                    <div
                        class="rounded-lg border border-border/50 bg-card p-4 shadow-sm"
                    >
                        <nav class="space-y-1">
                            <button
                                class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left text-sm font-medium transition-colors"
                                :class="
                                    activeTab === 'profile'
                                        ? 'bg-primary text-white'
                                        : 'text-muted-foreground hover:bg-muted'
                                "
                                @click="activeTab = 'profile'"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                                Profile
                            </button>
                            <button
                                class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left text-sm font-medium transition-colors"
                                :class="
                                    activeTab === 'transactions'
                                        ? 'bg-primary text-white'
                                        : 'text-muted-foreground hover:bg-muted'
                                "
                                @click="activeTab = 'transactions'"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                    />
                                </svg>
                                Transactions
                            </button>
                            <button
                                class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left text-sm font-medium transition-colors"
                                :class="
                                    activeTab === 'deposit'
                                        ? 'bg-primary text-white'
                                        : 'text-muted-foreground hover:bg-muted'
                                "
                                @click="activeTab = 'deposit'"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                Deposit
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="lg:col-span-3">
                    <!-- Profile Tab -->
                    <div
                        v-if="activeTab === 'profile'"
                        class="rounded-lg border border-border/50 bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-6 text-lg font-bold text-foreground">
                            Update Profile
                        </h2>
                        <form
                            class="space-y-4"
                            @submit.prevent="handleUpdateProfile"
                        >
                            <div>
                                <Label for="name">Name</Label>
                                <Input
                                    id="name"
                                    v-model="profileForm.name"
                                    class="mt-1"
                                />
                            </div>
                            <div>
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    v-model="profileForm.email"
                                    type="email"
                                    class="mt-1"
                                />
                            </div>
                            <div>
                                <Label for="whatsapp">WhatsApp</Label>
                                <Input
                                    id="whatsapp"
                                    v-model="profileForm.whatsapp"
                                    class="mt-1"
                                />
                            </div>

                            <div class="border-t border-border/30 pt-4">
                                <h3
                                    class="mb-4 text-sm font-semibold text-foreground"
                                >
                                    Change Password
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <Label for="currentPassword"
                                            >Current Password</Label
                                        >
                                        <Input
                                            id="currentPassword"
                                            v-model="
                                                profileForm.currentPassword
                                            "
                                            type="password"
                                            class="mt-1"
                                        />
                                    </div>
                                    <div>
                                        <Label for="newPassword"
                                            >New Password</Label
                                        >
                                        <Input
                                            id="newPassword"
                                            v-model="profileForm.newPassword"
                                            type="password"
                                            class="mt-1"
                                        />
                                    </div>
                                    <div>
                                        <Label for="confirmPassword"
                                            >Confirm New Password</Label
                                        >
                                        <Input
                                            id="confirmPassword"
                                            v-model="
                                                profileForm.confirmPassword
                                            "
                                            type="password"
                                            class="mt-1"
                                        />
                                    </div>
                                </div>
                            </div>

                            <Button
                                type="submit"
                                class="w-full bg-primary hover:bg-primary/90"
                            >
                                Update Profile
                            </Button>
                        </form>
                    </div>

                    <!-- Transactions Tab -->
                    <div
                        v-if="activeTab === 'transactions'"
                        class="rounded-lg border border-border/50 bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-6 text-lg font-bold text-foreground">
                            Transaction History
                        </h2>
                        <div class="space-y-4">
                            <div
                                v-for="transaction in transactions"
                                :key="transaction.id"
                                class="rounded-lg border border-border/50 p-4 hover:border-primary/50"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div
                                            class="mb-1 flex items-center gap-2"
                                        >
                                            <h3
                                                class="font-semibold text-foreground"
                                            >
                                                {{ transaction.product }}
                                            </h3>
                                            <Badge
                                                :class="
                                                    getStatusColor(
                                                        transaction.status,
                                                    )
                                                "
                                            >
                                                {{ transaction.status }}
                                            </Badge>
                                        </div>
                                        <p
                                            class="text-sm text-muted-foreground"
                                        >
                                            {{ transaction.date }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-primary">
                                            {{ transaction.amount }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deposit Tab -->
                    <div
                        v-if="activeTab === 'deposit'"
                        class="rounded-lg border border-border/50 bg-card p-6 shadow-sm"
                    >
                        <h2 class="mb-6 text-lg font-bold text-foreground">
                            Deposit Balance
                        </h2>

                        <!-- Current Balance -->
                        <div
                            class="mb-6 rounded-lg bg-gradient-to-r from-primary to-primary/80 p-6 text-white"
                        >
                            <p class="mb-2 text-sm opacity-90">
                                Current Balance
                            </p>
                            <p class="text-3xl font-bold">{{ balance }}</p>
                        </div>

                        <!-- Deposit Form -->
                        <form class="space-y-4" @submit.prevent="handleDeposit">
                            <div>
                                <Label for="depositAmount"
                                    >Deposit Amount</Label
                                >
                                <Input
                                    id="depositAmount"
                                    v-model="depositAmount"
                                    placeholder="Enter amount (e.g., 100000)"
                                    class="mt-1"
                                />
                            </div>

                            <div>
                                <Label>Payment Method</Label>
                                <div class="mt-2 space-y-2">
                                    <button
                                        type="button"
                                        class="flex w-full items-center justify-between rounded-lg border-2 border-border/50 p-4 transition-all hover:border-primary"
                                    >
                                        <span
                                            class="font-medium text-foreground"
                                            >DANA</span
                                        >
                                        <span
                                            class="text-sm text-muted-foreground"
                                            >Fee: Rp 500</span
                                        >
                                    </button>
                                    <button
                                        type="button"
                                        class="flex w-full items-center justify-between rounded-lg border-2 border-border/50 p-4 transition-all hover:border-primary"
                                    >
                                        <span
                                            class="font-medium text-foreground"
                                            >OVO</span
                                        >
                                        <span
                                            class="text-sm text-muted-foreground"
                                            >Fee: Rp 500</span
                                        >
                                    </button>
                                    <button
                                        type="button"
                                        class="flex w-full items-center justify-between rounded-lg border-2 border-border/50 p-4 transition-all hover:border-primary"
                                    >
                                        <span
                                            class="font-medium text-foreground"
                                            >BCA Virtual Account</span
                                        >
                                        <span
                                            class="text-sm text-muted-foreground"
                                            >Fee: Rp 4.000</span
                                        >
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
                </div>
            </div>
        </main>
    </div>
</template>
