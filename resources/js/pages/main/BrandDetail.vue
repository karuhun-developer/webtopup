<script setup lang="ts">
import MainFooter from '@/components/MainFooter.vue';
import MainHeader from '@/components/MainHeader.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { PPOBBrandDataItem } from '@/types/cms/ppob';
import { Head } from '@inertiajs/vue3';
import { BadgeCheck, Clock, MessageCircle, ShieldCheck } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const props = defineProps<{
    brand: PPOBBrandDataItem;
}>();

// Form state
const accountId = ref('');
const serverId = ref('');
const selectedProduct = ref<number | null>(null);
const email = ref('');
const name = ref('');
const phone = ref('');
const selectedPayment = ref<string | null>(null);

// Computed
const inputType = computed(() => props.brand.settings?.type || 'id');
const labelId = computed(() => props.brand.settings?.label_id || 'ID');
const labelServer = computed(
    () => props.brand.settings?.label_server || 'Server',
);
const serverOptions = computed(() => props.brand.settings?.servers || []);
const hasServerDropdown = computed(
    () => inputType.value === 'id+server' && serverOptions.value.length > 0,
);

const selectedProductData = computed(() => {
    if (!selectedProduct.value || !props.brand.products) return null;
    return props.brand.products.find((p) => p.id === selectedProduct.value);
});

// Payment methods (placeholder)
const paymentMethods = [
    { id: 'dana', name: 'DANA', fee: 500 },
    { id: 'ovo', name: 'OVO', fee: 500 },
    { id: 'gopay', name: 'GoPay', fee: 500 },
    { id: 'qris', name: 'QRIS', fee: 0 },
    { id: 'bca', name: 'BCA Virtual Account', fee: 4000 },
    { id: 'bni', name: 'BNI Virtual Account', fee: 4000 },
    { id: 'mandiri', name: 'Mandiri Virtual Account', fee: 4000 },
];

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

const handleCheckout = () => {
    // Validation
    if (!accountId.value) {
        alert(`Mohon isi ${labelId.value}`);
        return;
    }

    if (inputType.value === 'id+server' && !serverId.value) {
        alert(`Mohon isi ${labelServer.value}`);
        return;
    }

    if (!selectedProduct.value) {
        alert('Mohon pilih produk');
        return;
    }

    if (!email.value || !name.value || !phone.value) {
        alert('Mohon lengkapi data kontak');
        return;
    }

    if (!selectedPayment.value) {
        alert('Mohon pilih metode pembayaran');
        return;
    }

    alert('Checkout functionality will be implemented');
};
</script>

