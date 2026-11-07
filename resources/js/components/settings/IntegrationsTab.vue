<script setup lang="ts">
import InputError from '@/components/InputError.vue';
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
import { useSwal } from '@/composables/useSwal';
import { SettingValue } from '@/types/cms/setting';
import { Copy } from 'lucide-vue-next';

defineProps<{
    setting?: SettingValue;
    errors: Record<string, string>;
    configuredCredentials: Record<string, boolean>;
    callbackUrls: Record<string, string>;
}>();

const { toast } = useSwal();
const copyCallbackUrl = async (url: string) => {
    try {
        await navigator.clipboard.writeText(url);
        toast.fire({ icon: 'success', title: 'URL webhook disalin.' });
    } catch {
        toast.fire({ icon: 'error', title: 'Salin URL dari kolom webhook.' });
    }
};

const providers = [
    {
        name: 'H2H Karuhun',
        callbackKey: 'h2h',
        href: 'https://h2h.karuhundeveloper.com',
        fields: [
            { key: 'h2h_api_key', label: 'API key' },
            { key: 'h2h_webhook_secret', label: 'Webhook secret' },
        ],
        urlKey: 'h2h_base_url',
        url: 'https://h2h.karuhundeveloper.com',
    },
    {
        name: 'Digiflazz',
        callbackKey: 'digiflazz',
        href: 'https://digiflazz.com',
        fields: [
            { key: 'digiflazz_username', label: 'Username' },
            { key: 'digiflazz_api_key', label: 'API key' },
            { key: 'digiflazz_webhook_secret', label: 'Webhook secret' },
        ],
    },
    {
        name: 'API Games',
        href: 'https://apigames.id',
        fields: [
            { key: 'apigames_merchant_id', label: 'Merchant ID' },
            { key: 'apigames_secret_key', label: 'Secret key' },
        ],
        urlKey: 'apigames_base_url',
        url: 'https://v1.apigames.id',
    },
    {
        name: 'QRIS Karuhun',
        href: 'https://qris.karuhundeveloper.com',
        fields: [{ key: 'qris_api_key', label: 'API key' }],
        urlKey: 'qris_base_url',
        url: 'https://qris.karuhundeveloper.com',
    },
    {
        name: 'Midtrans',
        callbackKey: 'midtrans',
        href: 'https://midtrans.com',
        fields: [
            { key: 'midtrans_server_key', label: 'Server key' },
            { key: 'midtrans_client_key', label: 'Client key' },
        ],
    },
    {
        name: 'Paywuz',
        callbackKey: 'paywuz',
        href: 'https://paywuz.id',
        fields: [{ key: 'paywuz_api_key', label: 'Project API key' }],
        urlKey: 'paywuz_base_url',
        url: 'https://api.paywuz.id',
    },
];
const choices = [
    {
        key: 'supplier_provider',
        label: 'Supplier produk default',
        default: 'h2h',
        options: [
            { value: 'h2h', label: 'H2H Karuhun' },
            { value: 'digiflazz', label: 'Digiflazz' },
        ],
    },
    {
        key: 'username_provider',
        label: 'Provider cek username',
        default: 'h2h',
        options: [
            { value: 'h2h', label: 'H2H Karuhun' },
            { value: 'apigames', label: 'API Games' },
        ],
    },
    {
        key: 'payment_gateway',
        label: 'Payment gateway',
        default: 'karuhun_qris',
        options: [
            { value: 'karuhun_qris', label: 'QRIS Karuhun' },
            { value: 'midtrans', label: 'Midtrans' },
            { value: 'paywuz', label: 'Paywuz' },
        ],
    },
];
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-6 md:grid-cols-3">
            <div v-for="choice in choices" :key="choice.key" class="grid gap-2">
                <Label :for="choice.key">{{ choice.label }}</Label>
                <Select
                    :name="'value[' + choice.key + ']'"
                    :default-value="
                        String(
                            setting?.[choice.key as keyof SettingValue] ??
                                choice.default,
                        )
                    "
                >
                    <SelectTrigger :id="choice.key" class="w-full"
                        ><SelectValue
                    /></SelectTrigger>
                    <SelectContent
                        ><SelectItem
                            v-for="option in choice.options"
                            :key="option.value"
                            :value="option.value"
                            >{{ option.label }}</SelectItem
                        ></SelectContent
                    >
                </Select>
                <InputError :message="errors['value.' + choice.key]" />
            </div>
        </div>
        <p class="text-sm text-muted-foreground">
            Supplier default dipakai saat import. Pesanan mengikuti provider
            produk; perubahan gateway berlaku untuk pembayaran baru. Kosongkan
            credential untuk mempertahankan yang tersimpan.
        </p>
        <div class="grid gap-6 lg:grid-cols-2">
            <section
                v-for="provider in providers"
                :key="provider.name"
                class="space-y-4 rounded-lg border p-5"
            >
                <div class="flex items-center justify-between gap-4">
                    <h3 class="font-semibold">{{ provider.name }}</h3>
                    <a
                        :href="provider.href"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-sm text-primary underline underline-offset-4"
                        >Buka website ↗</a
                    >
                </div>
                <div v-if="provider.urlKey" class="grid gap-2">
                    <Label :for="provider.urlKey">Base URL</Label>
                    <Input
                        :id="provider.urlKey"
                        type="url"
                        :name="'value[' + provider.urlKey + ']'"
                        :default-value="
                            String(
                                setting?.[
                                    provider.urlKey as keyof SettingValue
                                ] ?? provider.url,
                            )
                        "
                    />
                    <InputError :message="errors['value.' + provider.urlKey]" />
                </div>
                <div v-if="provider.callbackKey" class="grid gap-2">
                    <Label :for="'callback-' + provider.callbackKey"
                        >Webhook URL (POST)</Label
                    >
                    <div class="flex gap-2">
                        <Input
                            :id="'callback-' + provider.callbackKey"
                            readonly
                            :model-value="callbackUrls[provider.callbackKey]"
                        />
                        <Button
                            type="button"
                            variant="outline"
                            size="icon"
                            aria-label="Salin URL webhook"
                            @click="
                                copyCallbackUrl(
                                    callbackUrls[provider.callbackKey],
                                )
                            "
                            ><Copy class="size-4"
                        /></Button>
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Salin ke dashboard provider. URL mengikuti APP_URL
                        website.
                    </p>
                </div>
                <p
                    v-if="provider.name === 'QRIS Karuhun'"
                    class="rounded-md bg-muted p-3 text-sm text-muted-foreground"
                >
                    Fee aplikasi Rp0. Tanpa webhook: pembayaran dicek tiap 5
                    detik di halaman transaksi dan direkonsiliasi tiap menit
                    untuk pesanan yang masih pending.
                </p>
                <div
                    v-for="field in provider.fields"
                    :key="field.key"
                    class="grid gap-2"
                >
                    <Label :for="field.key">{{ field.label }}</Label>
                    <Input
                        :id="field.key"
                        type="password"
                        autocomplete="new-password"
                        :name="'credentials[' + field.key + ']'"
                        :placeholder="
                            configuredCredentials[field.key]
                                ? 'Tersimpan — isi untuk mengganti'
                                : 'Belum diatur'
                        "
                    />
                    <InputError :message="errors['credentials.' + field.key]" />
                </div>
                <div v-if="provider.name === 'Midtrans'" class="grid gap-2">
                    <Label for="midtrans_is_production">Environment</Label>
                    <Select
                        name="value[midtrans_is_production]"
                        :default-value="
                            setting?.midtrans_is_production ? '1' : '0'
                        "
                    >
                        <SelectTrigger
                            id="midtrans_is_production"
                            class="w-full"
                            ><SelectValue
                        /></SelectTrigger>
                        <SelectContent
                            ><SelectItem value="0">Sandbox</SelectItem
                            ><SelectItem value="1"
                                >Production</SelectItem
                            ></SelectContent
                        >
                    </Select>
                    <InputError
                        :message="errors['value.midtrans_is_production']"
                    />
                </div>
                <div v-if="provider.name === 'QRIS Karuhun'" class="grid gap-2">
                    <Label for="qris_provider">Akun QRIS (opsional)</Label>
                    <Input
                        id="qris_provider"
                        name="value[qris_provider]"
                        :default-value="setting?.qris_provider"
                        placeholder="gopay:default atau shopee:toko-a"
                    />
                    <p class="text-xs text-muted-foreground">
                        Kosongkan untuk memakai akun aktif di QRIS Karuhun. Kode
                        unik dan masa berlaku mengikuti gateway.
                    </p>
                    <InputError :message="errors['value.qris_provider']" />
                </div>
            </section>
        </div>
    </div>
</template>
