<script setup lang="ts">
import CertificateFieldEditor from '@/components/admin/certificates/CertificateFieldEditor.vue';
import { Image as ImageIcon, Type } from 'lucide-vue-next';
import { ref } from 'vue';
import AppSelect from '@/components/ui/AppSelect.vue';

export interface CertificateTemplateForm {
    student_name_X: number;
    student_name_Y: number;
    student_name_font_size: number;
    course_title_X: number;
    course_title_Y: number;
    course_title_font_size: number;
    issue_date_X: number;
    issue_date_Y: number;
    issue_date_font_size: number;
    certificate_code_X: number;
    certificate_code_Y: number;
    certificate_code_font_size: number;
    font_color: string;
    font_family: string;
    template_image: File | null;
}

export type CertificateFieldKey = 'student_name' | 'course_title' | 'issue_date' | 'certificate_code';

const form = defineModel<CertificateTemplateForm>('form', { required: true });
const imagePreview = defineModel<string | null>('imagePreview', { required: true });

const activeField = ref<CertificateFieldKey | ''>('student_name');

const fields: { key: CertificateFieldKey; label: string }[] = [
    { key: 'student_name', label: 'Nombre del alumno' },
    { key: 'course_title', label: 'Título del curso' },
    { key: 'issue_date', label: 'Fecha de emisión' },
    { key: 'certificate_code', label: 'ID de verificación' },
];

function toggleField(key: CertificateFieldKey) {
    activeField.value = activeField.value === key ? '' : key;
}

function handleImageChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (!file) return;
    form.value.template_image = file;
    const reader = new FileReader();
    reader.onload = (event) => {
        imagePreview.value = event.target?.result as string;
    };
    reader.readAsDataURL(file);
}
</script>

<template>
    <div class="space-y-8 rounded-[2rem] border border-outline-variant/30 bg-surface-container-lowest p-6 shadow-sm md:p-8">
        <div class="space-y-3">
            <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-primary">
                <ImageIcon class="h-4 w-4" />
                Imagen base (PNG/JPG)
            </label>
            <div
                class="group relative cursor-pointer rounded-2xl border-2 border-dashed border-outline-variant/50 p-6 text-center transition-all hover:border-primary/50"
            >
                <input type="file" accept="image/*" class="absolute inset-0 cursor-pointer opacity-0" @change="handleImageChange" />
                <ImageIcon class="mx-auto mb-2 h-8 w-8 text-on-surface-variant/40" />
                <p class="text-sm text-on-surface-variant">Carga la plantilla sin textos para superponerlos dinámicamente.</p>
            </div>
        </div>

        <div class="space-y-4">
            <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-primary">
                <Type class="h-4 w-4" />
                Estética global
            </label>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <span class="text-sm font-semibold text-on-surface-variant">Color de fuente</span>
                    <input
                        v-model="form.font_color"
                        type="color"
                        class="h-11 w-full cursor-pointer rounded-xl border border-outline-variant/20 bg-surface-container-high p-1"
                    />
                </div>
                <div class="space-y-2">
                    <span class="text-sm font-semibold text-on-surface-variant">Tipografía</span>
                    <AppSelect
                        v-model="form.font_family"
                        :options="[
                            { value: 'serif', label: 'Serif (clásico)' },
                            { value: 'sans-serif', label: 'Sans-serif (moderno)' },
                            { value: 'cursive', label: 'Cursive (elegante)' }
                        ]"
                        class="h-11 border-outline-variant/20 bg-surface-container-high font-medium text-sm"
                    />
                </div>
            </div>
        </div>

        <div class="space-y-3 border-t border-outline-variant/30 pt-2">
            <p class="text-xs font-bold uppercase tracking-widest text-primary">Posicionamiento de campos</p>
            <CertificateFieldEditor
                v-for="field in fields"
                :key="field.key"
                :field-key="field.key"
                :label="field.label"
                :form="form"
                :open="activeField === field.key"
                @toggle="toggleField(field.key)"
            />
        </div>
    </div>
</template>
