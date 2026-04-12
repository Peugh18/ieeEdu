<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import {
    Plus, Search, Edit2, Trash2, X, Check,
    AlertCircle, Tag, Layers, Calendar, ArrowRight,
    ChevronRight, FolderOpen, MoreVertical, LayoutGrid,
    Target, BookOpen, Activity, Filter, BookMarked
} from 'lucide-vue-next';
import { ref, computed, watch } from 'vue';

interface Category {
    id: number;
    name: string;
    slug: string;
    courses_count: number;
    created_at: string;
}

const props = defineProps<{
    categories: { data: Category[]; links: any[]; total: number; per_page: number };
    filters: { search?: string; per_page?: string };
}>();

// ─── Filters ────────────────────────────────────────────────────
const searchInput = ref(props.filters.search || '');
const perPage = ref(props.filters.per_page || '20');

function applyFilters() {
    router.get(route('admin.categories.index'), {
        search: searchInput.value || undefined,
        per_page: perPage.value !== '20' ? perPage.value : undefined,
    }, { preserveState: true, replace: true });
}

let timer: any;
watch(searchInput, () => {
    clearTimeout(timer);
    timer = setTimeout(applyFilters, 400);
});
watch(perPage, applyFilters);

// ─── Create / Edit Modal ────────────────────────────────────────
const showModal = ref(false);
const editingCategory = ref<Category | null>(null);

const form = useForm({
    name: '',
});

function openCreate() {
    editingCategory.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}

function openEdit(category: Category) {
    editingCategory.value = category;
    form.name = category.name;
    form.clearErrors();
    showModal.value = true;
}

function submit() {
    if (editingCategory.value) {
        form.put(route('admin.categories.update', { category: editingCategory.value.id }), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('admin.categories.store'), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            }
        });
    }
}

const page = usePage();
const flash = computed(() => (page.props.flash as { success?: string; error?: string }) || {});

// ─── Delete ─────────────────────────────────────────────────────
function deleteCategory(category: Category) {
    if (!confirm(`¿Estás seguro de eliminar la categoría "${category.name}"?`)) return;
    
    router.delete(route('admin.categories.destroy', { category: category.id }), {
        onBefore: () => {
            if (category.courses_count > 0) {
                alert('Esta categoría tiene cursos asignados y no se puede eliminar.');
                return false;
            }
        }
    });
}

