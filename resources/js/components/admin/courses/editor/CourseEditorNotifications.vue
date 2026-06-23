<script setup lang="ts">
defineProps<{
    showSuccess: boolean;
    showPublishSuccess: boolean;
    showError: boolean;
}>();
</script>

<template>
    <transition
        enter-active-class="transition ease-out duration-300 transform"
        enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
        enter-to-class="opacity-100 translate-y-0 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="showSuccess"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl border border-white/20 bg-[#4B5320] px-6 py-4 text-white shadow-xl"
        >
            <svg class="h-6 w-6 flex-shrink-0 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h4 class="text-sm font-bold">¡Cambios guardados!</h4>
                <p class="text-xs text-white/80">El curso se ha actualizado correctamente.</p>
            </div>
        </div>

        <div
            v-else-if="showPublishSuccess"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl border border-white/20 bg-emerald-700 px-6 py-4 text-white shadow-xl"
        >
            <svg class="h-6 w-6 flex-shrink-0 text-emerald-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            <div>
                <h4 class="text-sm font-bold">¡Curso Publicado!</h4>
                <p class="text-xs text-white/80">El curso ahora está visible para los alumnos.</p>
            </div>
        </div>

        <div
            v-else-if="showError"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 rounded-xl border border-white/20 bg-red-600 px-6 py-4 text-white shadow-xl"
        >
            <svg class="h-6 w-6 flex-shrink-0 text-red-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <h4 class="text-sm font-bold">Error al guardar</h4>
                <p class="text-xs text-white/80">Revisa los campos marcados en rojo.</p>
            </div>
        </div>
    </transition>
</template>
