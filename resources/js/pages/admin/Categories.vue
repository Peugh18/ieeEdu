<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import CategoryStats from '@/components/admin/categories/CategoryStats.vue';
import CategoriesTable from '@/components/admin/categories/CategoriesTable.vue';
import CategoryFormModal from '@/components/admin/categories/CategoryFormModal.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Check, AlertCircle, Search, Filter } from 'lucide-vue-next';
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import type { Category } from '@/types/category';
import type { PaginatedResponse } from '@/types/pagination';
import type { SharedData } from '@/types';

const props = defineProps<{
    categories: PaginatedResponse<Category>;
    filters: { search?: string; per_page?: string };
}>();

const page = usePage<SharedData>();
const flash = computed(() => page.props.flash ?? {});

const filterForm = useForm({
    search: props.filters.search || '',
    per_page: props.filters.per_page || '20',
});

function applyFilters() {
    router.get(route('admin.categories.index'), {
        search: filterForm.search || undefined,
        per_page: filterForm.per_page !== '20' ? filterForm.per_page : undefined,
    }, { preserveState: false, replace: true });
}

useDebouncedInertiaFilters(filterForm, applyFilters);

const showModal = ref(false);
const editingCategory = ref<Category | null>(null);

const form = useForm({ name: '' });

function openCreate() {
    editingCategory.value = null;
    form.reset(); form.clearErrors();
    showModal.value = true;
}

function openEdit(category: Category) {
    editingCategory.value = category;
    form.name = category.name;
    form.clearErrors();
    showModal.value = true;
}

function submit() {
    const onSuccess = () => { showModal.value = false; form.reset(); };
    if (editingCategory.value) {
        form.put(route('admin.categories.update', { category: editingCategory.value.id }), { onSuccess });
    } else {
        form.post(route('admin.categories.store'), { onSuccess });
    }
}

function destroyCategory(category: Category) {
    if (!confirm(`¿Estás seguro de eliminar la categoría "${category.name}"?`)) return;
    router.delete(route('admin.categories.destroy', { category: category.id }), {
        onBefore: () => {
            if (category.courses_count > 0) {
                alert('Esta categoría tiene cursos asignados y no se puede eliminar.');
                return false;
            }
        },
    });
}

const paginationLinks = usePaginationLinks(props.categories.links);
</script>

<template>
    <Head title="Categorías - Admin IEE" />
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 py-8 space-y-10">
            <AdminPageHeader title="Categorías" subtitle="Clasificación del catálogo de cursos." action-label="Nueva categoría" action-order="first" compact @action="openCreate" />

            <CategoryStats :total="categories.total" :visible="categories.data.length" />

            <div class="bg-slate-50 rounded-[2.5rem] p-3 border border-slate-100 flex flex-col lg:flex-row items-center gap-3">
                <div class="relative flex-1 w-full lg:w-auto">
                    <Search class="absolute left-5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                    <input v-model="filterForm.search" placeholder="Filtrar por nombre de categoría..." class="w-full h-14 bg-white border border-slate-200 rounded-2xl pl-12 pr-6 text-sm font-medium outline-none focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all" />
                </div>
                <div class="relative flex-1 lg:flex-none min-w-[160px] w-full lg:w-auto">
                    <Filter class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                    <select v-model="filterForm.per_page" class="w-full h-14 bg-white border border-slate-200 rounded-2xl pl-11 pr-10 text-xs font-bold text-slate-600 appearance-none outline-none cursor-pointer hover:border-slate-300 transition-colors">
                        <option value="10">10 por página</option>
                        <option value="20">20 por página</option>
                        <option value="50">50 por página</option>
                    </select>
                </div>
            </div>

            <CategoriesTable :categories="categories.data" @edit="openEdit" @destroy="destroyCategory" />
            <AdminPagination :links="paginationLinks" label="Navegación de Categorías" />
        </div>

        <CategoryFormModal :show="showModal" :form="form" :editing-id="editingCategory?.id ?? null" @close="showModal = false" @submit="submit" />

        <Transition enter-active-class="transition duration-500" enter-from-class="translate-y-full opacity-0" enter-to-class="translate-y-0 opacity-100">
            <div v-if="flash.success || flash.error" class="fixed bottom-10 right-10 z-[100] flex items-center gap-4 rounded-3xl bg-slate-900 p-2 pr-6 text-white shadow-2xl">
                <div :class="`w-10 h-10 rounded-full flex items-center justify-center ${flash.success ? 'bg-emerald-500' : 'bg-rose-500'}`">
                    <Check v-if="flash.success" class="h-6 w-6 text-white" />
                    <AlertCircle v-else class="h-6 w-6 text-white" />
                </div>
                <div class="flex flex-col">
                    <span :class="`text-[10px] uppercase font-bold tracking-widest ${flash.success ? 'text-emerald-500' : 'text-rose-500'}`">{{ flash.success ? 'Éxito' : 'Error en sistema' }}</span>
                    <span class="text-sm font-medium">{{ flash.success || flash.error }}</span>
                </div>
            </div>
        </Transition>
    </AppLayout>
</template>

<style scoped>
select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1.2rem center;
    background-size: 1rem;
}
</style>
