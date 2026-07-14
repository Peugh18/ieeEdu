<script setup lang="ts">
import { Calendar as CalendarIcon, Clock, ExternalLink, Monitor, Video } from 'lucide-vue-next';

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
    <!-- === MOBILE: lista compacta vertical === -->
    <div v-if="variant === 'mobile'" class="space-y-3 pb-24 lg:hidden">
        <div v-if="sessions.length === 0" class="flex flex-col items-center rounded-2xl border border-dashed border-outline-variant/30 bg-surface-container-lowest py-14 text-center">
            <CalendarIcon class="mb-3 h-10 w-10 text-outline-variant/40" />
            <h4 class="text-sm font-bold text-on-background">Sin sesiones registradas</h4>
            <p class="mt-1 max-w-[220px] text-xs text-on-surface-variant/60">No hay cátedras en vivo para tu período académico actual.</p>
        </div>
        <template v-else>
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-sm font-bold uppercase tracking-widest text-on-background">Próximas sesiones</h3>
                <span class="rounded-full border border-primary/10 bg-primary/5 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-widest text-primary">{{ sessions.length }}</span>
            </div>
            <div
                v-for="session in sortedSessions"
                :key="session.id"
                class="relative overflow-hidden rounded-2xl border bg-surface-container-lowest shadow-sm transition-all active:scale-[0.99]"
                :class="session.is_today ? 'border-primary/30 shadow-primary/10' : 'border-outline-variant/15'"
            >
                <!-- Live banner -->
                <div v-if="session.is_today" class="flex items-center gap-2 bg-primary px-4 py-1.5 text-[9px] font-black uppercase tracking-widest text-white">
                    <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-white"></div>
                    Sesión de hoy
                </div>
                <div class="flex items-center gap-4 p-4">
                    <!-- Date badge -->
                    <div
                        class="flex h-12 w-12 shrink-0 flex-col items-center justify-center rounded-xl border"
                        :class="session.is_today ? 'border-primary/20 bg-primary/5' : 'border-outline-variant/15 bg-background'"
                    >
                        <span class="text-[8px] font-black uppercase text-on-surface-variant/50">{{ session.day }}</span>
                        <span class="text-xl font-bold leading-none text-on-background">{{ session.date.split('-').pop() }}</span>
                    </div>
                    <!-- Info -->
                    <div class="min-w-0 flex-1">
                        <h4 class="line-clamp-1 text-sm font-bold text-on-background" :title="session.title">{{ session.title }}</h4>
                        <div class="mt-0.5 flex items-center gap-1.5">
                            <div class="h-1.5 w-1.5 shrink-0 rounded-full" :class="getCourseColor(session.course_id)"></div>
                            <p class="truncate text-[9px] font-bold uppercase tracking-wider text-on-surface-variant/60">{{ session.course_title }}</p>
                        </div>
                        <div class="mt-1 flex items-center gap-1 text-[10px] text-on-surface-variant/50">
                            <Clock class="h-3 w-3 shrink-0" /> {{ session.time }}
                        </div>
                    </div>
                    <!-- Actions -->
                    <div class="flex shrink-0 flex-col gap-2">
                        <button
                            v-if="session.is_today && session.live_link"
                            @click="emit('openLive', session.live_link)"
                            class="flex items-center gap-1.5 rounded-lg bg-primary px-3 py-2 text-[10px] font-black uppercase tracking-wider text-white"
                        >
                            <Video class="h-3.5 w-3.5" /> En vivo
                        </button>
                        <button
                            @click="emit('goToClassroom', session)"
                            class="flex items-center gap-1.5 rounded-lg border border-outline-variant/20 bg-background px-3 py-2 text-[10px] font-black uppercase tracking-wider text-primary"
                        >
                            <Monitor class="h-3.5 w-3.5" /> Aula
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- === SIDEBAR: tarjetas compactas con scroll propio === -->
    <div v-else class="flex h-full flex-col">
        <!-- Header -->
        <div class="mb-4 flex shrink-0 items-center justify-between">
            <h3 class="text-sm font-bold uppercase tracking-widest text-on-background">Cronograma Próximo</h3>
            <span class="rounded-full border border-primary/10 bg-primary/5 px-2.5 py-0.5 text-[10px] font-bold text-primary">{{ sessions.length }} sesiones</span>
        </div>

        <!-- Empty state -->
        <div v-if="sessions.length === 0" class="flex flex-1 flex-col items-center justify-center rounded-2xl border border-dashed border-outline-variant/30 py-12 text-center">
            <CalendarIcon class="mb-3 h-8 w-8 text-outline-variant/40" />
            <h4 class="text-sm font-bold text-on-background">Sin sesiones</h4>
            <p class="mt-1 max-w-[180px] text-xs text-on-surface-variant/60">No hay cátedras en vivo registradas para este período.</p>
        </div>

        <!-- Session list with its own scrollable area -->
        <div v-else class="custom-scrollbar flex-1 space-y-2.5 overflow-y-auto pr-1">
            <div
                v-for="session in sortedSessions"
                :key="session.id"
                class="group relative overflow-hidden rounded-2xl border bg-surface-container-lowest transition-all hover:border-outline-variant/30 hover:shadow-sm"
                :class="session.is_today ? 'border-primary/30' : 'border-outline-variant/15'"
            >
                <!-- Live strip -->
                <div v-if="session.is_today" class="flex items-center gap-2 bg-primary/90 px-3 py-1 text-[8px] font-black uppercase tracking-widest text-white">
                    <div class="h-1 w-1 animate-pulse rounded-full bg-white"></div> En progreso
                </div>

                <div class="flex items-center gap-3 p-3">
                    <!-- Date badge -->
                    <div
                        class="flex h-11 w-11 shrink-0 flex-col items-center justify-center rounded-xl border text-center"
                        :class="session.is_today ? 'border-primary/20 bg-primary/5' : 'border-outline-variant/15 bg-background'"
                    >
                        <span class="text-[7px] font-black uppercase text-on-surface-variant/50">{{ session.day }}</span>
                        <span class="text-base font-bold leading-none text-on-background">{{ session.date.split('-').pop() }}</span>
                    </div>

                    <!-- Info -->
                    <div class="min-w-0 flex-1">
                        <h4 class="line-clamp-1 text-xs font-bold text-on-background" :title="session.title">{{ session.title }}</h4>
                        <div class="mt-0.5 flex items-center gap-1">
                            <div class="h-1.5 w-1.5 shrink-0 rounded-full" :class="getCourseColor(session.course_id)"></div>
                            <p class="truncate text-[9px] font-bold uppercase tracking-wide text-on-surface-variant/50">{{ session.course_title }}</p>
                        </div>
                        <div class="mt-0.5 flex items-center gap-1 text-[9px] text-on-surface-variant/40">
                            <Clock class="h-2.5 w-2.5 shrink-0" /> {{ session.time }}
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="flex shrink-0 flex-col gap-1.5">
                        <button
                            v-if="session.is_today && session.live_link"
                            @click="emit('openLive', session.live_link)"
                            class="flex items-center gap-1 rounded-lg bg-primary px-2.5 py-1.5 text-[9px] font-black uppercase tracking-wide text-white"
                        >
                            <Video class="h-3 w-3" /> Live
                        </button>
                        <button
                            @click="emit('goToClassroom', session)"
                            class="flex items-center gap-1 rounded-lg border border-outline-variant/20 px-2.5 py-1.5 text-[9px] font-black uppercase tracking-wide text-primary"
                        >
                            <Monitor class="h-3 w-3" /> Aula
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
