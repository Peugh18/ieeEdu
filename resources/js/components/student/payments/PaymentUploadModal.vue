<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { FileCheck, Upload, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    show: boolean;
    paymentId: number | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm({
    comprobante: null as File | null,
});

const fileName = ref('');
const fileInputRef = ref<HTMLInputElement | null>(null);

watch(
    () => props.show,
    (val) => {
        if (!val) {
            form.reset();
            fileName.value = '';
        }
    },
);

function onFileChange(e: Event) {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0] ?? null;
    form.comprobante = file;
    fileName.value = file?.name ?? '';
}

function submit() {
    if (!props.paymentId) return;
    form.post(route('student.payments.comprobante', { payment: props.paymentId }), {
        forceFormData: true,
        onSuccess: () => emit('close'),
    });
}
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100">
            <div
                v-if="show"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm"
                @click.self="emit('close')"
            >
                <div class="relative w-full max-w-lg rounded-[2.5rem] bg-white p-8 shadow-2xl md:p-10">
                    <button
                        @click="emit('close')"
                        class="absolute right-5 top-5 flex h-10 w-10 items-center justify-center rounded-full bg-slate-50 text-slate-400 transition-colors hover:text-slate-700"
                    >
                        <X class="h-5 w-5" />
                    </button>
                    <h3 class="mb-2 font-serif text-xl font-bold text-slate-900">Subir comprobante</h3>
                    <p class="mb-8 text-sm text-slate-500">Adjunta una imagen o PDF de tu depósito o transferencia.</p>

                    <div
                        class="relative cursor-pointer rounded-2xl border-2 border-dashed border-slate-200 p-8 text-center transition-colors hover:border-primary/30"
                        @click="fileInputRef?.click()"
                    >
                        <input ref="fileInputRef" type="file" accept="image/*,.pdf" class="hidden" @change="onFileChange" />
                        <div v-if="!fileName" class="space-y-3">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-50">
                                <Upload class="h-5 w-5 text-slate-400" />
                            </div>
                            <p class="text-sm font-medium text-slate-600">Haz clic para seleccionar archivo</p>
                            <p class="text-xs text-slate-400">JPG, PNG o PDF · Máx 5MB</p>
                        </div>
                        <div v-else class="flex items-center justify-center gap-3 text-primary">
                            <FileCheck class="h-5 w-5" />
                            <span class="text-sm font-bold">{{ fileName }}</span>
                        </div>
                    </div>
                    <p v-if="form.errors.comprobante" class="mt-2 text-[10px] font-bold uppercase text-rose-500">{{ form.errors.comprobante }}</p>

                    <div class="mt-8 flex gap-3">
                        <button
                            @click="emit('close')"
                            class="h-14 flex-1 rounded-2xl border border-slate-200 text-sm font-bold text-slate-600 transition-colors hover:bg-slate-50"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="submit"
                            :disabled="form.processing || !form.comprobante"
                            class="h-14 flex-1 rounded-2xl bg-primary text-sm font-bold text-white transition-all hover:bg-primary/90 disabled:opacity-50"
                        >
                            {{ form.processing ? 'Enviando...' : 'Confirmar' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
