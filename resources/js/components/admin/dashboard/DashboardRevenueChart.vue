<script setup lang="ts">
import AdminSegmentedControl from '@/components/admin/AdminSegmentedControl.vue';
import {
    BarController,
    BarElement,
    CategoryScale,
    Chart,
    Filler,
    Legend,
    LinearScale,
    LineController,
    LineElement,
    PointElement,
    Title,
    Tooltip,
} from 'chart.js';
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';

Chart.register(CategoryScale, LinearScale, BarElement, PointElement, Title, Tooltip, Legend, Filler, LineElement, LineController, BarController);

interface ChartPoint {
    label: string;
    total: number;
    subs: number;
    courses: number;
    books: number;
}

const props = defineProps<{
    charts: {
        weekly: ChartPoint[];
        monthly: ChartPoint[];
        quarterly: ChartPoint[];
        annually: ChartPoint[];
    };
}>();

const chartRef = ref<HTMLCanvasElement | null>(null);
const chartInst = ref<Chart | null>(null);

type ChartPeriod = 'weekly' | 'monthly' | 'quarterly' | 'annually';
const period = ref<ChartPeriod>('weekly');

const periodLabels: Record<ChartPeriod, string> = {
    weekly: 'Semanal',
    monthly: 'Mensual',
    quarterly: 'Trimestral',
    annually: '6 Meses',
};

const currentChart = computed(() => props.charts[period.value] ?? []);

function buildChart() {
    if (!chartRef.value) return;
    const ctx = chartRef.value.getContext('2d');
    if (!ctx) return;

    if (chartInst.value) {
        chartInst.value.destroy();
        chartInst.value = null;
    }

    const data = currentChart.value;
    const labels = data.map((d) => d.label);
    const totals = data.map((d) => d.total);
    const subs = data.map((d) => d.subs);
    const courses = data.map((d) => d.courses);
    const books = data.map((d) => d.books);

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
                    borderColor: '#57572A', // primary
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
                    borderColor: '#a1a166', // secondary/muted
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
                {
                    label: 'Libros',
                    data: books,
                    borderColor: '#3b82f6',
                    borderWidth: 2,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    tension: 0.4,
                    fill: false,
                    order: 3,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    display: false, // Hidden the default, created custom below
                },
                tooltip: {
                    backgroundColor: '#1a1a10',
                    titleFont: { family: 'Noto Serif', size: 13 },
                    bodyFont: { family: 'Inter', size: 12 },
                    padding: 14,
                    callbacks: {
                        title: (items) => items[0]?.label ?? '',
                        label: (ctx) => `  ${ctx.dataset.label}: S/ ${ctx.parsed.y.toLocaleString('es-PE', { minimumFractionDigits: 2 })}`,
                    },
                },
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
                    },
                },
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        font: { family: 'Inter', size: 11, weight: 'bold' },
                        color: '#6b7280',
                    },
                },
            },
        },
    });
}

onMounted(() => nextTick(() => buildChart()));
watch(period, () => buildChart());
onUnmounted(() => {
    chartInst.value?.destroy();
    chartInst.value = null;
});
</script>

<template>
    <article
        class="flex flex-col gap-6 overflow-hidden rounded-[2rem] border border-outline-variant/20 bg-surface-container-lowest p-5 shadow-sm md:gap-8 md:rounded-[3rem] md:p-10 xl:col-span-2"
    >
        <header class="flex flex-col justify-between gap-4 md:gap-6 xl:flex-row xl:items-center">
            <div class="space-y-1">
                <h3 class="font-serif text-xl font-bold text-on-surface md:text-2xl">Evolución de Ingresos</h3>
                <p class="text-xs font-medium text-on-surface-variant">Análisis financiero temporal</p>
            </div>

            <AdminSegmentedControl v-model="period" :options="periodLabels" class="self-start xl:self-auto" />
        </header>

        <div class="min-h-[380px] flex-1">
            <canvas ref="chartRef" class="h-full w-full"></canvas>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-6 border-t border-outline-variant/10 pt-4">
            <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full bg-[#57572A]"></div>
                <span class="text-xs font-bold text-on-surface-variant">Facturación Total</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full bg-[#a1a166]"></div>
                <span class="text-xs font-bold text-on-surface-variant">Membresías</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full border border-dashed border-[#8b8a7f] bg-[#c9c7b8]"></div>
                <span class="text-xs font-bold text-on-surface-variant">Cursos</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                <span class="text-xs font-bold text-on-surface-variant">Libros</span>
            </div>
        </div>
    </article>
</template>