// ─── Helpers ─────────────────────────────────────────────────────
function formatDate(date: string) {
    return new Date(date).toLocaleDateString('es-PE', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
}

const paginationLinks = computed(() => props.categories.links?.filter((l: any) => l.url) ?? []);
const initials = (n: string) => n.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
const avatarColors = ['bg-indigo-50 text-indigo-700', 'bg-blue-50 text-blue-700', 'bg-emerald-50 text-emerald-700', 'bg-amber-50 text-amber-700', 'bg-rose-50 text-rose-700'];
const getACls = (id: number) => avatarColors[id % avatarColors.length];
</script>

<template>
    <Head title="Categorías - Admin IEE" />

    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 py-8 space-y-10">
            <!-- ── Header ─────────────────────────────────────────── -->
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">
                        <span>Contenido</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-900">Taxonomía</span>
                    </div>
                    <h1 class="font-serif text-5xl text-slate-900 leading-tight">Gestión de <span class="italic">Categorías</span></h1>
                    <p class="text-sm text-slate-500 font-medium">Organiza y segmenta tu catálogo académico por áreas temáticas.</p>
                </div>
                <button
                    @click="openCreate"
                    class="h-14 inline-flex items-center justify-center gap-3 rounded-2xl bg-[#57572A] px-8 text-sm font-bold text-white shadow-xl shadow-[#57572A]/20 hover:scale-[1.02] active:scale-95 transition-all"
                >
                    <Plus class="h-5 w-5" />
                    Nueva Categoría
                </button>
            </div>

            <!-- ── Stats Grid ──────────────────────────────────────── -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="group relative overflow-hidden rounded-[2rem] bg-white p-6 border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-200">
                    <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Infraestructura</span>
                            <Layers class="h-4 w-4 text-[#57572A]/40" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Total Registradas</p>
                            <p class="text-4xl font-black text-slate-900 mt-1">{{ props.categories.total }}</p>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-20 h-20 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity">
                        <Layers class="w-full h-full" />
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-[2rem] bg-white p-6 border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-md hover:border-slate-200">
                    <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Visualización</span>
                            <BookOpen class="h-4 w-4 text-emerald-500/40" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">En esta vista</p>
                            <p class="text-4xl font-black text-emerald-600 mt-1">{{ props.categories.data.length }}</p>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-20 h-20 opacity-[0.03] group-hover:opacity-[0.08] transition-opacity text-emerald-500">
                        <BookOpen class="w-full h-full" />
                    </div>
                </div>

                <div class="group relative overflow-hidden rounded-[2rem] bg-[#57572A] p-6 shadow-xl shadow-[#57572A]/10 transition-all duration-300 hover:scale-[1.01]">
                    <div class="relative z-10 flex flex-col justify-between h-full space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#C9C7B8]">Actividad</span>
                            <Activity class="h-4 w-4 text-white/40" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-[#C9C7B8] uppercase tracking-widest leading-none">Estado de Red</p>
                            <p class="text-2xl font-black text-white mt-1 italic leading-tight">Canal Optimizando</p>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-24 h-24 opacity-[0.08] text-white">
                        <Target class="w-full h-full" />
                    </div>
                </div>
            </div>

            <!-- ── Filters ────────────────────────────────────────── -->
            <div class="bg-slate-50 rounded-[2.5rem] p-3 border border-slate-100 flex flex-col lg:flex-row items-center gap-3">
                <div class="relative flex-1 w-full lg:w-auto">
                    <Search class="absolute left-5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                    <input 
                        v-model="searchInput" 
                        placeholder="Filtrar por nombre de categoría..." 
                        class="w-full h-14 bg-white border border-slate-200 rounded-2xl pl-12 pr-6 text-sm font-medium outline-none focus:border-[#57572A] focus:ring-4 focus:ring-[#57572A]/5 transition-all" 
                    />
                </div>
                <div class="flex items-center gap-3 w-full lg:w-auto">
                    <div class="relative flex-1 lg:flex-none min-w-[160px]">
                        <Filter class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                        <select 
                            v-model="perPage" 
                            class="w-full h-14 bg-white border border-slate-200 rounded-2xl pl-11 pr-10 text-xs font-bold text-slate-600 appearance-none outline-none cursor-pointer hover:border-slate-300 transition-colors"
                        >
                            <option value="10">10 por página</option>
                            <option value="20">20 por página</option>
                            <option value="50">50 por página</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ── Table ──────────────────────────────────────────── -->
            <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden relative">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/80 border-b border-slate-100">
                        <tr>
                            <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Identidad / Slug</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Cursos Vinculados</th>
                            <th class="px-6 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-center">Creación</th>
                            <th class="px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-if="!props.categories.data.length" class="bg-white">
                            <td colspan="4" class="py-24 text-center">
                                <div class="flex flex-col items-center gap-2 opacity-20">
                                    <FolderOpen class="w-12 h-12" />
                                    <p class="text-sm font-bold uppercase tracking-widest">Sin resultados</p>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="cat in props.categories.data" :key="cat.id" class="group hover:bg-slate-50/50 transition-all duration-300">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div :class="`h-12 w-12 flex-shrink-0 flex items-center justify-center rounded-2xl text-xs font-bold ${getACls(cat.id)} shadow-sm shadow-slate-200 group-hover:scale-95 transition-transform duration-500`" >
                                        {{ initials(cat.name) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-900 leading-tight group-hover:text-[#57572A] transition-colors line-clamp-1">{{ cat.name }}</p>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5 uppercase tracking-wider line-clamp-1">{{ cat.slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex justify-center">
                                    <span class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold tracking-tight bg-slate-50 text-slate-700 ring-1 ring-inset ring-slate-200/50">
                                        <BookMarked class="h-3.5 w-3.5 text-[#57572A]" />
                                        {{ cat.courses_count }} <span class="font-medium text-slate-400 ml-0.5">cursos</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs font-bold text-slate-500">{{ formatDate(cat.created_at) }}</span>
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <Calendar class="h-2.5 w-2.5 text-slate-300" />
                                        <span class="text-[9px] text-slate-300 font-medium uppercase tracking-widest">Registro Base</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center justify-end gap-1.5 opacity-40 group-hover:opacity-100 transition-all duration-300">
                                    <button 
                                        @click="openEdit(cat)" 
                                        class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-[#57572A] hover:text-[#57572A] hover:bg-slate-50 transition-all"
                                        title="Editar Categoría"
                                    >
                                        <Edit2 class="h-4 w-4" />
                                    </button>
                                    <button 
                                        @click="deleteCategory(cat)" 
                                        class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:border-rose-500 hover:text-rose-600 hover:bg-rose-50 transition-all"
                                        title="Eliminar Permanente"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- ── Pagination ─────────────────────────────────────── -->
            <div v-if="paginationLinks.length > 1" class="flex flex-col md:flex-row items-center justify-between gap-4 px-2">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest italic">Mostrando {{ props.categories.data.length }} de {{ props.categories.total }} entradas</p>
                <div class="flex items-center gap-1.5">
                    <Link
                        v-for="link in props.categories.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="h-10 min-w-[2.5rem] flex items-center justify-center rounded-xl px-3 text-[10px] font-black tracking-widest transition-all"
                        :class="[
                            link.active ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-400 border border-slate-100 hover:border-slate-300 hover:text-slate-600',
                            !link.url ? 'pointer-events-none opacity-40' : ''
                        ]"
                        v-html="link.label"
                    />
                </div>
            </div>
        </div>

        <!-- ──────────────── MODAL CREATE/EDIT ──────────────────── -->
        <Teleport to="body">
            <Transition name="modal-bounce">
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" @click.self="showModal = false">
                    <div class="w-full max-w-md rounded-[2.5rem] bg-white shadow-2xl overflow-hidden flex flex-col transition-all">
                        <div class="bg-[#57572A] p-8 text-white relative">
                            <div class="absolute top-0 right-0 p-8 opacity-10"><Tag class="w-24 h-24" /></div>
                            <h2 class="font-serif text-3xl">
                                {{ editingCategory ? 'Editar' : 'Nueva' }} <span class="italic underline decoration-white/20 underline-offset-8">Categoría</span>
                            </h2>
                            <p class="mt-3 text-[#C9C7B8] text-xs font-medium uppercase tracking-widest">Organización de catálogo académico</p>
                            <button @click="showModal = false" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                                <X class="h-5 w-5" />
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="p-8 space-y-6">
                            <div class="space-y-2">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Título de la Categoría *</label>
                                <div class="relative group">
                                    <Tag class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-300 transition-colors group-focus-within:text-[#57572A]" />
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Ej. Ingeniería Civil o Arquitectura"
                                        class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-6 text-sm font-bold text-slate-700 outline-none focus:border-[#57572A] transition-all"
                                        :class="{ 'border-rose-500 bg-rose-50/10': form.errors.name }"
                                        required
                                    />
                                </div>
                                <p v-if="form.errors.name" class="mt-1.5 px-1 text-[10px] font-bold text-rose-500 uppercase tracking-widest">{{ form.errors.name }}</p>
                                <p v-else class="px-1 text-[9px] text-slate-400 italic font-medium">El identificador (slug) se generará al guardar.</p>
                            </div>

                            <div v-if="!editingCategory" class="rounded-2xl bg-[#57572A]/5 p-5 border border-[#57572A]/10 flex gap-3">
                                <AlertCircle class="h-5 w-5 text-[#57572A] flex-shrink-0" />
                                <p class="text-xs text-slate-600 font-medium leading-relaxed">Las categorías ayudan a los estudiantes a filtrar cursos más rápido en el panel principal.</p>
                            </div>

                            <div class="flex justify-end gap-3 pt-4">
                                <button
                                    type="button"
                                    @click="showModal = false"
                                    class="h-12 px-6 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.name"
                                    class="h-12 px-10 rounded-2xl bg-[#57572A] text-sm font-black text-white shadow-xl shadow-[#57572A]/20 hover:scale-[1.02] active:scale-95 disabled:opacity-50 transition-all flex items-center justify-center gap-2"
                                >
                                    <Check v-if="!form.processing" class="h-4 w-4" />
                                    {{ editingCategory ? 'Guardar Cambios' : 'Generar Categoría' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Flash messages -->
        <Transition enter-active-class="transition duration-500" enter-from-class="translate-y-full opacity-0" enter-to-class="translate-y-0 opacity-100">
            <div v-if="flash.success || flash.error" class="fixed bottom-10 right-10 z-[100] flex items-center gap-4 rounded-3xl bg-slate-900 p-2 pr-6 text-white shadow-2xl">
                <div :class="`w-10 h-10 rounded-full flex items-center justify-center ${flash.success ? 'bg-emerald-500' : 'bg-rose-500'}`">
                    <Check v-if="flash.success" class="h-6 w-6 text-white" />
                    <AlertCircle v-else class="h-6 w-6 text-white" />
                </div>
                <div class="flex flex-col">
                    <span :class="`text-[10px] uppercase font-bold tracking-widest ${flash.success ? 'text-emerald-500' : 'text-rose-500'}`">
                        {{ flash.success ? 'Éxito' : 'Error en sistema' }}
                    </span>
                    <span class="text-sm font-medium">{{ flash.success || flash.error }}</span>
                </div>
            </div>
        </Transition>

    </AppLayout>
</template>

<style scoped>
.modal-bounce-enter-active { animation: modal-bounce-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-bounce-leave-active { animation: modal-bounce-in 0.3s reverse ease-in; }

@keyframes modal-bounce-in {
    0% { transform: scale(0.9); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1.2rem center;
    background-size: 1rem;
}
</style>
