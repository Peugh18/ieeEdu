<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{ payment: any }>();
const actionForm = useForm({});

function approve() {
    actionForm.patch(route('admin.payments.approve', props.payment.id));
}

function reject() {
    actionForm.patch(route('admin.payments.reject', props.payment.id));
}
</script>

<template>
    <Head title="Detalle de pago" />

    <AppLayout>
        <div class="mb-4 flex justify-between">
            <h1 class="text-2xl font-bold">Detalle del pago #{{ payment.id }}</h1>
            <Link :href="route('admin.payments.index')" class="rounded bg-slate-700 px-4 py-2 text-white">Volver</Link>
        </div>

        <div class="rounded-lg border p-4">
            <p><strong>Usuario:</strong> {{ payment.user.name }} ({{ payment.user.email }})</p>
            <p><strong>Curso:</strong> {{ payment.course.title }}</p>
            <p><strong>Monto:</strong> S/ {{ payment.amount }}</p>
            <p><strong>Estado:</strong> {{ payment.status }}</p>
            <p><strong>Fecha:</strong> {{ new Date(payment.created_at).toLocaleString() }}</p>
            <p><strong>Comprobante:</strong> <span v-if="payment.comprobante">{{ payment.comprobante }}</span><span v-else>Sin comprobante</span></p>
        </div>

        <div class="mt-4 flex gap-2">
            <button @click.prevent="approve" class="rounded bg-emerald-600 px-4 py-2 text-white">Aprobar</button>
            <button @click.prevent="reject" class="rounded bg-red-600 px-4 py-2 text-white">Rechazar</button>
        </div>
    </AppLayout>
</template>
