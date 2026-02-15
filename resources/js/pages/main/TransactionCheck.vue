<script setup lang="ts">
import { check } from '@/actions/App/Http/Controllers/Main/TransactionController';
import MainFooter from '@/components/MainFooter.vue';
import MainHeader from '@/components/MainHeader.vue';
import StatusBadge from '@/components/transaction/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useSwal } from '@/composables/useSwal';
import { OrderDataItem } from '@/types/cms/main';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';

defineProps<{
    latestOrder: OrderDataItem[];
}>();

const form = useForm({
    reference: '',
});

const page = usePage();
const setting = page.props.setting;
const appUrl = page.props.app_url;
const { toast } = useSwal();

const handleSearch = () => {
    if (!form.reference) {
        return;
    }

    form.get(check().url, {
        onError: (errors) => {
            toast.fire({
                icon: 'error',
                title: 'Transaksi tidak ditemukan. Silakan periksa kembali nomor Invoice kamu.',
            });
        },
    });
};
</script>

<template>
    <Head>
        <title>Cek Transaksi</title>
        <link
            rel="icon"
            type="image/svg+xml"
            :href="setting?.favicon || '/favicon.svg'"
        />
        <meta
            name="description"
            :content="`Cek status transaksi top up game Anda di ${setting?.title}. Masukkan nomor transaksi untuk melihat detail pesanan Anda.`"
        />
        <meta
            name="keywords"
            :content="`cek transaksi, status pesanan, lacak pesanan, ${setting?.title}, topup game`"
        />
        <meta name="author" :content="setting?.title" />
        <meta name="application-name" :content="setting?.title" />
        <meta
            property="og:title"
            :content="`Cek Transaksi - ${setting?.title}`"
        />
        <meta
            property="og:description"
            :content="`Cek status transaksi top up game Anda di ${setting?.title}. Masukkan nomor transaksi untuk melihat detail pesanan Anda.`"
        />
        <meta property="og:url" :content="check().url" />
        <meta
            property="og:image"
            :content="`${appUrl}${setting?.favicon || '/favicon.svg'}`"
        />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta
            property="twitter:title"
            :content="`Cek Transaksi - ${setting?.title}`"
        />
        <meta
            property="twitter:description"
            :content="`Cek status transaksi top up game Anda di ${setting?.title}. Masukkan nomor transaksi untuk melihat detail pesanan Anda.`"
        />
        <meta
            property="twitter:image"
            :content="`${appUrl}${setting?.favicon || '/favicon.svg'}`"
        />
        <meta property="twitter:card" content="summary_large_image" />
        <meta property="twitter:site" :content="setting?.title" />
        <meta
            property="image"
            :content="`${appUrl}${setting?.favicon || '/favicon.svg'}`"
        />
        <meta property="canonical" :content="check().url" />
        <meta name="robots" content="index, follow" />
        <component :is="'script'" type="application/ld+json">
            {{
                JSON.stringify({
                    '@context': 'https://schema.org',
                    '@type': 'WebPage',
                    name: 'Cek Transaksi',
                    description: `Cek status transaksi top up game Anda di ${setting?.title}. Masukkan nomor transaksi untuk melihat detail pesanan Anda.`,
                    url: check().url,
                })
            }}
        </component>
    </Head>

    <div class="flex min-h-screen flex-col bg-background">
        <!-- Header -->
        <MainHeader />

        <!-- Main Content -->
        <main class="mx-auto w-full max-w-4xl px-4 py-16">
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
                            v-model="form.reference"
                            placeholder="Masukkan Invoice / Order ID Kamu Disini (Contoh: TRX-20250907-00002)"
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

                <!-- Recent Transactions -->
                <div class="mt-12">
                    <h2 class="mb-4 text-xl font-bold text-foreground">
                        Transaksi Real-Time
                    </h2>
                    <p class="mb-4 text-sm text-muted-foreground">
                        Berikut list Real-Time untuk pelanggan terbaru.
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
                                    v-for="order in latestOrder"
                                    :key="order.id"
                                    class="hover:bg-muted/50"
                                >
                                    <td class="px-4 py-3 text-muted-foreground">
                                        {{
                                            dayjs(order.created_at)
                                                .locale('id')
                                                .format('DD MMM YYYY, HH:mm')
                                        }}
                                    </td>
                                    <td
                                        class="px-4 py-3 font-mono font-medium text-foreground"
                                    >
                                        {{ order.reference }}
                                    </td>
                                    <td class="px-4 py-3 text-muted-foreground">
                                        {{ order.submited.account_id }}
                                    </td>
                                    <td
                                        class="px-4 py-3 font-medium text-foreground"
                                    >
                                        {{ order.total_amount }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <StatusBadge
                                            :status="order.payment_status"
                                            type="payment"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <MainFooter />
    </div>
</template>
