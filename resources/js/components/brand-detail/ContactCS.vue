<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Headphones } from 'lucide-vue-next';
</script>

<template>
    <div class="mt-5 rounded-lg border border-border/50 bg-card p-6 shadow-sm">
        <div class="flex items-start gap-3">
            <div
                class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10"
            >
                <Headphones class="h-5 w-5 text-primary" />
            </div>
            <div class="flex-1">
                <h3 class="mb-1 text-sm font-semibold text-foreground">
                    Butuh Bantuan?
                </h3>
                <p class="mb-3 text-xs text-muted-foreground">
                    Kamu bisa hubungi admin disini.
                </p>
                <Button variant="outline" size="sm" class="w-full" as-child>
                    <a
                        href="https://wa.me/6281234567890"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <Headphones class="mr-2 h-4 w-4" />
                        Hubungi CS
                    </a>
                </Button>
            </div>
        </div>
    </div>
</template>
