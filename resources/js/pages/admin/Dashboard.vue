<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import DashboardHeader from '@/components/admin/dashboard/DashboardHeader.vue';
import DashboardStatsGrid from '@/components/admin/dashboard/DashboardStatsGrid.vue';
import DashboardKpisGrid from '@/components/admin/dashboard/DashboardKpisGrid.vue';
import DashboardRevenueChart from '@/components/admin/dashboard/DashboardRevenueChart.vue';
import DashboardComposition from '@/components/admin/dashboard/DashboardComposition.vue';
import DashboardInsight from '@/components/admin/dashboard/DashboardInsight.vue';
import DashboardTopCourses from '@/components/admin/dashboard/DashboardTopCourses.vue';
import DashboardInventory from '@/components/admin/dashboard/DashboardInventory.vue';
import DashboardAcademicMetrics from '@/components/admin/dashboard/DashboardAcademicMetrics.vue';

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

// Computeds
const formattedDate = computed(() => {
    return new Date().toLocaleDateString('es-PE', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    });
});

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
    if (props.stats.premiumUsers > props.stats.activeUsers * 0.3) {
        return 'El modelo de membresías Premium está alcanzando masa crítica. Considera ampliar los beneficios del plan para acelerar la retención.';
    }
    if (props.stats.whatsappLeadsMonth > 5) {
        return `${props.stats.whatsappLeadsMonth} leads de WhatsApp este mes. Alto interés comercial — ideal momento para campaña de descuento urgente.`;
    }
    if (props.stats.pendingPayments > 0) {
        return `Hay ${props.stats.pendingPayments} pago(s) pendientes de revisar. Cada pago pendiente es ingreso esperando ser aprobado.`;
    }
    return 'Buen ritmo operativo. El ecosistema mantiene una base de alumnos activos y estable. Sigue publicando contenido para mejorar la tracción.';
});

function downloadPDF() {
    window.location.href = route('admin.dashboard.report');
}
</script>

<template>
    <Head title="Admin Dashboard · iieEdu" />

    <AppLayout>
        <div class="min-h-screen bg-background dark:bg-[#141410] font-sans">
            <div class="max-w-[1700px] mx-auto px-4 sm:px-6 lg:px-14 py-6 md:py-12 space-y-8 md:space-y-14">
                
                <DashboardHeader 
                    :formatted-date="formattedDate"
                    @download-p-d-f="downloadPDF"
                />

                <DashboardStatsGrid 
                    :stats="stats"
                    :sub-income-share="subIncomeShare"
                    :course-income-share="courseIncomeShare"
                />

                <DashboardKpisGrid 
                    :stats="stats"
                />

                <section class="grid gap-8 xl:grid-cols-3">
                    <DashboardRevenueChart 
                        :charts="charts"
                    />

                    <div class="flex flex-col gap-6">
                        <DashboardComposition 
                            :sub-income="stats.subIncome"
                            :course-income="stats.courseIncome"
                            :sub-income-share="subIncomeShare"
                            :course-income-share="courseIncomeShare"
                            :active-subs="stats.activeSubs"
                            :expired-subs="stats.expiredSubs"
                        />

                        <DashboardInsight 
                            :insight="insight"
                        />
                    </div>
                </section>

                <section class="grid gap-8 xl:grid-cols-3">
                    <DashboardTopCourses 
                        :course-sales="courseSales"
                    />

                    <DashboardInventory 
                        :stats="stats"
                    />
                </section>

                <DashboardAcademicMetrics 
                    :stats="stats"
                    :completion-rate="completionRate"
                />

            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Transition smoothness */
main, aside, section {
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
