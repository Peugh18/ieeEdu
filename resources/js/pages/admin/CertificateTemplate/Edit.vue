<script setup lang="ts">
import AdminFlashToast from '@/components/admin/AdminFlashToast.vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import CertificateTemplateControls from '@/components/admin/certificates/CertificateTemplateControls.vue';
import CertificateTemplatePreview from '@/components/admin/certificates/CertificateTemplatePreview.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Save } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Template {
    id?: number;
    student_name_X?: number;
    student_name_Y?: number;
    student_name_font_size?: number;
    course_title_X?: number;
    course_title_Y?: number;
    course_title_font_size?: number;
    issue_date_X?: number;
    issue_date_Y?: number;
    issue_date_font_size?: number;
    certificate_code_X?: number;
    certificate_code_Y?: number;
    certificate_code_font_size?: number;
    font_color?: string;
    font_family?: string;
    template_image?: string;
}

const props = defineProps<{
    course: { id: number; title: string };
    template: Template;
}>();

const form = useForm({
    student_name_X: Number(props.template?.student_name_X ?? 0.5),
    student_name_Y: Number(props.template?.student_name_Y ?? 0.45),
    student_name_font_size: props.template?.student_name_font_size ?? 32,
    course_title_X: Number(props.template?.course_title_X ?? 0.5),
    course_title_Y: Number(props.template?.course_title_Y ?? 0.55),
    course_title_font_size: props.template?.course_title_font_size ?? 24,
    issue_date_X: Number(props.template?.issue_date_X ?? 0.8),
    issue_date_Y: Number(props.template?.issue_date_Y ?? 0.85),
    issue_date_font_size: props.template?.issue_date_font_size ?? 12,
    certificate_code_X: Number(props.template?.certificate_code_X ?? 0.15),
    certificate_code_Y: Number(props.template?.certificate_code_Y ?? 0.85),
    certificate_code_font_size: props.template?.certificate_code_font_size ?? 8,
    font_color: props.template?.font_color ?? '#57572A',
    font_family: props.template?.font_family ?? 'serif',
    template_image: null as File | null,
});

const imagePreview = ref(props.template?.template_image ? `/storage/${props.template.template_image}` : null);

const toast = ref<{ show: boolean; variant: 'success' | 'error'; message: string; subtitle?: string }>({
    show: false,
    variant: 'success',
    message: '',
});

function showToast(variant: 'success' | 'error', message: string, subtitle?: string) {
    toast.value = { show: true, variant, message, subtitle };
    setTimeout(
        () => {
            toast.value.show = false;
        },
        variant === 'success' ? 4000 : 5000,
    );
}

function submit() {
    form.post(route('admin.courses.certificate-template.update', { course: props.course.id }), {
        onSuccess: () => showToast('success', 'Cambios guardados', 'La plantilla del certificado ha sido actualizada.'),
        onError: () => showToast('error', 'Error al guardar', 'Revisa que la imagen y los campos sean válidos.'),
        forceFormData: true,
    });
}

watch(
    () => form.errors,
    (errors) => {
        if (Object.keys(errors).length > 0) {
            showToast('error', 'Error de validación', Object.values(errors)[0] as string);
        }
    },
);
</script>

<template>
    <Head title="Diseñador de Diplomados" />
    <AppLayout>
        <div class="mx-auto max-w-7xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
            <AdminPageHeader
                badge="Académico / Certificados"
                title="Configuración de"
                title-accent="Certificado"
                :subtitle="course.title"
                back-link="admin.courses.index"
                back-label="Volver a cursos"
                compact
            >
                <button
                    type="button"
                    :disabled="form.processing"
                    class="inline-flex h-12 items-center gap-2 rounded-2xl bg-primary px-6 text-sm font-bold text-white shadow-lg transition-all hover:bg-primary/90 disabled:opacity-60"
                    @click="submit"
                >
                    <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        />
                    </svg>
                    <Save v-else class="h-4 w-4" />
                    {{ form.processing ? 'Guardando…' : 'Guardar cambios' }}
                </button>
            </AdminPageHeader>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:max-h-[calc(100vh-12rem)] lg:overflow-y-auto lg:pr-1">
                    <CertificateTemplateControls v-model:form="form" v-model:image-preview="imagePreview" />
                </div>
                <div class="lg:col-span-2">
                    <CertificateTemplatePreview
                        :course-title="course.title"
                        :font-color="form.font_color"
                        :font-family="form.font_family"
                        :image-preview="imagePreview"
                        :student-name-x="form.student_name_X"
                        :student-name-y="form.student_name_Y"
                        :student-name-font-size="form.student_name_font_size"
                        :course-title-x="form.course_title_X"
                        :course-title-y="form.course_title_Y"
                        :course-title-font-size="form.course_title_font_size"
                        :issue-date-x="form.issue_date_X"
                        :issue-date-y="form.issue_date_Y"
                        :issue-date-font-size="form.issue_date_font_size"
                        :certificate-code-x="form.certificate_code_X"
                        :certificate-code-y="form.certificate_code_Y"
                        :certificate-code-font-size="form.certificate_code_font_size"
                    />
                </div>
            </div>

            <AdminFlashToast
                :show="toast.show"
                :variant="toast.variant"
                :message="toast.message"
                :subtitle="toast.subtitle"
                @close="toast.show = false"
            />
        </div>
    </AppLayout>
</template>
