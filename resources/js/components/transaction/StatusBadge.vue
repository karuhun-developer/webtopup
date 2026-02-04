<script setup lang="ts">
import { Badge } from '@/components/ui/badge';

defineProps<{
    status: number;
    type: 'payment' | 'topup';
}>();

const getStatusColor = (status: number, type: 'payment' | 'topup') => {
    if (type === 'payment') {
        switch (status) {
            case 0:
                return 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20';
            case 2:
                return 'bg-green-500/10 text-green-500 border-green-500/20';
            case -1:
            case -2:
                return 'bg-red-500/10 text-red-500 border-red-500/20';
            default:
                return 'bg-gray-500/10 text-gray-500 border-gray-500/20';
        }
    } else {
        switch (status) {
            case 1:
                return 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20';
            case 2:
                return 'bg-green-500/10 text-green-500 border-green-500/20';
            case 3:
                return 'bg-red-500/10 text-red-500 border-red-500/20';
            default:
                return 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20';
        }
    }
};

const getStatusLabel = (status: number, type: string) => {
    if (type === 'payment') {
        switch (status) {
            case 0:
                return 'Pending';
            case 2:
                return 'Lunas';
            case -1:
                return 'Dibatalkan';
            case -2:
                return 'Kadaluarsa';
            default:
                return 'Tidak Diketahui';
        }
    } else {
        switch (status) {
            case 1:
                return 'Dalam Proses';
            case 2:
                return 'Sukses';
            case 3:
                return 'Gagal';
            default:
                return 'Pending';
        }
    }
};
</script>

<template>
    <Badge :class="getStatusColor(status, type)" variant="outline">
        {{ getStatusLabel(status, type) }}
    </Badge>
</template>
