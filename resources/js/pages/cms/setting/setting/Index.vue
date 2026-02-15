<script setup lang="ts">
import {
    index,
    save,
} from '@/actions/App/Http/Controllers/Cms/Setting/SettingController';
import Heading from '@/components/Heading.vue';
import ContentPagesTab from '@/components/settings/ContentPagesTab.vue';
import GeneralInformationTab from '@/components/settings/GeneralInformationTab.vue';
import ManualTransferTab from '@/components/settings/ManualTransferTab.vue';
import WhatsAppTemplatesTab from '@/components/settings/WhatsAppTemplatesTab.vue';
import { Button } from '@/components/ui/button';
import { useSwal } from '@/composables/useSwal';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Setting } from '@/types/cms/setting';
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

type Tab = 'general' | 'content' | 'templates' | 'transfer';

const props = defineProps<{
    setting: Setting;
}>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Settings',
        href: index().url,
    },
];

const { toast } = useSwal();

const title = 'Website Settings';
const description = 'Manage your website configuration and settings';

// Active tab
const activeTab = ref<Tab>('general');
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="title" />
        <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
            <Heading :title="title" :description="description" />

            <Form
                v-bind="save.form()"
                :onSuccess="
                    () =>
                        toast.fire({
                            icon: 'success',
                            title: 'Settings saved successfully.',
                        })
                "
                class="space-y-6"
                v-slot="{ errors, processing }"
            >
                <!-- Tabs Header with Save Button -->
                <div class="flex items-center justify-between border-b bg-card">
                    <div
                        class="flex w-full gap-6 overflow-x-auto px-6 md:w-auto"
                    >
                        <Button
                            type="button"
                            variant="ghost"
                            class="relative whitespace-nowrap rounded-none px-0 py-4 text-sm font-medium hover:bg-transparent"
                            :class="
                                activeTab === 'general'
                                    ? 'text-foreground'
                                    : 'text-muted-foreground hover:text-foreground'
                            "
                            @click="activeTab = 'general'"
                        >
                            General Information
                            <span
                                v-if="activeTab === 'general'"
                                class="absolute right-0 bottom-0 left-0 h-0.5 bg-primary"
                            ></span>
                        </Button>

                        <Button
                            type="button"
                            variant="ghost"
                            class="relative whitespace-nowrap rounded-none px-0 py-4 text-sm font-medium hover:bg-transparent"
                            :class="
                                activeTab === 'content'
                                    ? 'text-foreground'
                                    : 'text-muted-foreground hover:text-foreground'
                            "
                            @click="activeTab = 'content'"
                        >
                            Content Pages
                            <span
                                v-if="activeTab === 'content'"
                                class="absolute right-0 bottom-0 left-0 h-0.5 bg-primary"
                            ></span>
                        </Button>

                        <Button
                            type="button"
                            variant="ghost"
                            class="relative whitespace-nowrap rounded-none px-0 py-4 text-sm font-medium hover:bg-transparent"
                            :class="
                                activeTab === 'templates'
                                    ? 'text-foreground'
                                    : 'text-muted-foreground hover:text-foreground'
                            "
                            @click="activeTab = 'templates'"
                        >
                            WhatsApp Templates
                            <span
                                v-if="activeTab === 'templates'"
                                class="absolute right-0 bottom-0 left-0 h-0.5 bg-primary"
                            ></span>
                        </Button>

                        <Button
                            type="button"
                            variant="ghost"
                            class="relative whitespace-nowrap rounded-none px-0 py-4 text-sm font-medium hover:bg-transparent"
                            :class="
                                activeTab === 'transfer'
                                    ? 'text-foreground'
                                    : 'text-muted-foreground hover:text-foreground'
                            "
                            @click="activeTab = 'transfer'"
                        >
                            Manual Transfer
                            <span
                                v-if="activeTab === 'transfer'"
                                class="absolute right-0 bottom-0 left-0 h-0.5 bg-primary"
                            ></span>
                        </Button>
                    </div>

                    <div class="hidden px-6 py-2 md:block">
                        <Button :disabled="processing" size="default">
                            Save Settings
                        </Button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="rounded-lg border bg-card p-6">
                    <GeneralInformationTab
                        v-if="activeTab === 'general'"
                        :setting="setting?.value"
                        :errors="errors"
                    />

                    <ContentPagesTab
                        v-if="activeTab === 'content'"
                        :setting="setting?.value"
                        :errors="errors"
                    />

                    <WhatsAppTemplatesTab
                        v-if="activeTab === 'templates'"
                        :setting="setting?.value"
                        :errors="errors"
                    />

                    <ManualTransferTab
                        v-if="activeTab === 'transfer'"
                        :setting="setting?.value"
                        :errors="errors"
                    />
                </div>

                <!-- Bottom Save Button -->
                <div class="flex justify-end rounded-lg border bg-card p-4">
                    <Button :disabled="processing" size="default">
                        Save Settings
                    </Button>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
