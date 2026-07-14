<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminStatsCard from '@/components/admin/AdminStatsCard.vue';
import CertificatesTable from '@/components/admin/certificates/CertificatesTable.vue';
import AppSelect from '@/components/ui/AppSelect.vue';
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import type { PaginatedResponse } from '@/types/pagination';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Award, Filter, GraduationCap, FileText, Users, Search } from 'lucide-vue-next';

interface CertificateItem {
    id: number;
    code: string;
    file_path: string | null;
    issue_date: string;
    user: { id?: number; name: string; email: string };
    course: { id?: number; title: string } | null;
}

const props = defineProps<{
    certificates: PaginatedResponse<CertificateItem>;
    filters: { search?: string; per_page?: string };
    stats: {
        total: number;
        this_month: number;
        with_file: number;
        unique_students: number;
    };
}>();

// ─── Filters ────────────────────────────────────────────────────
const filterForm = useForm({
    search: props.filters.search || '',
    per_page: props.filters.per_page || '20',
});

function applyFilters() {
    router.get(
        route('admin.certificates.index'),
        {
            search: filterForm.search || undefined,
            per_page: filterForm.per_page !== '20' ? filterForm.per_page : undefined,
        },
        { preserveState: false, replace: true },
    );
}

useDebouncedInertiaFilters(filterForm, applyFilters);

const pgLinks = usePaginationLinks(props.certificates.links);
</script>

<template>
    <Head title="Gestión de Certificados - iieEdu Admin" />
    <AppLayout>
        <div class="w-full space-y-8 px-6 py-8 lg:px-10">
            <!-- ── Header ── -->
            <AdminPageHeader title="Certificados " titleAccent="emitidos" subtitle="Historial de certificados generados para estudiantes." />

            <!-- ── Stats Grid ── -->
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <AdminStatsCard
                    label="Total Emitidos"
                    sublabel="Certificados históricos"
                    :value="props.stats.total"
                    value-class="text-emerald-500"
                >
                    <template #icon><Award class="h-4 w-4 text-outline-variant" /></template>
                    <template #bg-icon><Award class="h-full w-full" /></template>
                </AdminStatsCard>

                <AdminStatsCard
                    label="Este Mes"
                    sublabel="Emitidos en el mes actual"
                    :value="props.stats.this_month"
                    value-class="text-blue-500"
                >
                    <template #icon><FileText class="h-4 w-4 text-outline-variant" /></template>
                    <template #bg-icon><FileText class="h-full w-full" /></template>
                </AdminStatsCard>

                <AdminStatsCard
                    label="Con Archivo"
                    sublabel="PDFs generados"
                    :value="props.stats.with_file"
                    value-class="text-violet-500"
                >
                    <template #icon><GraduationCap class="h-4 w-4 text-outline-variant" /></template>
                    <template #bg-icon><GraduationCap class="h-full w-full" /></template>
                </AdminStatsCard>

                <AdminStatsCard
                    label="Estudiantes"
                    sublabel="Alumnos certificados"
                    :value="props.stats.unique_students"
                    value-class="text-amber-500"
                >
                    <template #icon><Users class="h-4 w-4 text-outline-variant" /></template>
                    <template #bg-icon><Users class="h-full w-full" /></template>
                </AdminStatsCard>
            </div>

            <!-- ── Filter Bar ── -->
            <div class="flex flex-col items-center gap-3 rounded-[2.5rem] border border-outline-variant/20 bg-surface-container-low p-3 lg:flex-row">
                <div class="relative w-full flex-1 lg:w-auto">
                    <Search class="absolute left-5 top-1/2 h-4 w-4 -translate-y-1/2 text-on-surface-variant/40" />
                    <input
                        v-model="filterForm.search"
                        placeholder="Buscar por estudiante, curso o código..."
                        class="h-14 w-full rounded-2xl border border-outline-variant/20 bg-surface pl-12 pr-6 text-sm font-medium text-on-surface outline-none transition-all placeholder:text-on-surface-variant/40 focus:border-primary focus:ring-4 focus:ring-primary/5"
                    />
                </div>
                <div class="flex w-full flex-wrap items-center gap-3 lg:w-auto">
                    <div class="relative min-w-[160px] flex-1 lg:flex-none">
                        <Filter class="absolute left-4 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-on-surface-variant/40" />
                        <AppSelect
                            v-model="filterForm.per_page"
                            :options="[
                                { value: '10', label: '10 por página' },
                                { value: '20', label: '20 por página' },
                                { value: '50', label: '50 por página' }
                            ]"
                            class="h-14 border-outline-variant/20 bg-surface font-bold pl-10 shadow-none text-xs"
                        />
                    </div>
                </div>
            </div>

            <!-- ── Table View ── -->
            <CertificatesTable
                :certificates="props.certificates.data"
                :total="props.stats.total"
                :pagination-links="pgLinks"
            />
        </div>
    </AppLayout>
</template>

<style scoped>
</style>
