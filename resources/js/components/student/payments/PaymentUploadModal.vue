<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { X, Upload, FileCheck } from 'lucide-vue-next';
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

watch(() => props.show, (val) => {
    if (!val) { form.reset(); fileName.value = ''; }
});

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
            <div v-if="show" class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4" @click.self="emit('close')">
                <div class="bg-white rounded-[2.5rem] p-8 md:p-10 w-full max-w-lg shadow-2xl relative">
                    <button @click="emit('close')" class="absolute top-5 right-5 w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors"><X class="w-5 h-5" /></button>
                    <h3 class="text-xl font-serif font-bold text-slate-900 mb-2">Subir comprobante</h3>
                    <p class="text-sm text-slate-500 mb-8">Adjunta una imagen o PDF de tu depósito o transferencia.</p>

                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:border-primary/30 transition-colors cursor-pointer relative" @click="fileInputRef?.click()">
                        <input ref="fileInputRef" type="file" accept="image/*,.pdf" class="hidden" @change="onFileChange" />
                        <div v-if="!fileName" class="space-y-3">
                            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto"><Upload class="w-5 h-5 text-slate-400" /></div>
                            <p class="text-sm font-medium text-slate-600">Haz clic para seleccionar archivo</p>
                            <p class="text-xs text-slate-400">JPG, PNG o PDF · Máx 5MB</p>
                        </div>
                        <div v-else class="flex items-center justify-center gap-3 text-primary">
                            <FileCheck class="w-5 h-5" />
                            <span class="text-sm font-bold">{{ fileName }}</span>
                        </div>
                    </div>
                    <p v-if="form.errors.comprobante" class="text-[10px] font-bold text-rose-500 uppercase mt-2">{{ form.errors.comprobante }}</p>

                    <div class="flex gap-3 mt-8">
                        <button @click="emit('close')" class="flex-1 h-14 rounded-2xl border border-slate-200 text-sm font-bold text-slate-600 hover:bg-slate-50 transition-colors">Cancelar</button>
                        <button @click="submit" :disabled="form.processing || !form.comprobante" class="flex-1 h-14 rounded-2xl bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-all disabled:opacity-50">{{ form.processing ? 'Enviando...' : 'Confirmar' }}</button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
