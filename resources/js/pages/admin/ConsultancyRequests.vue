<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminPagination from '@/components/admin/AdminPagination.vue';
import ConsultancyStats from '@/components/admin/consultancies/ConsultancyStats.vue';
import ConsultancyFiltersComponent from '@/components/admin/consultancies/ConsultancyFilters.vue';
import ConsultancyRequestsList from '@/components/admin/consultancies/ConsultancyRequestsList.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import type { ConsultancyRequest, ConsultancyStats as Stats, ConsultancyFilters } from '@/types/consultancy-request';
import type { PaginatedResponse } from '@/types/pagination';

const props = defineProps<{
    requests: PaginatedResponse<ConsultancyRequest>;
    filters: ConsultancyFilters;
    stats: Stats;
}>();

const filterForm = useForm({
    search: props.filters.search ?? '',
    status: props.filters.status ?? '',
});

const viewMode = ref<'cards' | 'list'>('cards');

useDebouncedInertiaFilters(filterForm, () => {
    router.get(route('admin.consultancies.index'), filterForm.data(), { preserveState: false, replace: true });
});

function updateStatus(id: number, status: string) {
    router.patch(route('admin.consultancies.status', { consultancy: id }), { status }, { preserveScroll: true });
}
</script>

<template>
    <Head title="Solicitudes de Consultoría | Admin" />
    <AppLayout>
        <div class="px-4 py-8 max-w-7xl mx-auto min-h-screen">
            <AdminPageHeader
                title="Solicitudes de"
                title-accent="Consultoría"
                subtitle="Gestiona y haz seguimiento de las empresas interesadas en nuestros servicios."
                badge="Comercial / Leads"
            />

            <ConsultancyStats :stats="stats" />

            <ConsultancyFiltersComponent v-model:filterForm="filterForm" v-model:viewMode="viewMode" />

            <ConsultancyRequestsList :requests="requests.data" :view-mode="viewMode" @update-status="updateStatus" />

            <AdminPagination v-if="requests.last_page > 1" :links="requests.links" class="mt-10" />
        </div>
    </AppLayout>
</template>
