<script setup lang="ts">
import { Search, LayoutGrid, List } from 'lucide-vue-next';

const filterForm = defineModel<{ search: string; status: string }>('filterForm', { required: true });
const viewMode = defineModel<'cards' | 'list'>('viewMode', { required: true });
</script>

<template>
    <div class="bg-surface-container-lowest p-4 rounded-[2rem] border border-outline-variant/10 shadow-sm flex flex-col md:flex-row gap-4 justify-between items-center mb-8">
        <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><Search class="h-4 w-4 text-on-surface-variant/50" /></div>
            <input v-model="filterForm.search" type="text" placeholder="Buscar por nombre, correo o empresa..."
                class="w-full pl-10 pr-4 py-3 rounded-2xl bg-surface-container-highest/50 border-none focus:ring-2 focus:ring-primary/20 text-sm font-medium placeholder:text-on-surface-variant/50 transition-all text-on-surface" />
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <select v-model="filterForm.status" class="w-full md:w-auto bg-surface-container-highest/50 border-none rounded-2xl px-5 py-3 text-sm font-bold text-on-surface focus:ring-2 focus:ring-primary/20 outline-none cursor-pointer appearance-none pr-10 relative">
                <option value="">Ocultar resueltos</option>
                <option value="all">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="en_contacto">Contactado</option>
                <option value="cerrado">Resuelto</option>
            </select>
            <div class="hidden md:flex bg-surface-container-highest/30 p-1 rounded-2xl">
                <button @click="viewMode = 'cards'" class="px-3 py-2 rounded-xl transition-all" :class="viewMode === 'cards' ? 'bg-surface shadow-sm text-primary' : 'text-on-surface-variant hover:text-on-surface'"><LayoutGrid class="w-4 h-4" /></button>
                <button @click="viewMode = 'list'" class="px-3 py-2 rounded-xl transition-all" :class="viewMode === 'list' ? 'bg-surface shadow-sm text-primary' : 'text-on-surface-variant hover:text-on-surface'"><List class="w-4 h-4" /></button>
            </div>
        </div>
    </div>
</template>
