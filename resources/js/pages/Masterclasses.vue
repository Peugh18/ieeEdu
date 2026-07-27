<script setup lang="ts">
import CatalogHero from '@/components/catalog/CatalogHero.vue';
import CatalogPagination from '@/components/catalog/CatalogPagination.vue';
import MasterclassCard from '@/components/catalog/MasterclassCard.vue';
import Navigation from '@/components/landing/Navigation.vue';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import AppLayout from '@/layouts/AppLayout.vue';
import type { Course, CourseCategory } from '@/types/course';
import type { PaginatedResponse } from '@/types/pagination';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    courses: PaginatedResponse<Course>;
    categories: CourseCategory[];
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
    router.get(route(routeName), { category: category === 'Todas' ? '' : category }, { preserveState: false, preserveScroll: true, replace: true });
}

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Masterclasses en Vivo', href: '#' },
];

const paginationLinks = usePaginationLinks(props.courses.links);
</script>

<template>
    <Head title="Masterclasses - IEE" />
    <component :is="isDashboard ? AppLayout : 'div'" v-bind="isDashboard ? { breadcrumbs } : {}">
        <div :class="['flex min-h-screen flex-col font-sans', !isDashboard ? 'bg-background' : 'bg-transparent shadow-none']">
            <Navigation v-if="!isDashboard" />
            <main :class="['flex-1 pb-20', !isDashboard ? 'pt-28' : 'pt-0']">
                <CatalogHero
                    :banner="banner"
                    :is-dashboard="isDashboard"
                    badge-text="Campus Digital IEE"
                    default-heading="Masterclasses en Vivo"
                    default-subheading="Sesiones interactivas con expertos. Tu camino hacia la excelencia."
                />

                <div class="relative z-10 mx-auto max-w-[1400px] px-4 pt-4 sm:px-6 lg:px-8">
                    <div class="mb-8 flex flex-col gap-4 border-b border-outline-variant/15 pb-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none sm:pb-0">
                            <button
                                @click="applyFilters('Todas')"
                                class="whitespace-nowrap rounded-full border px-4 py-2 text-xs font-bold transition-all"
                                :class="
                                    selectedCategory === 'Todas'
                                        ? 'border-primary bg-primary text-on-primary shadow-md shadow-primary/10'
                                        : 'border-outline-variant/30 bg-surface-container text-on-surface-variant hover:border-primary/40 hover:text-primary'
                                "
                            >
                                Todas
                            </button>
                            <button
                                v-for="cat in categories"
                                :key="cat.id"
                                @click="applyFilters(cat.name)"
                                class="whitespace-nowrap rounded-full border px-4 py-2 text-xs font-bold transition-all"
                                :class="
                                    selectedCategory === cat.name
                                        ? 'border-primary bg-primary text-on-primary shadow-md shadow-primary/10'
                                        : 'border-outline-variant/30 bg-surface-container text-on-surface-variant hover:border-primary/40 hover:text-primary'
                                "
                            >
                                {{ cat.name }}
                            </button>
                        </div>
                        <div class="text-xs text-on-surface-variant">
                            Mostrando <span class="font-bold text-on-surface">{{ courses.total || 0 }}</span> sesiones encontradas
                        </div>
                    </div>

                    <div v-if="courses.data.length > 0" class="mb-12">
                        <div class="mb-12 grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                            <MasterclassCard v-for="course in courses.data" :key="course.id" :course="course" :is-dashboard="isDashboard" />
                        </div>
                        <CatalogPagination :links="paginationLinks" />
                    </div>

                    <div
                        v-else
                        class="mx-auto flex max-w-md flex-col items-center justify-center space-y-4 rounded-3xl border border-outline-variant/20 bg-surface-container-low/70 p-6 py-12 text-center shadow-sm"
                    >
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <h3 class="font-serif text-lg font-bold text-on-surface">Próximamente nuevas sesiones</h3>
                            <p class="max-w-xs text-xs text-on-surface-variant">
                                No encontramos masterclasses en esta categoría por ahora. ¡Vuelve pronto!
                            </p>
                        </div>
                    </div>
                </div>iv>
            </main>
        </div>
    </component>
</template>
