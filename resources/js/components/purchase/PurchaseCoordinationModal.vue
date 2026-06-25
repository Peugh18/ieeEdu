<script setup lang="ts">
import type { SharedData } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { CheckCircle2, MessageCircle, Upload } from 'lucide-vue-next';

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
    course_id: props.productType === 'course' ? props.productId : (null as number | null),
    book_id: props.productType === 'book' ? props.productId : (null as number | null),
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
                        ✕
                    </button>

                    <div class="mb-6">
                        <p class="mb-2 text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Proceso de compra</p>
                        <h2 class="text-xl font-bold text-slate-900">{{ productType === 'book' ? 'Comprar libro' : 'Inscribirme al curso' }}</h2>
                        <p class="mt-2 text-sm text-slate-500">
                            <strong>{{ productTitle }}</strong> — S/ {{ Number(amount).toFixed(2) }}
                        </p>
                    </div>

                    <ol class="mb-8 space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                <MessageCircle class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">1. Coordina por WhatsApp</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    Te indicamos a quién pagar{{ productType === 'book' ? ' y confirmamos tu dirección en Perú' : '' }}.
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                <Upload class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">2. Sube tu comprobante</p>
                                <p class="mt-0.5 text-xs text-slate-500">Cuando el asesor te lo indique, en <strong>Mis Pagos</strong>.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                <CheckCircle2 class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">3. Validación</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{
                                        productType === 'book'
                                            ? 'Aprobamos tu pago y gestionamos el envío físico.'
                                            : 'Activamos tu acceso al aula virtual.'
                                    }}
                                </p>
                            </div>
                        </li>
                    </ol>

                    <div
                        v-if="form.errors.course_id || form.errors.book_id"
                        class="mb-4 rounded-xl border border-rose-100 bg-rose-50 p-3 text-xs font-bold text-rose-600"
                    >
                        {{ form.errors.course_id || form.errors.book_id }}
                    </div>

                    <div class="flex flex-col gap-3">
                        <button
                            type="button"
                            @click="submitWhatsApp"
                            :disabled="form.processing"
                            class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 py-4 text-sm font-bold uppercase tracking-wider text-white shadow-lg transition-all hover:shadow-xl disabled:opacity-50"
                        >
                            <MessageCircle class="h-5 w-5" />
                            {{ form.processing ? 'Registrando...' : 'Continuar por WhatsApp' }}
                        </button>
                        <button type="button" @click="emit('close')" class="w-full py-3 text-xs font-bold text-slate-500 hover:text-slate-700">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
