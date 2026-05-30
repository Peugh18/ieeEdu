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
                <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-[2rem] md:rounded-[3rem] bg-white shadow-2xl">
                    <div class="bg-gradient-to-br from-primary to-[#6a6a3b] p-10 text-white relative">
                        <div class="absolute top-0 right-0 p-10 opacity-10">
                            <UserPlus class="w-32 h-32" />
                        </div>
                        <h2 class="font-serif text-4xl leading-tight">Alta de <span class="italic underline decoration-white/20 underline-offset-8">Nuevo Usuario</span></h2>
                        <p class="mt-4 text-white/70 text-sm max-w-sm">Completa el expediente digital del usuario para habilitar el acceso a la plataforma.</p>
                        <button @click="handleClose" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                            <X class="h-5 w-5" />
                        </button>
                    </div>
                    
                    <form @submit.prevent="submitCreate" class="p-10 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label for="create_name" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Nombre del Perfil</label>
                                <input id="create_name" name="name" v-model="createForm.name" autocomplete="name" required placeholder="Ej: Dr. Manuel García" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all" />
                                <p v-if="createForm.errors.name" class="text-[10px] text-rose-500 font-bold pl-1">{{ createForm.errors.name }}</p>
                            </div>
                            <div class="space-y-1.5">
                                <label for="create_email" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Correo Electrónico</label>
                                <input id="create_email" name="email" v-model="createForm.email" type="email" autocomplete="email" required placeholder="ejemplo@iieedu.pe" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all" />
                                <p v-if="createForm.errors.email" class="text-[10px] text-rose-500 font-bold pl-1">{{ createForm.errors.email }}</p>
                            </div>
                            <div class="space-y-1.5">
                                <label for="create_telefono" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Teléfono Móvil</label>
                                <input id="create_telefono" name="telefono" v-model="createForm.telefono" type="tel" autocomplete="tel" placeholder="+51 9XX XXX XXX" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all" />
                            </div>
                            <div class="space-y-1.5">
                                <label for="create_password" class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Clave de Seguridad</label>
                                <input id="create_password" name="password" v-model="createForm.password" type="password" autocomplete="new-password" required placeholder="Mínimo 8 caracteres" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-medium outline-none focus:ring-4 focus:ring-primary/5 focus:border-primary transition-all" />
                                <p v-if="createForm.errors.password" class="text-[10px] text-rose-500 font-bold pl-1">{{ createForm.errors.password }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Jerarquía / Rol</label>
                                <select v-model="createForm.role" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-bold text-slate-700 outline-none focus:border-primary transition-all cursor-pointer appearance-none">
                                    <option value="usuario">Estudiante Corporativo</option>
                                    <option value="admin">Administrador de Sistema</option>
                                </select>
                            </div>
                            <div class="space-y-1.5">
                                <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Estado de Cuenta</label>
                                <select v-model="createForm.status" class="w-full h-12 bg-slate-50 border border-slate-200 rounded-2xl px-4 text-sm font-bold text-slate-700 outline-none focus:border-primary transition-all cursor-pointer appearance-none">
                                    <option value="activo">Habilitar Inmediatamente</option>
                                    <option value="inactivo">Baja Preventiva</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-50">
                            <button type="button" @click="handleClose" class="h-12 px-8 rounded-2xl text-sm font-bold text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="createForm.processing" class="h-12 px-10 rounded-2xl bg-primary text-sm font-bold text-white shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 disabled:opacity-50 transition-all">
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
