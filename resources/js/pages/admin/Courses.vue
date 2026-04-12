<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import { 
    Plus, Search, Eye, EyeOff, Trash2, 
    Pen, FileBadge, Filter, CopyPlus, GraduationCap 
} from 'lucide-vue-next';
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
const courseToDuplicate = ref<CourseItem | null>(null);
const categoriesList = ref<Array<{ id: number; name: string }>>([...props.categories.data]);

const filtersForm = useForm({
    search: props.filters.search || '',
    status: props.filters.status || '',
    type: props.filters.type || '',
});

function applyFilters() {
    filtersForm.get(route('admin.courses.index'), { preserveState: true, replace: true });
}

function openCreateModal(course: CourseItem | null = null) {
    courseToDuplicate.value = course;
    showCreateModal.value = true;
}

const courseLinks = computed(() => props.courses.links.filter((link: any) => link.url));

function destroy(course: CourseItem) {
    if (!confirm('Eliminar curso?')) return;
    const form = useForm({});
    form.delete(route('admin.courses.destroy', { course: course.id }), { preserveState: true });
}

function publish(course: CourseItem) {
    const form = useForm({});
    form.patch(route('admin.courses.publish', { course: course.id }), { preserveState: true });
}

function hide(course: CourseItem) {
    const form = useForm({});
    form.patch(route('admin.courses.hide', { course: course.id }), { preserveState: true });
}

function duplicateCourse(course: CourseItem) {
    if (!confirm(`¿Deseas clonar el curso "${course.title}"? Se creará una copia con un nuevo ID.`)) return;
    const form = useForm({});
    form.post(route('admin.courses.duplicate', { course: course.id }), { 
        preserveScroll: true,
        onSuccess: () => {
            // Se refrescará la lista automáticamente
        }
    });
}

onMounted(() => {
    // Index page: creation happens via modal -> redirect to editor
});
</script>

