<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { 
    GraduationCap, Search, Pen, FileBadge, 
    CopyPlus, Eye, EyeOff, Trash2 
} from 'lucide-vue-next';
import { PaginationLink } from '@/types/pagination';

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
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <!-- Table Header -->
        <div class="hidden lg:grid grid-cols-12 gap-4 px-8 py-5 border-b border-slate-50 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 bg-slate-50/50">
            <div class="col-span-4">Programa Académico</div>
            <div class="col-span-2 text-center">Categoría</div>
            <div class="col-span-2 text-center">Inversión & Metodología</div>
            <div class="col-span-2 text-center">Estado de Visibilidad</div>
            <div class="col-span-2 text-right">Acciones</div>
        </div>

        <!-- Empty State -->
        <div v-if="!courses.length" class="flex flex-col items-center justify-center py-24 text-center space-y-4">
            <div class="w-16 h-16 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-200">
                <Search class="w-8 h-8" />
            </div>
            <div>
                <h4 class="text-xl font-serif text-slate-900">Sin registros académicos</h4>
                <p class="text-slate-400 text-sm max-w-xs mx-auto">No se encontraron cursos que coincidan con los criterios de búsqueda actuales.</p>
            </div>
        </div>

        <!-- Table Rows -->
        <div v-else class="divide-y divide-slate-50">
            <div
                v-for="course in courses"
                :key="course.id"
                class="group grid grid-cols-1 lg:grid-cols-12 items-center gap-4 px-8 py-6 hover:bg-slate-50/80 transition-all duration-300"
            >
                <!-- INFOGRAFÍA (4cols) -->
                <div class="col-span-1 lg:col-span-4 flex items-center gap-4 min-w-0">
                    <div class="h-16 w-24 rounded-2xl overflow-hidden bg-slate-50 border border-slate-200/50 flex-shrink-0 relative shadow-inner group-hover:shadow-md transition-all duration-300">
                        <img v-if="course.image" :src="course.image" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                        <div v-else class="w-full h-full bg-slate-50 flex items-center justify-center">
                            <GraduationCap class="w-6 h-6 text-slate-300" />
                        </div>
                    </div>
                    <div class="min-w-0 pr-4">
                        <h4 class="text-base font-bold text-slate-900 truncate tracking-tight group-hover:text-primary transition-colors leading-snug" :title="course.title">
                            {{ course.title }}
                        </h4>
                        <div class="flex items-center gap-1.5 text-xs text-slate-400 mt-1">
                            <span class="text-[10px] font-bold tracking-widest text-slate-400 uppercase bg-slate-100 px-2 py-0.5 rounded-md">REF: #{{ String(course.id).padStart(4, '0') }}</span>
                        </div>
                    </div>
                </div>

                <!-- CATEGORÍA (2cols) -->
                <div class="col-span-1 lg:col-span-2 flex justify-center">
                    <span class="inline-flex rounded-xl bg-slate-50 border border-slate-200/50 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-sm group-hover:border-slate-300 transition-colors">
                        {{ course.category?.name || 'General' }}
                    </span>
                </div>

                <!-- PRECIO & FORMATO (2cols) -->
                <div class="col-span-1 lg:col-span-2 flex flex-col items-center justify-center text-center">
                    <span class="font-bold text-slate-900 text-sm mb-0.5">
                        <template v-if="course.price > 0">S/ {{ course.price }}</template>
                        <template v-else><span class="text-primary italic tracking-tight font-medium">Acceso Gratuito</span></template>
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <div class="w-1.5 h-1.5 rounded-full" :class="course.type === 'grabado' ? 'bg-amber-400' : 'bg-blue-400'"></div>
                        {{ course.type === 'grabado' ? 'Asincrónico' : (course.type === 'en vivo' ? 'Sincrónico' : 'Masterclass') }}
                    </span>
                </div>

                <!-- ESTADO (2cols) -->
                <div class="col-span-1 lg:col-span-2 flex justify-center">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border shadow-sm w-fit transition-all duration-300"
                        :class="{
                            'border-emerald-200 bg-emerald-50/30 text-emerald-700': course.status === 'PUBLICADO',
                            'border-amber-200 bg-amber-50/30 text-amber-700': course.status === 'BORRADOR',
                            'border-rose-200 bg-rose-50/30 text-rose-700': course.status === 'OCULTO'
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
                        <span class="text-[10px] font-bold uppercase tracking-wider">
                            {{ course.status }}
                        </span>
                    </div>
                </div>

                <!-- ACCIONES (2cols) -->
                <div class="col-span-1 lg:col-span-2 flex justify-end gap-1.5 flex-wrap lg:flex-nowrap">
                    <!-- Editar -->
                    <Link :href="route('admin.courses.edit', course.id)" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300" title="Editar Contenido">
                        <Pen class="w-4 h-4" />
                    </Link>

                    <!-- Certificado -->
                    <Link :href="route('admin.courses.certificate-template.edit', { course: course.id })" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-300" title="Diseñar Certificado">
                        <FileBadge class="w-4 h-4" />
                    </Link>

                    <!-- Duplicar -->
                    <button @click="emit('duplicate', course)" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition-all duration-300" title="Duplicar Curso">
                        <CopyPlus class="w-4 h-4" />
                    </button>

                    <!-- Visibilidad: Publicar -->
                    <button v-if="course.status !== 'PUBLICADO'" @click="emit('publish', course)" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all duration-300" title="Publicar Ahora">
                        <Eye class="w-4 h-4" />
                    </button>
                    
                    <!-- Visibilidad: Ocultar -->
                    <button v-else @click="emit('hide', course)" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all duration-300" title="Bajar de Cartelera">
                        <EyeOff class="w-4 h-4" />
                    </button>

                    <!-- Eliminar -->
                    <button @click="emit('destroy', course)" class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all duration-300" title="Eliminar Registro">
                        <Trash2 class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Data Footer / Pagination -->
        <div v-if="paginationLinks.length > 1" class="px-8 py-6 bg-slate-50/50 border-t border-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm font-medium text-slate-500">
                Mostrando <span class="text-slate-900 font-bold">{{ courses.length }}</span> de <span class="text-slate-900 font-bold">{{ total }}</span> registros
            </p>
            <div class="flex items-center gap-1.5">
                <Link
                    v-for="link in paginationLinks"
                    :key="link.label"
                    :href="link.url || '#'"
                    class="min-w-[40px] h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all duration-300"
                    :class="link.active
                        ? 'bg-primary text-white shadow-lg shadow-primary/20'
                        : 'bg-white border border-slate-200 text-slate-600 hover:border-primary hover:text-primary'"
                    v-html="link.label"
                />
            </div>
        </div>
    </div>
</template>
