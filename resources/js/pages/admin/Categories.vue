<script setup lang="ts">
import AdminFlashToast from '@/components/admin/AdminFlashToast.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import CategoriesTable from '@/components/admin/categories/CategoriesTable.vue';
import CategoryFormModal from '@/components/admin/categories/CategoryFormModal.vue';
import CategoryStats from '@/components/admin/categories/CategoryStats.vue';
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import AppLayout from '@/layouts/AppLayout.vue';
import type { SharedData } from '@/types';
import type { Category } from '@/types/category';
import type { PaginatedResponse } from '@/types/pagination';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Filter, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppSelect from '@/components/ui/AppSelect.vue';

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
    router.get(
        route('admin.categories.index'),
        {
            search: filterForm.search || undefined,
            per_page: filterForm.per_page !== '20' ? filterForm.per_page : undefined,
        },
        { preserveState: false, replace: true },
    );
}

useDebouncedInertiaFilters(filterForm, applyFilters);

const showModal = ref(false);
const editingCategory = ref<Category | null>(null);

const form = useForm({ name: '' });

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
    const onSuccess = () => {
        showModal.value = false;
        form.reset();
    };
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
        <div class="w-full space-y-8 px-6 py-8 lg:px-10">
            <AdminPageHeader
                title="Categorías"
                subtitle="Clasificación del catálogo de cursos."
                action-label="Nueva categoría"
                action-order="first"
                compact
                @action="openCreate"
            />

            <CategoryStats :total="categories.total" :visible="categories.data.length" />

            <div class="flex flex-col items-center gap-3 rounded-[2.5rem] border border-outline-variant/20 bg-surface-container-low p-3 lg:flex-row">
                <div class="relative w-full flex-1 lg:w-auto">
                    <Search class="absolute left-5 top-1/2 h-4 w-4 -translate-y-1/2 text-on-surface-variant/40" />
                    <input
                        v-model="filterForm.search"
                        placeholder="Filtrar por nombre de categoría..."
                        class="h-14 w-full rounded-2xl border border-outline-variant/20 bg-surface pl-12 pr-6 text-sm font-medium text-on-surface outline-none transition-all placeholder:text-on-surface-variant/40 focus:border-primary focus:ring-4 focus:ring-primary/5"
                    />
                </div>
                <div class="relative w-full min-w-[160px] flex-1 lg:w-auto lg:flex-none">
                    <Filter class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-on-surface-variant/40" />
                    <AppSelect
                        v-model="filterForm.per_page"
                        :options="[
                            { value: '10', label: '10 por página' },
                            { value: '20', label: '20 por página' },
                            { value: '50', label: '50 por página' }
                        ]"
                        class="h-14 border-outline-variant/20 bg-surface font-bold pl-11 shadow-none text-xs"
                    />
                </div>
            </div>

            <CategoriesTable :categories="categories.data" @edit="openEdit" @destroy="destroyCategory" />
            <AdminPagination :links="paginationLinks" label="Navegación de Categorías" />
        </div>

        <CategoryFormModal :show="showModal" :form="form" :editing-id="editingCategory?.id ?? null" @close="showModal = false" @submit="submit" />

        <AdminFlashToast
            :show="!!(flash.success || flash.error)"
            :message="flash.success ?? flash.error ?? ''"
            :variant="flash.success ? 'success' : 'error'"
        />
    </AppLayout>
</template>

<style scoped>
</style>
