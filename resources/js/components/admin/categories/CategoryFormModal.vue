<script setup lang="ts">
import { Tag, X, Check, AlertCircle } from 'lucide-vue-next';

defineProps<{
    show: boolean;
    form: { name: string; errors: Record<string, string>; processing: boolean; reset: () => void; clearErrors: () => void; post: (url: string, opts: object) => void; put: (url: string, opts: object) => void };
    editingId: number | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'submit'): void;
}>();
</script>

<template>
    <Teleport to="body">
        <Transition name="modal-bounce">
            <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm" @click.self="emit('close')">
                <div class="w-full max-w-md rounded-[2.5rem] bg-white shadow-2xl overflow-hidden flex flex-col transition-all max-h-[90vh]">
                    <div class="bg-primary p-8 text-white relative shrink-0">
                        <div class="absolute top-0 right-0 p-8 opacity-10"><Tag class="w-24 h-24" /></div>
                        <h2 class="font-serif text-3xl">{{ editingId ? 'Editar' : 'Nueva' }} <span class="italic underline decoration-white/20 underline-offset-8">Categoría</span></h2>
                        <p class="mt-3 text-outline-variant text-xs font-medium uppercase tracking-widest">Organización de catálogo académico</p>
                        <button @click="emit('close')" class="absolute top-6 right-6 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <form @submit.prevent="emit('submit')" class="p-8 space-y-6 overflow-y-auto">
                        <div class="space-y-2">
                            <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Título de la Categoría *</label>
                            <div class="relative group">
                                <Tag class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-300 transition-colors group-focus-within:text-primary" />
                                <input v-model="form.name" type="text" placeholder="Ej. Ingeniería Civil o Arquitectura"
                                    class="w-full h-14 bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-6 text-sm font-bold text-slate-700 outline-none focus:border-primary transition-all"
                                    :class="{ 'border-rose-500 bg-rose-50/10': form.errors.name }" required />
                            </div>
                            <p v-if="form.errors.name" class="mt-1.5 px-1 text-[10px] font-bold text-rose-500 uppercase tracking-widest">{{ form.errors.name }}</p>
                            <p v-else class="px-1 text-[9px] text-slate-400 italic font-medium">El identificador (slug) se generará al guardar.</p>
                        </div>

                        <div v-if="!editingId" class="rounded-2xl bg-primary/5 p-5 border border-primary/10 flex gap-3">
                            <AlertCircle class="h-5 w-5 text-primary flex-shrink-0" />
                            <p class="text-xs text-slate-600 font-medium leading-relaxed">Las categorías ayudan a los estudiantes a filtrar cursos más rápido en el panel principal.</p>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" @click="emit('close')" class="h-12 px-6 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all">Cancelar</button>
                            <button type="submit" :disabled="form.processing || !form.name"
                                class="h-12 px-10 rounded-2xl bg-primary text-sm font-black text-white shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 disabled:opacity-50 transition-all flex items-center justify-center gap-2">
                                <Check v-if="!form.processing" class="h-4 w-4" />
                                {{ editingId ? 'Guardar Cambios' : 'Generar Categoría' }}
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
</style>
