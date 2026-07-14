<script setup lang="ts">
import { Award, Crosshair, Layout } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

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
            <span class="flex items-center gap-2"><Layout class="h-4 w-4" /> Previsualización a Escala</span>
            <span class="rounded-full bg-surface-container-high px-3 py-1 font-serif italic">Modo: Desarrollo Académico</span>
        </header>

        <div
            ref="previewContainer"
            class="relative aspect-[1.414] overflow-hidden rounded-[3rem] border border-outline-variant/40 bg-surface-container-high shadow-2xl"
        >
            <img v-if="imagePreview" :src="imagePreview" class="h-full w-full object-fill" />
            <div
                v-else
                class="flex h-full w-full flex-col items-center justify-center gap-4 bg-surface-container-low font-serif italic text-on-surface-variant/20"
            >
                <Award class="h-20 w-20 opacity-10" />
                <p>Carga una plantilla para comenzar a diseñar</p>
            </div>

            <div :style="textStyle(studentNameX, studentNameY, studentNameFontSize)">JUAN PÉREZ ACADÉMICO</div>
            <div :style="textStyle(courseTitleX, courseTitleY, courseTitleFontSize)">{{ courseTitle }}</div>
            <div :style="textStyle(issueDateX, issueDateY, issueDateFontSize)">Emitido el 01/01/2026</div>
            <div :style="textStyle(certificateCodeX, certificateCodeY, certificateCodeFontSize)" class="opacity-70">ID: IEE-SAMPLE-VERIF-2026</div>
        </div>

        <footer class="flex items-start gap-4 rounded-3xl border border-primary/10 bg-primary/5 p-6">
            <Crosshair class="mt-1 h-5 w-5 text-primary" />
            <p class="font-serif text-xs italic leading-relaxed text-on-surface-variant/70">
                <strong class="mb-1 inline-block font-sans uppercase tracking-widest text-primary">Nota Académica:</strong><br />
                Ajuste los controles laterales para posicionar los textos sobre su plantilla. Los valores se calculan en base a una hoja A4 en
                horizontal (297mm x 210mm). Use colores de contraste que respeten la autoridad institucional de su diseño.
            </p>
        </footer>
    </div>
</template>
