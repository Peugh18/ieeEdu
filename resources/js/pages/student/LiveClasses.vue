<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/student/BottomNav.vue';
import { Head } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import LiveClassCalendar from '@/components/student/live-classes/LiveClassCalendar.vue';
import LiveClassSessionList from '@/components/student/live-classes/LiveClassSessionList.vue';

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
    if (!link) { alert('Enlace de sesión no disponible todavía.'); return; }
    window.open(link, '_blank', 'noopener,noreferrer');
}

function goToClassroom(session: LiveClass) {
    if (!session.course_slug) { alert('Error: El curso no tiene un identificador válido.'); return; }
    router.visit(`/student/classroom/${session.course_slug}${session.id ? '/' + session.id : ''}`);
}
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
                </header>

                <LiveClassSessionList variant="mobile" :sessions="live_classes" @go-to-classroom="goToClassroom" @open-live="openLiveSession" />

                <div class="hidden lg:grid lg:grid-cols-12 gap-12 pb-10 min-h-[600px]">
                    <div class="lg:col-span-8">
                        <LiveClassCalendar :live-classes="live_classes" @go-to-classroom="goToClassroom" />
                    </div>
                    <div class="lg:col-span-4">
                        <LiveClassSessionList variant="sidebar" :sessions="live_classes" @go-to-classroom="goToClassroom" @open-live="openLiveSession" />
                    </div>
                </div>
            </div>
        </div>
        <BottomNav active="live-classes" />
    </AppLayout>
</template>
