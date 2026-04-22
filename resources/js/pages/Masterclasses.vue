<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
    courses: any[];
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

// Los cursos vienen listos del controlador
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

                <!-- Hero Banner: Tarjeta Contenida Premium -->
                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 mb-10">
                    <div
                        v-if="banner?.image_path"
                        :class="['relative overflow-hidden shadow-2xl', isDashboard ? 'rounded-[1.5rem]' : 'rounded-[2rem]']"
                        style="aspect-ratio: 16 / 5;"
                    >
                        <img :src="banner.image_path" alt="Masterclasses IEE" class="absolute inset-0 w-full h-full object-cover object-center" />
                        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/50 to-black/10"></div>

                        <div v-if="banner.show_text" class="relative z-10 h-full px-10 md:px-14 flex flex-col justify-center">
                            <div class="max-w-2xl space-y-4">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white/80 px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                                    Campus Digital IEE
                                </span>
                                <h1 class="text-3xl md:text-4xl lg:text-5xl font-serif font-bold text-white leading-[1.15] tracking-tight">
                                    {{ banner.heading || 'Domina la Economía con Clases en Vivo' }}
                                </h1>
                                <p class="text-base text-white/75 max-w-lg font-light leading-relaxed">
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

                <div class="max-w-7xl mx-auto px-6 md:px-8 relative z-10 pt-12">
                    <!-- Filters/Tabs -->
                    <div class="flex items-center justify-between mb-12 flex-wrap gap-6">
                        <div class="flex flex-wrap gap-2">
                            <button 
                                @click="applyFilters('Todas')"
                                class="px-6 py-2.5 rounded-full text-xs font-bold transition-all border"
                                :class="selectedCategory === 'Todas' 
                                    ? 'bg-primary border-primary text-on-primary shadow-lg shadow-primary/20' 
                                    : 'bg-surface border-outline-variant/30 text-on-surface-variant hover:border-primary/40 hover:text-primary'"
                            >
                                Todas
                            </button>
                            <button 
                                v-for="cat in categories" 
                                :key="cat.id"
                                @click="applyFilters(cat.name)"
                                class="px-6 py-2.5 rounded-full text-xs font-bold transition-all border"
                                :class="selectedCategory === cat.name 
                                    ? 'bg-primary border-primary text-on-primary shadow-lg shadow-primary/20' 
                                    : 'bg-surface border-outline-variant/30 text-on-surface-variant hover:border-primary/40 hover:text-primary'"
                            >
                                {{ cat.name }}
                            </button>
                        </div>
                        
                        <div class="text-sm text-on-surface-variant">
                            Mostrando <span class="font-bold text-on-surface">{{ courses.length }}</span> sesiones encontradas
                        </div>
                    </div>

                    <!-- Masterclasses Grid -->
                    <div v-if="courses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                        <article 
                            v-for="course in courses" 
                            :key="course.id"
                            class="group bg-surface-container rounded-[2rem] flex flex-col overflow-hidden border border-outline-variant/15 hover:border-primary/30 hover:shadow-[0_20px_40px_rgba(0,0,0,0.1)] transition-all duration-500"
                        >
                            <!-- Image Container -->
                            <div class="relative h-[220px] w-full overflow-hidden">
                                <img v-if="course.image" :src="course.image" :alt="course.title" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 ease-out" />
                                <div v-else class="w-full h-full bg-surface-container-highest flex items-center justify-center">
                                    <span class="text-on-surface-variant/20 font-serif text-3xl">IEE</span>
                                </div>
                                
                                <!-- Category Badge -->
                                <div class="absolute top-4 right-4 z-10" v-if="course.category">
                                    <span class="px-3 py-1 rounded-full bg-black/40 backdrop-blur-md text-white text-[9px] font-bold tracking-widest uppercase border border-white/20">
                                        {{ course.category.name }}
                                    </span>
                                </div>

                                <!-- Live Badge -->
                                <div class="absolute bottom-4 left-4 z-10">
                                    <span class="px-3 py-1.5 rounded-lg bg-[#D32F2F] text-white text-[10px] font-bold tracking-widest uppercase flex items-center gap-1.5 shadow-lg">
                                        <span class="w-1.5 h-2 rounded-full bg-white animate-pulse"></span>
                                        EN VIVO
                                    </span>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-8 flex flex-col flex-1">
                                <!-- Date/Time Strip -->
                                <div class="flex items-center gap-4 text-[12px] font-semibold text-primary mb-5">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ course.start_date || 'Por definir' }}
                                    </div>
                                    <div class="w-1 h-1 rounded-full bg-outline-variant"></div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ course.start_time || 'Sin hora' }}
                                    </div>
                                </div>

                                <Link :href="route('cursos.show', { slug: course.slug, ...(isDashboard ? { dashboard: true } : {}) })" class="mb-4 block">
                                    <h3 class="font-serif font-bold text-2xl text-on-background leading-tight group-hover:text-primary transition-colors line-clamp-2">
                                        {{ course.title }}
                                    </h3>
                                </Link>

                                <p class="text-sm text-on-surface-variant line-clamp-2 mb-8 flex-1 leading-relaxed">
                                    {{ course.description || 'Únete a esta sesión exclusiva y potencia tus conocimientos con los mejores expertos.' }}
                                </p>

                                <!-- Action Button: Unificado -->
                                <div class="flex flex-col gap-3">
                                    <a :href="course.whatsapp_link || '#'" target="_blank" class="group/btn w-full flex items-center justify-center gap-2.5 rounded-2xl text-[12px] uppercase tracking-widest font-bold px-6 py-4 bg-[#25D366] text-white hover:bg-[#20BE5A] transition-all shadow-md hover:shadow-xl hover:-translate-y-0.5">
                                        <svg class="w-4 h-4 fill-current group-hover/btn:scale-110 transition-transform" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.115.55 4.183 1.597 6l-1.6 5.8 5.922-1.556c1.782.973 3.791 1.488 5.85 1.488 6.645 0 12.03-5.385 12.03-12.03S18.676 0 12.031 0zm0 21.905c-1.801 0-3.56-.484-5.111-1.405l-.367-.217-3.793.996 1.002-3.693-.238-.38c-.997-1.59-1.523-3.425-1.523-5.32 0-5.632 4.58-10.213 10.212-10.213 5.631 0 10.212 4.581 10.212 10.213 0 5.632-4.58 10.213-10.212 10.213zm5.602-7.662c-.307-.154-1.815-.898-2.097-1.002-.282-.104-.488-.154-.693.154-.206.308-.792 1.002-.971 1.206-.18.205-.36.23-.667.077-.308-.154-1.295-.478-2.467-1.525-.912-.815-1.528-1.821-1.708-2.129-.18-.308-.02-.475.134-.627.139-.138.307-.358.461-.537.154-.18.205-.308.308-.513.102-.205.051-.385-.026-.539-.077-.154-.693-1.673-.95-2.289-.25-.6-.505-.518-.694-.527-.18-.009-.385-.011-.59-.011-.205 0-.539.077-.821.385-.282.308-1.077 1.053-1.077 2.568 0 1.515 1.103 2.978 1.257 3.183.154.205 2.174 3.321 5.265 4.659.735.318 1.308.508 1.758.65.736.234 1.405.2 1.933.121.59-.089 1.815-.742 2.072-1.46.257-.719.257-1.335.18-1.461-.077-.126-.282-.203-.59-.357z"/></svg>
                                        UNIRSE A LA SESIÓN
                                    </a>
                                    <Link :href="route('cursos.show', { slug: course.slug, ...(isDashboard ? { dashboard: true } : {}) })" class="w-full text-center text-[10px] font-bold text-on-surface-variant hover:text-primary transition-colors tracking-widest uppercase py-1">
                                        Ver detalles de la clase
                                    </Link>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="py-24 text-center bg-surface-container rounded-[3rem] border border-dashed border-outline-variant/30">
                        <div class="w-20 h-20 bg-surface-container-highest rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-outline-variant" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h10a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <h3 class="font-serif text-2xl text-on-surface mb-2">Próximamente nuevas sesiones</h3>
                        <p class="text-on-surface-variant max-w-sm mx-auto">No encontramos masterclasses en esta categoría por ahora. ¡Vuelve pronto!</p>
                    </div>
                </div>
            </main>
        </div>
    </component>
</template>
