<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import {
    ArrowLeft, CheckCircle2, XCircle, Clock, RefreshCw,
    Download, FileImage, User2, BookOpen, Calendar, DollarSign, Check
} from 'lucide-vue-next';

const props = defineProps<{
    payment: {
        id: number;
        status: string;
        amount: number;
        comprobante: string | null;
        created_at: string;
        updated_at: string;
        user: { id: number; name: string; email: string; telefono: string | null };
        course: { id: number; title: string; type: string } | null;
    };
}>();

const page = usePage();
const flash = computed(() => (page.props as any).flash ?? {});

function approve() { router.patch(route('admin.payments.approve', props.payment.id)); }
function reject() {
    if (!confirm('¿Rechazar este pago?')) return;
    router.patch(route('admin.payments.reject', props.payment.id));
}

const statusCfg: Record<string, { label: string; cls: string; bg: string; icon: any }> = {
    pendiente:   { label: 'Pendiente',   cls: 'text-slate-600',    bg: 'bg-slate-100',    icon: Clock },
    en_revision: { label: 'En revisión', cls: 'text-amber-700',    bg: 'bg-amber-50',     icon: RefreshCw },
    aprobado:    { label: 'Aprobado',    cls: 'text-emerald-700',  bg: 'bg-emerald-50',   icon: CheckCircle2 },
    rechazado:   { label: 'Rechazado',   cls: 'text-red-600',      bg: 'bg-red-50',       icon: XCircle },
};

const cfg = computed(() => statusCfg[props.payment.status] ?? { label: props.payment.status, cls: 'text-slate-600', bg: 'bg-slate-100', icon: Clock });

