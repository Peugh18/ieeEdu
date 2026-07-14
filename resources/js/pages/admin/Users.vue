<script setup lang="ts">
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import AppLayout from '@/layouts/AppLayout.vue';
import type { SharedData } from '@/types';
import { UserListItem } from '@/types/admin';
import { PaginationLink } from '@/types/pagination';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Activity, Check, Download, GraduationCap, ShieldCheck, Users as UsersIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

// Components
import AdminFlashToast from '@/components/admin/AdminFlashToast.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import AdminStatsCard from '@/components/admin/AdminStatsCard.vue';
import UsersCreateModal from '@/components/admin/users/UsersCreateModal.vue';
import UsersEditModal from '@/components/admin/users/UsersEditModal.vue';
import UsersFilters from '@/components/admin/users/UsersFilters.vue';
import UsersTable from '@/components/admin/users/UsersTable.vue';

const props = defineProps<{
    users: { data: UserListItem[]; links: PaginationLink[]; total: number };
    filters: { role?: string; status?: string; search?: string; per_page?: string };
    stats: { total: number; active: number; admins: number; students: number };
}>();

const page = usePage<SharedData>();
const flash = computed(() => page.props.flash ?? {});

// ─── Filters ──────────────────────────────────────────────────────
const filterForm = useForm({
    search: props.filters.search || '',
    role: props.filters.role || '',
    status: props.filters.status || '',
    per_page: props.filters.per_page || '20',
});

function applyFilters() {
    router.get(
        route('admin.users.index'),
        {
            search: filterForm.search || undefined,
            role: filterForm.role || undefined,
            status: filterForm.status || undefined,
            per_page: filterForm.per_page !== '20' ? filterForm.per_page : undefined,
        },
        { preserveState: false, replace: true },
    );
}

useDebouncedInertiaFilters(filterForm, applyFilters);

// ─── Create Modal ──────────────────────────────────────────────────
const showCreate = ref(false);

// ─── Edit Modal ────────────────────────────────────────────────────
const showEdit = ref(false);
const editTarget = ref<UserListItem | null>(null);

function openEdit(user: UserListItem) {
    editTarget.value = user;
    showEdit.value = true;
}

// ─── Actions ─────────────────────────────────────────────────────
function toggleStatus(user: UserListItem) {
    router.patch(route('admin.users.toggleStatus', user.id), {}, { preserveScroll: true });
}

const { confirm: confirmAction } = useConfirmDialog();

async function destroyUser(user: UserListItem) {
    const isConfirmed = await confirmAction({
        title: '¿Desactivar Usuario?',
        description: `¿Estás seguro de desactivar a "${user.name}"? No se borrará físicamente.`,
        danger: true,
        confirmLabel: 'Desactivar',
    });
    if (!isConfirmed) return;
    router.delete(route('admin.users.destroy', user.id), { preserveScroll: true });
}

const exportUrl = computed(() => {
    const p = new URLSearchParams();
    if (filterForm.search) p.set('search', filterForm.search);
    if (filterForm.role) p.set('role', filterForm.role);
    if (filterForm.status) p.set('status', filterForm.status);
    return route('admin.users.export') + (p.toString() ? '?' + p.toString() : '');
});

const paginationLinks = usePaginationLinks(props.users.links);
</script>

<template>
    <Head title="Gestión de Usuarios - iieEdu Admin" />

    <AppLayout>
        <div class="w-full space-y-8 px-6 py-8 lg:px-10">
            <!-- ── Superior Header ─────────────────────────────────────────── -->
            <AdminPageHeader
                title="Gestión de "
                titleAccent="Usuarios"
                subtitle="Directorio académico y control de accesos."
                actionLabel="Nuevo Usuario"
                @action="showCreate = true"
            >
                <template #actions>
                    <a
                        :href="exportUrl"
                        class="group inline-flex h-12 items-center gap-2.5 rounded-2xl border border-outline-variant/30 bg-surface-container-low px-6 text-sm font-bold text-on-surface-variant transition-all duration-300 hover:border-primary hover:bg-surface hover:text-primary"
                    >
                        <Download class="h-4 w-4 transition-transform group-hover:-translate-y-0.5" />
                        Exportar Listado
                    </a>
                </template>
            </AdminPageHeader>

            <!-- ── Global Statistics ──────────────────────────────────────────── -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <AdminStatsCard
                    label="Población Total"
                    sublabel="Cuentas registradas"
                    :value="stats.total"
                    value-class="text-on-surface"
                >
                    <template #icon><UsersIcon class="h-4 w-4 text-outline-variant" /></template>
                    <template #bg-icon><UsersIcon class="h-full w-full" /></template>
                </AdminStatsCard>
                
                <AdminStatsCard
                    label="Acceso Vigente"
                    sublabel="Usuarios activos hoy"
                    :value="stats.active"
                    value-class="text-emerald-500"
                >
                    <template #icon><Activity class="h-4 w-4 text-outline-variant" /></template>
                    <template #bg-icon><Activity class="h-full w-full" /></template>
                </AdminStatsCard>
                
                <AdminStatsCard
                    label="Cuerpo Admin"
                    sublabel="Personal de gestión"
                    :value="stats.admins"
                    value-class="text-indigo-500"
                >
                    <template #icon><ShieldCheck class="h-4 w-4 text-outline-variant" /></template>
                    <template #bg-icon><ShieldCheck class="h-full w-full" /></template>
                </AdminStatsCard>

                <AdminStatsCard
                    label="Discentes"
                    sublabel="Alumnos en formación"
                    :value="stats.students"
                    value-class="text-blue-500"
                >
                    <template #icon><GraduationCap class="h-4 w-4 text-outline-variant" /></template>
                    <template #bg-icon><GraduationCap class="h-full w-full" /></template>
                </AdminStatsCard>
            </div>

            <!-- ── Smart Filtering ──────────────────────────────────────── -->
            <UsersFilters
                v-model:search="filterForm.search"
                v-model:role="filterForm.role"
                v-model:status="filterForm.status"
                v-model:perPage="filterForm.per_page"
                @filter="applyFilters"
            />

            <!-- ── Main Data View ──────────────────────────────────────────── -->
            <UsersTable
                :users="users.data"
                :total="users.total"
                :paginationLinks="paginationLinks"
                @toggleStatus="toggleStatus"
                @edit="openEdit"
                @destroy="destroyUser"
            />
        </div>

        <!-- ───────────────────────── SYSTEM NOTIFICATIONS ──────────────────── -->
        <AdminFlashToast
            :show="!!flash.success"
            :message="flash.success ?? ''"
            variant="success"
        />

        <!-- ───────────────────────── MODALS ───────────────────────── -->
        <UsersCreateModal :show="showCreate" @close="showCreate = false" />

        <UsersEditModal
            :show="showEdit"
            :editTarget="editTarget"
            @close="
                showEdit = false;
                editTarget = null;
            "
        />
    </AppLayout>
</template>
