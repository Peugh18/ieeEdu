<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/student/BottomNav.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Award } from 'lucide-vue-next';
import ExamAvailableList from '@/components/student/exams/ExamAvailableList.vue';
import ExamHistory from '@/components/student/exams/ExamHistory.vue';

interface ExamAttempt {
    id: number;
    title: string;
    course_title: string;
    date: string;
    score: number;
    passing_score: number;
    status: 'bloqueado' | 'disponible' | 'aprobado' | 'reprobado' | 'pendiente';
    attempts_left: number;
    progress: number;
    passed: boolean;
    time_limit: number;
}

const props = defineProps<{
    availableExams: ExamAttempt[];
    blockedExams: ExamAttempt[];
    history: ExamAttempt[];
    stats: { average_score: number; certificate_count: number };
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Mis Exámenes', href: '/student/exams' },
];

const activeTab = ref<'available' | 'history'>('available');
</script>

<template>
    <Head title="Mis Exámenes - IEE" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-background text-on-background flex justify-center overflow-x-hidden">
            <div class="w-full max-w-7xl p-4 md:p-12 space-y-6 md:space-y-16">
                <header class="flex flex-col md:flex-row md:items-end justify-between gap-4 md:gap-10">
                    <div class="space-y-4">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 md:px-4 md:py-1.5 bg-primary/5 border border-primary/10 rounded-full">
                            <div class="w-1.5 h-1.5 md:w-2 md:h-2 rounded-full bg-[#D4AF37]"></div>
                            <span class="text-[9px] md:text-[10px] font-black text-primary uppercase tracking-[0.2em] md:tracking-[0.25em]">Expediente de Desempeño</span>
                        </div>
                        <h1 class="text-2xl md:text-5xl font-serif font-bold italic tracking-tight text-on-background">Evaluaciones Académicas</h1>
                        <p class="hidden md:block text-on-surface-variant font-serif italic text-lg max-w-2xl leading-relaxed">Gestione sus certificaciones y realice el seguimiento de su progresión hacia la excelencia profesional.</p>
                    </div>
                </header>

                <div class="md:hidden flex gap-3 mb-2">
                    <div class="flex-1 bg-primary rounded-2xl p-4 text-white text-center">
                        <div class="text-2xl font-serif font-bold italic">{{ (props.stats.average_score / 20 * 10).toFixed(1) }}</div>
                        <div class="text-[9px] font-black uppercase tracking-[0.2em] opacity-60 mt-0.5">Promedio</div>
                    </div>
                    <div class="flex-1 bg-white rounded-2xl p-4 border border-outline-variant/20 text-center">
                        <div class="text-2xl font-serif font-bold italic text-primary">{{ props.stats.certificate_count }}</div>
                        <div class="text-[9px] font-black uppercase tracking-[0.2em] text-on-surface-variant/50 mt-0.5">Certificados</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-12">
                    <div class="lg:col-span-8 space-y-8">
                        <div class="flex items-center gap-8 border-b border-outline-variant/20 mb-6">
                            <button @click="activeTab = 'available'" class="pb-5 text-[11px] font-black uppercase tracking-[0.25em] transition-all relative group flex items-center gap-2" :class="activeTab === 'available' ? 'text-primary' : 'text-on-surface-variant/40 hover:text-on-surface-variant'">Exámenes Habilitados<span v-if="availableExams.length > 0" class="px-2 py-0.5 text-[9px] bg-primary/10 text-primary rounded-full font-bold leading-none">{{ availableExams.length }}</span><span class="absolute bottom-0 left-0 w-full h-[4px] scale-x-0 group-hover:scale-x-50 transition-transform bg-primary/10"></span><span v-if="activeTab === 'available'" class="absolute bottom-0 left-0 w-full h-[4px] bg-primary rounded-full"></span></button>
                            <button @click="activeTab = 'history'" class="pb-5 text-[11px] font-black uppercase tracking-[0.25em] transition-all relative group flex items-center gap-2" :class="activeTab === 'history' ? 'text-primary' : 'text-on-surface-variant/40 hover:text-on-surface-variant'">Historial de Excelencia<span v-if="history.length > 0" class="px-2 py-0.5 text-[9px] bg-on-surface-variant/10 text-on-surface-variant rounded-full font-bold leading-none">{{ history.length }}</span><span class="absolute bottom-0 left-0 w-full h-[4px] scale-x-0 group-hover:scale-x-50 transition-transform bg-primary/10"></span><span v-if="activeTab === 'history'" class="absolute bottom-0 left-0 w-full h-[4px] bg-primary rounded-full"></span></button>
                        </div>
                        <div v-show="activeTab === 'available'" class="animate-in fade-in duration-500 space-y-6 md:space-y-8">
                            <ExamAvailableList :exams="availableExams" />
                            <div v-if="availableExams.length === 0 && blockedExams.length === 0" class="text-center py-12 text-on-surface-variant">
                                No tienes exámenes disponibles en este momento.
                            </div>
                            <div v-if="blockedExams.length > 0" class="space-y-4">
                                <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant/60 px-1">
                                    Próximamente — completa el curso al 100%
                                </p>
                                <ExamAvailableList :exams="blockedExams" />
                            </div>
                        </div>
                        <div v-show="activeTab === 'history'" class="animate-in fade-in duration-500 space-y-6 md:space-y-8"><ExamHistory :history="history" /></div>
                    </div>

                    <aside class="hidden md:block lg:col-span-4 space-y-10 h-fit lg:sticky lg:top-8">
                        <div class="bg-primary rounded-[4rem] p-12 text-white shadow-2xl shadow-primary/20 relative overflow-hidden group">
                            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-[0.03] -rotate-45 translate-x-32 -translate-y-32 group-hover:translate-x-24 transition-transform duration-[3s]"></div>
                            <div class="absolute bottom-0 left-0 w-48 h-48 bg-[#D4AF37] opacity-[0.05] rounded-full blur-[80px]"></div>
                            <div class="relative z-10 space-y-12">
                                <div class="space-y-4">
                                    <h3 class="text-xl font-serif font-bold italic opacity-60 uppercase tracking-widest text-center">Promedio lectivo</h3>
                                    <div class="flex flex-col items-center">
                                        <div class="relative inline-block">
                                            <span class="text-8xl lg:text-9xl font-serif font-bold italic leading-none">{{ (props.stats.average_score / 20 * 10).toFixed(1) }}</span>
                                            <div class="absolute -right-6 bottom-4 flex flex-col items-center opacity-40"><span class="text-sm font-black text-white/50 border-t border-white/20 pt-1">SCORE 2.0</span></div>
                                        </div>
                                        <div class="mt-4 flex items-center justify-center gap-3 text-white/40"><span class="text-[10px] font-black uppercase tracking-[0.4em] italic">{{ props.stats.average_score.toFixed(1) }} / 20 Puntos</span></div>
                                    </div>
                                </div>
                                <div class="bg-white/5 backdrop-blur-3xl rounded-[2.5rem] border border-white/10 p-8 space-y-8">
                                    <div class="flex items-center gap-6">
                                        <div class="w-16 h-16 bg-[#D4AF37]/20 rounded-[1.25rem] border border-[#D4AF37]/30 flex items-center justify-center shadow-inner"><Award class="w-8 h-8 text-[#D4AF37]" /></div>
                                        <div class="flex-1">
                                            <p class="text-[9px] font-black uppercase tracking-[0.3em] text-white/40 mb-1">Cátedras Acreditadas</p>
                                            <p class="text-2xl font-serif font-bold italic">{{ props.stats.certificate_count < 10 ? '0' + props.stats.certificate_count : props.stats.certificate_count }} Certificaciones</p>
                                        </div>
                                    </div>
                                    <div class="h-px bg-white/10 w-full"></div>
                                    <p class="text-sm font-serif italic text-white/60 leading-relaxed text-center">Su rendimiento académico lo sitúa en el percentil superior institucional.</p>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
        <div class="h-16 lg:hidden"></div>
        <BottomNav active="exams" />
    </AppLayout>
</template>
