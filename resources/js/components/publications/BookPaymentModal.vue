<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { PublicationBook } from '@/types/publications';

const props = defineProps<{
    show: boolean;
    book: PublicationBook;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm({
    book_id: props.book.id,
    comprobante: null as File | null,
});

const fileInput = ref<HTMLInputElement | null>(null);

function submit() {
    form.post(route('student.payments.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
}

function handleFileChange(e: Event) {
    const target = e.target as HTMLInputElement;
    form.comprobante = target.files?.[0] ?? null;
}
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100">
            <div v-if="show" class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4" @click.self="emit('close')">
                <div class="bg-white rounded-[2.5rem] p-8 md:p-10 w-full max-w-lg shadow-2xl relative">
                    <button @click="emit('close')" class="absolute top-5 right-5 w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors">✕</button>
                    <h2 class="text-xl font-bold text-slate-900 mb-4">Registrar pago</h2>
                    <p class="text-sm text-slate-500 mb-6">
                        Estás a punto de registrar tu pago para el libro <strong>{{ book.title }}</strong> por un monto de <strong>S/ {{ Number(book.price).toFixed(2) }}</strong>.
                    </p>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Comprobante (Opcional)</label>
                            <input type="file" ref="fileInput" @change="handleFileChange" accept="image/*,.pdf" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all border border-slate-200 rounded-xl cursor-pointer" />
                            <div v-if="form.errors.comprobante" class="text-rose-500 text-xs mt-1">{{ form.errors.comprobante }}</div>
                        </div>

                        <div v-if="form.errors.book_id" class="text-rose-600 text-xs font-bold bg-rose-50 p-3 rounded-xl border border-rose-100">
                            {{ form.errors.book_id }}
                        </div>

                        <div class="flex justify-end gap-3 mt-8">
                            <button type="button" @click="emit('close')" class="px-5 py-2.5 text-xs font-bold text-slate-500 hover:bg-slate-50 rounded-xl transition-colors">Cancelar</button>
                            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 text-xs font-bold text-white bg-primary hover:bg-primary/95 rounded-xl shadow-md transition-all disabled:opacity-50">Registrar Pago</button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
