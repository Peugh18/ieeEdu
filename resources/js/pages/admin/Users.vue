<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import {
    UserPlus, Search, SlidersHorizontal, Download,
    Users, ShieldCheck, GraduationCap, Activity,
    ChevronLeft, ChevronRight, Eye, Pencil, Trash2,
    ToggleLeft, ToggleRight, X, BookOpen, Check
} from 'lucide-vue-next';

interface UserItem {
    id: number;
    name: string;
    email: string;
    telefono: string | null;
    role: string;
    status: string;
    enrollments_count: number;
    created_at: string;
}

const props = defineProps<{
    users: { data: UserItem[]; links: any[]; meta?: any; current_page: number; last_page: number; total: number; per_page: number };
    filters: { role?: string; status?: string; search?: string; per_page?: string };
    stats: { total: number; active: number; admins: number; students: number };
}>();

const page = usePage();
const flash = computed(() => (page.props as any).flash ?? {});

// ─── Filters ──────────────────────────────────────────────────────
const searchInput = ref(props.filters.search || '');
const roleFilter = ref(props.filters.role || '');
const statusFilter = ref(props.filters.status || '');
const perPage = ref(props.filters.per_page || '20');

function applyFilters() {
    router.get(route('admin.users.index'), {
        search: searchInput.value || undefined,
        role: roleFilter.value || undefined,
        status: statusFilter.value || undefined,
        per_page: perPage.value !== '20' ? perPage.value : undefined,
    }, { preserveState: true, replace: true });
}

watch([roleFilter, statusFilter, perPage], () => applyFilters());

let searchTimer: any;
watch(searchInput, () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(applyFilters, 400);
});

// ─── Create Modal ──────────────────────────────────────────────────
const showCreate = ref(false);
const createForm = useForm({
    name: '',
    email: '',
    telefono: '',
    role: 'usuario',
    status: 'activo',
    password: '',
});

function submitCreate() {
    createForm.post(route('admin.users.store'), {
        onSuccess: () => {
            showCreate.value = false;
            createForm.reset();
        },
    });
}

// ─── Edit Modal ────────────────────────────────────────────────────
const showEdit = ref(false);
const editTarget = ref<UserItem | null>(null);
const editForm = useForm({
    name: '',
    email: '',
    telefono: '',
    role: 'usuario',
    status: 'activo',
    password: '',
});

function openEdit(user: UserItem) {
    editTarget.value = user;
    editForm.reset();
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.telefono = user.telefono || '';
    editForm.role = user.role;
    editForm.status = user.status;
    editForm.password = '';
    showEdit.value = true;
}

function submitEdit() {
    if (!editTarget.value) return;
    editForm.put(route('admin.users.update', editTarget.value.id), {
        onSuccess: () => {
            showEdit.value = false;
            editTarget.value = null;
            editForm.reset();
        },
    });
}

// ─── Actions ─────────────────────────────────────────────────────
function toggleStatus(user: UserItem) {
    router.patch(route('admin.users.toggleStatus', user.id), {}, { preserveScroll: true });
}

function destroyUser(user: UserItem) {
    if (!confirm(`¿Desactivar a "${user.name}"? No se borrará físicamente.`)) return;
    router.delete(route('admin.users.destroy', user.id), { preserveScroll: true });
}

// ─── Helpers ──────────────────────────────────────────────────────
function initials(name: string) {
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}

const avatarColors = [
    'bg-purple-100 text-purple-700', 'bg-blue-100 text-blue-700',
    'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700',
    'bg-rose-100 text-rose-700', 'bg-cyan-100 text-cyan-700',
];

function avatarColor(id: number) { return avatarColors[id % avatarColors.length]; }

function formatDate(d: string) {
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
}

const exportUrl = computed(() => {
    const p = new URLSearchParams();
    if (searchInput.value) p.set('search', searchInput.value);
    if (roleFilter.value) p.set('role', roleFilter.value);
    if (statusFilter.value) p.set('status', statusFilter.value);
    return route('admin.users.export') + (p.toString() ? '?' + p.toString() : '');
});

const paginationLinks = computed(() => props.users.links?.filter((l: any) => l.url) ?? []);
</script>

