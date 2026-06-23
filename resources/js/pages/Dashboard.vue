<script setup lang="ts">
import BottomNav from '@/components/student/BottomNav.vue';
import StudentDashboardAcademicPerformance from '@/components/student/dashboard/StudentDashboardAcademicPerformance.vue';
import StudentDashboardCertificates from '@/components/student/dashboard/StudentDashboardCertificates.vue';
import StudentDashboardContinueLearning from '@/components/student/dashboard/StudentDashboardContinueLearning.vue';
import StudentDashboardLiveBanner from '@/components/student/dashboard/StudentDashboardLiveBanner.vue';
import StudentDashboardLiveModal from '@/components/student/dashboard/StudentDashboardLiveModal.vue';
import StudentDashboardQuickLinks from '@/components/student/dashboard/StudentDashboardQuickLinks.vue';
import StudentDashboardRecommendations from '@/components/student/dashboard/StudentDashboardRecommendations.vue';
import StudentDashboardStats from '@/components/student/dashboard/StudentDashboardStats.vue';
import StudentDashboardSubscription from '@/components/student/dashboard/StudentDashboardSubscription.vue';
import EmailVerificationBanner from '@/components/student/EmailVerificationBanner.vue';
import { useAnimatedStats } from '@/composables/useAnimatedStats';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import type { Certificate, ContinueLearning, NextLiveClass, Recommendation, SummaryStats } from '@/types/student-dashboard';
import { Head, usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Mi Aula Virtual', href: '/dashboard' }];

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
        <div
            class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-amber-50/30 pb-24 dark:from-[#141410] dark:via-on-background dark:to-on-background"
        >
            <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
                <EmailVerificationBanner :email-verified-at="user?.email_verified_at" />

                <StudentDashboardLiveBanner
                    :user="user"
                    :animated-stats="animatedStats"
                    :next-live-class="nextLiveClass"
                    @open-modal="showLiveModal = true"
                />

                <StudentDashboardStats :stats="animatedStats" />

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="space-y-6 lg:col-span-2">
                        <StudentDashboardContinueLearning :continue-learning="continueLearning" :stats="stats" />
                        <StudentDashboardQuickLinks :stats="stats" />
                        <StudentDashboardCertificates :certificates="certificates" />
                        <StudentDashboardRecommendations :recommendations="recommendations" />
                    </div>

                    <aside class="space-y-5">
                        <StudentDashboardAcademicPerformance :stats="stats" :animated-stats="animatedStats" />
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
