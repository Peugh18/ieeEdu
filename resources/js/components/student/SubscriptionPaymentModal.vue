<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { X, Upload, CreditCard, CheckCircle2 } from 'lucide-vue-next';

const props = defineProps<{
    show: boolean;
    plan: {
        id: string;
        name: string;
        price: number;
        period: string;
    } | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm({
    plan: '',
    comprobante: null as File | null,
});

const fileInput = ref<HTMLInputElement | null>(null);
const previewUrl = ref<string | null>(null);

function handleFile(e: Event) {
    const target = e.target as HTMLInputElement;
    if (!target.files?.length) return;
    form.comprobante = target.files[0];
    previewUrl.value = URL.createObjectURL(target.files[0]);
}

function submit() {
    if (!props.plan) return;
    form.plan = props.plan.id;
    form.post(route('student.subscriptions.payment.store'), {
        forceFormData: true,
        onSuccess: () => {
            emit('close');
            form.reset();
            previewUrl.value = null;
        },
    });
}

function close() {
    emit('close');
    form.reset();
    form.clearErrors();
    previewUrl.value = null;
}
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="show && plan" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="close">
                <div class="bg-white rounded-[2rem] w-full max-w-md shadow-2xl overflow-hidden">
                    <!-- Header -->
                    <div class="bg-primary p-6 text-white relative overflow-hidden">
                        <div class="absolute inset-0 bg-white/5 opacity-50"></div>
                        <div class="relative z-10 flex items-start justify-between">
                            <div>
                                <p class="text-[10px] font-extrabold uppercase tracking-[0.25em] opacity-60 mb-1">Pago con comprobante</p>
                                <h2 class="text-2xl font-serif font-bold italic">{{ plan.name }}</h2>
                                <p class="mt-1 opacity-80 text-sm">S/ {{ plan.price }} <span class="opacity-60">/ {{ plan.period }}</span></p>
                            </div>
                            <button @click="close" class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition-colors">
                                <X class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-5">
                        <!-- Instructions -->
                        <div class="bg-slate-50 rounded-2xl p-4 space-y-2">
                            <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Instrucciones</p>
                            <ol class="text-sm text-slate-600 space-y-1.5 list-decimal list-inside">
                                <li>Realiza la transferencia a nuestra cuenta Yape/BCP.</li>
                                <li>Toma captura o foto del comprobante.</li>
                                <li>Súbela aquí. Te confirmaremos en menos de 24h.</li>
                            </ol>
                        </div>

                        <!-- File Upload -->
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-widest text-slate-400 mb-3">Comprobante de pago</p>
                            <div
                                @click="fileInput?.click()"
                                class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center cursor-pointer hover:border-primary/40 hover:bg-primary/5 transition-all group"
                                :class="previewUrl ? 'border-primary/30 bg-primary/5' : ''"
                            >
                                <div v-if="previewUrl" class="space-y-2">
                                    <CheckCircle2 class="w-8 h-8 text-primary mx-auto" />
                                    <p class="text-sm font-bold text-primary">Archivo seleccionado</p>
                                    <p class="text-xs text-slate-400">{{ form.comprobante?.name }}</p>
                                </div>
                                <div v-else class="space-y-2">
                                    <Upload class="w-8 h-8 text-slate-300 mx-auto group-hover:text-primary/50 transition-colors" />
                                    <p class="text-sm font-semibold text-slate-400">Click para subir comprobante</p>
                                    <p class="text-xs text-slate-300">JPG, PNG o PDF — máx 2MB</p>
                                </div>
                            </div>
                            <input ref="fileInput" type="file" accept=".jpg,.jpeg,.png,.pdf" class="hidden" @change="handleFile" />
                            <p v-if="form.errors.comprobante" class="text-xs text-red-500 mt-1">{{ form.errors.comprobante }}</p>
                        </div>

                        <p v-if="form.errors.plan" class="text-xs text-red-500">{{ form.errors.plan }}</p>
                    </div>

                    <!-- Footer -->
                    <div class="p-6 pt-0 flex gap-3">
                        <button @click="close" class="flex-1 py-3 rounded-xl border border-slate-200 text-sm font-bold text-slate-500 hover:bg-slate-50 transition-colors">
                            Cancelar
                        </button>
                        <button
                            @click="submit"
                            :disabled="!form.comprobante || form.processing"
                            class="flex-1 py-3 rounded-xl bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <CreditCard class="w-4 h-4" />
                            {{ form.processing ? 'Enviando...' : 'Enviar comprobante' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: all 0.3s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-from .bg-white, .modal-leave-to .bg-white { transform: translateY(20px); }
</style>
