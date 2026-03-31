<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';
import { ref } from 'vue';

const props = defineProps<{
    course: any;
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

</script>

<template>
    <Head :title="course.title + ' - IEE'" />

    <div class="flex min-h-screen flex-col bg-[#FAFAF5] text-[#1A1C19] font-sans">
        <Navigation />

        <main class="flex-1 pb-20 pt-10">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Breadcrumb / Back button -->
                <div class="mb-10">
                    <Link :href="route('cursos.index')" class="inline-flex items-center gap-2 text-[13px] font-bold text-[#5F5E5E] tracking-[0.05em] uppercase hover:text-[#1A1C19] transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> 
                        Volver a cursos
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
                                    <div class="w-14 h-14 rounded overflow-hidden bg-white flex items-center justify-center font-serif font-bold text-xl text-[#57572A] shadow-[0_10px_20px_rgba(26,28,25,0.04)]">
                                        {{ course.teacher?.name?.charAt(0) || 'I' }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-[#1A1C19] uppercase tracking-[0.05em]">{{ course.teacher?.name || 'Instructor Invitado' }}</p>
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

                        <!-- Features Grid (Static display of benefits) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                            <div class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)]">
                                <svg class="w-8 h-8 text-[#57572A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <h4 class="font-bold text-[#1A1C19] mb-2 text-sm uppercase tracking-[0.05em]">Acceso 24/7</h4>
                                <p class="text-[13px] text-[#5F5E5E] leading-relaxed">Apréndelo a tu propio ritmo, sin restricciones de horario.</p>
                            </div>
                            <div class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)]">
                                <svg class="w-8 h-8 text-[#57572A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                <h4 class="font-bold text-[#1A1C19] mb-2 text-sm uppercase tracking-[0.05em]">Certificación</h4>
                                <p class="text-[13px] text-[#5F5E5E] leading-relaxed">Obtén un certificado con validez y respaldo institucional.</p>
                            </div>
                            <div class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)]">
                                <svg class="w-8 h-8 text-[#57572A] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <h4 class="font-bold text-[#1A1C19] mb-2 text-sm uppercase tracking-[0.05em]">Material Incluido</h4>
                                <p class="text-[13px] text-[#5F5E5E] leading-relaxed">Recursos bibliográficos y herramientas prácticas evaluadas.</p>
                            </div>
                        </div>

                        <!-- Dynamic Section based on Course Type -->
                        <div class="pt-10 mb-8">
                            <h2 class="text-[32px] font-serif font-bold text-[#1A1C19] mb-10">{{ course.type === 'evento' ? 'Detalles del Evento' : (course.type === 'en vivo' ? 'Programa de Sesiones' : 'Estructura Curricular') }}</h2>
                            
                            <!-- 1. GRABADO (Modules -> Lessons) -->
                            <div v-if="course.type === 'grabado' && course.modules?.length" class="space-y-6">
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
                                            <span class="text-[#57572A] font-bold text-sm tracking-[0.1em] uppercase w-24 text-left">Módulo 0{{ index + 1 }}</span>
                                            <h3 class="font-bold text-[17px] text-[#1A1C19] text-left">{{ module.title }}</h3>
                                        </div>
                                        <svg class="w-5 h-5 text-[#5F5E5E] transition-transform" :class="{'rotate-180': openModules.includes(module.id)}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    
                                    <div v-show="openModules.includes(module.id)" class="bg-[#FAFAF5] p-2 border-t border-[#C9C7B8]/20">
                                        <ul v-if="module.lessons?.length" class="space-y-1">
                                            <li v-for="(lesson, lIdx) in module.lessons" :key="lesson.id" class="flex items-center gap-4 p-4 hover:bg-[#F4F4EF] rounded transition-colors text-[#1A1C19] text-[15px]">
                                                <svg class="w-5 h-5 text-[#57572A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span class="font-medium">Sesión {{ lIdx + 1 }}: {{ lesson.title }}</span>
                                            </li>
                                        </ul>
                                        <p v-else class="text-[13px] text-[#5F5E5E] p-4 italic font-serif">El material de este módulo estará disponible pronto en la biblioteca.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 2. EN VIVO (List of live lessons) -->
                            <div v-else-if="(course.type === 'en vivo' || course.type === 'evento') && course.lessons?.length" class="space-y-6">
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
                                <span v-if="originalPrice" class="text-[13px] text-[#C9C7B8] line-through font-bold tracking-widest">PEN {{ formatPrice(originalPrice) }}</span>
                                <div class="flex items-end gap-2 text-[#57572A]">
                                    <span class="text-xl font-bold leading-none mb-1">S/</span>
                                    <span class="text-5xl font-serif font-bold leading-none tracking-tight">{{ formatPrice(mainPrice) }}</span>
                                </div>
                            </div>

                            <!-- Payment Button / Inscribirse Flow Placeholder -->
                            <button class="w-full bg-gradient-to-br from-[#57572A] to-[#707040] text-white font-bold text-[12px] uppercase tracking-[0.05em] py-4 rounded shadow-md hover:shadow-lg transition-all mb-4 flex items-center justify-center gap-3 hover:-translate-y-0.5">
                                Completar Inscripción
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>

                            <p class="text-[11px] text-[#5F5E5E] mb-10 font-medium text-center italic tracking-wide">Transacción segura y acceso inmediato al campus institucional.</p>

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
</template>

<style scoped>
</style>
