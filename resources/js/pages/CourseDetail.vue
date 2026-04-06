<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps<{
    course: any;
    isDashboard?: boolean;
    isEnrolled?: boolean;
}>();

const formatPrice = (price: number) => {
    return Number(price).toFixed(2);
};

// Accordion UI state
const openModules = ref<number[]>([props.course.modules?.[0]?.id]);

const toggleModule = (id: number) => {
    if (openModules.value.includes(id)) {
        openModules.value = openModules.value.filter(m => m !== id);
    } else {
        openModules.value.push(id);
    }
};

const getModalityLabel = (type: string) => {
    if (type === 'en vivo') return 'En vivo / Sincrónico';
    if (type === 'evento') return 'Evento Especial';
    return 'Grabado / Asincrónico';
};

const mainPrice = props.course.sale_price > 0 ? props.course.sale_price : props.course.price;
const originalPrice = props.course.sale_price > 0 ? props.course.price : null;

const breadcrumbs = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: 'Explorar', href: route('student.explore.courses') },
    { title: 'Detalle de Curso', href: '#' },
];

</script>

<template>
    <Head :title="course.title + ' - IEE'" />

    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['flex flex-col text-[#1A1C19] font-sans', !isDashboard ? 'min-h-screen bg-[#FAFAF5]' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />

            <main :class="['flex-1 pb-20 pt-10', isDashboard ? 'p-0' : '']">
                <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                    
                    <!-- Breadcrumb / Back button -->
                    <div class="mb-10">
                        <Link :href="isDashboard ? route('student.explore.courses') : route('cursos.index')" class="inline-flex items-center gap-2 text-[13px] font-bold text-[#5F5E5E] tracking-[0.05em] uppercase hover:text-[#1A1C19] transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> 
                            {{ isDashboard ? 'Volver al catálogo' : 'Volver a cursos' }}
                        </Link>
                    </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 relative items-start">
                    
                    <!-- LEFT COLUMN: Content -->
                    <div class="lg:col-span-8 space-y-12">
                        
                        <!-- Header Section -->
                        <div>
                            <!-- Pill: rounded-sm instead of full -->
                            <span class="inline-flex rounded-sm bg-[#F4F4EF] text-[#57572A] px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.1em] mb-6">
                                {{ getModalityLabel(course.type) }}
                            </span>
                            
                            <!-- Headline: Noto Serif -->
                            <h1 class="text-4xl sm:text-5xl lg:text-[56px] font-serif font-bold text-[#1A1C19] leading-[1.1] mb-8 tracking-[-0.02em]">
                                {{ course.title }}
                            </h1>

                            <!-- Instructor & Ratings row: Layered on F4F4EF -->
                            <div class="flex flex-wrap items-center gap-6 mb-8 pt-4 pb-8 bg-[#F4F4EF] p-6 rounded border-l-4 border-[#57572A]">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 rounded-full overflow-hidden bg-white flex items-center justify-center font-serif font-bold text-xl text-[#57572A] shadow-[0_10px_20px_rgba(26,28,25,0.04)] border border-[#C9C7B8]/40">
                                        <img v-if="course.instructor_image" :src="course.instructor_image" :alt="course.instructor_name || course.teacher?.name" class="w-full h-full object-cover" />
                                        <template v-else>
                                            {{ (course.instructor_name || course.teacher?.name || 'I').charAt(0) }}
                                        </template>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-[#1A1C19] uppercase tracking-[0.05em]">{{ course.instructor_name || course.teacher?.name || 'Instructor Invitado' }}</p>
                                        <p class="text-[13px] text-[#5F5E5E] truncate max-w-[200px]">Docente especialista IEE</p>
                                    </div>
                                </div>
                                <div class="hidden sm:block w-[1px] h-8 bg-[#C9C7B8]"></div>
                                <div class="flex items-center gap-3">
                                    <div class="flex text-[#57572A] space-x-1">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        <span class="font-bold text-base text-[#1A1C19] ml-1">4.8</span>
                                    </div>
                                    <span class="text-[13px] text-[#5F5E5E]">(120 valoraciones)</span>
                                </div>
                            </div>
                            
                            <!-- Insight Pull-Quote style description -->
                            <div class="pl-6 border-l-4 border-[#57572A] my-10 relative">
                                <svg class="w-8 h-8 text-[#57572A]/20 absolute -left-4 -top-3" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                                <p class="whitespace-pre-line font-serif italic text-2xl text-[#1A1C19] leading-relaxed relative z-10">{{ course.description || 'Este curso de desarrollo y especialización provee al estudiante de herramientas comprobadas para la toma de decisiones.' }}</p>
                            </div>
                        </div>

                        <!-- Image Banner -->
                        <div v-if="course.image" class="w-full rounded bg-[#F4F4EF] overflow-hidden aspect-video shadow-[0_20px_40px_rgba(26,28,25,0.06)]">
                            <img :src="course.image" :alt="course.title" class="w-full h-full object-cover" />
                        </div>

                        <!-- Dynamic Features Grid based on Course Type -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                            <!-- Grabado -->
                            <template v-if="course.type === 'grabado'">
                                <div class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)] text-center flex flex-col items-center">
                                    <svg class="w-8 h-8 text-[#57572A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <h4 class="font-bold text-[#1A1C19] mb-2 text-sm uppercase tracking-[0.05em]">Acceso Ilimitado</h4>
                                    <p class="text-[13px] text-[#5F5E5E] leading-relaxed">Estudia a tu propio ritmo, 24/7 sin restricciones.</p>
                                </div>
                                <div class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)] text-center flex flex-col items-center">
                                    <svg class="w-8 h-8 text-[#57572A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <h4 class="font-bold text-[#1A1C19] mb-2 text-sm uppercase tracking-[0.05em]">Material Descargable</h4>
                                    <p class="text-[13px] text-[#5F5E5E] leading-relaxed">Recursos, plantillas y lecturas especializadas.</p>
                                </div>
                                <div class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)] text-center flex flex-col items-center">
                                    <svg class="w-8 h-8 text-[#57572A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    <h4 class="font-bold text-[#1A1C19] mb-2 text-sm uppercase tracking-[0.05em]">Certificado Digital</h4>
                                    <p class="text-[13px] text-[#5F5E5E] leading-relaxed">Acreditación válida expedida por el IEE.</p>
                                </div>
                            </template>

                            <!-- En Vivo -->
                            <template v-else-if="course.type === 'en vivo'">
                                <div class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)] text-center flex flex-col items-center">
                                    <svg class="w-8 h-8 text-[#57572A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <h4 class="font-bold text-[#1A1C19] mb-2 text-sm uppercase tracking-[0.05em]">Clases en Vivo</h4>
                                    <p class="text-[13px] text-[#5F5E5E] leading-relaxed">Interacción directa con el docente especialista.</p>
                                </div>
                                <div class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)] text-center flex flex-col items-center">
                                    <svg class="w-8 h-8 text-[#57572A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                                    <h4 class="font-bold text-[#1A1C19] mb-2 text-sm uppercase tracking-[0.05em]">Asesoría Docente</h4>
                                    <p class="text-[13px] text-[#5F5E5E] leading-relaxed">Resolución de casos y consultas en tiempo real.</p>
                                </div>
                                <div class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)] text-center flex flex-col items-center">
                                    <svg class="w-8 h-8 text-[#57572A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <h4 class="font-bold text-[#1A1C19] mb-2 text-sm uppercase tracking-[0.05em]">Comunidad Activa</h4>
                                    <p class="text-[13px] text-[#5F5E5E] leading-relaxed">Networking exclusivo con otros profesionales.</p>
                                </div>
                            </template>

                            <!-- Masterclass / Evento -->
                            <template v-else>
                                <div class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)] text-center flex flex-col items-center">
                                    <svg class="w-8 h-8 text-[#57572A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                    <h4 class="font-bold text-[#1A1C19] mb-2 text-sm uppercase tracking-[0.05em]">Sesión Magistral</h4>
                                    <p class="text-[13px] text-[#5F5E5E] leading-relaxed">Análisis de alto nivel en un encuentro único.</p>
                                </div>
                                <div class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)] text-center flex flex-col items-center">
                                    <svg class="w-8 h-8 text-[#57572A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    <h4 class="font-bold text-[#1A1C19] mb-2 text-sm uppercase tracking-[0.05em]">Acceso Directo</h4>
                                    <p class="text-[13px] text-[#5F5E5E] leading-relaxed">Preguntas y respuestas con el experto.</p>
                                </div>
                                <div class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)] text-center flex flex-col items-center">
                                    <svg class="w-8 h-8 text-[#57572A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    <h4 class="font-bold text-[#1A1C19] mb-2 text-sm uppercase tracking-[0.05em]">Grupo WhatsApp</h4>
                                    <p class="text-[13px] text-[#5F5E5E] leading-relaxed">Avisos y recordatorios directamente en tu móvil.</p>
                                </div>
                            </template>
                        </div>                        <!-- Instructor Profile Section (Centered & Aesthetic) -->
                        <div class="mt-16 mb-16 bg-[#FAFAF5] rounded-3xl p-10 sm:p-14 border border-[#C9C7B8]/40 shadow-sm relative overflow-hidden group flex flex-col items-center text-center">
                            <!-- Decor -->
                            <div class="absolute inset-0 text-[#57572A]/[0.02] pointer-events-none flex items-center justify-center">
                                <svg width="400" height="400" viewBox="0 0 24 24" fill="currentColor" class="group-hover:scale-110 transition-transform duration-1000">
                                    <path d="M12 14c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>

                            <div class="relative z-10 w-32 h-32 md:w-44 md:h-44 flex-shrink-0 rounded-full overflow-hidden bg-white border-4 border-[#F4F4EF] shadow-lg mb-6 mx-auto">
                                <img v-if="course.instructor_image" :src="course.instructor_image" :alt="course.instructor_name || course.teacher?.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                                <div v-else class="w-full h-full bg-[#57572A]/10 flex items-center justify-center">
                                    <svg class="w-20 h-20 text-[#57572A]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            
                            <div class="relative z-10 max-w-2xl mx-auto">
                                <span class="text-[10px] sm:text-xs font-bold text-[#57572A] tracking-[0.15em] uppercase block mb-3">Conoce a tu Especialista</span>
                                <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-[#1A1C19] mb-4">{{ course.instructor_name || course.teacher?.name || 'Instructor Invitado' }}</h2>
                                
                                <p v-if="course.instructor_title" class="text-[11px] sm:text-xs font-bold tracking-[0.2em] uppercase text-[#5F5E5E] border-t border-b border-[#C9C7B8]/40 py-3 mb-6 inline-block">
                                    {{ course.instructor_title }}
                                </p>
                                <p v-else class="text-[11px] sm:text-xs font-bold tracking-[0.2em] uppercase text-[#5F5E5E] border-t border-b border-[#C9C7B8]/40 py-3 mb-6 inline-block">
                                    Profesional Asociado al Instituto IEE
                                </p>
                                
                                <p v-if="course.instructor_bio" class="text-[16px] sm:text-[18px] text-[#5F5E5E] leading-relaxed font-serif max-w-2xl whitespace-pre-line text-center mx-auto">
                                    {{ course.instructor_bio }}
                                </p>
                                <p v-else class="text-[16px] sm:text-[18px] text-[#5F5E5E] leading-relaxed font-serif italic max-w-2xl text-center mx-auto">
                                    "Nuestro equipo docente está conformado por profesionales activos en el sector público y privado, garantizando que el conocimiento impartido se base en casos reales y actualizados."
                                </p>
                            </div>
                        </div>

                        <!-- Course Objectives Section -->
                        <div v-if="course.objectives" class="mb-16">
                            <h2 class="text-[32px] font-serif font-bold text-[#1A1C19] mb-8">Objetivos del Curso</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div v-for="(obj, i) in (course.objectives || '').split('\n').filter(o => o.trim())" :key="'obj-'+i" class="flex items-start gap-4 p-6 bg-white rounded shadow-[0_20px_40px_rgba(26,28,25,0.03)] border border-[#C9C7B8]/20">
                                    <div class="min-w-10 h-10 rounded-full bg-[#FAFAF5] flex items-center justify-center text-[#57572A]">
                                        <span class="font-bold font-serif">{{ Number(i) + 1 }}</span>
                                    </div>
                                    <div>
                                        <p class="text-[14px] text-[#5F5E5E] leading-relaxed pt-1.5">{{ obj.trim() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Course Requirements Section -->
                        <div v-if="course.requirements" class="mb-16">
                            <h2 class="text-[32px] font-serif font-bold text-[#1A1C19] mb-8">Requisitos Previos</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div v-for="(req, i) in (course.requirements || '').split('\n').filter(r => r.trim())" :key="'req-'+i" class="flex items-start gap-4 p-6 bg-white rounded shadow-[0_20px_40px_rgba(26,28,25,0.03)] border border-[#C9C7B8]/20">
                                    <div class="min-w-10 h-10 rounded-full bg-[#FAFAF5] flex items-center justify-center text-[#57572A]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-[14px] text-[#5F5E5E] leading-relaxed pt-1.5">{{ req.trim() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Section based on Course Type -->
                        <div class="pt-10 mb-8">
                            <h2 class="text-[32px] font-serif font-bold text-[#1A1C19] mb-10">{{ course.type === 'evento' ? 'Detalles del Evento' : (course.type === 'en vivo' ? 'Programa de Sesiones' : 'Estructura Curricular') }}</h2>
                            
                            <!-- 1. GRABADO (Modules -> Lessons) -->
                            <div v-if="(course.type === 'grabado' || course.type === 'en vivo') && course.modules?.length" class="space-y-6">
                                <div class="flex items-center gap-4 text-xs font-bold text-[#5F5E5E] tracking-[0.1em] uppercase mb-6 bg-[#F4F4EF] p-4 rounded">
                                    <span>Duración de Estudio: {{ course.duration_weeks || 'A tu ritmo' }}</span>
                                    <span class="text-[#C9C7B8]">|</span>
                                    <span>Total Módulos: {{ course.modules.length }}</span>
                                </div>
                                
                                <div v-for="(module, index) in course.modules" :key="module.id" class="bg-white rounded shadow-[0_20px_40px_rgba(26,28,25,0.03)] overflow-hidden">
                                    <button 
                                        @click="toggleModule(module.id)" 
                                        class="w-full flex items-center justify-between p-6 bg-transparent hover:bg-[#FAFAF5] transition-colors"
                                    >
                                        <div class="flex items-center gap-6">
                                            <span class="text-[#57572A] font-bold text-sm tracking-[0.1em] uppercase w-24 text-left">Módulo 0{{ Number(index) + 1 }}</span>
                                            <h3 class="font-bold text-[17px] text-[#1A1C19] text-left">{{ module.title }}</h3>
                                        </div>
                                        <svg class="w-5 h-5 text-[#5F5E5E] transition-transform" :class="{'rotate-180': openModules.includes(module.id)}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    
                                    <div v-show="openModules.includes(module.id)" class="bg-[#FAFAF5] p-2 border-t border-[#C9C7B8]/20">
                                        <ul v-if="module.lessons?.length" class="space-y-1">
                                            <li v-for="(lesson, lIdx) in module.lessons" :key="lesson.id" class="flex items-center gap-4 p-4 hover:bg-[#F4F4EF] rounded transition-colors text-[#1A1C19] text-[15px]">
                                                <svg class="w-5 h-5 text-[#57572A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span class="font-medium">Sesión {{ Number(lIdx) + 1 }}: {{ lesson.title }}</span>
                                            </li>
                                        </ul>
                                        <p v-else class="text-[13px] text-[#5F5E5E] p-4 italic font-serif">El material de este módulo estará disponible pronto en la biblioteca.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 2. MASTERCLASS / EVENTO (List of live lessons) -->
                            <div v-else-if="course.type === 'evento' && course.lessons?.length" class="space-y-6">
                                <div v-for="lesson in course.lessons" :key="lesson.id" class="bg-white rounded p-8 shadow-[0_20px_40px_rgba(26,28,25,0.04)] flex flex-col sm:flex-row gap-6 sm:items-center">
                                    <div class="flex-1">
                                        <span class="text-[10px] font-bold text-[#57572A] tracking-[0.1em] uppercase block mb-2">Evento Confirmado</span>
                                        <h3 class="font-serif font-bold text-2xl text-[#1A1C19] mb-2">{{ lesson.title }}</h3>
                                        <p class="text-sm text-[#5F5E5E] mb-6">{{ lesson.description || 'Participación sujeta al calendario oficial de sesiones.' }}</p>
                                        <div class="flex items-center gap-3 text-sm font-bold text-[#1A1C19] bg-[#FAFAF5] py-2 px-3 rounded inline-flex border border-[#C9C7B8]/30">
                                            <svg class="w-4 h-4 text-[#57572A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ lesson.start_time || 'Fecha por anunciar' }}
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 mt-4 sm:mt-0">
                                        <button class="px-5 py-3 rounded border border-[#C9C7B8] text-[11px] font-bold tracking-[0.05em] uppercase text-[#1A1C19] hover:bg-[#F4F4EF] transition-colors">
                                            Agendar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="font-serif italic text-[#5F5E5E] py-16 text-center bg-white rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)] text-lg">
                                La estructura académica se encuentra en proceso de actualización formal.
                            </div>
                        </div>


                    </div>

                    <!-- RIGHT COLUMN: Sticky Pricing / Add to Cart Card -->
                    <div class="lg:col-span-4 lg:sticky lg:top-12 mt-8 lg:mt-0">
                        <div class="bg-white rounded p-8 shadow-[0_20px_40px_rgba(26,28,25,0.06)] backdrop-blur-3xl relative overflow-hidden">
                            <!-- Background Accent -->
                            <div class="absolute top-0 right-0 w-32 h-32 bg-[#F4F4EF] rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

                            <h3 class="text-[11px] font-bold text-[#5F5E5E] tracking-[0.1em] uppercase mb-4">Inscripción Oficial</h3>
                            
                            <div class="mb-8 flex flex-col items-start gap-1">
                                <span v-if="originalPrice && mainPrice > 0" class="text-[13px] text-[#C9C7B8] line-through font-bold tracking-widest">PEN {{ formatPrice(originalPrice) }}</span>
                                <div v-if="mainPrice > 0" class="flex items-end gap-2 text-[#57572A]">
                                    <span class="text-xl font-bold leading-none mb-1">S/</span>
                                    <span class="text-5xl font-serif font-bold leading-none tracking-tight">{{ formatPrice(mainPrice) }}</span>
                                </div>
                                <div v-else class="flex items-end gap-2 text-[#57572A]">
                                    <span class="text-5xl font-serif font-bold leading-none tracking-tight uppercase">GRATIS</span>
                                </div>
                            </div>

                            <!-- Payment Button / Inscribirse Flow Placeholder -->
                            <div v-if="isEnrolled">
                                <Link :href="route('student.classroom', { course: course.slug })" class="w-full bg-[#57572A] text-white font-bold text-[12px] uppercase tracking-[0.05em] py-4 rounded shadow-md hover:shadow-lg transition-all mb-4 flex items-center justify-center gap-3 hover:-translate-y-0.5">
                                    IR AL AULA VIRTUAL
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </Link>
                            </div>
                            <a v-else-if="course.type === 'evento'" :href="course.lessons?.[0]?.live_link || '#'" target="_blank" class="w-full bg-gradient-to-br from-[#25D366] to-[#128C7E] text-white font-bold text-[12px] uppercase tracking-[0.05em] py-4 rounded shadow-md hover:shadow-lg transition-all mb-4 flex items-center justify-center gap-3 hover:-translate-y-0.5">
                                UNIRSE POR WHATSAPP
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a5.225 5.225 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.015-1.04 2.476 0 1.46 1.065 2.871 1.213 3.07.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                            <a v-else :href="course.whatsapp_link || 'https://wa.me/51934057867'" target="_blank" class="w-full bg-gradient-to-br from-[#57572A] to-[#707040] text-white font-bold text-[12px] uppercase tracking-[0.05em] py-4 rounded shadow-md hover:shadow-lg transition-all mb-4 flex items-center justify-center gap-3 hover:-translate-y-0.5">
                                Completar Inscripción
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>

                            <p v-if="course.type === 'evento'" class="text-[11px] text-[#5F5E5E] mb-10 font-medium text-center tracking-wide">Al unirte al grupo tendrás todos los detalles e información para conectarte.</p>
                            <p v-else class="text-[11px] text-[#5F5E5E] mb-10 font-medium text-center italic tracking-wide">Transacción segura y acceso inmediato al campus institucional.</p>

                            <!-- Extra selling points -->
                            <div class="space-y-4 pt-6 border-t border-[#F4F4EF]">
                                <h4 class="font-bold text-[11px] uppercase tracking-[0.1em] text-[#1A1C19] mb-4">Garantía del Instituto</h4>
                                <ul class="text-[13px] text-[#5F5E5E] space-y-3 font-sans leading-relaxed">
                                    <li class="flex items-start gap-3">
                                        <div class="min-w-1.5 h-1.5 rounded-full bg-[#57572A] mt-1.5"></div>
                                        Desarrollo curricular respaldado por casos aplicados.
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <div class="min-w-1.5 h-1.5 rounded-full bg-[#57572A] mt-1.5"></div>
                                        Reconocimiento académico de trayectoria.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        </div>
    </component>
</template>

<style scoped>
</style>
