<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import CourseCard from '@/components/CourseCard.vue';
import CatalogHero from '@/components/catalog/CatalogHero.vue';
import CatalogDesktopFilters from '@/components/catalog/CatalogDesktopFilters.vue';
import CatalogMobileFilters from '@/components/catalog/CatalogMobileFilters.vue';
import CatalogPagination from '@/components/catalog/CatalogPagination.vue';
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import type { CourseCategory } from '@/types/course';
import type { PaginatedResponse } from '@/types/pagination';

interface CourseItem {
    id: number;
    slug: string;
    title: string;
    image: string | null;
    category: { name: string } | null;
    type: string;
    price: number;
    sale_price?: number;
}

const props = defineProps<{
    courses: PaginatedResponse<CourseItem>;
    categories: CourseCategory[];
    filters: { search?: string; modality?: string; category?: string };
    isDashboard?: boolean;
    hasActiveSubscription?: boolean;
    banner?: {
        image_path?: string | null;
        heading?: string;
        subheading?: string;
        button_text?: string;
        button_link?: string;
        show_text?: boolean;
    };
}>();

const filterForm = useForm({
    search: props.filters.search || '',
    modality: props.filters.modality || 'Todos',
    category: props.filters.category || 'Todas las áreas',
});

const showFilters = ref(false);
const modalities = ['Todos', 'En vivo', 'Grabado'];

function applyFilters() {
    const routeName = props.isDashboard ? 'student.explore.courses' : 'cursos.index';
    router.get(route(routeName), {
        search: filterForm.search || undefined,
        modality: filterForm.modality !== 'Todos' ? filterForm.modality : undefined,
        category: filterForm.category !== 'Todas las áreas' ? filterForm.category : undefined,
    }, { preserveState: false, preserveScroll: true, replace: true });
}

useDebouncedInertiaFilters(filterForm, applyFilters);

function applyModalFilters(payload: { search: string; modality: string; category: string }) {
    filterForm.search = payload.search;
    filterForm.modality = payload.modality;
    filterForm.category = payload.category;
    showFilters.value = false;
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Explorar Catálogo', href: '#' },
];

const paginationLinks = usePaginationLinks(props.courses.links);
</script>

<template>
    <Head title="Cursos Profesionales - IEE" />
    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['flex flex-col text-on-background font-sans', !isDashboard ? 'min-h-screen bg-background' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />
            <main :class="['flex-1 pb-20', !isDashboard ? 'pt-28 md:pt-28' : 'pt-0']">
                <CatalogHero :banner="banner" :is-dashboard="isDashboard" badge-text="Estrategia y Excelencia" default-heading="Nuestra Oferta Académica" default-subheading="Invierta en su futuro profesional con diplomados diseñados por expertos." />

                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="lg:hidden mb-6">
                        <button @click="showFilters = true" class="w-full flex items-center justify-center gap-2 px-4 py-4 bg-surface-container rounded-2xl border border-outline-variant/20 font-medium">
                            <svg class="w-5 h-5 text-on-surface-variant" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            <span class="text-on-surface">Filtrar y Buscar</span>
                            <span v-if="filterForm.modality !== 'Todos' || filterForm.search || filterForm.category !== 'Todas las áreas'" class="ml-2 px-2 py-0.5 bg-primary/10 text-primary text-xs font-bold rounded-full">{{ [filterForm.modality !== 'Todos' ? 1 : 0, filterForm.search ? 1 : 0, filterForm.category !== 'Todas las áreas' ? 1 : 0].reduce((a,b) => a+b, 0) }}</span>
                        </button>
                    </div>

                    <CatalogMobileFilters :show="showFilters" :modalities="modalities" :categories="categories" :filters="{ search: filterForm.search, modality: filterForm.modality, category: filterForm.category }" @close="showFilters = false" @apply="applyModalFilters" />

                    <div class="flex flex-col lg:flex-row gap-10">
                        <CatalogDesktopFilters v-model:filter-form="filterForm" :modalities="modalities" :categories="categories" />

                        <main class="flex-1 min-w-0">
                            <div class="mb-10 pt-2 flex items-center justify-between">
                                <h2 class="text-on-surface-variant font-serif text-2xl"><strong class="text-on-background">{{ courses.total }}</strong> Programas de Excelencia</h2>
                            </div>

                            <div v-if="hasActiveSubscription" class="mb-8 bg-emerald-50 border border-emerald-100 rounded-2xl p-6 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-emerald-900">Tienes acceso total con tu membresía activa</p>
                                    <p class="text-xs text-emerald-700">Dirígete a <Link :href="route('student.courses.index')" class="font-bold underline">Mis Cursos</Link> para comenzar.</p>
                                </div>
                            </div>

                            <div v-if="courses.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                                <CourseCard v-for="course in courses.data" :key="course.id" :course="course" :is-dashboard="isDashboard" />
                            </div>

                            <div v-else class="py-24 flex flex-col items-center justify-center text-center bg-surface-container-highest rounded-[3rem] border-2 border-dashed border-outline-variant/40 space-y-6">
                                <h3 class="text-3xl font-serif font-bold text-on-background italic">Sin coincidencias académicas</h3>
                                <p class="text-on-surface-variant max-w-md font-serif italic text-lg">No hemos encontrado programas que coincidan con su búsqueda en esta escuela.</p>
                                <button @click="filterForm.modality='Todos'; filterForm.category='Todas las áreas'; filterForm.search='';" class="text-xs uppercase tracking-[0.2em] font-bold text-primary border-b border-primary/20 pb-2 hover:text-primary/70 transition-all">Restablecer Filtros</button>
                            </div>

                            <CatalogPagination :links="paginationLinks" />
                        </main>
                    </div>
                </div>
            </main>
        </div>
    </component>
</template>
