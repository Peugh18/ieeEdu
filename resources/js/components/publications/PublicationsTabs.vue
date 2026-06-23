<script setup lang="ts">
const currentTab = defineModel<'libros' | 'articulos'>('currentTab', { required: true });
const searchQuery = defineModel<string>('searchQuery', { required: true });
</script>

<template>
    <div class="mb-12 flex flex-col items-center justify-between gap-6 border-b border-outline-variant/20 pb-6 md:flex-row">
        <div class="inline-flex rounded-2xl border border-outline-variant/15 bg-surface-container p-1.5 shadow-inner">
            <button
                @click="currentTab = 'libros'"
                :class="[
                    'flex items-center gap-2 rounded-xl px-6 py-3.5 font-serif text-xs font-bold uppercase tracking-[0.05em] transition-all duration-300 sm:px-8',
                    currentTab === 'libros' ? 'bg-surface text-primary shadow-md' : 'text-on-surface-variant/60 hover:text-on-surface',
                ]"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                    />
                </svg>
                Libros y Guías
            </button>
            <button
                @click="currentTab = 'articulos'"
                :class="[
                    'flex items-center gap-2 rounded-xl px-6 py-3.5 font-serif text-xs font-bold uppercase tracking-[0.05em] transition-all duration-300 sm:px-8',
                    currentTab === 'articulos' ? 'bg-surface text-primary shadow-md' : 'text-on-surface-variant/60 hover:text-on-surface',
                ]"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
                    />
                </svg>
                Artículos de Análisis
            </button>
        </div>

        <div class="relative w-full md:w-80">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant/50">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input
                v-model="searchQuery"
                type="text"
                :placeholder="currentTab === 'libros' ? 'Buscar libro o autor...' : 'Buscar artículo o medio...'"
                class="w-full rounded-2xl border border-outline-variant/15 bg-surface-container py-3 pl-11 pr-10 text-xs text-on-surface shadow-sm transition-all placeholder:text-on-surface-variant/50 focus:border-primary focus:bg-surface focus:outline-none focus:ring-1 focus:ring-primary"
            />
            <button
                v-if="searchQuery"
                @click="searchQuery = ''"
                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1 text-on-surface-variant/40 transition-colors hover:text-on-surface"
            >
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</template>
