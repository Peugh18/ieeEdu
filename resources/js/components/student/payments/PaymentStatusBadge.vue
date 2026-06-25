<script setup lang="ts">
import type { StudentPayment } from '@/types/student-payment';
import { AlertCircle, CheckCircle2, Clock, XCircle } from 'lucide-vue-next';

defineProps<{
    status: StudentPayment['status'];
}>();

const config: Record<string, { label: string; class: string; icon: typeof Clock }> = {
    pendiente: { label: 'Pendiente', class: 'bg-amber-50 text-amber-700 ring-amber-700/10', icon: Clock },
    en_revision: { label: 'En revisión', class: 'bg-blue-50 text-blue-700 ring-blue-700/10', icon: AlertCircle },
    aprobado: { label: 'Aprobado', class: 'bg-emerald-50 text-emerald-700 ring-emerald-700/10', icon: CheckCircle2 },
    rechazado: { label: 'Rechazado', class: 'bg-rose-50 text-rose-700 ring-rose-700/10', icon: XCircle },
};
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset"
        :class="config[status]?.class ?? 'bg-slate-50 text-slate-500'"
    >
        <component :is="config[status]?.icon ?? AlertCircle" class="h-3 w-3" />
        {{ config[status]?.label ?? status }}
    </span>
</template>
