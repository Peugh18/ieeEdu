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
    <section class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-serif font-bold text-on-surface mb-2">Mis Cursos</h2>
                <p class="text-on-surface-variant text-sm">Tus programas de especialización activos y completados.</p>
            </div>
            <Link :href="route('student.explore.courses')" class="hidden sm:inline-flex text-[10px] font-bold uppercase tracking-widest text-primary border border-primary/20 px-4 py-2 rounded-xl hover:bg-primary/5 transition-all">Explorar más</Link>
        </div>
        <div v-if="courses.length > 0" class="space-y-4">
            <article v-for="course in courses" :key="course.id" class="flex flex-col sm:flex-row items-center gap-6 p-4 rounded-2xl border border-outline-variant/30 hover:border-primary/40 transition-colors bg-surface-container-lowest group">
                <img :src="course.image" class="w-full sm:w-48 h-32 object-cover rounded-xl shrink-0" />
                <div class="flex-1 space-y-4 w-full">
                    <div>
                        <h4 class="font-serif font-bold text-lg leading-tight group-hover:text-primary transition-colors text-on-surface">{{ course.title }}</h4>
                        <p class="text-xs text-on-surface-variant font-medium mt-1">Última actividad: <span class="text-on-surface text-primary">{{ course.last_lesson ?? 'Ninguna' }}</span></p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest mb-1" :class="course.progress === 100 ? 'text-green-600' : 'text-primary'"><span>Progreso</span><span>{{ course.progress }}%</span></div>
                            <div class="h-1.5 w-full bg-surface-container rounded-full overflow-hidden"><div class="h-full rounded-full transition-all duration-1000" :class="course.progress === 100 ? 'bg-green-500' : 'bg-primary'" :style="`width: ${course.progress}%`"></div></div>
                        </div>
                        <Link :href="route('student.classroom', { course: course.slug })" class="shrink-0 text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-lg transition-all" :class="course.progress === 100 ? 'bg-surface border border-outline-variant hover:bg-outline-variant/10 text-on-surface' : 'bg-primary text-on-primary hover:opacity-90'">{{ course.progress === 100 ? 'Repasar' : 'Continuar' }}</Link>
                    </div>
                </div>
            </article>
        </div>
        <div v-else class="py-16 text-center border-2 border-dashed border-outline-variant/40 rounded-3xl space-y-4">
            <BookOpen class="w-12 h-12 text-on-surface-variant/40 mx-auto" />
            <h4 class="text-xl font-serif font-bold text-on-surface">No tienes cursos</h4>
            <Link :href="route('student.explore.courses')" class="inline-flex mt-2 text-xs font-bold uppercase text-primary hover:underline">Ver Catálogo</Link>
        </div>
    </section>
</template>
