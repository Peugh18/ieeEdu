<script setup lang="ts">
import { BarChart3 } from 'lucide-vue-next';

interface HistoryItem {
    id: number;
    title: string;
    course_title: string;
    date: string;
    score: number;
    passing_score: number;
    status: string;
}

defineProps<{
    history: HistoryItem[];
}>();

function getStatusStyles(status: string) {
    switch (status) {
        case 'aprobado':
            return 'bg-primary/5 text-primary border-primary/20';
        case 'reprobado':
            return 'bg-rose-50 text-rose-900 border-rose-100';
        default:
            return 'bg-amber-50 text-amber-900 border-amber-100';
    }
}
</script>

<template>
    <section class="space-y-4 md:hidden" v-if="history.length > 0">
        <div
            v-for="attempt in history"
            :key="attempt.id + '_' + attempt.date"
            class="rounded-2xl border border-outline-variant/20 bg-white p-4 shadow-sm"
        >
            <div class="mb-3 flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="truncate font-serif text-sm font-bold italic leading-tight text-on-background">{{ attempt.title }}</p>
                    <p class="mt-0.5 text-[9px] font-black uppercase tracking-widest text-outline-variant">{{ attempt.course_title }}</p>
                </div>
                <div
                    class="inline-flex shrink-0 items-center gap-2 rounded-full border px-3 py-1.5 text-[9px] font-black uppercase italic tracking-[0.15em]"
                    :class="getStatusStyles(attempt.status)"
                >
                    {{ attempt.status }}
                </div>
            </div>
            <div class="flex items-center justify-between border-t border-outline-variant/10 pt-3">
                <span class="font-serif text-[10px] italic text-on-surface-variant/50">{{ attempt.date }}</span>
                <div class="flex flex-col items-end">
                    <span
                        class="font-serif text-lg font-bold italic"
                        :class="attempt.score >= attempt.passing_score ? 'text-primary' : 'text-rose-900'"
                        >{{ ((attempt.score / 20) * 10).toFixed(1) }}</span
                    >
                    <span class="text-[8px] font-black uppercase tracking-[0.15em] text-outline-variant">{{ attempt.score }}/20 Pts</span>
                </div>
            </div>
        </div>
    </section>

    <section class="hidden space-y-8 md:block" v-if="history.length > 0">
        <div class="custom-scrollbar overflow-hidden overflow-x-auto rounded-[3.5rem] border border-outline-variant/20 bg-white shadow-2xl">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-outline-variant/10 bg-background/50 font-serif">
                        <th class="px-10 py-8 text-[10px] font-black uppercase italic tracking-[0.3em] text-primary/60">Módulo Académico</th>
                        <th class="px-10 py-8 text-[10px] font-black uppercase italic tracking-[0.3em] text-primary/60">Fecha de Conclusión</th>
                        <th class="px-10 py-8 text-center text-[10px] font-black uppercase italic tracking-[0.3em] text-primary/60">
                            Calificación Final
                        </th>
                        <th class="px-10 py-8 text-right text-[10px] font-black uppercase italic tracking-[0.3em] text-primary/60">
                            Estatus Institucional
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/5">
                    <tr v-for="attempt in history" :key="attempt.id + '_' + attempt.date" class="group transition-all hover:bg-background/80">
                        <td class="relative px-10 py-8">
                            <div
                                class="absolute left-0 top-1/2 h-0 w-1 -translate-y-1/2 rounded-r-full bg-primary transition-all duration-500 group-hover:h-12"
                            ></div>
                            <div class="flex flex-col gap-1">
                                <span
                                    class="font-serif text-base font-bold italic leading-tight text-on-background transition-colors group-hover:text-primary"
                                    >{{ attempt.title }}</span
                                >
                                <span class="text-[9px] font-black uppercase tracking-widest text-outline-variant">{{ attempt.course_title }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 font-serif text-sm italic text-on-surface-variant/60">{{ attempt.date }}</td>
                        <td class="px-10 py-8 text-center">
                            <div class="inline-flex flex-col">
                                <span
                                    class="font-serif text-2xl font-bold italic"
                                    :class="attempt.score >= attempt.passing_score ? 'text-primary' : 'text-rose-900'"
                                    >{{ ((attempt.score / 20) * 10).toFixed(1) }}</span
                                >
                                <span class="text-[8px] font-black uppercase italic tracking-[0.2em] text-outline-variant"
                                    >{{ attempt.score }}/20 Pts</span
                                >
                            </div>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div
                                class="inline-flex items-center gap-3 rounded-full border px-5 py-2.5 text-[9px] font-black uppercase italic tracking-[0.2em]"
                                :class="getStatusStyles(attempt.status)"
                            >
                                <div class="h-1.5 w-1.5 rounded-full" :class="attempt.status === 'aprobado' ? 'bg-primary' : 'bg-rose-900'"></div>
                                {{ attempt.status }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div
        v-if="history.length === 0"
        class="group flex flex-col items-center rounded-2xl border border-dashed border-outline-variant/30 bg-white px-4 py-12 text-center shadow-inner md:rounded-[4rem] md:py-24"
    >
        <div
            class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl border border-outline-variant/20 bg-background transition-colors group-hover:bg-primary/5 md:mb-8 md:h-20 md:w-20 md:rounded-[1.75rem]"
        >
            <BarChart3 class="h-6 w-6 text-outline-variant md:h-8 md:w-8" />
        </div>
        <h4 class="mb-2 font-serif text-lg font-bold italic text-on-background md:mb-3 md:text-xl">Sin historial disponible</h4>
        <p class="max-w-xs font-serif text-xs italic leading-relaxed text-on-surface-variant md:text-sm">
            Aún no registra evaluaciones finalizadas en el sistema académico.
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
tr:last-child td:first-child {
    border-bottom-left-radius: 3.5rem;
}
tr:last-child td:last-child {
    border-bottom-right-radius: 3.5rem;
}
</style>
