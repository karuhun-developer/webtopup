<script setup lang="ts">
import MainFooter from '@/components/MainFooter.vue';
import MainHeader from '@/components/MainHeader.vue';
import DepositTab from '@/components/profile/DepositTab.vue';
import PasswordTab from '@/components/profile/PasswordTab.vue';
import ProfileTab from '@/components/profile/ProfileTab.vue';
import TransactionTab from '@/components/profile/TransactionTab.vue';
import { PaginationItem } from '@/types';
import { OrderDataItem } from '@/types/cms/main';
import { Head } from '@inertiajs/vue3';
import { Lock, Receipt, User } from 'lucide-vue-next';
import { ref } from 'vue';

type Tab = 'profile' | 'password' | 'transactions' | 'deposit';

defineProps<{
    mustVerifyEmail: boolean;
    status?: string;
    transactions: PaginationItem<OrderDataItem>;
    balance: number;
}>();

const activeTab = ref<Tab>('profile');
</script>

<template>
    <Head title="Profile" />

    <div class="flex min-h-screen flex-col bg-background">
        <!-- Header -->
        <MainHeader />

        <!-- Main Content -->
        <main class="flex-1">
            <div class="mx-auto max-w-7xl px-4 py-8">
                <h1 class="mb-6 text-2xl font-bold text-foreground">
                    My Profile
                </h1>

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
                                    <User class="h-5 w-5" />
                                    Profile
                                </button>

                                <button
                                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left text-sm font-medium transition-colors"
                                    :class="
                                        activeTab === 'password'
                                            ? 'bg-primary text-white'
                                            : 'text-muted-foreground hover:bg-muted'
                                    "
                                    @click="activeTab = 'password'"
                                >
                                    <Lock class="h-5 w-5" />
                                    Password
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
                                    <Receipt class="h-5 w-5" />
                                    Transactions
                                </button>

                                <!-- <button
                                    class="flex w-full items-center gap-3 rounded-lg px-4 py-3 text-left text-sm font-medium transition-colors"
                                    :class="
                                        activeTab === 'deposit'
                                            ? 'bg-primary text-white'
                                            : 'text-muted-foreground hover:bg-muted'
                                    "
                                    @click="activeTab = 'deposit'"
                                >
                                    <Wallet class="h-5 w-5" />
                                    Deposit
                                </button> -->
                            </nav>
                        </div>
                    </div>

                    <!-- Content Area -->
                    <div class="lg:col-span-3">
                        <ProfileTab
                            v-if="activeTab === 'profile'"
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                        />

                        <PasswordTab v-if="activeTab === 'password'" />

                        <TransactionTab
                            v-if="activeTab === 'transactions'"
                            :transactions="transactions"
                        />

                        <DepositTab
                            v-if="activeTab === 'deposit'"
                            :balance="balance"
                        />
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <MainFooter />
    </div>
</template>
