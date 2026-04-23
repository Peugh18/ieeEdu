<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/student/BottomNav.vue';
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
        'bg-primary', // IEE Olive
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
        'bg-primary text-white',
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
        <div class="min-h-[calc(100svh-4rem)] bg-background text-on-background flex flex-col items-center">
            
            <div class="w-full max-w-7xl mx-auto p-5 md:p-12 flex flex-col gap-6 md:gap-10">
                <header class="flex flex-col md:flex-row md:items-end justify-between gap-4 md:gap-10">
                    <div class="space-y-2 md:space-y-4">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/5 border border-primary/10 rounded-full">
                            <div class="w-1.5 h-1.5 rounded-full bg-[#D4AF37] animate-pulse"></div>
                            <span class="text-[9px] font-black text-primary uppercase tracking-[0.25em]">Agenda Académica Digital</span>
                        </div>
                        <h1 class="text-2xl md:text-4xl lg:text-5xl font-serif font-bold text-on-background italic tracking-tight">Clases en Vivo</h1>
                        <p class="hidden md:block text-on-surface-variant font-serif italic text-lg max-w-2xl leading-relaxed">Sincronice su cronograma con las sesiones magistrales en tiempo real para una formación integral.</p>
                    </div>
                    
                    <div class="hidden lg:flex items-center gap-4 bg-white p-2 md:p-3 rounded-2xl md:rounded-[2rem] border border-outline-variant/20 shadow-xl shadow-primary/5 px-5 md:px-8 self-start">
                        <button @click="prevMonth" class="p-3 hover:bg-background rounded-2xl text-outline-variant hover:text-primary transition-all"><ChevronLeft class="w-5 h-5" /></button>
                        <span class="text-sm font-black text-on-background uppercase tracking-[0.2em] min-w-[160px] text-center italic">{{ monthYearLabel }}</span>
                        <button @click="nextMonth" class="p-3 hover:bg-background rounded-2xl text-outline-variant hover:text-primary transition-all"><ChevronRight class="w-5 h-5" /></button>
                    </div>
                </header>

                <!-- ── MOBILE: Agenda list only ── -->
                <div class="lg:hidden space-y-4">
                    <div v-if="live_classes.length === 0" class="py-16 flex flex-col items-center text-center bg-white rounded-3xl border border-dashed border-outline-variant/40">
                        <CalendarIcon class="w-10 h-10 text-outline-variant mb-4" />
                        <h4 class="text-base font-serif font-bold text-on-background italic mb-2">Sin sesiones registradas</h4>
                        <p class="text-on-surface-variant font-serif italic text-sm max-w-[220px]">No hay cátedras en vivo para tu período académico actual.</p>
                    </div>

                    <template v-else>
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-black text-on-background uppercase tracking-[0.15em]">Próximas sesiones</h3>
                            <span class="text-[10px] font-black text-[#D4AF37] uppercase tracking-[0.2em] px-3 py-1 bg-[#D4AF37]/5 rounded-full border border-[#D4AF37]/10">{{ live_classes.length }} sesiones</span>
                        </div>

                        <div
                            v-for="session in [...live_classes].sort((a,b) => Number(b.is_today) - Number(a.is_today))"
                            :key="session.id"
                            class="bg-white rounded-2xl border border-outline-variant/20 p-4 shadow-sm relative overflow-hidden"
                        >
                            <div v-if="session.is_today" class="absolute top-0 right-0 bg-[#D4AF37] text-white text-[9px] font-black px-4 py-1.5 rounded-bl-xl uppercase tracking-[0.2em]">Hoy</div>

                            <div class="flex items-center gap-4">
                                <div class="flex flex-col items-center justify-center min-w-[52px] h-[52px] rounded-2xl border border-outline-variant/20 shadow-inner" :class="session.is_today ? 'bg-primary/5 border-primary/20' : 'bg-background'">
                                    <span class="text-[8px] font-black text-outline-variant uppercase">{{ session.day }}</span>
                                    <span class="text-xl font-serif font-bold text-on-background italic leading-none">{{ session.date.split('-').pop() }}</span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-on-background leading-tight truncate" :title="session.title">{{ session.title }}</h4>
                                    <div class="flex items-center gap-1.5 mt-0.5 mb-2">
                                        <div class="w-1.5 h-1.5 rounded-full" :class="getCourseColor(session.course_id)"></div>
                                        <p class="text-[9px] text-on-surface-variant/60 font-black uppercase tracking-[0.2em] truncate">{{ session.course_title }}</p>
                                    </div>
                                    <div class="flex items-center gap-3 text-[10px] font-bold text-on-surface-variant/50">
                                        <div class="flex items-center gap-1.5"><Clock class="w-3.5 h-3.5" /><span>{{ session.time }}</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 flex gap-3">
                                <button
                                    @click="goToClassroom(session)"
                                    class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] bg-background border border-outline-variant/20 text-primary active:scale-95 transition-all"
                                >
                                    <Monitor class="w-4 h-4" /> Aula Virtual
                                </button>
                                <button
                                    v-if="session.is_today && session.live_link"
                                    @click="openLiveSession(session.live_link)"
                                    class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] bg-primary text-white shadow-lg shadow-primary/20 active:scale-95 transition-all"
                                >
                                    Ingresar <ExternalLink class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- ── DESKTOP: Calendar + Sidebar ── -->
                <div class="hidden lg:grid lg:grid-cols-12 gap-12 pb-10 min-h-[600px]">
                    <!-- Calendar View: Institutional Grid -->
                    <div class="lg:col-span-8 flex flex-col bg-white rounded-[4rem] border border-outline-variant/20 shadow-2xl relative overflow-hidden group">
                         <!-- Decorative watermark -->
                         <div class="absolute -right-20 -bottom-20 opacity-[0.03] pointer-events-none group-hover:scale-110 transition-transform duration-[3s]">
                             <CalendarIcon class="w-96 h-96 text-primary" />
                         </div>

                         <div class="grid grid-cols-7 gap-0 border-b border-outline-variant/10 bg-background/50 shrink-0">
                            <div v-for="day in daysOfWeek" :key="day" class="py-5 text-center text-[10px] font-black text-primary/60 uppercase tracking-[0.3em] italic border-r border-outline-variant/10 last:border-r-0">
                                {{ day }}
                            </div>
                        </div>
                        
                        <div class="flex-1 grid grid-cols-7 gap-px bg-outline-variant/10 overflow-y-auto custom-scrollbar">
                             <div v-for="(day, idx) in calendarDays" :key="idx" 
                                class="bg-white min-h-[120px] p-4 transition-all hover:bg-background relative flex flex-col gap-3 group/cell"
                                 :class="[
                                    !day.currentMonth ? 'opacity-20 translate-z-0 grayscale' : '',
                                    day.isToday ? 'after:absolute after:inset-0 after:border-2 after:border-[#D4AF37]/30' : ''
                                ]"
                            >
                                 <span class="text-sm font-serif font-bold italic transition-colors" :class="day.isToday ? 'text-[#D4AF37]' : 'text-outline-variant/80 group-hover/cell:text-on-background'">{{ day.day }}</span>
                                 
                                 <div class="flex flex-col gap-2 relative z-10">
                                    <div v-for="c in getClassesForDay(day)" :key="c.id" 
                                        @click="goToClassroom(c)"
                                        class="p-2 rounded-xl text-[8px] font-black uppercase tracking-widest cursor-pointer transform hover:-translate-y-1 transition-all shadow-sm hover:shadow-lg hover:shadow-primary/10 border border-transparent hover:border-primary/20"
                                        :class="getCourseLightColor(c.course_id)"
                                    >
                                        {{ c.time.split(' ')[0] }} {{ c.title }}
                                    </div>
                                 </div>

                                 <div v-if="day.isToday" class="absolute top-4 right-4 w-2 h-2 rounded-full bg-[#D4AF37] animate-pulse"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Agenda Sidebar: Cinematic List -->
                    <div class="lg:col-span-4 flex flex-col gap-8 h-full overflow-hidden">
                        <div class="flex items-center justify-between shrink-0">
                            <h3 class="text-2xl font-serif font-bold text-on-background italic tracking-tight">Cronograma Próximo</h3>
                            <span class="text-[10px] font-black text-[#D4AF37] uppercase tracking-[0.2em] px-3 py-1 bg-[#D4AF37]/5 rounded-full border border-[#D4AF37]/10">{{ live_classes.length }} Sesiones</span>
                        </div>
                        
                        <div class="flex-1 overflow-y-auto custom-scrollbar space-y-6 pr-4 pb-12">
                            <div v-for="session in live_classes" :key="session.id" 
                                class="group bg-white rounded-[3rem] border border-outline-variant/20 p-8 shadow-sm hover:shadow-2xl hover:shadow-primary/5 hover:-translate-y-2 transition-all relative overflow-hidden"
                            >
                                <div v-if="session.is_today" class="absolute top-0 right-0 bg-[#D4AF37] text-white text-[9px] font-black px-5 py-2 rounded-bl-3xl uppercase tracking-[0.3em] italic animate-in slide-in-from-right duration-500">
                                    En progreso
                                </div>

                                <div class="flex items-start gap-6">
                                    <div class="flex flex-col items-center justify-center min-w-[70px] aspect-square rounded-[1.75rem] border border-outline-variant/30 shadow-inner group-hover:border-primary/30 transition-colors" :class="session.is_today ? 'bg-primary/5' : 'bg-background'">
                                        <span class="text-[9px] font-black text-outline-variant uppercase group-hover:text-primary transition-colors">{{ session.day }}</span>
                                        <span class="text-3xl font-serif font-bold text-on-background italic">{{ session.date.split('-').pop() }}</span>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0 pt-1">
                                        <h4 class="text-lg font-serif font-bold text-on-background leading-[1.3] mb-2 truncate group-hover:text-primary transition-colors" :title="session.title">{{ session.title }}</h4>
                                        <div class="flex items-center gap-2 mb-4">
                                            <div class="w-1.5 h-1.5 rounded-full" :class="getCourseColor(session.course_id)"></div>
                                            <p class="text-[9px] text-on-surface-variant/60 font-black uppercase tracking-[0.25em] truncate italic">{{ session.course_title }}</p>
                                        </div>
                                        
                                        <div class="flex items-center gap-6 text-[10px] font-bold italic text-on-surface-variant/40 group-hover:text-on-surface-variant transition-colors">
                                            <div class="flex items-center gap-2.5">
                                                <Clock class="w-4 h-4" />
                                                <span>{{ session.time }}</span>
                                            </div>
                                            <div class="flex items-center gap-2.5">
                                                <Monitor class="w-4 h-4" />
                                                <span>{{ session.is_today && session.live_link ? 'Link Activo' : 'Cátedra Multimedia' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-8 flex gap-4 transition-all" :class="session.is_today ? 'translate-y-0 opacity-100' : 'group-hover:translate-y-0 translate-y-2 opacity-80 group-hover:opacity-100'">
                                    <button 
                                        @click="goToClassroom(session)"
                                        class="flex-1 flex items-center justify-center gap-3 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all bg-background border border-outline-variant/20 text-primary hover:bg-white hover:shadow-xl shadow-primary/5"
                                    >
                                         <Monitor class="w-4 h-4" />
                                         <span>Aula Virtual</span>
                                    </button>
                                    
                                    <button 
                                        v-if="session.is_today && session.live_link"
                                        @click="openLiveSession(session.live_link)"
                                        class="flex-1 flex items-center justify-center gap-3 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all bg-primary text-white hover:bg-on-background shadow-2xl shadow-primary/30 relative group/btn overflow-hidden"
                                    >
                                         <span class="relative z-10">Ingresar ahora</span>
                                         <ExternalLink class="w-4 h-4 relative z-10" />
                                         <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div v-if="live_classes.length === 0" class="flex-1 flex flex-col items-center justify-center p-12 text-center bg-white rounded-[4rem] border border-dashed border-outline-variant/40 animate-in fade-in duration-1000">
                             <div class="w-24 h-24 bg-background rounded-[2rem] border border-outline-variant/20 flex items-center justify-center mb-8 shadow-inner">
                                <CalendarIcon class="w-10 h-10 text-outline-variant" />
                             </div>
                             <h4 class="text-xl font-serif font-bold text-on-background italic mb-3">Expediente sin sesiones</h4>
                             <p class="text-on-surface-variant font-serif italic text-sm leading-relaxed max-w-[240px]">No se registran cátedras magistrales en vivo para su período académico actual.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <BottomNav active="live-classes" />
    </AppLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
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

/* Institutional Transitions */
.group-hover\:scale-110 {
    transition: transform 3s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-in {
    animation: fadeIn 0.8s ease-out forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
