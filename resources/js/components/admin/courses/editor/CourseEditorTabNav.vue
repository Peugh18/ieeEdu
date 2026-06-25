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
    <div
        class="scrollbar-none mb-6 flex gap-2 overflow-x-auto rounded-3xl border border-outline-variant/20 bg-surface-container-lowest p-2 shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)]"
    >
        <button
            v-for="tab in tabs"
            :key="tab.id"
            type="button"
            class="whitespace-nowrap rounded-2xl border px-6 py-3 text-[11px] font-bold uppercase tracking-widest transition-all duration-200"
            :class="
                activeTab === tab.id
                    ? 'border-outline-variant/10 bg-white text-primary shadow-md'
                    : 'border-transparent text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface'
            "
            @click="emit('change', tab.id)"
        >
            {{ tab.label }}
            <span v-if="tab.id === 'curriculum'" class="ml-2 rounded-lg border border-primary/20 bg-primary/10 px-2.5 py-0.5 text-primary">{{
                lessonCount
            }}</span>
        </button>
    </div>
</template>
