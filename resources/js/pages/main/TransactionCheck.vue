<script setup lang="ts">
import MainHeader from '@/components/MainHeader.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Transaction {
    id: string;
    product: string;
    date: string;
    amount: string;
    status: 'success' | 'pending' | 'failed';
}

const invoiceId = ref('');
const searchResult = ref<Transaction | null>(null);

const recentTransactions: Transaction[] = [
    {
        id: 'INV-001',
        product: 'Mobile Legends - 100 Diamonds',
        date: '2024-01-25 10:30',
        amount: 'Rp 25.000',
        status: 'success',
    },
    {
        id: 'INV-002',
        product: 'Free Fire - 50 Diamonds',
        date: '2024-01-24 15:20',
        amount: 'Rp 12.000',
        status: 'success',
    },
    {
        id: 'INV-003',
        product: 'PUBG Mobile - UC 60',
        date: '2024-01-23 09:15',
        amount: 'Rp 15.000',
        status: 'pending',
    },
];

const handleSearch = () => {
    if (!invoiceId.value) {
        alert('Please enter invoice ID');
        return;
    }
    // Mock search result
    searchResult.value = {
        id: invoiceId.value,
        product: 'Mobile Legends - 100 Diamonds',
        date: '2024-01-25 10:30',
        amount: 'Rp 25.000',
        status: 'success',
    };
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

const getStatusText = (status: string) => {
    switch (status) {
        case 'success':
            return 'Success';
        case 'pending':
            return 'Pending';
        case 'failed':
            return 'Failed';
        default:
            return 'Unknown';
    }
};
</script>

<template>
    <Head title="Check Transaction - GameStore" />

    <div class="min-h-screen bg-background">
        <!-- Header -->
        <MainHeader />

        <!-- Main Content -->
        <main class="mx-auto max-w-2xl px-4 py-16">
            <div class="text-center">
                <h1 class="mb-2 text-3xl font-bold text-foreground">
                    Cek Invoice Kamu dengan Mudah dan Cepat
                </h1>
                <p class="mb-8 text-muted-foreground">
                    Lihat detail pembelian kamu menggunakan nomor Invoice.
                </p>

                <!-- Search Form -->
                <div
                    class="mb-8 rounded-lg border border-border/50 bg-card p-6 shadow-sm"
                >
                    <h2
                        class="mb-4 text-left text-sm font-semibold text-foreground"
                    >
                        Cari detail pembelian kamu disini!
                    </h2>
                    <div class="flex gap-2">
                        <Input
                            v-model="invoiceId"
                            placeholder="Masukkan Invoice / Order ID Kamu Disini (Contoh: OURAXXXXXXXXX)"
                            class="flex-1"
                            @keyup.enter="handleSearch"
                        />
                        <Button
                            class="bg-primary hover:bg-primary/90"
                            @click="handleSearch"
                        >
                            Cek Invoice
                        </Button>
                    </div>
                </div>

                <!-- Search Result -->
                <div
                    v-if="searchResult"
                    class="rounded-lg border border-border/50 bg-card p-6 text-left shadow-sm"
                >
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-foreground">
                            Detail Transaksi
                        </h3>
                        <Badge :class="getStatusColor(searchResult.status)">
                            {{ getStatusText(searchResult.status) }}
                        </Badge>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div
                            class="flex justify-between border-b border-border/30 pb-2"
                        >
                            <span class="text-muted-foreground">Tanggal</span>
                            <span class="font-medium text-foreground">{{
                                searchResult.date
                            }}</span>
                        </div>
                        <div
                            class="flex justify-between border-b border-border/30 pb-2"
                        >
                            <span class="text-muted-foreground"
                                >Nomor Invoice</span
                            >
                            <span class="font-medium text-foreground">{{
                                searchResult.id
                            }}</span>
                        </div>
                        <div
                            class="flex justify-between border-b border-border/30 pb-2"
                        >
                            <span class="text-muted-foreground"
                                >No. Identifikasi</span
                            >
                            <span class="font-medium text-foreground"
                                >••••••••123</span
                            >
                        </div>
                        <div
                            class="flex justify-between border-b border-border/30 pb-2"
                        >
                            <span class="text-muted-foreground">Item</span>
                            <span class="font-medium text-foreground">{{
                                searchResult.product
                            }}</span>
                        </div>
                        <div class="flex justify-between pt-2">
                            <span
                                class="text-base font-semibold text-foreground"
                                >Harga</span
                            >
                            <span class="text-base font-bold text-primary">{{
                                searchResult.amount
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div v-if="!searchResult" class="mt-12">
                    <h2 class="mb-4 text-xl font-bold text-foreground">
                        Transaksi Real-Time
                    </h2>
                    <p class="mb-4 text-sm text-muted-foreground">
                        Berikut list Real-Time untuk pelanggan terbaru
                        GameStore.
                    </p>

                    <div
                        class="overflow-hidden rounded-lg border border-border/50 bg-card shadow-sm"
                    >
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-border/50 bg-muted">
                                <tr>
                                    <th
                                        class="px-4 py-3 font-semibold text-foreground"
                                    >
                                        Tanggal
                                    </th>
                                    <th
                                        class="px-4 py-3 font-semibold text-foreground"
                                    >
                                        Nomor Invoice
                                    </th>
                                    <th
                                        class="px-4 py-3 font-semibold text-foreground"
                                    >
                                        No. Identifikasi
                                    </th>
                                    <th
                                        class="px-4 py-3 font-semibold text-foreground"
                                    >
                                        Harga
                                    </th>
                                    <th
                                        class="px-4 py-3 font-semibold text-foreground"
                                    >
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/30">
                                <tr
                                    v-for="i in 5"
                                    :key="i"
                                    class="hover:bg-muted/50"
                                >
                                    <td class="px-4 py-3 text-muted-foreground">
                                        29-01-2026 20:05:16
                                    </td>
                                    <td
                                        class="px-4 py-3 font-medium text-foreground"
                                    >
                                        OURA••••••••••••
                                    </td>
                                    <td class="px-4 py-3 text-muted-foreground">
                                        ••••••••123
                                    </td>
                                    <td
                                        class="px-4 py-3 font-medium text-foreground"
                                    >
                                        IDR 3 juta
                                    </td>
                                    <td class="px-4 py-3">
                                        <Badge class="bg-emerald-500"
                                            >Pending</Badge
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
