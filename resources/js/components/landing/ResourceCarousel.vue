<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useWindowSize } from '@vueuse/core';

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

const itemsPerView = computed(() => width.value < 768 ? 1 : 2);

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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <Transition
                    name="slide"
                    mode="out-in"
                >
                    <div 
                        :key="`slide-${currentIndex}`"
                        class="col-span-full md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6"
                    >
                        <a 
                            v-for="(resource, idx) in visibleResources"
                            :key="`${currentIndex}-${idx}`"
                            :href="resource.href"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group relative overflow-hidden rounded-xl md:rounded-2xl p-5 md:p-8 transition-all duration-300 hover:shadow-2xl cursor-pointer transform hover:-translate-y-1 flex items-start justify-between min-h-32 md:min-h-40"
                            :style="{ backgroundColor: resource.color }"
                        >
                            <!-- Gradient Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent opacity-10 group-hover:opacity-20 transition-opacity"></div>
                            
                            <!-- Content -->
                            <div class="relative z-10 flex flex-col justify-between h-full w-full">
                                <div>
                                    <div class="flex items-center gap-2 mb-2 md:mb-3">
                                        <span class="material-symbols-outlined text-white text-xl md:text-2xl">{{ resource.icon }}</span>
                                        <span class="text-[10px] md:text-xs font-semibold opacity-90 uppercase tracking-widest text-white/90">{{ resource.category }}</span>
                                    </div>
                                    <h3 class="font-serif text-base md:text-lg font-bold leading-tight text-white">{{ resource.title }}</h3>
                                </div>
                                <p class="text-[11px] md:text-xs opacity-80 font-medium text-white/90">{{ resource.size }}</p>
                            </div>

                            <!-- Download Icon -->
                            <div class="relative z-10 flex-shrink-0 ml-3 md:ml-4">
                                <div class="w-9 h-9 md:w-12 md:h-12 rounded-full bg-white/25 backdrop-blur-sm flex items-center justify-center group-hover:bg-white/40 transition-all">
                                    <svg class="w-4 h-4 md:w-6 md:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                </Transition>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-center md:justify-start gap-2 md:gap-3 mt-5 md:mt-8">
                <button
                    @click="prevSlide"
                    class="p-2 md:p-3 rounded-full bg-white/20 hover:bg-white/35 backdrop-blur-sm transition-all text-white focus:outline-none focus:ring-2 focus:ring-white/50"
                    aria-label="Previous slide"
                >
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button
                    @click="nextSlide"
                    class="p-2 md:p-3 rounded-full bg-white/20 hover:bg-white/35 backdrop-blur-sm transition-all text-white focus:outline-none focus:ring-2 focus:ring-white/50"
                    aria-label="Next slide"
                >
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Dots Indicator -->
            <div class="flex justify-center md:justify-start gap-2 mt-3 md:mt-4">
                <button
                    v-for="(_, index) in resources"
                    :key="index"
                    @click="goToSlide(index)"
                    :class="[
                        'h-1.5 md:h-2 rounded-full transition-all duration-300',
                        currentIndex === index ? 'w-6 md:w-8 bg-white' : 'w-1.5 md:w-2 bg-white/40 hover:bg-white/60'
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
