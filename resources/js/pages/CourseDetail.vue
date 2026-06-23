<script setup lang="ts">
import CourseCurriculum from '@/components/course-detail/CourseCurriculum.vue';
import CourseFeatures from '@/components/course-detail/CourseFeatures.vue';
import CoursePricingSidebar from '@/components/course-detail/CoursePricingSidebar.vue';
import Navigation from '@/components/landing/Navigation.vue';
import { useCart } from '@/composables/useCart';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Course, CourseLesson, CourseModule } from '@/types/course';
import { Head, Link } from '@inertiajs/vue3';

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
        <div :class="['flex flex-col font-sans text-on-background', !isDashboard ? 'min-h-screen bg-background' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />
            <main :class="['flex-1 pb-20 pt-10', isDashboard ? 'p-0' : '']">
                <div class="mx-auto max-w-[1200px] px-4 sm:px-6 lg:px-8">
                    <div class="mb-10">
                        <Link
                            :href="isDashboard ? route('student.explore.courses') : route('cursos.index')"
                            class="inline-flex items-center gap-2 text-[13px] font-bold uppercase tracking-[0.05em] text-on-surface-variant transition-colors hover:text-on-background"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            {{ isDashboard ? 'Volver al catálogo' : 'Volver a cursos' }}
                        </Link>
                    </div>
                    <div class="relative grid grid-cols-1 items-start gap-10 lg:grid-cols-12 lg:gap-16">
                        <div class="space-y-12 lg:col-span-8">
                            <div>
                                <span
                                    class="mb-6 inline-flex rounded-sm bg-surface-container-highest px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.1em] text-primary"
                                    >{{ getModalityLabel(course.type) }}</span
                                >
                                <h1
                                    class="mb-8 font-serif text-4xl font-bold leading-[1.1] tracking-[-0.02em] text-on-background sm:text-5xl lg:text-[56px]"
                                >
                                    {{ course.title }}
                                </h1>
                                <div
                                    class="mb-8 flex flex-wrap items-center gap-6 rounded border-l-4 border-primary bg-surface-container-highest p-6 pb-8 pt-4"
                                >
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-full border border-outline-variant/40 bg-white font-serif text-xl font-bold text-primary shadow-[0_10px_20px_rgba(26,28,25,0.04)]"
                                        >
                                            <img
                                                v-if="course.instructor_image"
                                                :src="course.instructor_image"
                                                :alt="course.instructor_name || 'Instructor'"
                                                class="h-full w-full object-cover"
                                            />
                                            <template v-else>{{ (course.instructor_name || 'I').charAt(0) }}</template>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold uppercase tracking-[0.05em] text-on-background">
                                                {{ course.instructor_name || 'Instructor Invitado' }}
                                            </p>
                                            <p class="max-w-[200px] truncate text-[13px] text-on-surface-variant">Docente especialista IEE</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="relative my-10 border-l-4 border-primary pl-6">
                                    <svg class="absolute -left-4 -top-3 h-8 w-8 text-primary/20" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"
                                        />
                                    </svg>
                                    <p class="relative z-10 whitespace-pre-line font-serif text-2xl italic leading-relaxed text-on-background">
                                        {{
                                            course.description ||
                                            'Este curso de desarrollo y especialización provee al estudiante de herramientas comprobadas para la toma de decisiones.'
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div
                                v-if="course.image"
                                class="aspect-video w-full overflow-hidden rounded bg-surface-container-highest shadow-[0_20px_40px_rgba(26,28,25,0.06)]"
                            >
                                <img :src="course.image" :alt="course.title" class="h-full w-full object-cover" />
                            </div>
                            <CourseFeatures :type="course.type" />
                            <div
                                class="group relative mb-16 mt-16 flex flex-col items-center overflow-hidden rounded-3xl border border-outline-variant/40 bg-background p-10 text-center shadow-sm sm:p-14"
                            >
                                <div class="pointer-events-none absolute inset-0 flex items-center justify-center text-primary/[0.02]">
                                    <svg
                                        width="400"
                                        height="400"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="transition-transform duration-1000 group-hover:scale-110"
                                    >
                                        <path
                                            d="M12 14c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                                        />
                                    </svg>
                                </div>
                                <div
                                    class="relative z-10 mx-auto mb-6 h-32 w-32 flex-shrink-0 overflow-hidden rounded-full border-4 border-surface-container-highest bg-white shadow-lg md:h-44 md:w-44"
                                >
                                    <img
                                        v-if="course.instructor_image"
                                        :src="course.instructor_image"
                                        :alt="course.instructor_name || 'Instructor'"
                                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center bg-primary/10">
                                        <svg class="h-20 w-20 text-primary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                    </div>
                                </div>
                                <div class="relative z-10 mx-auto max-w-2xl">
                                    <span class="mb-3 block text-[10px] font-bold uppercase tracking-[0.15em] text-primary sm:text-xs"
                                        >Conoce a tu Especialista</span
                                    >
                                    <h2 class="mb-4 font-serif text-3xl font-bold text-on-background sm:text-4xl lg:text-5xl">
                                        {{ course.instructor_name || 'Instructor Invitado' }}
                                    </h2>
                                    <p
                                        class="mb-6 inline-block border-b border-t border-outline-variant/40 py-3 text-[11px] font-bold uppercase tracking-[0.2em] text-on-surface-variant sm:text-xs"
                                    >
                                        {{ course.instructor_title || 'Profesional Asociado al Instituto IEE' }}
                                    </p>
                                    <p
                                        class="mx-auto max-w-2xl whitespace-pre-line text-center font-serif text-[16px] leading-relaxed text-on-surface-variant sm:text-[18px]"
                                    >
                                        {{
                                            course.instructor_bio ||
                                            'Nuestro equipo docente está conformado por profesionales activos en el sector público y privado, garantizando que el conocimiento impartido se base en casos reales y actualizados.'
                                        }}
                                    </p>
                                </div>
                            </div>
                            <div v-if="course.objectives" class="mb-16">
                                <h2 class="mb-8 font-serif text-[32px] font-bold text-on-background">Objetivos del Curso</h2>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div
                                        v-for="(obj, i) in course.objectives.split('\n').filter((o: string) => o.trim())"
                                        :key="'obj-' + i"
                                        class="flex items-start gap-4 rounded border border-outline-variant/20 bg-white p-6 shadow-[0_20px_40px_rgba(26,28,25,0.03)]"
                                    >
                                        <div class="flex h-10 min-w-10 items-center justify-center rounded-full bg-background text-primary">
                                            <span class="font-serif font-bold">{{ Number(i) + 1 }}</span>
                                        </div>
                                        <p class="pt-1.5 text-[14px] leading-relaxed text-on-surface-variant">{{ obj.trim() }}</p>
                                    </div>
                                </div>
                            </div>
                            <div v-if="course.requirements" class="mb-16">
                                <h2 class="mb-8 font-serif text-[32px] font-bold text-on-background">Requisitos Previos</h2>
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div
                                        v-for="(req, i) in course.requirements.split('\n').filter((r: string) => r.trim())"
                                        :key="'req-' + i"
                                        class="flex items-start gap-4 rounded border border-outline-variant/20 bg-white p-6 shadow-[0_20px_40px_rgba(26,28,25,0.03)]"
                                    >
                                        <div class="flex h-10 min-w-10 items-center justify-center rounded-full bg-background text-primary">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <p class="pt-1.5 text-[14px] leading-relaxed text-on-surface-variant">{{ req.trim() }}</p>
                                    </div>
                                </div>
                            </div>
                            <CourseCurriculum
                                :type="course.type"
                                :modules="course.modules"
                                :lessons="course.lessons"
                                :duration="course.duration_weeks"
                            />
                        </div>
                        <div class="mt-8 lg:sticky lg:top-12 lg:col-span-4 lg:mt-0">
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
