<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { ClipboardCheck, Clock, FileText, CheckCircle2, XCircle, MoreVertical, PlayCircle, BarChart3, RotateCw, Award } from 'lucide-vue-next';

interface ExamAttempt {
    id: number;
    title: string;
    course_title: string;
    date: string;
    score: number;
    passing_score: number;
    status: 'aprobado' | 'reprobado' | 'pendiente';
    attempts_left: number;
    progress: number;
    passed: boolean;
}

const props = defineProps<{
    exams: ExamAttempt[];
    history: ExamAttempt[];
    stats: {
        average_score: number;
        certificate_count: number;
    }
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Exámenes', href: '/student/exams' },
];

const getStatusStyles = (status: string) => {
    switch (status) {
        case 'aprobado': return 'bg-primary/5 text-primary border-primary/20';
        case 'reprobado': return 'bg-rose-50 text-rose-900 border-rose-100';
        default: return 'bg-amber-50 text-amber-900 border-amber-100';
    }
};
</script>

<template>
    <Head title="Mis Exámenes - IEE" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-background text-on-background flex justify-center overflow-x-hidden">
            
            <div class="w-full max-w-7xl p-8 md:p-12 space-y-16">
                <!-- Academic Header -->
                <header class="flex flex-col md:flex-row md:items-end justify-between gap-10">
                    <div class="space-y-4">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/5 border border-primary/10 rounded-full">
                            <div class="w-2 h-2 rounded-full bg-[#D4AF37]"></div>
                            <span class="text-[10px] font-black text-primary uppercase tracking-[0.25em]">Expediente de Desempeño</span>
                        </div>
                        <h1 class="text-4xl lg:text-5xl font-serif font-bold italic tracking-tight text-on-background">Evaluaciones Académicas</h1>
                        <p class="text-on-surface-variant font-serif italic text-lg max-w-2xl leading-relaxed">Gestione sus certificaciones y realice el seguimiento de su progresión hacia la excelencia profesional.</p>
                    </div>
                </header>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    <!-- Main Content Panel -->
                    <div class="lg:col-span-8 space-y-16">
                        
                        <!-- Available Exams Catalog -->
                        <section class="space-y-8">
                            <div class="flex items-center justify-between">
                                <h3 class="text-2xl font-serif font-bold italic text-on-background flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-primary/5 flex items-center justify-center">
                                        <PlayCircle class="w-5 h-5 text-primary" />
                                    </div>
                                    Exámenes Habilitados
                                </h3>
                            </div>
                            
                            <div v-if="exams.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div v-for="exam in exams" :key="exam.id" 
                                    class="group bg-white rounded-[3rem] border border-outline-variant/20 p-8 shadow-sm hover:shadow-2xl hover:shadow-primary/5 transition-all relative overflow-hidden flex flex-col"
                                    :class="exam.progress < 100 ? 'opacity-60 cursor-not-allowed' : 'hover:-translate-y-2'"
                                >
                                    <!-- Goal post watermark -->
                                    <div class="absolute -right-8 -top-8 opacity-[0.03] group-hover:scale-125 transition-transform duration-[2s]">
                                        <Award class="w-48 h-48 text-primary" />
                                    </div>

                                    <div class="flex justify-between items-start mb-8 relative z-10">
                                        <div class="w-14 h-14 rounded-[1.5rem] flex items-center justify-center shadow-inner" :class="exam.progress < 100 ? 'bg-background' : 'bg-primary/5 border border-primary/10'">
                                            <FileText class="w-6 h-6" :class="exam.progress < 100 ? 'text-outline-variant' : 'text-primary'" />
                                        </div>
                                        <div class="px-5 py-2 bg-background rounded-2xl text-[9px] font-black text-on-surface-variant uppercase tracking-[0.2em] border border-outline-variant/20">
                                            Oportunidades: {{ exam.attempts_left }} 
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2 flex-1 relative z-10">
                                        <h4 class="font-serif font-bold text-xl text-on-background italic leading-[1.3] group-hover:text-primary transition-colors line-clamp-2">{{ exam.title }}</h4>
                                        <p class="text-[9px] text-[#D4AF37] font-black uppercase tracking-[0.25em] mb-6 italic">{{ exam.course_title }}</p>
                                    </div>
                                    
                                    <!-- Requirement Bar -->
                                    <div v-if="exam.progress < 100" class="mb-8 relative z-10">
                                        <div class="flex items-center justify-between text-[8px] font-black uppercase tracking-[0.25em] text-outline-variant mb-3">
                                            <span>Progreso Lectivo</span>
                                            <span class="text-primary">{{ exam.progress }}%</span>
                                        </div>
                                        <div class="h-1.5 w-full bg-background rounded-full overflow-hidden border border-outline-variant/10 p-0.5">
                                             <div class="h-full bg-gradient-to-r from-[#D4AF37] to-primary rounded-full transition-all duration-1000 shadow-sm shadow-[#D4AF37]/20" :style="{ width: exam.progress + '%' }"></div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-8 border-t border-outline-variant/10 mt-auto relative z-10">
                                        <div class="flex items-center gap-3 text-[10px] font-bold italic text-outline-variant">
                                            <Clock class="w-4 h-4" />
                                            <span>{{ exam.time_limit }} min</span>
                                        </div>
                                        
                                        <template v-if="!exam.passed">
                                            <Link v-if="exam.attempts_left > 0 && exam.progress >= 100" :href="route('student.exams.take', exam.id)" class="px-8 py-4 rounded-2xl bg-primary text-white text-[9px] font-black uppercase tracking-[0.3em] hover:bg-on-background transition-all shadow-xl shadow-primary/10 active:scale-95">
                                                Iniciar Cátedra
                                            </Link>
                                            <div v-else-if="exam.progress < 100" class="px-6 py-4 bg-background rounded-2xl text-[8px] font-black text-outline-variant/60 uppercase tracking-[0.2em] border border-outline-variant/10 flex items-center gap-3 italic">
                                                <RotateCw class="w-3.5 h-3.5" /> Requisito de Lectura
                                            </div>
                                            <a v-else href="https://wa.me/51900000000?text=Hola,%20agote%20mis%20intentos%20en%20el%20examen" target="_blank" class="px-6 py-4 bg-rose-50 rounded-2xl text-[8px] font-black text-rose-600 uppercase tracking-[0.2em] border border-rose-100 flex items-center gap-3 hover:bg-rose-100 transition-all italic">
                                                <RotateCw class="w-3.5 h-3.5" /> Reintentos Agotados
                                            </a>
                                        </template>
                                        <span v-else class="text-[9px] font-black text-primary uppercase tracking-[0.3em] px-6 py-4 bg-primary/5 rounded-2xl italic flex items-center gap-2">
                                            <div class="w-1 h-1 rounded-full bg-primary"></div> Calificado
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="py-24 flex flex-col items-center text-center bg-white rounded-[4rem] border border-dashed border-outline-variant/30 shadow-inner group">
                                 <div class="w-20 h-20 bg-background rounded-[1.75rem] border border-outline-variant/20 flex items-center justify-center mb-8 group-hover:bg-primary/5 transition-colors">
                                    <ClipboardCheck class="w-8 h-8 text-outline-variant" />
                                 </div>
                                 <h4 class="text-xl font-serif font-bold italic text-on-background mb-3">Sin evaluaciones lectivas</h4>
                                 <p class="text-on-surface-variant font-serif italic text-sm leading-relaxed max-w-xs">No se presentan evaluaciones magistrales disponibles en su cronograma actual.</p>
                            </div>
                        </section>

                        <!-- Academic History Table -->
                        <section class="space-y-8">
                            <h3 class="text-2xl font-serif font-bold italic text-on-background flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-primary/5 flex items-center justify-center">
                                    <BarChart3 class="w-5 h-5 text-primary" />
                                </div>
                                Historial de Excelencia
                            </h3>
                            
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
                                                    <div class="w-1.5 h-1.5 rounded-full" :class="attempt.status === 'aprobado' ? 'bg-primary' : 'bg-rose-900'"></div>
                                                    {{ attempt.status }}
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>

                    <!-- Statistics Sidebar -->
                    <aside class="lg:col-span-4 space-y-10 h-fit lg:sticky lg:top-8">
                        <!-- Global Stats Card -->
                        <div class="bg-primary rounded-[4rem] p-12 text-white shadow-2xl shadow-primary/20 relative overflow-hidden group">
                            <!-- Cinematic highlights -->
                            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-[0.03] -rotate-45 translate-x-32 -translate-y-32 group-hover:translate-x-24 transition-transform duration-[3s]"></div>
                            <div class="absolute bottom-0 left-0 w-48 h-48 bg-[#D4AF37] opacity-[0.05] rounded-full blur-[80px]"></div>

                            <div class="relative z-10 space-y-12">
                                <div class="space-y-4">
                                    <h3 class="text-xl font-serif font-bold italic opacity-60 uppercase tracking-widest text-center">Promedio lectivo</h3>
                                    <div class="flex flex-col items-center">
                                        <div class="relative inline-block">
                                            <span class="text-8xl lg:text-9xl font-serif font-bold italic leading-none">{{ (stats.average_score / 20 * 10).toFixed(1) }}</span>
                                            <div class="absolute -right-6 bottom-4 flex flex-col items-center opacity-40">
                                                <span class="text-sm font-black text-white/50 border-t border-white/20 pt-1">SCORE 2.0</span>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex items-center justify-center gap-3 text-white/40">
                                            <span class="text-[10px] font-black uppercase tracking-[0.4em] italic">{{ stats.average_score.toFixed(1) }} / 20 Puntos</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white/5 backdrop-blur-3xl rounded-[2.5rem] border border-white/10 p-8 space-y-8">
                                    <div class="flex items-center gap-6">
                                        <div class="w-16 h-16 bg-[#D4AF37]/20 rounded-[1.25rem] border border-[#D4AF37]/30 flex items-center justify-center shadow-inner">
                                            <Award class="w-8 h-8 text-[#D4AF37]" />
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[9px] font-black uppercase tracking-[0.3em] text-white/40 mb-1">Cátedras Acreditadas</p>
                                            <p class="text-2xl font-serif font-bold italic">{{ stats.certificate_count < 10 ? '0' + stats.certificate_count : stats.certificate_count }} Certificaciones</p>
                                        </div>
                                    </div>
                                    
                                    <div class="h-px bg-white/10 w-full"></div>
                                    
                                    <p class="text-sm font-serif italic text-white/60 leading-relaxed text-center">Su rendimiento académico lo sitúa en el percentil superior institucional.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Assistance Card -->
                        <div class="bg-white rounded-[3rem] border border-outline-variant/20 p-8 shadow-sm group cursor-pointer hover:bg-background transition-all relative overflow-hidden">
                             <div class="absolute -right-4 -bottom-4 opacity-[0.02] group-hover:scale-125 transition-transform duration-[2s]">
                                <RotateCw class="w-24 h-24 text-primary" />
                             </div>
                             
                             <div class="flex items-center gap-6 relative z-10">
                                <div class="w-16 h-16 bg-[#D4AF37]/5 rounded-[1.5rem] border border-[#D4AF37]/10 flex items-center justify-center group-hover:bg-[#D4AF37]/10 transition-colors">
                                    <RotateCw class="w-7 h-7 text-[#D4AF37]" />
                                </div>
                                <div>
                                    <h4 class="text-lg font-serif font-bold italic text-on-background">Solicitar Re-evaluación</h4>
                                    <p class="text-[10px] text-on-surface-variant/60 font-black uppercase tracking-widest italic leading-relaxed">Si ha agotado sus intentos lectivos reglamentarios</p>
                                </div>
                             </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </AppLayout>
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

.animate-spin-slow {
    animation: spin 3s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

tr:last-child td:first-child {
    border-bottom-left-radius: 3.5rem;
}
tr:last-child td:last-child {
    border-bottom-right-radius: 3.5rem;
}

section, aside > div {
    animation: slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
