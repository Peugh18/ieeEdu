<script setup lang="ts">
import CourseCard from '@/components/CourseCard.vue';
import { useWelcomeCarousels } from '@/composables/useWelcomeCarousels';
import type { DynamicCourse } from '@/types/public';
import { computed, ref } from 'vue';

const props = defineProps<{
    courses: DynamicCourse[];
}>();

const courseTabs = [
    { id: 'en vivo', label: 'En Vivo' },
    { id: 'grabado', label: 'Grabados 24/7' },
];

const activeCourseTab = ref('en vivo');

const filterFn = (course: DynamicCourse, filter: string): boolean => {
    if (!filter) return true;
    return course.type === filter;
};

const {
    currentIndex,
    isMobile,
    scrollContainer,
    filteredItems: filteredCourses,
    goTo: goToCourse,
    next: nextCourse,
    prev: prevCourse,
    scroll: scrollCourses,
} = useWelcomeCarousels(
    computed(() => props.courses),
    activeCourseTab,
    filterFn,
);
</script>

<template>
    <section id="cursos" class="reveal relative bg-surface py-20 md:py-32">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute right-0 top-1/3 h-[500px] w-[500px] rounded-full bg-primary/[0.04] blur-[120px]"></div>
            <div class="absolute bottom-0 left-0 h-80 w-80 rounded-full bg-tertiary-container/[0.06] blur-[100px]"></div>
        </div>

        <div class="relative z-10 mx-auto max-w-7xl px-6 md:px-8">
            <div class="mb-6 flex items-center gap-3">
                <span class="font-serif text-[11px] font-bold uppercase tracking-[0.25em] text-primary/50">01</span>
                <div class="h-px w-8 bg-primary/30"></div>
                <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-primary">Oferta Académica</span>
            </div>

            <div class="mb-10 flex flex-col items-start justify-between gap-8 md:flex-row md:items-end">
                <div>
                    <h2 class="mb-4 font-serif text-3xl leading-tight text-on-surface md:text-5xl">
                        Programas de <span class="italic text-primary">Especialización</span>
                    </h2>
                    <p class="max-w-xl text-base leading-relaxed text-on-surface-variant">
                        Formación avanzada para líderes con metodología comprobada: en vivo o a tu ritmo.
                    </p>
                </div>
                <a
                    href="/cursos"
                    class="group inline-flex flex-shrink-0 items-center gap-2 rounded-xl border border-outline-variant/20 bg-surface-container px-6 py-3 text-sm font-bold text-on-surface transition-all duration-300 hover:border-primary/30 hover:bg-primary/5 hover:text-primary"
                >
                    Ver todos los cursos
                    <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>

            <div class="mb-10 inline-flex items-center gap-1 rounded-2xl border border-outline-variant/15 bg-surface-container p-1">
                <button
                    v-for="tab in courseTabs"
                    :key="tab.id"
                    @click.prevent="activeCourseTab = tab.id"
                    :class="[
                        'inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition-all duration-200',
                        activeCourseTab === tab.id ? 'bg-primary text-on-primary shadow-md' : 'text-on-surface-variant hover:text-on-surface',
                    ]"
                >
                    <svg v-if="tab.id === 'en vivo'" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                        />
                    </svg>
                    <svg v-else class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                        />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ tab.label }}
                </button>
            </div>

            <div v-if="isMobile && filteredCourses.length > 0" class="md:hidden">
                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-out" :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                        <div v-for="course in filteredCourses" :key="course.id" class="w-full flex-shrink-0 px-1">
                            <CourseCard :course="course" />
                        </div>
                    </div>

                    <button
                        v-if="filteredCourses.length > 1"
                        @click="prevCourse"
                        class="absolute left-2 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-outline-variant/30 bg-surface-container text-on-surface shadow-md transition-all hover:border-primary hover:bg-primary hover:text-on-primary"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        v-if="filteredCourses.length > 1"
                        @click="nextCourse"
                        class="absolute right-2 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-outline-variant/30 bg-surface-container text-on-surface shadow-md transition-all hover:border-primary hover:bg-primary hover:text-on-primary"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div v-if="filteredCourses.length > 1" class="mt-6 flex justify-center gap-2">
                    <button
                        v-for="(_, index) in filteredCourses"
                        :key="index"
                        @click="goToCourse(index)"
                        :class="[
                            'h-2.5 w-2.5 rounded-full transition-all duration-300',
                            currentIndex === index ? 'w-6 bg-primary' : 'bg-outline-variant/50 hover:bg-outline-variant',
                        ]"
                    ></button>
                </div>
            </div>

            <div class="group relative hidden md:block">
                <div
                    ref="scrollContainer"
                    class="hide-scrollbar flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-6 md:gap-6 lg:gap-8"
                >
                    <div
                        v-for="course in filteredCourses"
                        :key="course.id"
                        class="w-[calc(50%-1rem)] flex-shrink-0 snap-start lg:w-[calc(33.333%-1.5rem)]"
                    >
                        <CourseCard :course="course" class="h-full" />
                    </div>
                </div>
                <button
                    v-if="filteredCourses.length > 3"
                    @click="scrollCourses('left')"
                    class="absolute -left-5 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-outline-variant/30 bg-surface-container text-on-surface opacity-0 shadow-lg transition-all hover:border-primary hover:bg-primary hover:text-on-primary group-hover:opacity-100"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button
                    v-if="filteredCourses.length > 3"
                    @click="scrollCourses('right')"
                    class="absolute -right-5 top-1/2 z-10 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-outline-variant/30 bg-surface-container text-on-surface opacity-0 shadow-lg transition-all hover:border-primary hover:bg-primary hover:text-on-primary group-hover:opacity-100"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <template v-if="filteredCourses.length === 0">
                <div class="col-span-full py-20 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-surface-container">
                        <svg class="h-8 w-8 text-on-surface-variant/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.5"
                                d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                    <p class="mb-1 font-medium text-on-surface-variant">Sin cursos disponibles</p>
                    <p class="text-sm text-on-surface-variant/50">Pronto tendremos nuevos programas en esta modalidad.</p>
                </div>
            </template>
        </div>
    </section>
</template>

<style scoped>
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
</style>
