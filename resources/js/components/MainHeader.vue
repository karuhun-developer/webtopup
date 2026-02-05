<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { useSwal } from '@/composables/useSwal';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ArrowLeft, LogIn, Menu, UserPlus, X } from 'lucide-vue-next';
import { ref } from 'vue';
import Button from './ui/button/Button.vue';

interface Props {
    showSearch?: boolean;
    showBackButton?: boolean;
}

const page = usePage();
const mobileMenuOpen = ref(false);
const { confirm } = useSwal();

withDefaults(defineProps<Props>(), {
    showSearch: false,
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

const logout = () => {
    confirm({
        title: 'Apakah Anda yakin ingin keluar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, keluar',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post('/logout');
        }
    });
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

                <!-- Spacer when search is hidden -->
                <div v-else class="flex-1"></div>

                <!-- Desktop Navigation -->
                <nav class="hidden items-center gap-6 md:flex">
                    <Link
                        href="/"
                        class="text-sm font-medium text-foreground hover:text-primary"
                    >
                        Beranda
                    </Link>
                    <Link
                        href="/transaction"
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

                <!-- Right side container for mobile -->
                <div class="flex items-center gap-2">
                    <!-- Mobile Menu Button -->
                    <button
                        v-if="!showBackButton"
                        class="flex items-center justify-center rounded-md p-2 text-foreground hover:bg-muted md:hidden"
                        @click="toggleMobileMenu"
                    >
                        <Menu v-if="!mobileMenuOpen" class="h-6 w-6" />
                        <X v-else class="h-6 w-6" />
                    </button>

                    <!-- Back Button -->
                    <button
                        v-if="showBackButton"
                        class="flex items-center gap-2 text-sm font-medium text-foreground hover:text-primary"
                        @click="goBack"
                    >
                        <ArrowLeft class="h-4 w-4" />
                        <span class="hidden md:inline">Kembali</span>
                    </button>

                    <!-- Auth Buttons -->
                    <div
                        v-if="!page.props.auth.user"
                        class="hidden items-center gap-2 md:flex"
                    >
                        <Link href="/login">
                            <Button variant="outline">
                                <!-- LogIn Icon -->
                                <LogIn class="h-4 w-4" />
                                <span>LOG IN</span>
                            </Button>
                        </Link>
                        <Link href="/register">
                            <Button variant="outline">
                                <!-- UserPlus Icon -->
                                <UserPlus class="h-4 w-4" />
                                <span>SIGN UP</span>
                            </Button>
                        </Link>
                    </div>
                    <Button
                        v-else
                        variant="outline"
                        class="hidden md:flex"
                        @click="logout"
                    >
                        <!-- LogOut Icon -->
                        <LogIn class="h-4 w-4 rotate-180" />
                        <span>LOG OUT</span>
                    </Button>
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
                    href="/transaction"
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

                <!-- Mobile Auth Links -->
                <template v-if="!page.props.auth.user">
                    <Link
                        href="/login"
                        class="block rounded-md px-3 py-2 text-base font-medium text-foreground hover:bg-muted hover:text-primary"
                        @click="closeMobileMenu"
                    >
                        Log In
                    </Link>
                    <Link
                        href="/register"
                        class="block rounded-md px-3 py-2 text-base font-medium text-foreground hover:bg-muted hover:text-primary"
                        @click="closeMobileMenu"
                    >
                        Sign Up
                    </Link>
                </template>
                <button
                    v-else
                    class="block w-full rounded-md px-3 py-2 text-left text-base font-medium text-foreground hover:bg-muted hover:text-primary"
                    @click="logout"
                >
                    Log Out
                </button>
            </nav>
        </div>
    </header>
</template>
