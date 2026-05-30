<script setup lang="ts">
import { Search } from 'lucide-vue-next';

const search = defineModel<string>('search', { default: '' });
const status = defineModel<string>('status', { default: '' });
const type = defineModel<string>('type', { default: '' });

const emit = defineEmits<{
    (e: 'filter'): void;
}>();
</script>

<template>
    <!-- Filtros compactos -->
    <div class="flex flex-col sm:flex-row gap-2 items-stretch sm:items-center bg-white/80 rounded-2xl border border-outline-variant/10 px-3 py-2 shadow-sm">
        <div class="relative flex-1 min-w-0">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary/40" />
            <input
                v-model="search"
                placeholder="Buscar curso..."
                class="w-full rounded-xl bg-surface-container-highest pl-9 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20 border-0"
                @keydown.enter.prevent="emit('filter')"
            />
        </div>
        <select v-model="status" class="rounded-xl bg-surface-container-highest px-3 py-2 text-xs font-bold text-primary border-0 outline-none sm:w-36">
            <option value="">Estado</option>
            <option value="BORRADOR">Borrador</option>
            <option value="PUBLICADO">Publicado</option>
            <option value="OCULTO">Oculto</option>
        </select>
        <select v-model="type" class="rounded-xl bg-surface-container-highest px-3 py-2 text-xs font-bold text-primary border-0 outline-none sm:w-40">
            <option value="">Tipo</option>
            <option value="grabado">Grabado</option>
            <option value="en vivo">En vivo</option>
            <option value="evento">Masterclass</option>
        </select>
        <button type="button" class="rounded-xl bg-primary px-4 py-2 text-xs font-bold text-white shrink-0" @click="emit('filter')">
            Filtrar
        </button>
    </div>
</template>
