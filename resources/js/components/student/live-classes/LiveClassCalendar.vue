<script setup lang="ts">
import { Calendar as CalendarIcon, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed, ref } from 'vue';

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
const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
const daysOfWeek = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

const monthYearLabel = computed(() => `${monthNames[currentMonth.value]} ${currentYear.value}`);

function nextMonth() {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
}
function prevMonth() {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
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
        days.push({
            day: i,
            month: currentMonth.value,
            year: currentYear.value,
            currentMonth: true,
            isToday: i === now.getDate() && currentMonth.value === now.getMonth() && currentYear.value === now.getFullYear(),
        });
    }
    return days;
});

function getClassesForDay(day: { year: number; month: number; day: number }) {
    const dayStr = `${day.year}-${(day.month + 1).toString().padStart(2, '0')}-${day.day.toString().padStart(2, '0')}`;
    return props.liveClasses.filter((c) => c.date === dayStr);
}

const colors = ['bg-primary', 'bg-[#003366]', 'bg-[#800000]', 'bg-[#004d40]', 'bg-[#4a148c]', 'bg-[#c29100]'];
const lightColors = [
    'bg-primary text-white',
    'bg-[#003366] text-white',
    'bg-[#800000] text-white',
    'bg-[#004d40] text-white',
    'bg-[#4a148c] text-white',
    'bg-[#c29100] text-white',
];

function getCourseLightColor(courseId: number) {
    return lightColors[courseId % lightColors.length];
}
</script>

<template>
    <div>
        <div
            class="mb-6 hidden items-center gap-4 self-start rounded-2xl border border-outline-variant/20 bg-white p-2 px-5 shadow-xl shadow-primary/5 md:rounded-[2rem] md:p-3 md:px-8 lg:flex"
        >
            <button @click="prevMonth" class="rounded-2xl p-3 text-outline-variant transition-all hover:bg-background hover:text-primary">
                <ChevronLeft class="h-5 w-5" />
            </button>
            <span class="min-w-[160px] text-center text-sm font-black uppercase italic tracking-[0.2em] text-on-background">{{
                monthYearLabel
            }}</span>
            <button @click="nextMonth" class="rounded-2xl p-3 text-outline-variant transition-all hover:bg-background hover:text-primary">
                <ChevronRight class="h-5 w-5" />
            </button>
        </div>
        <div class="group relative flex flex-col overflow-hidden rounded-[4rem] border border-outline-variant/20 bg-white shadow-2xl">
            <div class="duration-[3s] pointer-events-none absolute -bottom-20 -right-20 opacity-[0.03] transition-transform group-hover:scale-110">
                <CalendarIcon class="h-96 w-96 text-primary" />
            </div>
            <div class="grid shrink-0 grid-cols-7 gap-0 border-b border-outline-variant/10 bg-background/50">
                <div
                    v-for="day in daysOfWeek"
                    :key="day"
                    class="border-r border-outline-variant/10 py-5 text-center text-[10px] font-black uppercase italic tracking-[0.3em] text-primary/60 last:border-r-0"
                >
                    {{ day }}
                </div>
            </div>
            <div class="custom-scrollbar grid flex-1 grid-cols-7 gap-px overflow-y-auto bg-outline-variant/10">
                <div
                    v-for="(day, idx) in calendarDays"
                    :key="idx"
                    class="group/cell relative flex min-h-[120px] flex-col gap-3 bg-white p-4 transition-all hover:bg-background"
                    :class="[
                        !day.currentMonth ? 'translate-z-0 opacity-20 grayscale' : '',
                        day.isToday ? 'after:absolute after:inset-0 after:border-2 after:border-[#D4AF37]/30' : '',
                    ]"
                >
                    <span
                        class="font-serif text-sm font-bold italic transition-colors"
                        :class="day.isToday ? 'text-[#D4AF37]' : 'text-outline-variant/80 group-hover/cell:text-on-background'"
                        >{{ day.day }}</span
                    >
                    <div class="relative z-10 flex flex-col gap-2">
                        <div
                            v-for="c in getClassesForDay(day)"
                            :key="c.id"
                            @click="emit('goToClassroom', c)"
                            class="transform cursor-pointer rounded-xl border border-transparent p-2 text-[8px] font-black uppercase tracking-widest shadow-sm transition-all hover:-translate-y-1 hover:border-primary/20 hover:shadow-lg hover:shadow-primary/10"
                            :class="getCourseLightColor(c.course_id)"
                        >
                            {{ c.time.split(' ')[0] }} {{ c.title }}
                        </div>
                    </div>
                    <div v-if="day.isToday" class="absolute right-4 top-4 h-2 w-2 animate-pulse rounded-full bg-[#D4AF37]"></div>
                </div>
            </div>
        </div>
    </div>
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
</style>
