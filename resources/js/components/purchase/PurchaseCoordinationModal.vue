<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { MessageCircle, Upload, CheckCircle2 } from 'lucide-vue-next';
import type { SharedData } from '@/types';

const props = defineProps<{
    show: boolean;
    productType: 'course' | 'book';
    productId: number;
    productTitle: string;
    amount: number;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm({
    course_id: props.productType === 'course' ? props.productId : null as number | null,
    book_id: props.productType === 'book' ? props.productId : null as number | null,
});

function submitWhatsApp() {
    form.post(route('student.purchase-intent.store'), {
        preserveScroll: true,
        onSuccess: () => {
            const flash = usePage<SharedData>().props.flash as { open_whatsapp?: string };
            if (flash?.open_whatsapp) {
                window.open(flash.open_whatsapp, '_blank', 'noopener');
            }
            form.reset();
            emit('close');
        },
    });
}
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100">
            <div v-if="show" class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4" @click.self="emit('close')">
                <div class="bg-white rounded-[2.5rem] p-8 md:p-10 w-full max-w-lg shadow-2xl relative">
                    <button @click="emit('close')" class="absolute top-5 right-5 w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors">✕</button>

                    <div class="mb-6">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60 mb-2">Proceso de compra</p>
                        <h2 class="text-xl font-bold text-slate-900">{{ productType === 'book' ? 'Comprar libro' : 'Inscribirme al curso' }}</h2>
                        <p class="text-sm text-slate-500 mt-2">
                            <strong>{{ productTitle }}</strong> — S/ {{ Number(amount).toFixed(2) }}
                        </p>
                    </div>

                    <ol class="space-y-4 mb-8">
                        <li class="flex gap-3 items-start">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><MessageCircle class="w-4 h-4" /></div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">1. Coordina por WhatsApp</p>
                                <p class="text-xs text-slate-500 mt-0.5">Te indicamos a quién pagar{{ productType === 'book' ? ' y confirmamos tu dirección en Perú' : '' }}.</p>
                            </div>
                        </li>
                        <li class="flex gap-3 items-start">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0"><Upload class="w-4 h-4" /></div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">2. Sube tu comprobante</p>
                                <p class="text-xs text-slate-500 mt-0.5">Cuando el asesor te lo indique, en <strong>Mis Pagos</strong>.</p>
                            </div>
                        </li>
                        <li class="flex gap-3 items-start">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0"><CheckCircle2 class="w-4 h-4" /></div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">3. Validación</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ productType === 'book' ? 'Aprobamos tu pago y gestionamos el envío físico.' : 'Activamos tu acceso al aula virtual.' }}</p>
                            </div>
                        </li>
                    </ol>

                    <div v-if="form.errors.course_id || form.errors.book_id" class="text-rose-600 text-xs font-bold bg-rose-50 p-3 rounded-xl border border-rose-100 mb-4">
                        {{ form.errors.course_id || form.errors.book_id }}
                    </div>

                    <div class="flex flex-col gap-3">
                        <button
                            type="button"
                            @click="submitWhatsApp"
                            :disabled="form.processing"
                            class="w-full flex items-center justify-center gap-2 py-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-sm uppercase tracking-wider shadow-lg hover:shadow-xl transition-all disabled:opacity-50"
                        >
                            <MessageCircle class="w-5 h-5" />
                            {{ form.processing ? 'Registrando...' : 'Continuar por WhatsApp' }}
                        </button>
                        <button type="button" @click="emit('close')" class="w-full py-3 text-xs font-bold text-slate-500 hover:text-slate-700">Cancelar</button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
