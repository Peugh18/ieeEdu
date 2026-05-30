<script setup lang="ts">
import { computed, ref } from 'vue';
import { Calendar as CalendarIcon, ChevronLeft, ChevronRight } from 'lucide-vue-next';

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
    liveClasses: LiveClass[];
}>();

const emit = defineEmits<{
    (e: 'goToClassroom', session: LiveClass): void;
}>();

const now = new Date();
const currentMonth = ref(now.getMonth());
const currentYear = ref(now.getFullYear());
const monthNames = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
const daysOfWeek = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];

const monthYearLabel = computed(() => `${monthNames[currentMonth.value]} ${currentYear.value}`);

function nextMonth() {
    if (currentMonth.value === 11) { currentMonth.value = 0; currentYear.value++; } else { currentMonth.value++; }
}
function prevMonth() {
    if (currentMonth.value === 0) { currentMonth.value = 11; currentYear.value--; } else { currentMonth.value--; }
}

const calendarDays = computed(() => {
    const firstDayOfMonth = new Date(currentYear.value, currentMonth.value, 1);
    const lastDayOfMonth = new Date(currentYear.value, currentMonth.value + 1, 0);
    let startDay = firstDayOfMonth.getDay();
    startDay = startDay === 0 ? 6 : startDay - 1;
    const days = [];
    const prevMonthLastDay = new Date(currentYear.value, currentMonth.value, 0).getDate();
    for (let i = startDay; i > 0; i--) {
        days.push({ day: prevMonthLastDay - i + 1, month: currentMonth.value - 1, year: currentYear.value, currentMonth: false });
    }
    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
        days.push({ day: i, month: currentMonth.value, year: currentYear.value, currentMonth: true, isToday: i === now.getDate() && currentMonth.value === now.getMonth() && currentYear.value === now.getFullYear() });
    }
    return days;
});

function getClassesForDay(day: { year: number; month: number; day: number }) {
    const dayStr = `${day.year}-${(day.month + 1).toString().padStart(2, '0')}-${day.day.toString().padStart(2, '0')}`;
    return props.liveClasses.filter(c => c.date === dayStr);
}

const colors = ['bg-primary','bg-[#003366]','bg-[#800000]','bg-[#004d40]','bg-[#4a148c]','bg-[#c29100]'];
const lightColors = ['bg-primary text-white','bg-[#003366] text-white','bg-[#800000] text-white','bg-[#004d40] text-white','bg-[#4a148c] text-white','bg-[#c29100] text-white'];

function getCourseLightColor(courseId: number) { return lightColors[courseId % lightColors.length]; }
</script>

<template>
    <div>
        <div class="hidden lg:flex items-center gap-4 bg-white p-2 md:p-3 rounded-2xl md:rounded-[2rem] border border-outline-variant/20 shadow-xl shadow-primary/5 px-5 md:px-8 self-start mb-6">
            <button @click="prevMonth" class="p-3 hover:bg-background rounded-2xl text-outline-variant hover:text-primary transition-all"><ChevronLeft class="w-5 h-5" /></button>
            <span class="text-sm font-black text-on-background uppercase tracking-[0.2em] min-w-[160px] text-center italic">{{ monthYearLabel }}</span>
            <button @click="nextMonth" class="p-3 hover:bg-background rounded-2xl text-outline-variant hover:text-primary transition-all"><ChevronRight class="w-5 h-5" /></button>
        </div>
        <div class="flex flex-col bg-white rounded-[4rem] border border-outline-variant/20 shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-20 -bottom-20 opacity-[0.03] pointer-events-none group-hover:scale-110 transition-transform duration-[3s]">
                <CalendarIcon class="w-96 h-96 text-primary" />
            </div>
            <div class="grid grid-cols-7 gap-0 border-b border-outline-variant/10 bg-background/50 shrink-0">
                <div v-for="day in daysOfWeek" :key="day" class="py-5 text-center text-[10px] font-black text-primary/60 uppercase tracking-[0.3em] italic border-r border-outline-variant/10 last:border-r-0">{{ day }}</div>
            </div>
            <div class="flex-1 grid grid-cols-7 gap-px bg-outline-variant/10 overflow-y-auto custom-scrollbar">
                <div v-for="(day, idx) in calendarDays" :key="idx" class="bg-white min-h-[120px] p-4 transition-all hover:bg-background relative flex flex-col gap-3 group/cell" :class="[!day.currentMonth ? 'opacity-20 translate-z-0 grayscale' : '', day.isToday ? 'after:absolute after:inset-0 after:border-2 after:border-[#D4AF37]/30' : '']">
                    <span class="text-sm font-serif font-bold italic transition-colors" :class="day.isToday ? 'text-[#D4AF37]' : 'text-outline-variant/80 group-hover/cell:text-on-background'">{{ day.day }}</span>
                    <div class="flex flex-col gap-2 relative z-10">
                        <div v-for="c in getClassesForDay(day)" :key="c.id" @click="emit('goToClassroom', c)" class="p-2 rounded-xl text-[8px] font-black uppercase tracking-widest cursor-pointer transform hover:-translate-y-1 transition-all shadow-sm hover:shadow-lg hover:shadow-primary/10 border border-transparent hover:border-primary/20" :class="getCourseLightColor(c.course_id)">{{ c.time.split(' ')[0] }} {{ c.title }}</div>
                    </div>
                    <div v-if="day.isToday" class="absolute top-4 right-4 w-2 h-2 rounded-full bg-[#D4AF37] animate-pulse"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar { width: 6px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(87, 87, 42, 0.08); border-radius: 20px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(87, 87, 42, 0.15); }
</style>
