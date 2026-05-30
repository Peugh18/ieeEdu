<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { 
    GraduationCap, Search, Pen, FileBadge, 
    CopyPlus, Eye, EyeOff, Trash2 
} from 'lucide-vue-next';

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
}>();

const emit = defineEmits<{
    (e: 'duplicate', course: CourseItem): void;
    (e: 'publish', course: CourseItem): void;
    (e: 'hide', course: CourseItem): void;
    (e: 'destroy', course: CourseItem): void;
}>();
</script>

<template>
    <div class="rounded-[3rem] border border-outline-variant/10 bg-white overflow-hidden shadow-2xl shadow-surface-tint/5 relative z-0">
        <div class="custom-scrollbar overflow-x-auto">
            <div>
                <!-- Table Header -->
                <div class="hidden lg:grid grid-cols-12 gap-6 px-12 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-primary/60 bg-surface-container-highest/40 border-b border-outline-variant/10">
                    <div class="col-span-4">Programa Académico</div>
                    <div class="col-span-2">Categoría</div>
                    <div class="col-span-2">Inversión & Metodología</div>
                    <div class="col-span-2 text-center">Estado de Visibilidad</div>
                    <div class="col-span-2 text-right">Acciones de Gestión</div>
                </div>

                <div v-if="!courses.length" class="p-24 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 rounded-full bg-surface-container-highest flex items-center justify-center text-primary/20 mb-6">
                        <Search class="w-8 h-8" />
                    </div>
                    <h3 class="font-serif font-bold text-on-surface text-xl">Sin registros académicos</h3>
                    <p class="text-sm text-on-surface-variant mt-2 max-w-xs">No se encontraron cursos que coincidan con los criterios de búsqueda actuales.</p>
                </div>

                <!-- Table Rows -->
                <div v-else class="divide-y divide-outline-variant/5">
                    <div
                        v-for="course in courses"
                        :key="course.id"
                        class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center px-6 lg:px-12 py-7 hover:bg-primary/[0.02] transition-colors duration-500 group"
                    >
                        <!-- INFOGRAFÍA (4cols) -->
                        <div class="col-span-1 lg:col-span-4 flex items-center gap-6 min-w-0">
                            <div class="h-20 w-28 rounded-3xl overflow-hidden bg-surface-container-low border border-outline-variant/10 flex-shrink-0 relative shadow-inner group-hover:shadow-lg transition-all duration-500">
                                <img v-if="course.image" :src="course.image" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
                                <div v-else class="w-full h-full bg-surface-container-highest flex items-center justify-center">
                                    <GraduationCap class="w-8 h-8 text-primary/10" />
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            </div>
                            <div class="min-w-0 pr-4">
                                <p class="font-serif font-bold text-[16px] text-on-surface truncate group-hover:text-primary transition-colors leading-snug mb-1.5" :title="course.title">
                                    {{ course.title }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black tracking-widest text-primary/40 uppercase bg-primary/5 px-2 py-0.5 rounded-md">REF: #{{ String(course.id).padStart(4, '0') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- CATEGORÍA (2cols) -->
                        <div class="col-span-1 lg:col-span-2 flex lg:block justify-center lg:justify-start">
                            <span class="inline-flex rounded-xl bg-white px-4 py-2 text-[11px] font-black uppercase tracking-widest text-primary border border-outline-variant/20 shadow-sm group-hover:border-primary/30 transition-colors">
                                {{ course.category?.name || 'General' }}
                            </span>
                        </div>

                        <!-- PRECIO & FORMATO (2cols) -->
                        <div class="col-span-1 lg:col-span-2 flex flex-col items-center lg:items-start justify-center">
                            <span class="font-serif font-black text-[17px] text-on-surface mb-0.5">
                                <template v-if="course.price > 0">S/ {{ course.price }}</template>
                                <template v-else><span class="text-primary italic tracking-tight">Acceso Gratuito</span></template>
                            </span>
                            <span class="text-[11px] font-bold tracking-wider text-on-surface-variant flex items-center gap-1.5">
                                <div class="w-1.5 h-1.5 rounded-full" :class="course.type === 'grabado' ? 'bg-amber-400' : 'bg-blue-400'"></div>
                                {{ course.type === 'grabado' ? 'Asincrónico' : (course.type === 'en vivo' ? 'Sincrónico' : 'Masterclass') }}
                            </span>
                        </div>

                        <!-- ESTADO (2cols) -->
                        <div class="col-span-1 lg:col-span-2 flex justify-center">
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
                        <div class="col-span-1 lg:col-span-2 flex items-center justify-center lg:justify-end">
                            <!-- Ghost floating action bar -->
                            <div class="flex items-center bg-surface-container-highest/30 p-2 rounded-2xl border border-outline-variant/20 lg:border-transparent group-hover:border-outline-variant/20 group-hover:bg-white group-hover:shadow-xl transition-all duration-700 opacity-100 lg:opacity-40 group-hover:opacity-100 transform lg:group-hover:-translate-x-2">
                                
                                <!-- Editar -->
                                <Link :href="route('admin.courses.edit', course.id)" class="p-3 rounded-xl hover:bg-blue-50 text-on-surface-variant hover:text-blue-600 transition-all hover:scale-110" title="Editar Contenido">
                                    <Pen class="w-4 h-4" />
                                </Link>

                                <!-- Certificado -->
                                <Link :href="route('admin.courses.certificate-template.edit', { course: course.id })" class="p-3 rounded-xl hover:bg-primary/5 text-on-surface-variant hover:text-primary transition-all hover:scale-110" title="Diseñar Certificado">
                                    <FileBadge class="w-4 h-4" />
                                </Link>

                                <!-- Duplicar -->
                                <button @click="emit('duplicate', course)" class="p-3 rounded-xl hover:bg-primary/5 text-on-surface-variant hover:text-primary transition-all hover:scale-110" title="Clonación Instantánea">
                                    <CopyPlus class="w-4 h-4" />
                                </button>

                                <div class="w-px h-6 bg-outline-variant/15 mx-1.5"></div>

                                <!-- Visibilidad: Publicar -->
                                <button v-if="course.status !== 'PUBLICADO'" @click="emit('publish', course)" class="p-3 rounded-xl hover:bg-emerald-50 text-on-surface-variant hover:text-emerald-600 transition-all hover:scale-110" title="Publicar Ahora">
                                    <Eye class="w-4 h-4" />
                                </button>
                                
                                <!-- Visibilidad: Ocultar -->
                                <button v-if="course.status === 'PUBLICADO'" @click="emit('hide', course)" class="p-3 rounded-xl hover:bg-amber-50 text-on-surface-variant hover:text-amber-600 transition-all hover:scale-110" title="Bajar de Cartelera">
                                    <EyeOff class="w-4 h-4" />
                                </button>

                                <!-- Eliminar -->
                                <button @click="emit('destroy', course)" class="p-3 rounded-xl hover:bg-rose-50 text-on-surface-variant hover:text-rose-600 transition-all hover:scale-110" title="Eliminar Registro">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
