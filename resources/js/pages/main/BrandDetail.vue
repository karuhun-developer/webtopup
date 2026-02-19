<script setup lang="ts">
import { show } from '@/actions/App/Http/Controllers/Main/BrandController';
import { store } from '@/actions/App/Http/Controllers/Main/TransactionController';
import AccountDataForm from '@/components/brand-detail/AccountDataForm.vue';
import BrandBanner from '@/components/brand-detail/BrandBanner.vue';
import ContactDetailsForm from '@/components/brand-detail/ContactDetailsForm.vue';
import OrderSummary from '@/components/brand-detail/OrderSummary.vue';
import PaymentMethodSelection from '@/components/brand-detail/PaymentMethodSelection.vue';
import ProductSelection from '@/components/brand-detail/ProductSelection.vue';
import MainFooter from '@/components/MainFooter.vue';
import MainHeader from '@/components/MainHeader.vue';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { useSwal } from '@/composables/useSwal';
import { PPOBBrandDataItem, PPOBProductDataItem } from '@/types/cms/ppob';
import { FaqDataItem } from '@/types/cms/web';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import axios from 'axios';
import { ChevronDown } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    brand: PPOBBrandDataItem;
    faqs: FaqDataItem[];
}>();
const page = usePage();
const user = page.props.auth.user;
const setting = page.props.setting;
const appUrl = page.props.app_url;

// Form data using Inertia's useForm
const form = useForm({
    type: props.brand.settings?.type || 'id',
    account_id: '', // 36688862
    server_id: '', // 2052
    product_id: null as number | null,
    voucher_code: null as string | null,
    email: user?.email || '',
    name: user?.name || '',
    phone: user?.phone || '',
    payment_type: 'manual' as 'manual' | 'automatic',
    payment_method: null as string | null,
});

const resolvedUsername = ref<string | null>(null);
const checkError = ref<string | null>(null);
const isLoadingCheck = ref(false);
const discountAmount = ref(0);

const abortController = ref<AbortController | null>(null);

const checkGameAccount = useDebounceFn(async () => {
    // Check if brand is mobile legends
    if (!props.brand.name.toLowerCase().includes('mobile legend')) {
        return;
    }

    if (!form.account_id) return;
    if (inputType.value === 'id+server' && !form.server_id) return;

    // Cancel previous request
    if (abortController.value) {
        abortController.value.abort();
    }
    abortController.value = new AbortController();

    isLoadingCheck.value = true;
    resolvedUsername.value = null;
    checkError.value = null;

    try {
        const response = await axios.post(
            '/check-game-account',
            {
                account_id: form.account_id,
                server_id: form.server_id,
                slug: props.brand.slug,
            },
            {
                signal: abortController.value.signal,
            },
        );

        if (response.data.status || response.data.code === 200) {
            resolvedUsername.value =
                response.data.data?.username ||
                response.data.data?.data?.username;
        } else {
            checkError.value =
                response.data.message || 'Game ID tidak ditemukan';
        }
    } catch (error: any) {
        if (axios.isCancel(error)) {
            return;
        }
        resolvedUsername.value = null;
        checkError.value =
            error.response?.data?.message || 'Gagal mengecek Game ID';
    } finally {
        if (abortController.value?.signal.aborted) {
            // Do nothing if aborted, wait for the next request to finish or set loading false only if this was the last one?
            // Actually if we abort, we don't want to set isLoadingCheck to false immediately if a NEW request started.
            // But here we are in the flow of the *aborted* request.
            // Ideally we should track if *this* request is the specific one.
            // But simpler: if aborted, don't touch isLoadingCheck.
            // The NEW request would have set isLoadingCheck = true.
        } else {
            isLoadingCheck.value = false;
        }
    }
}, 800);

watch(
    () => [form.account_id, form.server_id],
    () => {
        checkGameAccount();
    },
);

// Computed
const inputType = computed(() => props.brand.settings?.type || 'id');
const labelId = computed(() => props.brand.settings?.label_id || 'ID');
const labelServer = computed(
    () => props.brand.settings?.label_server || 'Server',
);
const serverOptions = computed(() => props.brand.settings?.servers || []);

const selectedProductData = computed(() => {
    if (!form.product_id || !props.brand.products) return null;
    return props.brand.products.find((p) => p.id === form.product_id);
});

// Manual payment - Bank accounts
const manualBank = {
    id: setting?.manual_transfer_bank,
    name: setting?.manual_transfer_bank,
    account_number: setting?.manual_transfer_account_number,
    account_name: setting?.manual_transfer_account_name,
    img: setting?.manual_transfer_bank_logo,
};