<template>
    <Head :title="`${brand.name} - Top Up`" />

    <div class="flex min-h-screen flex-col bg-background">
        <!-- Header -->
        <MainHeader :show-back-button="true" />

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Banner Section -->
            <div
                class="relative h-64 overflow-hidden bg-gradient-to-r from-primary/20 to-primary/5"
            >
                <img
                    v-if="brand.banner"
                    :src="brand.banner"
                    :alt="brand.name"
                    class="h-full w-full object-cover"
                />
                <div
                    class="absolute inset-0 bg-gradient-to-t from-background/80 to-transparent"
                ></div>

                <!-- Brand Info Overlay -->
                <div class="absolute right-0 bottom-0 left-0 p-6">
                    <div class="mx-auto max-w-5xl">
                        <div class="flex items-end gap-4">
                            <!-- Brand Logo -->
                            <div
                                class="h-20 w-20 overflow-hidden rounded-lg border-4 border-background bg-card shadow-lg"
                            >
                                <img
                                    v-if="brand.image"
                                    :src="brand.image"
                                    :alt="brand.name"
                                    class="h-full w-full object-cover"
                                />
                            </div>

                            <!-- Brand Name & Features -->
                            <div class="flex-1">
                                <h1
                                    class="mb-2 text-2xl font-bold text-foreground"
                                >
                                    {{ brand.name }}
                                </h1>
                                <div class="flex flex-wrap gap-2">
                                    <div
                                        class="flex items-center gap-1 rounded-full bg-background/80 px-3 py-1 text-xs font-medium text-foreground"
                                    >
                                        <Clock class="h-3 w-3" />
                                        Proses Cepat
                                    </div>
                                    <div
                                        class="flex items-center gap-1 rounded-full bg-background/80 px-3 py-1 text-xs font-medium text-foreground"
                                    >
                                        <MessageCircle class="h-3 w-3" />
                                        Layanan Chat 24/7
                                    </div>
                                    <div
                                        class="flex items-center gap-1 rounded-full bg-background/80 px-3 py-1 text-xs font-medium text-foreground"
                                    >
                                        <ShieldCheck class="h-3 w-3" />
                                        Pembayaran Aman
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <div class="mx-auto max-w-5xl px-4 py-8">
                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Left Column - Form -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Step 1: Account Data -->
                        <section
                            class="rounded-lg border border-border/50 bg-card p-6 shadow-sm"
                        >
                            <h2
                                class="mb-4 flex items-center gap-2 text-lg font-bold text-foreground"
                            >
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground"
                                    >1</span
                                >
                                Masukkan Data Akun
                            </h2>

                            <div
                                class="grid gap-4"
                                :class="
                                    inputType === 'id+server'
                                        ? 'sm:grid-cols-2'
                                        : ''
                                "
                            >
                                <!-- ID Input -->
                                <div>
                                    <Label :for="'account-id'">{{
                                        labelId
                                    }}</Label>
                                    <Input
                                        id="account-id"
                                        v-model="accountId"
                                        :placeholder="`Masukkan ${labelId}`"
                                        class="mt-1"
                                    />
                                </div>

                                <!-- Server Input/Dropdown (conditional) -->
                                <div v-if="inputType === 'id+server'">
                                    <Label :for="'server-id'">{{
                                        labelServer
                                    }}</Label>

                                    <!-- Dropdown if servers available -->
                                    <Select
                                        v-if="hasServerDropdown"
                                        v-model="serverId"
                                    >
                                        <SelectTrigger
                                            id="server-id"
                                            class="mt-1"
                                        >
                                            <SelectValue
                                                :placeholder="`Pilih ${labelServer}`"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="server in serverOptions"
                                                :key="server"
                                                :value="server"
                                            >
                                                {{ server }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>

                                    <!-- Text input if no servers -->
                                    <Input
                                        v-else
                                        id="server-id"
                                        v-model="serverId"
                                        :placeholder="`Masukkan ${labelServer}`"
                                        class="mt-1"
                                    />
                                </div>
                            </div>
                        </section>

                        <!-- Step 2: Select Product -->
                        <section
                            class="rounded-lg border border-border/50 bg-card p-6 shadow-sm"
                        >
                            <h2
                                class="mb-4 flex items-center gap-2 text-lg font-bold text-foreground"
                            >
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground"
                                    >2</span
                                >
                                Pilih Produk
                            </h2>

                            <div
                                v-if="
                                    brand.products && brand.products.length > 0
                                "
                                class="grid grid-cols-2 gap-3 sm:grid-cols-3"
                            >
                                <button
                                    v-for="product in brand.products"
                                    :key="product.id"
                                    class="rounded-lg border-2 p-4 text-left transition-all"
                                    :class="
                                        selectedProduct === product.id
                                            ? 'border-primary bg-primary/5'
                                            : 'border-border/50 hover:border-primary/50'
                                    "
                                    @click="selectedProduct = product.id"
                                >
                                    <div class="mb-2">
                                        <img
                                            v-if="product.image"
                                            :src="product.image"
                                            :alt="product.name"
                                            class="h-12 w-12 object-contain"
                                        />
                                    </div>
                                    <div
                                        class="text-sm font-semibold text-foreground"
                                    >
                                        {{ product.name }}
                                    </div>
                                    <div
                                        class="mt-2 text-sm font-bold text-primary"
                                    >
                                        {{ formatPrice(product.sell_price) }}
                                    </div>
                                </button>
                            </div>
                            <div
                                v-else
                                class="py-8 text-center text-sm text-muted-foreground"
                            >
                                Tidak ada produk tersedia
                            </div>
                        </section>

                        <!-- Step 3: Contact Details -->
                        <section
                            class="rounded-lg border border-border/50 bg-card p-6 shadow-sm"
                        >
                            <h2
                                class="mb-4 flex items-center gap-2 text-lg font-bold text-foreground"
                            >
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground"
                                    >3</span
                                >
                                Detail Kontak
                            </h2>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <Label for="email">Email</Label>
                                    <Input
                                        id="email"
                                        v-model="email"
                                        type="email"
                                        placeholder="email@example.com"
                                        class="mt-1"
                                    />
                                </div>
                                <div>
                                    <Label for="name">Nama</Label>
                                    <Input
                                        id="name"
                                        v-model="name"
                                        placeholder="Nama lengkap"
                                        class="mt-1"
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <Label for="phone">Nomor Telepon</Label>
                                    <Input
                                        id="phone"
                                        v-model="phone"
                                        type="tel"
                                        placeholder="08xxxxxxxxxx"
                                        class="mt-1"
                                    />
                                </div>
                            </div>
                        </section>

                        <!-- Step 4: Payment Method (Placeholder) -->
                        <section
                            class="rounded-lg border border-border/50 bg-card p-6 shadow-sm"
                        >
                            <h2
                                class="mb-4 flex items-center gap-2 text-lg font-bold text-foreground"
                            >
                                <span
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-primary text-sm font-bold text-primary-foreground"
                                    >4</span
                                >
                                Pilih Metode Pembayaran
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
                                    <span class="text-sm text-muted-foreground">
                                        Fee: {{ formatPrice(method.fee) }}
                                    </span>
                                </button>
                            </div>

                            <p class="mt-4 text-xs text-muted-foreground">
                                * Metode pembayaran ini adalah contoh.
                                Implementasi aktual akan ditambahkan nanti.
                            </p>
                        </section>
                    </div>

                    <!-- Right Column - Summary -->
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
                                    <span class="font-medium text-foreground">
                                        {{ brand.name }}
                                    </span>
                                </div>

                                <div
                                    v-if="selectedProductData"
                                    class="flex justify-between"
                                >
                                    <span class="text-muted-foreground"
                                        >Item:</span
                                    >
                                    <span class="font-medium text-foreground">
                                        {{ selectedProductData.name }}
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
                                            )?.name
                                        }}
                                    </span>
                                </div>

                                <div class="border-t border-border/30 pt-3">
                                    <div
                                        class="flex justify-between text-base font-bold"
                                    >
                                        <span class="text-foreground"
                                            >Total:</span
                                        >
                                        <span class="text-primary">
                                            {{
                                                selectedProductData
                                                    ? formatPrice(
                                                          selectedProductData.sell_price,
                                                      )
                                                    : 'Rp 0'
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <Button class="mt-6 w-full" @click="handleCheckout">
                                <BadgeCheck class="mr-2 h-4 w-4" />
                                Beli Sekarang
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <MainFooter />
    </div>
</template>
