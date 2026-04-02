<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Calendar, Clock, Monitor, Users, ExternalLink } from 'lucide-vue-next';

interface LiveClass {
    id: number;
    title: string;
    day: string;
    date: string;
    time: string;
    course_title: string;
    is_today: boolean;
}

const props = defineProps<{
    live_classes: LiveClass[];
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clases en Vivo', href: '/student/live-classes' },
];

const daysOfWeek = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

// Placeholder for current month view logic
const currentMonth = ref('Abril 2026');
</script>

<template>
    <Head title="Clases en Vivo - IEE" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6 max-w-7xl mx-auto space-y-8">
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-serif font-bold text-gray-900">Clases en Vivo</h1>
                    <p class="text-gray-500 mt-1">Accede a tus sesiones programadas y únete a la comunidad de aprendizaje.</p>
                </div>
                
                <div class="flex items-center gap-4 bg-white p-2 rounded-xl border shadow-sm">
                    <button class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 font-bold transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                    <span class="text-sm font-bold text-gray-900 px-2">{{ currentMonth }}</span>
                    <button class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 font-bold transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                </div>
            </header>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Calendar View -->
                <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden p-6">
                     <div class="grid grid-cols-7 gap-1 border-b border-gray-100 pb-4 mb-4">
                        <div v-for="day in daysOfWeek" :key="day" class="text-center text-xs font-bold text-[#57572A] uppercase tracking-widest">
                            {{ day }}
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-7 gap-2">
                         <!-- Placeholder calendar grid -->
                         <div v-for="i in 30" :key="i" 
                            class="aspect-square flex flex-col p-2 rounded-xl border border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer relative group"
                            :class="{ 'bg-indigo-50 border-indigo-100': i === 2 }"
                        >
                             <span class="text-sm font-bold text-gray-600 group-hover:text-gray-900 transition-colors">{{ i }}</span>
                             <div v-if="i === 2" class="mt-auto">
                                <div class="w-1.5 h-1.5 bg-indigo-600 rounded-full mx-auto"></div>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Session Details / Sidebar -->
                <div class="space-y-6">
                    <h3 class="text-xl font-serif font-bold text-gray-900">Agenda de Sesiones</h3>
                    
                    <div class="space-y-4">
                        <div v-for="session in live_classes" :key="session.id" 
                            class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden"
                            :class="{ 'ring-2 ring-indigo-500 ring-offset-2': session.is_today }"
                        >
                            <div v-if="session.is_today" class="absolute top-0 right-0 bg-indigo-500 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-widest">
                                Hoy
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="flex flex-col items-center justify-center min-w-[60px] p-3 bg-gray-50 rounded-xl">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">{{ session.day }}</span>
                                    <span class="text-xl font-serif font-bold text-gray-900">{{ session.date.split('-').pop() }}</span>
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-900 leading-tight mb-1 truncate" :title="session.title">{{ session.title }}</h4>
                                    <p class="text-[11px] text-[#57572A] font-bold uppercase tracking-wider mb-2">{{ session.course_title }}</p>
                                    
                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                        <div class="flex items-center gap-1.5">
                                            <Clock class="w-3.5 h-3.5 text-gray-400" />
                                            <span>{{ session.time }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <Users class="w-3.5 h-3.5 text-gray-400" />
                                            <span>Zoom Sessions</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-5">
                                <button 
                                    class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all"
                                    :class="session.is_today ? 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-indigo-200 shadow-lg' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                >
                                    <Monitor v-if="session.is_today" class="w-4 h-4" />
                                    <span>{{ session.is_today ? 'Unirse a la Clase' : 'Ver Detalles' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="live_classes.length === 0" class="py-12 px-6 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                         <div class="inline-flex p-3 bg-white rounded-2xl shadow-sm mb-4">
                            <Calendar class="w-6 h-6 text-gray-400" />
                         </div>
                         <h4 class="text-sm font-bold text-gray-900">Sin sesiones programadas</h4>
                         <p class="text-xs text-gray-500 mt-1 mx-auto max-w-xs">No tienes sesiones en vivo registradas para esta semana. Revisa tu cronograma de cursos.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
