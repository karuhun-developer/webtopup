<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
    showSearch?: boolean;
    showAuthButtons?: boolean;
    showBackButton?: boolean;
}

const page = usePage();
const mobileMenuOpen = ref(false);

withDefaults(defineProps<Props>(), {
    showSearch: false,
    showAuthButtons: false,
    showBackButton: false,
});

const goBack = () => {
    router.visit('/');
};

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

const closeMobileMenu = () => {
    mobileMenuOpen.value = false;
};
</script>

<template>
    <header class="border-b border-border/50 bg-card shadow-sm">
        <div class="mx-auto max-w-7xl px-4 py-4">
            <div class="flex items-center justify-between gap-4">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-2">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary"
                    >
                        <span class="text-xl font-bold text-white">G</span>
                    </div>
                    <span
                        class="hidden text-xl font-bold text-foreground md:inline"
                    >
                        GameStore
                    </span>
                </Link>

                <!-- Search Bar (only on home) -->
                <div v-if="showSearch" class="max-w-xl flex-1">
                    <Input
                        type="search"
                        placeholder="Cari nama game"
                        class="w-full border-border/50 bg-background/50 text-foreground placeholder:text-muted-foreground"
                    />
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden items-center gap-6 md:flex">
                    <Link
                        href="/"
                        class="text-sm font-medium text-foreground hover:text-primary"
                    >
                        Beranda
                    </Link>
                    <Link
                        href="/transaction-check"
                        class="text-sm font-medium text-foreground hover:text-primary"
                    >
                        Cek Transaksi
                    </Link>
                    <Link
                        href="/profile"
                        class="text-sm font-medium text-foreground hover:text-primary"
                    >
                        Profile
                    </Link>
                </nav>

                <!-- Mobile Menu Button -->
                <button
                    v-if="!showBackButton"
                    class="flex items-center justify-center rounded-md p-2 text-foreground hover:bg-muted md:hidden"
                    @click="toggleMobileMenu"
                >
                    <svg
                        v-if="!mobileMenuOpen"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <line x1="4" x2="20" y1="12" y2="12" />
                        <line x1="4" x2="20" y1="6" y2="6" />
                        <line x1="4" x2="20" y1="18" y2="18" />
                    </svg>
                    <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>

                <!-- Back Button -->
                <button
                    v-if="showBackButton"
                    class="flex items-center gap-2 text-sm font-medium text-foreground hover:text-primary"
                    @click="goBack"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                    <span class="hidden md:inline">Kembali</span>
                </button>

                <!-- Auth Buttons -->
                <div
                    v-if="showAuthButtons && !page.props.auth.user"
                    class="flex items-center gap-2"
                >
                    <button
                        class="flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90"
                    >
                        <!-- LogIn Icon -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"
                            />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" x2="3" y1="12" y2="12" />
                        </svg>
                        <span>LOG IN</span>
                    </button>
                    <button
                        class="hidden items-center gap-2 rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-gray-700 md:flex"
                    >
                        <!-- UserPlus Icon -->
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                            />
                            <circle cx="9" cy="7" r="4" />
                            <line x1="19" x2="19" y1="8" y2="14" />
                            <line x1="22" x2="16" y1="11" y2="11" />
                        </svg>
                        <span>SIGN UP</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div
            v-if="mobileMenuOpen"
            class="border-t border-border/50 bg-card md:hidden"
        >
            <nav class="mx-auto max-w-7xl space-y-1 px-4 py-4">
                <Link
                    href="/"
                    class="block rounded-md px-3 py-2 text-base font-medium text-foreground hover:bg-muted hover:text-primary"
                    @click="closeMobileMenu"
                >
                    Beranda
                </Link>
                <Link
                    href="/transaction-check"
                    class="block rounded-md px-3 py-2 text-base font-medium text-foreground hover:bg-muted hover:text-primary"
                    @click="closeMobileMenu"
                >
                    Cek Transaksi
                </Link>
                <Link
                    href="/profile"
                    class="block rounded-md px-3 py-2 text-base font-medium text-foreground hover:bg-muted hover:text-primary"
                    @click="closeMobileMenu"
                >
                    Profile
                </Link>
            </nav>
        </div>
    </header>
</template>
