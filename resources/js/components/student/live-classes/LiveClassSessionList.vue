<script setup lang="ts">
import { Calendar as CalendarIcon, Clock, ExternalLink, Monitor } from 'lucide-vue-next';

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
    sessions: LiveClass[];
    variant: 'mobile' | 'sidebar';
}>();

const emit = defineEmits<{
    (e: 'goToClassroom', session: LiveClass): void;
    (e: 'openLive', link?: string): void;
}>();

const colors = ['bg-primary', 'bg-[#003366]', 'bg-[#800000]', 'bg-[#004d40]', 'bg-[#4a148c]', 'bg-[#c29100]'];
function getCourseColor(courseId: number) {
    return colors[courseId % colors.length];
}

const sortedSessions = [...props.sessions].sort((a, b) => Number(b.is_today) - Number(a.is_today));
</script>

<template>
    <div v-if="variant === 'mobile'" class="space-y-4 pb-28">
        <div
            v-if="sessions.length === 0"
            class="flex flex-col items-center rounded-3xl border border-dashed border-outline-variant/40 bg-white py-20 text-center"
        >
            <CalendarIcon class="mb-4 h-12 w-12 text-outline-variant/50" />
            <h4 class="mb-2 font-serif text-base font-bold italic text-on-background">Sin sesiones registradas</h4>
            <p class="max-w-[220px] font-serif text-sm italic text-on-surface-variant">No hay cátedras en vivo para tu período académico actual.</p>
        </div>
        <template v-else>
            <div class="flex items-center justify-between">
                <h3 class="text-base font-black uppercase tracking-[0.15em] text-on-background">Próximas sesiones</h3>
                <span
                    class="rounded-full border border-[#D4AF37]/10 bg-[#D4AF37]/5 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-[#D4AF37]"
                    >{{ sessions.length }} sesiones</span
                >
            </div>
            <div
                v-for="session in sortedSessions"
                :key="session.id"
                class="relative overflow-hidden rounded-2xl border bg-white shadow-sm transition-all active:scale-[0.98]"
                :class="session.is_today ? 'border-[#D4AF37]/40 shadow-lg shadow-[#D4AF37]/10' : 'border-outline-variant/20'"
            >
                <div
                    v-if="session.is_today"
                    class="flex items-center gap-2 bg-[#D4AF37] px-4 py-2 text-[10px] font-black uppercase tracking-[0.2em] text-white"
                >
                    <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-white"></div>
                    Sesión de hoy
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-14 w-14 flex-shrink-0 flex-col items-center justify-center rounded-2xl border shadow-inner"
                            :class="session.is_today ? 'border-[#D4AF37]/20 bg-[#D4AF37]/5' : 'border-outline-variant/20 bg-background'"
                        >
                            <span class="text-[8px] font-black uppercase text-outline-variant">{{ session.day }}</span>
                            <span class="font-serif text-xl font-bold italic leading-none text-on-background">{{
                                session.date.split('-').pop()
                            }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="line-clamp-2 text-sm font-bold leading-tight text-on-background" :title="session.title">
                                {{ session.title }}
                            </h4>
                            <div class="mt-1 flex items-center gap-1.5">
                                <div class="h-1.5 w-1.5 flex-shrink-0 rounded-full" :class="getCourseColor(session.course_id)"></div>
                                <p class="truncate text-[9px] font-black uppercase tracking-[0.15em] text-on-surface-variant/60">
                                    {{ session.course_title }}
                                </p>
                            </div>
                            <div class="mt-1 flex items-center gap-1.5 text-[10px] font-bold text-on-surface-variant/50">
                                <Clock class="h-3.5 w-3.5 flex-shrink-0" /><span>{{ session.time }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <button
                            @click="emit('goToClassroom', session)"
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl border border-outline-variant/20 bg-background py-2.5 text-[10px] font-black uppercase tracking-[0.15em] text-primary transition-all active:scale-95"
                        >
                            <Monitor class="h-4 w-4" /> Aula Virtual
                        </button>
                        <button
                            v-if="session.is_today && session.live_link"
                            @click="emit('openLive', session.live_link)"
                            class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-[#D4AF37] py-2.5 text-[10px] font-black uppercase tracking-[0.15em] text-white shadow-lg transition-all active:scale-95"
                        >
                            Ingresar <ExternalLink class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div v-else class="flex h-full flex-col gap-8 overflow-hidden">
        <div class="flex shrink-0 items-center justify-between">
            <h3 class="font-serif text-2xl font-bold italic tracking-tight text-on-background">Cronograma Próximo</h3>
            <span
                class="rounded-full border border-[#D4AF37]/10 bg-[#D4AF37]/5 px-3 py-1 text-[10px] font-black uppercase tracking-[0.2em] text-[#D4AF37]"
                >{{ sessions.length }} Sesiones</span
            >
        </div>
        <div
            v-if="sessions.length === 0"
            class="flex flex-1 flex-col items-center justify-center rounded-[4rem] border border-dashed border-outline-variant/40 bg-white p-12 text-center"
        >
            <div class="mb-8 flex h-24 w-24 items-center justify-center rounded-[2rem] border border-outline-variant/20 bg-background shadow-inner">
                <CalendarIcon class="h-10 w-10 text-outline-variant" />
            </div>
            <h4 class="mb-3 font-serif text-xl font-bold italic text-on-background">Expediente sin sesiones</h4>
            <p class="max-w-[240px] font-serif text-sm italic leading-relaxed text-on-surface-variant">
                No se registran cátedras magistrales en vivo para su período académico actual.
            </p>
        </div>
        <div v-else class="custom-scrollbar flex-1 space-y-6 overflow-y-auto pb-12 pr-4">
            <div
                v-for="session in sessions"
                :key="session.id"
                class="group relative overflow-hidden rounded-[3rem] border border-outline-variant/20 bg-white p-8 shadow-sm transition-all hover:-translate-y-2 hover:shadow-2xl hover:shadow-primary/5"
            >
                <div
                    v-if="session.is_today"
                    class="absolute right-0 top-0 rounded-bl-3xl bg-[#D4AF37] px-5 py-2 text-[9px] font-black uppercase italic tracking-[0.3em] text-white"
                >
                    En progreso
                </div>
                <div class="flex items-start gap-6">
                    <div
                        class="flex aspect-square min-w-[70px] flex-col items-center justify-center rounded-[1.75rem] border border-outline-variant/30 shadow-inner transition-colors group-hover:border-primary/30"
                        :class="session.is_today ? 'bg-primary/5' : 'bg-background'"
                    >
                        <span class="text-[9px] font-black uppercase text-outline-variant transition-colors group-hover:text-primary">{{
                            session.day
                        }}</span>
                        <span class="font-serif text-3xl font-bold italic text-on-background">{{ session.date.split('-').pop() }}</span>
                    </div>
                    <div class="min-w-0 flex-1 pt-1">
                        <h4
                            class="mb-2 truncate font-serif text-lg font-bold leading-[1.3] text-on-background transition-colors group-hover:text-primary"
                            :title="session.title"
                        >
                            {{ session.title }}
                        </h4>
                        <div class="mb-4 flex items-center gap-2">
                            <div class="h-1.5 w-1.5 rounded-full" :class="getCourseColor(session.course_id)"></div>
                            <p class="truncate text-[9px] font-black uppercase italic tracking-[0.25em] text-on-surface-variant/60">
                                {{ session.course_title }}
                            </p>
                        </div>
                        <div
                            class="flex items-center gap-6 text-[10px] font-bold italic text-on-surface-variant/40 transition-colors group-hover:text-on-surface-variant"
                        >
                            <div class="flex items-center gap-2.5">
                                <Clock class="h-4 w-4" /><span>{{ session.time }}</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <Monitor class="h-4 w-4" /><span>{{
                                    session.is_today && session.live_link ? 'Link Activo' : 'Cátedra Multimedia'
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="mt-8 flex gap-4 transition-all"
                    :class="
                        session.is_today ? 'translate-y-0 opacity-100' : 'translate-y-2 opacity-80 group-hover:translate-y-0 group-hover:opacity-100'
                    "
                >
                    <button
                        @click="emit('goToClassroom', session)"
                        class="flex flex-1 items-center justify-center gap-3 rounded-2xl border border-outline-variant/20 bg-background py-4 text-[10px] font-black uppercase tracking-[0.3em] text-primary shadow-primary/5 transition-all hover:bg-white hover:shadow-xl"
                    >
                        <Monitor class="h-4 w-4" /><span>Aula Virtual</span>
                    </button>
                    <button
                        v-if="session.is_today && session.live_link"
                        @click="emit('openLive', session.live_link)"
                        class="group/btn relative flex flex-1 items-center justify-center gap-3 overflow-hidden rounded-2xl bg-primary py-4 text-[10px] font-black uppercase tracking-[0.3em] text-white shadow-2xl shadow-primary/30 transition-all hover:bg-on-background"
                    >
                        <span class="relative z-10">Ingresar ahora</span><ExternalLink class="relative z-10 h-4 w-4" />
                        <div
                            class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-1000 group-hover/btn:translate-x-full"
                        ></div>
                    </button>
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
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    line-clamp: 2;
}
</style>
