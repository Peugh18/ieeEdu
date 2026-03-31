<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import CreateCategoryMiniModal from './CreateCategoryMiniModal.vue';

type CourseType = 'grabado' | 'en vivo' | 'masterclass';

const props = defineProps<{
    open: boolean;
    categories: Array<{ id: number; name: string }>;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'categoryCreated', category: { id: number; name: string }): void;
}>();

const showCategoryModal = ref(false);
const imagePreview = ref<string | null>(null);

const form = useForm({
    title: '',
    description: '',
    price: 0,
    discount_enabled: false,
    sale_price: 0,
    type: 'grabado' as CourseType,
    // En este flujo el curso se crea siempre como BORRADOR
    status: 'BORRADOR',
    category_id: '' as string | number,
    image_file: null as File | null,
});

watch(
    () => props.open,
    (isOpen) => {
        if (!isOpen) return;
        form.reset();
        form.clearErrors();
        imagePreview.value = null;
        showCategoryModal.value = false;
        form.status = 'BORRADOR';
        form.type = 'grabado';
    },
);

const canSubmit = computed(() => !!form.title.trim() && !!form.description.trim() && Number(form.price) >= 0 && !!form.category_id && !!form.image_file);

function onPickImage(file: File | null) {
    form.image_file = file;
    if (!file) {
        imagePreview.value = null;
        return;
    }
    imagePreview.value = URL.createObjectURL(file);
}

function submit() {
    // Forzar borrador siempre (regla de negocio)
    form.status = 'BORRADOR';
    // En DB, "masterclass" se guarda como "evento" por compatibilidad
    if (form.type === 'masterclass') {
        (form as any).type = 'evento';
    }

    form.post(route('admin.courses.store'), {
        forceFormData: true,
        onSuccess: () => {
            // el backend redirige automáticamente a /admin/courses/{id}/edit
            emit('close');
        },
    });
}

function onCategoryCreated(category: { id: number; name: string }) {
    emit('categoryCreated', category);
    form.category_id = category.id;
}
</script>

<template>
    <transition name="fade">
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-3xl rounded-3xl bg-white shadow-2xl border border-outline-variant/10 overflow-hidden">
                <div class="flex items-start justify-between gap-4 p-6 md:p-8">
                    <div>
                        <h2 class="font-serif text-2xl md:text-3xl text-on-surface">Crear curso</h2>
                        <p class="mt-1 text-sm text-on-surface-variant">
                            Paso 1: datos básicos. Se creará como <span class="font-bold text-primary">BORRADOR</span> y pasarás al editor.
                        </p>
                    </div>
                    <button class="rounded-xl p-2 hover:bg-surface-container-low" @click="$emit('close')" aria-label="Cerrar">
                        <span class="material-symbols-outlined text-on-surface-variant">close</span>
                    </button>
                </div>

                <div class="px-6 md:px-8 pb-6 md:pb-8 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Nombre del curso</label>
                        <input
                            v-model="form.title"
                            class="w-full rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition"
                            placeholder="Ej. Análisis de datos para decisiones"
                        />
                        <p v-if="form.errors.title" class="mt-2 text-xs text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Precio</label>
                        <input
                            v-model.number="form.price"
                            type="number"
                            min="0"
                            class="w-full rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition"
                            placeholder="0"
                        />
                        <p v-if="form.errors.price" class="mt-2 text-xs text-red-600">{{ form.errors.price }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Descripción</label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            class="w-full rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition"
                            placeholder="¿De qué trata el curso?"
                        />
                        <p v-if="form.errors.description" class="mt-2 text-xs text-red-600">{{ form.errors.description }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-outline-variant/15 bg-surface-container-low px-4 py-3">
                            <div class="flex items-center gap-3">
                                <input id="discount_enabled" type="checkbox" v-model="form.discount_enabled" class="h-4 w-4" />
                                <label for="discount_enabled" class="text-sm font-semibold text-on-surface">Activar descuento</label>
                                <span class="text-xs text-on-surface-variant">Opcional</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input
                                    v-model.number="form.sale_price"
                                    :disabled="!form.discount_enabled"
                                    type="number"
                                    min="0"
                                    class="w-28 rounded-xl border border-outline-variant/30 bg-white px-3 py-2 text-sm disabled:opacity-60"
                                    placeholder="Precio final"
                                />
                                <span class="text-xs font-bold text-on-surface-variant">S/</span>
                            </div>
                        </div>
                        <p v-if="form.errors.sale_price" class="mt-2 text-xs text-red-600">{{ form.errors.sale_price }}</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Tipo de curso</label>
                        <select
                            v-model="form.type"
                            class="w-full rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition"
                        >
                            <option value="grabado">Grabado</option>
                            <option value="en vivo">En vivo</option>
                            <option value="masterclass">Masterclass</option>
                        </select>
                        <p v-if="form.errors.type" class="mt-2 text-xs text-red-600">{{ form.errors.type }}</p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Estado</label>
                        <div class="w-full rounded-2xl border border-outline-variant/20 bg-surface-container-low px-4 py-3 text-sm text-on-surface-variant">
                            Borrador (se publica desde el editor)
                        </div>
                    </div>

                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Categoría</label>
                            <div class="flex gap-2">
                                <select
                                    v-model="form.category_id"
                                    class="flex-1 rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition"
                                >
                                    <option value="">Sin categoría</option>
                                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                                <button
                                    type="button"
                                    class="rounded-2xl border border-outline-variant/30 px-4 py-3 text-sm font-semibold hover:bg-surface-container-low"
                                    @click="showCategoryModal = true"
                                >
                                    + Categoría
                                </button>
                            </div>
                            <p v-if="form.errors.category_id" class="mt-2 text-xs text-red-600">{{ form.errors.category_id }}</p>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Imagen</label>
                            <div class="flex items-center gap-3">
                                <label class="flex-1 cursor-pointer rounded-2xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm hover:bg-surface-container-low transition">
                                    <input
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="(e) => onPickImage((e.target as HTMLInputElement).files?.[0] ?? null)"
                                    />
                                    <span class="font-semibold text-on-surface">Subir imagen</span>
                                    <span class="ml-2 text-xs text-on-surface-variant">(JPG/PNG/WebP)</span>
                                </label>
                                <div v-if="imagePreview" class="h-14 w-14 overflow-hidden rounded-2xl border border-outline-variant/20">
                                    <img :src="imagePreview" alt="Preview" class="h-full w-full object-cover" />
                                </div>
                            </div>
                            <p v-if="form.errors.image_file" class="mt-2 text-xs text-red-600">{{ form.errors.image_file }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 border-t border-outline-variant/10 bg-surface-container-low px-6 md:px-8 py-4">
                    <p class="text-xs text-on-surface-variant">
                        Consejo: crea rápido y completa el contenido en el editor.
                    </p>
                    <div class="flex gap-2">
                        <button class="rounded-2xl border border-outline-variant/30 px-5 py-2.5 text-sm font-semibold hover:bg-surface-container-low" @click="$emit('close')">
                            Cancelar
                        </button>
                        <button
                            class="rounded-2xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow hover:opacity-95 disabled:opacity-60"
                            :disabled="!canSubmit || form.processing"
                            @click="submit"
                        >
                            {{ form.processing ? 'Creando…' : 'Crear curso' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>

    <CreateCategoryMiniModal
        :open="showCategoryModal"
        @close="showCategoryModal = false"
        @created="onCategoryCreated"
    />
</template>