<template>
    <Head title="Usuarios - Admin IEE" />

    <AppLayout>
        <!-- ── Header ─────────────────────────────────────────── -->
        <div class="mb-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="font-serif text-4xl text-on-surface">
                        Gestión de <span class="italic text-[#57572A]">Usuarios</span>
                    </h1>
                    <p class="mt-1 text-sm text-on-surface-variant">Administra cuentas, roles y accesos al campus digital.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a :href="exportUrl" class="inline-flex items-center gap-2 rounded-xl border border-outline-variant/30 bg-white px-4 py-2.5 text-sm font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors">
                        <Download class="h-4 w-4" />
                        Exportar CSV
                    </a>
                    <button
                        @click="showCreate = true"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#57572A] px-5 py-2.5 text-sm font-bold text-white shadow hover:opacity-95 transition-opacity"
                    >
                        <UserPlus class="h-4 w-4" />
                        Crear usuario
                    </button>
                </div>
            </div>
        </div>

        <!-- ── Stats ──────────────────────────────────────────── -->
        <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div class="rounded-2xl bg-white border border-outline-variant/10 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Total</p>
                    <Users class="h-5 w-5 text-[#C9C7B8]" />
                </div>
                <p class="mt-3 text-3xl font-bold text-on-surface">{{ props.stats.total }}</p>
            </div>
            <div class="rounded-2xl bg-white border border-outline-variant/10 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Activos</p>
                    <Activity class="h-5 w-5 text-emerald-400" />
                </div>
                <p class="mt-3 text-3xl font-bold text-emerald-600">{{ props.stats.active }}</p>
            </div>
            <div class="rounded-2xl bg-white border border-outline-variant/10 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Admins</p>
                    <ShieldCheck class="h-5 w-5 text-purple-400" />
                </div>
                <p class="mt-3 text-3xl font-bold text-purple-700">{{ props.stats.admins }}</p>
            </div>
            <div class="rounded-2xl bg-white border border-outline-variant/10 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Estudiantes</p>
                    <GraduationCap class="h-5 w-5 text-blue-400" />
                </div>
                <p class="mt-3 text-3xl font-bold text-blue-700">{{ props.stats.students }}</p>
            </div>
        </div>

        <!-- ── Filter Bar ──────────────────────────────────────── -->
        <div class="mb-6 rounded-2xl bg-white border border-outline-variant/10 p-4 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="flex flex-1 items-center gap-2 rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-3 py-2">
                    <Search class="h-4 w-4 flex-shrink-0 text-on-surface-variant" />
                    <input
                        v-model="searchInput"
                        placeholder="Buscar por nombre o email..."
                        class="w-full bg-transparent text-sm outline-none placeholder:text-on-surface-variant/60"
                    />
                </div>
                <div class="flex flex-wrap gap-2">
                    <select v-model="roleFilter" class="rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-3 py-2 text-sm text-on-surface outline-none">
                        <option value="">Todos los roles</option>
                        <option value="admin">Admin</option>
                        <option value="usuario">Estudiante</option>
                    </select>
                    <select v-model="statusFilter" class="rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-3 py-2 text-sm text-on-surface outline-none">
                        <option value="">Todos los estados</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                    <select v-model="perPage" class="rounded-xl border border-outline-variant/20 bg-surface-container-lowest px-3 py-2 text-sm text-on-surface outline-none">
                        <option value="10">10 / pág.</option>
                        <option value="20">20 / pág.</option>
                        <option value="50">50 / pág.</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ── Table ──────────────────────────────────────────── -->
        <div class="rounded-3xl border border-outline-variant/10 bg-white overflow-hidden shadow-sm">
            <!-- Header -->
            <div class="grid grid-cols-12 gap-2 bg-surface-container-low px-6 py-3 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant/70">
                <div class="col-span-4">Usuario</div>
                <div class="col-span-2">Teléfono</div>
                <div class="col-span-1">Rol</div>
                <div class="col-span-1">Estado</div>
                <div class="col-span-2">Cursos / Registro</div>
                <div class="col-span-2 text-right">Acciones</div>
            </div>

            <div v-if="!props.users.data.length" class="py-16 text-center text-sm text-on-surface-variant">
                No se encontraron usuarios con los filtros aplicados.
            </div>

            <div v-else>
                <div
                    v-for="user in props.users.data"
                    :key="user.id"
                    class="grid grid-cols-12 items-center gap-2 border-t border-outline-variant/10 px-6 py-4 hover:bg-surface-container-lowest transition-colors"
                >
                    <!-- Avatar + Name + Email -->
                    <div class="col-span-4 flex items-center gap-3 min-w-0">
                        <div :class="`flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full text-sm font-bold ${avatarColor(user.id)}`">
                            {{ initials(user.name) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-on-surface">{{ user.name }}</p>
                            <p class="truncate text-xs text-on-surface-variant">{{ user.email }}</p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="col-span-2 text-sm text-on-surface-variant">
                        {{ user.telefono || '—' }}
                    </div>

                    <!-- Role badge -->
                    <div class="col-span-1">
                        <span
                            class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider"
                            :class="user.role === 'admin'
                                ? 'bg-purple-100 text-purple-700'
                                : 'bg-blue-100 text-blue-700'"
                        >
                            {{ user.role === 'admin' ? 'Admin' : 'Estudiante' }}
                        </span>
                    </div>

                    <!-- Status toggle -->
                    <div class="col-span-1">
                        <button
                            @click="toggleStatus(user)"
                            :title="user.status === 'activo' ? 'Click para desactivar' : 'Click para activar'"
                            class="flex items-center gap-1.5 group"
                        >
                            <ToggleRight v-if="user.status === 'activo'" class="h-6 w-6 text-emerald-500 group-hover:text-emerald-600" />
                            <ToggleLeft v-else class="h-6 w-6 text-slate-400 group-hover:text-slate-500" />
                            <span class="text-xs font-medium" :class="user.status === 'activo' ? 'text-emerald-600' : 'text-slate-400'">
                                {{ user.status === 'activo' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </button>
                    </div>

                    <!-- Courses + Date -->
                    <div class="col-span-2">
                        <div class="flex items-center gap-1.5 text-sm text-on-surface-variant">
                            <BookOpen class="h-3.5 w-3.5" />
                            <span class="font-semibold text-on-surface">{{ user.enrollments_count }}</span>
                            <span>curso{{ user.enrollments_count !== 1 ? 's' : '' }}</span>
                        </div>
                        <p class="mt-0.5 text-[11px] text-on-surface-variant/70">{{ formatDate(user.created_at) }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="col-span-2 flex items-center justify-end gap-2">
                        <Link
                            :href="route('admin.users.show', user.id)"
                            class="rounded-lg p-2 text-on-surface-variant hover:bg-surface-container-low hover:text-[#57572A] transition-colors"
                            title="Ver perfil"
                        >
                            <Eye class="h-4 w-4" />
                        </Link>
                        <button
                            @click="openEdit(user)"
                            class="rounded-lg p-2 text-on-surface-variant hover:bg-surface-container-low hover:text-blue-600 transition-colors"
                            title="Editar"
                        >
                            <Pencil class="h-4 w-4" />
                        </button>
                        <button
                            @click="destroyUser(user)"
                            class="rounded-lg p-2 text-on-surface-variant hover:bg-red-50 hover:text-red-600 transition-colors"
                            title="Desactivar"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Pagination ─────────────────────────────────────── -->
        <div v-if="paginationLinks.length > 1" class="mt-4 flex items-center justify-between text-sm text-on-surface-variant">
            <p>
                {{ props.users.total }} usuario{{ props.users.total !== 1 ? 's' : '' }} en total
            </p>
            <div class="flex gap-1.5">
                <Link
                    v-for="link in paginationLinks"
                    :key="link.label"
                    :href="link.url"
                    class="rounded-xl border px-3 py-1.5 text-xs font-semibold transition-colors"
                    :class="link.active
                        ? 'border-[#57572A] bg-[#57572A] text-white'
                        : 'border-outline-variant/20 bg-white text-on-surface hover:bg-surface-container-low'"
                    v-html="link.label"
                />
            </div>
        </div>

        <!-- Flash success -->
        <Transition enter-active-class="transition duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100">
            <div v-if="flash.success" class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-2xl bg-emerald-600 px-5 py-3 text-white shadow-2xl">
                <Check class="h-5 w-5" />
                <span class="text-sm font-semibold">{{ flash.success }}</span>
            </div>
        </Transition>

        <!-- ───────────────────────── MODALS ──────────────────── -->

        <!-- Create Modal -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                    <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl">
                        <div class="flex items-center justify-between border-b border-outline-variant/10 px-7 py-5">
                            <div>
                                <h2 class="font-serif text-2xl text-on-surface">Crear <span class="italic">Usuario</span></h2>
                                <p class="mt-0.5 text-xs text-on-surface-variant">Nuevo acceso al campus digital IEE</p>
                            </div>
                            <button @click="showCreate = false; createForm.reset()" class="rounded-xl p-2 hover:bg-surface-container-low">
                                <X class="h-5 w-5 text-on-surface-variant" />
                            </button>
                        </div>
                        <form @submit.prevent="submitCreate" class="grid gap-4 p-7">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Nombre completo *</label>
                                    <input v-model="createForm.name" required placeholder="Juan Pérez" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors" />
                                    <p v-if="createForm.errors.name" class="mt-1 text-xs text-red-500">{{ createForm.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Email *</label>
                                    <input v-model="createForm.email" type="email" required placeholder="juan@email.com" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors" />
                                    <p v-if="createForm.errors.email" class="mt-1 text-xs text-red-500">{{ createForm.errors.email }}</p>
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Teléfono</label>
                                    <input v-model="createForm.telefono" placeholder="999 999 999" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Contraseña *</label>
                                    <input v-model="createForm.password" type="password" required placeholder="Mín. 8 caracteres" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors" />
                                    <p v-if="createForm.errors.password" class="mt-1 text-xs text-red-500">{{ createForm.errors.password }}</p>
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Rol *</label>
                                    <select v-model="createForm.role" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors bg-white">
                                        <option value="usuario">Estudiante</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Estado *</label>
                                    <select v-model="createForm.status" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors bg-white">
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 border-t border-outline-variant/10 pt-4">
                                <button type="button" @click="showCreate = false; createForm.reset()" class="rounded-xl border border-outline-variant/30 px-5 py-2.5 text-sm font-semibold hover:bg-surface-container-low transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" :disabled="createForm.processing" class="rounded-xl bg-[#57572A] px-6 py-2.5 text-sm font-bold text-white shadow hover:opacity-95 disabled:opacity-60 transition-opacity">
                                    {{ createForm.processing ? 'Guardando...' : 'Crear usuario' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Edit Modal -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                    <div class="w-full max-w-lg rounded-3xl bg-white shadow-2xl">
                        <div class="flex items-center justify-between border-b border-outline-variant/10 px-7 py-5">
                            <div>
                                <h2 class="font-serif text-2xl text-on-surface">Editar <span class="italic">Usuario</span></h2>
                                <p class="mt-0.5 text-xs text-on-surface-variant">{{ editTarget?.name }}</p>
                            </div>
                            <button @click="showEdit = false; editForm.reset()" class="rounded-xl p-2 hover:bg-surface-container-low">
                                <X class="h-5 w-5 text-on-surface-variant" />
                            </button>
                        </div>
                        <form @submit.prevent="submitEdit" class="grid gap-4 p-7">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Nombre *</label>
                                    <input v-model="editForm.name" required class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors" />
                                    <p v-if="editForm.errors.name" class="mt-1 text-xs text-red-500">{{ editForm.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Email *</label>
                                    <input v-model="editForm.email" type="email" required class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors" />
                                    <p v-if="editForm.errors.email" class="mt-1 text-xs text-red-500">{{ editForm.errors.email }}</p>
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Teléfono</label>
                                    <input v-model="editForm.telefono" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Nueva contraseña</label>
                                    <input v-model="editForm.password" type="password" placeholder="Dejar en blanco para no cambiar" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors" />
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Rol *</label>
                                    <select v-model="editForm.role" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A] bg-white">
                                        <option value="usuario">Estudiante</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Estado *</label>
                                    <select v-model="editForm.status" class="w-full rounded-xl border border-outline-variant/30 px-3 py-2.5 text-sm outline-none focus:border-[#57572A] bg-white">
                                        <option value="activo">Activo</option>
                                        <option value="inactivo">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 border-t border-outline-variant/10 pt-4">
                                <button type="button" @click="showEdit = false; editForm.reset()" class="rounded-xl border border-outline-variant/30 px-5 py-2.5 text-sm font-semibold hover:bg-surface-container-low">
                                    Cancelar
                                </button>
                                <button type="submit" :disabled="editForm.processing" class="rounded-xl bg-[#57572A] px-6 py-2.5 text-sm font-bold text-white shadow hover:opacity-95 disabled:opacity-60">
                                    {{ editForm.processing ? 'Guardando...' : 'Guardar cambios' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
