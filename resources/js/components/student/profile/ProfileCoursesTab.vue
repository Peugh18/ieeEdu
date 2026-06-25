<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen } from 'lucide-vue-next';

interface EnrolledCourse {
    id: number;
    title: string;
    slug: string;
    image: string;
    progress: number;
    last_lesson?: string;
}

defineProps<{
    courses: EnrolledCourse[];
}>();
</script>

<template>
    <section class="space-y-8 duration-500 animate-in fade-in slide-in-from-bottom-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="mb-2 font-serif text-3xl font-bold text-on-surface">Mis Cursos</h2>
                <p class="text-sm text-on-surface-variant">Tus programas de especialización activos y completados.</p>
            </div>
            <Link
                :href="route('student.explore.courses')"
                class="hidden rounded-xl border border-primary/20 px-4 py-2 text-[10px] font-bold uppercase tracking-widest text-primary transition-all hover:bg-primary/5 sm:inline-flex"
                >Explorar más</Link
            >
        </div>
        <div v-if="courses.length > 0" class="space-y-4">
            <article
                v-for="course in courses"
                :key="course.id"
                class="group flex flex-col items-center gap-6 rounded-2xl border border-outline-variant/30 bg-surface-container-lowest p-4 transition-colors hover:border-primary/40 sm:flex-row"
            >
                <img :src="course.image" class="h-32 w-full shrink-0 rounded-xl object-cover sm:w-48" />
                <div class="w-full flex-1 space-y-4">
                    <div>
                        <h4 class="font-serif text-lg font-bold leading-tight text-on-surface transition-colors group-hover:text-primary">
                            {{ course.title }}
                        </h4>
                        <p class="mt-1 text-xs font-medium text-on-surface-variant">
                            Última actividad: <span class="text-on-surface text-primary">{{ course.last_lesson ?? 'Ninguna' }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <div
                                class="mb-1 flex justify-between text-[10px] font-bold uppercase tracking-widest"
                                :class="course.progress === 100 ? 'text-green-600' : 'text-primary'"
                            >
                                <span>Progreso</span><span>{{ course.progress }}%</span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-surface-container">
                                <div
                                    class="h-full rounded-full transition-all duration-1000"
                                    :class="course.progress === 100 ? 'bg-green-500' : 'bg-primary'"
                                    :style="`width: ${course.progress}%`"
                                ></div>
                            </div>
                        </div>
                        <Link
                            :href="route('student.classroom', { course: course.slug })"
                            class="shrink-0 rounded-lg px-4 py-2 text-xs font-bold uppercase tracking-widest transition-all"
                            :class="
                                course.progress === 100
                                    ? 'border border-outline-variant bg-surface text-on-surface hover:bg-outline-variant/10'
                                    : 'bg-primary text-on-primary hover:opacity-90'
                            "
                            >{{ course.progress === 100 ? 'Repasar' : 'Continuar' }}</Link
                        >
                    </div>
                </div>
            </article>
        </div>
        <div v-else class="space-y-4 rounded-3xl border-2 border-dashed border-outline-variant/40 py-16 text-center">
            <BookOpen class="mx-auto h-12 w-12 text-on-surface-variant/40" />
            <h4 class="font-serif text-xl font-bold text-on-surface">No tienes cursos</h4>
            <Link :href="route('student.explore.courses')" class="mt-2 inline-flex text-xs font-bold uppercase text-primary hover:underline"
                >Ver Catálogo</Link
            >
        </div>
    </section>
</template>
