<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    courses: { data: any[]; links: any[]; total: number };
    categories: any[];
    filters: { category?: string };
    isDashboard?: boolean;
    banner?: {
        heading?: string;
        subheading?: string;
        image_path?: string | null;
        button_text?: string;
        button_link?: string;
        show_text?: boolean;
    };
}>();

const selectedCategory = ref(props.filters.category || 'Todas');

function applyFilters(category: string) {
    selectedCategory.value = category;
    const routeName = props.isDashboard ? 'student.explore.masterclasses' : 'masterclass.index';
    router.get(
        route(routeName),
        { category: category === 'Todas' ? '' : category },
        { preserveState: true, preserveScroll: true, replace: true }
    );
}

// Helper to extract day and short month from the event date for the calendar sheet badge
function parseDateParts(dateStr?: string) {
    if (!dateStr) return { day: '--', month: 'Pronto' };
    try {
        const date = new Date(dateStr.replace(/-/g, '/'));
        if (isNaN(date.getTime())) {
            const parts = dateStr.split(' ');
            return { 
                day: parts[0] || '--', 
                month: (parts[2] || 'Pronto').substring(0, 3).toUpperCase() 
            };
        }
        const day = date.getDate().toString();
        const month = date.toLocaleDateString('es-ES', { month: 'short' }).replace('.', '').toUpperCase();
        return { day, month };
    } catch (e) {
        return { day: '--', month: 'Pronto' };
    }
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Masterclasses en Vivo', href: '#' },
];
</script>

