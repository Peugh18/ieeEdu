<script setup lang="ts">
import { onMounted, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Chart, CategoryScale, LinearScale, BarElement, LineElement, PointElement, Title, Tooltip, Legend } from 'chart.js';

Chart.register(CategoryScale, LinearScale, BarElement, LineElement, PointElement, Title, Tooltip, Legend);

interface CourseSale {
    id: number;
    title: string;
    approved_payments_count: number;
}

const props = defineProps<{
    totalUsers: number;
    activeUsers: number;
    totalCourses: number;
    publishedCourses: number;
    totalIncome: number;
    courseSales: CourseSale[];
    completionRate: number;
    approvalRate: number;
}>();

const chartRef = ref<HTMLCanvasElement | null>(null);

onMounted(() => {
    if (!chartRef.value) return;

    new Chart(chartRef.value, {
        type: 'bar',
        data: {
            labels: props.courseSales.length ? props.courseSales.map((c) => c.title) : ['Sin datos'],
            datasets: [
                {
                    label: 'Ventas aprobadas',
                    data: props.courseSales.length ? props.courseSales.map((c) => c.approved_payments_count) : [0],
                    backgroundColor: 'rgba(79, 70, 229, 0.6)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: {
                    display: true,
                    text: 'Cursos más vendidos (aprobados)',
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                },
            },
        },
    });
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AppLayout>
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-xl border bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Ingresos Totales</p>
                <h2 class="mt-3 text-3xl font-bold">S/ {{ props.totalIncome.toFixed(2) }}</h2>
                <p class="text-sm text-green-600">+12.5% vs anterior</p>
            </article>
            <article class="rounded-xl border bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Usuarios Activos</p>
                <h2 class="mt-3 text-3xl font-bold">{{ props.activeUsers }}</h2>
                <p class="text-sm text-green-600">+4.2% vs anterior</p>
            </article>
            <article class="rounded-xl border bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Cursos Publicados</p>
                <h2 class="mt-3 text-3xl font-bold">{{ props.publishedCourses }}</h2>
                <p class="text-sm text-gray-500">Estable</p>
            </article>
            <article class="rounded-xl border bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Tasa de Aprobación</p>
                <h2 class="mt-3 text-3xl font-bold">{{ props.approvalRate.toFixed(1) }}%</h2>
                <p class="text-sm text-red-500">-2.1% vs anterior</p>
            </article>
        </section>

        <section class="mt-6 grid gap-4 lg:grid-cols-2">
            <article class="rounded-xl border bg-white p-5 shadow-sm">
                <header class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Cursos más vendidos</h3>
                    <select class="rounded border px-2 py-1 text-sm">
                        <option>Últimos 30 días</option>
                        <option>Últimos 90 días</option>
                    </select>
                </header>

                <div class="space-y-3">
                    <template v-if="props.courseSales.length">
                        <div v-for="course in props.courseSales" :key="course.id" class="space-y-1">
                            <div class="flex justify-between text-sm font-medium text-gray-700">
                                <span>{{ course.title }}</span>
                                <span>S/ {{ course.approved_payments_count * 100 }}</span>
                            </div>
                            <div class="h-3 rounded bg-gray-200">
                                <div class="h-3 rounded bg-indigo-500" :style="{ width: (course.approved_payments_count / (props.courseSales[0]?.approved_payments_count || 1) * 100) + '%' }"></div>
                            </div>
                        </div>
                    </template>
                    <p v-else class="text-sm text-gray-500">No hay ventas aún.</p>
                </div>
            </article>

            <article class="rounded-xl border bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Métricas de desempeño</h3>
                    <span class="text-xs text-gray-500">En tiempo real</span>
                </div>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="rounded-lg bg-indigo-50 p-3">
                        <p class="text-xs text-gray-500">Total Usuarios</p>
                        <p class="text-2xl font-bold">{{ props.totalUsers }}</p>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-3">
                        <p class="text-xs text-gray-500">Usuarios Activos</p>
                        <p class="text-2xl font-bold">{{ props.activeUsers }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs text-gray-500">Cursos Totales</p>
                        <p class="text-2xl font-bold">{{ props.totalCourses }}</p>
                    </div>
                    <div class="rounded-lg bg-amber-50 p-3">
                        <p class="text-xs text-gray-500">Cursos publicados</p>
                        <p class="text-2xl font-bold">{{ props.publishedCourses }}</p>
                    </div>
                </div>
            </article>
        </section>

        <section class="mt-6 rounded-xl border bg-white p-5 shadow-sm">
            <h3 class="text-lg font-semibold mb-3">Gráfica de ventas</h3>
            <div class="h-72">
                <canvas ref="chartRef" class="h-full w-full"></canvas>
            </div>
        </section>
    </AppLayout>
</template>
