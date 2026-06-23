<script setup lang="ts">
import type { CourseLesson, CourseModule } from '@/types/course';
import { ref } from 'vue';

const props = defineProps<{
    type: string;
    modules?: CourseModule[];
    lessons?: CourseLesson[];
    duration?: string | number;
}>();

const openModules = ref<number[]>([props.modules?.[0]?.id ?? 0]);

function toggleModule(id: number) {
    if (openModules.value.includes(id)) {
        openModules.value = openModules.value.filter((m) => m !== id);
    } else {
        openModules.value.push(id);
    }
}

const title = props.type === 'evento' ? 'Detalles del Evento' : props.type === 'en vivo' ? 'Programa de Sesiones' : 'Estructura Curricular';
</script>

<template>
    <div class="mb-8 pt-10">
        <h2 class="mb-10 font-serif text-[32px] font-bold text-on-background">{{ title }}</h2>

        <div v-if="(type === 'grabado' || type === 'en vivo') && modules?.length" class="space-y-6">
            <div
                class="mb-6 flex items-center gap-4 rounded bg-surface-container-highest p-4 text-xs font-bold uppercase tracking-[0.1em] text-on-surface-variant"
            >
                <span>Duración de Estudio: {{ duration || 'A tu ritmo' }}</span>
                <span class="text-outline-variant">|</span>
                <span>Total Módulos: {{ modules.length }}</span>
            </div>
            <div
                v-for="(module, index) in modules"
                :key="module.id"
                class="overflow-hidden rounded bg-white shadow-[0_20px_40px_rgba(26,28,25,0.03)]"
            >
                <button
                    @click="toggleModule(module.id)"
                    class="flex w-full items-center justify-between bg-transparent p-6 transition-colors hover:bg-background"
                >
                    <div class="flex items-center gap-6">
                        <span class="w-24 text-left text-sm font-bold uppercase tracking-[0.1em] text-primary">Módulo 0{{ Number(index) + 1 }}</span>
                        <h3 class="text-left text-[17px] font-bold text-on-background">{{ module.title }}</h3>
                    </div>
                    <svg
                        class="h-5 w-5 text-on-surface-variant transition-transform"
                        :class="{ 'rotate-180': openModules.includes(module.id) }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div v-show="openModules.includes(module.id)" class="border-t border-outline-variant/20 bg-background p-2">
                    <ul v-if="module.lessons?.length" class="space-y-1">
                        <li
                            v-for="(lesson, lIdx) in module.lessons"
                            :key="lesson.id"
                            class="flex items-center gap-4 rounded p-4 text-[15px] text-on-background transition-colors hover:bg-surface-container-highest"
                        >
                            <svg class="h-5 w-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"
                                />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium">Sesión {{ Number(lIdx) + 1 }}: {{ lesson.title }}</span>
                        </li>
                    </ul>
                    <p v-else class="p-4 font-serif text-[13px] italic text-on-surface-variant">
                        El material de este módulo estará disponible pronto en la biblioteca.
                    </p>
                </div>
            </div>
        </div>

        <div v-else-if="type === 'evento' && lessons?.length" class="space-y-6">
            <div
                v-for="lesson in lessons"
                :key="lesson.id"
                class="flex flex-col gap-6 rounded bg-white p-8 shadow-[0_20px_40px_rgba(26,28,25,0.04)] sm:flex-row sm:items-center"
            >
                <div class="flex-1">
                    <span class="mb-2 block text-[10px] font-bold uppercase tracking-[0.1em] text-primary">Evento Confirmado</span>
                    <h3 class="mb-2 font-serif text-2xl font-bold text-on-background">{{ lesson.title }}</h3>
                    <p class="mb-6 text-sm text-on-surface-variant">
                        {{ lesson.description || 'Participación sujeta al calendario oficial de sesiones.' }}
                    </p>
                    <div
                        class="flex inline-flex items-center gap-3 rounded border border-outline-variant/30 bg-background px-3 py-2 text-sm font-bold text-on-background"
                    >
                        <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                            />
                        </svg>
                        {{ lesson.start_time || 'Fecha por anunciar' }}
                    </div>
                </div>
                <div class="mt-4 flex-shrink-0 sm:mt-0">
                    <button
                        class="rounded border border-outline-variant px-5 py-3 text-[11px] font-bold uppercase tracking-[0.05em] text-on-background transition-colors hover:bg-surface-container-highest"
                    >
                        Agendar
                    </button>
                </div>
            </div>
        </div>

        <div
            v-else
            class="rounded bg-white py-16 text-center font-serif text-lg italic text-on-surface-variant shadow-[0_20px_40px_rgba(26,28,25,0.04)]"
        >
            La estructura académica se encuentra en proceso de actualización formal.
        </div>
    </div>
</template>
