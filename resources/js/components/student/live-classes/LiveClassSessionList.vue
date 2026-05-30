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

const colors = ['bg-primary','bg-[#003366]','bg-[#800000]','bg-[#004d40]','bg-[#4a148c]','bg-[#c29100]'];
function getCourseColor(courseId: number) { return colors[courseId % colors.length]; }

const sortedSessions = [...props.sessions].sort((a, b) => Number(b.is_today) - Number(a.is_today));
</script>

<template>
    <div v-if="variant === 'mobile'" class="space-y-4 pb-28">
        <div v-if="sessions.length === 0" class="py-20 flex flex-col items-center text-center bg-white rounded-3xl border border-dashed border-outline-variant/40">
            <CalendarIcon class="w-12 h-12 text-outline-variant/50 mb-4" />
            <h4 class="text-base font-serif font-bold text-on-background italic mb-2">Sin sesiones registradas</h4>
            <p class="text-on-surface-variant font-serif italic text-sm max-w-[220px]">No hay cátedras en vivo para tu período académico actual.</p>
        </div>
        <template v-else>
            <div class="flex items-center justify-between">
                <h3 class="text-base font-black text-on-background uppercase tracking-[0.15em]">Próximas sesiones</h3>
                <span class="text-[10px] font-black text-[#D4AF37] uppercase tracking-[0.2em] px-3 py-1 bg-[#D4AF37]/5 rounded-full border border-[#D4AF37]/10">{{ sessions.length }} sesiones</span>
            </div>
            <div v-for="session in sortedSessions" :key="session.id" class="bg-white rounded-2xl border shadow-sm relative overflow-hidden active:scale-[0.98] transition-all" :class="session.is_today ? 'border-[#D4AF37]/40 shadow-[#D4AF37]/10 shadow-lg' : 'border-outline-variant/20'">
                <div v-if="session.is_today" class="flex items-center gap-2 bg-[#D4AF37] text-white text-[10px] font-black px-4 py-2 uppercase tracking-[0.2em]"><div class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>Sesión de hoy</div>
                <div class="p-4">
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col items-center justify-center w-14 h-14 rounded-2xl border shadow-inner flex-shrink-0" :class="session.is_today ? 'bg-[#D4AF37]/5 border-[#D4AF37]/20' : 'bg-background border-outline-variant/20'">
                            <span class="text-[8px] font-black text-outline-variant uppercase">{{ session.day }}</span>
                            <span class="text-xl font-serif font-bold text-on-background italic leading-none">{{ session.date.split('-').pop() }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-on-background leading-tight line-clamp-2" :title="session.title">{{ session.title }}</h4>
                            <div class="flex items-center gap-1.5 mt-1"><div class="w-1.5 h-1.5 rounded-full flex-shrink-0" :class="getCourseColor(session.course_id)"></div><p class="text-[9px] text-on-surface-variant/60 font-black uppercase tracking-[0.15em] truncate">{{ session.course_title }}</p></div>
                            <div class="flex items-center gap-1.5 mt-1 text-[10px] font-bold text-on-surface-variant/50"><Clock class="w-3.5 h-3.5 flex-shrink-0" /><span>{{ session.time }}</span></div>
                        </div>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <button @click="emit('goToClassroom', session)" class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] bg-background border border-outline-variant/20 text-primary active:scale-95 transition-all"><Monitor class="w-4 h-4" /> Aula Virtual</button>
                        <button v-if="session.is_today && session.live_link" @click="emit('openLive', session.live_link)" class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] bg-[#D4AF37] text-white shadow-lg active:scale-95 transition-all">Ingresar <ExternalLink class="w-4 h-4" /></button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <div v-else class="flex flex-col gap-8 h-full overflow-hidden">
        <div class="flex items-center justify-between shrink-0">
            <h3 class="text-2xl font-serif font-bold text-on-background italic tracking-tight">Cronograma Próximo</h3>
            <span class="text-[10px] font-black text-[#D4AF37] uppercase tracking-[0.2em] px-3 py-1 bg-[#D4AF37]/5 rounded-full border border-[#D4AF37]/10">{{ sessions.length }} Sesiones</span>
        </div>
        <div v-if="sessions.length === 0" class="flex-1 flex flex-col items-center justify-center p-12 text-center bg-white rounded-[4rem] border border-dashed border-outline-variant/40">
            <div class="w-24 h-24 bg-background rounded-[2rem] border border-outline-variant/20 flex items-center justify-center mb-8 shadow-inner"><CalendarIcon class="w-10 h-10 text-outline-variant" /></div>
            <h4 class="text-xl font-serif font-bold text-on-background italic mb-3">Expediente sin sesiones</h4>
            <p class="text-on-surface-variant font-serif italic text-sm leading-relaxed max-w-[240px]">No se registran cátedras magistrales en vivo para su período académico actual.</p>
        </div>
        <div v-else class="flex-1 overflow-y-auto custom-scrollbar space-y-6 pr-4 pb-12">
            <div v-for="session in sessions" :key="session.id" class="group bg-white rounded-[3rem] border border-outline-variant/20 p-8 shadow-sm hover:shadow-2xl hover:shadow-primary/5 hover:-translate-y-2 transition-all relative overflow-hidden">
                <div v-if="session.is_today" class="absolute top-0 right-0 bg-[#D4AF37] text-white text-[9px] font-black px-5 py-2 rounded-bl-3xl uppercase tracking-[0.3em] italic">En progreso</div>
                <div class="flex items-start gap-6">
                    <div class="flex flex-col items-center justify-center min-w-[70px] aspect-square rounded-[1.75rem] border border-outline-variant/30 shadow-inner group-hover:border-primary/30 transition-colors" :class="session.is_today ? 'bg-primary/5' : 'bg-background'">
                        <span class="text-[9px] font-black text-outline-variant uppercase group-hover:text-primary transition-colors">{{ session.day }}</span>
                        <span class="text-3xl font-serif font-bold text-on-background italic">{{ session.date.split('-').pop() }}</span>
                    </div>
                    <div class="flex-1 min-w-0 pt-1">
                        <h4 class="text-lg font-serif font-bold text-on-background leading-[1.3] mb-2 truncate group-hover:text-primary transition-colors" :title="session.title">{{ session.title }}</h4>
                        <div class="flex items-center gap-2 mb-4"><div class="w-1.5 h-1.5 rounded-full" :class="getCourseColor(session.course_id)"></div><p class="text-[9px] text-on-surface-variant/60 font-black uppercase tracking-[0.25em] truncate italic">{{ session.course_title }}</p></div>
                        <div class="flex items-center gap-6 text-[10px] font-bold italic text-on-surface-variant/40 group-hover:text-on-surface-variant transition-colors">
                            <div class="flex items-center gap-2.5"><Clock class="w-4 h-4" /><span>{{ session.time }}</span></div>
                            <div class="flex items-center gap-2.5"><Monitor class="w-4 h-4" /><span>{{ session.is_today && session.live_link ? 'Link Activo' : 'Cátedra Multimedia' }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex gap-4 transition-all" :class="session.is_today ? 'translate-y-0 opacity-100' : 'group-hover:translate-y-0 translate-y-2 opacity-80 group-hover:opacity-100'">
                    <button @click="emit('goToClassroom', session)" class="flex-1 flex items-center justify-center gap-3 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all bg-background border border-outline-variant/20 text-primary hover:bg-white hover:shadow-xl shadow-primary/5"><Monitor class="w-4 h-4" /><span>Aula Virtual</span></button>
                    <button v-if="session.is_today && session.live_link" @click="emit('openLive', session.live_link)" class="flex-1 flex items-center justify-center gap-3 py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all bg-primary text-white hover:bg-on-background shadow-2xl shadow-primary/30 relative group/btn overflow-hidden"><span class="relative z-10">Ingresar ahora</span><ExternalLink class="w-4 h-4 relative z-10" /><div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div></button>
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
.line-clamp-2 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; }
</style>
