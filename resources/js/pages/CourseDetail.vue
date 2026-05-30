<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useCart } from '@/composables/useCart';
import CoursePricingSidebar from '@/components/course-detail/CoursePricingSidebar.vue';
import CourseFeatures from '@/components/course-detail/CourseFeatures.vue';
import CourseCurriculum from '@/components/course-detail/CourseCurriculum.vue';
import type { Course, CourseModule, CourseLesson } from '@/types/course';

const { addItem } = useCart();

interface Props {
    course: Course & {
        modules?: CourseModule[];
        lessons?: CourseLesson[];
        objectives?: string;
        requirements?: string;
        instructor_title?: string;
        instructor_bio?: string;
        duration_weeks?: string;
    };
    isDashboard?: boolean;
    isEnrolled?: boolean;
    hasPendingPayment?: boolean;
    canEnrollFree?: boolean;
    hasPermanentAccess?: boolean;
    canPurchasePermanent?: boolean;
}

const props = defineProps<Props>();

function handleAddToCart(e: Event) {
    e.preventDefault();
    addItem(props.course);
}

function getModalityLabel(type: string) {
    if (type === 'en vivo') return 'En vivo / Sincrónico';
    if (type === 'evento') return 'Evento Especial';
    return 'Grabado / Asincrónico';
}

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Explorar', href: route('student.explore.courses') },
    { title: 'Detalle de Curso', href: '#' },
];
</script>

