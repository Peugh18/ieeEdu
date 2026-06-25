<script setup lang="ts">
import type { Course } from '@/types/course';
import { Link } from '@inertiajs/vue3';

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
    <article
        class="group relative flex h-full flex-col rounded-3xl border border-outline-variant/15 bg-surface p-5 transition-all duration-500 hover:-translate-y-1.5 hover:border-primary/20 hover:shadow-xl"
    >
        <div
            class="relative mb-5 h-[220px] w-full flex-shrink-0 overflow-hidden rounded-2xl border border-outline-variant/5 bg-surface-container-low shadow-sm"
        >
            <img
                v-if="course.image"
                :src="course.image"
                :alt="course.title"
                class="duration-[1.5s] h-full w-full object-cover transition-transform ease-out group-hover:scale-105"
            />
            <div
                v-else
                class="duration-[1.5s] relative flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/10 via-primary/5 to-surface-container-highest transition-transform ease-out group-hover:scale-105"
            >
                <svg class="absolute inset-0 size-full stroke-primary/[0.04]" fill="none">
                    <defs>
                        <pattern :id="`master-pattern-${course.id}`" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                            <path d="M-1 5L5 -1M3 9L8.5 3.5" stroke-width="0.5"></path>
                        </pattern>
                    </defs>
                    <rect stroke="none" :fill="`url(#master-pattern-${course.id})`" width="100%" height="100%"></rect>
                </svg>
                <span class="relative font-serif text-3xl font-bold tracking-widest text-primary/20">IEE</span>
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-black/5 to-transparent"></div>
            <div
                class="absolute left-4 top-4 z-20 flex w-14 flex-col items-center overflow-hidden rounded-xl border border-outline-variant/15 bg-surface shadow-lg transition-all group-hover:scale-105"
            >
                <span
                    class="w-full bg-gradient-to-r from-[#D4AF37] to-[#AA7C11] py-1 text-center text-[8px] font-black uppercase tracking-wider text-white"
                    >{{ dateParts.month }}</span
                >
                <span class="w-full py-1.5 text-center font-serif text-base font-black leading-none text-on-surface">{{ dateParts.day }}</span>
            </div>
            <div v-if="categoryName" class="absolute right-4 top-4 z-10">
                <span
                    class="rounded-lg border border-white/10 bg-black/30 px-2.5 py-1 text-[9px] font-bold uppercase tracking-widest text-white shadow-sm backdrop-blur-md"
                    >{{ categoryName }}</span
                >
            </div>
            <div class="absolute bottom-4 left-4 z-10">
                <span
                    class="flex items-center gap-1.5 rounded-lg border border-white/15 bg-[#D32F2F] px-3 py-1.5 text-[10px] font-bold uppercase tracking-widest text-white shadow-lg"
                    ><span class="h-1.5 w-1.5 animate-pulse rounded-full bg-white"></span>EN VIVO</span
                >
            </div>
        </div>
        <div class="flex flex-1 flex-col">
            <div class="mb-3 flex items-center gap-3 text-[11px] font-bold uppercase tracking-wider text-primary">
                <div class="flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                    {{ course.start_date || 'Por definir' }}
                </div>
                <span class="text-outline-variant opacity-60">•</span>
                <div class="flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ course.start_time || 'Sin hora' }}
                </div>
            </div>
            <Link :href="route('cursos.show', { slug: course.slug, ...(isDashboard ? { dashboard: true } : {}) })" class="mb-2.5 block">
                <h3
                    class="line-clamp-2 min-h-[3rem] font-serif text-xl font-bold leading-snug text-on-surface transition-colors group-hover:text-primary"
                >
                    {{ course.title }}
                </h3>
            </Link>
            <p class="mb-6 line-clamp-2 flex-1 text-xs leading-relaxed text-on-surface-variant/70">
                {{ course.description || 'Únete a esta sesión exclusiva y potencia tus conocimientos con los mejores expertos.' }}
            </p>
            <div class="mt-auto flex flex-col gap-3 border-t border-outline-variant/10 pt-4">
                <a
                    :href="course.whatsapp_link || '#'"
                    target="_blank"
                    class="group/btn flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 px-5 py-3.5 text-[10px] font-bold uppercase tracking-wider text-white shadow-md transition-all hover:-translate-y-0.5 hover:from-emerald-600 hover:to-teal-700 hover:shadow-xl"
                >
                    <svg class="h-3.5 w-3.5 fill-current transition-transform group-hover/btn:scale-110" viewBox="0 0 24 24">
                        <path
                            d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.115.55 4.183 1.597 6l-1.6 5.8 5.922-1.556c1.782.973 3.791 1.488 5.85 1.488 6.645 0 12.03-5.385 12.03-12.03S18.676 0 12.031 0z"
                        />
                    </svg>
                    UNIRSE A LA SESIÓN
                </a>
                <Link
                    :href="route('cursos.show', { slug: course.slug, ...(isDashboard ? { dashboard: true } : {}) })"
                    class="w-full rounded-xl border border-outline-variant/10 bg-surface-container-low py-1 text-center text-[10px] font-bold uppercase tracking-wider text-on-surface-variant transition-all transition-colors hover:bg-surface-container hover:text-primary"
                    >Ver detalles de la clase</Link
                >
            </div>
        </div>
    </article>
</template>

<style scoped>
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    line-clamp: 2;
}
</style>
