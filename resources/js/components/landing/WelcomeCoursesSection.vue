<script setup lang="ts">
import { ref, computed } from 'vue';
import CourseCard from '@/components/CourseCard.vue';
import { useWelcomeCarousels } from '@/composables/useWelcomeCarousels';
import type { DynamicCourse } from '@/types/public';

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
  filterFn
);
</script>

<template>
  <section id="cursos" class="py-20 md:py-32 bg-surface relative reveal">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute top-1/3 right-0 w-[500px] h-[500px] bg-primary/[0.04] rounded-full blur-[120px]"></div>
      <div class="absolute bottom-0 left-0 w-80 h-80 bg-tertiary-container/[0.06] rounded-full blur-[100px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 md:px-8 relative z-10">
      <div class="flex items-center gap-3 mb-6">
        <span class="text-[11px] font-bold text-primary/50 tracking-[0.25em] uppercase font-serif">01</span>
        <div class="w-8 h-px bg-primary/30"></div>
        <span class="text-[11px] font-bold text-primary tracking-[0.2em] uppercase">Oferta Académica</span>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-8">
        <div>
          <h2 class="font-serif text-3xl md:text-5xl text-on-surface mb-4 leading-tight">
            Programas de <span class="italic text-primary">Especialización</span>
          </h2>
          <p class="text-on-surface-variant max-w-xl text-base leading-relaxed">
            Formación avanzada para líderes con metodología comprobada: en vivo o a tu ritmo.
          </p>
        </div>
        <a href="/cursos" class="group flex-shrink-0 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-surface-container border border-outline-variant/20 text-on-surface font-bold text-sm hover:border-primary/30 hover:text-primary hover:bg-primary/5 transition-all duration-300">
          Ver todos los cursos
          <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
      </div>

      <div class="inline-flex items-center p-1 rounded-2xl bg-surface-container border border-outline-variant/15 mb-10 gap-1">
        <button
          v-for="tab in courseTabs"
          :key="tab.id"
          @click.prevent="activeCourseTab = tab.id"
          :class="[
            'inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200',
            activeCourseTab === tab.id
              ? 'bg-primary text-on-primary shadow-md'
              : 'text-on-surface-variant hover:text-on-surface'
          ]"
        >
          <svg v-if="tab.id === 'en vivo'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
          <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          {{ tab.label }}
        </button>
      </div>

      <div v-if="isMobile && filteredCourses.length > 0" class="md:hidden">
        <div class="relative overflow-hidden">
          <div class="flex transition-transform duration-500 ease-out"
               :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
            <div v-for="course in filteredCourses" :key="course.id" class="w-full flex-shrink-0 px-1">
              <CourseCard :course="course" />
            </div>
          </div>

          <button v-if="filteredCourses.length > 1" @click="prevCourse"
                  class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-surface-container border border-outline-variant/30 shadow-md flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary hover:border-primary transition-all z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          </button>
          <button v-if="filteredCourses.length > 1" @click="nextCourse"
                  class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-surface-container border border-outline-variant/30 shadow-md flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary hover:border-primary transition-all z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </button>
        </div>

        <div v-if="filteredCourses.length > 1" class="flex justify-center gap-2 mt-6">
          <button v-for="(_, index) in filteredCourses" :key="index"
                  @click="goToCourse(index)"
                  :class="['w-2.5 h-2.5 rounded-full transition-all duration-300', currentIndex === index ? 'bg-primary w-6' : 'bg-outline-variant/50 hover:bg-outline-variant']">
          </button>
        </div>
      </div>

      <div class="hidden md:block relative group">
        <div ref="scrollContainer" class="flex overflow-x-auto snap-x snap-mandatory gap-4 md:gap-6 lg:gap-8 pb-6 hide-scrollbar scroll-smooth">
          <div v-for="course in filteredCourses" :key="course.id" class="snap-start flex-shrink-0 w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.5rem)]">
            <CourseCard :course="course" class="h-full" />
          </div>
        </div>
        <button v-if="filteredCourses.length > 3" @click="scrollCourses('left')" class="absolute -left-5 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-surface-container border border-outline-variant/30 shadow-lg flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary hover:border-primary transition-all opacity-0 group-hover:opacity-100 z-10">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button v-if="filteredCourses.length > 3" @click="scrollCourses('right')" class="absolute -right-5 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-surface-container border border-outline-variant/30 shadow-lg flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary hover:border-primary transition-all opacity-0 group-hover:opacity-100 z-10">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>

      <template v-if="filteredCourses.length === 0">
        <div class="col-span-full py-20 text-center">
          <div class="w-16 h-16 rounded-2xl bg-surface-container mx-auto flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-on-surface-variant/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
          </div>
          <p class="text-on-surface-variant font-medium mb-1">Sin cursos disponibles</p>
          <p class="text-on-surface-variant/50 text-sm">Pronto tendremos nuevos programas en esta modalidad.</p>
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
