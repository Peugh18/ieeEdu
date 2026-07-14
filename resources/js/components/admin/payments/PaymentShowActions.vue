<script setup lang="ts">
import { CheckCircle2, Undo2, XCircle } from 'lucide-vue-next';

defineProps<{ 
    status: string;
    productType?: 'course' | 'book' | 'membership' | null;
}>();
const emit = defineEmits<{ (e: 'approve'): void; (e: 'reject'): void; (e: 'revert'): void }>();
</script>

<template>
    <div class="rounded-3xl border border-outline-variant/10 bg-white p-6 shadow-sm">
        <p class="mb-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Acciones</p>

        <div v-if="status === 'aprobado'" class="space-y-3">
            <div class="rounded-2xl bg-emerald-50 p-4 text-center">
                <CheckCircle2 class="mx-auto mb-2 h-8 w-8 text-emerald-500" />
                <p class="text-sm font-bold text-emerald-700">
                    {{ 
                        productType === 'book' ? 'Pedido registrado' : 
                        productType === 'membership' ? 'Membresía activa' : 
                        'Acceso habilitado' 
                    }}
                </p>
                <p class="mt-1 text-xs text-emerald-600">
                    {{ 
                        productType === 'book' ? 'El pedido de envío físico ha sido programado' : 
                        productType === 'membership' ? 'El estudiante tiene membresía Premium activa' : 
                        'El estudiante tiene acceso activo al curso' 
                    }}
                </p>
            </div>
            <button
                @click="emit('revert')"
                class="flex w-full items-center justify-center gap-2 rounded-2xl border border-amber-200 bg-amber-50 py-3 text-sm font-bold text-amber-700 transition-colors hover:bg-amber-100"
            >
                <Undo2 class="h-4 w-4" /> Revertir aprobación
            </button>
        </div>

        <div v-else class="space-y-3">
            <div v-if="status === 'rechazado'" class="rounded-2xl bg-red-50 p-4 text-center mb-3">
                <XCircle class="mx-auto mb-2 h-8 w-8 text-red-500" />
                <p class="text-sm font-bold text-red-700">Pago rechazado</p>
                <p class="mt-1 text-xs text-red-600">El pago fue rechazado. El estudiante puede volver a subir un comprobante corregido.</p>
            </div>

            <button
                @click="emit('approve')"
                class="w-full rounded-2xl bg-emerald-600 py-3 text-sm font-bold text-white shadow transition-opacity hover:opacity-90"
            >
                ✓ Aprobar y {{ 
                    productType === 'book' ? 'confirmar pedido' : 
                    productType === 'membership' ? 'activar membresía' : 
                    'desbloquear curso' 
                }}
            </button>
            <button
                v-if="status !== 'rechazado'"
                @click="emit('reject')"
                class="w-full rounded-2xl border border-red-200 bg-red-50 py-3 text-sm font-bold text-red-600 transition-colors hover:bg-red-100"
            >
                ✕ Rechazar pago
            </button>
        </div>
    </div>
</template>
