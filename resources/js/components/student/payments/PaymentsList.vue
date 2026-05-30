<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { CreditCard, Calendar, ArrowRight, Upload } from 'lucide-vue-next';
import PaymentStatusBadge from './PaymentStatusBadge.vue';
import type { StudentPayment } from '@/types/student-payment';

defineProps<{
    payments: StudentPayment[];
}>();

const emit = defineEmits<{
    (e: 'upload', payment: StudentPayment): void;
}>();

function formatDate(d: string) {
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatMoney(n: number) {
    return 'S/ ' + Number(n).toFixed(2);
}
</script>

<template>
    <div class="bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden shadow-sm">
        <table class="w-full text-left">
            <thead class="bg-slate-50/80 border-b border-slate-100">
                <tr>
                    <th class="px-8 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Curso</th>
                    <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 text-center">Fecha</th>
                    <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 text-center">Monto</th>
                    <th class="px-6 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 text-center">Estado</th>
                    <th class="px-8 py-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400 text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <tr v-for="p in payments" :key="p.id" class="group hover:bg-slate-50/50 transition-colors">
                    <td class="px-8 py-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" :class="p.subscription_type ? 'bg-primary/10 text-primary' : 'bg-slate-50 text-slate-300'">
                                <CreditCard class="w-5 h-5" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-900 truncate leading-tight">
                                    <template v-if="p.subscription_type">
                                        <span class="text-primary">Membresía Premium</span>
                                        <span class="ml-1 text-[10px] font-extrabold uppercase tracking-widest bg-primary/10 text-primary px-2 py-0.5 rounded-full">{{ p.subscription_type }}</span>
                                    </template>
                                    <template v-else>{{ p.course?.title ?? 'Pago Directo' }}</template>
                                </p>
                                <p class="text-[10px] text-slate-400 mt-0.5 uppercase tracking-widest">ID-{{ p.id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center text-sm font-semibold text-slate-600"><div class="flex items-center justify-center gap-1.5"><Calendar class="w-3.5 h-3.5 text-slate-300" /><span>{{ formatDate(p.created_at) }}</span></div></td>
                    <td class="px-6 py-5 text-center text-base font-black text-slate-900">{{ formatMoney(p.amount) }}</td>
                    <td class="px-6 py-5 text-center"><PaymentStatusBadge :status="p.status" /></td>
                    <td class="px-8 py-5 text-right">
                        <Link v-if="p.status === 'aprobado' && p.course" :href="route('student.classroom', { course: p.course.slug })" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline"><ArrowRight class="w-3.5 h-3.5" /> Ir al curso</Link>
                        <Link v-else-if="p.status === 'aprobado' && p.subscription_type" :href="route('student.courses.index')" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:underline"><ArrowRight class="w-3.5 h-3.5" /> Ver cursos</Link>
                        <button v-else-if="p.status === 'pendiente'" @click="emit('upload', p)" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:text-primary/80 transition-colors"><Upload class="w-3.5 h-3.5" /> Subir comprobante</button>
                        <span v-else class="text-xs text-slate-400 font-medium">—</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
