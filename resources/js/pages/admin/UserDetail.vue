<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import {
    ArrowLeft, ShieldCheck, GraduationCap, Activity,
    BookOpen, CreditCard, CheckCircle2, Clock, AlertCircle,
    XCircle, Plus, X, Check, Mail, Phone, Calendar,
    ToggleLeft, ToggleRight, Pencil, BookMarked
} from 'lucide-vue-next';

interface EnrollmentItem {
    id: number;
    course_id: number;
    enrolled_at: string;
    completed_at: string | null;
    passed: boolean | null;
    course: { id: number; title: string; image: string | null; type: string } | null;
}
interface PaymentItem {
    id: number;
    status: string;
    amount: number;
    created_at: string;
    course: { id: number; title: string } | null;
}
interface UserDetail {
    id: number;
    name: string;
    email: string;
    telefono: string | null;
    role: string;
    status: string;
    profile_photo_url?: string;
    created_at: string;
    enrollments: EnrollmentItem[];
    payments: PaymentItem[];
}
interface CourseOption { id: number; title: string; type: string; }

const props = defineProps<{
    user: UserDetail;
    enrolledIds: number[];
    availableCourses: CourseOption[];
}>();

const page = usePage();
const flash = computed(() => (page.props as any).flash ?? {});

// ─── Tabs ──────────────────────────────────────────────────────────
const activeTab = ref<'cursos' | 'pagos'>('cursos');

// ─── Assign Course Modal ────────────────────────────────────────────
const showAssign = ref(false);
const assignForm = useForm({ course_id: '' });
const enrolledIds = ref<number[]>([...props.enrolledIds]);

const unenrolledCourses = computed(() =>
    props.availableCourses.filter(c => !enrolledIds.value.includes(c.id))
);

function submitAssign() {
    if (!assignForm.course_id) return;
    assignForm.post(route('admin.users.assignCourse', { user: props.user.id }), {
        onSuccess: () => {
            enrolledIds.value.push(Number(assignForm.course_id));
            showAssign.value = false;
            assignForm.reset();
        },
    });
}

// ─── Toggle Status ─────────────────────────────────────────────────
function toggleStatus() {
    router.patch(route('admin.users.toggleStatus', { user: props.user.id }), {}, { preserveScroll: true });
}

// ─── Helpers ──────────────────────────────────────────────────────
function initials(name: string) {
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase();
}

function formatDate(d: string | null) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' });
}

function formatMoney(n: number | string) {
    return 'S/ ' + Number(n).toFixed(2);
}

const paymentStatusConfig: Record<string, { label: string; class: string; icon: any }> = {
    aprobado:    { label: 'Aprobado',    class: 'bg-emerald-100 text-emerald-700', icon: CheckCircle2 },
    en_revision: { label: 'En revisión', class: 'bg-amber-100 text-amber-700',    icon: Clock },
    pendiente:   { label: 'Pendiente',   class: 'bg-blue-100 text-blue-700',      icon: AlertCircle },
    rechazado:   { label: 'Rechazado',   class: 'bg-red-100 text-red-700',        icon: XCircle },
};

const enrollmentStatus = (e: EnrollmentItem) => {
    if (e.completed_at && e.passed) return { label: 'Completado', class: 'bg-emerald-100 text-emerald-700', icon: CheckCircle2 };
    return { label: 'En progreso', class: 'bg-blue-100 text-blue-700', icon: Clock };
};

const avatarColors = [
    'bg-purple-100 text-purple-700', 'bg-blue-100 text-blue-700',
    'bg-emerald-100 text-emerald-700', 'bg-amber-100 text-amber-700',
];
const userColor = avatarColors[props.user.id % avatarColors.length];
</script>

