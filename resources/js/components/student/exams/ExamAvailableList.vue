<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Award, Clock, FileText, RotateCw } from 'lucide-vue-next';

interface Exam {
    id: number;
    title: string;
    course_title: string;
    time_limit: number;
    attempts_left: number;
    progress: number;
    passed: boolean;
}

defineProps<{
    exams: Exam[];
}>();
</script>

<template>
    <div v-if="exams.length > 0" class="custom-scrollbar flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-6 md:gap-8">
        <div
            v-for="exam in exams"
            :key="exam.id"
            class="group relative flex w-[285px] flex-shrink-0 snap-start flex-col overflow-hidden rounded-2xl border border-outline-variant/20 bg-white p-5 shadow-sm transition-all hover:shadow-2xl hover:shadow-primary/5 sm:w-[325px] md:w-[365px] md:rounded-[3rem] md:p-8"
            :class="exam.progress < 100 ? 'cursor-not-allowed opacity-60' : 'hover:-translate-y-2'"
        >
            <div class="duration-[2s] absolute -right-8 -top-8 opacity-[0.03] transition-transform group-hover:scale-125">
                <Award class="h-48 w-48 text-primary" />
            </div>
            <div class="relative z-10 mb-4 flex items-start justify-between md:mb-8">
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl shadow-inner md:h-14 md:w-14 md:rounded-[1.5rem]"
                    :class="exam.progress < 100 ? 'bg-background' : 'border border-primary/10 bg-primary/5'"
                >
                    <FileText class="h-5 w-5 md:h-6 md:w-6" :class="exam.progress < 100 ? 'text-outline-variant' : 'text-primary'" />
                </div>
                <div
                    class="rounded-xl border border-outline-variant/20 bg-background px-3 py-1.5 text-[8px] font-black uppercase tracking-[0.15em] text-on-surface-variant md:rounded-2xl md:px-5 md:py-2 md:text-[9px] md:tracking-[0.2em]"
                >
                    Oport: {{ exam.attempts_left }}
                </div>
            </div>
            <div class="relative z-10 flex-1 space-y-1 md:space-y-2">
                <h4
                    class="line-clamp-2 font-serif text-base font-bold italic leading-[1.3] text-on-background transition-colors group-hover:text-primary md:text-xl"
                >
                    {{ exam.title }}
                </h4>
                <p class="mb-3 text-[8px] font-black uppercase italic tracking-[0.2em] text-[#D4AF37] md:mb-6 md:text-[9px] md:tracking-[0.25em]">
                    {{ exam.course_title }}
                </p>
            </div>
            <div v-if="exam.progress < 100" class="relative z-10 mb-4 md:mb-8">
                <div class="mb-3 flex items-center justify-between text-[8px] font-black uppercase tracking-[0.25em] text-outline-variant">
                    <span>Progreso Lectivo</span><span class="text-primary">{{ exam.progress }}%</span>
                </div>
                <div class="h-1.5 w-full overflow-hidden rounded-full border border-outline-variant/10 bg-background p-0.5">
                    <div
                        class="h-full rounded-full bg-gradient-to-r from-[#D4AF37] to-primary shadow-sm shadow-[#D4AF37]/20 transition-all duration-1000"
                        :style="{ width: exam.progress + '%' }"
                    ></div>
                </div>
            </div>
            <div class="relative z-10 mt-auto flex items-center justify-between border-t border-outline-variant/10 pt-4 md:pt-8">
                <div class="flex items-center gap-2 text-[9px] font-bold italic text-outline-variant md:gap-3 md:text-[10px]">
                    <Clock class="h-3.5 w-3.5 md:h-4 md:w-4" /><span>{{ exam.time_limit }} min</span>
                </div>
                <template v-if="!exam.passed">
                    <Link
                        v-if="exam.attempts_left > 0 && exam.progress >= 100"
                        :href="route('student.exams.take', { quiz: exam.id })"
                        class="rounded-xl bg-primary px-5 py-3 text-[8px] font-black uppercase tracking-[0.2em] text-white shadow-xl shadow-primary/10 transition-all hover:bg-on-background active:scale-95 md:rounded-2xl md:px-8 md:py-4 md:text-[9px] md:tracking-[0.3em]"
                        >Iniciar</Link
                    >
                    <div
                        v-else-if="exam.progress < 100"
                        class="flex items-center gap-2 rounded-xl border border-outline-variant/10 bg-background px-4 py-2.5 text-[7px] font-black uppercase italic tracking-[0.15em] text-outline-variant/60 md:gap-3 md:rounded-2xl md:px-6 md:py-4 md:text-[8px] md:tracking-[0.2em]"
                    >
                        <RotateCw class="h-3 w-3 md:h-3.5 md:w-3.5" /> Requisito
                    </div>
                </template>
                <span
                    v-else
                    class="flex items-center gap-2 rounded-xl bg-primary/5 px-4 py-2.5 text-[8px] font-black uppercase italic tracking-[0.2em] text-primary md:rounded-2xl md:px-6 md:py-4 md:text-[9px] md:tracking-[0.3em]"
                    ><div class="h-1 w-1 rounded-full bg-primary"></div>
                    Calificado</span
                >
            </div>
        </div>
    </div>
    <div
        v-else
        class="group flex flex-col items-center rounded-3xl border border-dashed border-outline-variant/30 bg-surface-container-lowest px-4 py-12 text-center md:py-20"
    >
        <div
            class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl border border-outline-variant/20 bg-background transition-colors group-hover:bg-primary/5 md:mb-8"
        >
            <FileText class="h-6 w-6 text-outline-variant" />
        </div>
        <h4 class="mb-2 text-lg font-bold text-on-background md:mb-3">Sin evaluaciones lectivas</h4>
        <p class="max-w-xs text-sm text-on-surface-variant">
            No se presentan evaluaciones disponibles en tu cronograma actual.
        </p>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(87, 87, 42, 0.08);
    border-radius: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(87, 87, 42, 0.15);
}
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    line-clamp: 2;
}
</style>
