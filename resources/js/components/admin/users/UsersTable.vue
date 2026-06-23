<script setup lang="ts">
import { UserListItem } from '@/types/admin';
import { PaginationLink } from '@/types/pagination';
import { Link } from '@inertiajs/vue3';
import { Eye, GraduationCap, Mail, Pencil, PhoneCall, Search, ShieldCheck, Trash2 } from 'lucide-vue-next';

defineProps<{
    users: UserListItem[];
    total: number;
    paginationLinks: PaginationLink[];
}>();

const emit = defineEmits<{
    (e: 'toggleStatus', user: UserListItem): void;
    (e: 'edit', user: UserListItem): void;
    (e: 'destroy', user: UserListItem): void;
}>();

// ─── Helpers ──────────────────────────────────────────────────────
function initials(name: string) {
    return name
        .split(' ')
        .slice(0, 2)
        .map((w) => w[0])
        .join('')
        .toUpperCase();
}

const avatarColors = [
    'bg-indigo-50 text-indigo-700 ring-indigo-700/10',
    'bg-emerald-50 text-emerald-700 ring-emerald-700/10',
    'bg-amber-50 text-amber-700 ring-amber-700/10',
    'bg-rose-50 text-rose-700 ring-rose-700/10',
    'bg-blue-50 text-blue-700 ring-blue-700/10',
    'bg-purple-50 text-purple-700 ring-purple-700/10',
];

function avatarColor(id: number) {
    return avatarColors[id % avatarColors.length];
}

function formatDate(d: string) {
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <div class="overflow-hidden rounded-[2.5rem] border border-slate-100 bg-white shadow-sm">
        <!-- Data Header -->
        <div
            class="hidden grid-cols-12 gap-4 border-b border-slate-50 bg-slate-50/50 px-8 py-5 text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400 lg:grid"
        >
            <div class="col-span-4">Identidad del Usuario</div>
            <div class="col-span-2 text-center">Contacto</div>
            <div class="col-span-2 text-center">Privilegios</div>
            <div class="col-span-2 text-center">Acceso</div>
            <div class="col-span-2 text-right">Acciones</div>
        </div>

        <!-- Empty State -->
        <div v-if="!users.length" class="flex flex-col items-center justify-center space-y-4 py-24 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-50 text-slate-200">
                <Search class="h-8 w-8" />
            </div>
            <div>
                <h4 class="font-serif text-xl text-slate-900">Sin coincidencias</h4>
                <p class="mx-auto max-w-xs text-sm text-slate-400">
                    No hemos encontrado usuarios que cumplan con los criterios de búsqueda actuales.
                </p>
            </div>
        </div>

        <!-- Data Rows -->
        <div v-else class="divide-y divide-slate-50">
            <div
                v-for="user in users"
                :key="user.id"
                class="group grid grid-cols-1 items-center gap-4 px-8 py-6 transition-all duration-300 hover:bg-slate-50/80 lg:grid-cols-12"
            >
                <!-- Identity Column -->
                <div class="col-span-1 flex items-center gap-4 lg:col-span-4">
                    <div class="relative">
                        <div
                            :class="`flex h-14 w-14 items-center justify-center rounded-2xl text-lg font-bold shadow-inner ring-4 ring-white ${avatarColor(user.id)}`"
                        >
                            {{ initials(user.name) }}
                        </div>
                        <div
                            v-if="user.status === 'activo'"
                            class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full border-2 border-white bg-emerald-500"
                        ></div>
                        <div v-else class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full border-2 border-white bg-slate-300"></div>
                    </div>
                    <div class="min-w-0">
                        <h4 class="truncate text-base font-bold tracking-tight text-slate-900 transition-colors group-hover:text-primary">
                            {{ user.name }}
                        </h4>
                        <div class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-400">
                            <Mail class="h-3 w-3" />
                            {{ user.email }}
                        </div>
                    </div>
                </div>

                <!-- Contact Column -->
                <div class="col-span-1 flex justify-center lg:col-span-2">
                    <div
                        v-if="user.telefono"
                        class="flex items-center gap-2 rounded-xl border border-slate-200/50 bg-slate-50 px-3 py-1.5 text-sm font-medium text-slate-600"
                    >
                        <PhoneCall class="h-3 w-3 text-slate-400" />
                        {{ user.telefono }}
                    </div>
                    <span class="text-xs italic text-slate-300">— Sin teléfono —</span>
                </div>

                <!-- Privileges Column -->
                <div class="col-span-1 flex justify-center lg:col-span-2">
                    <span
                        v-if="user.role === 'admin'"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 ring-1 ring-inset ring-indigo-700/10"
                    >
                        <ShieldCheck class="h-3 w-3" />
                        ADMIN
                    </span>
                    <span
                        v-else
                        class="inline-flex items-center gap-1.5 rounded-xl bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 ring-1 ring-inset ring-blue-700/10"
                    >
                        <GraduationCap class="h-3 w-3" />
                        ESTUDIANTE
                    </span>
                </div>

                <!-- Access Column -->
                <div class="col-span-1 flex flex-col items-center gap-1 lg:col-span-2">
                    <button
                        @click="emit('toggleStatus', user)"
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
                    <span
                        class="text-[10px] font-bold uppercase tracking-wider"
                        :class="user.status === 'activo' ? 'text-emerald-600' : 'text-slate-400'"
                    >
                        {{ user.status === 'activo' ? 'Vigente' : 'Inactivo' }}
                    </span>
                </div>

                <!-- Actions Column -->
                <div class="col-span-1 flex justify-end gap-1.5 lg:col-span-2">
                    <Link
                        :href="route('admin.users.show', user.id)"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-300 hover:bg-indigo-50 hover:text-indigo-600"
                        title="Ficha Técnica"
                    >
                        <Eye class="h-5 w-5" />
                    </Link>
                    <button
                        @click="emit('edit', user)"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-300 hover:bg-blue-50 hover:text-blue-600"
                        title="Editar Perfil"
                    >
                        <Pencil class="h-5 w-5" />
                    </button>
                    <button
                        @click="emit('destroy', user)"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-300 hover:bg-rose-50 hover:text-rose-600"
                        title="Baja de Sistema"
                    >
                        <Trash2 class="h-5 w-5" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Data Footer / Pagination -->
        <div
            v-if="paginationLinks.length > 1"
            class="flex flex-col items-center justify-between gap-4 border-t border-slate-50 bg-slate-50/50 px-8 py-6 sm:flex-row"
        >
            <p class="text-sm font-medium text-slate-500">
                Mostrando <span class="font-bold text-slate-900">{{ users.length }}</span> de
                <span class="font-bold text-slate-900">{{ total }}</span> registros
            </p>
            <div class="flex items-center gap-1.5">
                <Link
                    v-for="link in paginationLinks"
                    :key="link.label"
                    :href="link.url"
                    class="flex h-10 min-w-[40px] items-center justify-center rounded-xl text-xs font-bold transition-all duration-300"
                    :class="
                        link.active
                            ? 'bg-primary text-white shadow-lg shadow-primary/20'
                            : 'border border-slate-200 bg-white text-slate-600 hover:border-primary hover:text-primary'
                    "
                    v-html="link.label"
                />
            </div>
        </div>
    </div>
</template>
