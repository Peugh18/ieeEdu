<script setup lang="ts">
import type { Course } from '@/types/course';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    show: boolean;
    course: Course;
    amount: number;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const form = useForm({
    course_id: props.course.id,
    comprobante: null as File | null,
});

const fileInput = ref<HTMLInputElement | null>(null);

function submit() {
    form.post(route('student.payments.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
}

function handleFileChange(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.comprobante = target.files[0];
    } else {
        form.comprobante = null;
    }
}
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100">
            <div
                v-if="show"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm"
                @click.self="emit('close')"
            >
                <div class="relative w-full max-w-lg rounded-[2.5rem] bg-white p-8 shadow-2xl md:p-10">
                    <button
                        @click="emit('close')"
                        class="absolute right-5 top-5 flex h-10 w-10 items-center justify-center rounded-full bg-slate-50 text-slate-400 transition-colors hover:text-slate-700"
                    >
                        ✕
                    </button>
                    <h2 class="mb-4 text-xl font-bold text-slate-900">Registrar pago</h2>
                    <p class="mb-6 text-sm text-slate-500">
                        Estás a punto de registrar tu pago para el curso <strong>{{ course.title }}</strong> por un monto de
                        <strong>S/ {{ Number(amount).toFixed(2) }}</strong
                        >.
                    </p>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Comprobante (Opcional)</label>
                            <input
                                type="file"
                                ref="fileInput"
                                @change="handleFileChange"
                                accept="image/*,.pdf"
                                class="w-full cursor-pointer rounded-xl border border-slate-200 text-sm text-slate-500 transition-all file:mr-4 file:rounded-xl file:border-0 file:bg-primary/10 file:px-4 file:py-2 file:text-xs file:font-bold file:text-primary hover:file:bg-primary/20"
                            />
                            <div v-if="form.errors.comprobante" class="mt-1 text-xs text-rose-500">{{ form.errors.comprobante }}</div>
                        </div>

                        <div v-if="form.errors.course_id" class="rounded-xl border border-rose-100 bg-rose-50 p-3 text-xs font-bold text-rose-600">
                            {{ form.errors.course_id }}
                        </div>

                        <div class="mt-8 flex justify-end gap-3">
                            <button
                                type="button"
                                @click="emit('close')"
                                class="rounded-xl px-5 py-2.5 text-xs font-bold text-slate-500 transition-colors hover:bg-slate-50"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="rounded-xl bg-primary px-5 py-2.5 text-xs font-bold text-white shadow-md transition-all hover:bg-primary/95 disabled:opacity-50"
                            >
                                Registrar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
