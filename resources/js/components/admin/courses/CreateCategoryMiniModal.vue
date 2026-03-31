<script setup lang="ts">
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'created', category: { id: number; name: string }): void;
}>();

const name = ref('');
const loading = ref(false);
const error = ref<string | null>(null);

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            name.value = '';
            error.value = null;
        }
    },
);

async function submit() {
    const value = name.value.trim();
    if (!value) {
        error.value = 'Ingrese un nombre.';
        return;
    }

    loading.value = true;
    error.value = null;
    try {
        const res = await axios.post(
            route('admin.categories.store'),
            { name: value },
            { headers: { Accept: 'application/json' } },
        );
        emit('created', res.data);
        emit('close');
    } catch (e: any) {
        error.value = e?.response?.data?.message ?? 'No se pudo crear la categoría.';
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <transition name="fade">
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-sm rounded-2xl bg-white p-5 shadow-2xl">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-on-surface">Nueva categoría</h3>
                        <p class="mt-1 text-xs text-on-surface-variant">Se agregará al select automáticamente.</p>
                    </div>
                    <button class="rounded-lg p-2 hover:bg-surface-container-low" @click="$emit('close')" aria-label="Cerrar">
                        <span class="material-symbols-outlined text-on-surface-variant">close</span>
                    </button>
                </div>

                <div class="mt-4">
                    <label class="block text-[11px] font-bold uppercase tracking-widest text-on-surface-variant mb-2">Nombre</label>
                    <input
                        v-model="name"
                        class="w-full rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/15 outline-none transition"
                        placeholder="Ej. Finanzas, Liderazgo, Data"
                        @keydown.enter.prevent="submit"
                    />
                    <p v-if="error" class="mt-2 text-xs text-red-600">{{ error }}</p>
                </div>

                <div class="mt-5 flex justify-end gap-2">
                    <button class="rounded-xl border border-outline-variant/30 px-4 py-2 text-sm font-semibold hover:bg-surface-container-low" @click="$emit('close')">
                        Cancelar
                    </button>
                    <button
                        class="rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white shadow hover:opacity-95 disabled:opacity-60"
                        :disabled="loading"
                        @click="submit"
                    >
                        {{ loading ? 'Guardando…' : 'Guardar' }}
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

