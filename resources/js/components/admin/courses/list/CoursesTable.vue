<script setup lang="ts">
import { PaginationLink } from '@/types/pagination';
import { Link } from '@inertiajs/vue3';
import { CopyPlus, Eye, EyeOff, FileBadge, GraduationCap, Pen, Search, Trash2 } from 'lucide-vue-next';

export interface CourseItem {
    id: number;
    title: string;
    description: string;
    price: number;
    type: string;
    status: string;
    category?: { id: number; name: string } | null;
    teacher?: { name: string } | null;
    slug?: string;
    image?: string;
}

defineProps<{
    courses: CourseItem[];
    total: number;
    paginationLinks: PaginationLink[];
}>();

const emit = defineEmits<{
    (e: 'duplicate', course: CourseItem): void;
    (e: 'publish', course: CourseItem): void;
    (e: 'hide', course: CourseItem): void;
    (e: 'destroy', course: CourseItem): void;
}>();
</script>

<template>
    <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
        <!-- Table Header -->
        <div
            class="hidden grid-cols-12 gap-4 border-b border-slate-50 bg-slate-50/50 px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 lg:grid"
        >
            <div class="col-span-4">Programa Académico</div>
            <div class="col-span-2 text-center">Categoría</div>
            <div class="col-span-2 text-center">Inversión & Metodología</div>
            <div class="col-span-2 text-center">Estado de Visibilidad</div>
            <div class="col-span-2 text-right">Acciones</div>
        </div>

        <!-- Empty State -->
        <div v-if="!courses.length" class="flex flex-col items-center justify-center space-y-4 py-24 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-50 text-slate-200">
                <Search class="h-8 w-8" />
            </div>
            <div>
                <h4 class="font-serif text-xl text-slate-900">Sin registros académicos</h4>
                <p class="mx-auto max-w-xs text-sm text-slate-400">No se encontraron cursos que coincidan con los criterios de búsqueda actuales.</p>
            </div>
        </div>

        <!-- Table Rows -->
        <div v-else class="divide-y divide-slate-50">
            <div
                v-for="course in courses"
                :key="course.id"
                class="group grid grid-cols-1 items-center gap-4 px-8 py-6 transition-all duration-300 hover:bg-slate-50/80 lg:grid-cols-12"
            >
                <!-- INFOGRAFÍA (4cols) -->
                <div class="col-span-1 flex min-w-0 items-center gap-4 lg:col-span-4">
                    <div
                        class="relative h-16 w-24 flex-shrink-0 overflow-hidden rounded-2xl border border-slate-200/50 bg-slate-50 shadow-inner transition-all duration-300 group-hover:shadow-md"
                    >
                        <img
                            v-if="course.image"
                            :src="course.image"
                            class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center bg-slate-50">
                            <GraduationCap class="h-6 w-6 text-slate-300" />
                        </div>
                    </div>
                    <div class="min-w-0 pr-4">
                        <h4
                            class="truncate text-base font-bold leading-snug tracking-tight text-slate-900 transition-colors group-hover:text-primary"
                            :title="course.title"
                        >
                            {{ course.title }}
                        </h4>
                        <div class="mt-1 flex items-center gap-1.5 text-xs text-slate-400">
                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-slate-400"
                                >REF: #{{ String(course.id).padStart(4, '0') }}</span
                            >
                        </div>
                    </div>
                </div>

                <!-- CATEGORÍA (2cols) -->
                <div class="col-span-1 flex justify-center lg:col-span-2">
                    <span
                        class="inline-flex rounded-xl border border-slate-200/50 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm transition-colors group-hover:border-slate-300"
                    >
                        {{ course.category?.name || 'General' }}
                    </span>
                </div>

                <!-- PRECIO & FORMATO (2cols) -->
                <div class="col-span-1 flex flex-col items-center justify-center text-center lg:col-span-2">
                    <span class="mb-0.5 text-sm font-bold text-slate-900">
                        <template v-if="course.price > 0">S/ {{ course.price }}</template>
                        <template v-else><span class="font-medium italic tracking-tight text-primary">Acceso Gratuito</span></template>
                    </span>
                    <span class="flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <div class="h-1.5 w-1.5 rounded-full" :class="course.type === 'grabado' ? 'bg-amber-400' : 'bg-blue-400'"></div>
                        {{ course.type === 'grabado' ? 'Asincrónico' : course.type === 'en vivo' ? 'Sincrónico' : 'Masterclass' }}
                    </span>
                </div>

                <!-- ESTADO (2cols) -->
                <div class="col-span-1 flex justify-center lg:col-span-2">
                    <div
                        class="flex w-fit items-center gap-2 rounded-xl border bg-white px-3 py-1.5 shadow-sm transition-all duration-300"
                        :class="{
                            'border-emerald-200 bg-emerald-50/30 text-emerald-700': course.status === 'PUBLICADO',
                            'border-amber-200 bg-amber-50/30 text-amber-700': course.status === 'BORRADOR',
                            'border-rose-200 bg-rose-50/30 text-rose-700': course.status === 'OCULTO',
                        }"
                    >
                        <div class="relative flex h-2 w-2">
                            <span
                                v-if="course.status === 'PUBLICADO'"
                                class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"
                            ></span>
                            <span
                                class="relative inline-flex h-2 w-2 rounded-full"
                                :class="{
                                    'bg-emerald-500': course.status === 'PUBLICADO',
                                    'bg-amber-400': course.status === 'BORRADOR',
                                    'bg-rose-500': course.status === 'OCULTO',
                                    'bg-slate-300': !['PUBLICADO', 'BORRADOR', 'OCULTO'].includes(course.status),
                                }"
                            ></span>
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider">
                            {{ course.status }}
                        </span>
                    </div>
                </div>

                <!-- ACCIONES (2cols) -->
                <div class="col-span-1 flex flex-wrap justify-end gap-1.5 lg:col-span-2 lg:flex-nowrap">
                    <!-- Editar -->
                    <Link
                        :href="route('admin.courses.edit', course.id)"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-300 hover:bg-blue-50 hover:text-blue-600"
                        title="Editar Contenido"
                    >
                        <Pen class="h-4 w-4" />
                    </Link>

                    <!-- Certificado -->
                    <Link
                        :href="route('admin.courses.certificate-template.edit', { course: course.id })"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-300 hover:bg-indigo-50 hover:text-indigo-600"
                        title="Diseñar Certificado"
                    >
                        <FileBadge class="h-4 w-4" />
                    </Link>

                    <!-- Duplicar -->
                    <button
                        @click="emit('duplicate', course)"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-300 hover:bg-slate-100 hover:text-slate-700"
                        title="Duplicar Curso"
                    >
                        <CopyPlus class="h-4 w-4" />
                    </button>

                    <!-- Visibilidad: Publicar -->
                    <button
                        v-if="course.status !== 'PUBLICADO'"
                        @click="emit('publish', course)"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-300 hover:bg-emerald-50 hover:text-emerald-600"
                        title="Publicar Ahora"
                    >
                        <Eye class="h-4 w-4" />
                    </button>

                    <!-- Visibilidad: Ocultar -->
                    <button
                        v-else
                        @click="emit('hide', course)"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-300 hover:bg-amber-50 hover:text-amber-600"
                        title="Bajar de Cartelera"
                    >
                        <EyeOff class="h-4 w-4" />
                    </button>

                    <!-- Eliminar -->
                    <button
                        @click="emit('destroy', course)"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-300 hover:bg-rose-50 hover:text-rose-600"
                        title="Eliminar Registro"
                    >
                        <Trash2 class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Data Footer / Pagination -->
        <div
            v-if="paginationLinks.length > 1"
            class="flex flex-col items-center justify-between gap-4 border-t border-slate-50 bg-slate-50/50 px-8 py-6 sm:flex-row"
        >
            <p class="text-sm font-medium text-slate-500">
                Mostrando <span class="font-bold text-slate-900">{{ courses.length }}</span> de
                <span class="font-bold text-slate-900">{{ total }}</span> registros
            </p>
            <div class="flex items-center gap-1.5">
                <Link
                    v-for="link in paginationLinks"
                    :key="link.label"
                    :href="link.url || '#'"
                    class="flex h-10 min-w-[40px] items-center justify-center rounded-xl text-xs font-bold transition-all duration-300"
                    :class="
                        link.active
                            ? 'bg-primary text-white shadow-lg shadow-primary/20'
                            : 'border border-slate-200 bg-white text-slate-600 hover:border-primary hover:text-primary'
                    "
                    v-html="link.label"
                />
            </div>
        </div>
    </div>
</template>
