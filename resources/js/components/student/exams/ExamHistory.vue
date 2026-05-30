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
        case 'aprobado': return 'bg-primary/5 text-primary border-primary/20';
        case 'reprobado': return 'bg-rose-50 text-rose-900 border-rose-100';
        default: return 'bg-amber-50 text-amber-900 border-amber-100';
    }
}
</script>

<template>
    <section class="md:hidden space-y-4" v-if="history.length > 0">
        <div v-for="attempt in history" :key="attempt.id + '_' + attempt.date" class="bg-white rounded-2xl border border-outline-variant/20 p-4 shadow-sm">
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="flex-1 min-w-0">
                    <p class="font-serif font-bold text-sm text-on-background italic leading-tight truncate">{{ attempt.title }}</p>
                    <p class="text-[9px] text-outline-variant font-black uppercase tracking-widest mt-0.5">{{ attempt.course_title }}</p>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-[9px] font-black uppercase tracking-[0.15em] italic shrink-0" :class="getStatusStyles(attempt.status)">{{ attempt.status }}</div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-outline-variant/10">
                <span class="text-[10px] text-on-surface-variant/50 font-serif italic">{{ attempt.date }}</span>
                <div class="flex flex-col items-end">
                    <span class="text-lg font-serif font-bold italic" :class="attempt.score >= attempt.passing_score ? 'text-primary' : 'text-rose-900'">{{ (attempt.score / 20 * 10).toFixed(1) }}</span>
                    <span class="text-[8px] text-outline-variant font-black uppercase tracking-[0.15em]">{{ attempt.score }}/20 Pts</span>
                </div>
            </div>
        </div>
    </section>

    <section class="hidden md:block space-y-8" v-if="history.length > 0">
        <div class="bg-white rounded-[3.5rem] border border-outline-variant/20 shadow-2xl overflow-hidden overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-background/50 border-b border-outline-variant/10 font-serif">
                        <th class="px-10 py-8 text-[10px] font-black text-primary/60 uppercase tracking-[0.3em] italic">Módulo Académico</th>
                        <th class="px-10 py-8 text-[10px] font-black text-primary/60 uppercase tracking-[0.3em] italic">Fecha de Conclusión</th>
                        <th class="px-10 py-8 text-[10px] font-black text-primary/60 uppercase tracking-[0.3em] italic text-center">Calificación Final</th>
                        <th class="px-10 py-8 text-[10px] font-black text-primary/60 uppercase tracking-[0.3em] italic text-right">Estatus Institucional</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/5">
                    <tr v-for="attempt in history" :key="attempt.id + '_' + attempt.date" class="group transition-all hover:bg-background/80">
                        <td class="px-10 py-8 relative">
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-0 group-hover:h-12 bg-primary transition-all duration-500 rounded-r-full"></div>
                            <div class="flex flex-col gap-1">
                                <span class="font-serif font-bold text-on-background text-base group-hover:text-primary transition-colors italic leading-tight">{{ attempt.title }}</span>
                                <span class="text-[9px] text-outline-variant uppercase font-black tracking-widest">{{ attempt.course_title }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-sm font-serif italic text-on-surface-variant/60">{{ attempt.date }}</td>
                        <td class="px-10 py-8 text-center">
                            <div class="inline-flex flex-col">
                                <span class="text-2xl font-serif font-bold italic" :class="attempt.score >= attempt.passing_score ? 'text-primary' : 'text-rose-900'">{{ (attempt.score / 20 * 10).toFixed(1) }}</span>
                                <span class="text-[8px] text-outline-variant font-black uppercase tracking-[0.2em] italic">{{ attempt.score }}/20 Pts</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="inline-flex items-center gap-3 px-5 py-2.5 rounded-full border text-[9px] font-black uppercase tracking-[0.2em] italic" :class="getStatusStyles(attempt.status)">
                                <div class="w-1.5 h-1.5 rounded-full" :class="attempt.status === 'aprobado' ? 'bg-primary' : 'bg-rose-900'"></div>{{ attempt.status }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div v-if="history.length === 0" class="py-12 md:py-24 flex flex-col items-center text-center bg-white rounded-2xl md:rounded-[4rem] border border-dashed border-outline-variant/30 shadow-inner group px-4">
        <div class="w-16 h-16 md:w-20 md:h-20 bg-background rounded-2xl md:rounded-[1.75rem] border border-outline-variant/20 flex items-center justify-center mb-6 md:mb-8 group-hover:bg-primary/5 transition-colors"><BarChart3 class="w-6 h-6 md:w-8 md:h-8 text-outline-variant" /></div>
        <h4 class="text-lg md:text-xl font-serif font-bold italic text-on-background mb-2 md:mb-3">Sin historial disponible</h4>
        <p class="text-on-surface-variant font-serif italic text-xs md:text-sm leading-relaxed max-w-xs">Aún no registra evaluaciones finalizadas en el sistema académico.</p>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(87, 87, 42, 0.08); border-radius: 20px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(87, 87, 42, 0.15); }
tr:last-child td:first-child { border-bottom-left-radius: 3.5rem; }
tr:last-child td:last-child { border-bottom-right-radius: 3.5rem; }
</style>
