<script setup lang="ts">
import BottomNav from '@/components/student/BottomNav.vue';
import LiveClassCalendar from '@/components/student/live-classes/LiveClassCalendar.vue';
import LiveClassSessionList from '@/components/student/live-classes/LiveClassSessionList.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';

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
    if (!link) {
        alert('Enlace de sesión no disponible todavía.');
        return;
    }
    window.open(link, '_blank', 'noopener,noreferrer');
}

function goToClassroom(session: LiveClass) {
    if (!session.course_slug) {
        alert('Error: El curso no tiene un identificador válido.');
        return;
    }
    router.visit(`/student/classroom/${session.course_slug}${session.id ? '/' + session.id : ''}`);
}
</script>

<template>
    <Head title="Clases en Vivo - IEE" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex min-h-[calc(100svh-4rem)] flex-col items-center bg-background text-on-background">
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-5 md:gap-10 md:p-12">
                <header class="flex flex-col justify-between gap-4 md:flex-row md:items-end md:gap-10">
                    <div class="space-y-2 md:space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-primary/10 bg-primary/5 px-4 py-1.5">
                            <div class="h-1.5 w-1.5 animate-pulse rounded-full bg-[#D4AF37]"></div>
                            <span class="text-[9px] font-black uppercase tracking-[0.25em] text-primary">Agenda Académica Digital</span>
                        </div>
                        <h1 class="font-serif text-2xl font-bold italic tracking-tight text-on-background md:text-4xl lg:text-5xl">Clases en Vivo</h1>
                        <p class="hidden max-w-2xl font-serif text-lg italic leading-relaxed text-on-surface-variant md:block">
                            Sincronice su cronograma con las sesiones magistrales en tiempo real para una formación integral.
                        </p>
                    </div>
                </header>

                <LiveClassSessionList variant="mobile" :sessions="live_classes" @go-to-classroom="goToClassroom" @open-live="openLiveSession" />

                <div class="hidden min-h-[600px] gap-12 pb-10 lg:grid lg:grid-cols-12">
                    <div class="lg:col-span-8">
                        <LiveClassCalendar :live-classes="live_classes" @go-to-classroom="goToClassroom" />
                    </div>
                    <div class="lg:col-span-4">
                        <LiveClassSessionList
                            variant="sidebar"
                            :sessions="live_classes"
                            @go-to-classroom="goToClassroom"
                            @open-live="openLiveSession"
                        />
                    </div>
                </div>
            </div>
        </div>
        <BottomNav active="live-classes" />
    </AppLayout>
</template>