<template>
    <Head :title="course.title + ' - IEE'" />
    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['flex flex-col text-on-background font-sans', !isDashboard ? 'min-h-screen bg-background' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />
            <main :class="['flex-1 pb-20 pt-10', isDashboard ? 'p-0' : '']">
                <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="mb-10">
                        <Link :href="isDashboard ? route('student.explore.courses') : route('cursos.index')" class="inline-flex items-center gap-2 text-[13px] font-bold text-on-surface-variant tracking-[0.05em] uppercase hover:text-on-background transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            {{ isDashboard ? 'Volver al catálogo' : 'Volver a cursos' }}
                        </Link>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 relative items-start">
                        <div class="lg:col-span-8 space-y-12">
                            <div>
                                <span class="inline-flex rounded-sm bg-surface-container-highest text-primary px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.1em] mb-6">{{ getModalityLabel(course.type) }}</span>
                                <h1 class="text-4xl sm:text-5xl lg:text-[56px] font-serif font-bold text-on-background leading-[1.1] mb-8 tracking-[-0.02em]">{{ course.title }}</h1>
                                <div class="flex flex-wrap items-center gap-6 mb-8 pt-4 pb-8 bg-surface-container-highest p-6 rounded border-l-4 border-primary">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-full overflow-hidden bg-white flex items-center justify-center font-serif font-bold text-xl text-primary shadow-[0_10px_20px_rgba(26,28,25,0.04)] border border-outline-variant/40">
                                            <img v-if="course.instructor_image" :src="course.instructor_image" :alt="course.instructor_name || 'Instructor'" class="w-full h-full object-cover" />
                                            <template v-else>{{ (course.instructor_name || 'I').charAt(0) }}</template>
                                        </div>
                                        <div>
                                            <p class="font-bold text-sm text-on-background uppercase tracking-[0.05em]">{{ course.instructor_name || 'Instructor Invitado' }}</p>
                                            <p class="text-[13px] text-on-surface-variant truncate max-w-[200px]">Docente especialista IEE</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="pl-6 border-l-4 border-primary my-10 relative">
                                    <svg class="w-8 h-8 text-primary/20 absolute -left-4 -top-3" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                    <p class="whitespace-pre-line font-serif italic text-2xl text-on-background leading-relaxed relative z-10">{{ course.description || 'Este curso de desarrollo y especialización provee al estudiante de herramientas comprobadas para la toma de decisiones.' }}</p>
                                </div>
                            </div>
                            <div v-if="course.image" class="w-full rounded bg-surface-container-highest overflow-hidden aspect-video shadow-[0_20px_40px_rgba(26,28,25,0.06)]">
                                <img :src="course.image" :alt="course.title" class="w-full h-full object-cover" />
                            </div>
                            <CourseFeatures :type="course.type" />
                            <div class="mt-16 mb-16 bg-background rounded-3xl p-10 sm:p-14 border border-outline-variant/40 shadow-sm relative overflow-hidden group flex flex-col items-center text-center">
                                <div class="absolute inset-0 text-primary/[0.02] pointer-events-none flex items-center justify-center">
                                    <svg width="400" height="400" viewBox="0 0 24 24" fill="currentColor" class="group-hover:scale-110 transition-transform duration-1000"><path d="M12 14c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                </div>
                                <div class="relative z-10 w-32 h-32 md:w-44 md:h-44 flex-shrink-0 rounded-full overflow-hidden bg-white border-4 border-surface-container-highest shadow-lg mb-6 mx-auto">
                                    <img v-if="course.instructor_image" :src="course.instructor_image" :alt="course.instructor_name || 'Instructor'" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                    <div v-else class="w-full h-full bg-primary/10 flex items-center justify-center">
                                        <svg class="w-20 h-20 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    </div>
                                </div>
                                <div class="relative z-10 max-w-2xl mx-auto">
                                    <span class="text-[10px] sm:text-xs font-bold text-primary tracking-[0.15em] uppercase block mb-3">Conoce a tu Especialista</span>
                                    <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-on-background mb-4">{{ course.instructor_name || 'Instructor Invitado' }}</h2>
                                    <p class="text-[11px] sm:text-xs font-bold tracking-[0.2em] uppercase text-on-surface-variant border-t border-b border-outline-variant/40 py-3 mb-6 inline-block">{{ course.instructor_title || 'Profesional Asociado al Instituto IEE' }}</p>
                                    <p class="text-[16px] sm:text-[18px] text-on-surface-variant leading-relaxed font-serif max-w-2xl whitespace-pre-line text-center mx-auto">{{ course.instructor_bio || 'Nuestro equipo docente está conformado por profesionales activos en el sector público y privado, garantizando que el conocimiento impartido se base en casos reales y actualizados.' }}</p>
                                </div>
                            </div>
                            <div v-if="course.objectives" class="mb-16">
                                <h2 class="text-[32px] font-serif font-bold text-on-background mb-8">Objetivos del Curso</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div v-for="(obj, i) in course.objectives.split('\n').filter((o: string) => o.trim())" :key="'obj-'+i" class="flex items-start gap-4 p-6 bg-white rounded shadow-[0_20px_40px_rgba(26,28,25,0.03)] border border-outline-variant/20">
                                        <div class="min-w-10 h-10 rounded-full bg-background flex items-center justify-center text-primary"><span class="font-bold font-serif">{{ Number(i) + 1 }}</span></div>
                                        <p class="text-[14px] text-on-surface-variant leading-relaxed pt-1.5">{{ obj.trim() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-if="course.requirements" class="mb-16">
                                <h2 class="text-[32px] font-serif font-bold text-on-background mb-8">Requisitos Previos</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div v-for="(req, i) in course.requirements.split('\n').filter((r: string) => r.trim())" :key="'req-'+i" class="flex items-start gap-4 p-6 bg-white rounded shadow-[0_20px_40px_rgba(26,28,25,0.03)] border border-outline-variant/20">
                                        <div class="min-w-10 h-10 rounded-full bg-background flex items-center justify-center text-primary"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                                        <p class="text-[14px] text-on-surface-variant leading-relaxed pt-1.5">{{ req.trim() }}</p>
                                    </div>
                                </div>
                            </div>
                            <CourseCurriculum :type="course.type" :modules="course.modules" :lessons="course.lessons" :duration="course.duration_weeks" />
                        </div>
                        <div class="lg:col-span-4 lg:sticky lg:top-12 mt-8 lg:mt-0">
                            <CoursePricingSidebar
                                :course="course"
                                :is-enrolled="isEnrolled"
                                :is-dashboard="isDashboard"
                                :has-pending-payment="hasPendingPayment"
                                :can-enroll-free="canEnrollFree"
                                :can-purchase-permanent="canPurchasePermanent"
                                @add-to-cart="handleAddToCart"
                            />
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </component>
</template>
