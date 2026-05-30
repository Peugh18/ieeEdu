import type { CourseEditorCourse } from '@/types/course-editor';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, watch, type Ref } from 'vue';

function firstError(errors: Record<string, string | string[]>, key: string): string | null {
    const value = errors[key];
    if (!value) return null;
    return Array.isArray(value) ? (value[0] ?? null) : value;
}

interface CourseFormNotifications {
    notifySuccess: () => void;
    notifyError: () => void;
    notifyPublishSuccess: () => void;
}

export function useCourseForm(
    course: CourseEditorCourse,
    notifications: CourseFormNotifications,
    lessonsCount: Ref<number>,
) {
    const form = useForm({
        title: course.title ?? '',
        description: course.description ?? '',
        price: Number(course.price ?? 0),
        discount_enabled: course.discount != null && Number(course.discount) > 0,
        discount: Number(course.discount ?? 0),
        sale_price: Number(course.sale_price ?? 0),
        type: course.type ?? 'grabado',
        status: course.status ?? 'BORRADOR',
        category_id: course.category_id ?? '',
        certificate_enabled: course.certificate_enabled ?? true,
        image_file: null as File | null,
        instructor_name: course.instructor_name ?? '',
        instructor_title: course.instructor_title ?? '',
        instructor_bio: course.instructor_bio ?? '',
        instructor_image_file: null as File | null,
        start_date: course.start_date ?? '',
        start_time: course.start_time ?? '',
        class_hours: course.class_hours ?? '',
        whatsapp_link: course.whatsapp_link ?? '',
        objectives: course.objectives ?? '',
        requirements: course.requirements ?? '',
    });

    watch([() => form.price, () => form.discount], ([newPrice, newDiscount]) => {
        if (form.discount_enabled && (newDiscount as number) > 0) {
            form.sale_price = Number(
                ((newPrice as number) - (newPrice as number) * ((newDiscount as number) / 100)).toFixed(2),
            );
        } else {
            form.sale_price = 0;
        }
    });

    watch(() => form.discount_enabled, (enabled: boolean) => {
        if (enabled && form.discount > 0) {
            form.sale_price = Number((form.price - form.price * (form.discount / 100)).toFixed(2));
        } else {
            form.sale_price = 0;
        }
    });

    watch(
        () => course.status,
        (status) => {
            if (status) {
                form.status = status;
            }
        },
    );

    const isMasterclass = computed(() => form.type === 'masterclass' || form.type === 'evento');

    const canPublish = computed(() => {
        if (lessonsCount.value < 1) return false;
        if (isMasterclass.value && lessonsCount.value !== 1) return false;
        return true;
    });

    const instructorPreviewUrl = computed(() =>
        form.instructor_image_file ? URL.createObjectURL(form.instructor_image_file) : null,
    );

    const courseImagePreviewUrl = computed(() =>
        form.image_file ? URL.createObjectURL(form.image_file) : null,
    );

    function saveCourse() {
        form.transform((data) => {
            const payload: Record<string, unknown> = {
                ...data,
                sale_price: data.discount_enabled ? data.sale_price : '',
                discount: data.discount_enabled ? data.discount : '',
                certificate_enabled: !!data.certificate_enabled,
                _method: 'PUT',
            };
            if (!payload.image_file) delete payload.image_file;
            if (!payload.instructor_image_file) delete payload.instructor_image_file;
            delete payload.status;
            return payload;
        }).post(route('admin.courses.update', course.id), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: notifications.notifySuccess,
            onError: notifications.notifyError,
        });
    }

    function publishCourse() {
        if (!canPublish.value) {
            alert('Debes tener al menos 1 clase. En masterclass solo se permite 1.');
            return;
        }
        router.patch(route('admin.courses.publish', course.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                const errors = usePage().props.errors as Record<string, string | string[]>;
                const courseError = firstError(errors, 'course');
                if (courseError) {
                    alert(courseError);
                    notifications.notifyError();
                    return;
                }
                form.status = 'PUBLICADO';
                notifications.notifyPublishSuccess();
            },
            onError: (errors: Record<string, string | string[]>) => {
                const courseError = firstError(errors, 'course');
                if (courseError) alert(courseError);
                else notifications.notifyError();
            },
        });
    }

    function hideCourse() {
        router.patch(route('admin.courses.hide', course.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                form.status = 'OCULTO';
                notifications.notifySuccess();
            },
        });
    }

    return {
        form,
        isMasterclass,
        canPublish,
        instructorPreviewUrl,
        courseImagePreviewUrl,
        saveCourse,
        publishCourse,
        hideCourse,
    };
}
