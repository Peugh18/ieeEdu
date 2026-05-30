<script setup lang="ts">
import type { CourseEditorTab } from '@/types/course-editor';

defineProps<{
    activeTab: CourseEditorTab | string;
    lessonCount: number;
}>();

const emit = defineEmits<{
    change: [tab: CourseEditorTab];
}>();

const tabs: { id: CourseEditorTab; label: string }[] = [
    { id: 'general', label: 'Datos Básicos' },
    { id: 'pricing', label: 'Precios & Tipo' },
    { id: 'details', label: 'Detalles Acad.' },
    { id: 'instructor', label: 'Autoría & Cert.' },
    { id: 'curriculum', label: 'Sílabo' },
    { id: 'exams', label: 'Evaluaciones' },
    { id: 'students', label: 'Alumnos Inscritos' },
];
</script>

<template>
    <div class="flex overflow-x-auto gap-2 bg-surface-container-lowest p-2 rounded-3xl border border-outline-variant/20 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] mb-6 scrollbar-none">
        <button
            v-for="tab in tabs"
            :key="tab.id"
            type="button"
            class="px-6 py-3 rounded-2xl font-bold text-[11px] uppercase tracking-widest transition-all whitespace-nowrap duration-200 border"
            :class="activeTab === tab.id
                ? 'bg-white text-primary shadow-md border-outline-variant/10'
                : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low border-transparent'"
            @click="emit('change', tab.id)"
        >
            {{ tab.label }}
            <span v-if="tab.id === 'curriculum'" class="ml-2 bg-primary/10 text-primary px-2.5 py-0.5 rounded-lg border border-primary/20">{{ lessonCount }}</span>
        </button>
    </div>
</template>
