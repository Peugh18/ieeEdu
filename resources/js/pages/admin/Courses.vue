<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import { Plus, Search } from 'lucide-vue-next';
import CreateCourseModal from '@/components/admin/courses/CreateCourseModal.vue';

interface CourseItem {
    id: number;
    title: string;
    description: string;
    price: number;
    type: string;
    status: string;
    category?: { id: number; name: string } | null;
    teacher?: { name: string } | null;
    slug?: string;
}

const props = defineProps<{
    courses: { data: CourseItem[]; links: any[] };
    categories: { data: any[] };
    filters: { status?: string; type?: string; search?: string };
    selected_course?: number;
}>();

const showCreateModal = ref(false);
const categoriesList = ref<Array<{ id: number; name: string }>>([...props.categories.data]);

const filtersForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
    type: props.filters.type || '',
});

function applyFilters() {
    filtersForm.get(route('admin.courses.index'), { preserveState: true, replace: true });
}

const courseLinks = computed(() => props.courses.links.filter((link: any) => link.url));

function destroy(course: CourseItem) {
    if (!confirm('Eliminar curso?')) return;
    useForm().delete(route('admin.courses.destroy', course.id), { preserveState: true });
}

function publish(course: CourseItem) {
    useForm().patch(route('admin.courses.publish', course.id), { preserveState: true });
}

function hide(course: CourseItem) {
    useForm().patch(route('admin.courses.hide', course.id), { preserveState: true });
}

onMounted(() => {
    // Index page: creation happens via modal -> redirect to editor
});
</script>

<template>
    <Head title="Admin Cursos" />
    <AppLayout>
        <div class="mb-6 flex flex-col gap-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="font-serif text-4xl text-on-surface">Gestión de <span class="italic">Cursos</span></h1>
                    <p class="mt-2 text-sm text-on-surface-variant">Administra y organiza el catálogo académico de la institución.</p>
                </div>
                <button
                    @click="showCreateModal = true"
                    class="inline-flex items-center justify-center gap-2 rounded-full bg-primary px-6 py-3 text-sm font-bold text-white shadow hover:opacity-95 w-full sm:w-auto"
                >
                    <Plus class="w-5 h-5" />
                    Crear nuevo curso
                </button>
            </div>

            <!-- Filters bar -->
            <div class="rounded-2xl bg-surface-container-low p-3 border border-outline-variant/10 mb-2">
                <div class="flex flex-col md:flex-row gap-3 items-center">
                    <div class="flex-1 w-full">
                        <div class="flex items-center gap-2 rounded-xl bg-white border border-outline-variant/15 px-3 py-2">
                            <Search class="w-4 h-4 text-on-surface-variant" />
                            <input
                                v-model="filtersForm.search"
                                placeholder="Buscar por título…"
                                class="w-full bg-transparent outline-none text-sm text-on-surface placeholder:text-on-surface-variant/60"
                                @keydown.enter.prevent="applyFilters"
                            />
                        </div>
                    </div>
                    <div class="w-full md:w-auto">
                        <select v-model="filtersForm.status" class="w-full rounded-xl bg-white border border-outline-variant/15 px-3 py-2 text-sm">
                            <option value="">Estado</option>
                            <option value="BORRADOR">Borrador</option>
                            <option value="PUBLICADO">Publicado</option>
                            <option value="OCULTO">Oculto</option>
                        </select>
                    </div>
                    <div class="w-full md:w-auto">
                        <select v-model="filtersForm.type" class="w-full rounded-xl bg-white border border-outline-variant/15 px-3 py-2 text-sm">
                            <option value="">Tipo</option>
                            <option value="grabado">Grabado</option>
                            <option value="en vivo">En vivo</option>
                            <option value="evento">Masterclass</option>
                        </select>
                    </div>
                    <div class="w-full md:w-auto flex justify-end">
                        <button class="rounded-xl bg-primary px-4 py-2 text-sm font-bold text-white w-full md:w-auto" @click="applyFilters">
                            Aplicar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <CreateCourseModal
            :open="showCreateModal"
            :categories="categoriesList"
            @close="showCreateModal = false"
            @categoryCreated="(c) => categoriesList.push(c)"
        />

        <!-- List -->
        <div class="rounded-3xl border border-outline-variant/10 bg-white overflow-hidden">
            <div class="grid grid-cols-12 gap-2 px-6 py-4 text-[11px] font-bold uppercase tracking-widest text-on-surface-variant/80 bg-surface-container-low">
                <div class="col-span-6">Curso</div>
                <div class="col-span-2">Categoría</div>
                <div class="col-span-2">Precio</div>
                <div class="col-span-1">Tipo</div>
                <div class="col-span-1">Estado</div>
            </div>

            <div v-if="!props.courses.data.length" class="p-10 text-center text-sm text-on-surface-variant">
                No hay cursos.
            </div>

            <div v-else>
                <div
                    v-for="course in props.courses.data"
                    :key="course.id"
                    class="grid grid-cols-12 gap-2 items-center px-6 py-4 border-t border-outline-variant/10 hover:bg-surface-container-lowest transition"
                >
                    <div class="col-span-6 flex items-center gap-4 min-w-0">
                        <div class="h-12 w-12 rounded-2xl overflow-hidden bg-surface-container-low border border-outline-variant/15 flex-shrink-0">
                            <img v-if="(course as any).image" :src="(course as any).image" class="h-full w-full object-cover" />
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-sm text-on-surface truncate">{{ course.title }}</p>
                            <p class="text-xs text-on-surface-variant truncate">ID: {{ course.id }}</p>
                        </div>
                        <Link :href="route('admin.courses.edit', course.id)" class="ml-auto text-xs font-bold text-primary hover:underline">
                            Editar
                        </Link>
                    </div>

                    <div class="col-span-2">
                        <span class="inline-flex rounded-full bg-secondary-container px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-on-secondary-container">
                            {{ course.category?.name || 'N/A' }}
                        </span>
                    </div>

                    <div class="col-span-2 font-bold text-sm text-on-surface">S/ {{ course.price }}</div>
                    <div class="col-span-1 text-sm text-on-surface-variant">{{ course.type }}</div>
                    <div class="col-span-1">
                        <div class="flex items-center gap-2">
                            <span
                                class="h-2 w-2 rounded-full"
                                :class="course.status === 'PUBLICADO' ? 'bg-emerald-500' : (course.status === 'BORRADOR' ? 'bg-amber-500' : 'bg-slate-400')"
                            />
                            <span class="text-xs font-semibold text-on-surface-variant">{{ course.status }}</span>
                        </div>
                    </div>

                    <div class="col-span-12 flex gap-2 justify-end pt-2">
                        <button class="rounded-xl border border-outline-variant/20 px-3 py-1.5 text-xs font-bold hover:bg-surface-container-low" @click="publish(course)">Publicar</button>
                        <button class="rounded-xl border border-outline-variant/20 px-3 py-1.5 text-xs font-bold hover:bg-surface-container-low" @click="hide(course)">Ocultar</button>
                        <button class="rounded-xl bg-red-600 px-3 py-1.5 text-xs font-bold text-white hover:opacity-90" @click="destroy(course)">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 flex justify-between">
            <ul class="flex gap-2">
                <li v-for="link in courseLinks" :key="link.label">
                    <Link :href="link.url" class="rounded border px-3 py-1">{{ link.label }}</Link>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
