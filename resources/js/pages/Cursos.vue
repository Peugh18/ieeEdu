<script setup lang="ts">
import CourseCard from '@/components/CourseCard.vue';
import CatalogDesktopFilters from '@/components/catalog/CatalogDesktopFilters.vue';
import CatalogHero from '@/components/catalog/CatalogHero.vue';
import CatalogMobileFilters from '@/components/catalog/CatalogMobileFilters.vue';
import CatalogPagination from '@/components/catalog/CatalogPagination.vue';
import Navigation from '@/components/landing/Navigation.vue';
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import AppLayout from '@/layouts/AppLayout.vue';
import type { CourseCategory } from '@/types/course';
import type { PaginatedResponse } from '@/types/pagination';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

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
    router.get(
        route(routeName),
        {
            search: filterForm.search || undefined,
            modality: filterForm.modality !== 'Todos' ? filterForm.modality : undefined,
            category: filterForm.category !== 'Todas las áreas' ? filterForm.category : undefined,
        },
        { preserveState: false, preserveScroll: true, replace: true },
    );
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
        <div :class="['flex flex-col font-sans text-on-background', !isDashboard ? 'min-h-screen bg-background' : 'bg-transparent']">
            <Navigation v-if="!isDashboard" />
            <main :class="['flex-1 pb-20', !isDashboard ? 'pt-28 md:pt-28' : 'pt-0']">
                <CatalogHero
                    :banner="banner"
                    :is-dashboard="isDashboard"
                    badge-text="Estrategia y Excelencia"
                    default-heading="Nuestra Oferta Académica"
                    default-subheading="Invierta en su futuro profesional con diplomados diseñados por expertos."
                />

                <div class="mx-auto max-w-[1400px] px-4 sm:px-6 lg:px-8">
                    <div class="mb-6 lg:hidden">
                        <button
                            @click="showFilters = true"
                            class="flex w-full items-center justify-center gap-2 rounded-2xl border border-outline-variant/20 bg-surface-container px-4 py-4 font-medium"
                        >
                            <svg class="h-5 w-5 text-on-surface-variant" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                                />
                            </svg>
                            <span class="text-on-surface">Filtrar y Buscar</span>
                            <span
                                v-if="filterForm.modality !== 'Todos' || filterForm.search || filterForm.category !== 'Todas las áreas'"
                                class="ml-2 rounded-full bg-primary/10 px-2 py-0.5 text-xs font-bold text-primary"
                                >{{
                                    [
                                        filterForm.modality !== 'Todos' ? 1 : 0,
                                        filterForm.search ? 1 : 0,
                                        filterForm.category !== 'Todas las áreas' ? 1 : 0,
                                    ].reduce((a, b) => a + b, 0)
                                }}</span
                            >
                        </button>
                    </div>

                    <CatalogMobileFilters
                        :show="showFilters"
                        :modalities="modalities"
                        :categories="categories"
                        :filters="{ search: filterForm.search, modality: filterForm.modality, category: filterForm.category }"
                        @close="showFilters = false"
                        @apply="applyModalFilters"
                    />

                    <div class="flex flex-col gap-10 lg:flex-row">
                        <CatalogDesktopFilters v-model:filter-form="filterForm" :modalities="modalities" :categories="categories" />

                        <main class="min-w-0 flex-1">
                            <div class="mb-10 flex items-center justify-between pt-2">
                                <h2 class="font-serif text-2xl text-on-surface-variant">
                                    <strong class="text-on-background">{{ courses.total }}</strong> Programas de Excelencia
                                </h2>
                            </div>

                            <div
                                v-if="hasActiveSubscription"
                                class="mb-8 flex items-center gap-4 rounded-2xl border border-emerald-100 bg-emerald-50 p-6"
                            >
                                <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100">
                                    <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-emerald-900">Tienes acceso total con tu membresía activa</p>
                                    <p class="text-xs text-emerald-700">
                                        Dirígete a <Link :href="route('student.courses.index')" class="font-bold underline">Mis Cursos</Link> para
                                        comenzar.
                                    </p>
                                </div>
                            </div>

                            <div v-if="courses.data.length > 0" class="grid grid-cols-1 gap-8 sm:grid-cols-2 xl:grid-cols-3">
                                <CourseCard v-for="course in courses.data" :key="course.id" :course="course" :is-dashboard="isDashboard" />
                            </div>

                            <div
                                v-else
                                class="flex flex-col items-center justify-center space-y-6 rounded-[3rem] border-2 border-dashed border-outline-variant/40 bg-surface-container-highest py-24 text-center"
                            >
                                <h3 class="font-serif text-3xl font-bold italic text-on-background">Sin coincidencias académicas</h3>
                                <p class="max-w-md font-serif text-lg italic text-on-surface-variant">
                                    No hemos encontrado programas que coincidan con su búsqueda en esta escuela.
                                </p>
                                <button
                                    @click="
                                        filterForm.modality = 'Todos';
                                        filterForm.category = 'Todas las áreas';
                                        filterForm.search = '';
                                    "
                                    class="border-b border-primary/20 pb-2 text-xs font-bold uppercase tracking-[0.2em] text-primary transition-all hover:text-primary/70"
                                >
                                    Restablecer Filtros
                                </button>
                            </div>

                            <CatalogPagination :links="paginationLinks" />
                        </main>
                    </div>
                </div>
            </main>
        </div>
    </component>
</template>
