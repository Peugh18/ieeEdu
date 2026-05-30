<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { Course } from '@/types/course';

const props = defineProps<{
    course: Course;
    isDashboard?: boolean;
}>();

function parseDateParts(dateStr?: string) {
    if (!dateStr) return { day: '--', month: 'Pronto' };
    try {
        const date = new Date(dateStr.replace(/-/g, '/'));
        if (isNaN(date.getTime())) {
            const parts = dateStr.split(' ');
            return { day: parts[0] || '--', month: (parts[2] || 'Pronto').substring(0, 3).toUpperCase() };
        }
        const day = date.getDate().toString();
        const month = date.toLocaleDateString('es-ES', { month: 'short' }).replace('.', '').toUpperCase();
        return { day, month };
    } catch (e) {
        return { day: '--', month: 'Pronto' };
    }
}

const dateParts = parseDateParts(props.course.start_date);
const categoryName = typeof props.course.category === 'object' ? props.course.category?.name : props.course.category;
</script>

<template>
    <article class="group bg-surface border border-outline-variant/15 p-5 rounded-3xl hover:shadow-xl hover:border-primary/20 transition-all duration-500 hover:-translate-y-1.5 flex flex-col h-full relative">
        <div class="relative h-[220px] w-full rounded-2xl overflow-hidden bg-surface-container-low shadow-sm border border-outline-variant/5 mb-5 flex-shrink-0">
            <img v-if="course.image" :src="course.image" :alt="course.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[1.5s] ease-out" />
            <div v-else class="w-full h-full bg-gradient-to-br from-primary/10 via-primary/5 to-surface-container-highest flex items-center justify-center relative group-hover:scale-105 transition-transform duration-[1.5s] ease-out">
                <svg class="absolute inset-0 size-full stroke-primary/[0.04]" fill="none">
                    <defs><pattern :id="`master-pattern-${course.id}`" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse"><path d="M-1 5L5 -1M3 9L8.5 3.5" stroke-width="0.5"></path></pattern></defs>
                    <rect stroke="none" :fill="`url(#master-pattern-${course.id})`" width="100%" height="100%"></rect>
                </svg>
                <span class="text-primary/20 font-serif text-3xl font-bold tracking-widest relative">IEE</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-black/5 to-transparent"></div>
            <div class="absolute top-4 left-4 z-20 flex flex-col items-center w-14 rounded-xl bg-surface border border-outline-variant/15 shadow-lg overflow-hidden transition-all group-hover:scale-105">
                <span class="w-full text-center py-1 text-[8px] font-black uppercase tracking-wider text-white bg-gradient-to-r from-[#D4AF37] to-[#AA7C11]">{{ dateParts.month }}</span>
                <span class="w-full text-center py-1.5 text-base font-serif font-black text-on-surface leading-none">{{ dateParts.day }}</span>
            </div>
            <div v-if="categoryName" class="absolute top-4 right-4 z-10">
                <span class="px-2.5 py-1 bg-black/30 backdrop-blur-md text-white text-[9px] font-bold tracking-widest uppercase border border-white/10 rounded-lg shadow-sm">{{ categoryName }}</span>
            </div>
            <div class="absolute bottom-4 left-4 z-10">
                <span class="px-3 py-1.5 rounded-lg bg-[#D32F2F] text-white text-[10px] font-bold tracking-widest uppercase flex items-center gap-1.5 shadow-lg border border-white/15"><span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>EN VIVO</span>
            </div>
        </div>
        <div class="flex-1 flex flex-col">
            <div class="flex items-center gap-3 text-[11px] font-bold tracking-wider uppercase text-primary mb-3">
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ course.start_date || 'Por definir' }}
                </div>
                <span class="text-outline-variant opacity-60">•</span>
                <div class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ course.start_time || 'Sin hora' }}
                </div>
            </div>
            <Link :href="route('cursos.show', { slug: course.slug, ...(isDashboard ? { dashboard: true } : {}) })" class="mb-2.5 block">
                <h3 class="font-serif font-bold text-xl text-on-surface leading-snug group-hover:text-primary transition-colors line-clamp-2 min-h-[3rem]">{{ course.title }}</h3>
            </Link>
            <p class="text-xs text-on-surface-variant/70 line-clamp-2 mb-6 flex-1 leading-relaxed">{{ course.description || 'Únete a esta sesión exclusiva y potencia tus conocimientos con los mejores expertos.' }}</p>
            <div class="pt-4 border-t border-outline-variant/10 flex flex-col gap-3 mt-auto">
                <a :href="course.whatsapp_link || '#'" target="_blank" class="group/btn w-full flex items-center justify-center gap-2 rounded-2xl text-[10px] uppercase tracking-wider font-bold px-5 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md hover:shadow-xl hover:-translate-y-0.5">
                    <svg class="w-3.5 h-3.5 fill-current group-hover/btn:scale-110 transition-transform" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.115.55 4.183 1.597 6l-1.6 5.8 5.922-1.556c1.782.973 3.791 1.488 5.85 1.488 6.645 0 12.03-5.385 12.03-12.03S18.676 0 12.031 0z"/></svg>
                    UNIRSE A LA SESIÓN
                </a>
                <Link :href="route('cursos.show', { slug: course.slug, ...(isDashboard ? { dashboard: true } : {}) })" class="w-full text-center text-[10px] font-bold text-on-surface-variant hover:text-primary transition-colors tracking-wider uppercase py-1 border border-outline-variant/10 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all">Ver detalles de la clase</Link>
            </div>
        </div>
    </article>
</template>

<style scoped>
.line-clamp-2 { overflow: hidden; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; line-clamp: 2; }
</style>
