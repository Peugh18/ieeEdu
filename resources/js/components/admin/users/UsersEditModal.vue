<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { X } from 'lucide-vue-next';
import { UserListItem } from '@/types/admin';

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

watch(() => props.editTarget, (user) => {
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
}, { immediate: true });

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
</script>

<template>
    <Teleport to="body">
        <Transition name="modal-bounce">
            <div v-if="show" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6">
                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="handleClose"></div>
                <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-[2rem] md:rounded-[3rem] bg-white shadow-2xl">
                    <div class="bg-slate-50 p-10 border-b border-slate-100 flex items-center gap-6">
                        <div :class="`w-20 h-20 rounded-[1.5rem] flex items-center justify-center text-3xl font-bold shadow-lg ${avatarColor(editTarget?.id ?? 0)}`">
                            {{ initials(editTarget?.name ?? '') }}
                        </div>
                        <div>
                            <h2 class="font-serif text-3xl text-slate-900 leading-tight">Actualizar <span class="italic">Perfil</span></h2>
                            <p class="text-slate-400 font-medium mt-1">{{ editTarget?.name }} • {{ editTarget?.email }}</p>
                        </div>
                        <button @click="handleClose" class="ml-auto w-10 h-10 rounded-full bg-slate-200/50 hover:bg-slate-200 flex items-center justify-center transition-colors">
                            <X class="h-5 w-5 text-slate-500" />
                        </button>
                    </div>
                    
                    <form @submit.prevent="submitEdit" class="p-10 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label for="edit_name" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Identidad Digital</label>
                                <input id="edit_name" name="name" v-model="editForm.name" autocomplete="name" required class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all" />
                                <p v-if="editForm.errors.name" class="text-[10px] text-rose-500 font-bold">{{ editForm.errors.name }}</p>
                            </div>
                            <div class="space-y-1.5">
                                <label for="edit_email" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Dirección Electrónica</label>
                                <input id="edit_email" name="email" v-model="editForm.email" type="email" autocomplete="email" required class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all" />
                                <p v-if="editForm.errors.email" class="text-[10px] text-rose-500 font-bold">{{ editForm.errors.email }}</p>
                            </div>
                            <div class="space-y-1.5">
                                <label for="edit_telefono" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Teléfono de Contacto</label>
                                <input id="edit_telefono" name="telefono" v-model="editForm.telefono" type="tel" autocomplete="tel" class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all" />
                            </div>
                            <div class="space-y-1.5">
                                <label for="edit_password" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Restablecer Contraseña</label>
                                <input id="edit_password" name="password" v-model="editForm.password" type="password" autocomplete="new-password" placeholder="Omitir para mantener actual" class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Grado de Acceso</label>
                                <select v-model="editForm.role" class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-bold text-slate-700 outline-none focus:border-primary transition-all appearance-none cursor-pointer">
                                    <option value="usuario">Estudiante</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Estatus Administrativo</label>
                                <select v-model="editForm.status" class="w-full h-12 bg-white border border-slate-200 rounded-2xl px-4 text-sm font-bold text-slate-700 outline-none focus:border-primary transition-all appearance-none cursor-pointer">
                                    <option value="activo">Vigencia Plena</option>
                                    <option value="inactivo">Baja del Sistema</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-50">
                            <button type="button" @click="handleClose" class="h-12 px-8 rounded-2xl text-sm font-bold text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all">
                                Descartar
                            </button>
                            <button type="submit" :disabled="editForm.processing" class="h-12 px-10 rounded-2xl bg-primary text-sm font-bold text-white shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 disabled:opacity-50 transition-all">
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
