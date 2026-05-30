<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { FileText, Clock, RotateCw, Award } from 'lucide-vue-next';

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
    <div v-if="exams.length > 0" class="flex gap-4 md:gap-8 overflow-x-auto pb-6 scroll-smooth snap-x snap-mandatory custom-scrollbar">
        <div v-for="exam in exams" :key="exam.id" class="flex-shrink-0 w-[285px] sm:w-[325px] md:w-[365px] snap-start group bg-white rounded-2xl md:rounded-[3rem] border border-outline-variant/20 p-5 md:p-8 shadow-sm hover:shadow-2xl hover:shadow-primary/5 transition-all relative overflow-hidden flex flex-col" :class="exam.progress < 100 ? 'opacity-60 cursor-not-allowed' : 'hover:-translate-y-2'">
            <div class="absolute -right-8 -top-8 opacity-[0.03] group-hover:scale-125 transition-transform duration-[2s]"><Award class="w-48 h-48 text-primary" /></div>
            <div class="flex justify-between items-start mb-4 md:mb-8 relative z-10">
                <div class="w-11 h-11 md:w-14 md:h-14 rounded-xl md:rounded-[1.5rem] flex items-center justify-center shadow-inner" :class="exam.progress < 100 ? 'bg-background' : 'bg-primary/5 border border-primary/10'">
                    <FileText class="w-5 h-5 md:w-6 md:h-6" :class="exam.progress < 100 ? 'text-outline-variant' : 'text-primary'" />
                </div>
                <div class="px-3 py-1.5 md:px-5 md:py-2 bg-background rounded-xl md:rounded-2xl text-[8px] md:text-[9px] font-black text-on-surface-variant uppercase tracking-[0.15em] md:tracking-[0.2em] border border-outline-variant/20">Oport: {{ exam.attempts_left }}</div>
            </div>
            <div class="space-y-1 md:space-y-2 flex-1 relative z-10">
                <h4 class="font-serif font-bold text-base md:text-xl text-on-background italic leading-[1.3] group-hover:text-primary transition-colors line-clamp-2">{{ exam.title }}</h4>
                <p class="text-[8px] md:text-[9px] text-[#D4AF37] font-black uppercase tracking-[0.2em] md:tracking-[0.25em] mb-3 md:mb-6 italic">{{ exam.course_title }}</p>
            </div>
            <div v-if="exam.progress < 100" class="mb-4 md:mb-8 relative z-10">
                <div class="flex items-center justify-between text-[8px] font-black uppercase tracking-[0.25em] text-outline-variant mb-3"><span>Progreso Lectivo</span><span class="text-primary">{{ exam.progress }}%</span></div>
                <div class="h-1.5 w-full bg-background rounded-full overflow-hidden border border-outline-variant/10 p-0.5"><div class="h-full bg-gradient-to-r from-[#D4AF37] to-primary rounded-full transition-all duration-1000 shadow-sm shadow-[#D4AF37]/20" :style="{ width: exam.progress + '%' }"></div></div>
            </div>
            <div class="flex items-center justify-between pt-4 md:pt-8 border-t border-outline-variant/10 mt-auto relative z-10">
                <div class="flex items-center gap-2 md:gap-3 text-[9px] md:text-[10px] font-bold italic text-outline-variant"><Clock class="w-3.5 h-3.5 md:w-4 md:h-4" /><span>{{ exam.time_limit }} min</span></div>
                <template v-if="!exam.passed">
                    <Link v-if="exam.attempts_left > 0 && exam.progress >= 100" :href="route('student.exams.take', { quiz: exam.id })" class="px-5 py-3 md:px-8 md:py-4 rounded-xl md:rounded-2xl bg-primary text-white text-[8px] md:text-[9px] font-black uppercase tracking-[0.2em] md:tracking-[0.3em] hover:bg-on-background transition-all shadow-xl shadow-primary/10 active:scale-95">Iniciar</Link>
                    <div v-else-if="exam.progress < 100" class="px-4 py-2.5 md:px-6 md:py-4 bg-background rounded-xl md:rounded-2xl text-[7px] md:text-[8px] font-black text-outline-variant/60 uppercase tracking-[0.15em] md:tracking-[0.2em] border border-outline-variant/10 flex items-center gap-2 md:gap-3 italic"><RotateCw class="w-3 h-3 md:w-3.5 md:h-3.5" /> Requisito</div>
                </template>
                <span v-else class="text-[8px] md:text-[9px] font-black text-primary uppercase tracking-[0.2em] md:tracking-[0.3em] px-4 py-2.5 md:px-6 md:py-4 bg-primary/5 rounded-xl md:rounded-2xl italic flex items-center gap-2"><div class="w-1 h-1 rounded-full bg-primary"></div> Calificado</span>
            </div>
        </div>
    </div>
    <div v-else class="py-12 md:py-24 flex flex-col items-center text-center bg-white rounded-2xl md:rounded-[4rem] border border-dashed border-outline-variant/30 shadow-inner group px-4">
        <div class="w-16 h-16 md:w-20 md:h-20 bg-background rounded-2xl md:rounded-[1.75rem] border border-outline-variant/20 flex items-center justify-center mb-6 md:mb-8 group-hover:bg-primary/5 transition-colors"><FileText class="w-6 h-6 md:w-8 md:h-8 text-outline-variant" /></div>
        <h4 class="text-lg md:text-xl font-serif font-bold italic text-on-background mb-2 md:mb-3">Sin evaluaciones lectivas</h4>
        <p class="text-on-surface-variant font-serif italic text-xs md:text-sm leading-relaxed max-w-xs">No se presentan evaluaciones magistrales disponibles en su cronograma actual.</p>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(87, 87, 42, 0.08); border-radius: 20px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(87, 87, 42, 0.15); }
.line-clamp-2 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; }
</style>
