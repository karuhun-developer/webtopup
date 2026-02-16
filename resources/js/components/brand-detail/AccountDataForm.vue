<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { CircleAlert } from 'lucide-vue-next';
import { computed } from 'vue';
import InputError from '../InputError.vue';

const props = defineProps<{
    inputType: 'id' | 'id+server';
    labelId: string;
    labelServer: string;
    serverOptions: string[];
    accountId: string;
    serverId: string;
    formErrors: any;
}>();

const emit = defineEmits<{
    'update:accountId': [value: string];
    'update:serverId': [value: string];
}>();

const hasServerDropdown = computed(
    () => props.inputType === 'id+server' && props.serverOptions.length > 0,
);
</script>

<template>
    <section class="rounded-lg border border-border/50 bg-card p-6 shadow-sm">
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
            :class="inputType === 'id+server' ? 'sm:grid-cols-2' : ''"
        >
            <!-- ID Input -->
            <div>
                <Label :for="'account-id'">{{ labelId }}</Label>
                <Input
                    id="account-id"
                    :model-value="accountId"
                    :placeholder="`Masukkan ${labelId}`"
                    class="mt-1"
                    @update:model-value="emit('update:accountId', $event)"
                />
                <InputError :message="formErrors.account_id" class="mt-1" />
            </div>

            <!-- Server Input/Dropdown (conditional) -->
            <div v-if="inputType === 'id+server'">
                <Label :for="'server-id'">{{ labelServer }}</Label>

                <!-- Dropdown if servers available -->
                <Select
                    v-if="hasServerDropdown"
                    :model-value="serverId"
                    @update:model-value="emit('update:serverId', $event)"
                >
                    <SelectTrigger id="server-id" class="mt-1">
                        <SelectValue :placeholder="`Pilih ${labelServer}`" />
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
                    :model-value="serverId"
                    :placeholder="`Masukkan ${labelServer}`"
                    class="mt-1"
                    @update:model-value="emit('update:serverId', $event)"
                />
                <InputError :message="formErrors.server_id" class="mt-1" />
            </div>
        </div>

        <p
            class="mt-6 flex items-start gap-2 rounded-md bg-secondary p-3 text-sm text-muted-foreground"
        >
            <CircleAlert class="inline h-4 w-4 text-destructive" />
            Pastikan data akun yang kamu masukkan benar.
        </p>
    </section>
</template>
