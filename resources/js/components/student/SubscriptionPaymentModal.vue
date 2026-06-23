<script setup lang="ts">
import type { SharedData } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';
import { CheckCircle2, Crown, MessageCircle, Upload } from 'lucide-vue-next';

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
            <div
                v-if="show && plan"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm"
                @click.self="close"
            >
                <div class="relative w-full max-w-lg overflow-hidden rounded-[2.5rem] bg-white p-8 shadow-2xl md:p-10">
                    <div class="absolute left-0 right-0 top-0 h-1.5 bg-gradient-to-r from-amber-400 to-amber-600"></div>
                    <button
                        @click="close"
                        class="absolute right-5 top-5 flex h-10 w-10 items-center justify-center rounded-full bg-slate-50 text-slate-400 transition-colors hover:text-slate-700"
                    >
                        ✕
                    </button>

                    <div class="mb-6">
                        <p class="mb-2 text-[10px] font-black uppercase tracking-[0.2em] text-primary/60">Membresía Premium</p>
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                <Crown class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">{{ plan.name }}</h2>
                                <p class="mt-1 text-sm text-slate-500">
                                    S/ {{ Number(plan.price).toFixed(2) }} <span class="text-slate-400">/ {{ plan.period }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <ol class="mb-8 space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                <MessageCircle class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">1. Coordina por WhatsApp</p>
                                <p class="mt-0.5 text-xs text-slate-500">Te indicamos a quién pagar (Yape/BCP) y resolvemos tus dudas.</p>
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
                                <p class="text-sm font-bold text-slate-800">3. Activación</p>
                                <p class="mt-0.5 text-xs text-slate-500">Validamos tu pago y activamos tu acceso Premium ilimitado.</p>
                            </div>
                        </li>
                    </ol>

                    <div v-if="form.errors.plan" class="mb-4 rounded-xl border border-rose-100 bg-rose-50 p-3 text-xs font-bold text-rose-600">
                        {{ form.errors.plan }}
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
                        <button type="button" @click="close" class="w-full py-3 text-xs font-bold text-slate-500 hover:text-slate-700">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