<template>
    <Head title="Admin Cursos" />
    <AppLayout>
        
        <div class="mb-10 flex flex-col gap-8">
            <!-- PAGE HEADER -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between px-2">
                <div>
                    <h1 class="font-serif text-4xl text-on-surface">Gestión de <span class="italic text-[#57572A]">Cursos</span></h1>
                    <p class="mt-3 text-[14px] text-on-surface-variant font-medium">Administra y organiza el catálogo académico de la institución.</p>
                </div>
                <button
                    @click="openCreateModal(null)"
                    class="inline-flex items-center justify-center gap-3 rounded-full bg-gradient-to-br from-[#57572A] to-[#6d6d35] px-8 py-3.5 text-[14px] font-bold text-white shadow-lg shadow-[#57572A]/20 hover:scale-[1.02] active:scale-[0.98] transition-all w-full sm:w-auto font-sans border-none"
                >
                    <Plus class="w-5 h-5" />
                    Crear nuevo curso
                </button>
            </div>

            <!-- SEARCH & FILTERS -->
            <div class="bg-white p-5 rounded-[2.5rem] border border-outline-variant/10 shadow-sm shadow-black/5 flex flex-col xl:flex-row gap-5 items-center relative z-10 w-full">
                <!-- Search bar -->
                <div class="flex-1 w-full relative">
                    <Search class="absolute left-6 top-1/2 -translate-y-1/2 w-4.5 h-4.5 text-[#57572A]/40" />
                    <input
                        v-model="filtersForm.search"
                        placeholder="Buscar por título, ID o descripción..."
                        class="w-full bg-surface-container-highest rounded-full pl-14 pr-6 py-4 text-[14px] font-sans outline-none focus:ring-2 focus:ring-[#57572A]/20 transition-all border-transparent placeholder:text-[#57572A]/30 font-medium"
                        @keydown.enter.prevent="applyFilters"
                    />
                </div>
                
                <div class="w-full xl:w-auto flex flex-col md:flex-row gap-4">
                    <!-- Status Filter -->
                    <div class="relative w-full md:w-48">
                        <select v-model="filtersForm.status" class="w-full rounded-full bg-surface-container-highest pl-6 pr-10 py-4 text-[13px] font-bold uppercase tracking-wider text-[#57572A] border-transparent outline-none focus:ring-2 focus:ring-[#57572A]/20 appearance-none cursor-pointer transition-all">
                            <option value="">Todos los Estados</option>
                            <option value="BORRADOR">Borrador</option>
                            <option value="PUBLICADO">Publicado</option>
                            <option value="OCULTO">Oculto</option>
                        </select>
                        <Filter class="absolute right-5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-[#57572A]/40 pointer-events-none" />
                    </div>

                    <!-- Type Filter -->
                    <div class="relative w-full md:w-56">
                        <select v-model="filtersForm.type" class="w-full rounded-full bg-surface-container-highest pl-6 pr-10 py-4 text-[13px] font-bold uppercase tracking-wider text-[#57572A] border-transparent outline-none focus:ring-2 focus:ring-[#57572A]/20 appearance-none cursor-pointer transition-all">
                            <option value="">Cualquier Modalidad</option>
                            <option value="grabado">Asincrónico (Grabado)</option>
                            <option value="en vivo">Sincrónico (En Vivo)</option>
                            <option value="evento">Masterclass / Evento</option>
                        </select>
                        <Filter class="absolute right-5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-[#57572A]/40 pointer-events-none" />
                    </div>

                    <!-- Apply -->
                    <button class="rounded-full bg-[#57572A] hover:bg-[#4a4a24] px-8 py-4 text-[13px] font-bold text-white transition-all shadow-md shadow-[#57572A]/10 border-none" @click="applyFilters">
                        Filtrar
                    </button>
                </div>
            </div>
        </div>

        <CreateCourseModal
            :open="showCreateModal"
            :categories="categoriesList"
            :duplicate-data="courseToDuplicate"
            @close="showCreateModal = false; courseToDuplicate = null"
            @categoryCreated="(c) => categoriesList.push(c)"
        />

        <!-- LIST TABLE SECTION -->
        <div class="rounded-[3rem] border border-outline-variant/10 bg-white overflow-hidden shadow-2xl shadow-surface-tint/5 relative z-0">
            <div class="overflow-x-auto custom-scrollbar">
                <div class="min-w-[1100px]">
                    <!-- Table Header -->
                    <div class="grid grid-cols-12 gap-6 px-12 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-[#57572A]/60 bg-surface-container-highest/40 border-b border-outline-variant/10">
                        <div class="col-span-4">Programa Académico</div>
                        <div class="col-span-2">Categoría</div>
                        <div class="col-span-2">Inversión & Metodología</div>
                        <div class="col-span-2 text-center">Estado de Visibilidad</div>
                        <div class="col-span-2 text-right">Acciones de Gestión</div>
                    </div>

                    <div v-if="!props.courses.data.length" class="p-24 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 rounded-full bg-surface-container-highest flex items-center justify-center text-[#57572A]/20 mb-6">
                            <Search class="w-8 h-8" />
                        </div>
                        <h3 class="font-serif font-bold text-on-surface text-xl">Sin registros académicos</h3>
                        <p class="text-sm text-on-surface-variant mt-2 max-w-xs">No se encontraron cursos que coincidan con los criterios de búsqueda actuales.</p>
                    </div>

                    <!-- Table Rows -->
                    <div v-else class="divide-y divide-outline-variant/5">
                        <div
                            v-for="course in props.courses.data"
                            :key="course.id"
                            class="grid grid-cols-12 gap-6 items-center px-12 py-7 hover:bg-[#57572A]/[0.02] transition-colors duration-500 group"
                        >
                            <!-- INFOGRAFÍA (4cols) -->
                            <div class="col-span-4 flex items-center gap-6 min-w-0">
                                <div class="h-20 w-28 rounded-3xl overflow-hidden bg-surface-container-low border border-outline-variant/10 flex-shrink-0 relative shadow-inner group-hover:shadow-lg transition-all duration-500">
                                    <img v-if="(course as any).image" :src="(course as any).image" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                                    <div v-else class="w-full h-full bg-surface-container-highest flex items-center justify-center">
                                        <GraduationCap class="w-8 h-8 text-[#57572A]/10" />
                                    </div>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                </div>
                                <div class="min-w-0 pr-4">
                                    <p class="font-serif font-bold text-[16px] text-on-surface truncate group-hover:text-[#57572A] transition-colors leading-snug mb-1.5" :title="course.title">
                                        {{ course.title }}
                                    </p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-black tracking-widest text-[#57572A]/40 uppercase bg-[#57572A]/5 px-2 py-0.5 rounded-md">REF: #{{ String(course.id).padStart(4, '0') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- CATEGORÍA (2cols) -->
                            <div class="col-span-2">
                                <span class="inline-flex rounded-xl bg-white px-4 py-2 text-[11px] font-black uppercase tracking-widest text-[#57572A] border border-outline-variant/20 shadow-sm group-hover:border-[#57572A]/30 transition-colors">
                                    {{ course.category?.name || 'General' }}
                                </span>
                            </div>

                            <!-- PRECIO & FORMATO (2cols) -->
                            <div class="col-span-2 flex flex-col justify-center">
                                <span class="font-serif font-black text-[17px] text-on-surface mb-0.5">
                                    <template v-if="course.price > 0">S/ {{ course.price }}</template>
                                    <template v-else><span class="text-[#57572A] italic tracking-tight">Acceso Gratuito</span></template>
                                </span>
                                <span class="text-[11px] font-bold tracking-wider text-on-surface-variant flex items-center gap-1.5">
                                    <div class="w-1.5 h-1.5 rounded-full" :class="course.type === 'grabado' ? 'bg-amber-400' : 'bg-blue-400'"></div>
                                    {{ course.type === 'grabado' ? 'Asincrónico' : (course.type === 'en vivo' ? 'Sincrónico' : 'Masterclass') }}
                                </span>
                            </div>

                            <!-- ESTADO (2cols) -->
                            <div class="col-span-2 flex justify-center">
                                <div class="flex items-center gap-3 px-5 py-2.5 rounded-2xl bg-white border border-outline-variant/10 shadow-sm w-fit group-hover:shadow-md transition-all duration-500"
                                    :class="{
                                        'border-emerald-500/20 shadow-emerald-500/[0.03]': course.status === 'PUBLICADO',
                                        'border-amber-500/20 shadow-amber-500/[0.03]': course.status === 'BORRADOR',
                                        'border-rose-500/20 shadow-rose-500/[0.03]': course.status === 'OCULTO'
                                    }"
                                >   
                                    <div class="relative flex h-2 w-2">
                                      <span v-if="course.status === 'PUBLICADO'" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2 w-2"
                                          :class="{
                                            'bg-emerald-500': course.status === 'PUBLICADO',
                                            'bg-amber-400': course.status === 'BORRADOR',
                                            'bg-rose-500': course.status === 'OCULTO',
                                            'bg-slate-300': !['PUBLICADO','BORRADOR','OCULTO'].includes(course.status)
                                          }"
                                      ></span>
                                    </div>
                                    <span class="text-[11px] font-black uppercase tracking-[0.15em] mt-[0.5px]"
                                          :class="{
                                            'text-emerald-700': course.status === 'PUBLICADO',
                                            'text-amber-700': course.status === 'BORRADOR',
                                            'text-rose-700': course.status === 'OCULTO',
                                            'text-slate-500': !['PUBLICADO','BORRADOR','OCULTO'].includes(course.status)
                                          }"
                                    >
                                        {{ course.status }}
                                    </span>
                                </div>
                            </div>

                            <!-- ACCIONES (2cols) -->
                            <div class="col-span-2 flex items-center justify-end">
                                <!-- Ghost floating action bar -->
                                <div class="flex items-center bg-surface-container-highest/30 p-2 rounded-2xl border border-transparent group-hover:border-outline-variant/20 group-hover:bg-white group-hover:shadow-xl transition-all duration-700 opacity-40 group-hover:opacity-100 transform group-hover:-translate-x-2">
                                    
                                    <!-- Editar -->
                                    <Link :href="route('admin.courses.edit', course.id)" class="p-3 rounded-xl hover:bg-blue-50 text-on-surface-variant hover:text-blue-600 transition-all hover:scale-110" title="Editar Contenido">
                                        <Pen class="w-4 h-4" />
                                    </Link>

                                    <!-- Certificado -->
                                    <Link :href="route('admin.courses.certificate-template.edit', { course: course.id })" class="p-3 rounded-xl hover:bg-[#57572A]/5 text-on-surface-variant hover:text-[#57572A] transition-all hover:scale-110" title="Diseñar Certificado">
                                        <FileBadge class="w-4 h-4" />
                                    </Link>

                                    <!-- Duplicar -->
                                    <button @click="duplicateCourse(course)" class="p-3 rounded-xl hover:bg-[#57572A]/5 text-on-surface-variant hover:text-[#57572A] transition-all hover:scale-110" title="Clonación Instantánea">
                                        <CopyPlus class="w-4 h-4" />
                                    </button>

                                    <div class="w-px h-6 bg-outline-variant/15 mx-1.5"></div>

                                    <!-- Visibilidad: Publicar -->
                                    <button v-if="course.status !== 'PUBLICADO'" @click="publish(course)" class="p-3 rounded-xl hover:bg-emerald-50 text-on-surface-variant hover:text-emerald-600 transition-all hover:scale-110" title="Publicar Ahora">
                                        <Eye class="w-4 h-4" />
                                    </button>
                                    
                                    <!-- Visibilidad: Ocultar -->
                                    <button v-if="course.status === 'PUBLICADO'" @click="hide(course)" class="p-3 rounded-xl hover:bg-amber-50 text-on-surface-variant hover:text-amber-600 transition-all hover:scale-110" title="Bajar de Cartelera">
                                        <EyeOff class="w-4 h-4" />
                                    </button>

                                    <!-- Eliminar -->
                                    <button @click="destroy(course)" class="p-3 rounded-xl hover:bg-rose-50 text-on-surface-variant hover:text-rose-600 transition-all hover:scale-110" title="Eliminar Registro">
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PAGINACIÓN MODERNA -->
        <div v-if="courseLinks.length > 1" class="mt-10 flex justify-center pb-12">
            <div class="inline-flex items-center bg-white rounded-full border border-outline-variant/10 shadow-sm p-1.5 gap-1">
                <template v-for="(link, i) in courseLinks" :key="i">
                    <!-- Parse link.label to remove &laquo; and &raquo; if they come from Laravel -->
                    <Link 
                        :href="link.url || '#'" 
                        class="px-5 py-2.5 text-[13px] font-bold rounded-full transition-all flex items-center justify-center min-w-[40px]"
                        :class="link.active 
                                ? 'bg-[#57572A] text-white shadow-md cursor-default' 
                                : 'text-on-surface hover:bg-surface-container-low cursor-pointer'"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Scrollbar that blends beautifully with the table wrapper */
.custom-scrollbar::-webkit-scrollbar {
    height: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
    margin-inline: 20px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: var(--md-sys-color-outline-variant, #C9C7B8);
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: var(--md-sys-color-outline, #57572A);
}
</style>
