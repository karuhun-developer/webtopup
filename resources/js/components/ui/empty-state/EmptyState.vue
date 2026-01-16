<script setup lang="ts">
import { LucideIcon } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { Button } from '@/components/ui/button';

interface Props {
  icon?: LucideIcon;
  title: string;
  description?: string;
  class?: string;
  actionLabel?: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'action'): void;
}>();
</script>

<template>
  <div :class="cn('flex flex-col items-center justify-center text-center p-8 min-h-[400px]', props.class)">
    <div v-if="icon" class="flex h-20 w-20 items-center justify-center rounded-full bg-muted">
      <component :is="icon" class="h-10 w-10 text-muted-foreground" />
    </div>
    <h3 class="mt-6 text-2xl font-semibold tracking-tight">{{ title }}</h3>
    <p v-if="description" class="mt-2 text-sm text-muted-foreground max-w-sm">
      {{ description }}
    </p>
    <div v-if="actionLabel || $slots.action" class="mt-8">
      <slot name="action">
         <Button @click="emit('action')">{{ actionLabel }}</Button>
      </slot>
    </div>
  </div>
</template>
