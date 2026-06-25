<script setup lang="ts">
import { useWindowSize } from '@vueuse/core';
import { computed, onMounted, onUnmounted, ref } from 'vue';

interface Resource {
    id: number;
    title: string;
    category: string;
    size: string;
    icon: string;
    color: string;
    href?: string;
}

const props = defineProps<{
    resources: Resource[];
}>();

const { width } = useWindowSize();

const currentIndex = ref(0);
const autoplayTimer = ref<ReturnType<typeof setInterval> | null>(null);

const startAutoplay = () => {
    autoplayTimer.value = setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % props.resources.length;
    }, 5000);
};

const stopAutoplay = () => {
    if (autoplayTimer.value) {
        clearInterval(autoplayTimer.value);
        autoplayTimer.value = null;
    }
};

const nextSlide = () => {
    stopAutoplay();
    currentIndex.value = (currentIndex.value + 1) % props.resources.length;
    startAutoplay();
};

const prevSlide = () => {
    stopAutoplay();
    currentIndex.value = (currentIndex.value - 1 + props.resources.length) % props.resources.length;
    startAutoplay();
};

const goToSlide = (index: number) => {
    stopAutoplay();
    currentIndex.value = index;
    startAutoplay();
};

const itemsPerView = computed(() => (width.value < 768 ? 1 : 2));

const visibleResources = computed(() => {
    const resources = [];
    for (let i = 0; i < itemsPerView.value; i++) {
        resources.push(props.resources[(currentIndex.value + i) % props.resources.length]);
    }
    return resources;
});

onMounted(() => {
    startAutoplay();
});

onUnmounted(() => {
    stopAutoplay();
});
</script>

<template>
    <div class="w-full" @mouseenter="stopAutoplay" @mouseleave="startAutoplay">
        <!-- Carousel Container -->
        <div class="relative h-full">
            <!-- Items -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6">
                <Transition name="slide" mode="out-in">
                    <div :key="`slide-${currentIndex}`" class="col-span-full grid grid-cols-1 gap-4 md:col-span-2 md:grid-cols-2 md:gap-6">
                        <a
                            v-for="(resource, idx) in visibleResources"
                            :key="`${currentIndex}-${idx}`"
                            :href="resource.href"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group relative flex min-h-32 transform cursor-pointer items-start justify-between overflow-hidden rounded-xl p-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl md:min-h-40 md:rounded-2xl md:p-8"
                            :style="{ backgroundColor: resource.color }"
                        >
                            <!-- Gradient Overlay -->
                            <div
                                class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent opacity-10 transition-opacity group-hover:opacity-20"
                            ></div>

                            <!-- Content -->
                            <div class="relative z-10 flex h-full w-full flex-col justify-between">
                                <div>
                                    <div class="mb-2 flex items-center gap-2 md:mb-3">
                                        <span class="material-symbols-outlined text-xl text-white md:text-2xl" translate="no">{{
                                            resource.icon
                                        }}</span>
                                        <span class="text-[10px] font-semibold uppercase tracking-widest text-white/90 opacity-90 md:text-xs">{{
                                            resource.category
                                        }}</span>
                                    </div>
                                    <h3 class="font-serif text-base font-bold leading-tight text-white md:text-lg">{{ resource.title }}</h3>
                                </div>
                                <p class="text-[11px] font-medium text-white/90 opacity-80 md:text-xs">{{ resource.size }}</p>
                            </div>

                            <!-- Download Icon -->
                            <div class="relative z-10 ml-3 flex-shrink-0 md:ml-4">
                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-white/25 backdrop-blur-sm transition-all group-hover:bg-white/40 md:h-12 md:w-12"
                                >
                                    <svg class="h-4 w-4 text-white md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                </Transition>
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-5 flex justify-center gap-2 md:mt-8 md:justify-start md:gap-3">
                <button
                    @click="prevSlide"
                    class="rounded-full bg-white/20 p-2 text-white backdrop-blur-sm transition-all hover:bg-white/35 focus:outline-none focus:ring-2 focus:ring-white/50 md:p-3"
                    aria-label="Previous slide"
                >
                    <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button
                    @click="nextSlide"
                    class="rounded-full bg-white/20 p-2 text-white backdrop-blur-sm transition-all hover:bg-white/35 focus:outline-none focus:ring-2 focus:ring-white/50 md:p-3"
                    aria-label="Next slide"
                >
                    <svg class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Dots Indicator -->
            <div class="mt-3 flex justify-center gap-2 md:mt-4 md:justify-start">
                <button
                    v-for="(_, index) in resources"
                    :key="index"
                    @click="goToSlide(index)"
                    :class="[
                        'h-1.5 rounded-full transition-all duration-300 md:h-2',
                        currentIndex === index ? 'w-6 bg-white md:w-8' : 'w-1.5 bg-white/40 hover:bg-white/60 md:w-2',
                    ]"
                    aria-label="Go to slide"
                />
            </div>
        </div>
    </div>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
    transition: all 0.5s ease;
}

.slide-enter-from {
    opacity: 0;
    transform: translateX(10px);
}

.slide-leave-to {
    opacity: 0;
    transform: translateX(-10px);
}
</style>
