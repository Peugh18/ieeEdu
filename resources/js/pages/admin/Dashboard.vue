<script setup lang="ts">
import { onMounted, ref, computed, watch, nextTick } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/student/BottomNav.vue';
import { Head } from '@inertiajs/vue3';
import {
    Chart, CategoryScale, LinearScale, BarElement, PointElement,
    Title, Tooltip, Legend, Filler, LineElement, LineController, BarController
} from 'chart.js';
import {
    TrendingUp, Users, BookOpen, Download, Video, Lightbulb, Sparkles,
    Crown, CreditCard, ArrowUpRight, GraduationCap, DollarSign,
    ChevronRight, Target, Award, MessageCircle, ShieldCheck, ShieldX,
    Clock, CheckCircle, UserCheck, UserX, Laptop
} from 'lucide-vue-next';

Chart.register(
    CategoryScale, LinearScale, BarElement, PointElement,
    Title, Tooltip, Legend, Filler, LineElement, LineController, BarController
);

// ─── TIPOS ────────────────────────────────────────────────────────────────
interface ChartPoint {
    label: string;
    total: number;
    subs: number;
    courses: number;
}

interface CourseSale {
    id: number;
    title: string;
    approved_payments_count: number;
    total_earned: number;
    category?: { name: string };
    type: string;
    price: number;
    image?: string;
}

interface Stats {
    totalIncome: number;
    subIncome: number;
    courseIncome: number;
    totalUsers: number;
    activeUsers: number;
    inactiveUsers: number;
    premiumUsers: number;
    newUsersThisMonth: number;
    totalCourses: number;
    publishedCourses: number;
    draftCourses: number;
    recordedCourses: number;
    liveCourses: number;
    totalBooks: number;
    availableBooks: number;
    totalEnrollments: number;
    completedCourses: number;
    approvalRate: number;
    activeSubs: number;
    expiredSubs: number;
    pendingPayments: number;
    approvedPayments: number;
    certificatesIssued: number;
    whatsappLeads: number;
    whatsappLeadsMonth: number;
}

const props = defineProps<{
    stats: Stats;
    charts: {
        weekly: ChartPoint[];
        monthly: ChartPoint[];
        quarterly: ChartPoint[];
        annually: ChartPoint[];
    };
    courseSales: CourseSale[];
}>();

// ─── ESTADO ────────────────────────────────────────────────────────────────
const chartRef  = ref<HTMLCanvasElement | null>(null);
const chartInst = ref<Chart | null>(null);
const period    = ref<'weekly' | 'monthly' | 'quarterly' | 'annually'>('weekly');

const periodLabels: Record<string, string> = {
    weekly: 'Semanal',
    monthly: 'Mensual',
    quarterly: 'Trimestral',
    annually: '6 Meses',
};

// ─── COMPUTADOS ───────────────────────────────────────────────────────────
const currentChart = computed(() => props.charts[period.value] ?? []);

const courseIncomeShare = computed(() => {
    if (!props.stats.totalIncome) return 0;
    return ((props.stats.courseIncome / props.stats.totalIncome) * 100).toFixed(1);
});

const subIncomeShare = computed(() => {
    if (!props.stats.totalIncome) return 0;
    return ((props.stats.subIncome / props.stats.totalIncome) * 100).toFixed(1);
});

const completionRate = computed(() => {
    if (!props.stats.totalEnrollments) return 0;
    return ((props.stats.completedCourses / props.stats.totalEnrollments) * 100).toFixed(1);
});

const insight = computed(() => {
    if (props.stats.premiumUsers > props.stats.activeUsers * 0.3)
        return 'El modelo de membresías Premium está alcanzando masa crítica. Considera ampliar los beneficios del plan para acelerar la retención.';
    if (props.stats.whatsappLeadsMonth > 5)
        return `${props.stats.whatsappLeadsMonth} leads de WhatsApp este mes. Alto interés comercial — ideal momento para campaña de descuento urgente.`;
    if (props.stats.pendingPayments > 0)
        return `Hay ${props.stats.pendingPayments} pago(s) pendientes de revisar. Cada pago pendiente es ingreso esperando ser aprobado.`;
    return 'Buen ritmo operativo. El ecosistema mantiene una base de alumnos activos y estable. Sigue publicando contenido para mejorar la tracción.';
});