<template>
    <Head title="Masterclasses - IEE" />
    
    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['flex min-h-screen flex-col font-sans', !isDashboard ? 'bg-background' : 'bg-transparent shadow-none']">
            <Navigation v-if="!isDashboard" />
            <main :class="['flex-1 pb-20', !isDashboard ? 'pt-28' : 'pt-0']">

                <!-- Hero Banner: Contained Premium Card -->
                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-10">
                    <div
                        v-if="banner?.image_path"
                        :class="['relative overflow-hidden shadow-2xl transition-all duration-500', isDashboard ? 'rounded-[1.5rem]' : 'rounded-[2rem]']"
                        style="aspect-ratio: 16 / 5;"
                    >
                        <img :src="banner.image_path" alt="Masterclasses IEE" class="absolute inset-0 w-full h-full object-cover object-center" />
                        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/55 to-black/20"></div>

                        <div v-if="banner.show_text" class="relative z-10 h-full px-8 md:px-14 flex flex-col justify-center">
                            <div class="max-w-2xl space-y-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/80 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#D4AF37] animate-pulse"></span>
                                    Campus Digital IEE
                                </span>
                                <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-white leading-[1.15] tracking-tight">
                                    {{ banner.heading || 'Domina la Economía con Clases en Vivo' }}
                                </h1>
                                <p class="text-xs sm:text-sm md:text-base text-white/75 max-w-lg font-light leading-relaxed">
                                    {{ banner.subheading || 'Sesiones interactivas con expertos del sector financiero y estratégico.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Fallback sin imagen -->
                    <div v-else
                        class="relative overflow-hidden rounded-[2rem] bg-surface-container-low py-20 px-10 md:px-14 flex flex-col justify-center"
                        style="aspect-ratio: 16 / 5;"
                    >
                        <div class="absolute -top-20 left-1/4 w-[500px] h-[500px] bg-primary/[0.06] rounded-full blur-[120px]"></div>
                        <div class="absolute -bottom-20 right-1/4 w-[400px] h-[400px] bg-tertiary-container/[0.08] rounded-full blur-[100px]"></div>
                        <div class="relative z-10 max-w-2xl space-y-4">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-primary">Campus Digital IEE</p>
                            <h1 class="text-3xl md:text-5xl font-serif font-bold leading-tight text-on-surface">
                                Masterclasses <span class="italic text-primary">en Vivo</span>
                            </h1>
                            <p class="text-lg text-on-surface-variant">
                                Sesiones interactivas con expertos. Tu camino hacia la excelencia.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-6">
                    <!-- Filters/Tabs -->
                    <div class="flex items-center justify-between mb-12 flex-wrap gap-6 border-b border-outline-variant/20 pb-6">
                        <div class="flex flex-wrap gap-2">
                            <button 
                                @click="applyFilters('Todas')"
                                class="px-5 py-2 rounded-full text-xs font-bold transition-all border"
                                :class="selectedCategory === 'Todas' 
                                    ? 'bg-primary border-primary text-on-primary shadow-md shadow-primary/10' 
                                    : 'bg-surface border-outline-variant/30 text-on-surface-variant hover:border-primary/40 hover:text-primary'"
                            >
                                Todas
                            </button>
                            <button 
                                v-for="cat in categories" 
                                :key="cat.id"
                                @click="applyFilters(cat.name)"
                                class="px-5 py-2 rounded-full text-xs font-bold transition-all border"
                                :class="selectedCategory === cat.name 
                                    ? 'bg-primary border-primary text-on-primary shadow-md shadow-primary/10' 
                                    : 'bg-surface border-outline-variant/30 text-on-surface-variant hover:border-primary/40 hover:text-primary'"
                            >
                                {{ cat.name }}
                            </button>
                        </div>
                        
                        <div class="text-xs text-on-surface-variant">
                            Mostrando <span class="font-bold text-on-surface">{{ courses.total || 0 }}</span> sesiones encontradas
                        </div>
                    </div>

                    <!-- Masterclasses Grid -->
                    <div v-if="courses.data.length > 0" class="mb-12">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                            <article 
                                v-for="course in courses.data" 
                                :key="course.id"
                                class="group bg-surface border border-outline-variant/15 p-5 rounded-3xl hover:shadow-xl hover:border-primary/20 transition-all duration-500 hover:-translate-y-1.5 flex flex-col h-full relative"
                            >
                                <!-- Framed Image Container -->
                                <div class="relative h-[220px] w-full rounded-2xl overflow-hidden bg-surface-container-low shadow-sm border border-outline-variant/5 mb-5 flex-shrink-0">
                                    <img 
                                        v-if="course.image" 
                                        :src="course.image" 
                                        :alt="course.title" 
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[1.5s] ease-out" 
                                    />
                                    <div v-else class="w-full h-full bg-gradient-to-br from-primary/10 via-primary/5 to-surface-container-highest flex items-center justify-center relative group-hover:scale-105 transition-transform duration-[1.5s] ease-out">
                                        <!-- Dynamic SVG fallback pattern -->
                                        <svg class="absolute inset-0 size-full stroke-primary/[0.04]" fill="none">
                                            <defs>
                                                <pattern :id="`master-pattern-${course.id}`" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                                                    <path d="M-1 5L5 -1M3 9L8.5 3.5" stroke-width="0.5"></path>
                                                </pattern>
                                            </defs>
                                            <rect stroke="none" :fill="`url(#master-pattern-${course.id})`" width="100%" height="100%"></rect>
                                        </svg>
                                        <span class="text-primary/20 font-serif text-3xl font-bold tracking-widest relative">IEE</span>
                                    </div>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/45 via-black/5 to-transparent"></div>
                                    
                                    <!-- Floating Calendar Badge Overlay (Top Left) -->
                                    <div class="absolute top-4 left-4 z-20 flex flex-col items-center w-14 rounded-xl bg-surface border border-outline-variant/15 shadow-lg overflow-hidden transition-all group-hover:scale-105">
                                        <!-- Calendar Month Header (gold/accent color) -->
                                        <span class="w-full text-center py-1 text-[8px] font-black uppercase tracking-wider text-white bg-gradient-to-r from-[#D4AF37] to-[#AA7C11]">
                                            {{ parseDateParts(course.start_date).month }}
                                        </span>
                                        <!-- Calendar Day Number -->
                                        <span class="w-full text-center py-1.5 text-base font-serif font-black text-on-surface leading-none">
                                            {{ parseDateParts(course.start_date).day }}
                                        </span>
                                    </div>

                                    <!-- Category Badge (Top Right) -->
                                    <div class="absolute top-4 right-4 z-10" v-if="course.category">
                                        <span class="px-2.5 py-1 bg-black/30 backdrop-blur-md text-white text-[9px] font-bold tracking-widest uppercase border border-white/10 rounded-lg shadow-sm">
                                            {{ typeof course.category === 'object' ? course.category.name : course.category }}
                                        </span>
                                    </div>

                                    <!-- Live Tag Badge (Bottom Left) -->
                                    <div class="absolute bottom-4 left-4 z-10">
                                        <span class="px-3 py-1.5 rounded-lg bg-[#D32F2F] text-white text-[10px] font-bold tracking-widest uppercase flex items-center gap-1.5 shadow-lg border border-white/15">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                            EN VIVO
                                        </span>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 flex flex-col">
                                    <!-- Date/Time Header strip -->
                                    <div class="flex items-center gap-3 text-[11px] font-bold tracking-wider uppercase text-primary mb-3">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ course.start_date || 'Por definir' }}
                                        </div>
                                        <span class="text-outline-variant opacity-60">•</span>
                                        <div class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            {{ course.start_time || 'Sin hora' }}
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <Link :href="route('cursos.show', { slug: course.slug, ...(isDashboard ? { dashboard: true } : {}) })" class="mb-2.5 block">
                                        <h3 class="font-serif font-bold text-xl text-on-surface leading-snug group-hover:text-primary transition-colors line-clamp-2 min-h-[3rem]">
                                            {{ course.title }}
                                        </h3>
                                    </Link>

                                    <!-- Description -->
                                    <p class="text-xs text-on-surface-variant/70 line-clamp-2 mb-6 flex-1 leading-relaxed">
                                        {{ course.description || 'Únete a esta sesión exclusiva y potencia tus conocimientos con los mejores expertos.' }}
                                    </p>

                                    <!-- Action Buttons -->
                                    <div class="pt-4 border-t border-outline-variant/10 flex flex-col gap-3 mt-auto">
                                        <a 
                                            :href="course.whatsapp_link || '#'" 
                                            target="_blank" 
                                            class="group/btn w-full flex items-center justify-center gap-2 rounded-2xl text-[10px] uppercase tracking-wider font-bold px-5 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white hover:from-emerald-600 hover:to-teal-700 transition-all shadow-md hover:shadow-xl hover:-translate-y-0.5"
                                        >
                                            <svg class="w-3.5 h-3.5 fill-current group-hover/btn:scale-110 transition-transform" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.115.55 4.183 1.597 6l-1.6 5.8 5.922-1.556c1.782.973 3.791 1.488 5.85 1.488 6.645 0 12.03-5.385 12.03-12.03S18.676 0 12.031 0zm0 21.905c-1.801 0-3.56-.484-5.111-1.405l-.367-.217-3.793.996 1.002-3.693-.238-.38c-.997-1.59-1.523-3.425-1.523-5.32 0-5.632 4.58-10.213 10.212-10.213 5.631 0 10.212 4.581 10.212 10.213 0 5.632-4.58 10.213-10.212 10.213zm5.602-7.662c-.307-.154-1.815-.898-2.097-1.002-.282-.104-.488-.154-.693.154-.206.308-.792 1.002-.971 1.206-.18.205-.36.23-.667.077-.308-.154-1.295-.478-2.467-1.525-.912-.815-1.528-1.821-1.708-2.129-.18-.308-.02-.475.134-.627.139-.138.307-.358.461-.537.154-.18.205-.308.308-.513.102-.205.051-.385-.026-.539-.077-.154-.693-1.673-.95-2.289-.25-.6-.505-.518-.694-.527-.18-.009-.385-.011-.59-.011-.205 0-.539.077-.821.385-.282.308-1.077 1.053-1.077 2.568 0 1.515 1.103 2.978 1.257 3.183.154.205 2.174 3.321 5.265 4.659.735.318 1.308.508 1.758.65.736.234 1.405.2 1.933.121.59-.089 1.815-.742 2.072-1.46.257-.719.257-1.335.18-1.461-.077-.126-.282-.203-.59-.357z"/></svg>
                                            UNIRSE A LA SESIÓN
                                        </a>
                                        <Link 
                                            :href="route('cursos.show', { slug: course.slug, ...(isDashboard ? { dashboard: true } : {}) })" 
                                            class="w-full text-center text-[10px] font-bold text-on-surface-variant hover:text-primary transition-colors tracking-wider uppercase py-1 border border-outline-variant/10 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all"
                                        >
                                            Ver detalles de la clase
                                        </Link>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- Pagination -->
                        <div v-if="courses.links && courses.links.length > 3" class="mt-12 flex justify-center">
                            <div class="flex flex-wrap gap-3">
                                <template v-for="(link, key) in courses.links" :key="key">
                                    <div v-if="link.url === null" class="px-4 py-2.5 rounded-xl text-xs text-outline-variant opacity-50 font-bold" v-html="link.label"></div>
                                    <Link v-else :href="link.url" class="px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all" :class="{ 'bg-primary text-white shadow-md shadow-primary/20': link.active, 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high border border-outline-variant/20': !link.active }" v-html="link.label" preserve-scroll />
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="py-24 text-center bg-surface-container-low rounded-[3rem] border border-dashed border-outline-variant/30 max-w-3xl mx-auto p-8 space-y-4">
                        <div class="w-16 h-16 bg-primary/5 rounded-full flex items-center justify-center mx-auto text-primary/40">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h10a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-on-surface">Próximamente nuevas sesiones</h3>
                        <p class="text-xs text-on-surface-variant max-w-sm mx-auto">No encontramos masterclasses en esta categoría por ahora. ¡Vuelve pronto!</p>
                    </div>
                </div>
            </main>
        </div>
    </component>
</template>

<style scoped>
</style>
