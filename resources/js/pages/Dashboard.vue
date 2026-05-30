<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import BottomNav from '@/components/student/BottomNav.vue';
import StudentDashboardLiveBanner from '@/components/student/dashboard/StudentDashboardLiveBanner.vue';
import StudentDashboardStats from '@/components/student/dashboard/StudentDashboardStats.vue';
import StudentDashboardContinueLearning from '@/components/student/dashboard/StudentDashboardContinueLearning.vue';
import StudentDashboardQuickLinks from '@/components/student/dashboard/StudentDashboardQuickLinks.vue';
import StudentDashboardCertificates from '@/components/student/dashboard/StudentDashboardCertificates.vue';
import StudentDashboardRecommendations from '@/components/student/dashboard/StudentDashboardRecommendations.vue';
import StudentDashboardAcademicPerformance from '@/components/student/dashboard/StudentDashboardAcademicPerformance.vue';
import StudentDashboardSubscription from '@/components/student/dashboard/StudentDashboardSubscription.vue';
import StudentDashboardLiveModal from '@/components/student/dashboard/StudentDashboardLiveModal.vue';
import EmailVerificationBanner from '@/components/student/EmailVerificationBanner.vue';
import { useAnimatedStats } from '@/composables/useAnimatedStats';
import { type BreadcrumbItem } from '@/types';
import { type SharedData, type User } from '@/types';
import type { SummaryStats, ContinueLearning, Recommendation, Certificate, NextLiveClass } from '@/types/student-dashboard';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Mi Aula Virtual', href: '/dashboard' },
];

const page = usePage<SharedData>();
const user = page.props.auth.user as User & { has_subscription?: boolean };

const props = defineProps<{
    stats: SummaryStats;
    continueLearning: ContinueLearning | null;
    recommendations: Recommendation[];
    certificates: Certificate[];
    nextLiveClass: NextLiveClass | null;
}>();

const showLiveModal = ref(false);

const { animatedStats, startAnimation } = useAnimatedStats();

const countdown = ref({ days: 0, hours: 0, minutes: 0, seconds: 0, isLive: false, isPast: false });
let countdownInterval: ReturnType<typeof setInterval> | null = null;

function updateCountdown() {
    if (!props.nextLiveClass) return;
    const target = new Date(props.nextLiveClass.start_time).getTime();
    const now = Date.now();
    const diff = target - now;

    if (diff <= 0 && diff > -5400000) {
        countdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0, isLive: true, isPast: false };
    } else if (diff <= -5400000) {
        countdown.value = { ...countdown.value, isLive: false, isPast: true };
    } else {
        const d = Math.floor(diff / 86400000);
        const h = Math.floor((diff % 86400000) / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        countdown.value = { days: d, hours: h, minutes: m, seconds: s, isLive: false, isPast: false };
    }
}

onMounted(() => {
    startAnimation(props.stats);
    if (props.nextLiveClass) {
        updateCountdown();
        countdownInterval = setInterval(updateCountdown, 1000);
    }
});

onUnmounted(() => {
    if (countdownInterval) clearInterval(countdownInterval);
});
</script>

<template>
    <Head title="Aula Virtual - IEE" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-amber-50/30 dark:from-[#141410] dark:via-on-background dark:to-on-background pb-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
                <EmailVerificationBanner :email-verified-at="user?.email_verified_at" />

                <StudentDashboardLiveBanner
                    :user="user"
                    :animated-stats="animatedStats"
                    :next-live-class="nextLiveClass"
                    @open-modal="showLiveModal = true"
                />

                <StudentDashboardStats :stats="animatedStats" />

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <StudentDashboardContinueLearning
                            :continue-learning="continueLearning"
                            :stats="stats"
                        />
                        <StudentDashboardQuickLinks :stats="stats" />
                        <StudentDashboardCertificates :certificates="certificates" />
                        <StudentDashboardRecommendations :recommendations="recommendations" />
                    </div>

                    <aside class="space-y-5">
                        <StudentDashboardAcademicPerformance
                            :stats="stats"
                            :animated-stats="animatedStats"
                        />
                        <StudentDashboardSubscription :user="user" />
                    </aside>
                </div>
            </div>
        </div>

        <StudentDashboardLiveModal
            :show="showLiveModal"
            :next-live-class="nextLiveClass"
            :is-live="countdown.isLive"
            @close="showLiveModal = false"
        />

        <BottomNav active="dashboard" />
    </AppLayout>
</template>