function formatDate(d: string) {
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
function formatMoney(n: number) { return 'S/ ' + Number(n).toFixed(2); }
</script>

<template>
    <Head :title="`Pago #${props.payment.id} - Admin IEE`" />

    <AppLayout>
        <!-- Back -->
        <div class="mb-6">
            <Link :href="route('admin.payments.index')" class="inline-flex items-center gap-2 text-sm font-semibold text-on-surface-variant hover:text-on-surface transition-colors">
                <ArrowLeft class="h-4 w-4" />
                Volver a pagos
            </Link>
        </div>

        <!-- Header -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-serif text-4xl text-on-surface">Pago <span class="italic text-primary">#{{ props.payment.id }}</span></h1>
                <p class="mt-1 text-sm text-on-surface-variant">Registrado el {{ formatDate(props.payment.created_at) }}</p>
            </div>
            <!-- Status badge large -->
            <div
                class="inline-flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-bold"
                :class="[cfg.bg, cfg.cls]"
            >
                <component :is="cfg.icon" class="h-5 w-5" />
                {{ cfg.label }}
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Left: Details -->
            <div class="space-y-6 lg:col-span-2">

                <!-- User card -->
                <div class="rounded-3xl border border-outline-variant/10 bg-white p-6 shadow-sm">
                    <p class="mb-4 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                        <User2 class="h-3.5 w-3.5" /> Estudiante
                    </p>
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-sm font-bold text-blue-700">
                            {{ props.payment.user.name.split(' ').slice(0,2).map((w: string) => w[0]).join('').toUpperCase() }}
                        </div>
                        <div>
                            <p class="font-bold text-on-surface">{{ props.payment.user.name }}</p>
                            <p class="text-sm text-on-surface-variant">{{ props.payment.user.email }}</p>
                            <p v-if="props.payment.user.telefono" class="text-xs text-on-surface-variant/70">{{ props.payment.user.telefono }}</p>
                        </div>
                        <Link :href="route('admin.users.show', props.payment.user.id)" class="ml-auto text-xs font-bold text-primary hover:underline">
                            Ver perfil →
                        </Link>
                    </div>
                </div>

                <!-- Course card -->
                <div class="rounded-3xl border border-outline-variant/10 bg-white p-6 shadow-sm">
                    <p class="mb-4 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                        <BookOpen class="h-3.5 w-3.5" /> Curso
                    </p>
                    <div v-if="props.payment.course" class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-on-surface">{{ props.payment.course.title }}</p>
                            <span class="text-xs text-on-surface-variant border border-outline-variant/20 rounded-full px-2 py-0.5 mt-1 inline-block">
                                {{ props.payment.course.type }}
                            </span>
                        </div>
                    </div>
                    <p v-else class="text-sm text-on-surface-variant">Curso no disponible</p>
                </div>

                <!-- Payment details -->
                <div class="rounded-3xl border border-outline-variant/10 bg-white p-6 shadow-sm">
                    <p class="mb-4 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                        <DollarSign class="h-3.5 w-3.5" /> Detalles del pago
                    </p>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs text-on-surface-variant">Monto</p>
                            <p class="mt-1 text-3xl font-bold text-primary">{{ formatMoney(props.payment.amount) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant">Método</p>
                            <p class="mt-1 font-semibold text-on-surface">Yape / Transferencia (manual)</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant">Fecha de registro</p>
                            <p class="mt-1 text-sm text-on-surface">{{ formatDate(props.payment.created_at) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-on-surface-variant">Última actualización</p>
                            <p class="mt-1 text-sm text-on-surface">{{ formatDate(props.payment.updated_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Comprobante + Actions -->
            <div class="space-y-6">
                <!-- Actions -->
                <div class="rounded-3xl border border-outline-variant/10 bg-white p-6 shadow-sm">
                    <p class="mb-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Acciones</p>

                    <div v-if="props.payment.status === 'aprobado'" class="rounded-2xl bg-emerald-50 p-4 text-center">
                        <CheckCircle2 class="mx-auto mb-2 h-8 w-8 text-emerald-500" />
                        <p class="text-sm font-bold text-emerald-700">Curso desbloqueado</p>
                        <p class="mt-1 text-xs text-emerald-600">El estudiante ya tiene acceso</p>
                    </div>

                    <div v-else class="space-y-3">
                        <button @click="approve" class="w-full rounded-2xl bg-emerald-600 py-3 text-sm font-bold text-white shadow hover:opacity-90 transition-opacity">
                            ✓ Aprobar y desbloquear curso
                        </button>
                        <button
                            v-if="props.payment.status !== 'rechazado'"
                            @click="reject"
                            class="w-full rounded-2xl border border-red-200 bg-red-50 py-3 text-sm font-bold text-red-600 hover:bg-red-100 transition-colors"
                        >
                            ✕ Rechazar pago
                        </button>
                    </div>
                </div>

                <!-- Comprobante -->
                <div class="rounded-3xl border border-outline-variant/10 bg-white p-6 shadow-sm">
                    <p class="mb-4 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">Comprobante</p>

                    <template v-if="props.payment.comprobante">
                        <a :href="props.payment.comprobante" target="_blank" rel="noopener">
                            <img
                                :src="props.payment.comprobante"
                                alt="Comprobante de pago"
                                class="w-full rounded-2xl border border-outline-variant/10 object-contain max-h-64 hover:opacity-90 transition-opacity cursor-zoom-in"
                            />
                        </a>
                        <a
                            :href="props.payment.comprobante"
                            download
                            class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-primary hover:underline"
                        >
                            <Download class="h-3.5 w-3.5" />
                            Descargar imagen
                        </a>
                    </template>
                    <div v-else class="flex flex-col items-center gap-2 rounded-2xl bg-surface-container-low py-8 text-center text-on-surface-variant">
                        <FileImage class="h-8 w-8 text-on-surface-variant/30" />
                        <p class="text-sm">Sin comprobante adjunto</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash -->
        <Transition enter-active-class="transition duration-300" enter-from-class="translate-y-4 opacity-0" enter-to-class="translate-y-0 opacity-100">
            <div v-if="flash.success" class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-2xl bg-emerald-600 px-5 py-3 text-white shadow-2xl">
                <Check class="h-5 w-5" />
                <span class="text-sm font-semibold">{{ flash.success }}</span>
            </div>
        </Transition>
    </AppLayout>
</template>
