<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Calendar as CalendarIcon, Clock, Monitor, 
    Users, ExternalLink, ChevronLeft, ChevronRight 
} from 'lucide-vue-next';

interface LiveClass {
    id: number;
    title: string;
    day: string;
    date: string;
    time: string;
    course_title: string;
    course_slug: string;
    course_id: number;
    is_today: boolean;
    live_link?: string;
}

const props = defineProps<{
    live_classes: LiveClass[];
}>();

const openLiveSession = (link?: string) => {
    try {
        if (!link) {
            alert('Enlace de sesión no disponible todavía.');
            return;
        }
        window.open(link, '_blank', 'noopener,noreferrer');
    } catch (e) {
        alert('Error al abrir sesión: ' + e);
    }
};

const goToClassroom = (session: LiveClass) => {
    try {
        console.log('[iieEdu] Navigating to:', session);
        
        if (!session.course_slug) {
            alert('Error: El curso no tiene un identificador (slug) válido.');
            return;
        }

        // Construir la URL amigable
        const targetUrl = `/student/classroom/${session.course_slug}${session.id ? '/' + session.id : ''}`;
        
        // Intentar navegación SPA primero para mejor UX
        router.visit(targetUrl);
        
    } catch (e) {
        console.error('[iieEdu] Navigation error:', e);
        // Fuerza bruta si falla lo anterior
        if (session.course_slug) {
            window.location.href = `/student/classroom/${session.course_slug}${session.id ? '/' + session.id : ''}`;
        }
    }
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clases en Vivo', href: '/student/live-classes' },
];

const daysOfWeek = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

// Calendar Logic
const now = new Date();
const currentMonth = ref(now.getMonth());
const currentYear = ref(now.getFullYear());

const monthNames = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];

const monthYearLabel = computed(() => {
    return `${monthNames[currentMonth.value]} ${currentYear.value}`;
});

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

const prevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const calendarDays = computed(() => {
    const firstDayOfMonth = new Date(currentYear.value, currentMonth.value, 1);
    const lastDayOfMonth = new Date(currentYear.value, currentMonth.value + 1, 0);
    
    // Adjust for Monday start (0=Sun in JS, so we want 0=Mon)
    let startDay = firstDayOfMonth.getDay(); 
    startDay = startDay === 0 ? 6 : startDay - 1; // 0 (Mon) to 6 (Sun)
    
    const days = [];
    
    // Previous month padding
    const prevMonthLastDay = new Date(currentYear.value, currentMonth.value, 0).getDate();
    for (let i = startDay; i > 0; i--) {
        days.push({
            day: prevMonthLastDay - i + 1,
            month: currentMonth.value - 1,
            year: currentYear.value,
            currentMonth: false
        });
    }
    
    // Current month days
    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
        days.push({
            day: i,
            month: currentMonth.value,
            year: currentYear.value,
            currentMonth: true,
            isToday: i === now.getDate() && currentMonth.value === now.getMonth() && currentYear.value === now.getFullYear()
        });
    }
    
    return days;
});

const getClassesForDay = (day: any) => {
    const dayStr = `${day.year}-${(day.month + 1).toString().padStart(2, '0')}-${day.day.toString().padStart(2, '0')}`;
    return props.live_classes.filter(c => c.date === dayStr);
};

// Course Colors Helper (Dynamic University Palette)
const getCourseColor = (courseId: number) => {
    const colors = [
        'bg-[#57572A]', // IEE Olive
        'bg-[#003366]', // Navy Blue (University Classic)
        'bg-[#800000]', // Maroon/Crimson
        'bg-[#004d40]', // Deep Teal
        'bg-[#4a148c]', // Royal Purple
        'bg-[#c29100]', // Deep Gold
    ];
    return colors[courseId % colors.length];
};

const getCourseLightColor = (courseId: number) => {
    const colors = [
        'bg-[#57572A] text-white',
        'bg-[#003366] text-white',
        'bg-[#800000] text-white',
        'bg-[#004d40] text-white',
        'bg-[#4a148c] text-white',
        'bg-[#c29100] text-white',
    ];
    return colors[courseId % colors.length];
};
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
                    <button @click="prevMonth" class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 font-bold transition-all"><ChevronLeft class="w-5 h-5" /></button>
                    <span class="text-sm font-bold text-gray-900 px-2 min-w-[120px] text-center">{{ monthYearLabel }}</span>
                    <button @click="nextMonth" class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 font-bold transition-all"><ChevronRight class="w-5 h-5" /></button>
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
                         <div v-for="(day, idx) in calendarDays" :key="idx" 
                            class="aspect-square flex flex-col p-2 rounded-xl border border-gray-50 hover:bg-gray-50 transition-colors cursor-pointer relative group overflow-hidden"
                             :class="[
                                !day.currentMonth ? 'opacity-30' : '',
                                day.isToday ? 'bg-[#57572A]/5 border-[#57572A]/20' : ''
                            ]"
                        >
                             <span class="text-sm font-bold transition-colors" :class="day.isToday ? 'text-[#57572A]' : 'text-gray-600 group-hover:text-gray-900'">{{ day.day }}</span>
                             
                             <div class="mt-1 flex flex-col gap-1 overflow-hidden">
                                <div v-for="c in getClassesForDay(day)" :key="c.id" 
                                    class="text-[8px] font-bold px-1 py-0.5 rounded truncate"
                                    :class="getCourseLightColor(c.course_id)"
                                >
                                    {{ c.time.split(' ')[0] }} {{ c.title }}
                                </div>
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
                            
                            <div class="mt-5 flex flex-col sm:flex-row gap-2">
                                <button 
                                    @click="goToClassroom(session)"
                                    class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl text-[10px] font-bold uppercase tracking-[0.2em] transition-all shadow-md active:scale-95 border border-[#57572A]/20 text-[#57572A] hover:bg-gray-50"
                                >
                                     <Monitor class="w-3.5 h-3.5" />
                                     <span>{{ session.is_today ? 'Aula Virtual' : 'Ir al Aula Virtual' }}</span>
                                </button>
                                
                                <button 
                                    v-if="session.is_today && session.live_link"
                                    @click="openLiveSession(session.live_link)"
                                    class="flex-1 flex items-center justify-center gap-2 py-3 rounded-2xl text-[10px] font-bold uppercase tracking-[0.2em] transition-all shadow-md active:scale-95 bg-[#57572A] text-white hover:bg-[#4a4a24] shadow-green-900/20"
                                >
                                     <ExternalLink class="w-3.5 h-3.5" />
                                     <span>Unirse a Sesión </span>
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
