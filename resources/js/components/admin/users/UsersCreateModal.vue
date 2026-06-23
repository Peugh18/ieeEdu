<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { UserPlus, X } from 'lucide-vue-next';

defineProps<{
    show: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

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
            emit('close');
            createForm.reset();
        },
    });
}

function handleClose() {
    emit('close');
    createForm.reset();
}
</script>

<template>
    <Teleport to="body">
        <Transition name="modal-bounce">
            <div v-if="show" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6">
                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="handleClose"></div>
                <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-[2rem] bg-white shadow-2xl md:rounded-[3rem]">
                    <div class="relative bg-gradient-to-br from-primary to-[#6a6a3b] p-10 text-white">
                        <div class="absolute right-0 top-0 p-10 opacity-10">
                            <UserPlus class="h-32 w-32" />
                        </div>
                        <h2 class="font-serif text-4xl leading-tight">
                            Alta de <span class="italic underline decoration-white/20 underline-offset-8">Nuevo Usuario</span>
                        </h2>
                        <p class="mt-4 max-w-sm text-sm text-white/70">
                            Completa el expediente digital del usuario para habilitar el acceso a la plataforma.
                        </p>
                        <button
                            @click="handleClose"
                            class="absolute right-6 top-6 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 transition-colors hover:bg-white/20"
                        >
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <form @submit.prevent="submitCreate" class="space-y-8 p-10">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-1.5">
                                <label for="create_name" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                    >Nombre del Perfil</label
                                >
                                <input
                                    id="create_name"
                                    name="name"
                                    v-model="createForm.name"
                                    autocomplete="name"
                                    required
                                    placeholder="Ej: Dr. Manuel García"
                                    class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                                />
                                <p v-if="createForm.errors.name" class="pl-1 text-[10px] font-bold text-rose-500">{{ createForm.errors.name }}</p>
                            </div>
                            <div class="space-y-1.5">
                                <label for="create_email" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                    >Correo Electrónico</label
                                >
                                <input
                                    id="create_email"
                                    name="email"
                                    v-model="createForm.email"
                                    type="email"
                                    autocomplete="email"
                                    required
                                    placeholder="ejemplo@iieedu.pe"
                                    class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                                />
                                <p v-if="createForm.errors.email" class="pl-1 text-[10px] font-bold text-rose-500">{{ createForm.errors.email }}</p>
                            </div>
                            <div class="space-y-1.5">
                                <label for="create_telefono" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                    >Teléfono Móvil</label
                                >
                                <input
                                    id="create_telefono"
                                    name="telefono"
                                    v-model="createForm.telefono"
                                    type="tel"
                                    autocomplete="tel"
                                    placeholder="+51 9XX XXX XXX"
                                    class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                                />
                            </div>
                            <div class="space-y-1.5">
                                <label for="create_password" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400"
                                    >Clave de Seguridad</label
                                >
                                <input
                                    id="create_password"
                                    name="password"
                                    v-model="createForm.password"
                                    type="password"
                                    autocomplete="new-password"
                                    required
                                    placeholder="Mínimo 8 caracteres"
                                    class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-medium outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/5"
                                />
                                <p v-if="createForm.errors.password" class="pl-1 text-[10px] font-bold text-rose-500">
                                    {{ createForm.errors.password }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-6 pt-4 md:grid-cols-2">
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Jerarquía / Rol</label>
                                <select
                                    v-model="createForm.role"
                                    class="h-12 w-full cursor-pointer appearance-none rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold text-slate-700 outline-none transition-all focus:border-primary"
                                >
                                    <option value="usuario">Estudiante Corporativo</option>
                                    <option value="admin">Administrador de Sistema</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Estado de Cuenta</label>
                                <select
                                    v-model="createForm.status"
                                    class="h-12 w-full cursor-pointer appearance-none rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm font-bold text-slate-700 outline-none transition-all focus:border-primary"
                                >
                                    <option value="activo">Habilitar Inmediatamente</option>
                                    <option value="inactivo">Baja Preventiva</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-slate-50 pt-6">
                            <button
                                type="button"
                                @click="handleClose"
                                class="h-12 rounded-2xl px-8 text-sm font-bold text-slate-400 transition-all hover:bg-slate-50 hover:text-slate-600"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="createForm.processing"
                                class="h-12 rounded-2xl bg-primary px-10 text-sm font-bold text-white shadow-xl shadow-primary/20 transition-all hover:scale-[1.02] active:scale-95 disabled:opacity-50"
                            >
                                {{ createForm.processing ? 'Registrando...' : 'Finalizar Alta' }}
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
