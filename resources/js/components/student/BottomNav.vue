<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, BookOpen, Calendar, ClipboardCheck, Award } from 'lucide-vue-next';

const props = defineProps<{
    active?: 'dashboard' | 'courses' | 'live-classes' | 'exams' | 'certificates';
}>();

const items = [
    { key: 'dashboard',     label: 'Inicio',      icon: LayoutGrid,     routeName: 'dashboard' },
    { key: 'courses',       label: 'Cursos',       icon: BookOpen,       routeName: 'student.courses.index' },
    { key: 'live-classes',  label: 'Clases',       icon: Calendar,       routeName: 'student.live-classes.index' },
    { key: 'exams',         label: 'Exámenes',     icon: ClipboardCheck, routeName: 'student.exams.index' },
    { key: 'certificates',  label: 'Certs',        icon: Award,          routeName: 'student.certificates.index' },
];
</script>

<template>
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-t border-outline-variant/10 flex items-center justify-around px-1 py-1.5 shadow-[0_-8px_30px_rgba(0,0,0,0.07)]">
        <Link
            v-for="item in items"
            :key="item.key"
            :href="route(item.routeName)"
            class="flex flex-col items-center gap-0.5 py-1.5 px-3 rounded-xl transition-all active:scale-90"
            :class="active === item.key
                ? 'text-primary bg-primary/8'
                : 'text-on-surface-variant hover:text-primary'"
        >
            <component :is="item.icon" class="w-[18px] h-[18px]" />
            <span class="text-[8px] font-black uppercase tracking-widest">{{ item.label }}</span>
        </Link>
    </nav>
</template>
