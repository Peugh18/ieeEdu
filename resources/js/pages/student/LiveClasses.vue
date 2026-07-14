<script setup lang="ts">
import LiveClassCalendar from '@/components/student/live-classes/LiveClassCalendar.vue';
import LiveClassSessionList from '@/components/student/live-classes/LiveClassSessionList.vue';
import BottomNav from '@/components/student/BottomNav.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { CalendarClock } from 'lucide-vue-next';

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

defineProps<{
    live_classes: LiveClass[];
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clases en Vivo', href: '/student/live-classes' },
];

function openLiveSession(link?: string) {
    if (!link) return;
    window.open(link, '_blank', 'noopener,noreferrer');
}

function goToClassroom(session: LiveClass) {
    if (!session.course_slug) return;
    router.visit(`/student/classroom/${session.course_slug}${session.id ? '/' + session.id : ''}`);
}
</script>

<template>
    <Head title="Clases en Vivo - IEE" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-[calc(100svh-4rem)] bg-background text-on-background">
            <div class="mx-auto w-full max-w-7xl px-4 py-6 md:px-8 md:py-8">

                <!-- Header compacto y moderno -->
                <header class="mb-6 flex items-start justify-between gap-4">
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-primary/10">
                                <CalendarClock class="h-4 w-4 text-primary" />
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest text-primary">Agenda Académica</span>
                        </div>
                        <h1 class="text-2xl font-bold text-on-background md:text-3xl">Clases en Vivo</h1>
                        <p class="mt-1 text-sm text-on-surface-variant/70">
                            Gestiona y accede a tus próximas sesiones en tiempo real.
                        </p>
                    </div>
                    <div v-if="live_classes.length" class="hidden shrink-0 md:flex items-center gap-2 rounded-2xl border border-primary/10 bg-primary/5 px-4 py-2">
                        <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-primary"></div>
                        <span class="text-xs font-bold text-primary">{{ live_classes.length }} sesión{{ live_classes.length > 1 ? 'es' : '' }} programada{{ live_classes.length > 1 ? 's' : '' }}</span>
                    </div>
                </header>

                <!-- Mobile: lista de tarjetas compactas -->
                <LiveClassSessionList
                    variant="mobile"
                    :sessions="live_classes"
                    @go-to-classroom="goToClassroom"
                    @open-live="openLiveSession"
                />

                <!-- Desktop: Calendario + sidebar sticky -->
                <div class="hidden lg:grid lg:grid-cols-12 lg:gap-6">
                    <!-- Calendario ocupa 8/12 cols -->
                    <div class="lg:col-span-8">
                        <LiveClassCalendar :live-classes="live_classes" @go-to-classroom="goToClassroom" />
                    </div>

                    <!-- Sidebar con sticky — se queda fijo mientras se scrollea la página -->
                    <aside class="lg:col-span-4">
                        <div class="sticky top-4 max-h-[calc(100vh-6rem)] overflow-hidden rounded-2xl border border-outline-variant/10 bg-surface-container-lowest p-4 shadow-sm">
                            <LiveClassSessionList
                                variant="sidebar"
                                :sessions="live_classes"
                                @go-to-classroom="goToClassroom"
                                @open-live="openLiveSession"
                            />
                        </div>
                    </aside>
                </div>

            </div>
        </div>
        <BottomNav active="live-classes" />
    </AppLayout>
</template>
