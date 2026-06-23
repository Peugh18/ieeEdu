<script setup lang="ts">
import { AlertCircle, Check, Tag, X } from 'lucide-vue-next';

defineProps<{
    show: boolean;
    form: {
        name: string;
        errors: Record<string, string>;
        processing: boolean;
        reset: () => void;
        clearErrors: () => void;
        post: (url: string, opts: object) => void;
        put: (url: string, opts: object) => void;
    };
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
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm"
                @click.self="emit('close')"
            >
                <div class="flex max-h-[90vh] w-full max-w-md flex-col overflow-hidden rounded-[2.5rem] bg-white shadow-2xl transition-all">
                    <div class="relative shrink-0 bg-primary p-8 text-white">
                        <div class="absolute right-0 top-0 p-8 opacity-10"><Tag class="h-24 w-24" /></div>
                        <h2 class="font-serif text-3xl">
                            {{ editingId ? 'Editar' : 'Nueva' }}
                            <span class="italic underline decoration-white/20 underline-offset-8">Categoría</span>
                        </h2>
                        <p class="mt-3 text-xs font-medium uppercase tracking-widest text-outline-variant">Organización de catálogo académico</p>
                        <button
                            @click="emit('close')"
                            class="absolute right-6 top-6 flex h-10 w-10 items-center justify-center rounded-full bg-white/10 transition-colors hover:bg-white/20"
                        >
                            <X class="h-5 w-5" />
                        </button>
                    </div>

                    <form @submit.prevent="emit('submit')" class="space-y-6 overflow-y-auto p-8">
                        <div class="space-y-2">
                            <label class="px-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Título de la Categoría *</label>
                            <div class="group relative">
                                <Tag
                                    class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-300 transition-colors group-focus-within:text-primary"
                                />
                                <input
                                    v-model="form.name"
                                    type="text"
                                    placeholder="Ej. Ingeniería Civil o Arquitectura"
                                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-6 text-sm font-bold text-slate-700 outline-none transition-all focus:border-primary"
                                    :class="{ 'border-rose-500 bg-rose-50/10': form.errors.name }"
                                    required
                                />
                            </div>
                            <p v-if="form.errors.name" class="mt-1.5 px-1 text-[10px] font-bold uppercase tracking-widest text-rose-500">
                                {{ form.errors.name }}
                            </p>
                            <p v-else class="px-1 text-[9px] font-medium italic text-slate-400">El identificador (slug) se generará al guardar.</p>
                        </div>

                        <div v-if="!editingId" class="flex gap-3 rounded-2xl border border-primary/10 bg-primary/5 p-5">
                            <AlertCircle class="h-5 w-5 flex-shrink-0 text-primary" />
                            <p class="text-xs font-medium leading-relaxed text-slate-600">
                                Las categorías ayudan a los estudiantes a filtrar cursos más rápido en el panel principal.
                            </p>
                        </div>

                        <div class="flex justify-end gap-3 pt-4">
                            <button
                                type="button"
                                @click="emit('close')"
                                class="h-12 rounded-2xl px-6 text-xs font-black uppercase tracking-widest text-slate-400 transition-all hover:bg-slate-50 hover:text-slate-600"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing || !form.name"
                                class="flex h-12 items-center justify-center gap-2 rounded-2xl bg-primary px-10 text-sm font-black text-white shadow-xl shadow-primary/20 transition-all hover:scale-[1.02] active:scale-95 disabled:opacity-50"
                            >
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
</style>
