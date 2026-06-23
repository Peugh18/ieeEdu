<script setup lang="ts">
import BottomNav from '@/components/student/BottomNav.vue';
import ExamAvailableList from '@/components/student/exams/ExamAvailableList.vue';
import ExamHistory from '@/components/student/exams/ExamHistory.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Award } from 'lucide-vue-next';
import { ref } from 'vue';

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
        <div class="flex min-h-screen justify-center overflow-x-hidden bg-background text-on-background">
            <div class="w-full max-w-7xl space-y-6 p-4 md:space-y-16 md:p-12">
                <header class="flex flex-col justify-between gap-4 md:flex-row md:items-end md:gap-10">
                    <div class="space-y-4">
                        <div class="inline-flex items-center gap-1.5 rounded-full border border-primary/10 bg-primary/5 px-3 py-1 md:px-4 md:py-1.5">
                            <div class="h-1.5 w-1.5 rounded-full bg-[#D4AF37] md:h-2 md:w-2"></div>
                            <span class="text-[9px] font-black uppercase tracking-[0.2em] text-primary md:text-[10px] md:tracking-[0.25em]"
                                >Expediente de Desempeño</span
                            >
                        </div>
                        <h1 class="font-serif text-2xl font-bold italic tracking-tight text-on-background md:text-5xl">Evaluaciones Académicas</h1>
                        <p class="hidden max-w-2xl font-serif text-lg italic leading-relaxed text-on-surface-variant md:block">
                            Gestione sus certificaciones y realice el seguimiento de su progresión hacia la excelencia profesional.
                        </p>
                    </div>
                </header>

                <div class="mb-2 flex gap-3 md:hidden">
                    <div class="flex-1 rounded-2xl bg-primary p-4 text-center text-white">
                        <div class="font-serif text-2xl font-bold italic">{{ ((props.stats.average_score / 20) * 10).toFixed(1) }}</div>
                        <div class="mt-0.5 text-[9px] font-black uppercase tracking-[0.2em] opacity-60">Promedio</div>
                    </div>
                    <div class="flex-1 rounded-2xl border border-outline-variant/20 bg-white p-4 text-center">
                        <div class="font-serif text-2xl font-bold italic text-primary">{{ props.stats.certificate_count }}</div>
                        <div class="mt-0.5 text-[9px] font-black uppercase tracking-[0.2em] text-on-surface-variant/50">Certificados</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 md:gap-12 lg:grid-cols-12">
                    <div class="space-y-8 lg:col-span-8">
                        <div class="mb-6 flex items-center gap-8 border-b border-outline-variant/20">
                            <button
                                @click="activeTab = 'available'"
                                class="group relative flex items-center gap-2 pb-5 text-[11px] font-black uppercase tracking-[0.25em] transition-all"
                                :class="activeTab === 'available' ? 'text-primary' : 'text-on-surface-variant/40 hover:text-on-surface-variant'"
                            >
                                Exámenes Habilitados<span
                                    v-if="availableExams.length > 0"
                                    class="rounded-full bg-primary/10 px-2 py-0.5 text-[9px] font-bold leading-none text-primary"
                                    >{{ availableExams.length }}</span
                                ><span
                                    class="absolute bottom-0 left-0 h-[4px] w-full scale-x-0 bg-primary/10 transition-transform group-hover:scale-x-50"
                                ></span
                                ><span
                                    v-if="activeTab === 'available'"
                                    class="absolute bottom-0 left-0 h-[4px] w-full rounded-full bg-primary"
                                ></span>
                            </button>
                            <button
                                @click="activeTab = 'history'"
                                class="group relative flex items-center gap-2 pb-5 text-[11px] font-black uppercase tracking-[0.25em] transition-all"
                                :class="activeTab === 'history' ? 'text-primary' : 'text-on-surface-variant/40 hover:text-on-surface-variant'"
                            >
                                Historial de Excelencia<span
                                    v-if="history.length > 0"
                                    class="rounded-full bg-on-surface-variant/10 px-2 py-0.5 text-[9px] font-bold leading-none text-on-surface-variant"
                                    >{{ history.length }}</span
                                ><span
                                    class="absolute bottom-0 left-0 h-[4px] w-full scale-x-0 bg-primary/10 transition-transform group-hover:scale-x-50"
                                ></span
                                ><span v-if="activeTab === 'history'" class="absolute bottom-0 left-0 h-[4px] w-full rounded-full bg-primary"></span>
                            </button>
                        </div>
                        <div v-show="activeTab === 'available'" class="space-y-6 duration-500 animate-in fade-in md:space-y-8">
                            <ExamAvailableList :exams="availableExams" />
                            <div v-if="availableExams.length === 0 && blockedExams.length === 0" class="py-12 text-center text-on-surface-variant">
                                No tienes exámenes disponibles en este momento.
                            </div>
                            <div v-if="blockedExams.length > 0" class="space-y-4">
                                <p class="px-1 text-xs font-bold uppercase tracking-widest text-on-surface-variant/60">
                                    Próximamente — completa el curso al 100%
                                </p>
                                <ExamAvailableList :exams="blockedExams" />
                            </div>
                        </div>
                        <div v-show="activeTab === 'history'" class="space-y-6 duration-500 animate-in fade-in md:space-y-8">
                            <ExamHistory :history="history" />
                        </div>
                    </div>

                    <aside class="hidden h-fit space-y-10 md:block lg:sticky lg:top-8 lg:col-span-4">
                        <div class="group relative overflow-hidden rounded-[4rem] bg-primary p-12 text-white shadow-2xl shadow-primary/20">
                            <div
                                class="duration-[3s] absolute right-0 top-0 h-64 w-64 -translate-y-32 translate-x-32 -rotate-45 bg-white opacity-[0.03] transition-transform group-hover:translate-x-24"
                            ></div>
                            <div class="absolute bottom-0 left-0 h-48 w-48 rounded-full bg-[#D4AF37] opacity-[0.05] blur-[80px]"></div>
                            <div class="relative z-10 space-y-12">
                                <div class="space-y-4">
                                    <h3 class="text-center font-serif text-xl font-bold uppercase italic tracking-widest opacity-60">
                                        Promedio lectivo
                                    </h3>
                                    <div class="flex flex-col items-center">
                                        <div class="relative flex items-end gap-2">
                                            <span class="font-serif text-8xl font-bold italic leading-none lg:text-9xl">{{
                                                ((props.stats.average_score / 20) * 10).toFixed(1)
                                            }}</span>
                                            <div class="mb-3 flex flex-col items-center opacity-40">
                                                <span
                                                    class="whitespace-nowrap border-t border-white/20 pt-1 text-[10px] font-black tracking-wider text-white/50"
                                                    >SCORE 2.0</span
                                                >
                                            </div>
                                        </div>
                                        <div class="mt-4 flex items-center justify-center gap-3 text-white/40">
                                            <span class="text-[10px] font-black uppercase italic tracking-[0.4em]"
                                                >{{ props.stats.average_score.toFixed(1) }} / 20 Puntos</span
                                            >
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-8 rounded-[2.5rem] border border-white/10 bg-white/5 p-8 backdrop-blur-3xl">
                                    <div class="flex items-center gap-6">
                                        <div
                                            class="flex h-16 w-16 items-center justify-center rounded-[1.25rem] border border-[#D4AF37]/30 bg-[#D4AF37]/20 shadow-inner"
                                        >
                                            <Award class="h-8 w-8 text-[#D4AF37]" />
                                        </div>
                                        <div class="flex-1">
                                            <p class="mb-1 text-[9px] font-black uppercase tracking-[0.3em] text-white/40">Cátedras Acreditadas</p>
                                            <p class="font-serif text-2xl font-bold italic">
                                                {{
                                                    props.stats.certificate_count < 10
                                                        ? '0' + props.stats.certificate_count
                                                        : props.stats.certificate_count
                                                }}
                                                Certificaciones
                                            </p>
                                        </div>
                                    </div>
                                    <div class="h-px w-full bg-white/10"></div>
                                    <p class="text-center font-serif text-sm italic leading-relaxed text-white/60">
                                        Su rendimiento académico lo sitúa en el percentil superior institucional.
                                    </p>
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
