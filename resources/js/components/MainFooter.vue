<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Facebook, Instagram, Mail, Phone, Twitter } from 'lucide-vue-next';
const page = usePage();
</script>

<template>
    <footer class="border-t border-border/50 bg-card">
        <div class="mx-auto max-w-7xl px-4 py-12">
            <div class="grid gap-8 md:grid-cols-4">
                <!-- Company Info -->
                <div class="md:col-span-2">
                    <h3 class="mb-4 text-lg font-bold text-foreground">
                        {{ page.props.name }}
                    </h3>
                    <p class="mb-4 text-sm text-muted-foreground">
                        {{ page.props.setting.footer_description }}
                    </p>
                    <div class="flex gap-4">
                        <a
                            href="#"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            <Facebook class="h-5 w-5" />
                        </a>
                        <a
                            href="#"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            <Instagram class="h-5 w-5" />
                        </a>
                        <a
                            href="#"
                            class="text-muted-foreground transition-colors hover:text-primary"
                        >
                            <Twitter class="h-5 w-5" />
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="mb-4 font-semibold text-foreground">
                        Quick Links
                    </h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <Link
                                href="#"
                                class="text-muted-foreground transition-colors hover:text-primary"
                            >
                                Tentang Kami
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="#"
                                class="text-muted-foreground transition-colors hover:text-primary"
                            >
                                Syarat & Ketentuan
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="#"
                                class="text-muted-foreground transition-colors hover:text-primary"
                            >
                                Kebijakan Privasi
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="#"
                                class="text-muted-foreground transition-colors hover:text-primary"
                            >
                                FAQ
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="mb-4 font-semibold text-foreground">Kontak</h4>
                    <ul class="space-y-3 text-sm">
                        <li
                            class="flex items-center gap-2 text-muted-foreground"
                        >
                            <Mail class="h-4 w-4" />
                            <span>{{ page.props.setting.email }}</span>
                        </li>
                        <li
                            class="flex items-center gap-2 text-muted-foreground"
                        >
                            <Phone class="h-4 w-4" />
                            <span>{{ page.props.setting.phone }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <div
                class="mt-8 border-t border-border/30 pt-8 text-center text-sm text-muted-foreground"
            >
                <p>
                    &copy; {{ new Date().getFullYear() }} GameStore. All rights
                    reserved.
                </p>
            </div>
        </div>
    </footer>
</template>
