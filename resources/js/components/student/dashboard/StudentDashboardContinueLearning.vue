<script setup lang="ts">
import type { ContinueLearning, SummaryStats } from '@/types/student-dashboard';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Flame, GraduationCap, Play, Sparkles } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    continueLearning: ContinueLearning | null;
    stats: SummaryStats;
}>();

function progressCircle(pct: number, r = 28) {
    const circumference = 2 * Math.PI * r;
    const offset = circumference - (pct / 100) * circumference;
    return { circumference, offset };
}

const ring = computed(() => {
    const pct = props.continueLearning?.progress ?? 0;
    return progressCircle(pct);
});

const motivationalMessage = computed(() => {
    const pct = props.continueLearning?.progress ?? 0;
    if (pct === 0) return '¡Comienza hoy tu aprendizaje!';
    if (pct < 25) return 'Excelente inicio, sigue adelante';
    if (pct < 50) return 'Vas por buen camino, continúa';
    if (pct < 75) return '¡Más de la mitad! Eres imparable';
    if (pct < 100) return '¡Casi lo logras! Un esfuerzo más';
    return '¡Curso completado! Descarga tu certificado';
});
</script>

<template>
    <div v-if="continueLearning">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-bold text-gray-900">
                <Flame class="h-5 w-5 text-orange-500" />
                Continuar aprendiendo
            </h2>
            <Link
                :href="route('student.courses.index')"
                class="flex items-center gap-1 text-[11px] font-bold uppercase tracking-widest text-primary hover:underline"
            >
                Todos mis cursos <ArrowRight class="h-3 w-3" />
            </Link>
        </div>

        <div class="group overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm transition-all duration-500 hover:shadow-xl">
            <div class="flex flex-col md:flex-row">
                <div class="relative h-52 flex-shrink-0 overflow-hidden bg-gray-900 md:h-auto md:w-64">
                    <img
                        v-if="continueLearning.image"
                        :src="continueLearning.image"
                        :alt="continueLearning.course_title"
                        class="h-full w-full object-cover opacity-80 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100"
                    />
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-900/60 to-transparent md:from-transparent md:to-gray-900/20"></div>

                    <div class="absolute bottom-4 left-4 z-10 md:-right-10 md:bottom-auto md:left-auto md:top-1/2 md:-translate-y-1/2">
                        <div class="relative flex h-20 w-20 items-center justify-center">
                            <svg class="h-20 w-20 -rotate-90" viewBox="0 0 72 72">
                                <circle cx="36" cy="36" r="28" fill="none" stroke="rgba(255,255,255,0.15)" stroke-width="4" />
                                <circle
                                    cx="36"
                                    cy="36"
                                    r="28"
                                    fill="none"
                                    stroke="#D4AF37"
                                    stroke-width="4"
                                    stroke-linecap="round"
                                    :stroke-dasharray="ring.circumference"
                                    :stroke-dashoffset="ring.offset"
                                    style="transition: stroke-dashoffset 1.5s ease"
                                />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-sm font-bold text-white">{{ continueLearning.progress }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-1 flex-col justify-between p-8">
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-orange-100 bg-orange-50 px-3 py-1">
                                <Flame class="h-3 w-3 text-orange-500" />
                                <span class="text-[10px] font-bold uppercase tracking-widest text-orange-600">En progreso</span>
                            </span>
                        </div>
                        <h3 class="text-2xl font-bold leading-snug text-gray-900 transition-colors group-hover:text-primary">
                            {{ continueLearning.course_title }}
                        </h3>
                        <p class="flex items-center gap-2 text-sm text-gray-500">
                            <Play class="h-3.5 w-3.5 flex-shrink-0 fill-current text-[#D4AF37]" />
                            Última lección: <span class="font-semibold text-gray-700">{{ continueLearning.lesson_title }}</span>
                        </p>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-medium text-gray-400">{{ motivationalMessage }}</p>
                                <span class="text-xs font-bold text-primary">{{ continueLearning.progress }}%</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100">
                                <div
                                    class="h-full rounded-full bg-gradient-to-r from-primary to-[#D4AF37] transition-all duration-1000"
                                    :style="{ width: continueLearning.progress + '%' }"
                                ></div>
                            </div>
                        </div>

                        <Link
                            :href="route('student.classroom', { course: continueLearning.course_slug })"
                            class="group/btn inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-3.5 text-xs font-bold uppercase tracking-widest text-white shadow-lg shadow-primary/20 transition-all hover:bg-[#4a4a24] hover:shadow-primary/30 active:scale-95"
                        >
                            <Play class="h-4 w-4 fill-current" />
                            Continuar curso
                            <ArrowRight class="h-4 w-4 transition-transform group-hover/btn:translate-x-1" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        v-else-if="stats.active_courses === 0"
        class="space-y-6 rounded-3xl border-2 border-dashed border-primary/15 bg-gradient-to-br from-primary/5 to-[#D4AF37]/5 p-12 text-center"
    >
        <div class="mb-2 inline-flex rounded-3xl bg-white p-6 shadow-sm ring-8 ring-primary/5">
            <GraduationCap class="h-12 w-12 text-primary" />
        </div>
        <div class="space-y-3">
            <h2 class="text-2xl font-bold text-gray-900">Empieza tu camino académico</h2>
            <p class="mx-auto max-w-lg text-sm leading-relaxed text-gray-500">
                Aún no tienes cursos en progreso. Explora nuestro catálogo y comienza a especializarte con los mejores expertos del sector.
            </p>
        </div>
        <Link
            :href="route('student.explore.courses')"
            class="inline-flex items-center gap-2 rounded-xl bg-primary px-8 py-4 text-xs font-bold uppercase tracking-widest text-white shadow-xl transition-all hover:shadow-primary/30 active:scale-95"
        >
            <Sparkles class="h-4 w-4" />
            Explorar cursos
        </Link>
    </div>
</template>
