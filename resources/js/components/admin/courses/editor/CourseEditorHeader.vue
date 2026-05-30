<script setup lang="ts">
defineProps<{
    status: string;
    moduleCount: number;
    lessonCount: number;
    processing: boolean;
    canPublish: boolean;
}>();

defineEmits<{
    hide: [];
    save: [];
    publish: [];
}>();
</script>

<template>
    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between bg-surface-container-lowest p-6 sm:p-10 rounded-[2rem] shadow-xl shadow-surface-tint/5 relative overflow-hidden border border-outline-variant/10">
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-surface-tint/10 to-transparent rounded-full blur-[40px] -mr-16 -mt-16 pointer-events-none"></div>
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-serif font-bold text-on-surface tracking-tight mb-2 italic">
                <span class="italic font-light">Editor Avanzado</span> de Curso
            </h1>
            <div class="flex flex-wrap items-center gap-3 text-xs font-bold text-on-surface-variant uppercase tracking-widest">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-surface-container-low border border-outline-variant/30">
                    Estado:
                    <span :class="{
                        'text-amber-600': status === 'BORRADOR',
                        'text-emerald-600': status === 'PUBLICADO',
                        'text-slate-500': status === 'OCULTO',
                    }">{{ status }}</span>
                </span>
                <span>·</span>
                <span><span class="text-on-surface">{{ moduleCount }}</span> Módulos</span>
                <span>·</span>
                <span><span class="text-on-surface">{{ lessonCount }}</span> Clases</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-3 relative z-10">
            <button type="button" class="rounded-full bg-surface-container-low px-8 py-3 text-[12px] font-bold text-on-surface hover:bg-surface-container-high transition-colors" @click="$emit('hide')">Ocultar</button>
            <button
                type="button"
                class="rounded-full flex items-center justify-center gap-2 bg-gradient-to-br from-primary to-[#707040] px-8 py-3 text-[12px] font-bold text-white shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] disabled:opacity-60 transition-all tracking-wide"
                :disabled="processing"
                @click="$emit('save')"
            >
                <svg v-if="processing" class="h-4 w-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Guardar Cambios
            </button>
            <button
                type="button"
                class="rounded-full bg-surface-container-lowest border border-outline-variant/10 px-8 py-3 text-[12px] font-bold text-primary shadow-lg shadow-primary/5 hover:bg-surface-container-low transition-all"
                :disabled="!canPublish"
                @click="$emit('publish')"
            >
                Publicar Oficial
            </button>
        </div>
    </div>
</template>
