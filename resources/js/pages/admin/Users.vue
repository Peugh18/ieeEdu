<script setup lang="ts">
import { useDebouncedInertiaFilters } from '@/composables/useDebouncedInertiaFilters';
import { usePaginationLinks } from '@/composables/usePaginationLinks';
import AppLayout from '@/layouts/AppLayout.vue';
import type { SharedData } from '@/types';
import { UserListItem } from '@/types/admin';
import { PaginationLink } from '@/types/pagination';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { Activity, Check, Download, GraduationCap, ShieldCheck, Users as UsersIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

// Components
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
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

function destroyUser(user: UserListItem) {
    if (!confirm(`¿Desactivar a "${user.name}"? No se borrará físicamente.`)) return;
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
        <div class="mx-auto max-w-[1400px] space-y-8 px-4 py-8">
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
                <!-- Total Users -->
                <div
                    class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-7 shadow-sm transition-all duration-500 hover:shadow-xl hover:shadow-slate-200/50"
                >
                    <div class="absolute right-0 top-0 p-4 opacity-5 transition-transform group-hover:scale-110">
                        <UsersIcon class="h-20 w-20 text-slate-900" />
                    </div>
                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-slate-500"
                                >Población Total</span
                            >
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400">
                                <UsersIcon class="h-5 w-5" />
                            </div>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-4xl font-extrabold tracking-tight text-slate-900">{{ stats.total }}</h3>
                            <p class="mt-1 text-xs font-medium italic text-slate-400">Cuentas registradas</p>
                        </div>
                    </div>
                </div>

                <!-- Active Users -->
                <div
                    class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-7 shadow-sm transition-all duration-500 hover:shadow-xl hover:shadow-emerald-200/30"
                >
                    <div class="absolute right-0 top-0 p-4 text-emerald-600 opacity-5 transition-transform group-hover:scale-110">
                        <Activity class="h-20 w-20" />
                    </div>
                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-emerald-600"
                                >Acceso Vigente</span
                            >
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500">
                                <Activity class="h-5 w-5" />
                            </div>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-4xl font-extrabold tracking-tight text-emerald-600">{{ stats.active }}</h3>
                            <p class="mt-1 text-xs font-medium italic text-emerald-600/60">Usuarios activos hoy</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Users -->
                <div
                    class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-7 shadow-sm transition-all duration-500 hover:shadow-xl hover:shadow-indigo-200/30"
                >
                    <div class="absolute right-0 top-0 p-4 text-indigo-600 opacity-5 transition-transform group-hover:scale-110">
                        <ShieldCheck class="h-20 w-20" />
                    </div>
                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-indigo-600"
                                >Cuerpo Admin</span
                            >
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-500">
                                <ShieldCheck class="h-5 w-5" />
                            </div>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-4xl font-extrabold tracking-tight text-indigo-700">{{ stats.admins }}</h3>
                            <p class="mt-1 text-xs font-medium italic text-indigo-600/60">Personal de gestión</p>
                        </div>
                    </div>
                </div>

                <!-- Students -->
                <div
                    class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-7 shadow-sm transition-all duration-500 hover:shadow-xl hover:shadow-blue-200/30"
                >
                    <div class="absolute right-0 top-0 p-4 text-blue-600 opacity-5 transition-transform group-hover:scale-110">
                        <GraduationCap class="h-20 w-20" />
                    </div>
                    <div class="relative z-10 flex h-full flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-blue-600"
                                >Discentes</span
                            >
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-500">
                                <GraduationCap class="h-5 w-5" />
                            </div>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-4xl font-extrabold tracking-tight text-blue-700">{{ stats.students }}</h3>
                            <p class="mt-1 text-xs font-medium italic text-blue-600/60">Alumnos en formación</p>
                        </div>
                    </div>
                </div>
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
        <Transition
            enter-active-class="transition duration-500"
            enter-from-class="translate-x-full opacity-0"
            enter-to-class="translate-x-0 opacity-100"
        >
            <div
                v-if="flash.success"
                class="fixed bottom-10 right-10 z-[100] flex items-center gap-4 rounded-[2rem] bg-slate-900 p-2 pr-6 text-white shadow-2xl"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500">
                    <Check class="h-6 w-6" />
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-500">Operación Exitosa</span>
                    <span class="text-sm font-medium">{{ flash.success }}</span>
                </div>
            </div>
        </Transition>

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
