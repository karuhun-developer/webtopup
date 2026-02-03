<script setup lang="ts">
import { SliderDataItem } from '@/types/cms/web';
import { onMounted, onUnmounted, ref } from 'vue';

interface Props {
    slides: SliderDataItem[];
    autoplay?: boolean;
    interval?: number;
}

const props = defineProps<Props>();

const currentSlide = ref(0);
let autoplayTimer: ReturnType<typeof setInterval> | null = null;

const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % props.slides.length;
};

const prevSlide = () => {
    currentSlide.value =
        currentSlide.value === 0
            ? props.slides.length - 1
            : currentSlide.value - 1;
};

const goToSlide = (index: number) => {
    currentSlide.value = index;
};

const startAutoplay = () => {
    if (props.autoplay && props.slides.length > 1) {
        autoplayTimer = setInterval(nextSlide, props.interval);
    }
};

const stopAutoplay = () => {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
};

onMounted(() => {
    startAutoplay();
});

onUnmounted(() => {
    stopAutoplay();
});
</script>

<template>
    <div
        class="relative overflow-hidden rounded-xl bg-gray-100"
        @mouseenter="stopAutoplay"
        @mouseleave="startAutoplay"
    >
        <!-- Slides -->
        <div class="relative aspect-[3/1] w-full">
            <div
                v-for="(slide, index) in slides"
                :key="slide.id"
                class="absolute inset-0 transition-opacity duration-500"
                :class="index === currentSlide ? 'opacity-100' : 'opacity-0'"
            >
                <img
                    :src="slide.image"
                    :alt="slide.title"
                    class="h-full w-full object-cover"
                />
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button
            v-if="slides.length > 1"
            class="absolute top-1/2 left-4 -translate-y-1/2 rounded-full bg-white/80 p-2 shadow-md transition-all hover:bg-white"
            @click="prevSlide"
        >
            <svg
                class="h-6 w-6 text-gray-800"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 19l-7-7 7-7"
                />
            </svg>
        </button>

        <button
            v-if="slides.length > 1"
            class="absolute top-1/2 right-4 -translate-y-1/2 rounded-full bg-white/80 p-2 shadow-md transition-all hover:bg-white"
            @click="nextSlide"
        >
            <svg
                class="h-6 w-6 text-gray-800"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                />
            </svg>
        </button>

        <!-- Indicators -->
        <div
            v-if="slides.length > 1"
            class="absolute bottom-4 left-1/2 flex -translate-x-1/2 gap-2"
        >
            <button
                v-for="(slide, index) in slides"
                :key="`indicator-${slide.id}`"
                class="h-2 rounded-full transition-all"
                :class="
                    index === currentSlide
                        ? 'w-8 bg-white'
                        : 'w-2 bg-white/50 hover:bg-white/75'
                "
                @click="goToSlide(index)"
            />
        </div>
    </div>
</template>
