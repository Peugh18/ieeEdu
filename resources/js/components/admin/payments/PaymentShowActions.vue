<script setup lang="ts">
import { CheckCircle2 } from 'lucide-vue-next';

defineProps<{ status: string }>();
const emit = defineEmits<{ (e: 'approve'): void; (e: 'reject'): void; }>();
</script>

<template>
    <div class="rounded-3xl border border-outline-variant/10 bg-white p-6 shadow-sm">
        <p class="mb-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Acciones</p>

        <div v-if="status === 'aprobado'" class="rounded-2xl bg-emerald-50 p-4 text-center">
            <CheckCircle2 class="mx-auto mb-2 h-8 w-8 text-emerald-500" />
            <p class="text-sm font-bold text-emerald-700">Curso desbloqueado</p>
            <p class="mt-1 text-xs text-emerald-600">El estudiante ya tiene acceso</p>
        </div>

        <div v-else class="space-y-3">
            <button @click="emit('approve')" class="w-full rounded-2xl bg-emerald-600 py-3 text-sm font-bold text-white shadow hover:opacity-90 transition-opacity">
                ✓ Aprobar y desbloquear curso
            </button>
            <button
                v-if="status !== 'rechazado'"
                @click="emit('reject')"
                class="w-full rounded-2xl border border-red-200 bg-red-50 py-3 text-sm font-bold text-red-600 hover:bg-red-100 transition-colors"
            >
                ✕ Rechazar pago
            </button>
        </div>
    </div>
</template>
