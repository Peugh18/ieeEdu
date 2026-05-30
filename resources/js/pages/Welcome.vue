<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import Navigation from '../components/landing/Navigation.vue';
import HeroSection from '../components/landing/HeroSection.vue';
import WelcomeClientsSection from '../components/landing/WelcomeClientsSection.vue';
import WelcomeCoursesSection from '../components/landing/WelcomeCoursesSection.vue';
import WelcomeConsultoriaSection from '../components/landing/WelcomeConsultoriaSection.vue';
import WelcomeBooksSection from '../components/landing/WelcomeBooksSection.vue';
import WelcomeFooterCta from '../components/landing/WelcomeFooterCta.vue';
import WelcomeFooter from '../components/landing/WelcomeFooter.vue';
import type { HomeSlide, DynamicCourse, TeaserBook, TeaserArticle } from '@/types/public';

defineProps<{
    dynamicCourses: DynamicCourse[];
    teaserBooks: TeaserBook[];
    teaserArticles: TeaserArticle[];
    homeSlides: HomeSlide[];
}>();

let revealObserver: IntersectionObserver | null = null;
let staggerObs: IntersectionObserver | null = null;

onMounted(() => {
    revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('revealed');
                revealObserver?.unobserve(e.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach(el => revealObserver?.observe(el));

    staggerObs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                const children = e.target.querySelectorAll(':scope > *');
                children.forEach((child, i) => {
                    (child as HTMLElement).style.transitionDelay = `${i * 100}ms`;
                    child.classList.add('revealed');
                });
                staggerObs?.unobserve(e.target);
            }
        });
    }, { threshold: 0.05 });
    document.querySelectorAll('.reveal-stagger').forEach(el => staggerObs?.observe(el));
});

onUnmounted(() => {
    revealObserver?.disconnect();
    staggerObs?.disconnect();
});
</script>

<template>
    <Head title="IEE - Instituto de Economía y Empresa">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    </Head>

    <div class="flex min-h-screen flex-col bg-surface text-on-surface font-body selection:bg-primary-container selection:text-on-primary-container">
        <Navigation />

        <main class="pt-0">
            <HeroSection :dbSlides="homeSlides" />
            <WelcomeClientsSection />
            <WelcomeCoursesSection :courses="dynamicCourses" />
            <div class="section-divider"></div>
            <WelcomeConsultoriaSection />
            <div class="section-divider alt"></div>
            <WelcomeBooksSection :books="teaserBooks" />
            <div class="section-divider"></div>
            <WelcomeFooterCta />
        </main>

        <WelcomeFooter />
    </div>
</template>

<style scoped>
/* Scroll reveal */
.reveal {
    opacity: 0;
    transform: translateY(36px);
    transition: opacity 0.9s cubic-bezier(0.22, 1, 0.36, 1), transform 0.9s cubic-bezier(0.22, 1, 0.36, 1);
}
.reveal.revealed {
    opacity: 1;
    transform: translateY(0);
}

/* Stagger children */
.reveal-stagger > * {
    opacity: 0;
    transform: translateY(24px);
    transition: opacity 0.6s cubic-bezier(0.22, 1, 0.36, 1), transform 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}
.reveal-stagger > .revealed {
    opacity: 1;
    transform: translateY(0);
}

/* Section dividers */
.section-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, var(--md-sys-color-outline-variant, #c4c7c5) 20%, var(--md-sys-color-outline-variant, #c4c7c5) 80%, transparent);
    opacity: 0.15;
    max-width: 80rem;
    margin: 0 auto;
}
.section-divider.alt {
    opacity: 0.1;
}
</style>
