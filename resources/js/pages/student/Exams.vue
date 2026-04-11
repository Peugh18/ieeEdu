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
        case 'aprobado': return 'bg-emerald-50 text-emerald-600 border-emerald-100';
        case 'reprobado': return 'bg-rose-50 text-rose-600 border-rose-100';
        default: return 'bg-amber-50 text-amber-600 border-amber-100';
    }
};
</script>

<template>
    <Head title="Mis Exámenes - IEE" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-7xl mx-auto space-y-8 pb-20">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-serif font-bold text-gray-900">Evaluaciones Académicas</h1>
                    <p class="text-gray-500 mt-1">Sigue tu desempeño académico, revisa tus intentos y obtén tus certificaciones.</p>
                </div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 ">
                <!-- Pending Exams -->
                <div class="lg:col-span-2 space-y-6 flex flex-col">
                    <h3 class="text-xl font-serif font-bold text-gray-900 flex items-center gap-2">
                        <PlayCircle class="w-5 h-5 text-[#57572A]" />
                        Exámenes Disponibles
                    </h3>
                    
                    <div v-if="exams.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="exam in exams" :key="exam.id" 
                            class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-all relative overflow-hidden"
                            :class="exam.progress < 100 ? 'opacity-80' : ''"
                        >
                            <div class="flex justify-between items-start mb-4">
                                <div class="p-2.5 rounded-xl" :class="exam.progress < 100 ? 'bg-gray-100' : 'bg-blue-50'">
                                    <FileText class="w-5 h-5" :class="exam.progress < 100 ? 'text-gray-400' : 'text-blue-600'" />
                                </div>
                                <div class="px-3 py-1 bg-gray-100 rounded-lg text-[10px] font-bold text-gray-600 uppercase tracking-widest">
                                    Intentos: {{ exam.attempts_left }} 
                                </div>
                            </div>
                            
                            <h4 class="font-serif font-bold text-lg text-gray-900 mb-1 leading-snug">{{ exam.title }}</h4>
                            <p class="text-xs text-[#57572A] font-bold uppercase tracking-wider mb-2">{{ exam.course_title }}</p>
                            
                            <div v-if="exam.progress < 100" class="mb-4">
                                <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest text-amber-600 mb-1">
                                    <span>Progreso Requerido</span>
                                    <span>{{ exam.progress }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                     <div class="h-full bg-amber-500 rounded-full transition-all" :style="{ width: exam.progress + '%' }"></div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-50 mt-auto">
                                <div class="flex items-center gap-2 text-xs text-gray-500 font-medium">
                                    <Clock class="w-4 h-4" />
                                    <span>{{ exam.time_limit }} Minutos</span>
                                </div>
                                
                                <template v-if="!exam.passed">
                                    <Link v-if="exam.attempts_left > 0 && exam.progress >= 100" :href="route('student.exams.take', exam.id)" class="px-5 py-2.5 rounded-xl bg-[#57572A] text-white text-[11px] font-bold uppercase tracking-widest hover:bg-[#4a4a24] transition-colors shadow-sm">
                                        Iniciar Ahora
                                    </Link>
                                    <div v-else-if="exam.progress < 100" class="px-4 py-2.5 bg-gray-50 rounded-xl text-[9px] font-bold text-gray-400 uppercase tracking-widest border border-gray-100 flex items-center gap-2">
                                        <Clock class="w-3 h-3" /> Termina los videos
                                    </div>
                                    <a v-else href="https://wa.me/51900000000?text=Hola,%20agote%20mis%20intentos%20en%20el%20examen" target="_blank" class="px-4 py-2.5 bg-rose-50 rounded-xl text-[9px] font-bold text-rose-600 uppercase tracking-widest border border-rose-100 flex items-center gap-2 hover:bg-rose-100 transition-colors">
                                        <RotateCw class="w-3 h-3" /> Solicitar Re-evaluación
                                    </a>
                                </template>
                                <span v-else class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest px-4 py-2.5 bg-emerald-50 rounded-xl">🌟 Aprobado</span>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-12 px-6 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                         <div class="inline-flex p-3 bg-white rounded-2xl shadow-sm mb-4">
                            <ClipboardCheck class="w-6 h-6 text-gray-400" />
                         </div>
                         <h4 class="text-sm font-bold text-gray-900">No hay exámenes pendientes</h4>
                         <p class="text-xs text-gray-500 mt-1 mx-auto max-w-xs">Has completado todas tus evaluaciones activas por el momento.</p>
                    </div>

                    <!-- History -->
                    <div class="mt-8 space-y-6">
                        <h3 class="text-xl font-serif font-bold text-gray-900 flex items-center gap-2">
                            <BarChart3 class="w-5 h-5 text-[#57572A]" />
                            Historial de Resultados
                        </h3>
                        
                        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Examen / Módulo</th>
                                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Fecha completion</th>
                                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Calificación</th>
                                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right">Estructura / Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <tr v-for="attempt in history" :key="attempt.id + '_' + attempt.date" class="hover:bg-gray-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-900">{{ attempt.title }}</span>
                                                <span class="text-[10px] text-gray-400 uppercase font-bold">{{ attempt.course_title }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ attempt.date }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex justify-center">
                                                <div class="text-center">
                                                    <span class="text-lg font-serif font-bold block leading-none" :class="attempt.score >= attempt.passing_score ? 'text-emerald-600' : 'text-rose-600'">{{ attempt.score }}/20</span>
                                                    <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">Puntaje</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-[10px] font-bold uppercase tracking-wider" :class="getStatusStyles(attempt.status)">
                                                <CheckCircle2 v-if="attempt.status === 'aprobado'" class="w-3.5 h-3.5" />
                                                <XCircle v-else class="w-3.5 h-3.5" />
                                                {{ attempt.status }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Aside - stats or recommendations -->
                <aside class="space-y-6">
                    <div class="bg-[#57572A] rounded-3xl p-8 text-white shadow-lg relative overflow-hidden">
                        <div class="relative z-10">
                            <h3 class="text-xl font-serif font-bold mb-4">Promedio Académico</h3>
                            <div class="flex items-baseline gap-2 mb-2">
                                <span class="text-5xl font-serif font-bold">{{ stats.average_score > 0 ? stats.average_score : '--' }}</span>
                                <span v-if="stats.average_score > 0" class="text-lg text-white/60 font-serif">/ 20</span>
                            </div>
                            <p class="text-sm text-white/80 leading-relaxed mb-6 font-serif">¡Excelente desempeño! Mantienes un promedio superior para obtener la certificación con honores.</p>
                            
                            <div class="flex items-center gap-3 p-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20">
                                <div class="p-2 bg-white/20 rounded-xl">
                                    <Award class="w-5 h-5" />
                                </div>
                                <div class="flex-1">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-white/70">Certificados listos</p>
                                    <p class="text-sm font-bold">{{ stats.certificate_count < 10 ? '0' + stats.certificate_count : stats.certificate_count }} Certificaciones</p>
                                </div>
                            </div>
                        </div>
                        <!-- Abstract background element -->
                         <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
                    </div>

                    <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm flex items-center justify-between group cursor-pointer hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-amber-50 rounded-2xl group-hover:bg-amber-100 transition-colors">
                                <RotateCw class="w-6 h-6 text-amber-600" />
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Solicitar Re-evaluación</h4>
                                <p class="text-[11px] text-gray-500 font-serif">Si tus intentos terminaron</p>
                            </div>
                        </div>
                        <MoreVertical class="w-5 h-5 text-gray-300" />
                    </div>
                </aside>
            </div>
        </div>
    </AppLayout>
</template>
