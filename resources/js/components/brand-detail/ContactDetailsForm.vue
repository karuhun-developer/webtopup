<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputDescription from '../InputDescription.vue';
import InputError from '../InputError.vue';

defineProps<{
    email: string;
    name: string;
    phone: string;
    formErrors: any;
}>();

const emit = defineEmits<{
    'update:email': [value: string];
    'update:name': [value: string];
    'update:phone': [value: string];
}>();
</script>

<template>
    <section class="rounded-lg border border-border/50 bg-card p-6 shadow-sm">
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
                <Label for="name">Nama</Label>
                <Input
                    id="name"
                    :model-value="name"
                    placeholder="Nama lengkap"
                    class="mt-1"
                    @update:model-value="emit('update:name', $event)"
                />
                <InputError :message="formErrors.name" class="mt-1" />
            </div>
            <div>
                <Label for="phone">Nomor Telepon</Label>
                <Input
                    id="phone"
                    :model-value="phone"
                    type="tel"
                    placeholder="08xxxxxxxxxx"
                    class="mt-1"
                    @update:model-value="emit('update:phone', $event)"
                />
                <InputError :message="formErrors.phone" class="mt-1" />
                <InputDescription class="sm:col-span-2">
                    * Jika ada kendala, kami akan menghubungi nomor WA kamu diatas.
                </InputDescription>
            </div>
            <div class="sm:col-span-2">
                <Label for="email">Email (Optional)</Label>
                <Input
                    id="email"
                    :model-value="email"
                    type="email"
                    placeholder="email@example.com"
                    class="mt-1"
                    @update:model-value="emit('update:email', $event)"
                />
                <InputError :message="formErrors.email" class="mt-1" />
                <InputDescription class="sm:col-span-2">
                    * Kami akan mengirimkan notifikasi transaksi melalui email jika kamu mengisi kolom ini.
                </InputDescription>
            </div>
        </div>
    </section>
</template>
