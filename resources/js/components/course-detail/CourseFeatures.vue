<script setup lang="ts">
import type { Course } from '@/types/course';

defineProps<{
    type: Course['type'];
}>();

const grabadoFeatures = [
    { title: 'Acceso Ilimitado', desc: 'Estudia a tu propio ritmo, 24/7 sin restricciones.', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>' },
    { title: 'Material Descargable', desc: 'Recursos, plantillas y lecturas especializadas.', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>' },
    { title: 'Certificado Digital', desc: 'Acreditación válida expedida por el IEE.', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>' },
];

const vivoFeatures = [
    { title: 'Clases en Vivo', desc: 'Interacción directa con el docente especialista.', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>' },
    { title: 'Asesoría Docente', desc: 'Resolución de casos y consultas en tiempo real.', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/>' },
    { title: 'Comunidad Activa', desc: 'Networking exclusivo con otros profesionales.', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>' },
];

const eventoFeatures = [
    { title: 'Sesión Magistral', desc: 'Análisis de alto nivel en un encuentro único.', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>' },
    { title: 'Acceso Directo', desc: 'Preguntas y respuestas con el experto.', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>' },
    { title: 'Grupo WhatsApp', desc: 'Avisos y recordatorios directamente en tu móvil.', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>' },
];

const features = props.type === 'en vivo' ? vivoFeatures : props.type === 'evento' ? eventoFeatures : grabadoFeatures;
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <div v-for="f in features" :key="f.title" class="bg-white p-6 rounded shadow-[0_20px_40px_rgba(26,28,25,0.04)] text-center flex flex-col items-center">
            <svg class="w-8 h-8 text-primary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="f.icon"></svg>
            <h4 class="font-bold text-on-background mb-2 text-sm uppercase tracking-[0.05em]">{{ f.title }}</h4>
            <p class="text-[13px] text-on-surface-variant leading-relaxed">{{ f.desc }}</p>
        </div>
    </div>
</template>
