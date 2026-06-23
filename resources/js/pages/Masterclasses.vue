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

                <div class="relative z-10 mx-auto max-w-[1400px] px-4 pt-6 sm:px-6 lg:px-8">
                    <div class="mb-12 flex flex-wrap items-center justify-between gap-6 border-b border-outline-variant/20 pb-6">
                        <div class="flex flex-wrap gap-2">
                            <button
                                @click="applyFilters('Todas')"
                                class="rounded-full border px-5 py-2 text-xs font-bold transition-all"
                                :class="
                                    selectedCategory === 'Todas'
                                        ? 'border-primary bg-primary text-on-primary shadow-md shadow-primary/10'
                                        : 'border-outline-variant/30 bg-surface text-on-surface-variant hover:border-primary/40 hover:text-primary'
                                "
                            >
                                Todas
                            </button>
                            <button
                                v-for="cat in categories"
                                :key="cat.id"
                                @click="applyFilters(cat.name)"
                                class="rounded-full border px-5 py-2 text-xs font-bold transition-all"
                                :class="
                                    selectedCategory === cat.name
                                        ? 'border-primary bg-primary text-on-primary shadow-md shadow-primary/10'
                                        : 'border-outline-variant/30 bg-surface text-on-surface-variant hover:border-primary/40 hover:text-primary'
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
                        class="mx-auto max-w-3xl space-y-4 rounded-[3rem] border border-dashed border-outline-variant/30 bg-surface-container-low p-8 py-24 text-center"
                    >
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary/5 text-primary/40">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h10a2 2 0 012 2v2M7 7h10"
                                />
                            </svg>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-on-surface">Próximamente nuevas sesiones</h3>
                        <p class="mx-auto max-w-sm text-xs text-on-surface-variant">
                            No encontramos masterclasses en esta categoría por ahora. ¡Vuelve pronto!
                        </p>
                    </div>
                </div>
            </main>
        </div>
    </component>
</template>
