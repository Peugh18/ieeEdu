<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Award, Layout, Crosshair } from 'lucide-vue-next';

const props = defineProps<{
    courseTitle: string;
    fontColor: string;
    fontFamily: string;
    imagePreview: string | null;
    studentNameX: number;
    studentNameY: number;
    studentNameFontSize: number;
    courseTitleX: number;
    courseTitleY: number;
    courseTitleFontSize: number;
    issueDateX: number;
    issueDateY: number;
    issueDateFontSize: number;
    certificateCodeX: number;
    certificateCodeY: number;
    certificateCodeFontSize: number;
}>();

const previewContainer = ref<HTMLElement | null>(null);
const currentWidth = ref(1000);
const previewScaleFactor = computed(() => currentWidth.value / 1122);

let resizeObserver: ResizeObserver | null = null;

onMounted(() => {
    if (previewContainer.value) {
        resizeObserver = new ResizeObserver((entries) => {
            for (const entry of entries) {
                currentWidth.value = entry.contentRect.width;
            }
        });
        resizeObserver.observe(previewContainer.value);
    }
});

onUnmounted(() => {
    resizeObserver?.disconnect();
});

function textStyle(x: number, y: number, size: number) {
    return {
        position: 'absolute' as const,
        left: `${x * 100}%`,
        top: `${y * 100}%`,
        fontSize: `calc(${size}pt * ${previewScaleFactor.value})`,
        color: props.fontColor,
        fontFamily: props.fontFamily,
        transform: 'translate(-50%, -50%)',
        textAlign: 'center' as const,
        whiteSpace: 'nowrap' as const,
        pointerEvents: 'none' as const,
        zIndex: 10,
    };
}
</script>

<template>
    <div class="sticky top-10 space-y-6">
        <header class="flex items-center justify-between text-xs font-bold uppercase tracking-widest text-on-surface-variant/40">
            <span class="flex items-center gap-2"><Layout class="w-4 h-4" /> Previsualización a Escala</span>
            <span class="bg-surface-container-high px-3 py-1 rounded-full italic font-serif">Modo: Desarrollo Académico</span>
        </header>

        <div ref="previewContainer" class="relative bg-surface-container-high rounded-[3rem] border border-outline-variant/40 shadow-2xl overflow-hidden aspect-[1.414]">
            <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
            <div v-else class="w-full h-full flex flex-col items-center justify-center bg-surface-container-low text-on-surface-variant/20 italic font-serif gap-4">
                <Award class="w-20 h-20 opacity-10" />
                <p>Carga una plantilla para comenzar a diseñar</p>
            </div>

            <div :style="textStyle(studentNameX, studentNameY, studentNameFontSize)">JUAN PÉREZ ACADÉMICO</div>
            <div :style="textStyle(courseTitleX, courseTitleY, courseTitleFontSize)">{{ courseTitle }}</div>
            <div :style="textStyle(issueDateX, issueDateY, issueDateFontSize)">Emitido el 01/01/2026</div>
            <div :style="textStyle(certificateCodeX, certificateCodeY, certificateCodeFontSize)" class="opacity-70">ID: IEE-SAMPLE-VERIF-2026</div>
        </div>

        <footer class="p-6 bg-primary/5 rounded-3xl border border-primary/10 flex items-start gap-4">
            <Crosshair class="w-5 h-5 text-primary mt-1" />
            <p class="text-xs text-on-surface-variant/70 leading-relaxed italic font-serif">
                <strong class="text-primary uppercase tracking-widest font-sans inline-block mb-1">Nota Académica:</strong><br>
                Ajuste los controles laterales para posicionar los textos sobre su plantilla.
                Los valores se calculan en base a una hoja A4 en horizontal (297mm x 210mm).
                Use colores de contraste que respeten la autoridad institucional de su diseño.
            </p>
        </footer>
    </div>
</template>
