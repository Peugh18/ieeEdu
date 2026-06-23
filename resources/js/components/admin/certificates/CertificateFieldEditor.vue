<script setup lang="ts">
import CertificateCoordinateSlider from '@/components/admin/certificates/CertificateCoordinateSlider.vue';
import type { CertificateFieldKey, CertificateTemplateForm } from '@/components/admin/certificates/CertificateTemplateControls.vue';
import { ChevronDown, ChevronUp } from 'lucide-vue-next';

const props = defineProps<{
    fieldKey: CertificateFieldKey;
    label: string;
    form: CertificateTemplateForm;
    open: boolean;
}>();

const emit = defineEmits<{ toggle: [] }>();

function coord(axis: 'X' | 'Y'): number {
    const key = `${props.fieldKey}_${axis}` as keyof CertificateTemplateForm;
    return (props.form[key] as number) * 100;
}

function setCoord(axis: 'X' | 'Y', percent: number) {
    const key = `${props.fieldKey}_${axis}` as keyof CertificateTemplateForm;
    (props.form as Record<string, number | string | File | null>)[key] = percent / 100;
}

function fontSize(): number {
    return props.form[`${props.fieldKey}_font_size` as keyof CertificateTemplateForm] as number;
}

function setFontSize(value: number) {
    (props.form as Record<string, number | string | File | null>)[`${props.fieldKey}_font_size`] = value;
}
</script>

<template>
    <div
        class="overflow-hidden rounded-2xl border border-outline-variant/30 bg-surface transition-shadow"
        :class="open ? 'shadow-sm ring-1 ring-primary/10' : ''"
    >
        <button
            type="button"
            class="flex w-full items-center justify-between bg-surface px-5 py-4 transition-colors hover:bg-surface-container-low"
            @click="emit('toggle')"
        >
            <div class="text-left">
                <span class="text-sm font-bold text-on-surface">{{ label }}</span>
                <p v-if="!open" class="mt-0.5 text-xs text-on-surface-variant">
                    X {{ Math.round(coord('X')) }}% · Y {{ Math.round(coord('Y')) }}% · {{ fontSize() }}px
                </p>
            </div>
            <ChevronDown v-if="!open" class="h-5 w-5 shrink-0 text-on-surface-variant" />
            <ChevronUp v-else class="h-5 w-5 shrink-0 text-primary" />
        </button>

        <div v-show="open" class="space-y-5 border-t border-outline-variant/10 px-5 pb-5 pt-1">
            <div class="flex items-center justify-between gap-4 pt-3">
                <label class="text-sm font-semibold text-on-surface-variant">Tamaño de fuente</label>
                <div class="flex items-center gap-2">
                    <input
                        :value="fontSize()"
                        type="number"
                        min="6"
                        max="72"
                        class="w-20 rounded-xl border border-outline-variant/30 bg-surface-container-high py-2 text-center text-sm font-bold focus:border-primary focus:ring-2 focus:ring-primary/30"
                        @input="setFontSize(parseInt(($event.target as HTMLInputElement).value, 10) || 12)"
                    />
                    <span class="text-xs text-on-surface-variant">px</span>
                </div>
            </div>

            <CertificateCoordinateSlider :model-value="coord('X')" label="Posición horizontal (X)" @update:model-value="setCoord('X', $event)" />
            <CertificateCoordinateSlider :model-value="coord('Y')" label="Posición vertical (Y)" @update:model-value="setCoord('Y', $event)" />
        </div>
    </div>
</template>
