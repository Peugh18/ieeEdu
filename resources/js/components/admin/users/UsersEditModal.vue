<script setup lang="ts">
import { UserListItem } from '@/types/admin';
import { useForm } from '@inertiajs/vue3';
import { X } from 'lucide-vue-next';
import { watch } from 'vue';

const props = defineProps<{
    show: boolean;
    editTarget: UserListItem | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const editForm = useForm({
    name: '',
    email: '',
    telefono: '',
    role: 'usuario',
    status: 'activo',
    password: '',
});

watch(
    () => props.editTarget,
    (user) => {
        if (user) {
            editForm.name = user.name;
            editForm.email = user.email;
            editForm.telefono = user.telefono || '';
            editForm.role = user.role;
            editForm.status = user.status;
            editForm.password = '';
        } else {
            editForm.reset();
        }
    },
    { immediate: true },
);

function submitEdit() {
    if (!props.editTarget) return;
    editForm.put(route('admin.users.update', props.editTarget.id), {
        onSuccess: () => {
            emit('close');
            editForm.reset();
        },
    });
}

function handleClose() {
    emit('close');
    editForm.reset();
}

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
</script>

<template>
    <Teleport to="body">
        <Transition name="modal-bounce">
            <div v-if="show" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6">
                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="handleClose"></div>
                <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl md:rounded-[3rem]">
                    <div class="flex items-center gap-6 border-b border-slate-100 bg-slate-50 p-10">
                        <div
                            :class="`flex h-20 w-20 items-center justify-center rounded-[1.5rem] text-3xl font-bold shadow-lg ${avatarColor(editTarget?.id ?? 0)}`"
                        >
                            {{ initials(editTarget?.name ?? '') }}
                        </div>
                        <div>
                            <h2 class="font-serif text-3xl leading-tight text-slate-900">Actualizar <span class="italic">Perfil</span></h2>
                            <p class="mt-1 font-medium text-slate-400">{{ editTarget?.name }} • {{ editTarget?.email }}</p>
                        </div>
                        <button
                            @click="handleClose"
                            class="ml-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-200/50 transition-colors hover:bg-slate-200"
                        >
                            <X class="h-5 w-5 text-slate-500" />
                        </button>
                    </div>

                    <form @submit.prevent="submitEdit" class="space-y-8 p-10">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-1.5">
                                <label for="edit_name" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                    >Identidad Digital</label
                                >
                                <input
                                    id="edit_name"
                                    name="name"
                                    v-model="editForm.name"
                                    autocomplete="name"
                                    required
                                    class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                                />
                                <p v-if="editForm.errors.name" class="text-[10px] font-bold text-rose-500">{{ editForm.errors.name }}</p>
                            </div>
                            <div class="space-y-1.5">
                                <label for="edit_email" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                    >Dirección Electrónica</label
                                >
                                <input
                                    id="edit_email"
                                    name="email"
                                    v-model="editForm.email"
                                    type="email"
                                    autocomplete="email"
                                    required
                                    class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                                />
                                <p v-if="editForm.errors.email" class="text-[10px] font-bold text-rose-500">{{ editForm.errors.email }}</p>
                            </div>
                            <div class="space-y-1.5">
                                <label for="edit_telefono" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                    >Teléfono de Contacto</label
                                >
                                <input
                                    id="edit_telefono"
                                    name="telefono"
                                    v-model="editForm.telefono"
                                    type="tel"
                                    autocomplete="tel"
                                    class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                                />
                            </div>
                            <div class="space-y-1.5">
                                <label for="edit_password" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                    >Restablecer Contraseña</label
                                >
                                <input
                                    id="edit_password"
                                    name="password"
                                    v-model="editForm.password"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="Omitir para mantener actual"
                                    class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 pt-4 md:grid-cols-2">
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Grado de Acceso</label>
                                <select
                                    v-model="editForm.role"
                                    class="h-12 w-full cursor-pointer appearance-none rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 outline-none transition-all focus:border-primary"
                                >
                                    <option value="usuario">Estudiante</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Estatus Administrativo</label>
                                <select
                                    v-model="editForm.status"
                                    class="h-12 w-full cursor-pointer appearance-none rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 outline-none transition-all focus:border-primary"
                                >
                                    <option value="activo">Vigencia Plena</option>
                                    <option value="inactivo">Baja del Sistema</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-slate-50 pt-6">
                            <button
                                type="button"
                                @click="handleClose"
                                class="h-12 rounded-2xl px-8 text-sm font-bold text-slate-400 transition-all hover:bg-slate-50 hover:text-slate-600"
                            >
                                Descartar
                            </button>
                            <button
                                type="submit"
                                :disabled="editForm.processing"
                                class="h-12 rounded-2xl bg-primary px-10 text-sm font-bold text-white shadow-xl shadow-primary/20 transition-all hover:scale-[1.02] active:scale-95 disabled:opacity-50"
                            >
                                {{ editForm.processing ? 'Actualizando...' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-bounce-enter-active {
    animation: modal-bounce-in 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.modal-bounce-leave-active {
    animation: modal-bounce-in 0.3s reverse ease-in;
}

@keyframes modal-bounce-in {
    0% {
        transform: scale(0.9);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1rem;
}
</style>
