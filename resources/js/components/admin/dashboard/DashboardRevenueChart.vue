<script setup lang="ts">
import { onMounted, onUnmounted, ref, computed, watch, nextTick } from 'vue';
import {
    Chart, CategoryScale, LinearScale, BarElement, PointElement,
    Title, Tooltip, Legend, Filler, LineElement, LineController, BarController
} from 'chart.js';
import AdminSegmentedControl from '@/components/admin/AdminSegmentedControl.vue';

Chart.register(
    CategoryScale, LinearScale, BarElement, PointElement,
    Title, Tooltip, Legend, Filler, LineElement, LineController, BarController
);

interface ChartPoint {
    label: string;
    total: number;
    subs: number;
    courses: number;
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
    const labels = data.map(d => d.label);
    const totals = data.map(d => d.total);
    const subs = data.map(d => d.subs);
    const courses = data.map(d => d.courses);

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
                    borderColor: '#c9c7b8', // fainter
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

onMounted(() => nextTick(() => buildChart()));
watch(period, () => buildChart());
onUnmounted(() => {
    chartInst.value?.destroy();
    chartInst.value = null;
});
</script>

<template>
    <article class="xl:col-span-2 bg-surface-container-lowest rounded-[2rem] md:rounded-[3rem] p-5 md:p-10 border border-outline-variant/20 shadow-sm flex flex-col gap-6 md:gap-8 overflow-hidden">
        <header class="flex flex-col xl:flex-row xl:items-center justify-between gap-4 md:gap-6">
            <div class="space-y-1">
                <h3 class="font-serif text-xl md:text-2xl font-bold text-on-surface">Evolución de Ingresos</h3>
                <p class="text-xs text-on-surface-variant font-medium">Análisis financiero temporal</p>
            </div>
            
            <AdminSegmentedControl
                v-model="period"
                :options="periodLabels"
                class="self-start xl:self-auto"
            />
        </header>

        <div class="flex-1 min-h-[380px]">
            <canvas ref="chartRef" class="h-full w-full"></canvas>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-6 pt-4 border-t border-outline-variant/10">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[#57572A]"></div>
                <span class="text-xs font-bold text-on-surface-variant">Facturación Total</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[#a1a166]"></div>
                <span class="text-xs font-bold text-on-surface-variant">Membresías</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-[#c9c7b8] border border-dashed border-[#8b8a7f]"></div>
                <span class="text-xs font-bold text-on-surface-variant">Cursos Individuales</span>
            </div>
        </div>
    </article>
</template>