<template>
    <Head :title="`${props.user.name} - Admin IEE`" />

    <AppLayout>
        <!-- ── Back nav ──────────────────────────────────────── -->
        <div class="mb-6">
            <Link :href="route('admin.users.index')" class="inline-flex items-center gap-2 text-sm font-semibold text-on-surface-variant hover:text-on-surface transition-colors">
                <ArrowLeft class="h-4 w-4" />
                Volver a usuarios
            </Link>
        </div>

        <!-- ── Profile Header ────────────────────────────────── -->
        <div class="mb-8 rounded-3xl bg-white border border-outline-variant/10 p-8 shadow-sm">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                <!-- Avatar -->
                <div :class="`flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-2xl text-3xl font-bold ${userColor}`">
                    <template v-if="props.user.profile_photo_url">
                        <img :src="props.user.profile_photo_url" :alt="props.user.name" class="h-full w-full rounded-2xl object-cover" />
                    </template>
                    <template v-else>{{ initials(props.user.name) }}</template>
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <h1 class="font-serif text-3xl font-bold text-on-surface">{{ props.user.name }}</h1>
                            <div class="mt-2 flex flex-wrap items-center gap-3">
                                <!-- Role -->
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wider"
                                    :class="props.user.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700'"
                                >
                                    <ShieldCheck v-if="props.user.role === 'admin'" class="h-3.5 w-3.5" />
                                    <GraduationCap v-else class="h-3.5 w-3.5" />
                                    {{ props.user.role === 'admin' ? 'Admin' : 'Estudiante' }}
                                </span>
                                <!-- Status -->
                                <button @click="toggleStatus" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wider transition-opacity hover:opacity-80"
                                    :class="props.user.status === 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                                >
                                    <ToggleRight v-if="props.user.status === 'activo'" class="h-3.5 w-3.5" />
                                    <ToggleLeft v-else class="h-3.5 w-3.5" />
                                    {{ props.user.status === 'activo' ? 'Activo' : 'Inactivo' }}
                                </button>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <Link
                                :href="route('admin.payments.index', { search: props.user.email, openCreate: 1 })"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#57572A] px-4 py-2.5 text-sm font-bold text-white shadow hover:opacity-95 transition-opacity"
                            >
                                <CreditCard class="h-4 w-4" />
                                Registrar Venta
                            </Link>
                        </div>
                    </div>

                    <!-- Contact info -->
                    <div class="mt-4 flex flex-wrap gap-5 text-sm text-on-surface-variant">
                        <span class="flex items-center gap-1.5">
                            <Mail class="h-4 w-4 text-[#C9C7B8]" />
                            {{ props.user.email }}
                        </span>
                        <span v-if="props.user.telefono" class="flex items-center gap-1.5">
                            <Phone class="h-4 w-4 text-[#C9C7B8]" />
                            {{ props.user.telefono }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <Calendar class="h-4 w-4 text-[#C9C7B8]" />
                            Registrado el {{ formatDate(props.user.created_at) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stats mini-row -->
            <div class="mt-6 grid grid-cols-2 gap-4 border-t border-outline-variant/10 pt-6 sm:grid-cols-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Cursos inscritos</p>
                    <p class="mt-1 text-2xl font-bold text-on-surface">{{ props.user.enrollments.length }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Completados</p>
                    <p class="mt-1 text-2xl font-bold text-emerald-600">
                        {{ props.user.enrollments.filter(e => e.completed_at && e.passed).length }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Pagos totales</p>
                    <p class="mt-1 text-2xl font-bold text-on-surface">{{ props.user.payments.length }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">Pagos aprobados</p>
                    <p class="mt-1 text-2xl font-bold text-[#57572A]">
                        {{ props.user.payments.filter(p => p.status === 'aprobado').length }}
                    </p>
                </div>
            </div>
        </div>

        <!-- ── Tabs ───────────────────────────────────────────── -->
        <div class="mb-6 flex gap-1 rounded-2xl bg-surface-container-low p-1 w-fit">
            <button
                v-for="tab in [{ id: 'cursos', label: 'Cursos', icon: BookOpen }, { id: 'pagos', label: 'Pagos', icon: CreditCard }]"
                :key="tab.id"
                @click="activeTab = tab.id as any"
                class="flex items-center gap-2 rounded-xl px-5 py-2 text-sm font-bold transition-all"
                :class="activeTab === tab.id ? 'bg-white text-[#57572A] shadow-sm' : 'text-on-surface-variant hover:text-on-surface'"
            >
                <component :is="tab.icon" class="h-4 w-4" />
                {{ tab.label }}
            </button>
        </div>

        <!-- ── Tab: Cursos ──────────────────────────────────── -->
        <div v-if="activeTab === 'cursos'" class="rounded-3xl bg-white border border-outline-variant/10 shadow-sm overflow-hidden">
            <div class="border-b border-outline-variant/10 px-6 py-4 flex items-center justify-between bg-surface-container-low">
                <h2 class="font-semibold text-on-surface">Cursos inscritos</h2>
                <span class="rounded-full bg-[#57572A]/10 px-3 py-1 text-xs font-bold text-[#57572A]">{{ props.user.enrollments.length }} total</span>
            </div>

            <div v-if="!props.user.enrollments.length" class="py-16 text-center text-sm text-on-surface-variant">
                Este usuario no tiene cursos asignados aún.
            </div>

            <div v-else>
                <div
                    v-for="e in props.user.enrollments"
                    :key="e.id"
                    class="flex items-center gap-4 border-t border-outline-variant/10 px-6 py-4 hover:bg-surface-container-lowest transition-colors"
                >
                    <!-- Image -->
                    <div class="h-12 w-16 flex-shrink-0 rounded-xl overflow-hidden bg-surface-container-low border border-outline-variant/10">
                        <img v-if="e.course?.image" :src="e.course.image" :alt="e.course?.title" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full items-center justify-center">
                            <BookMarked class="h-5 w-5 text-on-surface-variant/40" />
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <p class="truncate font-semibold text-sm text-on-surface">{{ e.course?.title ?? 'Curso eliminado' }}</p>
                        <p class="text-xs text-on-surface-variant">Inscrito: {{ formatDate(e.enrolled_at) }}</p>
                    </div>

                    <!-- Type badge -->
                    <span class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant border border-outline-variant/20 rounded-full px-2.5 py-1">
                        {{ e.course?.type ?? '—' }}
                    </span>

                    <!-- Status -->
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider"
                        :class="enrollmentStatus(e).class"
                    >
                        <component :is="enrollmentStatus(e).icon" class="h-3 w-3" />
                        {{ enrollmentStatus(e).label }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ── Tab: Pagos ──────────────────────────────────── -->
        <div v-if="activeTab === 'pagos'" class="rounded-3xl bg-white border border-outline-variant/10 shadow-sm overflow-hidden">
            <div class="border-b border-outline-variant/10 px-6 py-4 bg-surface-container-low flex items-center justify-between">
                <h2 class="font-semibold text-on-surface">Historial de pagos</h2>
                <span class="rounded-full bg-[#57572A]/10 px-3 py-1 text-xs font-bold text-[#57572A]">{{ props.user.payments.length }} total</span>
            </div>

            <div v-if="!props.user.payments.length" class="py-16 text-center text-sm text-on-surface-variant">
                Este usuario no tiene pagos registrados.
            </div>

            <div v-else>
                <div
                    v-for="p in props.user.payments"
                    :key="p.id"
                    class="flex items-center gap-4 border-t border-outline-variant/10 px-6 py-4 hover:bg-surface-container-lowest transition-colors"
                >
                    <div class="flex-1 min-w-0">
                        <p class="truncate font-semibold text-sm text-on-surface">{{ p.course?.title ?? 'Curso no disponible' }}</p>
                        <p class="text-xs text-on-surface-variant">{{ formatDate(p.created_at) }}</p>
                    </div>

                    <p class="text-sm font-bold text-on-surface">{{ formatMoney(p.amount) }}</p>

                    <span
                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider"
                        :class="paymentStatusConfig[p.status]?.class ?? 'bg-slate-100 text-slate-500'"
                    >
                        <component :is="paymentStatusConfig[p.status]?.icon ?? AlertCircle" class="h-3 w-3" />
                        {{ paymentStatusConfig[p.status]?.label ?? p.status }}
                    </span>
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

        <!-- ── Assign Course Modal ─────────────────────────── -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showAssign" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                    <div class="w-full max-w-md rounded-3xl bg-white shadow-2xl">
                        <div class="flex items-center justify-between border-b border-outline-variant/10 px-7 py-5">
                            <div>
                                <h2 class="font-serif text-xl text-on-surface">Asignar <span class="italic">Curso</span></h2>
                                <p class="text-xs text-on-surface-variant mt-0.5">{{ props.user.name }}</p>
                            </div>
                            <button @click="showAssign = false; assignForm.reset()" class="rounded-xl p-2 hover:bg-surface-container-low">
                                <X class="h-5 w-5 text-on-surface-variant" />
                            </button>
                        </div>
                        <div class="p-7">
                            <div v-if="!unenrolledCourses.length" class="py-8 text-center text-sm text-on-surface-variant">
                                El usuario ya está inscrito en todos los cursos disponibles.
                            </div>
                            <form v-else @submit.prevent="submitAssign" class="space-y-4">
                                <div>
                                    <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant">Seleccionar curso *</label>
                                    <select
                                        v-model="assignForm.course_id"
                                        required
                                        class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-2.5 text-sm outline-none focus:border-[#57572A] transition-colors"
                                    >
                                        <option value="">— Selecciona un curso —</option>
                                        <option v-for="c in unenrolledCourses" :key="c.id" :value="c.id">
                                            {{ c.title }} ({{ c.type }})
                                        </option>
                                    </select>
                                </div>
                                <p class="text-xs text-on-surface-variant">Se creará una inscripción inmediata sin pago. El acceso será concedido al estudiante.</p>
                                <div class="flex justify-end gap-3 border-t border-outline-variant/10 pt-4">
                                    <button type="button" @click="showAssign = false; assignForm.reset()" class="rounded-xl border border-outline-variant/30 px-5 py-2.5 text-sm font-semibold hover:bg-surface-container-low">
                                        Cancelar
                                    </button>
                                    <button type="submit" :disabled="assignForm.processing || !assignForm.course_id" class="rounded-xl bg-[#57572A] px-6 py-2.5 text-sm font-bold text-white shadow hover:opacity-95 disabled:opacity-60">
                                        {{ assignForm.processing ? 'Asignando...' : 'Asignar acceso' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
