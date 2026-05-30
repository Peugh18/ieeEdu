<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { computed, ref, onMounted, watch } from 'vue';
import { Plus } from 'lucide-vue-next';
import CreateCourseModal from '@/components/admin/courses/CreateCourseModal.vue';
import CoursesFilters from '@/components/admin/courses/list/CoursesFilters.vue';
import CoursesTable, { type CourseItem } from '@/components/admin/courses/list/CoursesTable.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import type { PaginationLink } from '@/types/pagination';

const props = defineProps<{
    courses: { data: CourseItem[]; links: PaginationLink[] };
    categories: { data: { id: number; name: string }[] };
    filters: { status?: string; type?: string; search?: string };
    selected_course?: number;
}>();

const showCreateModal = ref(false);
const courseToDuplicate = ref<CourseItem | null>(null);
const categoriesList = ref<Array<{ id: number; name: string }>>([...props.categories.data]);

const filtersForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
    type: props.filters.type || '',
});

function applyFilters() {
    filtersForm.get(route('admin.courses.index'), { preserveScroll: true, replace: true, preserveState: false });
}

let searchTimeout: ReturnType<typeof setTimeout>;
const skipFilterWatch = ref(true);
watch(
    () => [filtersForm.search, filtersForm.status, filtersForm.type],
    () => {
        if (skipFilterWatch.value) return;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => applyFilters(), 400);
    },
);

onMounted(() => {
    skipFilterWatch.value = false;
});

function openCreateModal(course: CourseItem | null = null) {
    courseToDuplicate.value = course;
    showCreateModal.value = true;
}

const courseLinks = computed(() => props.courses.links.filter((link: PaginationLink) => link.url));

function destroy(course: CourseItem) {
    if (!confirm('Eliminar curso?')) return;
    router.delete(route('admin.courses.destroy', { course: course.id }), { 
        preserveState: false,
        preserveScroll: true
    });
}

function publish(course: CourseItem) {
    router.patch(route('admin.courses.publish', { course: course.id }), {}, { 
        preserveState: false,
        preserveScroll: true,
        onError: (errors: Record<string, string>) => {
            if (errors.course) alert(errors.course);
        }
    });
}

function hide(course: CourseItem) {
    router.patch(route('admin.courses.hide', { course: course.id }), {}, { 
        preserveState: false,
        preserveScroll: true
    });
}

function duplicateCourse(course: CourseItem) {
    if (!confirm(`¿Deseas clonar el curso "${course.title}"? Se creará una copia con un nuevo ID.`)) return;
    router.post(route('admin.courses.duplicate', { course: course.id }), {}, { 
        preserveScroll: true,
        preserveState: false
    });
}
</script>

<template>
    <Head title="Admin Cursos" />
    <AppLayout>
        
        <div class="mb-10 flex flex-col gap-8">
            <!-- PAGE HEADER -->
            <AdminPageHeader
                title="Catálogo de "
                titleAccent="cursos"
                subtitle="Publica, edita y organiza el contenido académico."
                actionLabel="Crear nuevo curso"
                compact
                @action="openCreateModal(null)"
            />

            <!-- Filtros compactos -->
            <CoursesFilters
                v-model:search="filtersForm.search"
                v-model:status="filtersForm.status"
                v-model:type="filtersForm.type"
                @filter="applyFilters"
            />
        </div>

        <CreateCourseModal
            :open="showCreateModal"
            :categories="categoriesList"
            :duplicate-data="courseToDuplicate"
            @close="showCreateModal = false; courseToDuplicate = null"
            @categoryCreated="(c) => categoriesList.push(c)"
        />

        <!-- LIST TABLE SECTION -->
        <CoursesTable
            :courses="courses.data"
            @duplicate="duplicateCourse"
            @publish="publish"
            @hide="hide"
            @destroy="destroy"
        />

        <!-- PAGINACIÓN MODERNA -->
        <div v-if="courseLinks.length > 1" class="mt-10 flex justify-center pb-12">
            <div class="inline-flex items-center bg-white rounded-full border border-outline-variant/10 shadow-sm p-1.5 gap-1">
                <template v-for="(link, i) in courseLinks" :key="i">
                    <Link 
                        :href="link.url || '#'" 
                        class="px-5 py-2.5 text-[13px] font-bold rounded-full transition-all flex items-center justify-center min-w-[40px]"
                        :class="link.active 
                                ? 'bg-primary text-white shadow-md cursor-default' 
                                : 'text-on-surface hover:bg-surface-container-low cursor-pointer'"
                        ><span v-html="link.label"></span></Link>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
