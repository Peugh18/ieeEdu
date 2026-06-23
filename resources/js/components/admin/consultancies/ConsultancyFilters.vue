<script setup lang="ts">
import { LayoutGrid, List, Search } from 'lucide-vue-next';

const filterForm = defineModel<{ search: string; status: string }>('filterForm', { required: true });
const viewMode = defineModel<'cards' | 'list'>('viewMode', { required: true });
</script>

<template>
    <div
        class="mb-8 flex flex-col items-center justify-between gap-4 rounded-[2rem] border border-outline-variant/10 bg-surface-container-lowest p-4 shadow-sm md:flex-row"
    >
        <div class="relative w-full md:w-96">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <Search class="h-4 w-4 text-on-surface-variant/50" />
            </div>
            <input
                v-model="filterForm.search"
                type="text"
                placeholder="Buscar por nombre, correo o empresa..."
                class="w-full rounded-2xl border-none bg-surface-container-highest/50 py-3 pl-10 pr-4 text-sm font-medium text-on-surface transition-all placeholder:text-on-surface-variant/50 focus:ring-2 focus:ring-primary/20"
            />
        </div>
        <div class="flex w-full gap-2 md:w-auto">
            <select
                v-model="filterForm.status"
                class="relative w-full cursor-pointer appearance-none rounded-2xl border-none bg-surface-container-highest/50 px-5 py-3 pr-10 text-sm font-bold text-on-surface outline-none focus:ring-2 focus:ring-primary/20 md:w-auto"
            >
                <option value="">Ocultar resueltos</option>
                <option value="all">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="en_contacto">Contactado</option>
                <option value="cerrado">Resuelto</option>
            </select>
            <div class="hidden rounded-2xl bg-surface-container-highest/30 p-1 md:flex">
                <button
                    @click="viewMode = 'cards'"
                    class="rounded-xl px-3 py-2 transition-all"
                    :class="viewMode === 'cards' ? 'bg-surface text-primary shadow-sm' : 'text-on-surface-variant hover:text-on-surface'"
                >
                    <LayoutGrid class="h-4 w-4" />
                </button>
                <button
                    @click="viewMode = 'list'"
                    class="rounded-xl px-3 py-2 transition-all"
                    :class="viewMode === 'list' ? 'bg-surface text-primary shadow-sm' : 'text-on-surface-variant hover:text-on-surface'"
                >
                    <List class="h-4 w-4" />
                </button>
            </div>
        </div>
    </div>
</template>
