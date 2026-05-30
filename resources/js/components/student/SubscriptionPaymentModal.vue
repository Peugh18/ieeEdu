<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { MessageCircle, Upload, CheckCircle2, Crown } from 'lucide-vue-next';
import type { SharedData } from '@/types';

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
});

function submitWhatsApp() {
    if (!props.plan) return;
    form.plan = props.plan.id;
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

function close() {
    emit('close');
    form.reset();
    form.clearErrors();
}
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100">
            <div v-if="show && plan" class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4" @click.self="close">
                <div class="bg-white rounded-[2.5rem] p-8 md:p-10 w-full max-w-lg shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-400 to-amber-600"></div>
                    <button @click="close" class="absolute top-5 right-5 w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-slate-700 transition-colors">✕</button>

                    <div class="mb-6">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-primary/60 mb-2">Membresía Premium</p>
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                <Crown class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">{{ plan.name }}</h2>
                                <p class="text-sm text-slate-500 mt-1">
                                    S/ {{ Number(plan.price).toFixed(2) }} <span class="text-slate-400">/ {{ plan.period }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <ol class="space-y-4 mb-8">
                        <li class="flex gap-3 items-start">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0"><MessageCircle class="w-4 h-4" /></div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">1. Coordina por WhatsApp</p>
                                <p class="text-xs text-slate-500 mt-0.5">Te indicamos a quién pagar (Yape/BCP) y resolvemos tus dudas.</p>
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
                                <p class="text-sm font-bold text-slate-800">3. Activación</p>
                                <p class="text-xs text-slate-500 mt-0.5">Validamos tu pago y activamos tu acceso Premium ilimitado.</p>
                            </div>
                        </li>
                    </ol>

                    <div v-if="form.errors.plan" class="text-rose-600 text-xs font-bold bg-rose-50 p-3 rounded-xl border border-rose-100 mb-4">
                        {{ form.errors.plan }}
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
                        <button type="button" @click="close" class="w-full py-3 text-xs font-bold text-slate-500 hover:text-slate-700">Cancelar</button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