// ─── CHART ────────────────────────────────────────────────────────────────
function buildChart() {
    if (!chartRef.value) return;
    const ctx = chartRef.value.getContext('2d');
    if (!ctx) return;

    if (chartInst.value) {
        chartInst.value.destroy();
        chartInst.value = null;
    }

    const data = currentChart.value;
    const labels   = data.map(d => d.label);
    const totals   = data.map(d => d.total);
    const subs     = data.map(d => d.subs);
    const courses  = data.map(d => d.courses);

    // Gradiente total
    const gradTotal = ctx.createLinearGradient(0, 0, 0, 350);
    gradTotal.addColorStop(0, 'rgba(87,87,42,0.15)');
    gradTotal.addColorStop(1, 'rgba(87,87,42,0)');

    chartInst.value = new Chart(chartRef.value, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Facturación Total',
                    data: totals,
                    borderColor: '#57572A',
                    borderWidth: 3,
                    pointBackgroundColor: '#57572A',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: gradTotal,
                    order: 0,
                },
                {
                    label: 'Membresías',
                    data: subs,
                    borderColor: '#a1a166',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#a1a166',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    tension: 0.4,
                    fill: false,
                    order: 1,
                },
                {
                    label: 'Cursos Individuales',
                    data: courses,
                    borderColor: '#c9c7b8',
                    borderWidth: 2,
                    pointBackgroundColor: '#c9c7b8',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    tension: 0.4,
                    fill: false,
                    borderDash: [4, 4],
                    order: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        font: { family: 'Inter', size: 11, weight: 'bold' },
                        color: '#6b7280',
                        boxWidth: 12,
                        padding: 20,
                    }
                },
                tooltip: {
                    backgroundColor: '#1a1a10',
                    titleFont: { family: 'Noto Serif', size: 13 },
                    bodyFont: { family: 'Inter', size: 12 },
                    padding: 14,
                    callbacks: {
                        title: (items) => items[0]?.label ?? '',
                        label: (ctx) => `  ${ctx.dataset.label}: S/ ${ctx.parsed.y.toLocaleString('es-PE', { minimumFractionDigits: 2 })}`,
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    border: { display: false },
                    ticks: {
                        callback: (v) => 'S/ ' + v,
                        font: { family: 'Inter', size: 10 },
                        color: '#9ca3af',
                    }
                },
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        font: { family: 'Inter', size: 11, weight: 'bold' },
                        color: '#6b7280',
                    }
                }
            }
        }
    });
}

