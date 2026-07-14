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

const user = usePage<SharedData>().props.auth.user;

const form = useForm({
    course_id: props.productType === 'course' ? props.productId : (null as number | null),
    book_id: props.productType === 'book' ? props.productId : (null as number | null),
    department: '',
    province: '',
    district: '',
    shipping_address: '',
    shipping_phone: user?.telefono || '',
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

                     <ol class="mb-6 space-y-3">
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                <MessageCircle class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">1. Coordina por WhatsApp</p>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ productType === 'book' ? 'Confirmamos los datos de pago y tu dirección de envío en WhatsApp.' : 'Te indicamos los datos de pago.' }}
                                </p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                                <Upload class="h-4 w-4" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">2. Sube tu comprobante</p>
                                <p class="mt-0.5 text-xs text-slate-500">Cuando realices la transferencia, regístrala en <strong>Mis Pagos</strong>.</p>
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
                                            ? 'Aprobamos tu pago y procesamos el envío físico.'
                                            : 'Activamos tu acceso al aula virtual.'
                                    }}
                                </p>
                            </div>
                        </li>
                    </ol>

                    <!-- Shipping Address Form (Only for physical books) -->
                    <div v-if="productType === 'book'" class="mb-6 space-y-4 rounded-2xl border border-slate-100 bg-slate-50 p-5 text-left">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Dirección de despacho (Perú)</p>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase tracking-wider text-slate-500">Departamento</label>
                                <input
                                    v-model="form.department"
                                    type="text"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-800 outline-none focus:border-primary"
                                    placeholder="Ej: La Libertad"
                                />
                                <span v-if="form.errors.department" class="text-[9px] text-rose-500 font-semibold">{{ form.errors.department }}</span>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase tracking-wider text-slate-500">Provincia</label>
                                <input
                                    v-model="form.province"
                                    type="text"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-800 outline-none focus:border-primary"
                                    placeholder="Ej: Trujillo"
                                />
                                <span v-if="form.errors.province" class="text-[9px] text-rose-500 font-semibold">{{ form.errors.province }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase tracking-wider text-slate-500">Distrito</label>
                                <input
                                    v-model="form.district"
                                    type="text"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-800 outline-none focus:border-primary"
                                    placeholder="Ej: Víctor Larco"
                                />
                                <span v-if="form.errors.district" class="text-[9px] text-rose-500 font-semibold">{{ form.errors.district }}</span>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[9px] font-black uppercase tracking-wider text-slate-500">Teléfono Contacto</label>
                                <input
                                    v-model="form.shipping_phone"
                                    type="text"
                                    required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-800 outline-none focus:border-primary"
                                    placeholder="Ej: 958481351"
                                />
                                <span v-if="form.errors.shipping_phone" class="text-[9px] text-rose-500 font-semibold">{{ form.errors.shipping_phone }}</span>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[9px] font-black uppercase tracking-wider text-slate-500">Dirección de Entrega</label>
                            <input
                                v-model="form.shipping_address"
                                type="text"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs text-slate-800 outline-none focus:border-primary"
                                placeholder="Calle, Av, Nro, Urb, Dpto..."
                            />
                            <span v-if="form.errors.shipping_address" class="text-[9px] text-rose-500 font-semibold">{{ form.errors.shipping_address }}</span>
                        </div>
                    </div>

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