// Automatic payment methods
const paymentMethods = [
    {
        id: 'qris',
        name: 'QRIS',
        fee: 0.007, // 0.7%
        action: 'multiply' as const,
        img: '/images/QRIS.svg',
    },
    {
        id: 'bca',
        name: 'Virtual Account BCA',
        fee: 4000, // Flat 4000 IDR
        action: 'add' as const,
        img: '/images/BCA.svg',
    },
    {
        id: 'mandiri',
        name: 'Virtual Account Mandiri',
        fee: 4000, // Flat 4000 IDR
        action: 'add' as const,
        img: '/images/MANDIRI.svg',
    },
    {
        id: 'bni',
        name: 'Virtual Account BNI',
        fee: 4000, // Flat 4000 IDR
        action: 'add' as const,
        img: '/images/BNI.svg',
    },
    {
        id: 'bri',
        name: 'Virtual Account BRI',
        fee: 4000, // Flat 4000 IDR
        action: 'add' as const,
        img: '/images/BRI.svg',
    },
    {
        id: 'permata',
        name: 'Virtual Account Permata',
        fee: 4000, // Flat 4000 IDR
        action: 'add' as const,
        img: '/images/PERMATA.svg',
    },
];

// Calculate total with fee
const totalAmount = computed(() => {
    if (!selectedProductData.value) return 0;

    const basePrice = selectedProductData.value.sell_price;
    const priceAfterDiscount = Math.max(0, basePrice - discountAmount.value);

    // Manual payment has no fee
    if (form.payment_type === 'manual') {
        return priceAfterDiscount;
    }

    // Automatic payment - calculate fee
    if (!form.payment_method) return priceAfterDiscount;

    const method = paymentMethods.find((m) => m.id === form.payment_method);
    if (!method) return priceAfterDiscount;

    if (method.action === 'multiply') {
        // Multiply: price * fee, then add to base price
        const feeAmount = priceAfterDiscount * method.fee;
        return priceAfterDiscount + feeAmount;
    } else {
        // Add: flat fee
        return priceAfterDiscount + method.fee;
    }
});

const { toast } = useSwal();

const handleVoucherApplied = (code: string, discount: number) => {
    form.voucher_code = code;
    discountAmount.value = discount;
};

const handleVoucherRemoved = () => {
    form.voucher_code = null;
    discountAmount.value = 0;
};

const handleCheckout = () => {
    // Validation
    if (!form.account_id) {
        toast.fire({
            icon: 'error',
            title: `Mohon isi ${labelId.value}`,
        });
        return;
    }

    if (inputType.value === 'id+server' && !form.server_id) {
        toast.fire({
            icon: 'error',
            title: `Mohon isi ${labelServer.value}`,
        });
        return;
    }

    if (!form.product_id) {
        toast.fire({
            icon: 'error',
            title: 'Mohon pilih produk',
        });
        return;
    }

    if (!form.name || !form.phone) {
        toast.fire({
            icon: 'error',
            title: 'Mohon lengkapi data kontak',
        });
        return;
    }

    if (!form.payment_method) {
        toast.fire({
            icon: 'error',
            title: 'Mohon pilih metode pembayaran',
        });
        return;
    }

    form.post(store().url, {
        preserveScroll: true,
        onSuccess: () => {
            toast.fire({
                icon: 'success',
                title: 'Transaksi berhasil dibuat',
            });
        },
        onError: (errors) => {
            toast.fire({
                icon: 'error',
                title: errors.account_id
                    ? 'Game ID/Server tidak valid'
                    : 'Terjadi kesalahan',
            });
        },
    });
};
</script>

