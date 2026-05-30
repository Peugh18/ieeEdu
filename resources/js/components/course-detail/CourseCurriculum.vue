<script setup lang="ts">
import { ref } from 'vue';
import type { CourseModule, CourseLesson } from '@/types/course';

const props = defineProps<{
    type: string;
    modules?: CourseModule[];
    lessons?: CourseLesson[];
    duration?: string | number;
}>();

const openModules = ref<number[]>([props.modules?.[0]?.id ?? 0]);

function toggleModule(id: number) {
    if (openModules.value.includes(id)) {
        openModules.value = openModules.value.filter(m => m !== id);
    } else {
        openModules.value.push(id);
    }
}

const title = props.type === 'evento' ? 'Detalles del Evento' : props.type === 'en vivo' ? 'Programa de Sesiones' : 'Estructura Curricular';
</script>

<template>
    <div class="pt-10 mb-8">
        <h2 class="text-[32px] font-serif font-bold text-on-background mb-10">{{ title }}</h2>

        <div v-if="(type === 'grabado' || type === 'en vivo') && modules?.length" class="space-y-6">
            <div class="flex items-center gap-4 text-xs font-bold text-on-surface-variant tracking-[0.1em] uppercase mb-6 bg-surface-container-highest p-4 rounded">
                <span>Duración de Estudio: {{ duration || 'A tu ritmo' }}</span>
                <span class="text-outline-variant">|</span>
                <span>Total Módulos: {{ modules.length }}</span>
            </div>
            <div v-for="(module, index) in modules" :key="module.id" class="bg-white rounded shadow-[0_20px_40px_rgba(26,28,25,0.03)] overflow-hidden">
                <button @click="toggleModule(module.id)" class="w-full flex items-center justify-between p-6 bg-transparent hover:bg-background transition-colors">
                    <div class="flex items-center gap-6">
                        <span class="text-primary font-bold text-sm tracking-[0.1em] uppercase w-24 text-left">Módulo 0{{ Number(index) + 1 }}</span>
                        <h3 class="font-bold text-[17px] text-on-background text-left">{{ module.title }}</h3>
                    </div>
                    <svg class="w-5 h-5 text-on-surface-variant transition-transform" :class="{'rotate-180': openModules.includes(module.id)}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div v-show="openModules.includes(module.id)" class="bg-background p-2 border-t border-outline-variant/20">
                    <ul v-if="module.lessons?.length" class="space-y-1">
                        <li v-for="(lesson, lIdx) in module.lessons" :key="lesson.id" class="flex items-center gap-4 p-4 hover:bg-surface-container-highest rounded transition-colors text-on-background text-[15px]">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="font-medium">Sesión {{ Number(lIdx) + 1 }}: {{ lesson.title }}</span>
                        </li>
                    </ul>
                    <p v-else class="text-[13px] text-on-surface-variant p-4 italic font-serif">El material de este módulo estará disponible pronto en la biblioteca.</p>
                </div>
            </div>
        </div>

        <div v-else-if="type === 'evento' && lessons?.length" class="space-y-6">
            <div v-for="lesson in lessons" :key="lesson.id" class="bg-white rounded p-8 shadow-[0_20px_40px_rgba(26,28,25,0.04)] flex flex-col sm:flex-row gap-6 sm:items-center">
                <div class="flex-1">
                    <span class="text-[10px] font-bold text-primary tracking-[0.1em] uppercase block mb-2">Evento Confirmado</span>
                    <h3 class="font-serif font-bold text-2xl text-on-background mb-2">{{ lesson.title }}</h3>
                    <p class="text-sm text-on-surface-variant mb-6">{{ lesson.description || 'Participación sujeta al calendario oficial de sesiones.' }}</p>
                    <div class="flex items-center gap-3 text-sm font-bold text-on-background bg-background py-2 px-3 rounded inline-flex border border-outline-variant/30">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ lesson.start_time || 'Fecha por anunciar' }}
                    </div>
                </div>
                <div class="flex-shrink-0 mt-4 sm:mt-0">
                    <button class="px-5 py-3 rounded border border-outline-variant text-[11px] font-bold tracking-[0.05em] uppercase text-on-background hover:bg-surface-container-highest transition-colors">Agendar</button>
                </div>
            </div>
        </div>

        <div v-else class="font-serif italic text-on-surface-variant py-16 text-center bg-white rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)] text-lg">La estructura académica se encuentra en proceso de actualización formal.</div>
    </div>
</template>
