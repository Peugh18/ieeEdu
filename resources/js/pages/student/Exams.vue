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
            <div class="w-full max-w-7xl px-4 py-6 md:px-8 md:py-8 space-y-6 md:space-y-8">
                <!-- Clean Modern Header -->
                <header class="mb-6 flex flex-col justify-between gap-4 md:flex-row md:items-start">
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary/10">
                                <Award class="h-4 w-4 text-primary" />
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest text-primary">Expediente de Desempeño</span>
                        </div>
                        <h1 class="text-2xl font-bold text-on-background md:text-3xl">Mis Exámenes</h1>
                        <p class="mt-1 text-sm text-on-surface-variant/70">
                            Gestiona tus certificaciones y realiza el seguimiento de tus evaluaciones.
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

                    <aside class="hidden h-fit space-y-6 md:block lg:sticky lg:top-8 lg:col-span-4">
                        <div class="overflow-hidden rounded-3xl border border-outline-variant/20 bg-surface-container-lowest p-6 shadow-sm">
                            <div class="mb-6 flex items-center justify-between">
                                <h3 class="text-xs font-bold uppercase tracking-widest text-on-surface-variant/80">
                                    Promedio
                               </h3>
                                <div class="inline-flex min-h-[44px] items-center rounded-full bg-primary/10 px-3 py-1 text-xs font-bold text-primary">Score 20</div>
                            </div>
                            
                            <div class="mb-8 flex items-end gap-2">
                                <span class="text-6xl font-black leading-none text-primary">{{ ((props.stats.average_score / 20) * 10).toFixed(1) }}</span>
                                <span class="mb-1 text-sm font-bold text-on-surface-variant/70">/ 10</span>
                            </div>

                            <div class="space-y-4 rounded-2xl bg-background p-4 border border-outline-variant/20">
                                <div class="flex items-center gap-4">
                                    <div class="flex min-h-[44px] min-w-[44px] shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                        <Award class="h-5 w-5" />
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant/80">Certificaciones</p>
                                        <p class="text-lg font-bold text-on-background">
                                            {{ props.stats.certificate_count < 10 ? '0' + props.stats.certificate_count : props.stats.certificate_count }}
                                        </p>
                                    </div>
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