function fmt(n: number | null | undefined) {
    return (n ?? 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function downloadPDF() {
    window.location.href = route('admin.dashboard.report');
}

onMounted(() => nextTick(() => buildChart()));
watch(period, () => buildChart());
</script>

<template>
    <Head title="Admin Dashboard · iieEdu" />

    <AppLayout>
        <div class="min-h-screen bg-background dark:bg-[#141410] font-sans">
            <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-14 py-6 md:py-12 space-y-8 md:space-y-14">

                <!-- ══════════════════════════════════════════════════════
                     CABECERA
                ══════════════════════════════════════════════════════ -->
                <header class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-8 pb-12 border-b border-primary/10">
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] tracking-[0.3em] font-black uppercase text-primary/60">Panel de Inteligencia · iieEdu</span>
                        </div>
                        <h1 class="font-serif text-4xl md:text-5xl lg:text-6xl text-gray-900 leading-none tracking-tight">
                            Dashboard <span class="italic text-primary">Administrativo</span>
                        </h1>
                        <p class="text-gray-400 text-sm max-w-xl">Métricas en tiempo real de ingresos, suscripciones, cursos, libros y engagement estudiantil.</p>
                    </div>
                    <div class="flex flex-wrap gap-3 items-center">
                        <div class="px-6 py-3.5 bg-white rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-xs font-bold text-gray-700 uppercase tracking-widest">{{ new Date().toLocaleDateString('es-PE', { day:'2-digit', month:'long', year:'numeric'}) }}</span>
                        </div>
                        <button @click="downloadPDF" class="px-8 py-3.5 bg-on-background text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl hover:scale-105 active:scale-95 transition-all flex items-center gap-3 group">
                            <Download class="w-4 h-4 opacity-50 group-hover:opacity-100 transition" />
                            Reporte PDF
                        </button>
                    </div>
                </header>

                <!-- ══════════════════════════════════════════════════════
                     SECCIÓN 1 · REVENUE PILLARS
                ══════════════════════════════════════════════════════ -->
                <section class="space-y-4">
                    <div class="flex items-center gap-3">
                        <DollarSign class="w-4 h-4 text-primary" />
                        <h2 class="text-[11px] font-black uppercase tracking-[0.25em] text-gray-400">Flujos de Ingresos</h2>
                    </div>

                    <div class="grid gap-4 md:gap-6 grid-cols-1 lg:grid-cols-3">
                        <!-- Total -->
                        <article class="relative bg-on-background dark:bg-primary rounded-[2.5rem] p-6 md:p-9 overflow-hidden group hover:scale-[1.01] transition-all shadow-2xl">
                            <div class="relative z-10 space-y-8">
                                <div class="flex items-center justify-between">
                                    <div class="w-12 h-12 rounded-2xl bg-primary flex items-center justify-center text-primary-fixed">
                                        <DollarSign class="w-6 h-6" />
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-white/30 border border-white/10 px-3 py-1 rounded-xl">CAJA REAL</span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-white/30 mb-2">Facturación Bruta</p>
                                    <h3 class="font-serif text-5xl font-black text-primary-fixed tabular-nums leading-none">S/ {{ props.stats.totalIncome.toLocaleString() }}</h3>
                                    <p class="text-[11px] text-white/30 mt-3 font-medium">{{ props.stats.approvedPayments }} pagos aprobados en total</p>
                                </div>
                            </div>
                            <div class="absolute -bottom-8 -right-8 w-44 h-44 text-white/5"><DollarSign class="w-full h-full" /></div>
                        </article>

                        <!-- Suscripciones -->
                        <article class="relative bg-white rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm overflow-hidden group hover:shadow-xl hover:scale-[1.01] transition-all">
                            <div class="relative z-10 space-y-8">
                                <div class="flex items-center justify-between">
                                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                                        <Crown class="w-6 h-6" />
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-primary bg-primary/5 px-3 py-1 rounded-xl border border-primary/10">PREMIUM</span>
                                        <span class="text-[10px] text-gray-400 font-bold tabular-nums">{{ subIncomeShare }}% del total</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Membresías</p>
                                    <h3 class="font-serif text-5xl font-black text-gray-900 tabular-nums leading-none">S/ {{ props.stats.subIncome.toLocaleString() }}</h3>
                                    <div class="mt-3 flex items-center gap-2">
                                        <div class="w-full h-1.5 rounded-full bg-gray-100">
                                            <div class="h-full bg-primary rounded-full transition-all duration-[2000ms]" :style="{width: subIncomeShare + '%'}"></div>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-2 font-medium">{{ props.stats.activeSubs }} suscripciones activas</p>
                                </div>
                            </div>
                            <div class="absolute -bottom-6 -right-6 w-32 h-32 text-primary/5"><Crown class="w-full h-full" /></div>
                        </article>

                        <!-- Cursos -->
                        <article class="relative bg-white rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm overflow-hidden group hover:shadow-xl hover:scale-[1.01] transition-all">
                            <div class="relative z-10 space-y-8">
                                <div class="flex items-center justify-between">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                                        <Video class="w-6 h-6" />
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="text-[9px] font-black uppercase tracking-widest text-amber-600 bg-amber-50 px-3 py-1 rounded-xl border border-amber-100">CURSOS</span>
                                        <span class="text-[10px] text-gray-400 font-bold tabular-nums">{{ courseIncomeShare }}% del total</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Venta Directa</p>
                                    <h3 class="font-serif text-5xl font-black text-gray-900 tabular-nums leading-none">S/ {{ props.stats.courseIncome.toLocaleString() }}</h3>
                                    <div class="mt-3 flex items-center gap-2">
                                        <div class="w-full h-1.5 rounded-full bg-gray-100">
                                            <div class="h-full bg-amber-400 rounded-full transition-all duration-[2000ms]" :style="{width: courseIncomeShare + '%'}"></div>
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-2 font-medium">{{ props.stats.publishedCourses }} cursos publicados</p>
                                </div>
                            </div>
                            <div class="absolute -bottom-6 -right-6 w-32 h-32 text-amber-500/5"><Video class="w-full h-full" /></div>
                        </article>
                    </div>
                </section>

                <!-- ══════════════════════════════════════════════════════
                     SECCIÓN 2 · KPIs OPERATIVOS (fila rápida)
                ══════════════════════════════════════════════════════ -->
                <section class="space-y-4">
                    <div class="flex items-center gap-3">
                        <Target class="w-4 h-4 text-primary" />
                        <h2 class="text-[11px] font-black uppercase tracking-[0.25em] text-gray-400">Indicadores Operativos</h2>
                    </div>

                    <div class="grid gap-4 grid-cols-2 md:grid-cols-4 lg:grid-cols-7">

                        <!-- Alumnos activos -->
                        <article class="bg-white rounded-2xl p-4 md:p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3">
                            <UserCheck class="w-5 h-5 text-emerald-500" />
                            <span class="text-2xl md:text-3xl font-serif font-black text-gray-900 tabular-nums">{{ props.stats.activeUsers }}</span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-tight">Alumnos Activos</span>
                        </article>

                        <!-- Alumnos inactivos -->
                        <article class="bg-white rounded-2xl p-4 md:p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3">
                            <UserX class="w-5 h-5 text-red-400" />
                            <span class="text-2xl md:text-3xl font-serif font-black text-gray-900 tabular-nums">{{ props.stats.inactiveUsers }}</span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-tight">Inactivos</span>
                        </article>

                        <!-- Premium -->
                        <article class="bg-primary rounded-2xl p-4 md:p-6 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3 group hover:scale-105">
                            <Crown class="w-5 h-5 text-primary-fixed" />
                            <span class="text-2xl md:text-3xl font-serif font-black text-primary-fixed tabular-nums">{{ props.stats.premiumUsers }}</span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-primary-fixed/60 leading-tight">Usuarios Premium</span>
                        </article>

                        <!-- Nuevos este mes -->
                        <article class="bg-white rounded-2xl p-4 md:p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3">
                            <ArrowUpRight class="w-5 h-5 text-blue-400" />
                            <span class="text-2xl md:text-3xl font-serif font-black text-gray-900 tabular-nums">{{ props.stats.newUsersThisMonth }}</span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-tight">Nuevos este mes</span>
                        </article>

                        <!-- Pagos pendientes -->
                        <article class="bg-white rounded-2xl p-4 md:p-6 border border-orange-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3">
                            <Clock class="w-5 h-5 text-orange-400" />
                            <span class="text-2xl md:text-3xl font-serif font-black text-orange-500 tabular-nums">{{ props.stats.pendingPayments }}</span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-tight">Pagos Pendientes</span>
                        </article>

                        <!-- Certificados -->
                        <article class="bg-white rounded-2xl p-4 md:p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3">
                            <Award class="w-5 h-5 text-purple-400" />
                            <span class="text-2xl md:text-3xl font-serif font-black text-gray-900 tabular-nums">{{ props.stats.certificatesIssued }}</span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-tight">Certificados Emitidos</span>
                        </article>

                        <!-- WhatsApp Leads -->
                        <article class="bg-white rounded-2xl p-4 md:p-6 border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col gap-2 md:gap-3">
                            <MessageCircle class="w-5 h-5 text-green-500" />
                            <span class="text-2xl md:text-3xl font-serif font-black text-gray-900 tabular-nums">{{ props.stats.whatsappLeadsMonth }}</span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-tight">Leads WhatsApp / mes</span>
                        </article>

                    </div>
                </section>

                <!-- ══════════════════════════════════════════════════════
                     SECCIÓN 3 · GRÁFICA + COMPOSICIÓN
                ══════════════════════════════════════════════════════ -->
                <section class="grid gap-8 xl:grid-cols-3">

                    <!-- Gráfica de Ingresos -->
                    <article class="xl:col-span-2 bg-white rounded-[2rem] md:rounded-[3rem] p-5 md:p-10 border border-gray-100 shadow-sm flex flex-col gap-6 md:gap-8 overflow-hidden">
                        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 md:gap-6">
                            <div class="space-y-1">
                                <h3 class="font-serif text-xl md:text-2xl font-bold text-gray-900">Evolución de Ingresos</h3>
                                <p class="text-[12px] text-gray-400 font-medium">Facturación Total · Membresías · Cursos Individuales</p>
                            </div>
                            <div class="flex items-center bg-gray-50 p-1.5 rounded-2xl border border-gray-100 self-start shrink-0 overflow-x-auto max-w-full custom-scrollbar">
                                <button v-for="(label, key) in periodLabels" :key="key"
                                    type="button"
                                    @click="period = key as any"
                                    class="px-4 py-2 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all"
                                    :class="period === key
                                        ? 'bg-white text-primary shadow-md border border-gray-100'
                                        : 'text-gray-400 hover:text-gray-600'">
                                    {{ label }}
                                </button>
                            </div>
                        </header>
                        <div class="flex-1 min-h-[380px]">
                            <canvas ref="chartRef" class="h-full w-full"></canvas>
                        </div>
                    </article>

                    <!-- Panel Lateral: Composición + Insight -->
                    <div class="flex flex-col gap-6">

                        <!-- Composición de Ingresos -->
                        <article class="bg-surface-dim rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-primary/10 space-y-6 md:space-y-8 flex-1">
                            <h3 class="font-serif text-lg font-bold text-gray-900 italic">Composición de Caja</h3>

                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Membresías</span>
                                        <div class="text-right">
                                            <span class="text-sm font-black text-primary tabular-nums">{{ subIncomeShare }}%</span>
                                            <span class="block text-[9px] text-gray-400 font-bold italic">S/ {{ props.stats.subIncome.toLocaleString() }}</span>
                                        </div>
                                    </div>
                                    <div class="h-2 bg-white rounded-full overflow-hidden border border-gray-100">
                                        <div class="h-full bg-primary rounded-full transition-all duration-[2s]" :style="{width: subIncomeShare + '%'}"></div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Cursos</span>
                                        <div class="text-right">
                                            <span class="text-sm font-black text-amber-500 tabular-nums">{{ courseIncomeShare }}%</span>
                                            <span class="block text-[9px] text-gray-400 font-bold italic">S/ {{ props.stats.courseIncome.toLocaleString() }}</span>
                                        </div>
                                    </div>
                                    <div class="h-2 bg-white rounded-full overflow-hidden border border-gray-100">
                                        <div class="h-full bg-amber-400 rounded-full transition-all duration-[2s]" :style="{width: courseIncomeShare + '%'}"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Totales -->
                            <div class="pt-6 border-t border-primary/10 grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <p class="text-2xl font-serif font-black text-gray-900 tabular-nums">{{ props.stats.activeSubs }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">Subs Activas</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-serif font-black text-gray-900 tabular-nums">{{ props.stats.expiredSubs }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">Vencidas</p>
                                </div>
                            </div>
                        </article>

                        <!-- Insight Inteligente -->
                        <article class="bg-primary rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-8 text-white flex gap-5 items-start group">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center shrink-0 group-hover:rotate-12 transition-transform">
                                <Lightbulb class="w-6 h-6 text-primary-fixed" />
                            </div>
                            <div class="space-y-2">
                                <p class="text-[9px] font-black uppercase tracking-widest text-primary-fixed/60">Análisis Estratégico</p>
                                <p class="text-[13px] font-serif italic leading-relaxed text-white/90">{{ insight }}</p>
                            </div>
                        </article>

                    </div>
                </section>

                <!-- ══════════════════════════════════════════════════════
                     SECCIÓN 4 · INVENTARIO DE CONTENIDOS + TOP VENTAS
                ══════════════════════════════════════════════════════ -->
                <section class="grid gap-8 xl:grid-cols-3">

                    <!-- Top Cursos por Ventas -->
                    <article class="xl:col-span-2 bg-white rounded-[2rem] md:rounded-[3rem] p-5 md:p-10 border border-gray-100 shadow-sm space-y-6 md:space-y-8 overflow-hidden">
                        <header class="flex items-center justify-between">
                            <div class="space-y-1">
                                <h3 class="font-serif text-2xl font-bold text-gray-900">Top Cursos · Ranking de Ventas</h3>
                                <p class="text-xs text-gray-400 font-medium italic">Volumen de matrículas y facturación individual.</p>
                            </div>
                            <ChevronRight class="w-5 h-5 text-gray-300" />
                        </header>

                        <div class="space-y-4">
                            <template v-if="props.courseSales.length">
                                <div v-for="(course, idx) in props.courseSales" :key="course.id"
                                    class="flex items-center gap-5 p-5 rounded-2xl border border-gray-50 bg-gray-50/30 hover:bg-white hover:shadow-lg transition-all group cursor-pointer">

                                    <!-- Rank -->
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm shrink-0 transition-colors"
                                        :class="idx === 0 ? 'bg-primary text-primary-fixed' : idx === 1 ? 'bg-gray-100 text-gray-700' : 'bg-gray-50 text-gray-400'">
                                        {{ idx + 1 }}
                                    </div>

                                    <!-- Info -->
                                    <div class="flex-1 min-w-0">
                                        <p class="font-bold text-gray-900 truncate group-hover:text-primary transition-colors">{{ course.title }}</p>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ course.type === 'grabado' ? '🎬 Grabado' : '📡 En Vivo' }}</span>
                                        </div>
                                    </div>

                                    <!-- Barra de progreso -->
                                    <div class="w-24 space-y-1 hidden sm:block">
                                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-[1500ms]"
                                                :class="idx === 0 ? 'bg-primary' : 'bg-gray-300'"
                                                :style="{width: (course.approved_payments_count / (props.courseSales[0]?.approved_payments_count || 1) * 100) + '%'}">
                                            </div>
                                        </div>
                                        <p class="text-[9px] font-bold text-gray-400 text-right">{{ course.approved_payments_count }} ventas</p>
                                    </div>

                                    <!-- Total ganado -->
                                    <div class="text-right shrink-0">
                                        <p class="font-serif font-black text-gray-900 tabular-nums text-lg">S/ {{ (course.total_earned ?? 0).toLocaleString() }}</p>
                                        <p class="text-[9px] text-gray-400 font-bold italic">facturado</p>
                                    </div>
                                </div>
                            </template>
                            <div v-else class="text-center py-12 text-gray-300">
                                <Video class="w-12 h-12 mx-auto mb-3" />
                                <p class="text-sm font-medium">Sin ventas registradas aún</p>
                            </div>
                        </div>
                    </article>

                    <!-- Inventario de Contenidos -->
                    <div class="flex flex-col gap-6">
                        <!-- Cursos -->
                        <article class="bg-primary-fixed/30 rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-primary/10 space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-primary-fixed">
                                    <Video class="w-5 h-5" />
                                </div>
                                <h3 class="font-serif font-bold text-gray-900">Inventario de Cursos</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white rounded-2xl p-4 text-center border border-gray-100">
                                    <p class="text-2xl font-serif font-black text-gray-900 tabular-nums">{{ props.stats.publishedCourses }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">Publicados</p>
                                </div>
                                <div class="bg-white rounded-2xl p-4 text-center border border-gray-100">
                                    <p class="text-2xl font-serif font-black text-gray-500 tabular-nums">{{ props.stats.draftCourses }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">Borradores</p>
                                </div>
                                <div class="bg-white rounded-2xl p-4 text-center border border-gray-100">
                                    <p class="text-2xl font-serif font-black text-amber-600 tabular-nums">{{ props.stats.recordedCourses }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">Grabados</p>
                                </div>
                                <div class="bg-white rounded-2xl p-4 text-center border border-gray-100">
                                    <p class="text-2xl font-serif font-black text-primary tabular-nums">{{ props.stats.liveCourses }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">En Vivo</p>
                                </div>
                            </div>
                        </article>

                        <!-- Libros -->
                        <article class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm space-y-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                    <BookOpen class="w-5 h-5" />
                                </div>
                                <h3 class="font-serif font-bold text-gray-900">Publicaciones</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-blue-50 rounded-2xl p-4 text-center border border-blue-100">
                                    <p class="text-3xl font-serif font-black text-blue-600 tabular-nums">{{ props.stats.totalBooks }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-blue-400 mt-1">Total Libros</p>
                                </div>
                                <div class="bg-gray-50 rounded-2xl p-4 text-center border border-gray-100">
                                    <p class="text-3xl font-serif font-black text-gray-900 tabular-nums">{{ props.stats.availableBooks }}</p>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-gray-400 mt-1">Disponibles</p>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <!-- ══════════════════════════════════════════════════════
                     SECCIÓN 5 · ENGAGEMENT ACADÉMICO
                ══════════════════════════════════════════════════════ -->
                <section class="space-y-4">
                    <div class="flex items-center gap-3">
                        <GraduationCap class="w-4 h-4 text-primary" />
                        <h2 class="text-[11px] font-black uppercase tracking-[0.25em] text-gray-400">Salud Académica & Engagement</h2>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">

                        <!-- Tasa de Aprobación (donut visual) -->
                        <article class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm flex flex-col items-center gap-6 text-center">
                            <div class="relative w-28 h-28">
                                <svg class="w-full h-full -rotate-90">
                                    <circle cx="56" cy="56" r="48" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-100" />
                                    <circle cx="56" cy="56" r="48" stroke="currentColor" stroke-width="10" fill="none"
                                        :stroke-dasharray="301.6"
                                        :stroke-dashoffset="301.6 - (301.6 * props.stats.approvalRate / 100)"
                                        stroke-linecap="round" class="text-primary transition-all duration-[2s]" />
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center font-serif font-black text-xl text-gray-900 tabular-nums">{{ props.stats.approvalRate.toFixed(0) }}%</span>
                            </div>
                            <div>
                                <h4 class="font-serif font-bold text-gray-900">Tasa de Aprobación</h4>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ props.stats.totalEnrollments }} matrículas totales</p>
                            </div>
                        </article>

                        <!-- Completitud -->
                        <article class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm flex flex-col items-center gap-6 text-center">
                            <div class="relative w-28 h-28">
                                <svg class="w-full h-full -rotate-90">
                                    <circle cx="56" cy="56" r="48" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-100" />
                                    <circle cx="56" cy="56" r="48" stroke="currentColor" stroke-width="10" fill="none"
                                        :stroke-dasharray="301.6"
                                        :stroke-dashoffset="301.6 - (301.6 * Number(completionRate) / 100)"
                                        stroke-linecap="round" class="text-emerald-500 transition-all duration-[2s]" />
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center font-serif font-black text-xl text-gray-900 tabular-nums">{{ completionRate }}%</span>
                            </div>
                            <div>
                                <h4 class="font-serif font-bold text-gray-900">Tasa de Finalización</h4>
                                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mt-1">{{ props.stats.completedCourses }} cursos completados</p>
                            </div>
                        </article>

                        <!-- Usuarios vs Premium -->
                        <article class="bg-white rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 border border-gray-100 shadow-sm space-y-6">
                            <div class="flex items-center gap-3">
                                <ShieldCheck class="w-5 h-5 text-primary" />
                                <h4 class="font-serif font-bold text-gray-900">Alumnos vs Premium</h4>
                            </div>
                            <div class="space-y-4">
                                <div class="space-y-1.5">
                                    <div class="flex justify-between">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total</span>
                                        <span class="text-sm font-black text-gray-900 tabular-nums">{{ props.stats.totalUsers }}</span>
                                    </div>
                                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-gray-300 rounded-full" style="width:100%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="flex justify-between">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Activos</span>
                                        <span class="text-sm font-black text-emerald-600 tabular-nums">{{ props.stats.activeUsers }}</span>
                                    </div>
                                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-400 rounded-full transition-all duration-[2s]"
                                            :style="{width: (props.stats.activeUsers / props.stats.totalUsers * 100) + '%'}"></div>
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="flex justify-between">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Premium</span>
                                        <span class="text-sm font-black text-primary tabular-nums">{{ props.stats.premiumUsers }}</span>
                                    </div>
                                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-primary rounded-full transition-all duration-[2s]"
                                            :style="{width: (props.stats.premiumUsers / props.stats.totalUsers * 100) + '%'}"></div>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <!-- Conversión & Leads -->
                        <article class="bg-on-background dark:bg-[#2a2a1a] dark:border dark:border-[rgba(231,230,171,0.10)] rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-9 text-white space-y-6 md:space-y-8">
                            <div class="flex items-center gap-3">
                                <MessageCircle class="w-5 h-5 text-primary-fixed" />
                                <h4 class="font-serif font-bold text-primary-fixed">Leads & Conversión</h4>
                            </div>
                            <div class="space-y-6">
                                <div class="space-y-1">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-white/30">Leads WhatsApp Total</p>
                                    <p class="text-4xl font-serif font-black text-primary-fixed tabular-nums">{{ props.stats.whatsappLeads }}</p>
                                </div>
                                <div class="pt-5 border-t border-white/10 space-y-1">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-white/30">Este Mes</p>
                                    <p class="text-2xl font-serif font-black text-white tabular-nums">{{ props.stats.whatsappLeadsMonth }} Leads</p>
                                </div>
                            </div>
                        </article>

                    </div>
                </section>

            </div>
        </div>
        <BottomNav />
    </AppLayout>
</template>

<style scoped>
.font-serif {
    font-family: 'Noto Serif', serif;
}
.font-sans {
    font-family: 'Inter', sans-serif;
}
</style>
