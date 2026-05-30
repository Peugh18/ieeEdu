<script setup lang="ts">
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import Navigation from '@/components/landing/Navigation.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import CatalogHero from '@/components/catalog/CatalogHero.vue';
import CatalogPagination from '@/components/catalog/CatalogPagination.vue';
import MasterclassCard from '@/components/catalog/MasterclassCard.vue';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import type { Course, CourseCategory } from '@/types/course';
import type { PaginatedResponse } from '@/types/pagination';

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
                <CatalogHero :banner="banner" :is-dashboard="isDashboard" badge-text="Campus Digital IEE" default-heading="Masterclasses en Vivo" default-subheading="Sesiones interactivas con expertos. Tu camino hacia la excelencia." />

                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-6">
                    <div class="flex items-center justify-between mb-12 flex-wrap gap-6 border-b border-outline-variant/20 pb-6">
                        <div class="flex flex-wrap gap-2">
                            <button @click="applyFilters('Todas')" class="px-5 py-2 rounded-full text-xs font-bold transition-all border" :class="selectedCategory === 'Todas' ? 'bg-primary border-primary text-on-primary shadow-md shadow-primary/10' : 'bg-surface border-outline-variant/30 text-on-surface-variant hover:border-primary/40 hover:text-primary'">Todas</button>
                            <button v-for="cat in categories" :key="cat.id" @click="applyFilters(cat.name)" class="px-5 py-2 rounded-full text-xs font-bold transition-all border" :class="selectedCategory === cat.name ? 'bg-primary border-primary text-on-primary shadow-md shadow-primary/10' : 'bg-surface border-outline-variant/30 text-on-surface-variant hover:border-primary/40 hover:text-primary'">{{ cat.name }}</button>
                        </div>
                        <div class="text-xs text-on-surface-variant">Mostrando <span class="font-bold text-on-surface">{{ courses.total || 0 }}</span> sesiones encontradas</div>
                    </div>

                    <div v-if="courses.data.length > 0" class="mb-12">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                            <MasterclassCard v-for="course in courses.data" :key="course.id" :course="course" :is-dashboard="isDashboard" />
                        </div>
                        <CatalogPagination :links="paginationLinks" />
                    </div>

                    <div v-else class="py-24 text-center bg-surface-container-low rounded-[3rem] border border-dashed border-outline-variant/30 max-w-3xl mx-auto p-8 space-y-4">
                        <div class="w-16 h-16 bg-primary/5 rounded-full flex items-center justify-center mx-auto text-primary/40">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h10a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <h3 class="font-serif text-xl font-bold text-on-surface">Próximamente nuevas sesiones</h3>
                        <p class="text-xs text-on-surface-variant max-w-sm mx-auto">No encontramos masterclasses en esta categoría por ahora. ¡Vuelve pronto!</p>
                    </div>
                </div>
            </main>
        </div>
    </component>
</template>
