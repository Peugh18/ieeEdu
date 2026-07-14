<script setup lang="ts">
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import CreateCourseModal from '@/components/admin/courses/CreateCourseModal.vue';
import CoursesFilters from '@/components/admin/courses/list/CoursesFilters.vue';
import CoursesTable, { type CourseItem } from '@/components/admin/courses/list/CoursesTable.vue';
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import AppLayout from '@/layouts/AppLayout.vue';
import type { PaginatedResponse } from '@/types/pagination';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    courses: PaginatedResponse<CourseItem>;
    categories: { data: { id: number; name: string }[] };
    filters: { status?: string; type?: string; search?: string; per_page?: string };
    selected_course?: number;
}>();

const showCreateModal = ref(false);
const courseToDuplicate = ref<CourseItem | null>(null);
const categoriesList = ref<Array<{ id: number; name: string }>>([...props.categories.data]);

const filtersForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
    type: props.filters.type || '',
    per_page: props.filters.per_page || '10',
});

function applyFilters() {
    router.get(
        route('admin.courses.index'),
        {
            search: filtersForm.search || undefined,
            status: filtersForm.status || undefined,
            type: filtersForm.type || undefined,
            per_page: filtersForm.per_page !== '10' ? filtersForm.per_page : undefined,
        },
        { preserveState: false, replace: true },
    );
}

useDebouncedInertiaFilters(filtersForm, applyFilters);

function openCreateModal(course: CourseItem | null = null) {
    courseToDuplicate.value = course;
    showCreateModal.value = true;
}

function destroy(course: CourseItem) {
    if (!confirm('Eliminar curso?')) return;
    router.delete(route('admin.courses.destroy', { course: course.id }), {
        preserveState: false,
        preserveScroll: true,
    });
}

function publish(course: CourseItem) {
    router.patch(
        route('admin.courses.publish', { course: course.id }),
        {},
        {
            preserveState: false,
            preserveScroll: true,
            onError: (errors: Record<string, string>) => {
                if (errors.course) alert(errors.course);
            },
        },
    );
}

function hide(course: CourseItem) {
    router.patch(
        route('admin.courses.hide', { course: course.id }),
        {},
        {
            preserveState: false,
            preserveScroll: true,
        },
    );
}

function duplicateCourse(course: CourseItem) {
    if (!confirm(`¿Deseas clonar el curso "${course.title}"? Se creará una copia con un nuevo ID.`)) return;
    router.post(
        route('admin.courses.duplicate', { course: course.id }),
        {},
        {
            preserveScroll: true,
            preserveState: false,
        },
    );
}

const paginationLinks = usePaginationLinks(props.courses.links);
</script>

<template>
    <Head title="Gestión de Cursos - iieEdu Admin" />
    <AppLayout>
        <div class="w-full space-y-8 px-6 py-8 lg:px-10">
            <!-- PAGE HEADER -->
            <AdminPageHeader
                title="Gestión de "
                titleAccent="Cursos"
                subtitle="Publica, edita y organiza el contenido académico."
                actionLabel="Nuevo Curso"
                @action="openCreateModal(null)"
            />

            <!-- Smart Filtering -->
            <CoursesFilters
                v-model:search="filtersForm.search"
                v-model:status="filtersForm.status"
                v-model:type="filtersForm.type"
                v-model:perPage="filtersForm.per_page"
                @filter="applyFilters"
            />

            <!-- LIST TABLE SECTION -->
            <CoursesTable
                :courses="courses.data"
                :total="courses.total"
                :paginationLinks="paginationLinks"
                @duplicate="duplicateCourse"
                @publish="publish"
                @hide="hide"
                @destroy="destroy"
            />
        </div>

        <CreateCourseModal
            :open="showCreateModal"
            :categories="categoriesList"
            :duplicate-data="courseToDuplicate"
            @close="
                showCreateModal = false;
                courseToDuplicate = null;
            "
            @categoryCreated="(c) => categoriesList.push(c)"
        />
    </AppLayout>
</template>
