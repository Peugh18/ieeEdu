import { ref } from 'vue';
import type { SummaryStats } from '@/types/student-dashboard';

export function useAnimatedStats() {
  const animatedStats = ref<SummaryStats>({
    active_courses: 0,
    completed_courses: 0,
    upcoming_classes: 0,
    available_exams: 0,
    average_score: 0,
    certificate_count: 0,
  });

  function animateNumber(key: keyof SummaryStats, target: number, duration = 1200) {
    const start = Date.now();
    const startVal = 0;
    const tick = () => {
      const elapsed = Date.now() - start;
      const progress = Math.min(elapsed / duration, 1);
      const ease = 1 - Math.pow(1 - progress, 3);
      animatedStats.value[key] = Math.round(startVal + (target - startVal) * ease);
      if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  }

  function startAnimation(stats: SummaryStats) {
    setTimeout(() => {
      animateNumber('active_courses', stats.active_courses);
      animateNumber('completed_courses', stats.completed_courses, 1400);
      animateNumber('upcoming_classes', stats.upcoming_classes, 1100);
      animateNumber('available_exams', stats.available_exams, 1300);
      animateNumber('average_score', stats.average_score, 1600);
      animateNumber('certificate_count', stats.certificate_count, 1500);
    }, 300);
  }

  return { animatedStats, startAnimation };
}