<template>
    <Head>
        <title>{{ `${brand.name} - Top Up Murah & Cepat` }}</title>
        <link
            rel="icon"
            type="image/svg+xml"
            :href="setting?.icon || '/favicon.svg'"
        />
        <meta
            name="description"
            :content="`Top up ${brand.name} termurah dan terpercaya di ${setting?.title}. Proses instan, tersedia berbagai metode pembayaran. Beli ${brand.name} sekarang!`"
        />
        <meta
            name="keywords"
            :content="`top up ${brand.name}, beli ${brand.name}, harga ${brand.name}, ${brand.name} murah, ${setting?.title}, topup game`"
        />
        <meta name="author" :content="setting?.title" />
        <meta name="application-name" :content="setting?.title" />
        <meta
            property="og:title"
            :content="`${brand.name} - Top Up Murah & Cepat | ${setting?.title}`"
        />
        <meta
            property="og:description"
            :content="`Top up ${brand.name} termurah dan terpercaya di ${setting?.title}. Proses instan, tersedia berbagai metode pembayaran.`"
        />
        <meta property="og:url" :content="show({ brand: brand.slug }).url" />
        <meta
            property="og:image"
            :content="
                brand.image
                    ? brand.image
                    : `${appUrl}${setting?.favicon || '/favicon.svg'}`
            "
        />
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta
            property="twitter:title"
            :content="`${brand.name} - Top Up Murah & Cepat | ${setting?.title}`"
        />
        <meta
            property="twitter:description"
            :content="`Top up ${brand.name} termurah dan terpercaya di ${setting?.title}. Proses instan, tersedia berbagai metode pembayaran.`"
        />
        <meta
            property="twitter:image"
            :content="
                brand.image
                    ? brand.image
                    : `${appUrl}${setting?.favicon || '/favicon.svg'}`
            "
        />
        <meta property="twitter:card" content="summary_large_image" />
        <meta property="twitter:site" :content="setting?.title" />
        <meta
            property="image"
            :content="
                brand.image
                    ? brand.image
                    : `${appUrl}${setting?.favicon || '/favicon.svg'}`
            "
        />
        <meta property="canonical" :content="show({ brand: brand.slug }).url" />
        <meta name="robots" content="index, follow" />
        <component :is="'script'" type="application/ld+json">
            {{
                JSON.stringify({
                    '@context': 'https://schema.org',
                    '@type': 'Product',
                    name: brand.name,
                    description: `Top up ${brand.name} termurah dan terpercaya di ${setting?.title}.`,
                    image: brand.image,
                    brand: {
                        '@type': 'Brand',
                        name: brand.name,
                    },
                    offers: {
                        '@type': 'AggregateOffer',
                        lowPrice:
                            brand.products && brand.products.length > 0
                                ? Math.min(
                                      ...brand.products.map(
                                          (p) => p.sell_price,
                                      ),
                                  )
                                : 0,
                        highPrice:
                            brand.products && brand.products.length > 0
                                ? Math.max(
                                      ...brand.products.map(
                                          (p) => p.sell_price,
                                      ),
                                  )
                                : 0,
                        priceCurrency: 'IDR',
                        offerCount: brand.products ? brand.products.length : 0,
                    },
                })
            }}
        </component>
    </Head>

    <div class="flex min-h-screen flex-col bg-background">
        <!-- Header -->
        <MainHeader :show-back-button="true" />

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Banner Section -->
            <BrandBanner :brand="brand" />

            <!-- Form Content -->
            <div class="mx-auto max-w-7xl px-4 py-8">
                <div class="grid gap-6 lg:grid-cols-3">
                    <!-- Left Column - Form -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Step 1: Account Data -->
                        <AccountDataForm
                            :input-type="inputType"
                            :label-id="labelId"
                            :label-server="labelServer"
                            :server-options="serverOptions"
                            v-model:account-id="form.account_id"
                            v-model:server-id="form.server_id"
                            :form-errors="form.errors"
                            :resolved-username="resolvedUsername"
                            :check-error="checkError"
                            :is-loading-check="isLoadingCheck"
                        />

                        <!-- Step 2: Select Product -->
                        <ProductSelection
                            :products="brand.products || []"
                            v-model:selected-product="form.product_id"
                        />

                        <!-- Step 3: Contact Details -->
                        <ContactDetailsForm
                            v-model:email="form.email"
                            v-model:name="form.name"
                            v-model:phone="form.phone"
                            :form-errors="form.errors"
                        />

                        <!-- Step 4: Payment Method -->
                        <PaymentMethodSelection
                            v-model:payment-type="form.payment_type"
                            v-model:selected-payment="form.payment_method"
                            :manual-bank="manualBank"
                            :payment-methods="paymentMethods"
                        />

                        <!-- Brand Description -->
                        <div
                            v-if="brand.description"
                            class="rounded-lg border bg-card p-4 text-card-foreground"
                        >
                            <h2 class="mb-2 text-lg font-semibold">
                                Deskripsi
                            </h2>
                            <div
                                v-html="brand.description"
                                class="text-sm"
                            ></div>
                        </div>
                    </div>

                    <!-- Right Column - Summary -->
                    <div class="lg:col-span-1">
                        <OrderSummary
                            :brand-name="brand.name"
                            :selected-product="
                                selectedProductData as PPOBProductDataItem
                            "
                            :selected-payment="form.payment_method"
                            :payment-type="form.payment_type"
                            :total-amount="totalAmount"
                            :manual-bank="manualBank"
                            :payment-methods="paymentMethods"
                            :is-loading="form.processing"
                            @checkout="handleCheckout"
                            @voucher-applied="handleVoucherApplied"
                            @voucher-removed="handleVoucherRemoved"
                        />
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div v-if="faqs.length > 0" class="mx-auto mb-8 max-w-7xl px-4">
                <h2 class="mb-4 text-xl font-semibold">Pertanyaan Umum</h2>
                <div class="space-y-2">
                    <Collapsible
                        v-for="faq in faqs"
                        :key="faq.id"
                        v-slot="{ open }"
                        class="rounded-lg border bg-card text-card-foreground"
                    >
                        <CollapsibleTrigger
                            class="flex w-full items-center justify-between p-4 font-medium"
                        >
                            <span v-html="faq.question"></span>
                            <ChevronDown
                                class="h-4 w-4 transition-transform duration-200"
                                :class="{ 'rotate-180': open }"
                            />
                        </CollapsibleTrigger>
                        <CollapsibleContent
                            class="p-4 pt-0 text-sm text-muted-foreground"
                        >
                            <div v-html="faq.answer"></div>
                        </CollapsibleContent>
                    </Collapsible>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <MainFooter />
    </div>
</template>
