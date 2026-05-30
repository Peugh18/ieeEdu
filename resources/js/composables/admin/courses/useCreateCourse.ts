import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

export type CourseType = 'grabado' | 'en vivo' | 'masterclass';

export interface CreateCourseForm {
    title: string;
    description: string;
    price: number;
    discount_enabled: boolean;
    discount: number;
    sale_price: number;
    type: CourseType;
    status: string;
    category_id: string | number;
    image_file: File | null;
    instructor_name: string;
    instructor_title: string;
    instructor_bio: string;
    instructor_image_file: File | null;
    start_date: string;
    start_time: string;
    whatsapp_link: string;
    objectives: string;
    requirements: string;
}

export function useCreateCourse(
    categories: Array<{ id: number; name: string }>,
    emit: {
        (e: 'close'): void;
        (e: 'categoryCreated', category: { id: number; name: string }): void;
    }
) {
    const showCategoryModal = ref(false);
    const imagePreview = ref<string | null>(null);
    const instructorImagePreview = ref<string | null>(null);

    const step = ref(1);
    const totalSteps = 3;

    function nextStep() {
        if (step.value < totalSteps) step.value++;
    }
    function prevStep() {
        if (step.value > 1) step.value--;
    }

    const form = useForm<CreateCourseForm>({
        title: '',
        description: '',
        price: 0,
        discount_enabled: false,
        discount: 0,
        sale_price: 0,
        type: 'grabado' as CourseType,
        status: 'BORRADOR',
        category_id: '' as string | number,
        image_file: null as File | null,
        instructor_name: '',
        instructor_title: '',
        instructor_bio: '',
        instructor_image_file: null as File | null,

        // Rigor & Modality logic
        start_date: '',
        start_time: '',
        whatsapp_link: '',
        objectives: '',
        requirements: '',
    });

    function initForm(open: boolean, duplicateData?: Record<string, any> | null) {
        if (!open) return;
        form.reset();
        form.clearErrors();
        imagePreview.value = null;
        instructorImagePreview.value = null;
        showCategoryModal.value = false;
        step.value = 1;

        if (duplicateData) {
            form.title = duplicateData.title + ' (Copia)';
            form.description = duplicateData.description || '';
            form.price = duplicateData.price || 0;
            form.discount_enabled = Boolean(duplicateData.discount_enabled);
            form.discount = duplicateData.discount || 0;
            form.sale_price = duplicateData.sale_price || 0;
            form.type = duplicateData.type === 'evento' ? 'masterclass' : (duplicateData.type || 'grabado');
            form.category_id = duplicateData.category_id || (duplicateData.category?.id || '');
            form.instructor_name = duplicateData.instructor_name || '';
            form.instructor_title = duplicateData.instructor_title || '';
            form.instructor_bio = duplicateData.instructor_bio || '';
            form.start_date = duplicateData.start_date || '';
            form.start_time = duplicateData.start_time || '';
            form.whatsapp_link = duplicateData.whatsapp_link || '';
            form.objectives = duplicateData.objectives || '';
            form.requirements = duplicateData.requirements || '';
            form.status = 'BORRADOR';
        } else {
            form.status = 'BORRADOR';
            form.type = 'grabado';
        }
    }

    function setModality(t: CourseType) {
        form.type = t;
        if (t === 'masterclass') {
            form.price = 0;
            form.discount_enabled = false;
            form.discount = 0;
            form.sale_price = 0;
        }
    }

    watch(
        [() => form.price, () => form.discount],
        ([newPrice, newDiscount]) => {
            if (form.discount_enabled && (newDiscount as number) > 0) {
                form.sale_price = Number(
                    ((newPrice as number) - (newPrice as number) * ((newDiscount as number) / 100)).toFixed(2)
                );
            } else {
                form.sale_price = 0;
            }
        }
    );

    watch(
        () => form.discount_enabled,
        (enabled: boolean) => {
            if (enabled && form.discount > 0) {
                form.sale_price = Number(
                    (form.price - form.price * (form.discount / 100)).toFixed(2)
                );
            } else {
                form.sale_price = 0;
            }
        }
    );

    const canSubmit = computed(() => {
        const isFree = form.type === 'masterclass';
        const hasValidPrice = isFree || Number(form.price) >= 0;
        return (
            !!form.title.trim() &&
            !!form.description.trim() &&
            hasValidPrice &&
            !!form.category_id &&
            !!form.image_file
        );
    });

    function onPickImage(file: File | null) {
        form.image_file = file;
        if (!file) {
            imagePreview.value = null;
            return;
        }
        imagePreview.value = URL.createObjectURL(file);
    }

    function onPickInstructorImage(file: File | null) {
        form.instructor_image_file = file;
        if (!file) {
            instructorImagePreview.value = null;
            return;
        }
        instructorImagePreview.value = URL.createObjectURL(file);
    }

    function submit() {
        form.status = 'BORRADOR';
        form.transform((data) => {
            const payload: Record<string, unknown> = {
                ...data,
                sale_price: data.discount_enabled ? data.sale_price : '',
                discount: data.discount_enabled ? data.discount : '',
            };
            if (payload.type === 'masterclass') {
                payload.type = 'evento';
                payload.price = 0;
            }
            return payload;
        }).post(route('admin.courses.store'), {
            forceFormData: true,
            onSuccess: () => {
                emit('close');
            },
        });
    }

    function onCategoryCreated(category: { id: number; name: string }) {
        emit('categoryCreated', category);
        form.category_id = category.id;
        showCategoryModal.value = false;
    }

    return {
        form,
        step,
        totalSteps,
        nextStep,
        prevStep,
        showCategoryModal,
        imagePreview,
        instructorImagePreview,
        setModality,
        canSubmit,
        onPickImage,
        onPickInstructorImage,
        submit,
        onCategoryCreated,
        initForm,
    };
}
