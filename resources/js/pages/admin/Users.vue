<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import {
    UserPlus, Search, SlidersHorizontal, Download,
    Users as UsersIcon, ShieldCheck, GraduationCap, Activity,
    ChevronLeft, ChevronRight, Eye, Pencil, Trash2,
    ToggleLeft, ToggleRight, X, BookOpen, Check, MoreVertical,
    Calendar, PhoneCall, Mail
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
    'bg-indigo-50 text-indigo-700 ring-indigo-700/10',
    'bg-emerald-50 text-emerald-700 ring-emerald-700/10',
    'bg-amber-50 text-amber-700 ring-amber-700/10',
    'bg-rose-50 text-rose-700 ring-rose-700/10',
    'bg-blue-50 text-blue-700 ring-blue-700/10',
    'bg-purple-50 text-purple-700 ring-purple-700/10',
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
    <Head title="Gestión de Usuarios - iieEdu Admin" />

    <AppLayout>
        <div class="max-w-[1400px] mx-auto px-4 py-8 space-y-8">
            
            <!-- ── Superior Header ─────────────────────────────────────────── -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
                <div class="space-y-1">
                    <h1 class="font-serif text-5xl tracking-tight text-slate-900 leading-tight">
                        Gestión de <span class="italic text-[#57572A] relative">Usuarios
                            <span class="absolute -bottom-1 left-0 w-full h-[3px] bg-[#57572A]/10 rounded-full"></span>
                        </span>
                    </h1>
                    <p class="text-slate-500 font-medium text-lg flex items-center gap-2">
                        <UsersIcon class="w-4 h-4" />
                        Directorio académico y control de accesos.
                    </p>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <a :href="exportUrl" class="group h-12 inline-flex items-center gap-2.5 rounded-2xl bg-slate-50 border border-slate-200 px-6 text-sm font-bold text-slate-600 hover:bg-white hover:border-[#57572A] hover:text-[#57572A] hover:shadow-lg hover:shadow-[#57572A]/5 transition-all duration-300">
                        <Download class="h-4 w-4 group-hover:-translate-y-0.5 transition-transform" />
                        Exportar Listado
                    </a>
                    <button
                        @click="showCreate = true"
                        class="h-12 inline-flex items-center gap-2.5 rounded-2xl bg-[#57572A] px-7 text-sm font-bold text-white shadow-xl shadow-[#57572A]/20 hover:scale-[1.02] active:scale-95 hover:opacity-95 transition-all duration-300"
                    >
                        <UserPlus class="h-4 w-4" />
                        Nuevo Usuario
                    </button>
                </div>
            </div>

            <!-- ── Global Statistics ──────────────────────────────────────────── -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Users -->
                <div class="group relative overflow-hidden rounded-[2rem] bg-white border border-slate-100 p-7 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-500">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform">
                        <UsersIcon class="w-20 h-20 text-slate-900" />
                    </div>
                    <div class="relative z-10 flex flex-col justify-between h-full">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full bg-slate-100 text-[10px] font-bold uppercase tracking-widest text-slate-500">Población Total</span>
                            <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                                <UsersIcon class="w-5 h-5" />
                            </div>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-4xl font-extrabold text-slate-900 tracking-tight">{{ props.stats.total }}</h3>
                            <p class="text-xs font-medium text-slate-400 mt-1 italic">Cuentas registradas</p>
                        </div>
                    </div>
                </div>

                <!-- Active Users -->
                <div class="group relative overflow-hidden rounded-[2rem] bg-white border border-slate-100 p-7 shadow-sm hover:shadow-xl hover:shadow-emerald-200/30 transition-all duration-500">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform text-emerald-600">
                        <Activity class="w-20 h-20" />
                    </div>
                    <div class="relative z-10 flex flex-col justify-between h-full">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full bg-emerald-50 text-[10px] font-bold uppercase tracking-widest text-emerald-600">Acceso Vigente</span>
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                                <Activity class="w-5 h-5" />
                            </div>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-4xl font-extrabold text-emerald-600 tracking-tight">{{ props.stats.active }}</h3>
                            <p class="text-xs font-medium text-emerald-600/60 mt-1 italic">Usuarios activos hoy</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Users -->
                <div class="group relative overflow-hidden rounded-[2rem] bg-white border border-slate-100 p-7 shadow-sm hover:shadow-xl hover:shadow-indigo-200/30 transition-all duration-500">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform text-indigo-600">
                        <ShieldCheck class="w-20 h-20" />
                    </div>
                    <div class="relative z-10 flex flex-col justify-between h-full">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full bg-indigo-50 text-[10px] font-bold uppercase tracking-widest text-indigo-600">Cuerpo Admin</span>
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500">
                                <ShieldCheck class="w-5 h-5" />
                            </div>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-4xl font-extrabold text-indigo-700 tracking-tight">{{ props.stats.admins }}</h3>
                            <p class="text-xs font-medium text-indigo-600/60 mt-1 italic">Personal de gestión</p>
                        </div>
                    </div>
                </div>

                <!-- Students -->
                <div class="group relative overflow-hidden rounded-[2rem] bg-white border border-slate-100 p-7 shadow-sm hover:shadow-xl hover:shadow-blue-200/30 transition-all duration-500">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform text-blue-600">
                        <GraduationCap class="w-20 h-20" />
                    </div>
                    <div class="relative z-10 flex flex-col justify-between h-full">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full bg-blue-50 text-[10px] font-bold uppercase tracking-widest text-blue-600">Discentes</span>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                                <GraduationCap class="w-5 h-5" />
                            </div>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-4xl font-extrabold text-blue-700 tracking-tight">{{ props.stats.students }}</h3>
                            <p class="text-xs font-medium text-blue-600/60 mt-1 italic">Alumnos en formación</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Smart Filtering ──────────────────────────────────────── -->
            <div class="bg-white/80 backdrop-blur-md rounded-[2rem] border border-slate-100 p-6 flex flex-col lg:flex-row items-center gap-4 shadow-sm">
                <div class="relative w-full lg:w-96 group">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-[#57572A] transition-colors" />
                    <input
                        v-model="searchInput"
                        placeholder="Filtrar por nombre, DNI o email..."
                        class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-4 text-sm font-medium outline-none focus:ring-4 focus:ring-[#57572A]/5 focus:border-[#57572A] transition-all"
                    />
                </div>
                
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                    <div class="flex items-center gap-2 bg-slate-50 px-2 rounded-2xl border border-slate-200">
                        <span class="text-[10px] font-bold uppercase text-slate-400 pl-2">Rol</span>
                        <select v-model="roleFilter" class="h-10 bg-transparent text-sm font-semibold text-slate-700 pr-8 outline-none cursor-pointer">
                            <option value="">Cualquier Rol</option>
                            <option value="admin">Administrador</option>
                            <option value="usuario">Estudiante</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 bg-slate-50 px-2 rounded-2xl border border-slate-200">
                        <span class="text-[10px] font-bold uppercase text-slate-400 pl-2">Estado</span>
                        <select v-model="statusFilter" class="h-10 bg-transparent text-sm font-semibold text-slate-700 pr-8 outline-none cursor-pointer">
                            <option value="">Cualquier Estado</option>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Baja</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 bg-slate-50 px-2 rounded-2xl border border-slate-200">
                        <select v-model="perPage" class="h-10 bg-transparent text-sm font-semibold text-slate-700 px-2 outline-none cursor-pointer">
                            <option value="10">10 por hoja</option>
                            <option value="20">20 por hoja</option>
                            <option value="50">50 por hoja</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ── Main Data View ──────────────────────────────────────────── -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                <!-- Data Header -->
                <div class="hidden lg:grid grid-cols-12 gap-4 px-8 py-5 border-b border-slate-50 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 bg-slate-50/50">
                    <div class="col-span-4">Identidad del Usuario</div>
                    <div class="col-span-2 text-center">Contacto</div>
                    <div class="col-span-2 text-center">Privilegios</div>
                    <div class="col-span-2 text-center">Acceso</div>
                    <div class="col-span-2 text-right">Acciones</div>
                </div>

                <!-- Empty State -->
                <div v-if="!props.users.data.length" class="flex flex-col items-center justify-center py-24 text-center space-y-4">
                    <div class="w-16 h-16 rounded-3xl bg-slate-50 flex items-center justify-center text-slate-200">
                        <Search class="w-8 h-8" />
                    </div>
                    <div>
                        <h4 class="text-xl font-serif text-slate-900">Sin coincidencias</h4>
                        <p class="text-slate-400 text-sm max-w-xs mx-auto">No hemos encontrado usuarios que cumplan con los criterios de búsqueda actuales.</p>
                    </div>
                </div>

                <!-- Data Rows -->
                <div v-else class="divide-y divide-slate-50">
                    <div
                        v-for="user in props.users.data"
                        :key="user.id"
                        class="group grid grid-cols-1 lg:grid-cols-12 items-center gap-4 px-8 py-6 hover:bg-slate-50/80 transition-all duration-300"
                    >
                        <!-- Identity Column -->
                        <div class="col-span-1 lg:col-span-4 flex items-center gap-4">
                            <div class="relative">
                                <div :class="`w-14 h-14 rounded-2xl flex items-center justify-center text-lg font-bold shadow-inner ring-4 ring-white ${avatarColor(user.id)}`">
                                    {{ initials(user.name) }}
                                </div>
                                <div v-if="user.status === 'activo'" class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                                <div v-else class="absolute -bottom-1 -right-1 w-4 h-4 bg-slate-300 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-base font-bold text-slate-900 truncate tracking-tight group-hover:text-[#57572A] transition-colors">{{ user.name }}</h4>
                                <div class="flex items-center gap-1.5 text-xs text-slate-400 mt-0.5">
                                    <Mail class="w-3 h-3" />
                                    {{ user.email }}
                                </div>
                            </div>
                        </div>

                        <!-- Contact Column -->
                        <div class="col-span-1 lg:col-span-2 flex justify-center">
                            <div v-if="user.telefono" class="flex items-center gap-2 text-sm font-medium text-slate-600 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200/50">
                                <PhoneCall class="w-3 h-3 text-slate-400" />
                                {{ user.telefono }}
                            </div>
                            <span v-else class="text-slate-300 text-xs italic">— Sin teléfono —</span>
                        </div>

                        <!-- Privileges Column -->
                        <div class="col-span-1 lg:col-span-2 flex justify-center">
                            <span
                                v-if="user.role === 'admin'"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold ring-1 ring-inset ring-indigo-700/10"
                            >
                                <ShieldCheck class="w-3 h-3" />
                                ADMIN
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-bold ring-1 ring-inset ring-blue-700/10"
                            >
                                <GraduationCap class="w-3 h-3" />
                                ESTUDIANTE
                            </span>
                        </div>

                        <!-- Access Column -->
                        <div class="col-span-1 lg:col-span-2 flex flex-col items-center gap-1">
                            <button
                                @click="toggleStatus(user)"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none"
                                :class="user.status === 'activo' ? 'bg-emerald-500' : 'bg-slate-200'"
                            >
                                <span class="sr-only">Cambiar estado</span>
                                <span
                                    aria-hidden="true"
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="user.status === 'activo' ? 'translate-x-5' : 'translate-x-0'"
                                ></span>
                            </button>
                            <span class="text-[10px] font-bold uppercase tracking-wider" :class="user.status === 'activo' ? 'text-emerald-600' : 'text-slate-400'">
                                {{ user.status === 'activo' ? 'Vigente' : 'Inactivo' }}
                            </span>
                        </div>

                        <!-- Actions Column -->
                        <div class="col-span-1 lg:col-span-2 flex justify-end gap-1.5">
                            <Link
                                :href="route('admin.users.show', user.id)"
                                class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-300"
                                title="Ficha Técnica"
                            >
                                <Eye class="w-5 h-5" />
                            </Link>
                            <button
                                @click="openEdit(user)"
                                class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-all duration-300"
                                title="Editar Perfil"
                            >
                                <Pencil class="w-5 h-5" />
                            </button>
                            <button
                                @click="destroyUser(user)"
                                class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all duration-300"
                                title="Baja de Sistema"
                            >
                                <Trash2 class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Data Footer / Pagination -->
                <div v-if="paginationLinks.length > 1" class="px-8 py-6 bg-slate-50/50 border-t border-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm font-medium text-slate-500">
                        Mostrando <span class="text-slate-900 font-bold">{{ props.users.data.length }}</span> de <span class="text-slate-900 font-bold">{{ props.users.total }}</span> registros
                    </p>
                    <div class="flex items-center gap-1.5">
                        <Link
                            v-for="link in paginationLinks"
                            :key="link.label"
                            :href="link.url"
                            class="min-w-[40px] h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all duration-300"
                            :class="link.active
                                ? 'bg-[#57572A] text-white shadow-lg shadow-[#57572A]/20'
                                : 'bg-white border border-slate-200 text-slate-600 hover:border-[#57572A] hover:text-[#57572A]'"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- ───────────────────────── SYSTEM NOTIFICATIONS ──────────────────── -->
        <Transition enter-active-class="transition duration-500" enter-from-class="translate-x-full opacity-0" enter-to-class="translate-x-0 opacity-100">
            <div v-if="flash.success" class="fixed bottom-10 right-10 z-[100] flex items-center gap-4 rounded-[2rem] bg-slate-900 p-2 pr-6 text-white shadow-2xl">
                <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center">
                    <Check class="h-6 w-6" />
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] uppercase font-bold tracking-widest text-emerald-500">Operación Exitosa</span>
                    <span class="text-sm font-medium">{{ flash.success }}</span>
                </div>
            </div>
        </Transition>

        <!-- ───────────────────────── MODAL ENGINE ──────────────────── -->

        <!-- Create User Modal -->
        <Teleport to="body">
            <Transition name="modal-bounce">
                <div v-if="showCreate" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showCreate = false; createForm.reset()"></div>
                    <div class="relative w-full max-w-2xl rounded-[3rem] bg-white shadow-2xl overflow-hidden">
                        <div class="bg-gradient-to-br from-[#57572A] to-[#6a6a3b] p-10 text-white relative">
                            <div class="absolute top-0 right-0 p-10 opacity-10">
                                <UserPlus class="w-32 h-32" />
                            </div>
                            <h2 class="font-serif text-4xl leading-tight">Alta de <span class="italic underline decoration-white/20 underline-offset-8">Nuevo Usuario</span></h2>
                            <p class="mt-4 text-white/70 text-sm max-w-sm">Completa el expediente digital del usuario para habilitar el acceso a la plataforma.</p>
                            <button @click="showCreate = false; createForm.reset()" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                                <X class="h-5 w-5" />
                            </button>
                        </div>
                        
                        <form @submit.prevent="submitCreate" class="p-10 space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label for="create_name" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Nombre del Perfil</label>
                                    <input id="create_name" name="name" v-model="createForm.name" autocomplete="name" required placeholder="Ej: Dr. Manuel García" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-[#57572A]/5 focus:border-[#57572A] transition-all" />
                                    <p v-if="createForm.errors.name" class="text-[10px] text-rose-500 font-bold pl-1">{{ createForm.errors.name }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="create_email" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Correo Electrónico</label>
                                    <input id="create_email" name="email" v-model="createForm.email" type="email" autocomplete="email" required placeholder="ejemplo@iieedu.pe" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-[#57572A]/5 focus:border-[#57572A] transition-all" />
                                    <p v-if="createForm.errors.email" class="text-[10px] text-rose-500 font-bold pl-1">{{ createForm.errors.email }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="create_telefono" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Teléfono Móvil</label>
                                    <input id="create_telefono" name="telefono" v-model="createForm.telefono" type="tel" autocomplete="tel" placeholder="+51 9XX XXX XXX" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-[#57572A]/5 focus:border-[#57572A] transition-all" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="create_password" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Clave de Seguridad</label>
                                    <input id="create_password" name="password" v-model="createForm.password" type="password" autocomplete="new-password" required placeholder="Mínimo 8 caracteres" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-[#57572A]/5 focus:border-[#57572A] transition-all" />
                                    <p v-if="createForm.errors.password" class="text-[10px] text-rose-500 font-bold pl-1">{{ createForm.errors.password }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                                <div class="space-y-1.5">
                                    <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Jerarquía / Rol</label>
                                    <select v-model="createForm.role" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-bold text-slate-700 outline-none focus:border-[#57572A] transition-all cursor-pointer appearance-none">
                                        <option value="usuario">Estudiante Corporativo</option>
                                        <option value="admin">Administrador de Sistema</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Estado de Cuenta</label>
                                    <select v-model="createForm.status" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-bold text-slate-700 outline-none focus:border-[#57572A] transition-all cursor-pointer appearance-none">
                                        <option value="activo">Habilitar Inmediatamente</option>
                                        <option value="inactivo">Baja Preventiva</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-50">
                                <button type="button" @click="showCreate = false; createForm.reset()" class="h-12 px-8 rounded-2xl text-sm font-bold text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all">
                                    Cancelar
                                </button>
                                <button type="submit" :disabled="createForm.processing" class="h-12 px-10 rounded-2xl bg-[#57572A] text-sm font-bold text-white shadow-xl shadow-[#57572A]/20 hover:scale-[1.02] active:scale-95 disabled:opacity-50 transition-all">
                                    {{ createForm.processing ? 'Registrando...' : 'Finalizar Alta' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Edit User Modal -->
        <Teleport to="body">
            <Transition name="modal-bounce">
                <div v-if="showEdit" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showEdit = false; editForm.reset()"></div>
                    <div class="relative w-full max-w-2xl rounded-[3rem] bg-white shadow-2xl overflow-hidden">
                        <div class="bg-slate-50 p-10 border-b border-slate-100 flex items-center gap-6">
                            <div :class="`w-20 h-20 rounded-[1.5rem] flex items-center justify-center text-3xl font-bold shadow-lg ${avatarColor(editTarget?.id ?? 0)}`">
                                {{ initials(editTarget?.name ?? '') }}
                            </div>
                            <div>
                                <h2 class="font-serif text-3xl text-slate-900 leading-tight">Actualizar <span class="italic">Perfil</span></h2>
                                <p class="text-slate-400 font-medium mt-1">{{ editTarget?.name }} • {{ editTarget?.email }}</p>
                            </div>
                            <button @click="showEdit = false; editForm.reset()" class="ml-auto w-10 h-10 rounded-full bg-slate-200/50 hover:bg-slate-200 flex items-center justify-center transition-colors">
                                <X class="h-5 w-5 text-slate-500" />
                            </button>
                        </div>
                        
                        <form @submit.prevent="submitEdit" class="p-10 space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label for="edit_name" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Identidad Digital</label>
                                    <input id="edit_name" name="name" v-model="editForm.name" autocomplete="name" required class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-[#57572A]/5 focus:border-[#57572A] transition-all" />
                                    <p v-if="editForm.errors.name" class="text-[10px] text-rose-500 font-bold">{{ editForm.errors.name }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="edit_email" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Dirección Electrónica</label>
                                    <input id="edit_email" name="email" v-model="editForm.email" type="email" autocomplete="email" required class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-[#57572A]/5 focus:border-[#57572A] transition-all" />
                                    <p v-if="editForm.errors.email" class="text-[10px] text-rose-500 font-bold">{{ editForm.errors.email }}</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label for="edit_telefono" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Teléfono de Contacto</label>
                                    <input id="edit_telefono" name="telefono" v-model="editForm.telefono" type="tel" autocomplete="tel" class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-[#57572A]/5 focus:border-[#57572A] transition-all" />
                                </div>
                                <div class="space-y-1.5">
                                    <label for="edit_password" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Restablecer Contraseña</label>
                                    <input id="edit_password" name="password" v-model="editForm.password" type="password" autocomplete="new-password" placeholder="Omitir para mantener actual" class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-[#57572A]/5 focus:border-[#57572A] transition-all" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                                <div class="space-y-1.5">
                                    <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Grado de Acceso</label>
                                    <select v-model="editForm.role" class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-bold text-slate-700 outline-none focus:border-[#57572A] transition-all appearance-none cursor-pointer">
                                        <option value="usuario">Estudiante</option>
                                        <option value="admin">Administrador</option>
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Estatus Administrativo</label>
                                    <select v-model="editForm.status" class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-bold text-slate-700 outline-none focus:border-[#57572A] transition-all appearance-none cursor-pointer">
                                        <option value="activo">Vigencia Plena</option>
                                        <option value="inactivo">Baja del Sistema</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-50">
                                <button type="button" @click="showEdit = false; editForm.reset()" class="h-12 px-8 rounded-2xl text-sm font-bold text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all">
                                    Descartar
                                </button>
                                <button type="submit" :disabled="editForm.processing" class="h-12 px-10 rounded-2xl bg-[#57572A] text-sm font-bold text-white shadow-xl shadow-[#57572A]/20 hover:scale-[1.02] active:scale-95 disabled:opacity-50 transition-all">
                                    {{ editForm.processing ? 'Actualizando...' : 'Guardar Cambios' }}
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
.modal-bounce-enter-active { animation: modal-bounce-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1); }
.modal-bounce-leave-active { animation: modal-bounce-in 0.3s reverse ease-in; }

@keyframes modal-bounce-in {
    0% { transform: scale(0.9); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1rem;
}
</style>
