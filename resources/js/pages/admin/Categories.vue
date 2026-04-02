<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import {
    Plus, Search, Edit2, Trash2, X, Check,
    AlertCircle, Tag, Layers, Calendar, ArrowRight
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
        month: 'short',
        year: 'numeric'
    });
}

const paginationLinks = computed(() => props.categories.links?.filter((l: any) => l.url) ?? []);
</script>

<template>
    <Head title="Categorías - Admin IEE" />

    <AppLayout>
        <!-- ── Header ─────────────────────────────────────────── -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="font-serif text-4xl text-on-surface">
                    Gestión de <span class="italic text-[#57572A]">Categorías</span>
                </h1>
                <p class="mt-1 text-sm text-on-surface-variant">Organiza tus cursos mediante categorías temáticas.</p>
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center gap-2 rounded-xl bg-[#57572A] px-5 py-2.5 text-sm font-bold text-white shadow hover:opacity-95 transition-all active:scale-95"
            >
                <Plus class="h-4 w-4" />
                Nueva categoría
            </button>
        </div>

        <!-- ── Stats ──────────────────────────────────────────── -->
        <div class="mb-8 flex gap-4">
            <div class="flex-1 rounded-2xl border border-outline-variant/10 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#57572A]/10 text-[#57572A]">
                        <Layers class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Total Categorías</p>
                        <p class="text-2xl font-bold text-on-surface">{{ props.categories.total }}</p>
                    </div>
                </div>
            </div>
            <!-- Dynamic placeholder stat -->
            <div class="flex-1 rounded-2xl border border-outline-variant/10 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <Tag class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Categorías Activas</p>
                        <p class="text-2xl font-bold text-on-surface">{{ props.categories.data.length }} en esta página</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Filters ────────────────────────────────────────── -->
        <div class="mb-6 rounded-2xl border border-outline-variant/10 bg-white p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="flex flex-1 items-center gap-2 rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-3 py-2.5">
                    <Search class="h-4 w-4 flex-shrink-0 text-on-surface-variant" />
                    <input
                        v-model="searchInput"
                        placeholder="Buscar categorías por nombre..."
                        class="w-full bg-transparent text-sm outline-none placeholder:text-on-surface-variant/60"
                    />
                </div>
                <div class="flex gap-2">
                    <select
                        v-model="perPage"
                        class="rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors"
                    >
                        <option value="10">10 por página</option>
                        <option value="20">20 por página</option>
                        <option value="50">50 por página</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ── Table ──────────────────────────────────────────── -->
        <div class="overflow-hidden rounded-3xl border border-outline-variant/10 bg-white shadow-sm">
            <div class="grid grid-cols-12 gap-2 bg-surface-container-low px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/70">
                <div class="col-span-4">Nombre / Slug</div>
                <div class="col-span-3 text-center">Cursos asociados</div>
                <div class="col-span-3">Fecha creación</div>
                <div class="col-span-2 text-right">Acciones</div>
            </div>

            <div v-if="!props.categories.data.length" class="flex flex-col items-center py-20 text-on-surface-variant">
                <Layers class="mb-4 h-12 w-12 opacity-20" />
                <p class="text-sm">No se encontraron categorías.</p>
            </div>

            <div v-else>
                <div
                    v-for="cat in props.categories.data"
                    :key="cat.id"
                    class="grid grid-cols-12 items-center gap-2 border-t border-outline-variant/10 px-6 py-5 hover:bg-surface-container-lowest transition-colors"
                >
                    <div class="col-span-4">
                        <p class="text-sm font-bold text-on-surface">{{ cat.name }}</p>
                        <p class="text-xs text-on-surface-variant/70 font-mono">{{ cat.slug }}</p>
                    </div>

                    <div class="col-span-3 flex justify-center">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-container-low px-3 py-1 text-xs font-bold text-on-surface">
                            <ArrowRight class="h-3 w-3 text-[#57572A]" />
                            {{ cat.courses_count }} cursos
                        </span>
                    </div>

                    <div class="col-span-3 text-sm text-on-surface-variant">
                        <span class="flex items-center gap-2"><Calendar class="h-3.5 w-3.5 opacity-40" /> {{ formatDate(cat.created_at) }}</span>
                    </div>

                    <div class="col-span-2 flex items-center justify-end gap-1">
                        <button
                            @click="openEdit(cat)"
                            class="rounded-lg p-2 text-on-surface-variant hover:bg-slate-50 hover:text-blue-600 transition-colors"
                            title="Editar"
                        >
                            <Edit2 class="h-4 w-4" />
                        </button>
                        <button
                            @click="deleteCategory(cat)"
                            class="rounded-lg p-2 text-on-surface-variant hover:bg-red-50 hover:text-red-600 transition-colors"
                            title="Eliminar"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Pagination ─────────────────────────────────────── -->
        <div v-if="paginationLinks.length > 1" class="mt-4 flex items-center justify-between text-sm text-on-surface-variant">
            <p>Mostrando {{ props.categories.data.length }} de {{ props.categories.total }} registros</p>
            <div class="flex gap-1.5">
                <Link
                    v-for="link in props.categories.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    class="flex h-9 min-w-[36px] items-center justify-center rounded-xl border px-2 text-xs font-semibold transition-all"
                    :class="[
                        link.active ? 'border-[#57572A] bg-[#57572A] text-white' : 'border-outline-variant/20 bg-white text-on-surface hover:bg-surface-container-low',
                        !link.url ? 'pointer-events-none opacity-40' : ''
                    ]"
                    v-html="link.label"
                />
            </div>
        </div>

        <!-- ──────────────── MODAL CREATE/EDIT ──────────────────── -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm" @click.self="showModal = false">
                    <div class="w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl transition-all">
                        <div class="flex items-center justify-between border-b border-outline-variant/10 px-6 py-5">
                            <div>
                                <h2 class="font-serif text-2xl text-on-surface">
                                    {{ editingCategory ? 'Editar' : 'Nueva' }} <span class="italic">Categoría</span>
                                </h2>
                                <p class="text-xs text-on-surface-variant">Los cambios se aplican globalmente a los cursos.</p>
                            </div>
                            <button @click="showModal = false" class="rounded-xl p-2 hover:bg-surface-container-low transition-colors">
                                <X class="h-5 w-5 text-on-surface-variant" />
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="p-6">
                            <div class="space-y-5">
                                <div>
                                    <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Nombre de la categoría *</label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Ej. Inteligencia Artificial"
                                        class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm outline-none focus:border-[#57572A] transition-colors"
                                        :class="{ 'border-red-500': form.errors.name }"
                                        required
                                    />
                                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                                    <p v-else class="mt-1.5 text-[10px] text-on-surface-variant/60">
                                        El slug se generará automáticamente a partir del nombre.
                                    </p>
                                </div>

                                <div v-if="!editingCategory" class="rounded-xl bg-amber-50 p-4 text-xs text-amber-700">
                                    <div class="flex gap-2">
                                        <AlertCircle class="h-4 w-4 flex-shrink-0" />
                                        <p>Asegúrate de que el nombre sea único para evitar confusiones en el catálogo.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end gap-3">
                                <button
                                    type="button"
                                    @click="showModal = false"
                                    class="rounded-xl px-5 py-2.5 text-sm font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.name"
                                    class="inline-flex items-center gap-2 rounded-xl bg-[#57572A] px-6 py-2.5 text-sm font-bold text-white shadow hover:opacity-95 disabled:opacity-50 transition-all active:scale-95"
                                >
                                    <Check v-if="!form.processing" class="h-4 w-4" />
                                    {{ editingCategory ? 'Guardar cambios' : 'Crear categoría' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Flash messages integration -->
        <Transition enter-active-class="transition duration-300 transform" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100">
            <div v-if="flash.success || flash.error" class="fixed bottom-6 right-6 z-50">
                <div v-if="flash.success" class="flex items-center gap-3 rounded-2xl bg-emerald-600 px-5 py-3 text-white shadow-2xl">
                    <Check class="h-5 w-5" />
                    <span class="text-sm font-semibold">{{ flash.success }}</span>
                </div>
                <div v-if="flash.error" class="flex items-center gap-3 rounded-2xl bg-red-600 px-5 py-3 text-white shadow-2xl">
                    <AlertCircle class="h-5 w-5" />
                    <span class="text-sm font-semibold">{{ flash.error }}</span>
                </div>
            </div>
        </Transition>

    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
